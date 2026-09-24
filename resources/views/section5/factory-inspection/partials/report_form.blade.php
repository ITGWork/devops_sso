{{-- Report Form for Creating Inspection Report --}}
@php
    $app_data = $item->applicant_data;
    if(empty($app_data)) {
        $head_address = trim(($user_created->head_address_no ?? '') . ' ' .
                        ($user_created->head_moo ? 'หมู่ '.$user_created->head_moo.' ' : '') .
                        ($user_created->head_soi ? 'ซอย '.$user_created->head_soi.' ' : '') .
                        ($user_created->head_street ? 'ถนน '.$user_created->head_street.' ' : '') .
                        ($user_created->head_subdistrict ? 'ตำบล'.$user_created->head_subdistrict.' ' : '') .
                        ($user_created->head_district ? 'อำเภอ'.$user_created->head_district.' ' : '') .
                        ($user_created->head_province ? 'จังหวัด'.$user_created->head_province.' ' : ''));

        $app_data = "1.1 ชื่อผู้ยื่นคำขอ " . ($user_created->name ?? '-') . "\r\n" .
                    "1.2 สำนักงานแห่งใหญ่ตั้งอยู่ที่ " . $head_address . "\r\n" .
                    "1.3 ชื่อผู้ประสานงาน: " . ($item->factory->coordinator ?? '-') . "\r\n" .
                    "    ตำแหน่ง: " . ($item->factory->coordinator_position ?? '-') . "\r\n" .
                    "    โทรศัพท์: " . ($item->factory->coordinator_tel ?? '-') . "    โทรสาร: -    email: " . ($user_created->email ?? '-');
    }

    $fac_data = $item->factory_data;
    if(empty($fac_data)){
        $fac_address = trim(($item->factory->factory_address_no ?? '') . ' ' .
                       ($item->factory->factory_moo ? 'หมู่ '.$item->factory->factory_moo.' ' : '') .
                       ($item->factory->factory_soi ? 'ซอย '.$item->factory->factory_soi.' ' : '') .
                       ($item->factory->factory_street ? 'ถนน '.$item->factory->factory_street.' ' : '') .
                       ($item->factory->factory_subdistrict ? 'ตำบล'.$item->factory->factory_subdistrict.' ' : '') .
                       ($item->factory->factory_district ? 'อำเภอ'.$item->factory->factory_district.' ' : '') .
                       ($item->factory->factory_province ? 'จังหวัด'.$item->factory->factory_province.' ' : ''));

        $fac_data = "2.1 ชื่อโรงงาน: " . ($item->factory->factory_name ?? '-') . "\r\n" .
                    "2.2 สถานที่ตั้งโรงงาน: " . ($item->factory->factory_name ?? '') . " " . $fac_address . "\r\n" .
                    "2.3 เลขทะเบียนโรงงาน: " . ($item->factory->factory_register_no ?? '-');
    }

    $scope_data = $item->product_scope;
    if(empty($scope_data)){
        $scope_data = "3.1 ชื่อผลิตภัณฑ์: มาตรฐานผลิตภัณฑ์อุตสาหกรรม " . ($item->factory->tis_name ?? '-') . "\r\n" .
                      "    รายละเอียดมาจาก มอก. " . ($item->factory->tis_number ?? '-');
    }

    $insp_result_val = $item->inspect_result;
    $latest_insp     = $inspections->first();

    if(empty($insp_result_val)) {
        if($latest_insp) {
            $insp_result_val = ($latest_insp->result == 1) ? 1 : 2;
        }
    }

    $insp_eval_data = $item->result_data;
    if(empty($insp_eval_data)) {
        $insp_date_text = ".................";
        if($latest_insp && $latest_insp->start_inspect_date){
            $insp_date_text = HP::DateThaiFull($latest_insp->start_inspect_date);
        }
        $box_pass = ($insp_result_val == 1) ? '☑' : '☐';
        $box_fail = ($insp_result_val != 1) ? '☑' : '☐';

        $insp_eval_data = "ตรวจประเมินระบบการควบคุมคุณภาพของโรงงาน เมื่อวันที่ " . $insp_date_text . "\r\n" .
                          "ปรากฏผลว่า:\r\n" .
                          "    " . $box_pass . " เอกสารสอดคล้องกับข้อกำหนดระบบควบคุมคุณภาพทุกรายการ\r\n" .
                          "    " . $box_fail . " เอกสารไม่สอดคล้องกับข้อกำหนดระบบควบคุมคุณภาพบางรายการ";
    }
@endphp

