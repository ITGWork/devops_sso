@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">
                        บันทึกผลการตรวจโรงงาน (E-Surveillance Plan)
                    </h3>
                    <div class="pull-right">
                        @if($action == 'report')
                            <a href="{{ url('section5/factory-inspection/esurveillance/approve/' . $item->id) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-check-square-o"></i> บันทึกผลการตรวจ
                            </a>
                        @else
                            <a href="{{ url('section5/factory-inspection/esurveillance/approve/' . $item->id . '?action=report') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-file-text-o"></i> สร้างรายงาน
                            </a>
                        @endif
                        <a href="{{ url('section5/factory-inspection?type=esurveillance') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> กลับ
                        </a>
                    </div>
                    <div class="clearfix"></div>
                    <hr>

                    @if(session('flash_message'))
                        <div class="alert alert-success">{{ session('flash_message') }}</div>
                    @endif
                    @if(session('error_message'))
                        <div class="alert alert-danger">{{ session('error_message') }}</div>
                    @endif

                    @if($action == 'report')
                        @include('section5.factory-inspection.esurveillance.partials.report_form')
                    @else
                        {{-- ===== ฟอร์มบันทึกผลการตรวจ QC ===== --}}
                        {{ Form::open(['url' => 'section5/factory-inspection/esurveillance/result-save', 'class' => 'form-horizontal', 'method' => 'POST', 'files' => true]) }}
                        <input type="hidden" name="id" value="{{ $item->id }}">
                        <input type="hidden" name="action" value="checking">

                        <div class="row">
                            {{-- Section 1: ข้อมูลแผนงาน --}}
                            <div class="col-md-12">
                                <div class="panel panel-info">
                                    <div class="panel-heading">1. ข้อมูลแผนงาน</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">ชื่อแผนงาน :</label>
                                                    <div class="col-md-8"><p class="form-control-static">{{ $plan->title ?? '-' }}</p></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">ปีงบประมาณ :</label>
                                                    <div class="col-md-8"><p class="form-control-static">{{ $plan->make_annual ?? '-' }}</p></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">ผู้จัดทำแผน :</label>
                                                    <div class="col-md-8"><p class="form-control-static">{{ $plan->check_officer ?? '-' }}</p></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">สถานะ :</label>
                                                    <div class="col-md-8">
                                                        <p class="form-control-static">
                                                            <span class="label label-warning">{{ $item->officer_status ?? '-' }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 2: ข้อมูลผู้รับใบอนุญาต --}}
                            <div class="col-md-12">
                                <div class="panel panel-info">
                                    <div class="panel-heading">2. ข้อมูลผู้รับใบอนุญาต</div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">ชื่อผู้รับใบอนุญาต :</label>
                                                    <div class="col-md-8"><p class="form-control-static">{{ $item->operator_name ?? '-' }}</p></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">เลขเสียภาษี :</label>
                                                    <div class="col-md-8"><p class="form-control-static">{{ $item->tax_id ?? '-' }}</p></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">เลขที่ใบอนุญาต :</label>
                                                    <div class="col-md-8"><p class="form-control-static">{{ $item->license_no ?? '-' }}</p></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">ที่อยู่ :</label>
                                                    <div class="col-md-8"><p class="form-control-static">{{ $item->address ?? '-' }}</p></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">เลขที่ มอก. :</label>
                                                    <div class="col-md-8"><p class="form-control-static">{{ $item->tis_no ?? '-' }}</p></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 3: ประวัติการตรวจ --}}
                            <div class="col-md-12">
                                <div class="panel panel-warning" style="border: 2px solid #ffc107;">
                                    <div class="panel-heading" style="background-color: #ffc107; color: #000; font-weight: bold;">ประวัติการตรวจ</div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr style="background-color: #f8f9fa;">
                                                        <th class="text-center" width="5%">#</th>
                                                        <th class="text-center" width="20%">วันที่บันทึก</th>
                                                        <th class="text-center" width="15%">ผลการตรวจ</th>
                                                        <th class="text-center" width="30%">ความเห็น</th>
                                                        <th class="text-center" width="30%">ไฟล์แนบ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(isset($inspections) && $inspections->count() > 0)
                                                        @foreach($inspections as $index => $insp)
                                                            <tr>
                                                                <td class="text-center">{{ $index + 1 }}</td>
                                                                <td class="text-center">{{ !empty($insp->created) ? HP::DateThai($insp->created) : '-' }}</td>
                                                                <td class="text-center">
                                                                    @php
                                                                        $res = $insp->inspect_final_result;
                                                                    @endphp
                                                                    @if($res == 1)
                                                                        <span class="label label-success" style="padding: 5px 10px;">ผ่าน</span>
                                                                    @elseif($res == 2)
                                                                        <span class="label label-warning" style="padding: 5px 10px;">แก้ไขข้อบกพร่อง</span>
                                                                    @elseif($res == 3)
                                                                        <span class="label label-danger" style="padding: 5px 10px;">ไม่ผ่าน</span>
                                                                    @else
                                                                        <span class="label label-default">-</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $insp->inspect_comment ?? '-' }}</td>
                                                                <td class="text-center">
                                                                    @php
                                                                        $files = json_decode($insp->inspect_report_file ?? '[]', true);
                                                                    @endphp
                                                                    @if(!empty($files))
                                                                        @foreach((array)$files as $file)
                                                                            @php
                                                                                $realfile = is_array($file) ? ($file['realfile'] ?? '') : $file;
                                                                                $filename = is_array($file) ? ($file['filename'] ?? $realfile) : $file;
                                                                            @endphp
                                                                            <a href="{{ url('section5/factory-inspection/esurveillance/download/' . $realfile) }}"
                                                                               target="_blank" class="btn btn-info btn-xs m-b-5">
                                                                                <i class="fa fa-paperclip"></i> {{ $filename }}
                                                                            </a>
                                                                        @endforeach
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5" class="text-center">ไม่พบประวัติการตรวจ</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 4: ข้อกำหนด QC --}}
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
                                                    $qc_res = $qc_results[$qc->id] ?? null;
                                                    $val_ofsev = $qc_res->result_text ?? '';
                                                    $val_res_raw = $qc_res->summary_status ?? '';
                                                    $res_code = '';
                                                    if($val_res_raw == 'pass')         $res_code = 1;
                                                    elseif($val_res_raw == 'fail')     $res_code = 2;
                                                    elseif($val_res_raw == 'request')  $res_code = 3;
                                                @endphp
                                                <tr>
                                                    <td>{{ $qc->ordering ?? ($index + 1) }}</td>
                                                    <td>{{ $qc->question_text }}</td>
                                                    <td>
                                                        <textarea name="qc_summary[{{ $qc->id }}]" class="form-control"
                                                                  style="overflow-y: auto; min-height: 60px; resize: vertical;"
                                                                  oninput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px';"
                                                                  placeholder="ผลการตรวจประเมิน">{{ $val_ofsev }}</textarea>
                                                    </td>
                                                    <td>
                                                        <select name="qc_result[{{ $qc->id }}]" class="form-control">
                                                            <option value="">-- เลือก --</option>
                                                            <option value="1" {{ $res_code == 1 ? 'selected' : '' }}>ผ่าน</option>
                                                            <option value="2" {{ $res_code == 2 ? 'selected' : '' }}>ไม่ผ่าน</option>
                                                            <option value="3" {{ $res_code == 3 ? 'selected' : '' }}>ขอเอกสารเพิ่มเติม</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 5: ผลการตรวจโรงงาน --}}
                            <div class="col-md-12">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">ผลการตรวจโรงงาน</div>
                                    <div class="panel-body">
                                        <div class="form-group required">
                                            <label class="col-md-2 control-label">ผลการตรวจ :</label>
                                            <div class="col-md-10">
                                                <div id="result-display" class="alert alert-info" style="margin-bottom: 0;">
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
                                                                <button type="button" class="btn btn-danger btn-remove-file" disabled>
                                                                    <i class="fa fa-times"></i>
                                                                </button>
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

                            {{-- Submit --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fa fa-save"></i> บันทึกผลการตรวจ
                                        </button>
                                        <a href="{{ url('section5/factory-inspection?type=esurveillance') }}" class="btn btn-default btn-lg">ยกเลิก</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{ Form::close() }}
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
$(document).ready(function(){

    if($.fn.datepicker) {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    }

    function calculateResult() {
        var totalItems   = $('select[name^="qc_result"]').length;
        var filledItems  = 0;
        var passCount    = 0;
        var failCount    = 0;
        var requestCount = 0;

        $('select[name^="qc_result"]').each(function() {
            var val = $(this).val();
            if (val !== '' && val !== null) {
                filledItems++;
                if (val == '1')      passCount++;
                else if (val == '2') failCount++;
                else if (val == '3') requestCount++;
            }
        });

        var resultDisplay = $('#result-display');
        var resultText    = $('#result-text');
        var resultValue   = $('#result-value');

        if (totalItems === 0) {
            resultDisplay.removeClass('alert-success alert-warning alert-danger').addClass('alert-info');
            resultText.text('ไม่พบรายการตรวจ QC');
            resultValue.val('');
        } else if (filledItems < totalItems) {
            resultDisplay.removeClass('alert-success alert-warning alert-danger').addClass('alert-info');
            resultText.text('กรุณาเลือกสรุปผลการประเมินให้ครบทุกข้อ (' + filledItems + '/' + totalItems + ')');
            resultValue.val('');
        } else if (failCount > 0) {
            resultDisplay.removeClass('alert-success alert-warning alert-info').addClass('alert-danger');
            resultText.text('ไม่ผ่าน - ต้องไปตรวจโรงงานใหม่');
            resultValue.val('3');
        } else if (requestCount > 0) {
            resultDisplay.removeClass('alert-success alert-danger alert-info').addClass('alert-warning');
            resultText.text('แก้ไขข้อบกพร่อง - รอเอกสารเพิ่มเติม');
            resultValue.val('2');
        } else {
            resultDisplay.removeClass('alert-warning alert-danger alert-info').addClass('alert-success');
            resultText.text('ผ่าน - ครบทุกข้อ');
            resultValue.val('1');
        }
    }

    calculateResult();

    $('body').on('change', 'select[name^="qc_result"]', function() {
        calculateResult();
    });

    $('body').on('input', 'textarea[name^="qc_summary"]', function() {
        var strVal  = $(this).val().trim();
        var idMatch = $(this).attr('name').match(/\[(\d+)\]/);
        if (idMatch) {
            var qcId      = idMatch[1];
            var selectBox = $('select[name="qc_result[' + qcId + ']"]');
            if (strVal.length > 0) {
                selectBox.val('1').trigger('change');
            } else {
                selectBox.val('2').trigger('change');
            }
        }
    });

    $('#btn-add-file').on('click', function() {
        var newRow = '<div class="file-row" style="margin-bottom: 10px;">' +
            '<div class="input-group">' +
            '<input type="file" name="att_file[]" class="form-control">' +
            '<span class="input-group-btn">' +
            '<button type="button" class="btn btn-danger btn-remove-file"><i class="fa fa-times"></i></button>' +
            '</span>' +
            '</div>' +
            '</div>';
        $('#file-attachment-container').append(newRow);
        updateRemoveButtons();
    });

    $(document).on('click', '.btn-remove-file', function() {
        $(this).closest('.file-row').remove();
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        var rows = $('.file-row').length;
        $('.btn-remove-file').prop('disabled', rows <= 1);
    }
});
</script>
@endpush
