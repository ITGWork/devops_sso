<?php

namespace App\Http\Controllers\Section5;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use HP;

use App\Models\Section5\ApplicationIbcb;
use App\Models\Section5\ApplicationIbcbScope;
use App\Models\Section5\ApplicationIbcbScopeDetail;
use App\Models\Section5\ApplicationIbcbScopeTis;
use App\Models\Section5\IbcbsScope;
use App\Models\Section5\Ibcbs;
use App\AttachFile;
use Illuminate\Support\Facades\Storage;

class ApplicationIbcbChangeInfoController extends Controller
{
    private $attach_path;//ที่เก็บไฟล์แนบ

    public function __construct()
    {
        $this->middleware('auth');
        $this->attach_path = 'files/sso';
    }

    public function index(Request $request)
    {
        return view('section5.application-ib-cb.change-info.index');
    }

    public function data_list(Request $request)
    {
        $user = auth()->user();
        $filter_search = $request->get('filter_search');

        $applicationIbcbTable = (new ApplicationIbcb)->getTable();
        $ibcbsTable            = (new Ibcbs)->getTable();

        $query = Ibcbs::where('state', 1)
            ->whereExists(function ($q) use ($applicationIbcbTable, $ibcbsTable) {
                $q->select(DB::raw(1))
                  ->from($applicationIbcbTable)
                  ->whereColumn($applicationIbcbTable.'.application_no', $ibcbsTable.'.ref_ibcb_application_no')
                  ->where($applicationIbcbTable.'.application_status', 15); // 15 = ประกาศราชกิจจาฯ แล้ว
            })
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
            ->addColumn('ibcb_address_col', function ($item) {
                $parts = array_filter([
                    $item->ibcb_address,
                    $item->ibcb_moo ? 'หมู่ ' . $item->ibcb_moo : null,
                    $item->ibcb_soi ? 'ซ.' . $item->ibcb_soi : null,
                    $item->ibcb_road ? 'ถ.' . $item->ibcb_road : null,
                    $item->IbcbSubdistrictName,
                    $item->IbcbDistrictName,
                    $item->IbcbProvinceName,
                    $item->ibcb_zipcode,
                ]);
                return implode(' ', $parts) ?: '-';
            })
            ->addColumn('manage', function ($item) {
                // ถ้ามีคำขอเปลี่ยนแปลงข้อมูลเดิมที่ยังแก้ไขได้อยู่ (เอกสารไม่ครบ/แก้ไขกลับมา) ให้พาไปแก้ไขคำขอเดิม
                // ไม่งั้นจะกลายเป็นสร้างคำขอใหม่ (เลขที่คำขอใหม่) ทุกครั้งที่กด "จัดการ" (มิเรอร์จาก Lab)
                $existing = ApplicationIbcb::where('ibcb_id', $item->id)
                    ->where('applicant_request_type', 6)
                    ->whereIn('application_status', [0, 2, 15])
                    ->latest('id')
                    ->first();

                $url = url('/request-section-5/application-ibcb/change-info/details') . '?ibcb_id=' . $item->id;
                if ($existing) {
                    $url .= '&application_id=' . $existing->id;
                }

                return '<a href="' . $url . '" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> จัดการ</a>';
            })
            ->order(function ($query) {
                $query->orderBy('id', 'DESC');
            })
            ->rawColumns(['ibcb_info', 'ibcb_address_col', 'manage'])
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

        // ── โหลดค่าที่เคยยื่นไว้ (กรณีเปิดจากปุ่ม "จัดการ"/"แก้ไข"/"ดูรายละเอียด" ของคำขอที่มีอยู่แล้ว) ──
        // มิเรอร์จาก ApplicationLabChangeInfoController::details()
        $existingApplication    = null;
        $existingAttachFiles    = collect();
        $existingAttachmentType = null;

        if (!empty($validated['application_id'])) {
            $existingApplication = ApplicationIbcb::where('id', $validated['application_id'])
                ->where('ibcb_id', $ibcb_id)
                ->where('applicant_request_type', 6)
                ->first();
        }

        if ($existingApplication) {
            $existingAttachFiles = AttachFile::where('ref_table', (new ApplicationIbcb)->getTable())
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

        return view('section5.application-ib-cb.change-info.details', compact(
            'ibcbs', 'existingApplication', 'existingAttachFiles', 'existingAttachmentType', 'readonly'
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

        // แก้ไขคำขอเปลี่ยนแปลงข้อมูลที่เคยยื่นไว้ → อัปเดต record เดิม ไม่สร้างคำขอซ้ำ (มิเรอร์จาก Lab)
        $application = !empty($validated['application_id'])
            ? ApplicationIbcb::where('id', $validated['application_id'])
                ->where('ibcb_id', $ibcb_id)
                ->where('applicant_request_type', 6)
                ->first()
            : null;

        // บังคับแนบไฟล์ "ทุกไฟล์" ของประเภทหน่วยงานที่เลือก (มิเรอร์ logic เดียวกับ Lab change-info)
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
            ? AttachFile::where('ref_table', (new ApplicationIbcb)->getTable())
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
            return back()->withErrors($missingFieldErrors);
        }

        if (!$application) {
            $ref = ($ibcbs->ibcb_type == 2) ? 'CB' : 'IB';
            $gen_number = HP::ConfigFormat('APP-IB-CB', (new ApplicationIbcb)->getTable(), 'application_no', $ref, null, null);
            $application_check = ApplicationIbcb::where('application_no', $gen_number)->first();
            if (!is_null($application_check)) {
                $gen_number = HP::ConfigFormat('APP-IB-CB', (new ApplicationIbcb)->getTable(), 'application_no', $ref, null, null);
            }

            $application = new ApplicationIbcb;
            $application->application_no = $gen_number;
        } else {
            // ล้างขอบข่ายที่เคย copy ไว้ (จะ copy ชุดใหม่จาก Ibcbs ปัจจุบันแทน)
            $oldScopeIds = ApplicationIbcbScope::where('application_id', $application->id)->pluck('id');
            ApplicationIbcbScopeDetail::whereIn('ibcb_scope_id', $oldScopeIds)->delete();
            ApplicationIbcbScopeTis::whereIn('ibcb_scope_id', $oldScopeIds)->delete();
            ApplicationIbcbScope::where('application_id', $application->id)->delete();
        }

        $application->applicant_request_type = 6; // ขอเปลี่ยนแปลงข้อมูลหน่วยตรวจสอบ
        $application->application_date   = date('Y-m-d');
        $application->application_status = 1;
        $application->application_type   = $ibcbs->ibcb_type;

        $application->applicant_taxid = $ibcbs->taxid;
        $application->applicant_name  = $ibcbs->name;
        $application->ibcb_id         = $ibcbs->id;
        $application->ibcb_code       = $ibcbs->ibcb_code;

        // ข้อมูลใหม่ที่ขอเปลี่ยนแปลง
        $application->ibcb_name           = $request->input('ibcb_name', $ibcbs->ibcb_name);
        $application->ibcb_address        = $request->input('ibcb_address');
        $application->ibcb_moo            = $request->input('ibcb_moo');
        $application->ibcb_soi            = $request->input('ibcb_soi');
        $application->ibcb_road           = $request->input('ibcb_road');
        $application->ibcb_building       = $request->input('ibcb_building');
        $application->ibcb_subdistrict_id = $request->input('ibcb_subdistrict_id');
        $application->ibcb_district_id    = $request->input('ibcb_district_id');
        $application->ibcb_province_id    = $request->input('ibcb_province_id');
        $application->ibcb_zipcode        = $request->input('ibcb_zipcode');
        $application->ibcb_phone          = $request->input('ibcb_phone');
        $application->ibcb_fax            = $request->input('ibcb_fax');

        $application->co_name     = $request->input('co_name');
        $application->co_position = $request->input('co_position');
        $application->co_mobile   = $request->input('co_mobile');
        $application->co_phone    = $request->input('co_phone');
        $application->co_fax      = $request->input('co_fax');
        $application->co_email    = $request->input('co_email');

        $application->created_by = auth()->user() ? auth()->user()->getKey() : null;
        $application->save();

        // copy ขอบข่ายปัจจุบันของหน่วยตรวจสอบ (3 ระดับ: กลุ่ม/สาขา/เลขมอก.) ไปแสดงในคำขอ เพื่อให้เจ้าหน้าที่เห็นขอบข่ายเดิม
        $liveScopes = IbcbsScope::where('ibcb_id', $ibcbs->id)->get();
        foreach ($liveScopes as $liveScope) {
            $scope = new ApplicationIbcbScope;
            $scope->application_id  = $application->id;
            $scope->application_no  = $application->application_no;
            $scope->branch_group_id = $liveScope->branch_group_id;
            $scope->isic_no         = $liveScope->isic_no;
            $scope->type            = 1; // ขอบข่ายเดิม
            $scope->ibcb_id         = $ibcbs->id;
            $scope->ibcb_code       = $ibcbs->ibcb_code;
            $scope->created_by      = auth()->user()->getKey();
            $scope->save();

            foreach ($liveScope->scopes_details as $liveDetail) {
                $detail = new ApplicationIbcbScopeDetail;
                $detail->ibcb_scope_id  = $scope->id;
                $detail->application_no = $application->application_no;
                $detail->branch_id      = $liveDetail->branch_id;
                $detail->ibcb_id        = $ibcbs->id;
                $detail->ibcb_code      = $ibcbs->ibcb_code;
                $detail->save();
            }

            foreach ($liveScope->scopes_tis as $liveTis) {
                $tis = new ApplicationIbcbScopeTis;
                $tis->ibcb_scope_id = $scope->id;
                $tis->tis_id        = $liveTis->tis_id;
                $tis->tis_no        = $liveTis->tis_no;
                $tis->ibcb_id       = $ibcbs->id;
                $tis->ibcb_code     = $ibcbs->ibcb_code;
                $tis->save();
            }
        }

        // บันทึกไฟล์แนบ ($fileFields คำนวณไว้แล้วด้านบนตอนเช็คไฟล์บังคับ)
        $attach_path = $this->attach_path.'/Section5/ApplicationIbcb/'.$application->application_no;

        foreach ($fileFields as $field) {
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                $file = $request->file($field);

                // ชื่อไฟล์จริงบน FTP ต้องเป็น ASCII เท่านั้น เก็บชื่อไทยไว้แค่คอลัมน์ filename สำหรับแสดงผล
                $originalName = $file->getClientOriginalName();
                $safeFilename = str_random(10) . '_' . date('YmdHis') . '.' . $file->getClientOriginalExtension();

                $url = Storage::disk('ftp')->putFileAs($attach_path, $file, $safeFilename);

                // ใช้ firstOrNew แทน new/create() ตรงๆ ไม่งั้นทุกครั้งที่แก้ไข/อัปโหลดซ้ำ section เดิม
                // จะได้ record ซ้ำใน attach_files เรื่อยๆ
                $attach                = AttachFile::firstOrNew([
                    'ref_table' => (new ApplicationIbcb)->getTable(),
                    'ref_id'    => $application->id,
                    'section'   => 'change_info_' . $field,
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
            ->with('flash_message', 'บันทึกคำขอเปลี่ยนแปลงข้อมูลเรียบร้อย');
    }
}
