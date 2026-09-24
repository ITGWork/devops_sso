<?php

namespace App\Http\Controllers\Section5;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Elicense\Rform\FactoryQcItem;
use App\Models\Csurv\ControlFollowIbInspection;
use App\Models\Csurv\ControlFollowIbQcResult;
use HP;

/**
 * ระบบบันทึกผลการตรวจโรงงาน สำหรับ IB/CB (E-Surveillance Plan)
 * ใช้ตาราง control_follow_list_table (admin_dbtest) เป็นต้นทาง
 * บันทึกรายงานลง control_follow_ib_inspection และ control_follow_ib_qc_results
 */
class CsurvInspectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    /**
     * ดึง ros_users record จาก SSO username (bridge ระหว่าง 2 ระบบ) — tax_number ใช้ไม่ได้เพราะบริษัทเดียวมีหลาย ros_users ซ้ำ tax_number กัน
     */
    private function _getRosUser()
    {
        return DB::connection('mysql_elicense')
            ->table('ros_users')
            ->where('username', auth()->user()->username)
            ->first();
    }

    /**
     * โหลด control_follow_list_table item หรือ abort 404
     */
    private function _getItem($id)
    {
        $item = DB::table('control_follow_list_table')->where('id', $id)->first();
        if (!$item) {
            abort(404);
        }
        return $item;
    }

    /**
     * โหลด control_follow plan
     */
    private function _getPlan($id_follow)
    {
        return DB::table('control_follow')->where('id', $id_follow)->first();
    }

    /**
     * ตรวจสิทธิ์ IB — ibcb_code ต้องตรงกับ plan_ib_select ของรายการนั้น
     */
    private function _checkIbOwnership($item)
    {
        $ros_user = $this->_getRosUser();
        if (!$ros_user || empty($ros_user->ibcb_code) || $ros_user->ibcb_code !== $item->plan_ib_select) {
            abort(403);
        }
        return $ros_user;
    }

    // -------------------------------------------------------
    // Step 1: Check (ยืนยัน/ปฏิเสธรับงาน)
    // -------------------------------------------------------

    public function inspection_check($id)
    {
        $item     = $this->_getItem($id);
        $ros_user = $this->_checkIbOwnership($item);
        $plan     = $this->_getPlan($item->id_follow);

        return view('section5.factory-inspection.esurveillance.check', compact('item', 'plan'));
    }

    // -------------------------------------------------------
    // Step 2: Approve (บันทึกผลการตรวจ)
    // -------------------------------------------------------

    public function inspection_approve($id)
    {
        $item     = $this->_getItem($id);
        $ros_user = $this->_checkIbOwnership($item);
        $plan     = $this->_getPlan($item->id_follow);
        $action   = request()->get('action', 'checking');

        $qc_items = FactoryQcItem::active()->ordered()->get();

        // ผล QC ที่เคยบันทึกสำหรับรายการนี้ (keyed by item_id)
        $qc_results = ControlFollowIbQcResult::where('list_id', $id)
                        ->get()
                        ->keyBy('item_id');

        // ประวัติการตรวจ (สามารถมีหลายครั้ง)
        $inspections = ControlFollowIbInspection::where('list_id', $id)
                        ->orderBy('id', 'desc')
                        ->get();

        // รายงานล่าสุด (ใช้สำหรับ pre-fill ฟอร์มรายงาน)
        $report = $inspections->first();

        return view('section5.factory-inspection.esurveillance.approve',
            compact('item', 'plan', 'action', 'qc_items', 'qc_results', 'inspections', 'report'));
    }

    // -------------------------------------------------------
    // Save Step 1 (ยืนยัน/ปฏิเสธ)
    // -------------------------------------------------------

    public function inspection_approve_save(Request $request)
    {
        $id     = $request->input('id');
        $status = $request->input('status'); // '2' = Accept, '3' = Refuse

        $item = $this->_getItem($id);
        $this->_checkIbOwnership($item);

        $officer_status = ($status == '2') ? 'IB ตอบรับการตรวจ' : 'IB ปฏิเสธ';

        DB::table('control_follow_list_table')->where('id', $id)->update([
            'officer_status' => $officer_status,
            'updated_at'     => now(),
        ]);

        return redirect('section5/factory-inspection?type=esurveillance')
            ->with('flash_message', 'บันทึกสถานะเรียบร้อยแล้ว');
    }

    // -------------------------------------------------------
    // Save Step 2 (บันทึกผลการตรวจ / รายงาน)
    // -------------------------------------------------------

    public function inspection_result_save(Request $request)
    {
        $id     = $request->input('id');
        $action = $request->input('action', 'checking'); // 'checking' | 'report'

        $item     = $this->_getItem($id);
        $ros_user = $this->_checkIbOwnership($item);

        DB::beginTransaction();
        try {
            if ($action == 'report') {
                // ------- สร้าง/อัพเดทรายงาน (control_follow_ib_inspection) -------
                $insp = ControlFollowIbInspection::firstOrNew(['list_id' => $id]);
                $isNew = !$insp->exists;

                $insp->list_id              = $id;
                $insp->subject              = $request->input('subject');
                $insp->ref_no               = $request->input('ref_no');
                $insp->applicant_data       = $request->input('applicant_data');
                $insp->factory_data         = $request->input('factory_data');
                $insp->product_scope        = $request->input('product_scope');
                $insp->result_data          = $request->input('result_data');
                $insp->inspect_final_result = $request->input('inspect_result');
                $insp->inspect_comment      = $request->input('inspect_comment');
                $insp->modified             = now();
                $insp->modified_by          = $ros_user->id;

                if ($isNew) {
                    $insp->created    = now();
                    $insp->created_by = $ros_user->id;
                }

                if ($request->hasFile('inspect_report_file')) {
                    $files = [];
                    foreach ($request->file('inspect_report_file') as $file) {
                        if ($file->isValid()) {
                            $realfile = time() . '_' . $file->getClientOriginalName();
                            Storage::disk('ftp')->putFileAs('factory_report', $file, $realfile);
                            $files[] = [
                                'realfile' => $realfile,
                                'filename' => $file->getClientOriginalName(),
                            ];
                        }
                    }
                    $insp->inspect_report_file = json_encode($files);
                }

                $insp->save();

                DB::commit();
                return redirect('section5/factory-inspection/esurveillance/approve/' . $id . '?action=report')
                    ->with('flash_message', 'บันทึกรายงานเรียบร้อยแล้ว');
            }

            // ------- action == checking: บันทึกผล QC -------
            $result = $request->input('result');
            if (empty($result)) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error_message', 'กรุณาเลือกสรุปผลการประเมินให้ครบทุกข้อก่อนบันทึก')
                    ->withInput();
            }

            $qc_results_input = $request->input('qc_result', []);
            $qc_summaries     = $request->input('qc_summary', []);

            foreach ($qc_results_input as $item_id => $result_val) {
                $summary_status = 'pass';
                if ($result_val == 2)      $summary_status = 'fail';
                elseif ($result_val == 3)  $summary_status = 'request';

                ControlFollowIbQcResult::updateOrCreate(
                    ['list_id' => $id, 'item_id' => $item_id],
                    [
                        'result_text'    => $qc_summaries[$item_id] ?? '',
                        'summary_status' => $summary_status,
                        'summary_detail' => $qc_summaries[$item_id] ?? '',
                        'created'        => now(),
                        'created_by'     => $ros_user->id,
                    ]
                );
            }

            // บันทึกผลรวม + วันที่ตรวจลง control_follow_ib_inspection
            $insp = ControlFollowIbInspection::firstOrNew(['list_id' => $id]);
            $isNew = !$insp->exists;

            $insp->list_id              = $id;
            $insp->inspect_final_result = $result;
            $insp->modified             = now();
            $insp->modified_by          = $ros_user->id;

            if ($isNew) {
                $insp->created    = now();
                $insp->created_by = $ros_user->id;
            }

            // ไฟล์แนบ
            if ($request->hasFile('att_file')) {
                $filenames = [];
                foreach ($request->file('att_file') as $file) {
                    if ($file->isValid()) {
                        $filename = time() . '_' . $file->getClientOriginalName();
                        Storage::disk('ftp')->putFileAs('factory_inspection', $file, $filename);
                        $filenames[] = $filename;
                    }
                }
                // เก็บในช่อง result_data ชั่วคราว หรือใช้ field ใหม่ถ้ามี
                // ใช้ result_data เก็บ json ไฟล์ attachment ชั่วคราว (override หากมี)
                // หรือจะเพิ่ม column ก็ได้ — ตอนนี้ใช้ field inspect_report_file สำรอง
                if (!empty($filenames)) {
                    $existingFiles = json_decode($insp->inspect_report_file ?? '[]', true) ?: [];
                    foreach ($filenames as $fn) {
                        $existingFiles[] = ['realfile' => $fn, 'filename' => $fn];
                    }
                    $insp->inspect_report_file = json_encode($existingFiles);
                }
            }

            $insp->save();

            // อัพเดทสถานะเป็น "เสร็จสิ้น"
            DB::table('control_follow_list_table')->where('id', $id)->update([
                'officer_status' => 'เสร็จสิ้น',
                'updated_at'     => now(),
            ]);

            DB::commit();
            return redirect('section5/factory-inspection/esurveillance/approve/' . $id)
                ->with('flash_message', 'บันทึกผลการตรวจเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('CsurvInspectionController@inspection_result_save error: ' . $e->getMessage());
            return redirect()->back()->with('error_message', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}
