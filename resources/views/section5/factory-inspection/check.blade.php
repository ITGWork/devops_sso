@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">ตรวจสอบคำขอตรวจโรงงาน (IB/CB)</h3>
                    <div class="pull-right">
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

                    {{ Form::open(['url' => 'section5/factory-inspection/approve-save', 'class' => 'form-horizontal', 'method' => 'POST', 'files' => true]) }}
                    <input type="hidden" name="id" value="{{ $item->id }}">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">รายละเอียดคำขอ</div>
                                <div class="panel-wrapper collapse in" aria-expanded="true">
                                    <div class="panel-body">

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">ข้อมูลผู้ยื่นคำขอ</legend>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ชื่อผู้ยื่นขอ :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $user_created->name ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">วันเกิด/วันที่จดทะเบียน :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $user_created->date_niti ? HP::DateThai($user_created->date_niti) : ($user_created->date_of_birth ? HP::DateThai($user_created->date_of_birth) : '-') }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">เลขเสียภาษี :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $user_created->tax_number ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">Email :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $user_created->email ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">โทรศัพท์ :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $user_created->tel ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ที่อยู่ :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">
                                                        {{ $user_created->address_no ?? '' }}
                                                        {{ $user_created->moo ? 'หมู่ '.$user_created->moo : '' }}
                                                        {{ $user_created->soi ? 'ซอย '.$user_created->soi : '' }}
                                                        {{ $user_created->street ? 'ถนน '.$user_created->street : '' }}
                                                        {{ $user_created->subdistrict ? 'ต.'.$user_created->subdistrict : '' }}
                                                        {{ $user_created->district ? 'อ.'.$user_created->district : '' }}
                                                        {{ $user_created->province ? 'จ.'.$user_created->province : '' }}
                                                        {{ $user_created->zipcode ?? '' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">ข้อมูลขอรับบริการ</legend>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">เลขที่ มอก. :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $item->factory->tis_number ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ชื่อผลิตภัณฑ์ :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $item->factory->tis_name ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">ข้อมูลโรงงานที่ทำผลิตภัณฑ์</legend>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ชื่อโรงงาน :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $item->factory->factory_name ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ที่อยู่โรงงาน :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">
                                                        {{ $item->factory->factory_address_no ?? '' }}
                                                        {{ $item->factory->factory_moo ? 'หมู่ '.$item->factory->factory_moo : '' }}
                                                        {{ $item->factory->factory_soi ? 'ซอย '.$item->factory->factory_soi : '' }}
                                                        {{ $item->factory->factory_street ? 'ถนน '.$item->factory->factory_street : '' }}
                                                        {{ $item->factory->factory_subdistrict ? 'ต.'.$item->factory->factory_subdistrict : '' }}
                                                        {{ $item->factory->factory_district ? 'อ.'.$item->factory->factory_district : '' }}
                                                        {{ $item->factory->factory_province ? 'จ.'.$item->factory->factory_province : '' }}
                                                        {{ $item->factory->factory_zipcode ?? '' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">สถานที่จัดเก็บผลิตภัณฑ์</legend>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ชื่อสถานที่จัดเก็บ :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $item->factory->storage_name ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ที่อยู่สถานที่จัดเก็บ :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">
                                                        {{ $item->factory->storage_address_no ?? '' }}
                                                        {{ $item->factory->storage_moo ? 'หมู่ '.$item->factory->storage_moo : '' }}
                                                        {{ $item->factory->storage_soi ? 'ซอย '.$item->factory->storage_soi : '' }}
                                                        {{ $item->factory->storage_street ? 'ถนน '.$item->factory->storage_street : '' }}
                                                        {{ $item->factory->storage_subdistrict ? 'ต.'.$item->factory->storage_subdistrict : '' }}
                                                        {{ $item->factory->storage_district ? 'อ.'.$item->factory->storage_district : '' }}
                                                        {{ $item->factory->storage_province ? 'จ.'.$item->factory->storage_province : '' }}
                                                        {{ $item->factory->storage_zipcode ?? '' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">ผู้ประสานงาน</legend>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ชื่อผู้ประสานงาน :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $item->factory->coordinator ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ตำแหน่ง :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $item->factory->coordinator_position ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">โทรศัพท์ :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $item->factory->coordinator_tel ?? '-' }}</p>
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
                                <div class="panel-heading">ตรวจสอบคำขอ</div>
                                <div class="panel-wrapper collapse in" aria-expanded="true">
                                    <div class="panel-body">
                                        <div class="form-group required">
                                            <label class="col-md-3 control-label">ผลการพิจารณา :</label>
                                            <div class="col-md-7">
                                                <label>{!! Form::radio('status', '2', true, ['class'=>'check', 'data-radio'=>'iradio_square-green']) !!} รับคำขอ</label>
                                                &nbsp;&nbsp;
                                                <label>{!! Form::radio('status', '3', false, ['class'=>'check', 'data-radio'=>'iradio_square-red']) !!} ไม่รับคำขอ</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">หมายเหตุ :</label>
                                            <div class="col-md-8">
                                                {!! Form::textarea('remark', null, ['class' => 'form-control', 'rows'=>'4', 'placeholder'=>'ระบุหมายเหตุ (ถ้ามี)']) !!}
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
                            <a class="btn btn-default" href="{{ url('section5/factory-inspection') }}">
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
