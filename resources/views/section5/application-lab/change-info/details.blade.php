@extends('layouts.master')

@section('title', 'เปลี่ยนแปลงข้อมูลห้องปฏิบัติการ')

@push('css')
    <link href="{{asset('plugins/components/icheck/skins/all.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('plugins/components/toast-master/css/jquery.toast.css')}}">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">
                        เปลี่ยนแปลงข้อมูล — {{ $lab->lab_name }}
                        @if($readonly ?? false)
                            <span class="label label-default" style="font-size:12px; vertical-align:middle;">ดูรายละเอียด (อ่านอย่างเดียว)</span>
                        @endif
                    </h3>
                    <a class="btn btn-success pull-right" href="{{ url('/request-section-5/application-lab/change-info') }}">
                        <i class="icon-arrow-left-circle"></i> กลับ
                    </a>
                    <div class="clearfix"></div>
                    <hr>

                    @if ($errors->any())
                        <ul class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    {!! Form::open(['url' => '/request-section-5/application-lab/change-info/save', 'method' => 'POST', 'files' => true, 'class' => 'form-horizontal', 'id' => 'change_info_form']) !!}
                    {!! Form::hidden('lab_id', $lab->id) !!}
                    @if(!empty($existingApplication))
                        {!! Form::hidden('application_id', $existingApplication->id) !!}
                    @endif

                    {{-- ข้อมูลผู้ยื่นคำขอ --}}
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><h5>ข้อมูลผู้ยื่นคำขอ</h5></legend>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('applicant_name', 'ชื่อ - นามสกุล :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('applicant_name', $lab->name, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('applicant_taxid', 'เลขผู้เสียภาษี :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('applicant_taxid', $lab->taxid, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    {{-- ข้อมูลเดิมห้องปฏิบัติการ --}}
                    <fieldset class="scheduler-border" style="margin-top:20px;">
                        <legend class="scheduler-border"><h5>ข้อมูลเดิมห้องปฏิบัติการ</h5></legend>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('old_lab_name', 'ชื่อห้องปฏิบัติการ :', ['class' => 'col-md-2 control-label']) !!}
                                    <div class="col-md-10">
                                        {!! Form::text('old_lab_name', $lab->lab_name, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('old_lab_address', 'เลขที่ :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('old_lab_address', $lab->lab_address, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('old_lab_building', 'อาคาร/หมู่บ้าน :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('old_lab_building', $lab->lab_building, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('old_lab_soi', 'ตรอก/ซอย :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('old_lab_soi', $lab->lab_soi, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('old_lab_moo', 'หมู่ :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('old_lab_moo', $lab->lab_moo, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('old_lab_road', 'ถนน :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('old_lab_road', $lab->lab_road, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('old_lab_subdistrict', 'แขวง/ตำบล :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('old_lab_subdistrict', $lab->LabSubdistrictName, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('old_lab_district', 'เขต/อำเภอ :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('old_lab_district', $lab->LabDistrictName, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('old_lab_province', 'จังหวัด :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('old_lab_province', $lab->LabProvinceName, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('old_lab_zipcode', 'รหัสไปรษณีย์ :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('old_lab_zipcode', $lab->lab_zipcode, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('old_lab_phone', 'เบอร์โทรศัพท์ :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('old_lab_phone', $lab->lab_phone, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4"><h6>ผู้ประสานงานเดิม</h6></label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('old_co_name', 'ชื่อผู้ประสานงาน :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('old_co_name', $lab->co_name, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('old_co_position', 'ตำแหน่ง :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('old_co_position', $lab->co_position, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('old_co_email', 'อีเมล :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('old_co_email', $lab->co_email, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('old_co_mobile', 'โทรศัพท์มือถือ :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('old_co_mobile', $lab->co_mobile, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    {{-- ข้อมูลใหม่ --}}
                    @php
                        // ถ้าเปิดจากคำขอที่เคยยื่นไว้ (แก้ไข/ดูรายละเอียด) ให้โชว์ค่าที่เคยกรอกไว้ ไม่ใช่ข้อมูลปัจจุบันของ lab
                        $newVal = $existingApplication ?: $lab;
                    @endphp
                    <fieldset class="scheduler-border" style="margin-top:20px;">
                        <legend class="scheduler-border"><h5>ข้อมูลใหม่ที่ต้องการเปลี่ยนแปลง</h5></legend>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('lab_name', 'ชื่อห้องปฏิบัติการ :', ['class' => 'col-md-2 control-label']) !!}
                                    <div class="col-md-10">
                                        {!! Form::text('lab_name', $newVal->lab_name, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_address', 'เลขที่ :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_address', $newVal->lab_address, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_building', 'อาคาร/หมู่บ้าน :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_building', $newVal->lab_building, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_soi', 'ตรอก/ซอย :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_soi', $newVal->lab_soi, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_moo', 'หมู่ :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_moo', $newVal->lab_moo, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_road', 'ถนน :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_road', $newVal->lab_road, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ค้นหาที่อยู่ด้วย Select2 --}}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('lab_address_seach', 'ค้นหาที่อยู่ :', ['class' => 'col-md-2 control-label']) !!}
                                    <div class="col-md-10">
                                        <select id="lab_address_seach" class="form-control select2" style="width:100%">
                                            <option value="">- ค้นหาแขวง/ตำบล เขต/อำเภอ จังหวัด -</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_subdistrict_txt', 'แขวง/ตำบล :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_subdistrict_txt', $newVal->LabSubdistrictName, ['class' => 'form-control lab_input_show', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_district_txt', 'เขต/อำเภอ :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_district_txt', $newVal->LabDistrictName, ['class' => 'form-control lab_input_show', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_province_txt', 'จังหวัด :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_province_txt', $newVal->LabProvinceName, ['class' => 'form-control lab_input_show', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_zipcode', 'รหัสไปรษณีย์ :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_zipcode', $newVal->lab_zipcode, ['class' => 'form-control lab_input_show', 'id' => 'lab_zipcode']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_phone', 'เบอร์โทรศัพท์ :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_phone', $newVal->lab_phone, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_fax', 'เบอร์โทรสาร :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_fax', $newVal->lab_fax, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {!! Form::hidden('lab_subdistrict_id', $newVal->lab_subdistrict_id, ['class' => 'lab_input_show', 'id' => 'lab_subdistrict_id']) !!}
                        {!! Form::hidden('lab_district_id',    $newVal->lab_district_id,    ['class' => 'lab_input_show', 'id' => 'lab_district_id']) !!}
                        {!! Form::hidden('lab_province_id',    $newVal->lab_province_id,    ['class' => 'lab_input_show', 'id' => 'lab_province_id']) !!}

                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label col-md-4"><h6>ผู้ประสานงานใหม่</h6></label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('co_name', 'ชื่อผู้ประสานงาน :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('co_name', $newVal->co_name, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('co_position', 'ตำแหน่ง :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('co_position', $newVal->co_position, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('co_mobile', 'โทรศัพท์มือถือ :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('co_mobile', $newVal->co_mobile, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('co_phone', 'โทรศัพท์ :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('co_phone', $newVal->co_phone, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('co_fax', 'โทรสาร :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('co_fax', $newVal->co_fax, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('co_email', 'อีเมล :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('co_email', $newVal->co_email, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    {{-- เอกสารแนบ --}}
                    <fieldset class="scheduler-border" style="margin-top:20px;">
                        <legend class="scheduler-border"><h5>เอกสารแนบ</h5></legend>

                        <p class="text-muted">โปรดเลือกประเภทหน่วยงาน</p>

                        <div class="form-group m-b-0">
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <label>
                                {!! Form::radio('attachment_type', '1', ($existingAttachmentType ?? null) == 1, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'attachment_type_1', 'required' => true]) !!}
                                หน่วยงานของรัฐ
                            </label>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <label>
                                {!! Form::radio('attachment_type', '2', ($existingAttachmentType ?? null) == 2, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'attachment_type_2', 'required' => true]) !!}
                                หน่วยงานเอกชน
                            </label>
                        </div>

                        {{-- หน่วยงานของรัฐ --}}
                        <div id="section_gov" style="display:none; margin-top:15px;">
                            <h5>แนบไฟล์ประกอบคำขอ (หน่วยงานของรัฐ)</h5>
                            <div class="row" style="margin:15px;">
                                <div class="col-md-6">1. หนังสือขอเปลี่ยนแปลงข้อมูลจากหน่วยงานที่ลงนามโดยผู้มีอำนาจสูงสุด (ต้องมี)</div>
                                <div class="col-md-6">
                                    @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'change_info_gov_file_1'])
                                    {!! Form::file('gov_file_1', ['class' => 'form-control'] + (empty(($existingAttachFiles ?? collect())->get('change_info_gov_file_1')) ? ['data-force-required' => '1'] : [])) !!}
                                </div>
                            </div>
                            <div class="row" style="margin:15px;">
                                <div class="col-md-6">2. หนังสือคำสั่งแต่งตั้งผู้อำนวยการ (กรณีหน่วยงานภายใต้การกำกับดูแล) (ต้องมี)</div>
                                <div class="col-md-6">
                                    @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'change_info_gov_file_2'])
                                    {!! Form::file('gov_file_2', ['class' => 'form-control'] + (empty(($existingAttachFiles ?? collect())->get('change_info_gov_file_2')) ? ['data-force-required' => '1'] : [])) !!}
                                </div>
                            </div>
                            <div class="row" style="margin:15px;">
                                <div class="col-md-6">3. หนังสือมอบอำนาจ (กรณีมอบอำนาจ) (ต้องมี)</div>
                                <div class="col-md-6">
                                    @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'change_info_gov_file_3'])
                                    {!! Form::file('gov_file_3', ['class' => 'form-control'] + (empty(($existingAttachFiles ?? collect())->get('change_info_gov_file_3')) ? ['data-force-required' => '1'] : [])) !!}
                                </div>
                            </div>
                        </div>

                        {{-- หน่วยงานเอกชน --}}
                        <div id="section_pri" style="display:none; margin-top:15px;">
                            <h5>แนบไฟล์ประกอบคำขอ (หน่วยงานเอกชน)</h5>
                            <div class="row" style="margin:15px;">
                                <div class="col-md-6">1. หนังสือขอเปลี่ยนแปลงข้อมูลจากหน่วยงานที่ลงนามโดยผู้มีอำนาจสูงสุด (ต้องมี)</div>
                                <div class="col-md-6">
                                    @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'change_info_pri_file_1'])
                                    {!! Form::file('pri_file_1', ['class' => 'form-control'] + (empty(($existingAttachFiles ?? collect())->get('change_info_pri_file_1')) ? ['data-force-required' => '1'] : [])) !!}
                                </div>
                            </div>
                            <div class="row" style="margin:15px;">
                                <div class="col-md-6">2. หนังสือมอบอำนาจ พร้อมประทับตราบริษัท (ต้องมี)</div>
                                <div class="col-md-6">
                                    @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'change_info_pri_file_2'])
                                    {!! Form::file('pri_file_2', ['class' => 'form-control'] + (empty(($existingAttachFiles ?? collect())->get('change_info_pri_file_2')) ? ['data-force-required' => '1'] : [])) !!}
                                </div>
                            </div>
                            <div class="row" style="margin:15px;">
                                <div class="col-md-6">3. สำเนาบัตรประชาชน/หนังสือเดินทาง ของผู้มอบอำนาจและผู้รับมอบอำนาจ (ต้องมี)</div>
                                <div class="col-md-6">
                                    @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'change_info_pri_file_3'])
                                    {!! Form::file('pri_file_3', ['class' => 'form-control'] + (empty(($existingAttachFiles ?? collect())->get('change_info_pri_file_3')) ? ['data-force-required' => '1'] : [])) !!}
                                </div>
                            </div>
                            <div class="row" style="margin:15px;">
                                <div class="col-md-6">4. หนังสือรับรองบริษัท อายุไม่เกิน 6 เดือน (ต้องมี)</div>
                                <div class="col-md-6">
                                    @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'change_info_pri_file_4'])
                                    {!! Form::file('pri_file_4', ['class' => 'form-control'] + (empty(($existingAttachFiles ?? collect())->get('change_info_pri_file_4')) ? ['data-force-required' => '1'] : [])) !!}
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <center style="margin-top:20px;">
                        <div class="form-group">
                            <div class="col-md-offset-4 col-md-4">
                                @if(!($readonly ?? false))
                                    <button class="btn btn-success" type="submit">
                                        บันทึกคำขอ
                                    </button>
                                @endif
                                <a class="btn btn-default" href="{{ url('/request-section-5/application-lab/change-info') }}">
                                    {{ ($readonly ?? false) ? 'กลับ' : 'ยกเลิก' }}
                                </a>
                            </div>
                        </div>
                    </center>

                    {!! Form::close() !!}

                    @if(!empty($existingApplication) && $existingApplication->app_accept()->count() > 0)
                        @include('section5.application-lab.history', ['applicationlab' => $existingApplication])
                    @endif

                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function () {

        @if($readonly ?? false)
            // โหมด "ดูรายละเอียด" (จาก ApplicationLabController::show()) — ล็อกทุกช่องไม่ให้แก้ไข
            var $form = $('#change_info_form');
            $form.find('input, select, textarea').prop('disabled', true);
            $form.find('.fileinput-remove, .btn-file').hide();
            if ($('#attachment_type_1').is(':checked')) { $('#section_gov').show(); }
            else if ($('#attachment_type_2').is(':checked')) { $('#section_pri').show(); }
            return;
        @endif

        // เลือก "หน่วยงานของรัฐ" ต้องบังคับแนบเฉพาะไฟล์ฝั่งรัฐ ไม่บังคับฝั่งเอกชน (และกลับกัน) —
        // input ทั้ง 2 ฝั่งอยู่ใน DOM พร้อมกันตลอด ถ้าใส่ required ตรงๆ ตอน render ฝั่งที่ถูกซ่อนไว้
        // (display:none) ก็ยังมี attribute required ค้างอยู่ จึงต้องเปิด/ปิด required ด้วย JS ตามฝั่ง
        // ที่ active จริงเท่านั้น — [data-force-required] มาจาก server (true เฉพาะไฟล์ที่ยังไม่เคยแนบไว้)
        function applyRequiredBySection() {
            $('#section_gov input[type="file"]').prop('required', false);
            $('#section_pri input[type="file"]').prop('required', false);

            var $active = $('#section_gov').is(':visible') ? $('#section_gov')
                        : ($('#section_pri').is(':visible') ? $('#section_pri') : null);

            if ($active) {
                $active.find('input[type="file"]').each(function () {
                    if ($(this).data('force-required') == 1) {
                        $(this).prop('required', true);
                    }
                });
            }
        }

        $('#attachment_type_1').on('ifChecked', function () {
            $('#section_gov').show();
            $('#section_pri').hide();
            applyRequiredBySection();
        });

        $('#attachment_type_2').on('ifChecked', function () {
            $('#section_gov').hide();
            $('#section_pri').show();
            applyRequiredBySection();
        });

        // กรณีแก้ไขคำขอที่เคยยื่นไว้ (มี attachment_type เลือกไว้แล้วจาก server) iCheck ไม่ยิง ifChecked
        // ให้เองตอน init ต้องเช็คค่า checked จริงแล้วโชว์ section ที่ตรงกันเอง
        if ($('#attachment_type_1').is(':checked')) {
            $('#section_gov').show();
        } else if ($('#attachment_type_2').is(':checked')) {
            $('#section_pri').show();
        }
        applyRequiredBySection();

        // ค้นหาที่อยู่ใหม่
        $("#lab_address_seach").select2({
            dropdownAutoWidth: true,
            width: '100%',
            ajax: {
                url: "{{ url('/funtions/search-addreess') }}",
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) { return { results: data }; },
                cache: true
            },
            placeholder: 'ค้นหาแขวง/ตำบล เขต/อำเภอ จังหวัด',
            minimumInputLength: 1,
        });

        $("#lab_address_seach").on('change', function () {
            $.ajax({
                url: "{!! url('/funtions/get-addreess/') !!}/" + $(this).val()
            }).done(function (jsondata) {
                if (jsondata !== '') {
                    $('input[name="lab_subdistrict_txt"]').val(jsondata.subdistrict);
                    $('input[name="lab_district_txt"]').val(jsondata.district);
                    $('input[name="lab_province_txt"]').val(jsondata.province);
                    $('#lab_zipcode').val(jsondata.zipcode);
                    $('#lab_subdistrict_id').val(jsondata.subdistrict_id);
                    $('#lab_district_id').val(jsondata.district_id);
                    $('#lab_province_id').val(jsondata.province_id);
                }
            });
        });

    });
</script>
@endpush
