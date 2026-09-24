<?php

namespace App\Http\Controllers\Section5;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use HP;

use App\Models\Section5\ApplicationLab;
use App\Models\Section5\ApplicationLabScope;
use App\Models\Section5\Labs;
use App\Models\Section5\LabsScope;
use App\Models\Section5\ApplicationLabBoardApprove;
use App\Models\Section5\ApplicationLabGazette;
use App\Models\Section5\ApplicationLabGazetteDetail;
use App\AttachFile;
use Illuminate\Support\Facades\Storage;

class ApplicationLabCancellationController extends Controller
{
    public function cancellation(Request $request)
    {
        return view('section5.application-lab.cancellation.index');
    }

    public function cancellation_data_list(Request $request)
    {
        $user = auth()->user();

        $filter_search = $request->get('filter_search');

        $query = Labs::with(['board_approve.attach_file_gazette'])
            ->where('state', 1)
            ->when($filter_search, function ($query, $filter_search) {
                $search_full = str_replace(' ', '', $filter_search);

                return $query->where(function ($q) use ($search_full) {
                    $q->where(DB::raw("REPLACE(lab_name,' ','')"), 'LIKE', "%{$search_full}%")
                      ->orWhere(DB::raw("REPLACE(lab_code,' ','')"), 'LIKE', "%{$search_full}%")
                      ->orWhere(DB::raw("REPLACE(taxid,' ','')"), 'LIKE', "%{$search_full}%")
                      ->orWhere('ref_lab_application_no', 'LIKE', "%{$search_full}%");
                });
            })
            ->when($user, function ($query, $user) {
                $user_act_instead = $user->ActInstead;
                if (is_null($user_act_instead)) {
                    $query->where('lab_user_id', $user->getKey());
                } else {
                    $query->where('lab_user_id', $user_act_instead->getKey());
                }
            });

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('application_no', function ($item) {
                return '<div><strong>' . e($item->lab_code) . '</strong></div>'
                    . '<div>' . e($item->lab_name) . '</div>';
            })
            ->addColumn('gazette', function ($item) {
                // Chain: section5_labs.ref_lab_application_no
                //        → section5_application_labs.application_no
                //        → gazette_details.app_lab_id → section5_application_labs_gazette
                $refAppNo = $item->ref_lab_application_no;
                if (!$refAppNo) return '-';

                $appLabId = ApplicationLab::where('application_no', $refAppNo)->value('id');
                if (!$appLabId) return '-';

                $gazettes = ApplicationLabGazette::whereHas('details', function ($q) use ($appLabId) {
                    $q->where('app_lab_id', $appLabId);
                })->orderBy('announcement_date', 'asc')->get();

                $html = $gazettes->isNotEmpty()
                    ? $gazettes->map(function ($g) { return '<div>' . e($g->FormattedLabel) . '</div>'; })->implode('')
                    : '';

                // แสดงไฟล์ PDF ราชกิจจาจาก board_approve (ถ้ามี)
                $file = optional($item->board_approve)->attach_file_gazette;
                if ($file) {
                    $cleanPath = preg_replace('/\/+/', '/', $file->url);
                    $nasBase   = rtrim(env('FILESYSTEM_ROOT_URL', ''), '/');
                    $fileUrl   = $nasBase . '/' . ltrim($cleanPath, '/');
                    $fileExt   = HP::FileExtension($file->filename) ?? 'ไฟล์';
                    $html .= "<div class='m-t-5'><a href='{$fileUrl}' target='_blank' class='btn btn-xs btn-info'>"
                        . "<i class='fa fa-file-pdf-o'></i> {$fileExt}</a></div>";
                }

                return $html ?: '-';
            })
            ->addColumn('manage', function ($item) {
                // ถ้ามีคำขอยกเลิกเดิมที่ยังแก้ไขได้อยู่ (ฉบับร่าง/เอกสารไม่ครบ/แก้ไขกลับมา) ให้พาไปแก้ไขคำขอเดิม
                // ไม่งั้นจะกลายเป็นสร้างคำขอใหม่ (เลขที่คำขอใหม่) ทุกครั้งที่กด "จัดการ"
                $existing = ApplicationLab::where('lab_id', $item->id)
                    ->where('applicant_type', 5)
                    ->whereIn('application_status', [0, 2, 15])
                    ->latest('id')
                    ->first();

                $url = url('/request-section-5/application-lab/cancellation/details') . '?lab_id=' . $item->id;
                if ($existing) {
                    $url .= '&application_id=' . $existing->id;
                }

                return '<a href="' . $url . '" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> จัดการ</a>';
            })
            ->order(function ($query) {
                $query->orderBy('id', 'DESC');
            })
            ->rawColumns(['application_no', 'gazette', 'manage'])
            ->make(true);
    }

