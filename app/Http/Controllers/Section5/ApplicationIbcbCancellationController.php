<?php

namespace App\Http\Controllers\Section5;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use HP;

use App\Models\Section5\ApplicationIbcb;
use App\Models\Section5\ApplicationIbcbScope;
use App\Models\Section5\ApplicationIbcbBoardApprove;
use App\Models\Section5\ApplicationIbcbGazette;
use App\Models\Section5\IbcbsScope;
use App\Models\Section5\Ibcbs;
use App\AttachFile;
use Illuminate\Support\Facades\Storage;

class ApplicationIbcbCancellationController extends Controller
{
    private $attach_path;//ที่เก็บไฟล์แนบ

    public function __construct()
    {
        $this->middleware('auth');
        $this->attach_path = 'files/sso';
    }

    public function index(Request $request)
    {
        return view('section5.application-ib-cb.cancellation.index');
    }

    public function data_list(Request $request)
    {
        $user = auth()->user();
        $filter_search = $request->get('filter_search');

        $query = Ibcbs::where('state', 1)
            ->when($filter_search, function ($query, $filter_search) {
                $search_full = str_replace(' ', '', $filter_search);
                return $query->where(function ($q) use ($search_full) {
                    $q->where(DB::raw("REPLACE(ibcb_name,' ','')"), 'LIKE', "%{$search_full}%")
                      ->orWhere(DB::raw("REPLACE(ibcb_code,' ','')"), 'LIKE', "%{$search_full}%")
                      ->orWhere(DB::raw("REPLACE(taxid,' ','')"), 'LIKE', "%{$search_full}%")
                      ->orWhere('ref_ibcb_application_no', 'LIKE', "%{$search_full}%");
                });
            })
            ->when($user, function ($query, $user) {
                $user_act_instead = $user->ActInstead;
                if (is_null($user_act_instead)) {
                    $query->where('ibcb_user_id', $user->getKey());
                } else {
                    $query->where('ibcb_user_id', $user_act_instead->getKey());
                }
            });

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('ibcb_info', function ($item) {
                return '<div><strong>' . e($item->ibcb_code) . '</strong></div>'
                    . '<div>' . e($item->ibcb_name) . '</div>';
            })
            ->addColumn('gazette', function ($item) {
                $refAppNo = $item->ref_ibcb_application_no;
                if (!$refAppNo) return '-';

                $gazettes = ApplicationIbcbGazette::where('application_no', $refAppNo)
                    ->orderBy('announcement_date', 'asc')
                    ->get();

                $html = $gazettes->isNotEmpty()
                    ? $gazettes->map(function ($g) {
                        $date = $g->announcement_date ? HP::revertDate($g->announcement_date) : '-';
                        return '<div>เล่ม/ตอนที่ ' . e($g->issue) . ' ปี ' . e($g->year) . ' (' . $date . ')</div>';
                    })->implode('')
                    : '';

                $board = ApplicationIbcbBoardApprove::where('application_no', $refAppNo)->latest('id')->first();
                if ($board) {
                    $file = AttachFile::where('ref_table', (new ApplicationIbcbBoardApprove)->getTable())
                        ->where('ref_id', $board->id)
                        ->where('section', 'file_attach_government_gazette')
                        ->first();
                    if ($file) {
                        $cleanPath = preg_replace('/\/+/', '/', $file->url);
                        $nasBase   = rtrim(env('FILESYSTEM_ROOT_URL', ''), '/');
                        $fileUrl   = $nasBase . '/' . ltrim($cleanPath, '/');
                        $fileExt   = HP::FileExtension($file->filename) ?? 'ไฟล์';
                        $html .= "<div class='m-t-5'><a href='{$fileUrl}' target='_blank' class='btn btn-xs btn-info'>"
                            . "<i class='fa fa-file-pdf-o'></i> {$fileExt}</a></div>";
                    }
                }

                return $html ?: '-';
            })
            ->addColumn('manage', function ($item) {
                // ถ้ามีคำขอยกเลิกเดิมที่ยังแก้ไขได้อยู่ (ฉบับร่าง/เอกสารไม่ครบ/แก้ไขกลับมา) ให้พาไปแก้ไขคำขอเดิม
                // ไม่งั้นจะกลายเป็นสร้างคำขอใหม่ (เลขที่คำขอใหม่) ทุกครั้งที่กด "จัดการ" (มิเรอร์จาก Lab)
                $existing = ApplicationIbcb::where('ibcb_id', $item->id)
                    ->where('applicant_request_type', 5)
                    ->whereIn('application_status', [0, 2, 15])
                    ->latest('id')
                    ->first();

                $url = url('/request-section-5/application-ibcb/cancellation/details') . '?ibcb_id=' . $item->id;
                if ($existing) {
                    $url .= '&application_id=' . $existing->id;
                }

                return '<a href="' . $url . '" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> จัดการ</a>';
            })
            ->order(function ($query) {
                $query->orderBy('id', 'DESC');
            })
            ->rawColumns(['ibcb_info', 'gazette', 'manage'])
            ->make(true);
    }

