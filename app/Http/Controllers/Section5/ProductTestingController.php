<?php

namespace App\Http\Controllers\Section5;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use HP;

use App\Models\Elicense\Rform\Product;
use App\Models\Elicense\Rform\ProductLab;
use App\Models\Elicense\Rform\ProductDetail;
use App\Models\Elicense\RosUsers;

class ProductTestingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if (!$request->has('type')) {
            return view('section5.product-testing.dashboard');
        }
        return view('section5.product-testing.index');
    }

    public function data_list(Request $request)
    {
        $user = auth()->user();

        $query = DB::connection('mysql_elicense')->table('ros_rform_product_lab as a')
            ->leftJoin('ros_rform_product as p', 'a.product_id', '=', 'p.id')
            ->leftJoin('ros_rbasicdata_standard_tisi as st', 'p.tis_number', '=', 'st.tis_number')
            ->leftJoin('ros_users as lab_user', 'a.lab_id', '=', 'lab_user.id')
            ->select(
                'a.id',
                'a.product_id',
                'a.status',
                'a.date_sent',
                'a.checking_by',
                'a.checking_date',
                'a.checking_comment',
                'p.refno',
                'p.tis_number',
                'p.tax_number',
                'p.factory_name',
                'st.tis_name',
                'lab_user.name as lab_name'
            )
            ->whereIn('a.status', [1, 2, 3, 4, 5, 6, 7, 8])
            ->whereRaw("CONCAT(lab_user.tax_number, IFNULL(lab_user.branch_code, '')) = ?", [$user->username]);

        if ($request->input('type') === 'esurveillance') {
            $query->whereRaw('1 = 0');
        }

        if ($request->filled('filter_search')) {
            $search = $request->input('filter_search');
            $query->where(function ($q) use ($search) {
                $q->where('p.refno', 'LIKE', "%{$search}%")
                    ->orWhere('p.tis_number', 'LIKE', "%{$search}%")
                    ->orWhere('st.tis_name', 'LIKE', "%{$search}%")
                    ->orWhere('p.factory_name', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('filter_status')) {
            $query->where('a.status', (int)$request->input('filter_status'));
        }

        $query->orderByDesc('a.id');

        $status_list = [
            '1' => '<span class="label label-warning">รอการตอบรับ</span>',
            '2' => '<span class="label label-info">รับคำขอ</span>',
            '3' => '<span class="label label-danger">ไม่รับคำขอ</span>',
            '4' => '<span class="label label-default">ยกเลิก</span>',
            '5' => '<span class="label label-primary">อยู่ระหว่างการทดสอบ</span>',
            '6' => '<span class="label label-warning">ขอตัวอย่างเพิ่มเติม</span>',
            '7' => '<span class="label label-info">แจ้งผล</span>',
            '8' => '<span class="label label-success">สรุปผล</span>',
        ];

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('refno', function ($item) {
                return $item->refno ?? '-';
            })
            ->addColumn('tis', function ($item) {
                return ($item->tis_number ?? '-') . ' : ' . ($item->tis_name ?? '-');
            })
            ->addColumn('factory_name', function ($item) {
                return $item->factory_name ?? '-';
            })
            ->addColumn('lab_name', function ($item) {
                return $item->lab_name ?? '-';
            })
            ->addColumn('date_sent', function ($item) {
                return !empty($item->date_sent) ? HP::DateThai($item->date_sent) : '-';
            })
            ->addColumn('status', function ($item) use ($status_list) {
                return $status_list[(string)$item->status] ?? '-';
            })
            ->addColumn('action', function ($item) {
                if ((int)$item->status === 1) {
                    return '<a href="' . url('section5/product-testing/approve/' . $item->id) . '"
                                class="btn btn-primary btn-xs">
                                <i class="fa fa-pencil-square-o"></i> รับคำขอ
                            </a>';
                }
                if ((int)$item->status === 2) {
                    return '<a href="' . url('section5/product-testing/report/' . $item->id) . '"
                                class="btn btn-success btn-xs">
                                <i class="fa fa-file-text-o"></i> บันทึกผล/รายงานผล
                            </a>';
                }
                return '<a href="' . url('section5/product-testing/approve/' . $item->id) . '"
                            class="btn btn-info btn-xs">
                            <i class="fa fa-eye"></i> ดูรายละเอียด
                        </a>';
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    /**
     * GET /section5/product-testing/approve/{id}
     * Status=1: check.blade.php | Status>1: approve.blade.php (read-only + report link)
     */
    public function approve(Request $request, $id)
    {
        $item = ProductLab::on('mysql_elicense')
            ->with(['product', 'lab'])
            ->findOrFail($id);

        $product  = $item->product;
        $lab_user = RosUsers::on('mysql_elicense')->find($item->lab_id);

        if (!$lab_user || (($lab_user->tax_number ?? '') . ($lab_user->branch_code ?? '')) !== auth()->user()->username) {
            abort(403);
        }

        // Append tis_name from standard table
        if ($product && $product->tis_number) {
            $tis = DB::connection('mysql_elicense')
                ->table('ros_rbasicdata_standard_tisi')
                ->where('tis_number', $product->tis_number)
                ->first();
            $product->tis_name = $tis->tis_name ?? null;
        }

        $user_created = $product ? RosUsers::on('mysql_elicense')->find($product->created_by) : null;
        $action = $request->input('action', '');

        if ((int)$item->status === 1) {
            $product_details = ProductDetail::on('mysql_elicense')
                ->where('product_id', $item->product_id)
                ->get();

            $labdetail_items = DB::connection('mysql_elicense')
                ->table('ros_rform_product_labdetail_item as li')
                ->join('admin_dbtest.bsection5_test_item as t', 't.id', '=', 'li.test_item_id')
                ->where('li.product_lab_id', $item->id)
                ->where('li.state', 1)
                ->select('li.product_detail_id', 't.no', 't.title')
                ->orderByRaw("CAST(SUBSTRING_INDEX(t.no, '.', 1) AS UNSIGNED), t.no")
                ->get()
                ->groupBy('product_detail_id');

            return view('section5.product-testing.check', compact(
                'item', 'product', 'user_created', 'product_details', 'labdetail_items'
            ));
        }

        // Load test report if status >= 8
        $test_items      = collect();
        $existing_results = [];
        $report          = null;

        if ((int)$item->status >= 8) {
            $report = DB::connection('mysql_elicense')
                ->table('tisi_db.ros_rform_product_test_report')
                ->where('product_lab_id', $item->id)
                ->orderBy('report_no', 'desc')
                ->first();

            if ($report) {
                $rows = DB::connection('mysql_elicense')
                    ->table('tisi_db.ros_rform_product_test_report_row as r')
                    ->leftJoin('tisi_db.ros_rform_product_test_report_result as rr', 'rr.report_row_id', '=', 'r.id')
                    ->select([
                        'r.test_item_id', 'r.product_detail_id', 'r.item_title as title', 'r.item_no as no',
                        'r.unit_id', 'r.criteria', 'rr.seq', 'rr.measured_value', 'rr.item_result',
                    ])
                    ->where('r.report_no', $report->report_no)
                    ->orderByRaw('CAST(r.item_no AS UNSIGNED) ASC')
                    ->orderBy('r.item_no')
                    ->get();

                $grouped = [];
                foreach ($rows as $x) {
                    // key ด้วย product_detail_id + test_item_id ร่วมกัน กันรุ่นผลิตภัณฑ์ต่างกันมายุบทับกัน
                    $groupKey = ($x->product_detail_id ?? 0) . '-' . $x->test_item_id;
                    if (!isset($grouped[$groupKey])) {
                        $grouped[$groupKey] = (object)[
                            'id'                => $x->test_item_id,
                            'product_detail_id' => $x->product_detail_id,
                            'title'             => $x->title,
                            'no'                => $x->no,
                            'unit_id'           => $x->unit_id,
                            'criteria'          => $x->criteria,
                        ];
                    }
                    if ($x->seq) {
                        $existing_results[$x->product_detail_id][$x->test_item_id][(int)$x->seq] = [
                            'measured_value' => $x->measured_value,
                            'item_result'    => $x->item_result,
                        ];
                    }
                }
                $test_items = collect(array_values($grouped));
            }
        }

        return view('section5.product-testing.approve', compact(
            'item', 'product', 'user_created', 'action', 'report', 'test_items', 'existing_results'
        ));
    }

    /**
     * POST /section5/product-testing/approve-save
     * รับ/ไม่รับคำขอ
     */
    public function approve_save(Request $request)
    {
        $request->validate([
            'id'     => 'required',
            'status' => 'required',
        ]);

        $id     = $request->input('id');
        $status = (int)$request->input('status');
        $remark = $request->input('remark');
        $user   = auth()->user();

        $lab      = ProductLab::on('mysql_elicense')->findOrFail($id);
        $product  = Product::on('mysql_elicense')->find($lab->product_id);
        $lab_user = RosUsers::on('mysql_elicense')->find($lab->lab_id);

        if (!$lab_user || (($lab_user->tax_number ?? '') . ($lab_user->branch_code ?? '')) !== $user->username) {
            abort(403);
        }

        $detail = ProductDetail::on('mysql_elicense')
            ->where('product_id', $lab->product_id)
            ->first();

        DB::connection('mysql_elicense')->beginTransaction();

        try {
            $lab->status           = $status;
            $lab->checking_comment = $remark;
            $lab->checking_by      = $user->name; // varchar(70)
            $lab->checking_date    = date('Y-m-d');
            $lab->save();

            if ($product) {
                if ($status === 2) {
                    $product->status_id   = 5;
                    $product->modified_by = $user->getKey();
                    $product->save();

                    // Insert fallback test items if none exist
                    $existingCount = DB::connection('mysql_elicense')
                        ->table('ros_rform_product_labdetail_item')
                        ->where('product_lab_id', $lab->id)
                        ->where('state', 1)
                        ->count();

                    if ($existingCount === 0 && $detail) {
                        $tis      = DB::table('tb3_tis')->where('tb3_Tisno', $product->tis_number)->first();
                        $lab_code = $lab_user ? trim((string)$lab_user->lab_code) : '';

                        if ($tis && $lab_code !== '') {
                            $items = DB::table('admin_dbtest.bsection5_test_item as t')
                                ->join('admin_dbtest.section5_labs_scopes as s', 's.test_item_id', '=', 't.id')
                                ->where('t.tis_id', $tis->tb3_TisAutono)
                                ->where('s.lab_code', $lab_code)
                                ->where('s.state', 1)
                                ->pluck('t.id');

                            if ($items->count() > 0) {
                                $rows = $items->map(function ($item_id) use ($lab, $detail) {
                                    return [
                                        'product_lab_id'    => $lab->id,
                                        'product_detail_id' => $detail->id,
                                        'test_item_id'      => $item_id,
                                        'state'             => 1,
                                    ];
                                })->toArray();

                                DB::connection('mysql_elicense')
                                    ->table('ros_rform_product_labdetail_item')
                                    ->insert($rows);
                            }
                        }
                    }
                } elseif ($status === 3) {
                    $next = ProductLab::on('mysql_elicense')
                        ->where('product_id', $lab->product_id)
                        ->where('id', '>', $lab->id)
                        ->where('status', '!=', 4)
                        ->orderBy('id', 'asc')
                        ->first();

                    if ($next) {
                        $next->status = 1;
                        $next->save();
                    } else {
                        $product->status_id   = 3;
                        $product->modified_by = $user->getKey();
                        $product->save();
                    }
                }
            }

            DB::connection('mysql_elicense')->commit();

            return redirect('section5/product-testing?type=elicense')
                ->with('flash_message', 'บันทึกผลการพิจารณาเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::connection('mysql_elicense')->rollBack();
            return redirect('section5/product-testing?type=elicense')
                ->with('error_message', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * รายการทดสอบที่ต้องทำสำหรับ product_lab หนึ่งตัว แยกตามรุ่นผลิตภัณฑ์ (product_detail_id)
     * รายการทดสอบเดียวกันอาจต้องทำซ้ำหลายครั้งถ้ามีหลายรุ่นผลิตภัณฑ์ในคำขอเดียว (ไม่ distinct โดยตั้งใจ)
     * ใช้ร่วมกันทั้งตอนโหลดฟอร์ม (report) และตอนบันทึกผล (report_save) เพื่อให้ผลไม่ยุบรวมข้ามรุ่น
     */
    private function getTestItemsForLab($productLabId, $labCode)
    {
        if ($labCode === '') {
            return collect();
        }

        $toolSub = DB::connection('mysql_elicense')
            ->table('admin_dbtest.bsection5_test_item_tools as it')
            ->join('admin_dbtest.bsection5_test_tools as tt', 'tt.id', '=', 'it.test_tools_id')
            ->select([
                'it.bsection5_test_item_id',
                DB::raw("GROUP_CONCAT(DISTINCT tt.title ORDER BY tt.title SEPARATOR ', ') as tool_text"),
            ])
            ->groupBy('it.bsection5_test_item_id');

        $items = DB::connection('mysql_elicense')
            ->table('ros_rform_product_labdetail_item as li')
            ->join('admin_dbtest.bsection5_test_item as t', 't.id', '=', 'li.test_item_id')
            ->leftJoin('admin_dbtest.bsection5_unit as u', 'u.id', '=', 't.unit_id')
            ->leftJoin('admin_dbtest.bsection5_test_method as tm', 'tm.id', '=', 't.test_method_id')
            ->leftJoin('admin_dbtest.bsection5_test_item as p', 'p.id', '=', 't.parent_id')
            ->leftJoinSub($toolSub, 'toolmap', function ($j) {
                $j->on('toolmap.bsection5_test_item_id', '=', 't.id');
            })
            // เกณฑ์แบบมีโครงสร้างจากฝั่งจัดกลุ่ม bcertify (devops_center) — ใช้ก็ต่อเมื่อรุ่นสินค้านี้
            // ถูกจับคู่กลุ่มไว้แล้วตอนยื่นคำขอ (ros_rform_product_detail.bcertify_group_id) และรายการ
            // ทดสอบตัวนี้มีเกณฑ์ตั้งไว้ในกลุ่มนั้นจริง — ไม่มีก็ไม่กระทบอะไร (LEFT JOIN ทั้งสาย, คอลัมน์
            // จะเป็น NULL แล้ว fallback ไปใช้ t.criteria เดิมที่ view) ดูหัวข้อ 8 ของ
            // docs/tiw/std-test-item-elicense-groups-exploration.md (devops_center)
            ->leftJoin('ros_rform_product_detail as pd', 'pd.id', '=', 'li.product_detail_id')
            ->leftJoin('admin_dbtest.bcertify_std_test_group_items as gi', function ($j) {
                $j->on('gi.group_id', '=', 'pd.bcertify_group_id')
                  ->on('gi.test_item_id', '=', 't.id')
                  ->where('gi.state', 1);
            })
            ->leftJoin('admin_dbtest.bcertify_std_test_item_criteria as cr', function ($j) {
                $j->on('cr.group_item_id', '=', 'gi.id')
                  ->where('cr.state', 1);
            })
            ->leftJoin('admin_dbtest.bcertify_std_test_tolerance_rules as tr', function ($j) {
                $j->on('tr.criterion_id', '=', 'cr.id')
                  ->where('tr.state', 1);
            })
            ->where('li.product_lab_id', $productLabId)
            ->where('li.state', 1)
            ->select([
                'li.id as labdetail_item_id',
                'li.product_lab_id',
                'li.product_detail_id',
                'li.test_item_id as id',
                't.no',
                't.title',
                't.unit_id',
                DB::raw('u.title as unit_text'),
                't.criteria',
                't.parent_id',
                DB::raw('p.title as parent_text'),
                't.test_method_id',
                DB::raw('tm.title as test_method_text'),
                't.test_tools_id',
                DB::raw('toolmap.tool_text as tool_text'),
                't.amount_test_list',
                't.format_result',
                't.format_result_detail',
                DB::raw('cr.comparison_type as bcertify_comparison_type'),
                DB::raw('cr.operator_start as bcertify_operator_start'),
                DB::raw('cr.value_start as bcertify_value_start'),
                DB::raw('cr.operator_end as bcertify_operator_end'),
                DB::raw('cr.value_end as bcertify_value_end'),
                DB::raw('cr.unit_text as bcertify_unit_text'),
                DB::raw('tr.minus_value as bcertify_minus_value'),
                DB::raw('tr.plus_value as bcertify_plus_value'),
                DB::raw('tr.tolerance_unit as bcertify_tolerance_unit'),
            ])
            ->orderByRaw("
                CAST(SUBSTRING_INDEX(t.no, '.', 1) AS UNSIGNED),
                CAST(CASE WHEN t.no LIKE '%.%' THEN SUBSTRING_INDEX(t.no, '.', -1) ELSE '0' END AS UNSIGNED),
                t.no
            ")
            ->get();

        foreach ($items as $item) {
            $item->bcertify_criteria_text = $this->formatBcertifyCriterionText($item);
        }

        return $items;
    }

    /**
     * แปลงเกณฑ์แบบมีโครงสร้างจาก bcertify (ถ้ามี) ให้เป็นข้อความสรุปสำหรับแสดงในคอลัมน์ "เกณฑ์" —
     * ยังไม่ใช้ตัดสิน pass/fail อัตโนมัติ (ทำในขั้นถัดไป) ขั้นนี้แค่แสดงให้ถูกก่อน
     */
    private function formatBcertifyCriterionText($item)
    {
        $type = $item->bcertify_comparison_type ?? null;
        if (empty($type)) {
            return null;
        }

        $unit = trim((string) ($item->bcertify_unit_text ?? ''));
        $parts = [];

        if ($type === 'direct_range' || $type === 'reference_tolerance') {
            $parts[] = trim(($item->bcertify_operator_start ?? '>=') . ' ' . $item->bcertify_value_start);
            $parts[] = trim(($item->bcertify_operator_end ?? '<=') . ' ' . $item->bcertify_value_end);
        } elseif ($type === 'upper_limit') {
            $parts[] = trim(($item->bcertify_operator_end ?? '<=') . ' ' . $item->bcertify_value_end);
        } elseif ($type === 'lower_limit') {
            $parts[] = trim(($item->bcertify_operator_start ?? '>=') . ' ' . $item->bcertify_value_start);
        } elseif ($type === 'exact') {
            $parts[] = trim('= ' . $item->bcertify_value_end);
        }

        $text = implode(' และ ', array_filter($parts, function ($p) { return $p !== ''; }));
        if ($unit !== '') {
            $text .= ' ' . $unit;
        }

        $minus = $item->bcertify_minus_value ?? null;
        $plus  = $item->bcertify_plus_value ?? null;
        if ($minus !== null || $plus !== null) {
            $toleranceUnit = ($item->bcertify_tolerance_unit ?? 'absolute') === 'percent' ? '%' : $unit;
            $text .= ' (ค่าคลาดเคลื่อน -' . ($minus ?? '0') . ' ถึง +' . ($plus ?? '0') . ($toleranceUnit !== '' ? ' ' . $toleranceUnit : '') . ')';
        }

        return trim($text) !== '' ? trim($text) : null;
    }

    /**
     * GET /section5/product-testing/report/{id}
     * หน้ากรอกผลการทดสอบ (test_report_table)
     */
    public function report(Request $request, $id)
    {
        $item = ProductLab::on('mysql_elicense')
            ->with(['product', 'lab'])
            ->findOrFail($id);

        $product  = $item->product;
        $lab_user = RosUsers::on('mysql_elicense')->find($item->lab_id);

        if (!$lab_user || (($lab_user->tax_number ?? '') . ($lab_user->branch_code ?? '')) !== auth()->user()->username) {
            abort(403);
        }

        // Append tis_name
        if ($product && $product->tis_number) {
            $tis = DB::connection('mysql_elicense')
                ->table('ros_rbasicdata_standard_tisi')
                ->where('tis_number', $product->tis_number)
                ->first();
            $product->tis_name = $tis->tis_name ?? null;
        }

        $user_created = $product ? RosUsers::on('mysql_elicense')->find($product->created_by) : null;
        $lab_code = $lab_user ? trim((string)$lab_user->lab_code) : '';

        $test_items = $this->getTestItemsForLab($item->id, $lab_code);

        $report_no = (int)$request->query('report_no', 0);
        $report    = null;

        if ($report_no > 0) {
            $report = DB::connection('mysql_elicense')
                ->table('tisi_db.ros_rform_product_test_report')
                ->where('report_no', $report_no)
                ->where('product_lab_id', $item->id)
                ->first();
        }

        $existing_results = [];
        if ($report_no > 0 && $report) {
            $rows = DB::connection('mysql_elicense')
                ->table('tisi_db.ros_rform_product_test_report_row as r')
                ->leftJoin('tisi_db.ros_rform_product_test_report_result as rr', 'rr.report_row_id', '=', 'r.id')
                ->select(['r.test_item_id', 'r.product_detail_id', 'rr.seq', 'rr.measured_value', 'rr.item_result'])
                ->where('r.report_no', $report_no)
                ->get();

            foreach ($rows as $x) {
                if (!$x->seq) continue;
                $existing_results[$x->product_detail_id][$x->test_item_id][(int)$x->seq] = [
                    'measured_value' => $x->measured_value,
                    'item_result'    => $x->item_result,
                ];
            }
        }

        $is_retest = (int)($item->is_retest ?? 0) === 1;

        $other_reports = DB::connection('mysql_elicense')
            ->table('ros_rform_product_lab as pl')
            ->leftJoin('ros_users as u', 'pl.lab_id', '=', 'u.id')
            ->join('tisi_db.ros_rform_product_test_report as tr', 'tr.product_lab_id', '=', 'pl.id')
            ->where('pl.product_id', $item->product_id)
            ->where('pl.id', '!=', $item->id)
            ->where('pl.status', '>=', 8)
            ->select(['pl.id as product_lab_id', 'u.name as lab_name', 'tr.report_no', 'tr.overall_result', 'tr.created_at'])
            ->get();

        return view('section5.product-testing.report', compact(
            'item', 'product', 'user_created',
            'test_items', 'existing_results',
            'is_retest', 'report_no', 'report',
            'lab_user', 'lab_code',
            'other_reports'
        ));
    }

    /**
     * POST /section5/product-testing/report-save
     * บันทึกผลการทดสอบ + insert tisi_db report tables
     */
    public function report_save(Request $request)
    {
        $request->validate([
            'id'             => 'required',
            'overall_result' => 'required|in:pass,fail',
            'measured'       => 'array',
            'measured_mix'   => 'array',
            'item_result'    => 'array',
            'full_report_pdf' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $user   = auth()->user();
        $id     = (int)$request->input('id');
        $detail   = ProductLab::on('mysql_elicense')->with(['product', 'lab'])->findOrFail($id);
        $product  = $detail->product;
        $lab_user = RosUsers::on('mysql_elicense')->find($detail->lab_id);

        if (!$lab_user || (($lab_user->tax_number ?? '') . ($lab_user->branch_code ?? '')) !== $user->username) {
            abort(403);
        }
        $lab_code = $lab_user ? trim((string)$lab_user->lab_code) : '';

        $test_items = $this->getTestItemsForLab($detail->id, $lab_code);

        $measured     = $request->input('measured', []);
        $measured_mix = $request->input('measured_mix', []);
        $item_result  = $request->input('item_result', []);

        DB::connection('mysql_elicense')->beginTransaction();

        try {
            $standard = null;
            if ($product && !empty($product->tis_number)) {
                $standard = DB::connection('mysql_elicense')
                    ->table('ros_rbasicdata_standard_tisi')
                    ->where('tis_number', $product->tis_number)
                    ->first();
            }

            $report_no = DB::connection('mysql_elicense')
                ->table('tisi_db.ros_rform_product_test_report')
                ->insertGetId([
                    'product_lab_id' => $detail->id,
                    'product_id'     => $detail->product_id,
                    'tis_number'     => $product->tis_number ?? null,
                    'tis_name'       => $standard->tis_name ?? null,
                    'coordinator'    => $product->coordinator ?? null,
                    'ref_no'         => $product->ref_no ?? null,
                    'overall_result' => $request->input('overall_result'),
                    'created_by'     => $user->getKey(),
                    'updated_by'     => $user->getKey(),
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

            foreach ($test_items as $ti) {
                $rowId = DB::connection('mysql_elicense')
                    ->table('tisi_db.ros_rform_product_test_report_row')
                    ->insertGetId([
                        'report_no'         => $report_no,
                        'product_lab_id'    => $detail->id,
                        'product_detail_id' => $ti->product_detail_id ?? null,
                        'test_item_id'      => $ti->id,
                        'item_title'        => $ti->title ?? '',
                        'item_no'           => $ti->no ?? null,
                        'unit_id'           => $ti->unit_id ?? null,
                        'criteria'          => $ti->criteria ?? null,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);

                $detailId = $ti->product_detail_id ?? 0;

                $maxSeq = (int)($ti->amount_test_list ?? 0);
                if ($maxSeq <= 0) $maxSeq = 8; // fallback ให้ตรงกับหน้าฟอร์ม (partials/test_report_table.blade.php)

                for ($seq = 1; $seq <= $maxSeq; $seq++) {
                    $mv = null;
                    if (isset($measured[$detailId][$ti->id][$seq])) {
                        $mv = $measured[$detailId][$ti->id][$seq];
                    }
                    if (isset($measured_mix[$detailId][$ti->id][$seq])) {
                        $mv = json_encode($measured_mix[$detailId][$ti->id][$seq], JSON_UNESCAPED_UNICODE);
                    }
                    if (is_array($mv)) {
                        $mv = implode(',', $mv);
                    }

                    $rs = $item_result[$detailId][$ti->id][$seq] ?? null;
                    if ($mv === null && ($rs === null || $rs === '')) continue;

                    DB::connection('mysql_elicense')
                        ->table('tisi_db.ros_rform_product_test_report_result')
                        ->insert([
                            'report_row_id'  => $rowId,
                            'seq'            => $seq,
                            'measured_value' => ($mv === '' ? null : $mv),
                            'item_result'    => ($rs === '' ? null : $rs),
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ]);
                }

                // Row summary at seq=0 — รายการที่เจ้าหน้าที่ยังไม่ได้ตั้ง "รูปแบบข้อมูลผลทดสอบ" (7.2,
                // format_result) ไม่มี select ให้ห้องแล็บเลือกในหน้าเว็บอยู่แล้ว (ดู test_report_table.blade.php
                // @elseif($isUnconfigured) — ยึด format_result เป็นตัวตัดสินเดียว ยืนยันกับผู้ใช้แล้ว
                // 2026-09-09) จึงต้อง "ปล่อยว่าง" (NULL) แทนการบังคับเป็น fail — ไม่งั้นรายการที่ไม่เคยถูก
                // ประเมินจริงจะไหลไปเป็น "ไม่ผ่าน" ปลอมที่หน้ายื่นใบอนุญาต (moao1/moao3/moao5 form) ซึ่งอ่าน
                // คอลัมน์นี้ผ่าน tisi_db ตรงๆ และรองรับค่าว่างเป็น "-" (ยังไม่มีผล ไม่นับทั้งผ่าน/ไม่ผ่าน)
                // อยู่แล้ว — ดูหัวข้อ 11/12 ของ docs/tiw/std-test-item-elicense-groups-exploration.md
                // (devops_center)
                $isUnconfigured = empty($ti->format_result);

                if ($isUnconfigured) {
                    $summaryResult = null;
                } else {
                    $rowPass = true;
                    for ($s = 1; $s <= $maxSeq; $s++) {
                        if (($item_result[$detailId][$ti->id][$s] ?? 'fail') !== 'pass') {
                            $rowPass = false;
                            break;
                        }
                    }
                    $summaryResult = $rowPass ? 'pass' : 'fail';
                }

                DB::connection('mysql_elicense')
                    ->table('tisi_db.ros_rform_product_test_report_result')
                    ->insert([
                        'report_row_id'  => $rowId,
                        'seq'            => 0,
                        'measured_value' => null,
                        'item_result'    => $summaryResult,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
            }

            $detail->status           = 8;
            $detail->checking_by      = $user->name; // varchar(70)
            $detail->checking_date    = date('Y-m-d');
            $detail->checking_comment = ($request->input('overall_result') === 'pass') ? 'ผ่าน' : 'ไม่ผ่าน';
            $detail->save();

            if ($product) {
                $product->status_id   = 9;
                $product->modified_by = $user->getKey();
                $product->save();
            }

            if ($request->hasFile('full_report_pdf')) {
                $file = $request->file('full_report_pdf');
                $path = $file->store('product_testing_full_report', 'ftp');
                DB::connection('mysql_elicense')
                    ->table('tisi_db.ros_rform_product_test_report')
                    ->where('report_no', $report_no)
                    ->update(['report_file_path' => $path]);
            }

            DB::connection('mysql_elicense')->commit();

            return redirect('section5/product-testing')
                ->with('flash_message', 'ส่งรายงานผลเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::connection('mysql_elicense')->rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error_message', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function detail(Request $request, $id)
    {
        return $this->approve($request, $id);
    }
}
