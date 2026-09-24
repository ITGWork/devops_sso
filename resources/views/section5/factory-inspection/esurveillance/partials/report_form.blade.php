{{-- Report Form — E-Surveillance Factory Inspection --}}
@php
    // ใช้ข้อมูลจาก control_follow_ib_inspection (ถ้ามี) ก่อน แล้ว fallback ไปที่ control_follow_list_table

    // 1. ข้อมูลผู้ยื่น
    $app_data = $report->applicant_data ?? null;
    if (empty($app_data)) {
        $app_data = "1.1 ชื่อผู้ยื่นคำขอ " . ($item->operator_name ?? '-') . "\r\n" .
                    "1.2 สำนักงานแห่งใหญ่ตั้งอยู่ที่ " . ($item->address ?? '-') . "\r\n" .
                    "1.3 เลขประจำตัวผู้เสียภาษี: " . ($item->tax_id ?? '-');
    }

    // 2. ข้อมูลโรงงาน
    $fac_data = $report->factory_data ?? null;
    if (empty($fac_data)) {
        $fac_data = "2.1 ชื่อโรงงาน: " . ($item->operator_name ?? '-') . "\r\n" .
                    "2.2 สถานที่ตั้งโรงงาน: " . ($item->address ?? '-') . "\r\n" .
                    "2.3 เลขทะเบียนโรงงาน: -";
    }

    // 3. ขอบเขตผลิตภัณฑ์
    $scope_data = $report->product_scope ?? null;
    if (empty($scope_data)) {
        $scope_data = "3.1 มาตรฐานเลขที่ มอก. " . ($item->tis_no ?? '-') . "\r\n" .
                      "    เลขที่ใบอนุญาต: " . ($item->license_no ?? '-');
    }

    // 4. ผลประเมิน
    $insp_result_val = $report->inspect_final_result ?? null;

    $insp_eval_data = $report->result_data ?? null;
    if (empty($insp_eval_data)) {
        $box_pass = ($insp_result_val == 1) ? '☑' : '☐';
        $box_fail = ($insp_result_val != 1 && $insp_result_val !== null) ? '☑' : '☐';

        $insp_eval_data = "ตรวจประเมินระบบการควบคุมคุณภาพของโรงงาน\r\n" .
                          "ปรากฏผลว่า:\r\n" .
                          "    " . $box_pass . " เอกสารสอดคล้องกับข้อกำหนดระบบควบคุมคุณภาพทุกรายการ\r\n" .
                          "    " . $box_fail . " เอกสารไม่สอดคล้องกับข้อกำหนดระบบควบคุมคุณภาพบางรายการ";
    }

    // ไฟล์รายงาน
    $reportFiles = json_decode($report->inspect_report_file ?? '[]', true) ?: [];
@endphp

{{ Form::open(['url' => 'section5/factory-inspection/esurveillance/result-save', 'class' => 'form-horizontal', 'method' => 'POST', 'files' => true]) }}
<input type="hidden" name="id" value="{{ $item->id }}">
<input type="hidden" name="action" value="report">

<div class="row">
    {{-- ฟอร์มสร้างรายงาน --}}
    <div class="col-md-12">
        <div class="panel panel-success">
            <div class="panel-heading">สร้างรายงานผลการตรวจ (E-Surveillance Plan)</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label">เรื่อง :</label>
                    <div class="col-md-10">
                        <input type="text" name="subject" class="form-control"
                               value="{{ $report->subject ?? 'รายงานผลการตรวจประเมินระบบควบคุมคุณภาพผลิตภัณฑ์ (ตรวจติดตาม)' }}"
                               placeholder="หัวข้อรายงาน">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">เลขที่อ้างอิง :</label>
                    <div class="col-md-4">
                        <input type="text" name="ref_no" class="form-control"
                               value="{{ $report->ref_no ?? ($item->license_no ?? '') }}"
                               placeholder="เลขที่อ้างอิง">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ข้อมูลผู้ยื่น :</label>
                    <div class="col-md-10">
                        <textarea name="applicant_data" class="form-control" rows="5" placeholder="ข้อมูลผู้ยื่นคำขอ">{{ $app_data }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ข้อมูลโรงงาน :</label>
                    <div class="col-md-10">
                        <textarea name="factory_data" class="form-control" rows="4" placeholder="ข้อมูลโรงงาน">{{ $fac_data }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ขอบเขตผลิตภัณฑ์ :</label>
                    <div class="col-md-10">
                        <textarea name="product_scope" class="form-control" rows="3" placeholder="ขอบเขตผลิตภัณฑ์">{{ $scope_data }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ผลการตรวจประเมิน :</label>
                    <div class="col-md-10">
                        <textarea name="result_data" class="form-control" rows="5" placeholder="สรุปผลการตรวจ">{{ $insp_eval_data }}</textarea>
                        <input type="hidden" name="inspect_result" value="{{ $insp_result_val }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ความเห็น :</label>
                    <div class="col-md-10">
                        <textarea name="inspect_comment" class="form-control" rows="3" placeholder="ความเห็นเพิ่มเติม">{{ $report->inspect_comment ?? '' }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ไฟล์รายงาน :</label>
                    <div class="col-md-10">
                        <input type="file" name="inspect_report_file[]" multiple class="form-control">
                        <small class="text-muted">สามารถแนบได้หลายไฟล์ (แนบใหม่จะแทนที่ไฟล์เดิม)</small>

                        @if(!empty($reportFiles))
                            <div class="m-t-10">
                                <strong>ไฟล์ที่แนบไว้:</strong><br>
                                @foreach($reportFiles as $file)
                                    @php
                                        $realfile = is_array($file) ? ($file['realfile'] ?? '') : $file;
                                        $filename  = is_array($file) ? ($file['filename']  ?? $realfile) : $file;
                                    @endphp
                                    <a href="{{ url('section5/factory-inspection/esurveillance/download-report/' . $realfile) }}"
                                       target="_blank" class="btn btn-xs btn-info m-t-5">
                                        <i class="fa fa-file"></i> {{ $filename }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- QC Summary (Read-only) --}}
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">ข้อกำหนดการตรวจ QC ที่ถูกบันทึก</div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">ลำดับ</th>
                                <th width="30%">หัวข้อ</th>
                                <th width="50%">รายละเอียด</th>
                                <th width="15%" class="text-center">ผล</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($qc_items as $index => $qc)
                            @php
                                $qc_res      = $qc_results[$qc->id] ?? null;
                                $val_ofsev   = $qc_res->result_text ?? '-';
                                $val_res_raw = $qc_res->summary_status ?? '-';

                                $status_map = [
                                    'pass'    => 'ผ่าน',
                                    'fail'    => 'ไม่ผ่าน',
                                    'request' => 'ขอเพิ่ม',
                                ];
                                $val_res = $status_map[$val_res_raw] ?? $val_res_raw;
                                $label_class = ($val_res_raw == 'pass') ? 'success'
                                             : (($val_res_raw == 'fail') ? 'danger' : 'warning');
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $qc->question_text }}</td>
                                <td>{!! nl2br(e($val_ofsev)) !!}</td>
                                <td class="text-center">
                                    <span class="label label-{{ $label_class }}">{{ $val_res }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 text-center m-b-20">
        <button type="submit" class="btn btn-success btn-lg">
            <i class="fa fa-save"></i> บันทึกรายงาน
        </button>
        <a href="{{ url('section5/factory-inspection/esurveillance/approve/' . $item->id) }}" class="btn btn-default btn-lg">
            ยกเลิก
        </a>
    </div>
</div>
{{ Form::close() }}