{{ Form::open(['url' => 'section5/factory-inspection/result-save', 'class' => 'form-horizontal', 'method' => 'POST', 'files' => true]) }}
<input type="hidden" name="id" value="{{ $item->id }}">
<input type="hidden" name="action" value="report">

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-success">
            <div class="panel-heading">สร้างรายงานผลการตรวจ</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label">เรื่อง :</label>
                    <div class="col-md-10">
                        <input type="text" name="subject" class="form-control"
                               value="{{ $item->subject ?? 'รายงานผลการตรวจประเมินระบบควบคุมคุณภาพผลิตภัณฑ์' }}"
                               placeholder="หัวข้อรายงาน">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">เลขที่อ้างอิง :</label>
                    <div class="col-md-4">
                        <input type="text" name="ref_no" class="form-control"
                               value="{{ $item->ref_no ?? ($item->factory->refno ?? '') }}"
                               placeholder="เลขที่อ้างอิง">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ข้อมูลผู้ยื่น :</label>
                    <div class="col-md-10">
                        <textarea name="applicant_data" class="form-control" rows="6"
                                  placeholder="ข้อมูลผู้ยื่นคำขอ">{{ $app_data }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ข้อมูลโรงงาน :</label>
                    <div class="col-md-10">
                        <textarea name="factory_data" class="form-control" rows="4"
                                  placeholder="ข้อมูลโรงงาน">{{ $fac_data }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ขอบเขตผลิตภัณฑ์ :</label>
                    <div class="col-md-10">
                        <textarea name="product_scope" class="form-control" rows="3"
                                  placeholder="ขอบเขตผลิตภัณฑ์">{{ $scope_data }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ผลการตรวจประเมิน :</label>
                    <div class="col-md-10">
                        <textarea name="result_data" class="form-control" rows="5"
                                  placeholder="สรุปผลการตรวจ">{{ $insp_eval_data }}</textarea>
                        <input type="hidden" name="inspect_result" value="{{ $insp_result_val }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ความเห็น :</label>
                    <div class="col-md-10">
                        <textarea name="inspect_comment" class="form-control" rows="3"
                                  placeholder="ความเห็นเพิ่มเติม">{{ $item->inspect_comment ?? '' }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ไฟล์รายงาน :</label>
                    <div class="col-md-10">
                        @php
                            $checkingFiles = json_decode($latest_insp->att_file ?? '[]', true);
                            $reportFiles   = json_decode($item->inspect_report_file ?? '[]', true);
                        @endphp

                        @if(empty($reportFiles) && !empty($checkingFiles))
                            <div class="alert alert-info m-b-10">
                                <strong><i class="fa fa-info-circle"></i> ไฟล์จากการตรวจ</strong>
                                (จะถูกนำมาใช้โดยอัตโนมัติ หากไม่แนบไฟล์ใหม่)
                                <div class="m-t-5">
                                    @foreach($checkingFiles as $f)
                                        @php
                                            $ckRealfile = is_array($f) ? ($f['realfile'] ?? '') : $f;
                                            $ckFilename = is_array($f) ? ($f['filename'] ?? $ckRealfile) : $f;
                                        @endphp
                                        <a href="{{ HP::getFileStorage('factory_inspection/' . $ckRealfile) }}" target="_blank" class="btn btn-xs btn-info m-r-5"><i class="fa fa-file"></i> {{ $ckFilename }}</a>
                                        <input type="hidden" name="inherited_att_file[]" value="{{ $ckRealfile }}">
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <input type="file" name="inspect_report_file[]" multiple class="form-control">
                        <small class="text-muted">สามารถแนบได้หลายไฟล์ (แนบใหม่จะแทนที่ไฟล์ข้างต้น)</small>

                        @if(!empty($reportFiles))
                            <div class="m-t-10">
                                <strong>ไฟล์ที่แนบไว้:</strong>
                                @foreach((array)$reportFiles as $file)
                                    @php
                                        $realfile = is_array($file) ? ($file['realfile'] ?? '') : $file;
                                        $filename = is_array($file) ? ($file['filename'] ?? $realfile) : $file;
                                        $folder   = is_array($file) ? ($file['folder'] ?? 'factory_report') : 'factory_report';
                                    @endphp
                                    <a href="{{ HP::getFileStorage($folder . '/' . $realfile) }}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-file"></i> {{ $filename }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- QC Items (Read Only) --}}
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">ข้อกำหนดการตรวจ QC ที่ถูกบันทึก</div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">ลำดับ</th>
                                <th width="30%">ข้อกำหนดระบบการควบคุมคุณภาพผลิตภัณฑ์</th>
                                <th width="50%">ผลการตรวจประเมิน</th>
                                <th width="15%" class="text-center">สรุปผลการประเมิน</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($qc_items) && isset($qc_results))
                                @foreach($qc_items as $index => $qc)
                                @php
                                    $existing    = $qc_results->get($qc->id);
                                    $status_text = '-';
                                    $label_class = 'default';
                                    if(isset($existing)) {
                                        if($existing->summary_status == 'pass')    { $status_text = 'ผ่าน'; $label_class = 'success'; }
                                        elseif($existing->summary_status == 'fail')    { $status_text = 'ไม่ผ่าน'; $label_class = 'danger'; }
                                        elseif($existing->summary_status == 'request') { $status_text = 'ขอเอกสารเพิ่มเติม'; $label_class = 'warning'; }
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $qc->ordering ?? ($index + 1) }}</td>
                                    <td>{{ $qc->question_text }}</td>
                                    <td>{!! nl2br(e($existing ? $existing->summary_detail : '-')) !!}</td>
                                    <td class="text-center"><span class="label label-{{ $label_class }}">{{ $status_text }}</span></td>
                                </tr>
                                @endforeach
                            @else
                                <tr><td colspan="4" class="text-center">ไม่พบข้อมูลการตรวจ QC</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Submit Button --}}
    <div class="col-md-12">
        <div class="form-group">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save"></i> บันทึกรายงาน</button>
                <a href="{{ url('section5/factory-inspection/approve/' . $item->id) }}" class="btn btn-default btn-lg">ยกเลิก</a>
            </div>
        </div>
    </div>
</div>

{{ Form::close() }}