    public function details(Request $request)
    {
        $validated = $request->validate([
            'ibcb_id'         => 'required|integer',
            'application_id'  => 'nullable|integer',
        ]);
        $ibcb_id  = $validated['ibcb_id'];
        $readonly = filter_var($request->get('readonly'), FILTER_VALIDATE_BOOLEAN);

        $ibcbs = Ibcbs::where('id', $ibcb_id)->firstOrFail();

        $refAppNo = $ibcbs->ref_ibcb_application_no;

        $gazettes = $refAppNo
            ? ApplicationIbcbGazette::where('application_no', $refAppNo)->orderBy('announcement_date', 'asc')->get()
            : collect();

        $scopes = IbcbsScope::with(['bs_branch_group', 'scopes_details.bs_branch', 'scopes_tis'])
            ->where('ibcb_id', $ibcb_id)
            ->where('state', 1)
            ->get();

        // ── โหลดค่าที่เคยยื่นไว้ (กรณีเปิดจากปุ่ม "จัดการ"/"แก้ไข"/"ดูรายละเอียด" ของคำขอที่มีอยู่แล้ว) ──
        // มิเรอร์จาก ApplicationLabCancellationController::cancellation_details()
        $existingApplication    = null;
        $selectedScopeIds       = [];
        $existingAttachFiles    = collect();
        $existingAttachmentType = null;

        if (!empty($validated['application_id'])) {
            $existingApplication = ApplicationIbcb::where('id', $validated['application_id'])
                ->where('ibcb_id', $ibcb_id)
                ->where('applicant_request_type', 5)
                ->first();
        }

        if ($existingApplication) {
            $selectedScopeIds = ApplicationIbcbScope::where('application_id', $existingApplication->id)
                ->pluck('live_scope_id')
                ->filter()
                ->all();

            $existingAttachFiles = AttachFile::where('ref_table', (new ApplicationIbcb)->getTable())
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

        return view('section5.application-ib-cb.cancellation.details', compact(
            'ibcbs', 'gazettes', 'scopes', 'existingApplication', 'selectedScopeIds',
            'existingAttachFiles', 'existingAttachmentType', 'readonly'
        ));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'ibcb_id'          => 'required|integer',
            'application_id'   => 'nullable|integer',
            'attachment_type'  => 'required|in:1,2',
        ]);
        $ibcb_id = $validated['ibcb_id'];

        $ibcbs = Ibcbs::findOrFail($ibcb_id);

        // แก้ไขคำขอยกเลิกที่เคยยื่นไว้ → อัปเดต record เดิม ไม่สร้างคำขอซ้ำ (มิเรอร์จาก Lab)
        $application = !empty($validated['application_id'])
            ? ApplicationIbcb::where('id', $validated['application_id'])
                ->where('ibcb_id', $ibcb_id)
                ->where('applicant_request_type', 5)
                ->first()
            : null;

