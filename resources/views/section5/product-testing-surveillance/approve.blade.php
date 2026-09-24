@extends('layouts.master')

@php
    $page_title = 'บันทึกผลการตรวจ';
    if($item->status == 1){
        $page_title = 'การรับคำขอทดสอบผลิตภัณฑ์ (เฝ้าระวัง)';
    }
@endphp

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">
                        {{ $page_title }}
                    </h3>
                    <div class="pull-right">
                        <a href="{{ url('section5/product-testing-surveillance') }}" class="btn btn-default btn-sm">
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

                    @include('section5.product-testing-surveillance.partials.checking_form')

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

    $('#btn-add-file').on('click', function() {
        var newRow = '<div class="file-row" style="margin-bottom: 10px;">' +
            '<div class="input-group">' +
            '<input type="file" name="att_file[]" class="form-control">' +
            '<span class="input-group-btn">' +
            '<button type="button" class="btn btn-danger btn-remove-file">' +
            '<i class="fa fa-times"></i></button>' +
            '</span>' +
            '</div></div>';

        $('#file-attachment-container').append(newRow);
        updateRemoveButtons();
    });

    $(document).on('click', '.btn-remove-file', function() {
        $(this).closest('.file-row').remove();
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        var rows = $('.file-row').length;
        if (rows <= 1) {
            $('.btn-remove-file').prop('disabled', true);
        } else {
            $('.btn-remove-file').prop('disabled', false);
        }
    }

});
</script>
@endpush
