{{-- Checking Form for Recording Inspection Results --}}
{{ Form::open(['url' => 'section5/factory-inspection/result-save', 'class' => 'form-horizontal', 'method' => 'POST', 'files' => true]) }}
<input type="hidden" name="id" value="{{ $item->id }}">
<input type="hidden" name="action" value="checking">

<div class="row">
    {{-- Include readonly sections --}}
    @include('section5.factory-inspection.partials.readonly_view')
</div>

<div class="row">
    {{-- Section: QC Items --}}
    <div class="col-md-12">
        <div class="panel panel-success">
            <div class="panel-heading">ข้อกำหนดการตรวจ QC</div>
            <div class="panel-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">ลำดับ</th>
                            <th width="30%">ข้อกำหนดระบบการควบคุมคุณภาพผลิตภัณฑ์</th>
                            <th width="50%">ผลการตรวจประเมิน</th>
                            <th width="15%">สรุปผลการประเมิน</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($qc_items as $index => $qc)
                        @php
                            $existing = $qc_results->get($qc->id);
                        @endphp
                        <tr>
                            <td>{{ $qc->ordering ?? ($index + 1) }}</td>
                            <td>{{ $qc->question_text }}</td>
                            <td>
                                <textarea name="qc_summary[{{ $qc->id }}]" class="form-control"
                                          style="overflow-y: auto; min-height: 60px; resize: vertical;"
                                          oninput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px';"
                                          placeholder="ผลการตรวจประเมิน">{{ $existing ? $existing->summary_detail : '' }}</textarea>
                            </td>
                            <td>
                                @php
                                    $summary_text = $existing ? trim($existing->summary_detail) : '';
                                    $is_empty_detail = empty($summary_text);
                                    $selected_val = "";
                                    if ($is_empty_detail) {
                                        $selected_val = "2";
                                    } else {
                                        if ($existing) {
                                            if ($existing->summary_status == 'fail') $selected_val = "2";
                                            elseif ($existing->summary_status == 'request') $selected_val = "3";
                                            else $selected_val = "1";
                                        } else {
                                            $selected_val = "1";
                                        }
                                    }
                                @endphp
                                <select name="qc_result[{{ $qc->id }}]" class="form-control">
                                    <option value="">-- เลือก --</option>
                                    <option value="1" {{ $selected_val == '1' ? 'selected' : '' }}>ผ่าน</option>
                                    <option value="2" {{ $selected_val == '2' ? 'selected' : '' }}>ไม่ผ่าน</option>
                                    <option value="3" {{ $selected_val == '3' ? 'selected' : '' }}>ขอเอกสารเพิ่มเติม</option>
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Section: Inspection Result --}}
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">ผลการตรวจโรงงาน</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label">วันที่ตรวจ (เริ่ม) :</label>
                    <div class="col-md-4">
                        <input type="text" name="start_inspect_date" class="form-control datepicker"
                               value="{{ $item->start_inspect_date ?? date('Y-m-d') }}" placeholder="YYYY-MM-DD">
                    </div>
                    <label class="col-md-2 control-label">วันที่ตรวจ (สิ้นสุด) :</label>
                    <div class="col-md-4">
                        <input type="text" name="end_inspect_date" class="form-control datepicker"
                               value="{{ $item->end_inspect_date ?? date('Y-m-d') }}" placeholder="YYYY-MM-DD">
                    </div>
                </div>
                <div class="form-group required">
                    <label class="col-md-2 control-label">ผลการตรวจ :</label>
                    <div class="col-md-10">
                        <div id="result-display" class="alert" style="margin-bottom: 0;">
                            <strong id="result-text">กรุณาเลือกสรุปผลการประเมินให้ครบทุกข้อ</strong>
                        </div>
                        <input type="hidden" name="result" id="result-value" value="">
                        <small class="text-muted">
                            * ระบบจะคำนวณผลอัตโนมัติ: ผ่านทุกข้อ = ผ่าน, มีไม่ผ่าน = ไม่ผ่าน, มีขอเอกสารเพิ่ม = แก้ไขข้อบกพร่อง
                        </small>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ข้อบกพร่อง :</label>
                    <div class="col-md-10">
                        <textarea name="defect" class="form-control" rows="3" placeholder="ระบุข้อบกพร่อง (ถ้ามี)"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">หมายเหตุ :</label>
                    <div class="col-md-10">
                        <textarea name="remark" class="form-control" rows="3" placeholder="หมายเหตุเพิ่มเติม"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">ไฟล์แนบ :</label>
                    <div class="col-md-10">
                        <div id="file-attachment-container">
                            <div class="file-row" style="margin-bottom: 10px;">
                                <div class="input-group">
                                    <input type="file" name="att_file[]" class="form-control">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-danger btn-remove-file" disabled><i class="fa fa-times"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success btn-sm" id="btn-add-file">
                            <i class="fa fa-plus"></i> เพิ่มไฟล์แนบ
                        </button>
                        <small class="text-muted" style="margin-left: 10px;">คลิกปุ่ม "เพิ่มไฟล์แนบ" เพื่อเพิ่มไฟล์</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Submit Button --}}
    <div class="col-md-12">
        <div class="form-group">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save"></i> บันทึกผลการตรวจ</button>
                <a href="{{ url('section5/factory-inspection/approve/' . $item->id) }}" class="btn btn-default btn-lg">ยกเลิก</a>
            </div>
        </div>
    </div>
</div>

{{ Form::close() }}
