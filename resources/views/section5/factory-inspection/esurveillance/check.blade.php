@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">ตรวจสอบคำขอตรวจโรงงาน (E-Surveillance Plan)</h3>
                    <div class="pull-right">
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

                    {{ Form::open(['url' => 'section5/factory-inspection/esurveillance/approve-save', 'class' => 'form-horizontal', 'method' => 'POST']) }}
                    <input type="hidden" name="id" value="{{ $item->id }}">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">รายละเอียดแผนการตรวจ</div>
                                <div class="panel-wrapper collapse in" aria-expanded="true">
                                    <div class="panel-body">

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">ข้อมูลแผนงาน</legend>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ชื่อแผนงาน :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $plan->title ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ปีงบประมาณ :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $plan->make_annual ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ผู้จัดทำแผน :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $plan->check_officer ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">ข้อมูลผู้รับใบอนุญาต</legend>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ชื่อผู้รับใบอนุญาต :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $item->operator_name ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">เลขประจำตัวผู้เสียภาษี :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $item->tax_id ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">เลขที่ใบอนุญาต :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $item->license_no ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ที่อยู่ :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $item->address ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">ข้อมูลผลิตภัณฑ์</legend>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">เลขที่ มอก. :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $item->tis_no ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">สถานะปัจจุบัน :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">
                                                        <span class="label label-warning">{{ $item->officer_status ?? 'รอ IB ยอมรับ' }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </fieldset>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">ตรวจสอบการรับงาน</div>
                                <div class="panel-wrapper collapse in" aria-expanded="true">
                                    <div class="panel-body">
                                        <div class="form-group required">
                                            <label class="col-md-3 control-label">ผลการพิจารณา :</label>
                                            <div class="col-md-7">
                                                <label>
                                                    {!! Form::radio('status', '2', true, ['class' => 'check', 'data-radio' => 'iradio_square-green']) !!}
                                                    ยอมรับรับงาน
                                                </label>
                                                &nbsp;&nbsp;
                                                <label>
                                                    {!! Form::radio('status', '3', false, ['class' => 'check', 'data-radio' => 'iradio_square-red']) !!}
                                                    ปฏิเสธรับงาน
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">หมายเหตุ :</label>
                                            <div class="col-md-8">
                                                {!! Form::textarea('remark', null, ['class' => 'form-control', 'rows' => '4', 'placeholder' => 'ระบุหมายเหตุ (ถ้ามี)']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-offset-4 col-md-4">
                            <button type="submit" class="btn btn-primary" onclick="return confirm('ยืนยันบันทึกผลการพิจารณา?')">
                                <i class="fa fa-save"></i> บันทึก
                            </button>
                            <a class="btn btn-default" href="{{ url('section5/factory-inspection?type=esurveillance') }}">
                                <i class="fa fa-rotate-left"></i> ยกเลิก
                            </a>
                        </div>
                    </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
$(document).ready(function () {
    $('.check').each(function () {
        var ck = $(this);
        ck.iCheck({
            radioClass: ck.data('radio') || 'iradio_square-blue',
            checkboxClass: ck.data('checkbox') || 'icheckbox_square-blue'
        });
    });
});
</script>
@endpush