    public function cancellation_details(Request $request)
    {
        $validated = $request->validate([
            'lab_id'         => 'required|integer',
            'application_id' => 'nullable|integer',
        ]);
        $lab_id   = $validated['lab_id'];
        // Laravel 5.6 (เวอร์ชันของโปรเจกต์นี้) ไม่มีเมธอด Request::boolean() — เพิ่มเข้ามาใน Laravel 7.0
        // เรียกแล้ว fatal 500 ทันที ("Call to undefined method") ทุกครั้งที่เข้าหน้านี้
        $readonly = filter_var($request->get('readonly'), FILTER_VALIDATE_BOOLEAN);

        $applicationLab = Labs::where('id', $lab_id)->firstOrFail();

        // ── โหลดค่าที่เคยยื่นไว้ (กรณีเปิดจากหน้า "แก้ไข" ของคำขอยกเลิกที่มีอยู่แล้ว) ──
        $existingApplication  = null;
        $selectedLabScopeIds  = [];
        $existingAttachFiles  = collect();
        $existingAttachmentType = null;

        if (!empty($validated['application_id'])) {
            $existingApplication = ApplicationLab::where('id', $validated['application_id'])
                ->where('lab_id', $lab_id)
                ->where('applicant_type', 5)
                ->first();
        }

        if ($existingApplication) {
            // มอก. ที่เคยเลือกไว้ว่าจะยกเลิก — ใช้ live_scope_id ตรงๆ (เก็บไว้ตั้งแต่ตอนสร้างคำขอแล้ว)
            // fallback ด้วยคู่ tis_id+test_item_id เผื่อเป็นคำขอเก่าที่ยื่นไว้ก่อนแก้ save_cancellation()
            $cancelScopes = ApplicationLabScope::where('application_lab_id', $existingApplication->id)
                ->where('type', 3)
                ->get(['live_scope_id', 'tis_id', 'test_item_id']);

            $selectedLabScopeIds = $cancelScopes->pluck('live_scope_id')->filter()->all();

            $missingPairs = $cancelScopes->filter(function ($s) { return empty($s->live_scope_id); })
                ->map(function ($s) { return $s->tis_id . '|' . $s->test_item_id; })
                ->all();

            if (!empty($missingPairs)) {
                $fallbackIds = LabsScope::where('lab_id', $lab_id)
                    ->get(['id', 'tis_id', 'test_item_id'])
                    ->filter(function ($s) use ($missingPairs) {
                        return in_array($s->tis_id . '|' . $s->test_item_id, $missingPairs);
                    })
                    ->pluck('id')
                    ->all();

                $selectedLabScopeIds = array_merge($selectedLabScopeIds, $fallbackIds);
            }

            // ไฟล์แนบที่เคยอัปโหลดไว้ (key ด้วย section เช่น cancellation_gov_file_1)
            // เรียง id น้อย→มากก่อน keyBy() เพื่อให้ตัวล่าสุด (id มากสุด) ทับตัวเก่าเสมอ เผื่อมี record
            // ซ้ำ section เดิมค้างจากช่วงที่ยังไม่ได้แก้บั๊ก firstOrNew (ก่อนหน้านี้ใช้ new AttachFile ตรงๆ)
            $existingAttachFiles = AttachFile::where('ref_table', (new ApplicationLab)->getTable())
                ->where('ref_id', $existingApplication->id)
                ->orderBy('id')
                ->get()
                ->keyBy('section');

            // ไม่มีคอลัมน์ attachment_type เก็บไว้ในตาราง ต้อง infer จาก section ของไฟล์ที่เคยแนบ
            if ($existingAttachFiles->keys()->contains(function ($section) { return strpos($section, 'cancellation_gov_file') === 0; })) {
                $existingAttachmentType = 1;
            } elseif ($existingAttachFiles->keys()->contains(function ($section) { return strpos($section, 'cancellation_pri_file') === 0; })) {
                $existingAttachmentType = 2;
            }
        }

        // Chain: section5_labs.ref_lab_application_no
        //        → section5_application_labs.application_no (application type=1 ต้นทาง)
        //        → gazette_details.app_lab_id
        //        → section5_application_labs_gazette
        $refAppNo = $applicationLab->ref_lab_application_no;

        $appLabIds = $refAppNo
            ? ApplicationLab::where('application_no', $refAppNo)->pluck('id')
            : collect();

        $detailsFilter = function ($q) use ($appLabIds) {
            if ($appLabIds->isEmpty()) {
                $q->whereRaw('0 = 1');
                return;
            }
            $q->whereIn('app_lab_id', $appLabIds);
        };

        $allLabScopes = LabsScope::with('tis_standards')
            ->where('lab_id', $lab_id)
            ->get()
            ->keyBy('tis_id');

        $assignedTisIds = collect();

        $gazettes = ApplicationLabGazette::with([
                'details' => function ($q) use ($detailsFilter) {
                    $detailsFilter($q);
                    $q->with('application.app_scope_standard.standards');
                },
            ])
            ->whereHas('details', $detailsFilter)
            ->orderBy('announcement_date', 'asc')
            ->get()
            ->map(function ($gazette) use ($allLabScopes, &$assignedTisIds) {
                // รวม มอก. ที่ไม่ซ้ำจากทุก application ที่ผูกกับ gazette นี้
                $gazette->tis_items = $gazette->details
                    ->pluck('application.app_scope_standard')
                    ->flatten()
                    ->filter()
                    ->unique('tis_id')
                    ->sortBy('tis_tisno')
                    ->values();

                // หา LabsScope ที่ตรงกับ tis_id ของ gazette นี้
                $tisIds = $gazette->tis_items->pluck('tis_id');
                $assignedTisIds = $assignedTisIds->merge($tisIds);
                $gazette->scope_items = $allLabScopes->only($tisIds->all())->sortBy('tis_tisno')->values();

                return $gazette;
            });

        $ungroupedScopes = $allLabScopes->except($assignedTisIds->unique()->all())->sortBy('tis_tisno')->values();

        return view(
            'section5.application-lab.cancellation.details',
            compact('applicationLab', 'gazettes', 'ungroupedScopes', 'existingApplication', 'selectedLabScopeIds', 'existingAttachFiles', 'existingAttachmentType', 'readonly')
        );
    }

