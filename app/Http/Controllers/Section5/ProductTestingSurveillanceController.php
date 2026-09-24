<?php

namespace App\Http\Controllers\Section5;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use HP;

class ProductTestingSurveillanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('section5.product-testing-surveillance.index');
    }

    /**
     * GET /section5/product-testing-surveillance/data_list
     * Filter โดย lab_user_id ที่เก็บใน name_lap (ข้อมูลใหม่ = SSO user ID, เก่า = ชื่อบริษัท)
     */
    public function data_list(Request $request)
    {
        $user          = auth()->user();
        $filter_search = $request->input('filter_search');
        $filter_status = $request->input('filter_status');

        $query = DB::table('save_example_map_lap as t1')
            ->leftJoin('save_example as t2', 't1.example_id', '=', 't2.id')
            ->leftJoin('tb3_tis as tis', 't2.tis_standard', '=', 'tis.tb3_Tisno')
            ->leftJoin('section5_labs as lab', 'lab.id', '=', 't1.detail_product')
            ->leftJoin('tb4_licensesizedetial as sizedetail', 'sizedetail.autoNO', '=', 't1.detail_product_maplap')
            ->select([
                't1.id',
                't1.no_example_id as refno',
                't1.licensee as applicant_name',
                't2.licensee_no as tax_number',
                't1.tis_standard as tis',
                't1.status',
                't1.user_lab as checking_by',
                't1.created_at as date_sent',
                't1.status_send as test_result',
                't1.name_lap as name_lap_raw',
                't1.detail_product_maplap',
                'sizedetail.sizeDetial as detail_label',
                DB::raw("COALESCE(lab.lab_name, t1.name_lap) as lab_display_name"),
            ])
            ->where(function ($q) use ($user) {
                // ข้อมูลใหม่: name_lap เก็บ SSO user ID
                $q->where('t1.name_lap', (string) $user->id)
                  // ข้อมูลเก่า: name_lap เก็บชื่อบริษัท (backward compat)
                  ->orWhere('t1.name_lap', $user->name);
            });

        if (!empty($filter_search)) {
            $query->where(function ($q) use ($filter_search) {
                $q->where('t1.no_example_id', 'like', "%{$filter_search}%")
                    ->orWhere('t1.licensee', 'like', "%{$filter_search}%")
                    ->orWhere('t1.tis_standard', 'like', "%{$filter_search}%");
            });
        }

        if ($filter_status !== null && $filter_status !== '') {
            $query->where('t1.status', $filter_status);
        }

        $query->orderByDesc('t1.id');

        try {
            $rows = $query->get();

            // จัดกลุ่มตาม no_example_id: คำขอเดียวกันที่มีหลายรายการทดสอบ (detail_product_maplap ต่างกัน)
            // ต้องอยู่แถวเดียวกันในหน้ารายการ แต่ยังแยก action/รายงานผลเป็นรายการย่อยตาม id เดิม
            $groups = [];
            $order  = [];
            foreach ($rows as $row) {
                $key = $row->refno;
                if (!isset($groups[$key])) {
                    // ปุ่ม action ระดับกลุ่ม: รับคำขอ/บันทึกผลครั้งเดียวครอบคลุมทุกรายการทดสอบใน no_example_id นี้
                    // ใช้ id ของรายการแรกที่เจอเป็นตัวแทน (approve_save/report_save จะ cascade ไปทั้งกลุ่มเองฝั่ง backend)
                    $groupAction = '';
                    if ($row->status == 1) {
                        $groupAction = '<a href="' . url('/section5/product-testing-surveillance/approve/' . $row->id) . '" class="btn btn-success btn-xs"><i class="fa fa-check-circle"></i> รับคำขอ</a>';
                    } elseif ($row->status == 2) {
                        $groupAction = '<a href="' . url('/section5/product-testing-surveillance/report/esurv-' . $row->id) . '" class="btn btn-info btn-xs"><i class="fa fa-pencil-square-o"></i> บันทึกผล</a>';
                    } else {
                        $groupAction = '<a href="' . url('/section5/product-testing-surveillance/approve/' . $row->id) . '" class="btn btn-default btn-xs"><i class="fa fa-eye"></i></a>';
                    }

                    $groups[$key] = (object) [
                        'refno'           => $row->refno,
                        'applicant_name'  => $row->applicant_name,
                        'tax_number'      => $row->tax_number,
                        'tis'             => $row->tis,
                        'date_sent'       => !empty($row->date_sent) ? HP::DateThai($row->date_sent) : '-',
                        'status'          => HP::Section5StatusProductTesting($row->status),
                        'action'          => $groupAction,
                        'items'           => [],
                    ];
                    $order[] = $key;
                }

                $groups[$key]->items[] = [
                    'detail_label'  => $row->detail_label ?? ('#' . $row->detail_product_maplap),
                    'status'        => HP::Section5StatusProductTesting($row->status),
                    'test_result'   => HP::map_lap_status($row->test_result ?? ''),
                    'checking_by'   => $row->checking_by ?? '-',
                ];
            }

            $groupList = array_values($groups);
            $recordsTotal = count($groupList);

            $start  = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);
            $paged  = $length > 0 ? array_slice($groupList, $start, $length) : $groupList;

            return response()->json([
                'draw'            => (int) $request->input('draw', 1),
                'recordsTotal'    => $recordsTotal,
                'recordsFiltered' => $recordsTotal,
                'data'            => $paged,
            ]);
        } catch (\Exception $e) {
            \Log::error('ProductTestingSurveillanceController@data_list error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /section5/product-testing-surveillance/approve/{id}
     */
    public function approve(Request $request, $id)
    {
        // Ensure dynamic columns exist
        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('save_example_map_lap', 'start_inspect_date')) {
                DB::statement("ALTER TABLE save_example_map_lap ADD COLUMN start_inspect_date DATE NULL");
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('save_example_map_lap', 'end_inspect_date')) {
                DB::statement("ALTER TABLE save_example_map_lap ADD COLUMN end_inspect_date DATE NULL");
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('save_example_map_lap', 'result')) {
                DB::statement("ALTER TABLE save_example_map_lap ADD COLUMN result VARCHAR(10) NULL");
            }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('save_example_map_lap', 'defect')) {
                DB::statement("ALTER TABLE save_example_map_lap ADD COLUMN defect TEXT NULL");
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('alter columns: ' . $e->getMessage());
        }

        $item = DB::table('save_example_map_lap')->where('id', $id)->first();
        if (!$item) {
            return redirect('/section5/product-testing-surveillance')->with('error_message', 'ไม่พบข้อมูล');
        }

        // Security: verify this item belongs to the logged-in trader
        $this->_checkOwnership($item);

        if ($item->status == 2) {
            return redirect('/section5/product-testing-surveillance/report/esurv-' . $id);
        }

        [$product, $user_created] = $this->_loadProductAndUser($item);

        $inspections = DB::table('save_example_map_lap')
            ->where('example_id', $item->example_id)
            ->where('id', '!=', $id)
            ->get();

        foreach ($inspections as $insp) {
            foreach (['start_inspect_date', 'end_inspect_date', 'result', 'defect', 'remark', 'att_file'] as $prop) {
                if (!property_exists($insp, $prop)) $insp->$prop = null;
            }
        }

        $action = $request->get('action');
        $report = null;
        try {
            $report = DB::table('lab_report_info')->where('maplap_id', $item->id)->first();
        } catch (\Exception $e) { }

        $test_items = $this->_loadTestItems($item);

        $existing_results = [];
        try {
            $records = DB::table('lab_report_results')->where('maplap_id', $item->id)->get();
            foreach ($records as $rec) {
                $existing_results[$rec->test_item_id][$rec->test_no] = [
                    'measured_value' => $rec->measured_value,
                    'item_result'    => $rec->item_result,
                ];
            }
        } catch (\Exception $e) { }

        if (is_null($report) && count($test_items) > 0) {
            $report = (object)['overall_result' => '-'];
        }

        $qc_items   = DB::connection('mysql_elicense')->table('ros_rform_factory_qc_items')->where('published', 1)->orderBy('ordering')->get();
        $qc_results = DB::connection('mysql_elicense')->table('ros_rform_factory_qc_results')->where('factory_id', $id)->get()->keyBy('item_id');

        return view('section5.product-testing-surveillance.approve', compact(
            'item', 'product', 'action', 'report', 'qc_items', 'qc_results',
            'user_created', 'inspections', 'test_items', 'existing_results'
        ));
    }

    /**
     * POST /section5/product-testing-surveillance/approve-save
     * รับ/ไม่รับคำขอ (status 2=รับ, 3=ไม่รับ)
     */
    public function approve_save(Request $request)
    {
        $request->validate(['id' => 'required', 'status' => 'required']);

        $id     = $request->input('id');
        $status = (int)$request->input('status');
        $remark = $request->input('remark');
        $user   = auth()->user();

        $item = DB::table('save_example_map_lap')->where('id', $id)->first();
        if (!$item) {
            return redirect()->back()->with('error_message', 'ไม่พบข้อมูล');
        }

        $this->_checkOwnership($item);

        // รับ/ไม่รับคำขอ ต้องครอบคลุมทุกรายการทดสอบภายใต้เลขที่อ้างอิง (no_example_id) เดียวกัน ไม่ใช่แค่รายการที่กดมา
        $siblingIds = $this->_siblingIds($item);

        DB::table('save_example_map_lap')->whereIn('id', $siblingIds)->update([
            'status'     => $status,
            'name_lap'   => (string) $user->id,   // อัปเดตเป็น ID สำหรับข้อมูลเก่าที่ยังเป็น string
            'user_lab'   => $user->name,
            'updated_at' => now(),
        ]);

        if ($status == 2) {
            return redirect('/section5/product-testing-surveillance/report/esurv-' . $id)
                ->with('flash_message', 'รับคำขอเรียบร้อยแล้ว');
        }

        return redirect('/section5/product-testing-surveillance')
            ->with('flash_message', 'บันทึกผลการพิจารณาเรียบร้อยแล้ว');
    }

    /**
     * POST /section5/product-testing-surveillance/result-save
     * รับตัวอย่าง (action=acceptance) หรือบันทึกผลตรวจ
     */
    public function result_save(Request $request)
    {
        try {
            $id     = $request->input('id');
            $action = $request->input('action');
            $user   = auth()->user();

            $item = DB::table('save_example_map_lap')->where('id', $id)->first();
            if (!$item) {
                return redirect()->back()->with('error_message', 'ไม่พบข้อมูล');
            }

            $this->_checkOwnership($item);

            if ($action == 'acceptance') {
                DB::table('save_example_map_lap')->where('id', $id)->update([
                    'status'     => 2,
                    'name_lap'   => (string) $user->id,
                    'user_lab'   => $user->name,
                    'updated_at' => now(),
                ]);
                return redirect('/section5/product-testing-surveillance/report/esurv-' . $id)
                    ->with('flash_message', 'รับคำขอและตัวอย่างเรียบร้อยแล้ว');
            }

            DB::table('save_example_map_lap')->where('id', $id)->update([
                'start_inspect_date' => $request->input('start_inspect_date'),
                'end_inspect_date'   => $request->input('end_inspect_date'),
                'result'             => $request->input('result'),
                'defect'             => $request->input('defect'),
                'remark'             => $request->input('remark'),
            ]);

            return redirect('section5/product-testing-surveillance/approve/' . $id)
                ->with('flash_message', 'บันทึกข้อมูลเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_message', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * GET /section5/product-testing-surveillance/report/{id}
     */
    public function report($id)
    {
        $source_type  = 'elicense';
        $item_id      = $id;

        if (strpos($id, 'esurv-') === 0) {
            $source_type = 'esurv';
            $item_id     = str_replace('esurv-', '', $id);
        }

        $report_no      = null;
        $report         = new \stdClass();
        $other_reports  = collect([]);
        $test_items     = collect([]);
        $existing_results = [];
        $product        = new \stdClass();
        $user_created   = new \stdClass();

        if ($source_type === 'esurv') {
            $item = DB::table('save_example_map_lap')->where('id', $item_id)->first();
            if (!$item) {
                return redirect('/section5/product-testing-surveillance')->with('error_message', 'ไม่พบข้อมูล');
            }

            $this->_checkOwnership($item);

            [$product, $user_created] = $this->_loadProductAndUser($item);

            // รับคำขอ/บันทึกผล ครอบคลุมทุกรายการทดสอบภายใต้เลขที่อ้างอิงเดียวกัน (no_example_id)
            // ดึงรายการทดสอบของทุก id ในกลุ่ม มารวมกัน โดยติด maplap_id กำกับแต่ละรายการ เพื่อแยกตารางย่อยตามรายละเอียดผลิตภัณฑ์
            $siblingIds = $this->_siblingIds($item);
            $siblingItems = DB::table('save_example_map_lap as t1')
                ->leftJoin('tb4_licensesizedetial as sizedetail', 'sizedetail.autoNO', '=', 't1.detail_product_maplap')
                ->whereIn('t1.id', $siblingIds)
                ->select(['t1.*', 'sizedetail.sizeDetial as detail_label'])
                ->get();

            $test_items = collect([]);
            foreach ($siblingItems as $siblingItem) {
                $siblingTestItems = $this->_loadTestItems($siblingItem);
                foreach ($siblingTestItems as $ti) {
                    $ti->maplap_id    = $siblingItem->id;
                    $ti->detail_label = $siblingItem->detail_label ?? ('#' . $siblingItem->detail_product_maplap);
                    $test_items->push($ti);
                }
            }

            $existing_report = null;
            try {
                $existing_report = DB::table('lab_report_info')->where('maplap_id', $item->id)->first();
            } catch (\Exception $e) { }

            if ($existing_report) {
                $report    = $existing_report;
                $report_no = $report->report_no ?? null;
            } elseif (count($test_items) > 0) {
                $report = (object)['overall_result' => '-'];
            }

            try {
                $records = DB::table('lab_report_results')->whereIn('maplap_id', $siblingIds)->get();
                foreach ($records as $rec) {
                    $existing_results[$rec->maplap_id][$rec->test_item_id][$rec->test_no] = [
                        'measured_value' => $rec->measured_value,
                        'item_result'    => $rec->item_result,
                    ];
                }
            } catch (\Exception $e) { }
        } else {
            $item = DB::table('save_example_map_lap')->where('id', $item_id)->first();
            if (!$item) {
                return redirect('/section5/product-testing-surveillance')->with('error_message', 'ไม่พบข้อมูล');
            }
        }

        return view('section5.product-testing-surveillance.report', compact(
            'source_type', 'item', 'product', 'user_created',
            'report', 'report_no', 'other_reports', 'test_items', 'existing_results'
        ));
    }

    /**
     * POST /section5/product-testing-surveillance/report-save
     */
    public function report_save(Request $request)
    {
        $request->validate([
            'id'              => 'required',
            'overall_result'  => 'required|in:pass,fail',
            'measured'        => 'array',
            'measured_mix'    => 'array',
            'item_result'     => 'array',
            'full_report_pdf' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $sourceType = $request->input('source_type', 'esurv');
        if ($sourceType !== 'esurv') {
            return redirect('/section5/product-testing-surveillance')->with('error_message', 'ยังไม่รองรับประเภทนี้');
        }

        $itemId = (int) $request->input('id');
        $item   = DB::table('save_example_map_lap')->where('id', $itemId)->first();
        if (!$item) {
            return redirect('/section5/product-testing-surveillance')->with('error_message', 'ไม่พบข้อมูล');
        }

        $this->_checkOwnership($item);

        // บันทึกผลครอบคลุมทุกรายการทดสอบภายใต้เลขที่อ้างอิงเดียวกัน (no_example_id) ไม่ใช่แค่ id ที่ส่งมา
        $siblingIds   = $this->_siblingIds($item);
        $measured     = $request->input('measured', []);
        $measured_mix = $request->input('measured_mix', []);
        $item_result  = $request->input('item_result', []);

        DB::beginTransaction();
        try {
            $filePath = null;
            if ($request->hasFile('full_report_pdf')) {
                $filePath = $request->file('full_report_pdf')->store('product_testing_surveillance_full_report', 'ftp');
            }

            foreach ($siblingIds as $maplapId) {
                $siblingRow = DB::table('save_example_map_lap')->where('id', $maplapId)->first();
                $test_items = $this->_loadTestItems($siblingRow);

                $reportData = [
                    'overall_result' => $request->input('overall_result'),
                    'report_date'    => now()->toDateString(),
                    'updated_at'     => now(),
                ];
                if ($filePath) {
                    $reportData['report_file_path'] = $filePath;
                }

                $existingReport = DB::table('lab_report_info')->where('maplap_id', $maplapId)->first();
                if ($existingReport) {
                    DB::table('lab_report_info')->where('id', $existingReport->id)->update($reportData);
                } else {
                    $reportData['maplap_id']   = $maplapId;
                    $reportData['report_no']   = null;
                    $reportData['created_at']  = now();
                    $reportInfoId = DB::table('lab_report_info')->insertGetId($reportData);
                    // ใช้ id ที่ได้เป็นเลขที่รายงาน (ตาม pattern เดียวกับระบบ product-testing ที่ไม่มีปัญหา)
                    DB::table('lab_report_info')->where('id', $reportInfoId)->update(['report_no' => (string) $reportInfoId]);
                }

                // ล้างผลการทดสอบเดิม (กรณีแก้ไข/ส่งซ้ำ) แล้วบันทึกใหม่ทั้งหมด
                DB::table('lab_report_results')->where('maplap_id', $maplapId)->delete();

                $rows = [];
                foreach ($test_items as $ti) {
                    // ต้องใช้ fallback เดียวกับ partials/test_report_table.blade.php (amount_test_list ว่าง -> 8 ครั้ง)
                    // ไม่งั้นฟอร์มจะเรนเดอร์ให้กรอก 8 ช่อง แต่ตรงนี้จะข้ามรายการทิ้งหมดเพราะ maxSeq=0
                    $maxSeq = (int) ($ti->amount_test_list ?? 0);
                    if ($maxSeq <= 0) $maxSeq = 8;

                    for ($seq = 1; $seq <= $maxSeq; $seq++) {
                        $mv = $measured[$maplapId][$ti->id][$seq] ?? null;
                        if (isset($measured_mix[$maplapId][$ti->id][$seq])) {
                            $mv = json_encode($measured_mix[$maplapId][$ti->id][$seq], JSON_UNESCAPED_UNICODE);
                        }
                        if (is_array($mv)) {
                            $mv = implode(',', $mv);
                        }

                        $rs = $item_result[$maplapId][$ti->id][$seq] ?? null;
                        if (($mv === null || $mv === '') && ($rs === null || $rs === '')) continue;

                        $rows[] = [
                            'maplap_id'      => $maplapId,
                            'test_item_id'   => $ti->id,
                            'test_no'        => $seq,
                            'measured_value' => ($mv === '' ? null : $mv),
                            'item_result'    => ($rs === '' ? null : $rs),
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ];
                    }
                }
                if (!empty($rows)) {
                    DB::table('lab_report_results')->insert($rows);
                }
            }

            // อัปเดตสถานะคำขอ -> 5 (แจ้งผล) ทุก id ในกลุ่มพร้อมกัน
            DB::table('save_example_map_lap')->whereIn('id', $siblingIds)->update([
                'status'     => 5,
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect('/section5/product-testing-surveillance')
                ->with('flash_message', 'บันทึกรายงานผลการทดสอบสำเร็จ');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('ProductTestingSurveillanceController@report_save error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error_message', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // -----------------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------------

    /**
     * รายการ id ทั้งหมดของ save_example_map_lap ที่มี no_example_id เดียวกับ $item (รวม $item->id เองด้วย)
     * ใช้ทำให้รับคำขอ/บันทึกผล ครอบคลุมทุกรายการทดสอบภายใต้เลขที่อ้างอิงเดียวกันพร้อมกัน
     */
    private function _siblingIds($item): array
    {
        return DB::table('save_example_map_lap')
            ->where('no_example_id', $item->no_example_id)
            ->pluck('id')
            ->toArray();
    }

    /** Abort 403 if the item does not belong to the logged-in LAB user */
    private function _checkOwnership($item)
    {
        $user    = auth()->user();
        $nameLap = (string) ($item->name_lap ?? '');

        // ข้อมูลใหม่: name_lap = SSO user ID (เช่น "18077")
        $byId   = ($nameLap === (string) $user->id);
        // ข้อมูลเก่า: name_lap = ชื่อบริษัท (backward compat)
        $byName = ($nameLap === (string) $user->name);

        if (!$byId && !$byName) {
            abort(403);
        }
    }

    /** Build $product (stdClass) and $user_created (stdClass) from save_example */
    private function _loadProductAndUser($item): array
    {
        $product_example = DB::table('save_example')->where('id', $item->example_id)->first();
        $product = new \stdClass();

        if ($product_example) {
            $product->refno       = $product_example->no ?? '-';
            $product->ref_no      = $product_example->licensee_no ?? '-';
            $product->execute_date = $product_example->created_at ?? null;
            $product->tis_number  = $product_example->tis_standard ?? null;
            $product->coordinator = $product_example->licensee ?? '-';

            if (!empty($product_example->tis_standard)) {
                $tis = DB::table('tb3_tis')->where('tb3_Tisno', $product_example->tis_standard)->first();
                $product->tis_name = $tis ? $tis->tb3_TisThainame : '-';
            } else {
                $product->tis_name = '-';
            }
        } else {
            foreach (['refno', 'ref_no', 'tis_number', 'tis_name', 'coordinator'] as $p) {
                $product->$p = '-';
            }
        }

        $extra_props = [
            'applicant_name', 'factory_name', 'factory_address_no', 'factory_moo',
            'factory_soi', 'factory_street', 'factory_subdistrict', 'factory_district',
            'factory_province', 'factory_zipcode', 'factory_register_no',
            'storage_name', 'storage_address_no', 'storage_moo', 'storage_soi',
            'storage_street', 'storage_subdistrict', 'storage_district',
            'storage_province', 'storage_zipcode', 'coordinator_position', 'coordinator_tel',
            'receive_date', 'product_detail',
        ];
        foreach ($extra_props as $p) {
            if (!property_exists($product, $p)) $product->$p = null;
        }

        // Load user_created from sso_users via license
        $user_created = new \stdClass();
        if ($product_example && !empty($product_example->licensee_no)) {
            $license = DB::table('tb4_tisilicense')
                ->where('tbl_licenseNo', $product_example->licensee_no)
                ->first();
            if ($license && !empty($license->tbl_taxpayer)) {
                $row = DB::table('sso_users')->where('tax_number', $license->tbl_taxpayer)->first();
                if ($row) $user_created = $row;
            }
        }

        $user_props = [
            'name', 'nationality', 'id_card_no', 'tax_number', 'email', 'tel',
            'address_no', 'moo', 'soi', 'street', 'subdistrict', 'district', 'province', 'zipcode',
            'head_address_no', 'head_moo', 'head_soi', 'head_street', 'head_subdistrict',
            'head_district', 'head_province', 'head_zipcode', 'head_tel',
            'date_niti', 'register_no', 'commercial_register_no',
        ];
        foreach ($user_props as $p) {
            if (!property_exists($user_created, $p)) $user_created->$p = null;
        }
        $user_created->head_moo        = $user_created->head_moo        ?? $user_created->moo        ?? null;
        $user_created->head_soi        = $user_created->head_soi        ?? $user_created->soi        ?? null;
        $user_created->head_street     = $user_created->head_street     ?? $user_created->street     ?? null;
        $user_created->head_subdistrict = $user_created->head_subdistrict ?? $user_created->subdistrict ?? null;
        $user_created->head_district   = $user_created->head_district   ?? $user_created->district   ?? null;
        $user_created->head_province   = $user_created->head_province   ?? $user_created->province   ?? null;
        $user_created->head_zipcode    = $user_created->head_zipcode    ?? $user_created->zipcode    ?? null;
        $user_created->head_tel        = $user_created->head_tel        ?? $user_created->tel        ?? null;

        return [$product, $user_created];
    }

    /** Query builder ร่วมสำหรับดึงรายการทดสอบ พร้อม join ตารางชื่อ (หน่วย/หัวข้อแม่/วิธีทดสอบ/เครื่องมือ) */
    private function _testItemQuery()
    {
        return DB::table('bsection5_test_item as t')
            ->leftJoin('bsection5_unit as u', 't.unit_id', '=', 'u.id')
            ->leftJoin('bsection5_test_item as p', 't.parent_id', '=', 'p.id')
            ->leftJoin('bsection5_test_method as m', 't.test_method_id', '=', 'm.id')
            ->leftJoin('bsection5_test_tools as tl', 't.test_tools_id', '=', 'tl.id')
            ->select([
                't.*',
                DB::raw('u.title as unit_text'),
                DB::raw('p.title as parent_text'),
                DB::raw('m.title as test_method_text'),
                DB::raw('tl.title as tool_text'),
            ]);
    }

    /** Load test items from save_example_map_lap_detail or bsection5_test_item */
    private function _loadTestItems($item)
    {
        if (($item->type_send ?? '') === 'all') {
            $tis = DB::table('tb3_tis')->where('tb3_Tisno', $item->tis_standard)->first();
            if ($tis) {
                return $this->_testItemQuery()
                    ->where('t.tis_id', $tis->tb3_TisAutono)
                    ->where('t.state', 1)
                    ->orderByRaw("CAST(REPLACE(t.no, '.', '') AS UNSIGNED)")
                    ->get();
            }
            return collect([]);
        }

        $ids = DB::table('save_example_map_lap_detail')
            ->where('maplap_id', $item->id)
            ->pluck('test_item_id')
            ->toArray();

        if (!empty($ids)) {
            return $this->_testItemQuery()
                ->whereIn('t.id', $ids)
                ->where('t.state', 1)
                ->orderByRaw("CAST(REPLACE(t.no, '.', '') AS UNSIGNED)")
                ->get();
        }

        return collect([]);
    }
}
