@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">
                        บันทึกผลการตรวจโรงงาน (IB/CB)
                    </h3>
                    <div class="pull-right">
                        @if($action == 'report')
                            <a href="{{ url('section5/factory-inspection/approve/' . $item->id) }}" class="btn btn-info btn-sm"><i class="fa fa-check-square-o"></i> บันทึกผลการตรวจ</a>
                        @else
                            <a href="{{ url('section5/factory-inspection/approve/' . $item->id . '?action=report') }}" class="btn btn-primary btn-sm"><i class="fa fa-file-text-o"></i> สร้างรายงาน</a>
                        @endif
                        <a href="{{ url('section5/factory-inspection') }}" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> กลับ</a>
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
                        @include('section5.factory-inspection.partials.report_form')
                    @else
                        @include('section5.factory-inspection.partials.checking_form')
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
        var totalItems  = $('select[name^="qc_result"]').length;
        var filledItems = 0;
        var passCount   = 0;
        var failCount   = 0;
        var requestCount = 0;

        $('select[name^="qc_result"]').each(function() {
            var val = $(this).val();
            if (val !== '' && val !== null) {
                filledItems++;
                if (val == '1') passCount++;
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