    public function save_cancellation(Request $request)
    {
        $validated = $request->validate([
            'lab_id'          => 'required|integer',
            'application_id'  => 'nullable|integer',
            'attachment_type' => 'required|in:1,2',
        ]);
        $lab_id = $validated['lab_id'];

        $lab = Labs::findOrFail($lab_id);

        // แก้ไขคำขอยกเลิกที่เคยยื่นไว้ → อัปเดต record เดิม ไม่สร้างคำขอซ้ำ
        $application = !empty($validated['application_id'])
            ? ApplicationLab::where('id', $validated['application_id'])
                ->where('lab_id', $lab_id)
                ->where('applicant_type', 5)
                ->first()
            : null;

        // เอกสารข้อ 1 ("หนังสือขอยกเลิกการแต่งตั้ง...") บังคับแนบ ตามป้าย "(ต้องมี)" ในฟอร์ม —
        // เดิมไม่มีการเช็คทั้งฝั่ง client/server เลย (บั๊กเดียวกับที่เจอใน change-info type 6)
        // ถ้าเป็นการแก้ไขคำขอเดิมที่เคยแนบไฟล์นี้ไว้แล้ว ไม่บังคับให้อัปโหลดซ้ำ
        $requiredField = $validated['attachment_type'] == '1' ? 'gov_file_1' : 'pri_file_1';
        $hasExistingRequiredFile = $application && AttachFile::where('ref_table', (new ApplicationLab)->getTable())
            ->where('ref_id', $application->id)
            ->where('section', 'cancellation_' . $requiredField)
            ->exists();

        if (!$request->hasFile($requiredField) && !$hasExistingRequiredFile) {
            // ไม่ใช้ withInput() เพราะ request มีไฟล์ปน (files=>true) — flash ไฟล์เข้า session ไม่ควรทำ
            return back()->withErrors([
                $requiredField => 'กรุณาแนบ "หนังสือขอยกเลิกการแต่งตั้งจากหน่วยงานที่ลงนามโดยผู้มีอำนาจสูงสุด" (ข้อ 1)',
            ]);
        }

        if ($application) {
            // ล้างขอบข่ายที่เคยเลือกไว้ (จะบันทึกชุดใหม่จากฟอร์มนี้แทน)
            ApplicationLabScope::where('application_lab_id', $application->id)->delete();
        } else {
            $gen_number = HP::ConfigFormat('APP-LAB', (new ApplicationLab)->getTable(), 'application_no', null, null, null);
            $application_check = ApplicationLab::where('application_no', $gen_number)->first();
            if (!is_null($application_check)) {
                $gen_number = HP::ConfigFormat('APP-LAB', (new ApplicationLab)->getTable(), 'application_no', null, null, null);
            }

            $application = new ApplicationLab;
            $application->application_no = $gen_number;
        }

        $application->applicant_type = 5;
        $application->application_date = date('Y-m-d');
        $application->application_status = 1;

        $application->applicant_taxid = $lab->taxid;
        $application->applicant_name = $lab->name;

        $application->hq_address = $lab->hq_address;
        $application->hq_moo = $lab->hq_moo;
        $application->hq_soi = $lab->hq_soi;
        $application->hq_road = $lab->hq_road;
        $application->hq_building = $lab->hq_building;
        $application->hq_subdistrict_id = $lab->hq_subdistrict_id;
        $application->hq_district_id = $lab->hq_district_id;
        $application->hq_province_id = $lab->hq_province_id;
        $application->hq_zipcode = $lab->hq_zipcode;

        $application->lab_id = $lab->id;
        $application->lab_code = $lab->lab_code;
        $application->lab_name = $lab->lab_name;
        $application->lab_address = $lab->lab_address;
        $application->lab_moo = $lab->lab_moo;
        $application->lab_soi = $lab->lab_soi;
        $application->lab_road = $lab->lab_road;
        $application->lab_building = $lab->lab_building;
        $application->lab_subdistrict_id = $lab->lab_subdistrict_id;
        $application->lab_district_id = $lab->lab_district_id;
        $application->lab_province_id = $lab->lab_province_id;
        $application->lab_zipcode = $lab->lab_zipcode;
        $application->lab_phone = $lab->lab_phone;
        $application->lab_fax = $lab->lab_fax;

        $application->co_name = $lab->co_name;
        $application->co_position = $lab->co_position;
        $application->co_mobile = $lab->co_mobile;
        $application->co_phone = $lab->co_phone;
        $application->co_fax = $lab->co_fax;
        $application->co_email = $lab->co_email;

        $application->created_by = auth()->user() ? auth()->user()->getKey() : null;
        $application->save();

        // --- บันทึก มอก. ที่เลือก (section5_application_labs_scope) ---
        $scope_ids = $request->input('scope_ids', []);
        foreach ($scope_ids as $labs_scope_id) {
            $labsScope = LabsScope::find($labs_scope_id);
            if (!$labsScope) continue;

            ApplicationLabScope::create([
                'application_lab_id' => $application->id,
                'application_no'     => $application->application_no,
                'tis_id'             => $labsScope->tis_id,
                'tis_tisno'          => $labsScope->tis_tisno,
                'test_item_id'       => $labsScope->test_item_id,
                'lab_id'             => $lab->id,
                'lab_code'           => $lab->lab_code,
                'type'               => 3,
                // ต้องเก็บไว้ให้ CancelLabs() (devops_center) ปิดเฉพาะ มอก. ที่เลือกไว้จริง
                // ไม่งั้นจะไม่มีทางรู้ว่าต้องปิด LabsScope แถวไหนโดยเฉพาะ
                'live_scope_id'      => $labsScope->id,
            ]);
        }

        $storagePath = 'files/sso/Section5/CancellationLab/' . $lab->lab_code;

        // บันทึกไฟล์ราชกิจจา
        if ($request->hasFile('gazette_file') && $request->file('gazette_file')->isValid()) {
            $file = $request->file('gazette_file');

            // ชื่อไฟล์จริงบน FTP ต้องเป็น ASCII เท่านั้น (ext ftp ของ PHP ไม่รองรับ UTF-8/ไทยในชื่อไฟล์
            // ส่งผลให้ไฟล์ถูกเขียนด้วยชื่อที่ถูกเข้ารหัสผิดเพี้ยนบน NAS จนหาไฟล์กลับมาไม่เจอ)
            // เก็บชื่อไฟล์เดิม (ภาษาไทยได้) ไว้แค่ในคอลัมน์ filename สำหรับแสดงผลเท่านั้น
            $originalName  = $file->getClientOriginalName();
            $safeFilename  = str_random(10) . '_' . date('YmdHis') . '.' . $file->getClientOriginalExtension();

            $board = $lab->board_approve;

            if ($board) {
                $url = Storage::disk('ftp')->putFileAs($storagePath, $file, $safeFilename);

                $attach = AttachFile::firstOrNew([
                    'ref_table' => (new ApplicationLabBoardApprove)->getTable(),
                    'ref_id'    => $board->id,
                    'section'   => 'file_attach_government_gazette',
                ]);
                $attach->url          = $url;
                $attach->filename     = $originalName;
                $attach->new_filename = $safeFilename;
                $attach->created_by   = auth()->user() ? auth()->user()->getKey() : null;
                $attach->save();
            }
        }

        // บันทึกไฟล์แนบคำขอยกเลิก
        $attachmentType = $request->input('attachment_type');
        $fileFields = $attachmentType == '1'
            ? ['gov_file_1', 'gov_file_2', 'gov_file_3']
            : ['pri_file_1', 'pri_file_2', 'pri_file_3', 'pri_file_4'];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                $file = $request->file($field);

                // ดูหมายเหตุเรื่อง ASCII filename ที่ block ไฟล์ราชกิจจาด้านบน
                $originalName = $file->getClientOriginalName();
                $safeFilename = str_random(10) . '_' . date('YmdHis') . '.' . $file->getClientOriginalExtension();

                $url = Storage::disk('ftp')->putFileAs($storagePath, $file, $safeFilename);

                // ใช้ firstOrNew แทน new ตรงๆ ไม่งั้นทุกครั้งที่แก้ไข/อัปโหลดซ้ำ section เดิม
                // จะได้ record ซ้ำใน attach_files เรื่อยๆ (ของเดิมค้างเป็นไฟล์เสีย/เพี้ยนแล้วยังโผล่ปนอยู่)
                $attach              = AttachFile::firstOrNew([
                    'ref_table' => (new ApplicationLab)->getTable(),
                    'ref_id'    => $application->id,
                    'section'   => 'cancellation_' . $field,
                ]);
                $attach->url         = $url;
                $attach->filename    = $originalName;
                $attach->new_filename = $safeFilename;
                $attach->caption     = $field;
                $attach->created_by  = auth()->user() ? auth()->user()->getKey() : null;
                $attach->save();
            }
        }

        return redirect(url('/request-section-5/application-lab'))
            ->with('flash_message', 'บันทึกคำขอยกเลิกเรียบร้อย');
    }
}
