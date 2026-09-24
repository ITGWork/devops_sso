<?php

namespace App\Http\Controllers\Section5;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Elicense\Rform\Factory;
use App\Models\Elicense\Rform\FactoryDetail;
use App\Models\Elicense\Rform\FactoryInspection;
use App\Models\Elicense\Rform\FactoryQcItem;
use App\Models\Elicense\Rform\FactoryQcResult;
use App\Models\Elicense\Basic\InspecStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use HP;

class FactoryInspectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if (!$request->has('type')) {
            return view('section5.factory-inspection.dashboard');
        }
        return view('section5.factory-inspection.index');
    }

    public function data_list(Request $request)
    {
        $user     = auth()->user();
        $ros_user = $this->_getRosUser();

        // 1. Elicense Certification Data Query
        $elicenseQuery = DB::connection('mysql_elicense')->table('ros_rform_factory_detail as a')
                    ->leftJoin('ros_rform_factory as f', 'a.factory_id', '=', 'f.id')
                    ->leftJoin('ros_users as created_by', 'f.created_by', '=', 'created_by.id')
                    ->leftJoin('ros_users as auditor', 'a.auditor_id', '=', 'auditor.id')
                    ->leftJoin(DB::raw('(SELECT factory_detail_id, MAX(id) as max_id FROM ros_rform_factory_inspection GROUP BY factory_detail_id) as latest_insp'), 'a.id', '=', 'latest_insp.factory_detail_id')
                    ->leftJoin('ros_rform_factory_inspection as insp', 'latest_insp.max_id', '=', 'insp.id')
                    ->select(
                        'a.id as id',
                        DB::raw("CONVERT(f.refno USING utf8) COLLATE utf8_unicode_ci as refno"),
                        DB::raw("CONVERT(f.tis_number USING utf8) COLLATE utf8_unicode_ci as tis_number"),
                        DB::raw("CONVERT(f.tis_name USING utf8) COLLATE utf8_unicode_ci as tis_name"),
                        DB::raw("CONVERT(created_by.name USING utf8) COLLATE utf8_unicode_ci as applicant_name"),
                        DB::raw("CONVERT(created_by.tax_number USING utf8) COLLATE utf8_unicode_ci as tax_number"),
                        'a.date_sent as date_sent',
                        'a.status as status',
                        'a.checking_date as checking_date',
                        DB::raw("CONVERT(auditor.name USING utf8) COLLATE utf8_unicode_ci as inspector_name"),
                        'insp.result as inspection_result',
                        DB::raw("CONVERT('elicense' USING utf8) COLLATE utf8_unicode_ci as source")
                    )
                    ->whereIn('a.status', [1, 2, 3])
                    ->whereRaw("CONCAT(auditor.tax_number, IFNULL(auditor.branch_code, '')) = ?", [$user->username]);

        // 2. Planning Surveillance Data Query
        $planningQuery = DB::connection('mysql_elicense')->table('admin_dbtest.control_follow_list_table as a')
                    ->select(
                        'a.id as id',
                        DB::raw("CONVERT(TRIM(a.license_no) USING utf8) COLLATE utf8_unicode_ci as refno"),
                        DB::raw("CONVERT(TRIM(a.license_no) USING utf8) COLLATE utf8_unicode_ci as tis_number"),
                        DB::raw("CONVERT('[Surveillance Plan]' USING utf8) COLLATE utf8_unicode_ci as tis_name"),
                        DB::raw("CONVERT(a.operator_name USING utf8) COLLATE utf8_unicode_ci as applicant_name"),
                        DB::raw("CONVERT(a.tax_id USING utf8) COLLATE utf8_unicode_ci as tax_number"),
                        'a.updated_at as date_sent',
                        DB::raw("CASE
                            WHEN a.officer_status = 'รอ IB ยอมรับ' THEN 1
                            WHEN a.officer_status = 'IB ตอบรับการตรวจ' THEN 2
                            ELSE 1
                        END as status"),
                        DB::raw("CONVERT(a.assign_officer USING utf8) COLLATE utf8_unicode_ci as checking_by"),
                        'a.approval_date as checking_date',
                        DB::raw("CAST(NULL AS CHAR) COLLATE utf8_unicode_ci as inspector_name"),
                        DB::raw("CAST(NULL AS SIGNED) as inspection_result"),
                        DB::raw("CONVERT('planning' USING utf8) COLLATE utf8_unicode_ci as source")
                    )
                    ->where('a.plan_ib', 1)
                    ->whereNotNull('a.plan_ib_select')
                    ->where('a.plan_ib_select', $ros_user && $ros_user->ibcb_code ? $ros_user->ibcb_code : '__NO_MATCH__');

        // Filter by type or combine via Union
        $type = $request->input('type');
        if ($type === 'elicense') {
            $query = $elicenseQuery;
        } elseif ($type === 'esurveillance') {
            $query = $planningQuery;
        } else {
            $query = $elicenseQuery->unionAll($planningQuery);
        }

        $finalQuery = DB::connection('mysql_elicense')->table(DB::raw("({$query->toSql()}) as combined"))
                        ->mergeBindings($query);

        if ($request->filled('filter_search')) {
            $search = trim($request->input('filter_search'));
            $finalQuery->where(function ($q) use ($search) {
                $q->where(DB::raw("TRIM(refno)"), 'LIKE', "%{$search}%")
                  ->orWhere(DB::raw("TRIM(tis_number)"), 'LIKE', "%{$search}%")
                  ->orWhere(DB::raw("TRIM(applicant_name) COLLATE utf8_unicode_ci"), 'LIKE', "%{$search}%")
                  ->orWhere(DB::raw("TRIM(tax_number)"), 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('filter_status')) {
            $status = $request->input('filter_status');
            $status_map_reverse = [
                5  => 1,
                7  => 2,
                6  => 3,
                100 => 4,
            ];
            $finalQuery->where('status', $status_map_reverse[(int)$status] ?? $status);
        }

        try {
            return DataTables::of($finalQuery)
                ->addIndexColumn()
                ->addColumn('refno', function ($item) {
                    return $item->refno ?? '-';
                })
                ->addColumn('applicant_name', function ($item) {
                    return $item->applicant_name ?? '-';
                })
                ->addColumn('tax_number', function ($item) {
                    return $item->tax_number ?? '-';
                })
                ->addColumn('tis', function ($item) {
                    return ($item->tis_number ?? '-') . ' : ' . ($item->tis_name ?? '-');
                })
                ->addColumn('status', function ($item) {
                    $status_id_map = [
                        1 => 5,
                        2 => 7,
                        3 => 6,
                        4 => 100,
                    ];
                    $new_status_id = $status_id_map[(int)$item->status] ?? null;
                    return $new_status_id ? InspecStatus::getStatusText($new_status_id) : '-';
                })
                ->addColumn('checking_by', function ($item) {
                    $text = $item->inspector_name ?? '-';
                    if (!empty($item->checking_date)) {
                        $text .= ' (' . HP::DateThai($item->checking_date) . ')';
                    }
                    return $text;
                })
                ->addColumn('date_sent', function ($item) {
                    return HP::DateThai($item->date_sent) ?? '-';
                })
                ->addColumn('inspection_result', function ($item) {
                    if (empty($item->inspection_result)) {
                        return '<span class="label label-default">ยังไม่ตรวจ</span>';
                    }
                    $result_id_map   = [1 => 10, 2 => 8, 3 => 11];
                    $label_class_map = [1 => 'label-success', 2 => 'label-warning', 3 => 'label-danger'];
                    $result_val  = (int) $item->inspection_result;
                    $status_id   = $result_id_map[$result_val] ?? null;
                    $label_class = $label_class_map[$result_val] ?? 'label-default';
                    $status_text = $status_id ? InspecStatus::getStatusText($status_id) : '-';
                    return '<span class="label ' . $label_class . '">' . $status_text . '</span>';
                })
                ->addColumn('action', function ($item) {
                    if ($item->source === 'planning') {
                        // รอ IB ยอมรับ → หน้า check (ยืนยัน/ปฏิเสธรับงาน)
                        if ($item->status == 1) {
                            return '<a href="' . url('section5/factory-inspection/esurveillance/check/' . $item->id) . '" class="btn btn-warning btn-xs" title="ยืนยัน/ปฏิเสธรับงาน">
                                        <i class="fa fa-check-circle"></i> รับงาน
                                    </a>';
                        }
                        // IB ตอบรับแล้ว → หน้า approve (บันทึกผลการตรวจ)
                        return '<a href="' . url('section5/factory-inspection/esurveillance/approve/' . $item->id) . '" class="btn btn-primary btn-xs" title="บันทึกผลการตรวจ">
                                    <i class="fa fa-pencil-square-o"></i> บันทึกผล
                                </a>';
                    }

                    if ($item->inspection_result == 1) {
                        return '<a href="' . url('section5/factory-inspection/approve/' . $item->id) . '" class="btn btn-info btn-xs" title="ดูรายละเอียด">
                                    <i class="fa fa-eye"></i>
                                </a>';
                    }
                    return '<a href="' . url('section5/factory-inspection/approve/' . $item->id) . '" class="btn btn-primary btn-xs" title="บันทึกผลการตรวจ">
                                <i class="fa fa-pencil-square-o"></i>
                            </a>';
                })
                ->rawColumns(['action', 'inspection_result'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('SSO FactoryInspectionController@data_list error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * ดึง ros_users record จาก SSO username (link ระหว่าง 2 ระบบ)
     * username ฝั่ง SSO = tax_number (+ branch_code ต่อท้าย ถ้าเป็นบัญชีสาขา) — ต้อง CONCAT เทียบ
     * หมายเหตุ: 1 branch อาจมีหลายแถว ros_users ซ้ำ tax_number+branch_code กัน (คนละ auditor code)
     * ฟังก์ชันนี้จึงคืนได้แค่ "แถวใดแถวหนึ่ง" ที่ match — ห้ามใช้ตรวจสิทธิ์ความเป็นเจ้าของแถวใดแถวหนึ่งโดยเฉพาะ
     * (ดู _checkFactoryOwnership ที่ตรวจแบบ per-row แทน)
     */
    private function _getRosUser()
    {
        return DB::connection('mysql_elicense')
            ->table('ros_users')
            ->whereRaw("CONCAT(tax_number, IFNULL(branch_code, '')) = ?", [auth()->user()->username])
            ->first();
    }

    /**
     * ตรวจสิทธิ์ว่า factory_detail นี้เป็นของ auditor ที่ login
     * เช็คจากแถว ros_users ที่ถูก assign จริง (auditor_id ของ $item) แทนการเดาแถวจาก _getRosUser()
     * เพราะ 1 บริษัท/สาขาอาจมีหลายแถว ros_users (auditor code คนละอัน) ที่ tax_number+branch_code ตรงกันหมด
     */
    private function _checkFactoryOwnership($item)
    {
        $auditor = DB::connection('mysql_elicense')
            ->table('ros_users')
            ->where('id', $item->auditor_id)
            ->first();

        $username = auth()->user()->username;
        $matches  = $auditor && (($auditor->tax_number ?? '') . ($auditor->branch_code ?? '')) === $username;

        if (!$matches) {
            abort(403);
        }
        return $auditor;
    }

    public function approve(Request $request, $id)
    {
        $item     = FactoryDetail::with(['factory.qcResults', 'inspections'])->findOrFail($id);
        $ros_user = $this->_checkFactoryOwnership($item);

        $user_created = \App\Models\Elicense\RosUsers::find($item->factory->created_by);

        $action = $request->input('action', '');

        // Status 1 = รอตรวจสอบ → แสดงหน้า check
        if ($item->status == 1) {
            return view('section5.factory-inspection.check', compact('item', 'user_created'));
        }

        $qc_items   = FactoryQcItem::active()->ordered()->get();
        $qc_results = $item->factory->qcResults->keyBy('item_id');
        $inspections = $item->inspections()->orderBy('id', 'desc')->get();

        return view('section5.factory-inspection.approve', compact(
            'item',
            'user_created',
            'action',
            'qc_items',
            'qc_results',
            'inspections'
        ));
    }

    public function approve_save(Request $request)
    {
        $request->validate([
            'id'     => 'required',
            'status' => 'required',
        ]);

        $id     = $request->input('id');
        $status = $request->input('status');
        $remark = $request->input('remark');
        $user   = auth()->user();

        $detail   = FactoryDetail::findOrFail($id);
        $ros_user = $this->_checkFactoryOwnership($detail);
        $factory  = $detail->factory;

        DB::beginTransaction();
        try {
            $detail->status           = $status;
            $detail->checking_comment = $remark;
            $detail->checking_by      = $ros_user->id;   // elicense ros_users.id
            $detail->checking_date    = date('Y-m-d');
            $detail->save();

            if ($status == 2) {
                $factory->status_id   = 7;
                $factory->modified_by = $ros_user->id;
                $factory->save();
            } elseif ($status == 3) {
                $next_auditor = FactoryDetail::where('factory_id', $factory->id)
                                             ->where('id', '>', $detail->id)
                                             ->where('status', '!=', 4)
                                             ->orderBy('id', 'asc')
                                             ->first();

                if ($next_auditor) {
                    $next_auditor->status = 1;
                    $next_auditor->save();
                } else {
                    $factory->status_id   = 6;
                    $factory->modified_by = $ros_user->id;
                    $factory->save();
                }
            }

            DB::commit();
            return redirect('section5/factory-inspection')->with('flash_message', 'บันทึกผลการพิจารณาเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('section5/factory-inspection')->with('error_message', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function result_save(Request $request)
    {
        $request->validate([
            'id'     => 'required',
            'action' => 'required|in:checking,report',
        ]);

        $id     = $request->input('id');
        $action = $request->input('action');
        $user   = auth()->user();

        if ($action == 'checking') {
            $result = $request->input('result');
            if (empty($result)) {
                return redirect()->back()
                    ->with('error_message', 'กรุณาเลือกสรุปผลการประเมินให้ครบทุกข้อก่อนบันทึก')
                    ->withInput();
            }
        }

        $detail   = FactoryDetail::findOrFail($id);
        $ros_user = $this->_checkFactoryOwnership($detail);
        $factory  = $detail->factory;

        DB::beginTransaction();
        try {
            if ($action == 'checking') {
                $qc_results_input = $request->input('qc_result', []);
                $qc_summaries     = $request->input('qc_summary', []);

                foreach ($qc_results_input as $item_id => $result_val) {
                    $summary_status = 'pass';
                    if ($result_val == 2) $summary_status = 'fail';
                    elseif ($result_val == 3) $summary_status = 'request';

                    FactoryQcResult::updateOrCreate(
                        ['factory_id' => $factory->id, 'item_id' => $item_id],
                        [
                            'summary_status' => $summary_status,
                            'summary_detail' => $qc_summaries[$item_id] ?? '',
                            'created_by'     => $ros_user->id,
                            'created'        => date('Y-m-d H:i:s'),
                        ]
                    );
                }

                $inspection                     = new FactoryInspection();
                $inspection->factory_detail_id  = $id;
                $inspection->start_inspect_date = $request->input('start_inspect_date');
                $inspection->end_inspect_date   = $request->input('end_inspect_date');
                $inspection->result             = $request->input('result');
                $inspection->defect             = $request->input('defect');
                $inspection->remark             = $request->input('remark');
                $inspection->created_by         = $ros_user->id;

                if ($request->hasFile('att_file')) {
                    $filenames = [];
                    foreach ($request->file('att_file') as $file) {
                        $filename = time() . '_' . $file->getClientOriginalName();
                        Storage::disk('ftp')->putFileAs('factory_inspection', $file, $filename);
                        $filenames[] = $filename;
                    }
                    $inspection->att_file = json_encode($filenames);
                    $inspection->att_name = implode(', ', $filenames);
                }
                $inspection->save();

                $result = $request->input('result');
                if ($result == 1 || $result == 3) {
                    $detail->inspect_status = 3;
                    $factory->status_id     = 9;
                } elseif ($result == 2) {
                    $detail->inspect_status = 2;
                    $factory->status_id     = 8;
                }

                $detail->save();
                $factory->modified_by = $ros_user->id;
                $factory->save();

            } elseif ($action == 'report') {
                $detail->subject         = $request->input('subject');
                $detail->ref_no          = $request->input('ref_no');
                $detail->applicant_data  = $request->input('applicant_data');
                $detail->factory_data    = $request->input('factory_data');
                $detail->product_scope   = $request->input('product_scope');
                $detail->result_data     = $request->input('result_data');
                $detail->inspect_result  = $request->input('inspect_result');
                $detail->inspect_comment = $request->input('inspect_comment');
                $detail->inspect_by      = $ros_user->id;
                $detail->inspect_date    = date('Y-m-d');

                if ($request->hasFile('inspect_report_file')) {
                    $files = [];
                    foreach ($request->file('inspect_report_file') as $file) {
                        $realfile = time() . '_' . $file->getClientOriginalName();
                        Storage::disk('ftp')->putFileAs('factory_report', $file, $realfile);
                        $files[] = [
                            'realfile' => $realfile,
                            'filename' => $file->getClientOriginalName(),
                        ];
                    }
                    $detail->inspect_report_file = json_encode($files);
                }

                $detail->inspect_status = 4;
                $detail->approve_status = null;
                $detail->save();
            }

            DB::commit();
            return redirect('section5/factory-inspection/approve/' . $id)->with('flash_message', 'บันทึกข้อมูลเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error_message', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}