        // เอกสารข้อ 1 ("หนังสือขอยกเลิกการแต่งตั้ง...") บังคับแนบ — ถ้าเป็นการแก้ไขคำขอเดิมที่เคยแนบ
        // ไฟล์นี้ไว้แล้ว ไม่บังคับให้อัปโหลดซ้ำ
        $requiredField = $validated['attachment_type'] == '1' ? 'gov_file_1' : 'pri_file_1';
        $hasExistingRequiredFile = $application && AttachFile::where('ref_table', (new ApplicationIbcb)->getTable())
            ->where('ref_id', $application->id)
            ->where('section', 'cancellation_' . $requiredField)
            ->exists();

        if (!$request->hasFile($requiredField) && !$hasExistingRequiredFile) {
            return back()->withErrors([
                $requiredField => 'กรุณาแนบ "หนังสือขอยกเลิกการแต่งตั้งจากหน่วยงานที่ลงนามโดยผู้มีอำนาจสูงสุด" (ข้อ 1)',
            ]);
        }

        if ($application) {
            // ล้างขอบข่ายที่เคยเลือกไว้ (จะบันทึกชุดใหม่จากฟอร์มนี้แทน)
            ApplicationIbcbScope::where('application_id', $application->id)->delete();
        } else {
            $ref = ($ibcbs->ibcb_type == 2) ? 'CB' : 'IB';
            $gen_number = HP::ConfigFormat('APP-IB-CB', (new ApplicationIbcb)->getTable(), 'application_no', $ref, null, null);
            $application_check = ApplicationIbcb::where('application_no', $gen_number)->first();
            if (!is_null($application_check)) {
                $gen_number = HP::ConfigFormat('APP-IB-CB', (new ApplicationIbcb)->getTable(), 'application_no', $ref, null, null);
            }

            $application = new ApplicationIbcb;
            $application->application_no = $gen_number;
        }

        $application->applicant_request_type = 5; // ขอยกเลิกการเป็นหน่วยตรวจสอบ
        $application->application_date   = date('Y-m-d');
        $application->application_status = 1;
        $application->application_type   = $ibcbs->ibcb_type;

        $application->applicant_taxid = $ibcbs->taxid;
        $application->applicant_name  = $ibcbs->name;
        $application->ibcb_id         = $ibcbs->id;
        $application->ibcb_code       = $ibcbs->ibcb_code;

        // คำขอยกเลิกไม่ได้ให้แก้ข้อมูล — ก็อปข้อมูลปัจจุบันของหน่วยตรวจสอบมาทั้งหมดเพื่อบันทึก/แสดงผลเท่านั้น
        $application->ibcb_name           = $ibcbs->ibcb_name;
        $application->ibcb_address        = $ibcbs->ibcb_address;
        $application->ibcb_moo            = $ibcbs->ibcb_moo;
        $application->ibcb_soi            = $ibcbs->ibcb_soi;
        $application->ibcb_road           = $ibcbs->ibcb_road;
        $application->ibcb_building       = $ibcbs->ibcb_building;
        $application->ibcb_subdistrict_id = $ibcbs->ibcb_subdistrict_id;
        $application->ibcb_district_id    = $ibcbs->ibcb_district_id;
        $application->ibcb_province_id    = $ibcbs->ibcb_province_id;
        $application->ibcb_zipcode        = $ibcbs->ibcb_zipcode;
        $application->ibcb_phone          = $ibcbs->ibcb_phone;
        $application->ibcb_fax            = $ibcbs->ibcb_fax;

        $application->co_name     = $ibcbs->co_name;
        $application->co_position = $ibcbs->co_position;
        $application->co_mobile   = $ibcbs->co_mobile;
        $application->co_phone    = $ibcbs->co_phone;
        $application->co_fax      = $ibcbs->co_fax;
        $application->co_email    = $ibcbs->co_email;

