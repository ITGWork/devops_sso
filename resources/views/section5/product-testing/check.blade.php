@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">ตรวจสอบคำขอทดสอบผลิตภัณฑ์</h3>
                    <div class="pull-right">
                        <a href="{{ url('section5/product-testing?type=elicense') }}" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> กลับ</a>
                    </div>
                    <div class="clearfix"></div>
                    <hr>

                    {{ Form::open(['url' => 'section5/product-testing/approve-save', 'class' => 'form-horizontal', 'method' => 'POST']) }}
                    <input type="hidden" name="id" value="{{ $item->id }}">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    รายละเอียดคำขอ
                                    <span class="pull-right">เลขที่คำขอ : {{ $product->refno ?? '-' }}</span>
                                </div>
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
                                                <label class="col-md-3 control-label">เลขผู้เสียภาษี :</label>
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
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">เลขที่ มอก. :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $product->tis_number ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ชื่อผลิตภัณฑ์ :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $product->tis_name ?? '-' }}</p>
                                                </div>
                                            </div>

                                        
                                            <legend class="scheduler-border">รายละเอียดผลิตภัณฑ์อุตสาหกรรม</legend>
                                            @forelse(($product_details ?? collect()) as $detail)
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">รายละเอียดผลิตภัณฑ์อุตสาหกรรม :</label>
                                                    <div class="col-md-8">
                                                        <p class="form-control-static">{{ $detail->product_detail ?? '-' }}</p>
                                                        @php
                                                            $detail_items = $labdetail_items[$detail->id] ?? collect();
                                                        @endphp
                                                        @if($detail_items->isNotEmpty())
                                                            <label class="m-b-0">รายการทดสอบ :</label>
                                                            <ul class="m-b-0">
                                                                @foreach($detail_items as $ti)
                                                                    <li>{{ $ti->no ? 'ข้อ '.$ti->no.' ' : '' }}{{ $ti->title }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="form-group">
                                                    <div class="col-md-8 col-md-offset-3">
                                                        <p class="form-control-static text-muted">-</p>
                                                    </div>
                                                </div>
                                            @endforelse
                                        </fieldset>

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">ข้อมูลโรงงานที่ทำผลิตภัณฑ์</legend>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ชื่อโรงงาน :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $product->factory_name ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ที่อยู่โรงงาน :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">
                                                        {{ $product->factory_address_no ?? '' }}
                                                        {{ $product->factory_moo ? 'หมู่ '.$product->factory_moo : '' }}
                                                        {{ $product->factory_soi ? 'ซอย '.$product->factory_soi : '' }}
                                                        {{ $product->factory_street ? 'ถนน '.$product->factory_street : '' }}
                                                        {{ $product->factory_subdistrict ? 'ต.'.$product->factory_subdistrict : '' }}
                                                        {{ $product->factory_district ? 'อ.'.$product->factory_district : '' }}
                                                        {{ $product->factory_province ? 'จ.'.$product->factory_province : '' }}
                                                        {{ $product->factory_zipcode ?? '' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="scheduler-border">
                                            <legend class="scheduler-border">ผู้ประสานงาน</legend>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ชื่อผู้ประสานงาน :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $product->coordinator ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">ตำแหน่ง :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $product->coordinator_position ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-3 control-label">โทรศัพท์ :</label>
                                                <div class="col-md-8">
                                                    <p class="form-control-static">{{ $product->coordinator_tel ?? '-' }}</p>
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
                                <div class="panel-heading"> ตรวจสอบคำขอ </div>
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
                            <a class="btn btn-default" href="{{ url('section5/product-testing?type=elicense') }}">
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
