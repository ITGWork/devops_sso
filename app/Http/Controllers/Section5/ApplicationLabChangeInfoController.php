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
use App\AttachFile;
use Illuminate\Support\Facades\Storage;

class ApplicationLabChangeInfoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('section5.application-lab.change-info.index');
    }

    public function data_list(Request $request)
    {
        $user = auth()->user();
        $filter_search = $request->get('filter_search');

        $query = Labs::where('state', 1)
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
            ->addColumn('lab_info', function ($item) {
                return '<div><strong>' . e($item->lab_code) . '</strong></div>'
                    . '<div>' . e($item->lab_name) . '</div>';
            })
            ->addColumn('lab_address_col', function ($item) {
                $parts = array_filter([
                    $item->lab_address,
                    $item->lab_moo ? 'หมู่ ' . $item->lab_moo : null,
                    $item->lab_soi ? 'ซ.' . $item->lab_soi : null,
                    $item->lab_road ? 'ถ.' . $item->lab_road : null,
                    $item->LabSubdistrictName,
                    $item->LabDistrictName,
                    $item->LabProvinceName,
                    $item->lab_zipcode,
                ]);
                return implode(' ', $parts) ?: '-';
            })
            ->addColumn('manage', function ($item) {
                // ถ้ามีคำขอเปลี่ยนแปลงข้อมูลเดิมที่ยังแก้ไขได้อยู่ (เอกสารไม่ครบ/แก้ไขกลับมา) ให้พาไปแก้ไขคำขอเดิม
                // ไม่งั้นจะกลายเป็นสร้างคำขอใหม่ (เลขที่คำขอใหม่) ทุกครั้งที่กด "จัดการ"
                $existing = ApplicationLab::where('lab_id', $item->id)
                    ->where('applicant_type', 6)
                    ->whereIn('application_status', [0, 2, 15])
                    ->latest('id')
                    ->first();

                $url = url('/request-section-5/application-lab/change-info/details') . '?lab_id=' . $item->id;
                if ($existing) {
                    $url .= '&application_id=' . $existing->id;
                }

                return '<a href="' . $url . '" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> จัดการ</a>';
            })
            ->order(function ($query) {
                $query->orderBy('id', 'DESC');
            })
            ->rawColumns(['lab_info', 'lab_address_col', 'manage'])
            ->make(true);
    }

    public function details(Request $request)
    {
        $validated = $request->validate([
            'lab_id'         => 'required|integer',
            'application_id' => 'nullable|integer',
        ]);
        $lab_id   = $validated['lab_id'];
        // Laravel 5.6 (เวอร์ชันของโปรเจกต์นี้) ไม่มีเมธอด Request::boolean() — เพิ่มเข้ามาใน Laravel 7.0
        // เรียกแล้ว fatal 500 ทันที ("Call to undefined method") ทุกครั้งที่เข้าหน้านี้
        $readonly = filter_var($request->get('readonly'), FILTER_VALIDATE_BOOLEAN);

        $lab = Labs::where('id', $lab_id)->firstOrFail();

        // ── โหลดค่าที่เคยยื่นไว้ (กรณีเปิดจากปุ่ม "จัดการ"/"แก้ไข"/"ดูรายละเอียด" ของคำขอที่มีอยู่แล้ว) ──
        $existingApplication    = null;
        $existingAttachFiles    = collect();
        $existingAttachmentType = null;

        if (!empty($validated['application_id'])) {
            $existingApplication = ApplicationLab::where('id', $validated['application_id'])
                ->where('lab_id', $lab_id)
                ->where('applicant_type', 6)
                ->first();
        }

        if ($existingApplication) {
            $existingAttachFiles = AttachFile::where('ref_table', (new ApplicationLab)->getTable())
                ->where('ref_id', $existingApplication->id)
                ->orderBy('id')
                ->get()
                ->keyBy('section');

            // ไม่มีคอลัมน์ attachment_type เก็บไว้ในตาราง ต้อง infer จาก section ของไฟล์ที่เคยแนบ
            if ($existingAttachFiles->keys()->contains(function ($section) { return strpos($section, 'change_info_gov_file') === 0; })) {
                $existingAttachmentType = 1;
            } elseif ($existingAttachFiles->keys()->contains(function ($section) { return strpos($section, 'change_info_pri_file') === 0; })) {
                $existingAttachmentType = 2;
            }
        }

        return view('section5.application-lab.change-info.details', compact(
            'lab', 'existingApplication', 'existingAttachFiles', 'existingAttachmentType', 'readonly'
        ));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'lab_id'          => 'required|integer',
            'application_id'  => 'nullable|integer',
            'attachment_type' => 'required|in:1,2',
        ]);
        $lab_id = $validated['lab_id'];

        $lab = Labs::findOrFail($lab_id);

        // แก้ไขคำขอเปลี่ยนแปลงข้อมูลที่เคยยื่นไว้ → อัปเดต record เดิม ไม่สร้างคำขอซ้ำ
        $application = !empty($validated['application_id'])
            ? ApplicationLab::where('id', $validated['application_id'])
                ->where('lab_id', $lab_id)
                ->where('applicant_type', 6)
                ->first()
            : null;

        // บังคับแนบไฟล์ "ทุกไฟล์" ของประเภทหน่วยงานที่เลือก (เดิมบังคับแค่ข้อ 1 ตาม label "(ต้องมี)"
        // ตอนนี้เปลี่ยน logic ใหม่ตามที่ขอ: เลือกประเภทแล้วต้องแนบให้ครบทุกข้อของฝั่งนั้น)
        // ถ้าเป็นการแก้ไขคำขอเดิมที่เคยแนบไฟล์ข้อนั้นไว้แล้ว ไม่บังคับให้อัปโหลดซ้ำ
        $fileFieldLabels = [
            'gov_file_1' => '1. หนังสือขอเปลี่ยนแปลงข้อมูลจากหน่วยงานที่ลงนามโดยผู้มีอำนาจสูงสุด',
            'gov_file_2' => '2. หนังสือคำสั่งแต่งตั้งผู้อำนวยการ (กรณีหน่วยงานภายใต้การกำกับดูแล)',
            'gov_file_3' => '3. หนังสือมอบอำนาจ (กรณีมอบอำนาจ)',
            'pri_file_1' => '1. หนังสือขอเปลี่ยนแปลงข้อมูลจากหน่วยงานที่ลงนามโดยผู้มีอำนาจสูงสุด',
            'pri_file_2' => '2. หนังสือมอบอำนาจ พร้อมประทับตราบริษัท',
            'pri_file_3' => '3. สำเนาบัตรประชาชน/หนังสือเดินทาง ของผู้มอบอำนาจและผู้รับมอบอำนาจ',
            'pri_file_4' => '4. หนังสือรับรองบริษัท อายุไม่เกิน 6 เดือน',
        ];
        $fileFields = $validated['attachment_type'] == '1'
            ? ['gov_file_1', 'gov_file_2', 'gov_file_3']
            : ['pri_file_1', 'pri_file_2', 'pri_file_3', 'pri_file_4'];

        $existingSections = $application
            ? AttachFile::where('ref_table', (new ApplicationLab)->getTable())
                ->where('ref_id', $application->id)
                ->pluck('section')
                ->all()
            : [];

        $missingFieldErrors = [];
        foreach ($fileFields as $field) {
            $hasExistingFile = in_array('change_info_' . $field, $existingSections);
            if (!$request->hasFile($field) && !$hasExistingFile) {
                $missingFieldErrors[$field] = 'กรุณาแนบ "' . $fileFieldLabels[$field] . '"';
            }
        }

        if (!empty($missingFieldErrors)) {
            // ไม่ใช้ withInput() เพราะ request มีไฟล์ปน (files=>true) — flash ไฟล์เข้า session ไม่ได้/ไม่ควรทำ
            return back()->withErrors($missingFieldErrors);
        }

        if ($application) {
            // ล้างขอบข่ายที่เคย copy ไว้ (จะ copy ชุดใหม่จาก Labs ปัจจุบันแทน)
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

        $application->applicant_type     = 6;
        $application->application_date   = date('Y-m-d');
        $application->application_status = 1;

        $application->applicant_taxid = $lab->taxid;
        $application->applicant_name  = $lab->name;
        $application->lab_id          = $lab->id;
        $application->lab_code        = $lab->lab_code;

        // ข้อมูลใหม่ที่ขอเปลี่ยนแปลง
        $application->lab_name           = $request->input('lab_name', $lab->lab_name);
        $application->lab_address        = $request->input('lab_address');
        $application->lab_moo            = $request->input('lab_moo');
        $application->lab_soi            = $request->input('lab_soi');
        $application->lab_road           = $request->input('lab_road');
        $application->lab_building       = $request->input('lab_building');
        $application->lab_subdistrict_id = $request->input('lab_subdistrict_id');
        $application->lab_district_id    = $request->input('lab_district_id');
        $application->lab_province_id    = $request->input('lab_province_id');
        $application->lab_zipcode        = $request->input('lab_zipcode');
        $application->lab_phone          = $request->input('lab_phone');
        $application->lab_fax            = $request->input('lab_fax');

        $application->co_name     = $request->input('co_name');
        $application->co_position = $request->input('co_position');
        $application->co_mobile   = $request->input('co_mobile');
        $application->co_phone    = $request->input('co_phone');
        $application->co_fax      = $request->input('co_fax');
        $application->co_email    = $request->input('co_email');

        $application->created_by = auth()->user() ? auth()->user()->getKey() : null;
        $application->save();

        // copy ขอบข่ายปัจจุบันจาก Labs → application_labs_scope (เพื่อให้เจ้าหน้าที่เห็น)
        $labScopes = LabsScope::where('lab_id', $lab->id)->get();
        foreach ($labScopes as $scope) {
            ApplicationLabScope::create([
                'application_lab_id' => $application->id,
                'application_no'     => $application->application_no,
                'tis_id'             => $scope->tis_id,
                'tis_tisno'          => $scope->tis_tisno,
                'test_item_id'       => $scope->test_item_id,
                'lab_id'             => $scope->lab_id,
                'lab_code'           => $scope->lab_code,
            ]);
        }

        // บันทึกไฟล์แนบ ($fileFields คำนวณไว้แล้วด้านบนตอนเช็คไฟล์บังคับ)
        $storagePath = 'files/sso/Section5/ChangeInfoLab/' . $lab->lab_code;

        foreach ($fileFields as $field) {
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                $file = $request->file($field);

                // ชื่อไฟล์จริงบน FTP ต้องเป็น ASCII เท่านั้น (ext ftp ของ PHP ไม่รองรับ UTF-8/ไทยในชื่อไฟล์
                // ส่งผลให้ไฟล์ถูกเขียนด้วยชื่อที่ถูกเข้ารหัสผิดเพี้ยนบน NAS จนหาไฟล์กลับมาไม่เจอ)
                // เก็บชื่อไฟล์เดิม (ภาษาไทยได้) ไว้แค่ในคอลัมน์ filename สำหรับแสดงผลเท่านั้น
                $originalName = $file->getClientOriginalName();
                $safeFilename = str_random(10) . '_' . date('YmdHis') . '.' . $file->getClientOriginalExtension();

                $url = Storage::disk('ftp')->putFileAs($storagePath, $file, $safeFilename);

                // ใช้ firstOrNew แทน new ตรงๆ ไม่งั้นทุกครั้งที่แก้ไข/อัปโหลดซ้ำ section เดิม
                // จะได้ record ซ้ำใน attach_files เรื่อยๆ (ของเดิมค้างเป็นไฟล์เสีย/เพี้ยนแล้วยังโผล่ปนอยู่)
                $attach               = AttachFile::firstOrNew([
                    'ref_table' => (new ApplicationLab)->getTable(),
                    'ref_id'    => $application->id,
                    'section'   => 'change_info_' . $field,
                ]);
                $attach->url          = $url;
                $attach->filename     = $originalName;
                $attach->new_filename = $safeFilename;
                $attach->caption      = $field;
                $attach->created_by   = auth()->user() ? auth()->user()->getKey() : null;
                $attach->save();
            }
        }

        // เดิม redirect กลับไปหน้า list ของ change-info เอง ไม่สอดคล้องกับ flow อื่น (เช่นคำขอยกเลิก
        // ที่ redirect กลับหน้าหลัก request-section-5/application-lab หลังบันทึกเสร็จ) เปลี่ยนให้ตรงกัน
        return redirect(url('/request-section-5/application-lab'))
            ->with('message', 'บันทึกคำขอเปลี่ยนแปลงข้อมูลเรียบร้อย');
    }
}