        $application->created_by = auth()->user() ? auth()->user()->getKey() : null;
        $application->save();

        // บันทึกขอบข่ายที่เลือก — ตอนอนุมัติ (ApplicationIbcbBoardApproveController::CancelIbcbs() ฝั่ง
        // devops_center) จะปิดเฉพาะขอบข่ายที่เลือกไว้ตรงนี้ (จับคู่ผ่าน live_scope_id) ไม่ใช่ปิดทุก scope
        // ของหน่วยเสมอไปแล้ว (เดิมเป็นแบบนั้น แก้ไปพร้อมกันแล้ว — ดู applicant_type_ibcb.edit.md)
        // live_scope_id จำเป็นต้องเก็บให้ครบ ไม่งั้น backend จะไม่รู้ว่าต้องปิด IbcbsScope แถวไหนโดยเฉพาะ
        $scope_ids = $request->input('scope_ids', []);
        foreach ($scope_ids as $ibcb_scope_id) {
            $liveScope = IbcbsScope::find($ibcb_scope_id);
            if (!$liveScope) continue;

            $scope = new ApplicationIbcbScope;
            $scope->application_id  = $application->id;
            $scope->application_no  = $application->application_no;
            $scope->branch_group_id = $liveScope->branch_group_id;
            $scope->isic_no         = $liveScope->isic_no;
            $scope->type            = 3; // ขอลด/ขอยกเลิกขอบข่ายนี้
            $scope->live_scope_id   = $liveScope->id;
            $scope->ibcb_id         = $ibcbs->id;
            $scope->ibcb_code       = $ibcbs->ibcb_code;
            $scope->created_by      = auth()->user()->getKey();
            $scope->save();
        }

        $attach_path = $this->attach_path.'/Section5/ApplicationIbcb/'.$application->application_no;

        // บันทึกไฟล์ราชกิจจา (ถ้ามีการแนบใหม่)
        if ($request->hasFile('gazette_file') && $request->file('gazette_file')->isValid()) {
            $board = ApplicationIbcbBoardApprove::where('application_no', $ibcbs->ref_ibcb_application_no)->latest('id')->first();
            if ($board) {
                HP::singleFileUpload(
                    $request->file('gazette_file'),
                    $attach_path,
                    (auth()->user()->tax_number ?? null),
                    (auth()->user()->username ?? null),
                    'SSO',
                    (new ApplicationIbcbBoardApprove)->getTable(),
                    $board->id,
                    'file_attach_government_gazette',
                    null,
                    null
                );
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

                // ชื่อไฟล์จริงบน FTP ต้องเป็น ASCII เท่านั้น เก็บชื่อไทยไว้แค่คอลัมน์ filename สำหรับแสดงผล
                $originalName = $file->getClientOriginalName();
                $safeFilename = str_random(10) . '_' . date('YmdHis') . '.' . $file->getClientOriginalExtension();

                $url = Storage::disk('ftp')->putFileAs($attach_path, $file, $safeFilename);

                // ใช้ firstOrNew แทน new ตรงๆ (ต่างจาก HP::singleFileUpload() ที่ใช้ create() ตรงๆ เสมอ)
                // ไม่งั้นทุกครั้งที่แก้ไข/อัปโหลดซ้ำ section เดิม จะได้ record ซ้ำใน attach_files เรื่อยๆ
                $attach                = AttachFile::firstOrNew([
                    'ref_table' => (new ApplicationIbcb)->getTable(),
                    'ref_id'    => $application->id,
                    'section'   => 'cancellation_' . $field,
                ]);
                $attach->url           = $url;
                $attach->filename      = $originalName;
                $attach->new_filename  = $safeFilename;
                $attach->caption       = $field;
                $attach->created_by    = auth()->user() ? auth()->user()->getKey() : null;
                $attach->save();
            }
        }

        return redirect(url('/request-section-5/application-ibcb'))
            ->with('flash_message', 'บันทึกคำขอยกเลิกเรียบร้อย');
    }
}
