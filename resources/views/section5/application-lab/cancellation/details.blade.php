@extends('layouts.master')

@section('title', 'ยกเลิก มอก. ที่ได้รับการแต่งตั้ง')

@push('css')
    <link href="{{asset('plugins/components/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{asset('plugins/components/toast-master/css/jquery.toast.css')}}">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">
                        {{ $applicationLab->lab_name }}
                        @if($readonly ?? false)
                            <span class="label label-default" style="font-size:12px; vertical-align:middle;">ดูรายละเอียด (อ่านอย่างเดียว)</span>
                        @endif
                    </h3>

                    <div class="clearfix"></div>
                    <hr>

                    {!! Form::open(['url' => '/request-section-5/application-lab/cancellation/save_cancellation', 'method' => 'POST', 'files' => true, 'class' => 'form-horizontal', 'id' => 'cancellation_form']) !!}
                    {!! Form::hidden('lab_id', $applicationLab->id) !!}
                    @if(!empty($existingApplication))
                        {!! Form::hidden('application_id', $existingApplication->id) !!}
                    @endif

                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><h5>ข้อมูลผู้ยื่นคำขอ</h5></legend>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('applicant_name', 'ชื่อ - นามสกุลผู้ยื่นคำขอ'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('applicant_name', !empty($applicationLab->applicant_name) ? $applicationLab->applicant_name : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('applicant_taxid', 'เลขประจำตัวผู้เสียภาษีอากร'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('applicant_taxid', !empty($applicationLab->taxid) ? $applicationLab->taxid : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4"><h6>ข้อมูลห้องปฏิบัติการ</h6></label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('lab_name', 'ชื่อห้องปฏิบัติการ'.' :', ['class' => 'col-md-2 control-label']) !!}
                                    <div class="col-md-10">
                                        {!! Form::text('lab_name', !empty($applicationLab->lab_name) ? $applicationLab->lab_name : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_address', 'เลขที่'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_address', !empty($applicationLab->lab_address) ? $applicationLab->lab_address : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_building', 'อาคาร/หมู่บ้าน'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_building', !empty($applicationLab->lab_building) ? $applicationLab->lab_building : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_soi', 'ตรอก/ซอย'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_soi', !empty($applicationLab->lab_soi) ? $applicationLab->lab_soi : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_moo', 'หมู่'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_moo', !empty($applicationLab->lab_moo) ? $applicationLab->lab_moo : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_road', 'ถนน'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_road', !empty($applicationLab->lab_road) ? $applicationLab->lab_road : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_subdistrict_txt', 'แขวง/ตำบล'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_subdistrict_txt', !empty($applicationLab->LabSubdistrictName) ? $applicationLab->LabSubdistrictName : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_district_txt', 'เขต/อำเภอ'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_district_txt', !empty($applicationLab->LabDistrictName) ? $applicationLab->LabDistrictName : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_province_txt', 'จังหวัด'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_province_txt', !empty($applicationLab->LabProvinceName) ? $applicationLab->LabProvinceName : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_zipcode_txt', 'รหัสไปรษณีย์'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_zipcode_txt', !empty($applicationLab->lab_zipcode) ? $applicationLab->lab_zipcode : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_phone', 'เบอร์โทรศัพท์'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_phone', !empty($applicationLab->lab_phone) ? $applicationLab->lab_phone : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('lab_fax', 'เบอร์โทรสาร'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('lab_fax', !empty($applicationLab->lab_fax) ? $applicationLab->lab_fax : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4"><h6>ผู้ประสานงาน</h6></label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('co_name', 'ชื่อผู้ประสานงาน'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('co_name', !empty($applicationLab->co_name) ? $applicationLab->co_name : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('co_position', 'ตำแหน่ง'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('co_position', !empty($applicationLab->co_position) ? $applicationLab->co_position : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('co_mobile', 'โทรศัพท์มือถือ'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('co_mobile', !empty($applicationLab->co_mobile) ? $applicationLab->co_mobile : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('co_phone', 'โทรศัพท์'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('co_phone', !empty($applicationLab->co_phone) ? $applicationLab->co_phone : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('co_fax', 'โทรสาร'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('co_fax', !empty($applicationLab->co_fax) ? $applicationLab->co_fax : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('co_email', 'อีเมล'.' :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('co_email', !empty($applicationLab->co_email) ? $applicationLab->co_email : null, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </fieldset>

                    <fieldset class="scheduler-border" style="margin-top:20px;">
                        <legend class="scheduler-border"><h5>มอก. ที่ได้รับการแต่งตั้ง</h5></legend>

                        {{-- ประกาศราชกิจจาฯ --}}
                        <div class="row">
                            <div class="col-md-12">
                                <label class="col-md-2 control-label" style="padding-top:6px;">ประกาศราชกิจจาฯ :</label>
                                <div class="col-md-10">
                                    @if($gazettes->isNotEmpty())
                                        @foreach($gazettes as $gazette)
                                            <div style="border:1px solid #dce8f0; border-radius:6px; padding:10px 14px; margin-bottom:10px; background:#f7fbff;">
                                                <div style="color:#1a6fa8; font-weight:600; margin-bottom:6px;">
                                                    <i class="fa fa-book" style="margin-right:6px;"></i>{{ $gazette->FormattedLabel }}
                                                </div>
                                                @if($gazette->tis_items->isNotEmpty())
                                                    <div style="display:flex; flex-wrap:wrap; gap:6px;">
                                                        @foreach($gazette->tis_items as $scope)
                                                            <div style="background:#fff; border:1px solid #b8d4ea; border-radius:4px; padding:3px 10px; font-size:13px; display:flex; align-items:center; gap:6px;">
                                                                <span style="background:#1a6fa8; color:#fff; border-radius:3px; padding:1px 7px; font-size:12px; white-space:nowrap;">{{ $scope->tis_tisno }}</span>
                                                                <span style="color:#333;">{{ $scope->standards->tb3_TisThainame ?? '-' }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">ไม่พบข้อมูลประกาศราชกิจจา</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- ไฟล์ราชกิจจา: แสดงถ้ามีอยู่แล้ว / แสดง upload ถ้ายังไม่มี --}}
                        @php
                            $board   = $applicationLab->board_approve;
                            $gazFile = $board ? $board->attach_file_gazette : null;
                        @endphp
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('gazette_file', 'ไฟล์ราชกิจจา :', ['class' => 'col-md-2 control-label']) !!}
                                    <div class="col-md-10">
                                        @if($gazFile)
                                            @php
                                                $cleanPath = preg_replace('/\/+/', '/', $gazFile->url);
                                                $nasBase   = rtrim(env('FILESYSTEM_ROOT_URL', ''), '/');
                                                $fileUrl   = $nasBase . '/' . ltrim($cleanPath, '/');
                                                $fileExt   = HP::FileExtension($gazFile->filename) ?? 'ไฟล์';
                                            @endphp
                                            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-xs btn-info">
                                                <i class="fa fa-file-pdf-o"></i> {{ $fileExt }}
                                            </a>
                                        @else
                                            {!! Form::file('gazette_file', ['class' => 'form-control']) !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><h6>เลือก มอก. ที่ต้องการยกเลิก</h6></legend>

                            <div style="margin-bottom:8px;">
                                <label style="font-size:13px; color:#555; font-weight:normal;">
                                    <input type="checkbox" id="check_all" style="margin-right:5px;" />
                                    เลือกทั้งหมด
                                </label>
                            </div>

                            @if($gazettes->isNotEmpty())
                                @foreach($gazettes as $gazette)
                                    @if($gazette->scope_items->isNotEmpty())
                                        <div style="border:1px solid #dce8f0; border-radius:6px; margin-bottom:12px; overflow:hidden;">
                                            <div style="background:#1a6fa8; color:#fff; padding:7px 14px; font-weight:600; font-size:13px;">
                                                <i class="fa fa-book" style="margin-right:6px;"></i>{{ $gazette->FormattedLabel }}
                                            </div>
                                            <table class="table table-bordered" style="margin-bottom:0;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width:40px; background:#e8f3fb;">
                                                            <input type="checkbox" class="check_gazette" data-gazette="{{ $gazette->id }}" />
                                                        </th>
                                                        <th style="background:#e8f3fb; width:130px;">เลขมอก.</th>
                                                        <th style="background:#e8f3fb;">ชื่อ มอก.</th>
                                                        <th style="background:#e8f3fb; width:120px;">ประเภท</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($gazette->scope_items as $scope)
                                                        <tr>
                                                            <td class="text-center">
                                                                <input type="checkbox" class="checkbox_scope" name="scope_ids[]" value="{{ $scope->id }}" data-gazette="{{ $gazette->id }}" {{ in_array($scope->id, $selectedLabScopeIds ?? []) ? 'checked' : '' }} />
                                                            </td>
                                                            <td>{{ $scope->tis_tisno }}</td>
                                                            <td>{{ optional($scope->tis_standards)->tb3_TisThainame }}</td>
                                                            <td>{{ $scope->remarks }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                @endforeach
                            @endif

                            @if($ungroupedScopes->isNotEmpty())
                                <div style="border:1px solid #e0d4b8; border-radius:6px; margin-bottom:12px; overflow:hidden;">
                                    <div style="background:#7f8c8d; color:#fff; padding:7px 14px; font-weight:600; font-size:13px;">
                                        <i class="fa fa-list" style="margin-right:6px;"></i>มอก. อื่น ๆ (ไม่มีข้อมูลประกาศ)
                                    </div>
                                    <table class="table table-bordered" style="margin-bottom:0;">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width:40px; background:#f5f5f5;">
                                                    <input type="checkbox" class="check_gazette" data-gazette="ungrouped" />
                                                </th>
                                                <th style="background:#f5f5f5; width:130px;">เลขมอก.</th>
                                                <th style="background:#f5f5f5;">ชื่อ มอก.</th>
                                                <th style="background:#f5f5f5; width:120px;">ประเภท</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($ungroupedScopes as $scope)
                                                <tr>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="checkbox_scope" name="scope_ids[]" value="{{ $scope->id }}" data-gazette="ungrouped" {{ in_array($scope->id, $selectedLabScopeIds ?? []) ? 'checked' : '' }} />
                                                    </td>
                                                    <td>{{ $scope->tis_tisno }}</td>
                                                    <td>{{ optional($scope->tis_standards)->tb3_TisThainame }}</td>
                                                    <td>{{ $scope->remarks }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                        </fieldset>

                    </fieldset>

                    <fieldset class="scheduler-border" style="margin-top:20px;">
                        <legend class="scheduler-border"><h5>เอกสารแบบ</h5></legend>

                        <p class="text-muted">โปรดเลือก ประเภทหน่วยงาน</p>

                        <div class="form-group m-b-0">
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <label>
                                {!! Form::radio('attachment_type', '1', ($existingAttachmentType ?? null) == 1, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'audit_type_1', 'required' => true]) !!}
                                หน่วยงานของรัฐ
                            </label>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <label>
                                {!! Form::radio('attachment_type', '2', ($existingAttachmentType ?? null) == 2, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'audit_type_2', 'required' => true]) !!}
                                หน่วยงานเอกชน
                            </label>
                        </div>

                        {{-- หน่วยงานของรัฐ --}}
                    <div id="section_gov" style="display:none; margin-top:15px;">
                        <div class="form-group">
                            <h5>แนบไฟล์ประกอบคำขอ (หน่วยงานของรัฐ)</h5>
                        </div>
                        <div class="row" style="margin:15px;">
                            <div class="col-md-6">
                                1. หนังสือขอยกเลิกการแต่งตั้งจากหน่วยงานที่ลงนามโดยผู้มีอำนาจสูงสุด (ต้องมี) โดย "เรียน เลขาธิการสำนักงานมาตรฐานอุตสาหกรรม"
                            </div>
                            <div class="col-md-6">
                                @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'cancellation_gov_file_1'])
                                {!! Form::file('gov_file_1', ['class' => 'form-control'] + (empty(($existingAttachFiles ?? collect())->get('cancellation_gov_file_1')) ? ['required' => true] : [])) !!}
                            </div>
                        </div>
                        <div class="row" style="margin:15px;">
                            <div class="col-md-6">
                                2. หนังสือคำสั่งแต่งตั้งผู้อำนวยการศูนย์/สำนัก/อื่น ๆ จากหน่วยงานหลัก (กรณีหน่วยงานภายใต้การกำกับดูแล)
                            </div>
                            <div class="col-md-6">
                                @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'cancellation_gov_file_2'])
                                {!! Form::file('gov_file_2', ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="row" style="margin:15px;">
                            <div class="col-md-6">
                                3. หนังสือมอบอำนาจ ลงนามโดยผู้มีอำนาจ ผู้รับมอบอำนาจ และพยาน (กรณีมอบอำนาจ) หากเลือกข้อ 4 จะต้องแนบไฟล์สำเนาบัตรประชาชน (ผู้รับมอบอำนาจ)
                            </div>
                            <div class="col-md-6">
                                @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'cancellation_gov_file_3'])
                                {!! Form::file('gov_file_3', ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>

                    {{-- หน่วยงานเอกชน --}}
                    <div id="section_pri" style="display:none; margin-top:15px;">
                        <div class="form-group">
                            <h5>แนบไฟล์ประกอบคำขอ (หน่วยงานเอกชน)</h5>
                        </div>
                        <div class="row" style="margin:15px;">
                            <div class="col-md-6">
                                1. หนังสือขอยกเลิกการแต่งตั้งจากหน่วยงานที่ลงนามโดยผู้มีอำนาจสูงสุด (ต้องมี)
                            </div>
                            <div class="col-md-6">
                                @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'cancellation_pri_file_1'])
                                {!! Form::file('pri_file_1', ['class' => 'form-control'] + (empty(($existingAttachFiles ?? collect())->get('cancellation_pri_file_1')) ? ['required' => true] : [])) !!}
                            </div>
                        </div>
                        <div class="row" style="margin:15px;">
                            <div class="col-md-6">
                                2. หนังสือมอบอำนาจ ลงนามโดยผู้มีอำนาจ ผู้รับมอบอำนาจ และพยาน พร้อมประทับตราบริษัท
                            </div>
                            <div class="col-md-6">
                                @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'cancellation_pri_file_2'])
                                {!! Form::file('pri_file_2', ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="row" style="margin:15px;">
                            <div class="col-md-6">
                                3. สำเนาบัตรประชาชน/หนังสือเดินทาง ของผู้มอบอำนาจและผู้รับมอบอำนาจ (ผู้ที่ได้รับมอบอำนาจให้ใช้งานระบบ)
                            </div>
                            <div class="col-md-6">
                                @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'cancellation_pri_file_3'])
                                {!! Form::file('pri_file_3', ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="row" style="margin:15px;">
                            <div class="col-md-6">
                                4. หนังสือรับรองบริษัท อายุไม่เกิน 6 เดือน ประทับตราบริษัทและกรรมการ ลงนามทุกแผ่น
                            </div>
                            <div class="col-md-6">
                                @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'cancellation_pri_file_4'])
                                {!! Form::file('pri_file_4', ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>

                    </fieldset>

                    <center style="margin-top:20px;">
                        <div class="form-group">
                            <div class="col-md-offset-4 col-md-4">
                                @if(!($readonly ?? false))
                                    <button class="btn btn-success" type="submit" id="btn_submit">
                                        บันทึก
                                    </button>
                                @endif
                                <a class="btn btn-default" href="{{ url('/request-section-5/application-lab/cancellation') }}">
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
            var $form = $('#cancellation_form');
            $form.find('input, select, textarea').prop('disabled', true);
            $form.find('.fileinput-remove, .btn-file').hide();
            return;
        @endif

        $('#btn_submit').on('click', function (e) {
            if ($('.checkbox_scope:checked').length === 0) {
                e.preventDefault();
                alert('กรุณาเลือก มอก. ที่ต้องการยกเลิกอย่างน้อย 1 รายการ ก่อนบันทึก');
                return;
            }
            if (!$('#audit_type_1').is(':checked') && !$('#audit_type_2').is(':checked')) {
                e.preventDefault();
                alert('กรุณาเลือกประเภทหน่วยงาน (หน่วยงานของรัฐ / หน่วยงานเอกชน) ก่อนบันทึก');
            }
        });

        $('#audit_type_1').on('ifChecked', function () {
            $('#section_gov').show();
            $('#section_pri').hide();
        });

        $('#audit_type_2').on('ifChecked', function () {
            $('#section_gov').hide();
            $('#section_pri').show();
        });

        // กรณีแก้ไขคำขอยกเลิกที่เคยยื่นไว้ (มี attachment_type ที่เลือกไว้แล้วจาก server)
        // iCheck ไม่ยิง ifChecked ให้เองตอน init ต้องเช็คค่า checked จริงแล้วโชว์ section ที่ตรงกันเอง
        if ($('#audit_type_1').is(':checked')) {
            $('#section_gov').show();
        } else if ($('#audit_type_2').is(':checked')) {
            $('#section_pri').show();
        }

        // เลือกทั้งหมด
        $('#check_all').on('change', function () {
            $('.checkbox_scope').prop('checked', this.checked);
            $('.check_gazette').prop('checked', this.checked);
        });

        // เลือกทั้งกลุ่ม gazette
        $(document).on('change', '.check_gazette', function () {
            var gazetteId = $(this).data('gazette');
            $('.checkbox_scope[data-gazette="' + gazetteId + '"]').prop('checked', this.checked);
        });

        // sync check_all เมื่อ checkbox เปลี่ยน
        $(document).on('change', '.checkbox_scope', function () {
            var gazetteId = $(this).data('gazette');
            var allInGroup = $('.checkbox_scope[data-gazette="' + gazetteId + '"]');
            $('.check_gazette[data-gazette="' + gazetteId + '"]').prop('checked', allInGroup.length === allInGroup.filter(':checked').length);
            $('#check_all').prop('checked', $('.checkbox_scope').length === $('.checkbox_scope:checked').length);
        });
    });
</script>
@endpush
