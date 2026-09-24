@push('css')
    <link href="{{asset('plugins/components/icheck/skins/all.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/components/bootstrap-datepicker-thai/css/datepicker.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('plugins/components/switchery/dist/switchery.min.css')}}" rel="stylesheet" />
    <link href="{{asset('plugins/components/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>

    <style type="text/css">

        .text-left{
            text-align: left !important;
        }

        .table td.text-ellipsis {
            max-width: 177px;
        }
        .table td.text-ellipsis a {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            max-width: 90%;
        }

        .scope-loading-text {
            color: #777;
            display: none;
            margin-top: 6px;
        }

    </style>
@endpush

@php

    if( !isset($applicationInspector->id)  ){

        //ข้อมูลผู้ยื่นคำขอ
        $user_login = auth()->user();

        $user_assignor = HP::AuthUserSSO(!empty($applicationInspector->created_by) ? $applicationInspector->created_by : (is_null($user_login->ActInstead) ? $user_login->getKey() : $user_login->ActInstead->getKey()) );

        $applicationInspector = new stdClass;
        $applicationInspector->applicant_prefix = !empty($user_assignor->prefix_text)?$user_assignor->prefix_text:null;
        $applicationInspector->applicant_first_name = !empty($user_assignor->person_first_name)?$user_assignor->person_first_name:null;
        $applicationInspector->applicant_last_name = !empty($user_assignor->person_last_name)?$user_assignor->person_last_name:null;
        $applicationInspector->applicant_full_name = !empty($user_assignor->name)?$user_assignor->prefix_text.$user_assignor->person_first_name." ".$user_assignor->person_last_name:null;
        $applicationInspector->applicant_taxid = !empty($user_assignor->tax_number)?$user_assignor->tax_number:null;
        $applicationInspector->applicant_date_of_birth = !empty($user_assignor->date_of_birth)?$user_assignor->date_of_birth:null;

        if(  $user_assignor->applicanttype_id != 2 ){
            $applicationInspector->applicant_date_niti =  !empty( $user_assignor->date_niti )?$user_assignor->date_niti:null;
        }else{
            $applicationInspector->applicant_date_niti =  !empty( $user_assignor->date_of_birth )?$user_assignor->date_of_birth:null;
        }

        $applicationInspector->applicant_email = !empty($user_assignor->email)?$user_assignor->email:null;
        $applicationInspector->applicant_phone = !empty($user_assignor->contact_tel)?$user_assignor->contact_tel:null;
        $applicationInspector->applicant_mobile = !empty($user_assignor->contact_phone_number)?$user_assignor->contact_phone_number:null;
        $applicationInspector->applicant_fax = !empty($user_assignor->fax)?$user_assignor->fax:null;

    }

@endphp

<div class="form-body">

    <div class="row">
        <div class="text-center">
            <h3 style="color: black">คำขอรับการขึ้นทะเบียน</h3>
            <h3 style="color: black">ผู้ตรวจ และผู้ประเมินของผู้ตรวจสอบการทำผลิตภัณฑ์อุตสาหกรรม</h3>
            <h3 style="color: black">ตามมาตรา 5 แห่งพระราชบัญญัติมาตรฐานผลิตภัณฑ์อุตสาหกรรม พ.ศ.2511 และที่แก้ไขเพิ่มเติม</h3>
        </div>
    </div>

    @if(  isset($applicationInspector->id)  )
        <div class="clearfix" style="margin-top:25px"></div>

        <div class="row">
            <div class="col-lg-12 col-sm-12" style="font-size: 16px;">
                <div class="col-md-2 col-md-offset-8 text-right" >เลขที่คำขอ :</div>
                <div class="col-md-2 div_dotted">
                    <p>{!! !empty( $applicationInspector->application_no )?$applicationInspector->application_no:null !!}</p>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="row">
            <div class="col-lg-12 col-sm-12" style="font-size: 16px;">
                <div class="col-md-2 col-md-offset-8 text-right">วันที่ยื่นคำขอ :</div>
                <div class="col-md-2 div_dotted">
                    <p>{!! !empty($applicationInspector->application_date)?HP::DateThaiFull($applicationInspector->application_date):null !!}</p>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="row">
            <div class="col-lg-12 col-sm-12" style="font-size: 16px;">
                <div class="col-md-2 col-md-offset-8 text-right">วันที่รับคำขอ :</div>
                <div class="col-md-2 div_dotted">
                    <p>{!! !empty($applicationInspector->accept_date) ? HP::DateThaiFull($applicationInspector->accept_date) : '-' !!}</p>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="row">
            <div class="col-lg-12 col-sm-12" style="font-size: 16px;">
                <div class="col-md-2 col-md-offset-8 text-right">ผู้รับคำขอ :</div>
                <div class="col-md-2 div_dotted">
                    <p>{!! !empty($applicationInspector->accept_by) && !is_null($applicationInspector->accepter) ? $applicationInspector->accepter->FullName : '-' !!}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="clearfix" style="margin-top:10px"></div>
    <br>
    <fieldset class="white-box">
       <h5 style="color: orange; margin-bottom: 25px">1. ข้อมูลผู้ยื่นคำขอ</h5>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('applicant_full_name', 'ชื่อ - สกุลผู้ยื่นคำขอ', ['class' => 'col-md-4 control-label text-left']) !!}
                    <div class="col-md-12">
                        {!! Form::hidden('applicant_prefix', !empty( $applicationInspector->applicant_prefix )?$applicationInspector->applicant_prefix:null) !!}
                        {!! Form::hidden('applicant_first_name', !empty( $applicationInspector->applicant_first_name )?$applicationInspector->applicant_first_name:null) !!}
                        {!! Form::hidden('applicant_last_name', !empty( $applicationInspector->applicant_last_name )?$applicationInspector->applicant_last_name:null) !!}
                        {!! Form::text('applicant_full_name', !empty( $applicationInspector->applicant_full_name )?$applicationInspector->applicant_full_name:null,['class' => 'form-control input_show', 'readonly' => true ]) !!}
                        {!! $errors->first('applicant_full_name', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('applicant_taxid', 'เลขประจำตัวประชาชน', ['class' => 'col-md-4 control-label text-left']) !!}
                    <div class="col-md-12">
                        {!! Form::text('applicant_taxid', !empty( $applicationInspector->applicant_taxid )?$applicationInspector->applicant_taxid:null, ['class' => 'form-control input_show', 'readonly' => true ]) !!}
                        {!! $errors->first('applicant_taxid', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('applicant_date_of_birth', 'วัน/เดือน/ปี เกิด', ['class' => 'col-md-4 control-label text-left']) !!}
                    <div class="col-md-12">
                        <div class="input-group">
                            {!! Form::text('applicant_date_of_birth', !empty( $applicationInspector->applicant_date_of_birth )?HP::revertDate($applicationInspector->applicant_date_of_birth):null, ['class' => 'form-control input_show', 'readonly' => true ]) !!}
                            {!! $errors->first('applicant_date_of_birth', '<p class="help-block">:message</p>') !!}
                            <span class="input-group-addon"><i class="icon-calender"></i></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group{{ $errors->has('applicant_position') ? 'has-error' : ''}}">
                    {!! Form::label('applicant_position', 'ตำแหน่ง', ['class' => 'col-md-4 control-label text-left']) !!}
                    <div class="col-md-12">
                        {!! Form::text('applicant_position', !empty( $applicationInspector->applicant_position )?$applicationInspector->applicant_position:null, ['class' => 'form-control input_show']) !!}
                        {!! $errors->first('applicant_position', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group{{ $errors->has('applicant_phone') ? 'has-error' : ''}}">
                    {!! Form::label('applicant_phone', 'โทรศัพท์', ['class' => 'col-md-4 control-label text-left']) !!}
                    <div class="col-md-12">
                        {!! Form::text('applicant_phone', !empty( $applicationInspector->applicant_phone )?$applicationInspector->applicant_phone:null, ['class' => 'form-control input_show']) !!}
                        {!! $errors->first('applicant_phone', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group{{ $errors->has('applicant_fax') ? 'has-error' : ''}}">
                    {!! Form::label('applicant_fax', 'แฟกซ์', ['class' => 'col-md-4 control-label text-left']) !!}
                    <div class="col-md-12">
                        {!! Form::text('applicant_fax', !empty( $applicationInspector->applicant_fax )?$applicationInspector->applicant_fax:null, ['class' => 'form-control input_show']) !!}
                        {!! $errors->first('applicant_fax', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group required{{ $errors->has('applicant_mobile') ? 'has-error' : ''}}">
                    {!! Form::label('applicant_mobile', 'มือถือ', ['class' => 'col-md-4 control-label text-left']) !!}
                    <div class="col-md-12">
                        {!! Form::text('applicant_mobile', !empty( $applicationInspector->applicant_mobile )?$applicationInspector->applicant_mobile:null, ['class' => 'form-control input_show', 'required' => true]) !!}
                        {!! $errors->first('applicant_mobile', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group required{{ $errors->has('applicant_email') ? 'has-error' : ''}}">
                    {!! Form::label('applicant_email', 'E-mail', ['class' => 'col-md-4 control-label text-left']) !!}
                    <div class="col-md-12">
                        {!! Form::text('applicant_email', !empty( $applicationInspector->applicant_email )?$applicationInspector->applicant_email:null, ['class' => 'form-control input_show', 'required' => true]) !!}
                        {!! $errors->first('applicant_email', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">

            </div>
        </div>

        <div class="box_agency">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4 text-left"><h5>หน่วยงาน</h5></label>
                        <div class="col-md-9">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="form-group">
                            {!! Html::decode(Form::label('agency_search', 'ค้นหาหน่วยงาน'.' <span style="font-style: italic; color: gray">(กรอกชื่อหน่วยงาน 10 ตัวอักษรขึ้นไป หรือกรอกเลขประจำตัวผู้เสียภาษีอากรเพื่อค้นหา)</span>', ['class' => 'col-md-12 control-label text-left'])) !!}
                            <div class="col-md-12">
                                {!! Form::text('agency_search', null,['class' => 'form-control', 'id' => 'agency_search']) !!}
                                <p class="scope-loading-text" id="agency_search_loading_text">กำลังค้นหาข้อมูลหน่วยงาน ...</p>
                                {!! $errors->first('agency_search', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required{{ $errors->has('agency_name') ? 'has-error' : ''}}">
                        {!! Form::label('agency_name', 'ชื่อหน่วยงาน', ['class' => 'col-md-2 control-label text-left']) !!}  
                        <div class="col-md-12">
                            {!! Form::text('agency_name', !empty( $applicationInspector->agency_name )?$applicationInspector->agency_name:null,['class' => 'form-control', 'readonly' => true  , 'required' => true]) !!}
                            {!! $errors->first('agency_name', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    {{-- <div class="form-group">
                        {!! Form::label('asdfasf', '&nbsp;', ['class' => 'col-md-4 control-label text-left']) !!}
                        <div class="col-md-12">
                            <button type="button" class="btn btn-info waves-effect waves-light" id="btn_search">ค้นหา</button>
                        </div>
                    </div> --}}
                </div>

                <div class="col-md-4">
                    <div class="form-group required{{ $errors->has('agency_taxid') ? 'has-error' : ''}}">
                        {!! Form::label('agency_taxid', 'เลขประจำตัวผู้เสียภาษีอากร', ['class' => 'col-md-5 control-label text-left']) !!}
                        <div class="col-md-12">
                            {!! Form::text('agency_taxid', !empty( $applicationInspector->agency_taxid )?$applicationInspector->agency_taxid:null,['class' => 'form-control', 'readonly' => true , 'required' => true ]) !!}
                            {!! $errors->first('agency_taxid', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group required{{ $errors->has('agency_address') ? 'has-error' : ''}}">
                        {!! Form::label('agency_address', 'ที่ตั้งเลขที่', ['class' => 'col-md-4 control-label text-left']) !!}
                        <div class="col-md-12">
                            {!! Form::text('agency_address', !empty( $applicationInspector->agency_address )?$applicationInspector->agency_address:null,['class' => 'form-control', 'readonly' => true , 'required' => true ]) !!}
                            {!! $errors->first('agency_address', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group {{ $errors->has('agency_moo') ? 'has-error' : ''}}">
                        {!! Form::label('agency_moo', 'หมู่ที่', ['class' => 'col-md-4 control-label text-left']) !!}
                        <div class="col-md-12">
                            {!! Form::text('agency_moo', !empty( $applicationInspector->agency_moo )?$applicationInspector->agency_moo:null,  ['class' => 'form-control', 'readonly' => true ]) !!}
                            {!! $errors->first('agency_moo', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group {{ $errors->has('agency_soi') ? 'has-error' : ''}}">
                        {!! Form::label('agency_soi', 'ตรอก/ซอย', ['class' => 'col-md-4 control-label text-left']) !!}
                        <div class="col-md-12">
                            {!! Form::text('agency_soi', !empty( $applicationInspector->agency_soi )?$applicationInspector->agency_soi:null,  ['class' => 'form-control', 'readonly' => true ]) !!}
                            {!! $errors->first('agency_soi', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group {{ $errors->has('agency_road') ? 'has-error' : ''}}">
                        {!! Form::label('agency_road', 'ถนน', ['class' => 'col-md-4 control-label text-left']) !!}
                        <div class="col-md-12">
                            {!! Form::text('agency_road', !empty( $applicationInspector->agency_road )?$applicationInspector->agency_road:null, ['class' => 'form-control agency_input_show', 'readonly' => true ]) !!}
                            {!! $errors->first('agency_road', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required{{ $errors->has('agency_subdistrict_txt') ? 'has-error' : ''}}">
                        {!! Form::label('agency_subdistrict_txt', 'ตำบล/แขวง', ['class' => 'col-md-4 control-label text-left']) !!}
                        <div class="col-md-12">
                            {!! Form::text('agency_subdistrict_txt', !empty( $applicationInspector->AgencySubdistrictName )?$applicationInspector->AgencySubdistrictName:null, ['class' => 'form-control agency_input_show', 'readonly' => true, 'required' => true  ]) !!}
                            {!! $errors->first('agency_subdistrict_txt', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required{{ $errors->has('agency_district_txt') ? 'has-error' : ''}}">
                        {!! Form::label('agency_district_txt', 'อำเภอ/เขต', ['class' => 'col-md-4 control-label text-left']) !!}
                        <div class="col-md-12">
                            {!! Form::text('agency_district_txt', !empty( $applicationInspector->AgencyDistrictName )?$applicationInspector->AgencyDistrictName:null,['class' => 'form-control agency_input_show', 'readonly' => true, 'required' => true ]) !!}
                            {!! $errors->first('agency_district_txt', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group required{{ $errors->has('agency_province_txt') ? 'has-error' : ''}}">
                        {!! Form::label('agency_province_txt', 'จังหวัด', ['class' => 'col-md-4 control-label text-left']) !!}
                        <div class="col-md-12">
                            {!! Form::text('agency_province_txt', !empty( $applicationInspector->AgencyProvinceName )?$applicationInspector->AgencyProvinceName:null, ['class' => 'form-control agency_input_show', 'readonly' => true , 'required' => true ]) !!}
                            {!! $errors->first('agency_province_txt', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group required{{ $errors->has('agency_zipcode_txt') ? 'has-error' : ''}}">
                        {!! Form::label('agency_zipcode_txt', 'รหัสไปรษณีย์', ['class' => 'col-md-4 control-label text-left']) !!}
                        <div class="col-md-12">
                            {!! Form::text('agency_zipcode_txt', !empty( $applicationInspector->agency_zipcode )?$applicationInspector->agency_zipcode:null,['class' => 'form-control agency_input_show', 'readonly' => true , 'required' => true ]) !!}
                            {!! $errors->first('agency_zipcode_txt', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">

                </div>
            </div>

            <div class="row">
                {!! Form::hidden('agency_id', !empty( $applicationInspector->agency_id )?$applicationInspector->agency_id:null, [ 'class' => 'agency_input_show', 'id' => 'agency_id' ] ) !!}
                {!! Form::hidden('agency_subdistrict', !empty( $applicationInspector->agency_subdistrict )?$applicationInspector->agency_subdistrict:null, [ 'class' => 'agency_input_show', 'id' => 'agency_subdistrict' ] ) !!}
                {!! Form::hidden('agency_district', !empty( $applicationInspector->agency_district )?$applicationInspector->agency_district:null, [ 'class' => 'agency_input_show', 'id' => 'agency_district' ] ) !!}
                {!! Form::hidden('agency_province', !empty( $applicationInspector->agency_province )?$applicationInspector->agency_province:null, [ 'class' => 'agency_input_show', 'id' => 'agency_province' ] ) !!}
                {!! Form::hidden('agency_zipcode', !empty( $applicationInspector->agency_zipcode )?$applicationInspector->agency_zipcode:null, [ 'class' => 'agency_input_show', 'id' => 'agency_zipcode' ] ) !!}
            </div>
        </div>

    </fieldset>

    <fieldset class="white-box box_branch">
        <h5 style="color: orange; margin-bottom: 25px">2. ข้อมูลขอรับบริการ</h5>

        <p style="text-indent:45px">ยื่นคำขอต่อสำนักงานมาตรฐานผลิตภัณฑ์อุตสาหกรรม กระทรวงอุตสาหกรรม เพื่อรับการขึ้นทะเบียนเป็นผู้ตรวจ และ ผู้ประเมินของผู้สอบการทำผลิตภัณฑ์อุตสาหกรรม<br>
ตามมาตรา 5 แห่งพระราชบัญญัติมาตรฐานผลิตภัณฑ์อุตสาหกรรม พ.ศ. 2511 และที่แก้ไขเพิ่มเติม ในหมวดอุตสาหกรรม ต่อไปนี้</p>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group required{{ $errors->has('branch_group') ? 'has-error' : ''}}">
                    {!! Form::label('branch_group', 'หมวดอุตสากรรม :', ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::select('branch_group', $branchgroups = App\Models\Basic\BranchGroup::where('state', 1)->pluck('title', 'id')->all(), null,['class' => 'form-control branch_group', 'placeholder' => '- เลือกหมวดอุตสากรรม -', 'id' => 'branch_group']) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group required m-b-10 {{ $errors->has('input_branch') ? 'has-error' : ''}}">
                    {!! Form::label('input_branch', 'รายสาขา :', ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::select('input_branch[]', [], null, ['class' => 'select2-multiple', 'multiple'=>'multiple', 'id'=>'input_branch', 'data-placeholder'=>'- เลือกรายสาขา -']) !!}
                        <p class="scope-loading-text" id="input_branch_loading_text">กำลังโหลดข้อมูลรายสาขา ...</p>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group p-t-10">
                            {!! Form::checkbox('check_all_branch', '1', null, ['class' => 'form-control check', 'data-checkbox' => 'icheckbox_flat-blue', 'id'=>'check_all_branch','required' => false]) !!}
                            <label for="check_all_branch" class="font-medium-1">&nbsp;&nbsp; All</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group required">
                    {!! Form::label('scope_branches_tis', 'เลขที่ มอก. :', ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::select('scope_branches_tis[]', [] , null, ['class' => 'select2-multiple scope_branches_tis', 'id' => 'scope_branches_tis',  'data-placeholder'=>'- เลือกมาตรฐาน -', 'multiple' => 'multiple', 'disabled' => false]); !!}
                        <p class="scope-loading-text" id="scope_branches_tis_loading_text">กำลังโหลดข้อมูลเลขที่ มอก. ...</p>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group" style="padding-top: 10px;">
                            {!! Form::checkbox('check_all_scope_branches_tis', '1', null, ['class' => 'form-control check', 'data-checkbox' => 'icheckbox_flat-blue', 'id'=>'check_all_scope_branches_tis','required' => false]) !!}
                            <label for="check_all_scope_branches_tis" class="font-medium-1">&nbsp;&nbsp; All</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

                <div class="text-center">
                    <button type="button" class="btn btn-success waves-effect waves-light text-center" id="btn_add" style="margin-bottom: 10px;">เพิ่ม</button>
                    <button type="button" class="btn btn-default waves-effect waves-light text-center" id="btn_clear" style="margin-bottom: 10px;">ล้างข้อมูล</button>
                </div>

                <div class="table-responsive">
                    <table class="table-bordered table table-hover primary-table" id="table-branch">
                        <thead>
                            <tr>
                                <th width="7%" class="text-center">รายการที่</th>
                                <th width="23%" class="text-center">สาขา</th>
                                <th width="33%" class="text-center">รายสาขา</th>
                                <th width="30%">มาตรฐาน มอก. เลขที่</th>
                                <th width="5%" class="text-center">ลบ</th>
                            </tr>
                        </thead>
                        <tbody data-repeater-list="repeater-branch" id="box_list_branch">
                            @php
                                $i = 1;
                            @endphp
                            @if(!empty($branch_scopes) && count($branch_scopes) > 0)
                                @foreach($branch_scopes as $key => $scope_groups)

                                    @php
                                        $branch_group_ids = collect($scope_groups)->pluck('branch_group_id');
                                        $inspector_scope_ids = App\Models\Section5\Section5ApplicationInspectorsScope::whereIn('branch_group_id', $branch_group_ids)->where('application_id', $applicationInspector->id)->select('id');
                                        $scopes_ties = App\Models\Section5\Section5ApplicationInspectorsScopeTis::whereIn('inspector_scope_id', $inspector_scope_ids)->get();
                                    @endphp

                                    <tr>
                                        <td class="text-center branch_no">{{ ($i++).'.' }}</td>
                                        <td>
                                            {{ array_key_exists($key, $branch_groups)?$branch_groups[$key]:null }}
                                            {!! Form::hidden('branch_group_id', $key, [ 'class' => 'branch_group_id', 'data-name' => 'branch_group_id'] ) !!}
                                        </td>
                                        <td>
                                            @php
                                                $arr = [];
                                            @endphp
                                            @foreach($scope_groups as $k=>$scope)
                                                @php
                                                    $arr[] =  $scope['branch_title'];
                                                @endphp
                                                {!! Form::hidden('branch_id', !empty( $scope['branch_id'] )?$scope['branch_id']:null, [ 'class' => 'branch_id', 'data-name' => 'branch_id'] ) !!}
                                                {!! Form::hidden('old_id', !empty( $scope['id'])?$scope['id']:null, [ 'class' => 'old_id', 'data-name' => 'old_id'] ) !!}
                                            @endforeach
                                                {{ implode(', ', $arr) }}
                                        </td>
                                        <td class="text-ellipsis">
                                            <a class="open_scope_branches_tis_details" href="javascript:void(0)" title="คลิกดูรายละเอียด">{{ !empty($scopes_ties)?implode(', ', $scopes_ties->pluck('tis_no')->toArray()):'-' }}</a>
                                            @foreach($scopes_ties as $scopes_tis)
                                                @php
                                                    $branch_title = !empty($scopes_tis->application_inspector_scope->basic_branches->title) ? $scopes_tis->application_inspector_scope->basic_branches->title : '' ;
                                                @endphp
                                                <input type="hidden" value="{!! $scopes_tis->tis_name !!}" data-tis_no="{!! $scopes_tis->tis_no !!}" data-branch_title="{!! $branch_title !!}" class="tis_details">
                                                <input type="hidden" name="tis_id" value="{!! $scopes_tis->tis_id !!}" class="input_array" data-name="tis_id">
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger waves-effect waves-light btn_remove">X</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                </div>

            </div>
        </div>

    </fieldset>

    <fieldset class="white-box">

        <h5 style="color: orange; margin-bottom: 25px">3. หลักฐานประกอบการพิจารณา</h5>

        <div class="repeater-form">
            <div data-repeater-list="evidences">
                @if(!empty($applicationInspector->id))

                    @if(!empty($app_configs_evidences) && count(@$app_configs_evidences) > 0)
                        @foreach($app_configs_evidences as $key=>$app_configs_evidence)

                            <div class="row" data-repeater-item>
                                <div class="col-md-12">
                                    <div class="form-group @if($app_configs_evidence->required == 1) required @endif">
                                        {!! HTML::decode(Form::label('evidence_file_config', (!empty($app_configs_evidence->title)?$app_configs_evidence->title:null), ['class' => 'col-md-6 control-label text-left'])) !!}

                                        @php
                                            $attachment_educational = App\AttachFile::where('ref_table', (new App\Models\Section5\Section5ApplicationInspector )->getTable() )
                                                                                    ->where('tax_number', $applicationInspector->applicant_taxid)
                                                                                    ->where('ref_id', $applicationInspector->id )
                                                                                    ->when($app_configs_evidence->id, function ($query, $setting_file_id){
                                                                                        return $query->where('setting_file_id', $setting_file_id);
                                                                                    })->first();
                                        @endphp

                                        @if( !empty($attachment_educational) )
                                            <div class="col-md-4" >
                                                {{-- <a href="{{url('funtions/get-view/'.$attachment_educational->url.'/'.( !empty($attachment_educational->filename) ? $attachment_educational->filename :  basename($attachment_educational->url)  ))}}" target="_blank" title="{!! !empty($attachment_educational->filename) ? $attachment_educational->filename : 'ไฟล์แนบ' !!}"> --}}
                                                <a href="{!! HP::getFileStorage($attachment_educational->url) !!}" target="_blank" title="{!! !empty($attachment_educational->filename) ? $attachment_educational->filename : 'ไฟล์แนบ' !!}">
                                                    <i class="fa fa-folder-open fa-lg" style="color:#FFC000;" aria-hidden="true"></i>
                                                    <span>{{ ( !empty($attachment_educational->filename) ? $attachment_educational->filename : ' ') }}</span>
                                                </a>
                                            </div>
                                            <div class="col-md-2" >
                                                <a class="btn btn-danger btn-xs show_tag_a" href="{!! url('funtions/get-delete/files/'.($attachment_educational->id).'/'.base64_encode('request_section5/application_inspectors/'.$applicationInspector->id.'/edit') ) !!}" title="ลบไฟล์"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                            </div>
                                        @else
                                            <div class="col-md-4">
                                                {!! Form::hidden('setting_title' ,(!empty($app_configs_evidence->title)?$app_configs_evidence->title:null), ['required' => false]) !!}
                                                {!! Form::hidden('setting_id' ,(!empty($app_configs_evidence->id)?$app_configs_evidence->id:null), ['required' => false]) !!}
                                                {{-- {!! Form::hidden('bs_attachment_type_id' ,(!empty($app_configs_evidence->bs_attachment_type_id)?$app_configs_evidence->bs_attachment_type_id:null), ['required' => false]) !!} --}}
                                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                                    <div class="form-control" data-trigger="fileinput">
                                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                                        <span class="fileinput-filename"></span>
                                                    </div>
                                                    <span class="input-group-addon btn btn-default btn-file">
                                                        <span class="fileinput-new">เลือกไฟล์</span>
                                                        <span class="fileinput-exists">เปลี่ยน</span>
                                                        <input type="file" name="evidence_file_config" class="evidence_file_config" @if($app_configs_evidence->required == 1) required @endif data-accept="{{ base64_encode($app_configs_evidence->file_properties) }}" data-max-size="{{ base64_encode($app_configs_evidence->bytes) }}">
                                                    </span>
                                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>

                        @endforeach
                    @else

                        @if(!empty($configs_evidences) && count(@$configs_evidences) > 0)
                            @foreach($configs_evidences as $key=>$configs_evidence)

                                <div class="row" data-repeater-item>
                                    <div class="col-md-12">
                                        <div class="form-group @if($configs_evidence->required == 1) required @endif">
                                            {!! HTML::decode(Form::label('evidence_file_config', (!empty($configs_evidence->title)?$configs_evidence->title:null), ['class' => 'col-md-6 control-label text-left'])) !!}
                                            <div class="col-md-4">
                                                {!! Form::hidden('setting_title' ,(!empty($configs_evidence->title)?$configs_evidence->title:null), ['required' => false]) !!}
                                                {!! Form::hidden('setting_id' ,(!empty($configs_evidence->id)?$configs_evidence->id:null), ['required' => false]) !!}
                                                {{-- {!! Form::hidden('bs_attachment_type_id' ,(!empty($configs_evidence->bs_attachment_type_id)?$configs_evidence->bs_attachment_type_id:null), ['required' => false]) !!} --}}
                                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                                    <div class="form-control" data-trigger="fileinput">
                                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                                        <span class="fileinput-filename"></span>
                                                    </div>
                                                    <span class="input-group-addon btn btn-default btn-file">
                                                        <span class="fileinput-new">เลือกไฟล์</span>
                                                        <span class="fileinput-exists">เปลี่ยน</span>
                                                        <input type="file" name="evidence_file_config" class="evidence_file_config" @if($configs_evidence->required == 1) required @endif data-accept="{{ base64_encode($configs_evidence->file_properties) }}" data-max-size="{{ base64_encode($configs_evidence->bytes) }}">
                                                    </span>
                                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                        @endif

                    @endif
                @else

                    @if(!empty($configs_evidences) && count(@$configs_evidences) > 0)
                        @foreach($configs_evidences as $key=>$configs_evidence)

                            <div class="row" data-repeater-item>
                                <div class="col-md-12">
                                    <div class="form-group @if($configs_evidence->required == 1) required @endif">
                                        {!! HTML::decode(Form::label('evidence_file_config', (!empty($configs_evidence->title)?$configs_evidence->title:null), ['class' => 'col-md-6 control-label text-left'])) !!}
                                        <div class="col-md-4">
                                            {!! Form::hidden('setting_title' ,(!empty($configs_evidence->title)?$configs_evidence->title:null), ['required' => false]) !!}
                                            {!! Form::hidden('setting_id' ,(!empty($configs_evidence->id)?$configs_evidence->id:null), ['required' => false]) !!}
                                            {{-- {!! Form::hidden('bs_attachment_type_id' ,(!empty($configs_evidence->bs_attachment_type_id)?$configs_evidence->bs_attachment_type_id:null), ['required' => false]) !!} --}}
                                            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                                <div class="form-control" data-trigger="fileinput">
                                                    <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                                    <span class="fileinput-filename"></span>
                                                </div>
                                                <span class="input-group-addon btn btn-default btn-file">
                                                    <span class="fileinput-new">เลือกไฟล์</span>
                                                    <span class="fileinput-exists">เปลี่ยน</span>
                                                    <input type="file" name="evidence_file_config" class="evidence_file_config" @if($configs_evidence->required == 1) required @endif data-accept="{{ base64_encode($configs_evidence->file_properties) }}" data-max-size="{{ base64_encode($configs_evidence->bytes) }}">
                                                </span>
                                                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endforeach
                    @endif

                @endif
            </div>
        </div>

        <div class="row repeater-form" id="div_attach">
            <div class="col-md-12" data-repeater-list="repeater-file">


                @php
                    $file_other = [];
                    if( isset($applicationInspector->id) ){
                        $file_other = App\AttachFile::where('section', 'evidence_file_other')->where('ref_table', (new App\Models\Section5\Section5ApplicationInspector )->getTable() )->where('ref_id', $applicationInspector->id )->get();
                    }
                @endphp



                @foreach ( $file_other as $attach )

                    <div class="form-group">
                        {!! HTML::decode(Form::label('personfile', 'เอกสารเพิ่มเติม', ['class' => 'col-md-2 control-label text-left'])) !!}
                        <div class="col-md-4">
                            {!! Form::text('file_documents', ( !empty($attach->caption) ? $attach->caption:null) , ['class' => 'form-control' , 'placeholder' => 'คำอธิบาย', 'disabled' => true]) !!}
                        </div>
                        <div class="col-md-4">
                            <a href="{!! HP::getFileStorage($attach->url) !!}" target="_blank" title="{!! !empty($attach->filename) ? $attach->filename : 'ไฟล์แนบ' !!}">
                                <i class="fa fa-folder-open fa-lg" style="color:#FFC000;" aria-hidden="true"></i>
                                <span>{{ ( !empty($attach->filename) ? $attach->filename : ' ') }}</span>
                            </a>
                        </div>
                        <div class="col-md-1" >
                            <a class="btn btn-danger btn-xs show_tag_a " href="{!! url('funtions/get-delete/files/'.($attach->id).'/'.base64_encode('request_section5/application_inspectors/'.$applicationInspector->id.'/edit') ) !!}" title="ลบไฟล์"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                        </div>
                    </div>

                @endforeach

                <div class="form-group input_show_file" data-repeater-item>
                    {!! HTML::decode(Form::label('personfile', 'เอกสารเพิ่มเติม', ['class' => 'col-md-2 control-label text-left'])) !!}
                    <div class="col-md-4">
                        {!! Form::text('file_documents', null , ['class' => 'form-control' , 'placeholder' => 'กรอกหมายเหตุ']) !!}
                    </div>
                    <div class="col-md-4">
                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                            <div class="form-control" data-trigger="fileinput">
                                <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                <span class="fileinput-filename"></span>
                            </div>
                            <span class="input-group-addon btn btn-default btn-file">
                                <span class="fileinput-new">เลือกไฟล์</span>
                                <span class="fileinput-exists">เปลี่ยน</span>
                                <input type="file" name="evidence_file_other" id="evidence_file_other">
                            </span>
                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-danger btn_file_remove" data-repeater-delete type="button">
                            ลบ
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-success pull-right" data-repeater-create><i class="icon-plus"></i> เพิ่ม</button>
                    </div>
                </div>
            </div>
        </div>

    </fieldset>

</div>

@if( isset( $applicationInspector->edited ) && $applicationInspector->edited == true || isset( $applicationInspector->show ) && $applicationInspector->show == true )
@if(!empty($application_inspectors_accepts) && count($application_inspectors_accepts) > 0)
<div class="row">
    <div class="col-lg-12 col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <span style="font-size:20px">ประวัติการตรวจสอบคำขอ</span>
                <div class="pull-right">
                    <a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a>
                </div>
            </div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body" id="box-request">
                    <div class="table-responsive">
                        <table class="table-bordered table table-hover primary-table" id="table-history">
                            <thead>
                                <tr>
                                    <th width="7%" class="text-center">ลำดับที่</th>
                                    <th width="27%" class="text-center">วันที่</th>
                                    <th class="text-center">สถานะ</th>
                                    <th class="text-center">รายละเอียด</th>
                                    <th width="10%" class="text-center">ผู้ดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody data-repeater-list="repeater-history" id="box_list_history">
                                @foreach($application_inspectors_accepts as $key=>$inspectors_accept)
                                <tr>
                                    <td class="text-center">{{ ($key+1).'.' }}</td>
                                    <td>
                                        {{ HP::DateTimeThaiTormat_1($inspectors_accept->created_at) }}
                                    </td>
                                    <td>
                                        {{ $inspectors_accept->AppStatus }}
                                    </td>
                                    <td>
                                        {{ $inspectors_accept->description }}
                                    </td>
                                    <td class="text-center">
                                        {{ $inspectors_accept->RequestRecipient }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endif

@include ('section5.application-ib-cb.modals.modal-scope-branches-tis-details')

{!! Form::hidden('type_save', null , ['id' => 'type_save' ]) !!}

<center>
    <div class="form-group">
        <div class="col-md-offset-4 col-md-4">

            @if( !isset( $applicationInspector->id ) || empty($applicationInspector->application_status) || in_array($applicationInspector->application_status, [12]) )
                <button class="btn btn-info show_tag_a" type="button" id="btn_draft">
                    <i class="fa fa-file-o"></i> ฉบับร่าง
                </button>
            @endif

            @if(isset($applicationInspector) && !empty($applicationInspector->id) && $applicationInspector->application_status!=12)
                {!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'บันทึก', ['class' => 'btn btn-primary show_tag_a']) !!}
            @else
                <button class="btn btn-primary show_tag_a" type="button" id="btn_submit">
                    <i class="fa fa-paper-plane"></i> บันทึก
                </button>
            @endif

            <a class="btn btn-default show_tag_a" href="{{ url('/request_section5/application_inspectors') }}">
                ยกเลิก
            </a>

        </div>
    </div>
</center>


@push('js')
    <script src="{{ asset('plugins/components/bootstrap-typeahead/bootstrap3-typeahead.min.js') }}"></script>
    <script src="{{asset('plugins/components/icheck/icheck.min.js')}}"></script>
    <script src="{{asset('plugins/components/icheck/icheck.init.js')}}"></script>

    <!-- input calendar thai -->
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker.js') }}"></script>
    <!-- thai extension -->
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker-thai.js') }}"></script>
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/locales/bootstrap-datepicker.th.js') }}"></script>
    <script src="{{asset('plugins/components/switchery/dist/switchery.min.js')}}"></script>
    <script src="{{asset('js/jasny-bootstrap.js')}}"></script>
    {{-- <script src="{{asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')}}"></script> --}}
    <script src="{{asset('plugins/components/repeater/jquery.repeater.min.js')}}"></script>

    <script src="{{asset('plugins/components/datatables/jquery.dataTables.min.js')}}"></script>

    <script>

        $(document).ready(function () {

            var group_branchs = $.parseJSON('{!! json_encode(App\Models\Basic\Branch::select("id", "title", "branch_group_id")->where("state", 1)->get()->keyBy("id")->groupBy("branch_group_id")->toArray()) !!}');

            $('.repeater-form').repeater({
                show: function () {
                    $(this).slideDown();
                    resetOrderNoFile();
                },
                hide: function (deleteElement) {
                    if (confirm('คุณต้องการลบแถวนี้ ?')) {
                        $(this).slideUp(deleteElement);

                        setTimeout(function(){
                            resetOrderNoFile();
                        }, 400);
                    }
                }
            });

            $('#btn_clear').click(function (e) {

                $('#branch_group').val('');
                $('#branch_group').trigger('change');
                $('#input_branch').val('');
                $('#input_branch').trigger('change');

            });

            $('#btn_add').click(function (e) {

                let branch_group_id = $('#branch_group').val();
                let branch = $('#input_branch').val();
                let branch_text = $('#input_branch option:selected').toArray().map(item => item.text)
                let rows = $('#box_list_branch').children();
                let branchgroups = $.parseJSON('{!! json_encode($branchgroups,JSON_UNESCAPED_UNICODE) !!}');

                let tis_show = '';
                let tis_details = '';
                let tis_no_input  = '';

                if(!!branch_group && !!branch){

                    let branch_show = '';
                    let branch_input = '';
                    let branch_old_id = '';
                    if(branch.length>0){
                        let branch_arr = [];
                        $.each(branch, function(index,item){

                            branch_arr.push(branch_text[index]);
                            branch_input += `<input class="branch_id" name="branch_id" type="hidden" value="${item}" data-name="branch_id">`;
                            branch_old_id += `<input class="old_id" data-name="old_id" name="old_id" type="hidden" value="">`;
                        });

                        if(branch_arr.length > 0){
                            branch_show = branch_arr.join(', ');
                        }

                        //ข้อมูลมอก.
                        let tis_arr = [];
                        let tis_detail_arr = [];
                        $( "#scope_branches_tis option:selected" ).each(function( index, data ) {
                            let tis_cut = $(data).text().split(':');
                            tis_detail_arr.push( $(data).text() );
                            tis_arr.push( checkNone(tis_cut[0])?tis_cut[0]:'' );
                            tis_details  += '<input type="hidden" data-tis_no="'+( checkNone(tis_cut[0])?tis_cut[0]:'' )+'" value="'+(checkNone(tis_cut[1])?tis_cut[1]:'')+'" data-branch_title="'+$(data).data('branch')+'" class="tis_details">';
                            tis_no_input += '<input type="hidden" name="tis_id" value="'+$(data).val()+'" class="input_array" data-name="tis_id">';
                        });
                        tis_show = `<a class="open_scope_branches_tis_details" data-detail="${tis_detail_arr}" href="javascript:void(0)" title="คลิกดูรายละเอียด">${tis_arr.join(', ')}</a>`;
                    }


                    $('#box_list_branch').append(
                        `
                        <tr data-repeater-item>
                            <td class="text-center branch_no">${rows.length+1}</td>
                            <td>
                                ${branchgroups[branch_group_id]}
                                <input class="branch_group_id" name="branch_group_id" type="hidden" value="${branch_group_id}" data-name="branch_group_id">

                            </td>
                            <td>
                                ${branch_show}
                                ${branch_input}
                                ${branch_old_id}
                            </td>
                            <td class="text-ellipsis">
                                ${tis_show}
                                ${tis_details}
                                ${tis_no_input}
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger waves-effect waves-light btn_remove">X</button>
                            </td>
                        </tr>
                        `
                    );
                    reset_name();
                    reset_value();
                    resetOrderNo();
                    // show_or_hide_remove();
                }else{
                    alert('กรุณากรอกข้อมูลให้ครบ!!');
                }
            });

            reset_name();
            resetOrderNo();

            $(document).on('click', '.btn_remove', function (e) {
                if (confirm('คุณต้องการลบแถวนี้ ?')) {
                    $(this).closest('tr').remove();
                    reset_name();
                    resetOrderNo();
                }
            });

            $('#btn_submit').click(function (e) {
                $('#type_save').val('save');
                $('#from_box').submit();
            });

            $('#btn_draft').click(function (e) {
                $('#type_save').val('draft');
                
                var applicant_taxid = $('#applicant_taxid').val();
                
                if( $('#applicant_type_1').is(':checked',true) ){
                    $('.box_lab').find('input, select, hidden, checkbox').prop('required', false);
                }
                
                $('.box_agency,.box_branch').find('input, select, hidden, checkbox').prop('required', false);
                
                $('.repeater-form').find('input, select, hidden, checkbox, input[type="file"]').prop('required', false);
                
                if( applicant_taxid == '' ){
                    Swal.fire({
                        type: 'error',
                        title: 'ไม่สามารถบันทึกได้ เนื่องจากไม่มีเลขประจำตัวผู้เสียภาษีอากร',
                        // html: '<p class="h4"></p>',
                        width: 500
                    });
                }else{
                    $('#from_box').submit();
                }

            });


            $(".js-switch").each(function() {
                new Switchery($(this)[0], { size: 'small' });
             });

            $('.datepicker').datepicker({
                autoclose: true,
                toggleActive: true,
                todayHighlight: true,
                language:'th-th',
                format: 'dd/mm/yyyy'
            });

            $("#agency_address_seach").select2({
                dropdownAutoWidth: true,
                width: '100%',
                ajax: {
                    url: "{{ url('/funtions/search-addreess') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params // search term
                        };
                    },
                    results: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true,
                },
                placeholder: 'คำค้นหา',
                minimumInputLength: 1,
            });

            $('.evidence_file_config').change(function (e) {

                var result = true;

                if(!!$(this).val()){
                    var filesize = this.files[0].size;
                    var fileName = $(this).val();
                    var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
                    if(filesize > atob($(this).data('max-size'))){
                        alert('ไฟล์ขนาดต้องไม่เกิน '+formatBytes(atob($(this).data('max-size'))));
                        $(this).val('');
                        $(this).next(".custom-file-label").html('Choose file')

                        result = false;
                    }else if(!!$(this).data('accept') && (JSON.parse(atob($(this).data('accept'))).filter((a) => a)).length > 0){
                        if ($.inArray(ext, JSON.parse(atob($(this).data('accept')))) === -1) {//ถ้าเป็นประเภทไฟล์ที่กำหนด และขนาดไม่เกิน 2MB
                            alert('นามสกุลไฟล์แนบต้องเป็น '+JSON.parse(atob($(this).data('accept'))).map((number) => '.'+number).join(', ')+' เท่านั้น');
                            $(this).val('');
                            $(this).next(".custom-file-label").html('Choose file')
                            result = false;
                        }
                    }
                }

                return result;

            });

            $('#agency_search').typeahead({
                minLength: 10,
                source:  function (query, process) {
                    setAgencySearchLoadingState(true);
                    return $.get('{{ url("request_section5/application_inspectors/search-users") }}', { query: query }, function (data) {
                        setAgencySearchLoadingState(false);
                        return process(data);
                    }).fail(function() {
                        setAgencySearchLoadingState(false);
                    });
                },
                autoSelect: true,
                afterSelect: function (jsondata) {
                var agency_data = $('ul.typeahead > li.active > a').text();
                var agency_name = agency_data.split(" | ");

                    $('#agency_name').val(agency_name[0]);
                    $('#agency_taxid').val(jsondata.taxid);
                    $('#agency_address').val(jsondata.address_no);
                    $('#agency_building').val(jsondata.building);
                    $('#agency_soi').val(jsondata.soi);
                    $('#agency_moo').val(jsondata.moo);
                    $('#agency_road').val(jsondata.street);
                    $('#agency_subdistrict_txt').val(jsondata.agency_subdistrict_title);
                    $('#agency_district_txt').val(jsondata.agency_district_title);
                    $('#agency_province_txt').val(jsondata.agency_province_title);
                    $('#agency_zipcode_txt').val(jsondata.zipcode);
                    $('#agency_id').val(jsondata.id);
                    $('#agency_subdistrict').val(jsondata.agency_subdistrict_id);
                    $('#agency_district').val(jsondata.agency_district_id);
                    $('#agency_province').val(jsondata.agency_province_id);
                    $('#agency_zipcode').val(jsondata.zipcode);
                    $('#agency_search').val('');
                    
                }
            });

            resetOrderNoFile();


            $('#branch_group').click(function (e) {
                $('#input_branch').html('');
                $('#input_branch').val('').change();
                if(!!$(this).val()){
                    let branchs = group_branchs[$(this).val()];
                    if(!!branchs && branchs.length > 0){
                        $.each(branchs, function(index, branch){
                            $('#input_branch').append(`<option value="${branch.id}">${branch.title}</option>`);
                        });
                    }
                }
            });

            //ติ๊กเลือกรายสาขาทั้งหมด
            $('#check_all_branch').on('ifChecked', function (event){
                $("#input_branch > option").prop("selected", "selected");
                $('#input_branch').trigger("change");
            });

            //ไม่ติ๊กเลือกรายสาขาทั้งหมด
            $('#check_all_branch').on('ifUnchecked', function (event){
                $('#input_branch').val('').trigger("change");
            });

            //เมื่อเลือกรายสาขา
            $('#input_branch').on('change', function (e) {
                var branch_length = $(this).find('option').length;
                var branch_length_selected = $(this).find('option:selected').length;
                if(branch_length != 0){
                    if(branch_length == branch_length_selected){
                        $('#check_all_branch').iCheck('check');
                    }else{
                        $('#check_all_branch').iCheck('uncheck');
                    }
                }

                LoadBrancheTis();
            });

            //ติ๊กเลือกมอก.ทั้งหมด
            $('#check_all_scope_branches_tis').on('ifChecked', function (event){
                $("#scope_branches_tis > option").prop("selected", "selected");
                $('#scope_branches_tis').trigger("change");
            });

            //ไม่ติ๊กเลือกมอก.ทั้งหมด
            $('#check_all_scope_branches_tis').on('ifUnchecked', function (event){
                $('#scope_branches_tis').val('').trigger("change");
            });

            //คลิกลิงค์มาตรฐานมอก.
            $(document).on('click', '.open_scope_branches_tis_details', function(){

                $("#table_scope_branches_tis_details").DataTable().clear().destroy();

                open_scope_branches_tis_details($(this));

                $('#table_scope_branches_tis_details').DataTable({
                    searching: true,
                    autoWidth: false,
                    columnDefs: [
                        { className: "text-center col-md-1", targets: 0 },
                        { className: "col-md-9", targets: 1 },
                        { className: "col-md-2", targets: 2 },
                        { width: "10%", targets: 0 }
                    ]
                });

                $('#maodal_scope_branches_tis_details').modal('show');

            });

        });

        function LoadBrancheTis(){

            //รายสาขา
            var branch_ids = $("#input_branch").val();

            //เลขที่ มอก.
            var select = $('#scope_branches_tis');
            $(select).html('');
            $(select).val('').trigger('change');

            $('#check_all_scope_branches_tis').iCheck('uncheck');//เคลียร์เลือกทั้งหมด

            if(checkNone(branch_ids)){
                setScopeBranchesTisLoadingState(true);
                $.ajax({
                    url: "{!! url('/request-section-5/application-ibcb/get-branche-tis') !!}" + "/" + branch_ids
                }).done(function( object ) {

                    if( checkNone(object) ){
                        $.each(object, function( index, data ) {
                            $(select).append('<option value="'+data.id+'" data-branch="'+data.branch_title+'">'+data.title+'</option>');
                        });
                    }
                    setScopeBranchesTisLoadingState(false);

                }).fail(function() {
                    setScopeBranchesTisLoadingState(false);
                });
            } else {
                setScopeBranchesTisLoadingState(false);
            }

        }

        function setScopeBranchesTisLoadingState(isLoading)
        {
            var select = $('#scope_branches_tis');
            var loadingText = $('#scope_branches_tis_loading_text');

            select.prop('disabled', isLoading);
            $('#btn_add').prop('disabled', isLoading);

            if (isLoading) {
                loadingText.show();
            } else {
                loadingText.hide();
            }
        }

        function setAgencySearchLoadingState(isLoading)
        {
            var input = $('#agency_search');
            var loadingText = $('#agency_search_loading_text');

            input.prop('disabled', isLoading);

            if (isLoading) {
                loadingText.show();
            } else {
                loadingText.hide();
            }
        }

        function resetOrderNoFile(){

            if($('.btn_file_remove').length > 1){
                $('.btn_file_remove').show();
            }else{
                $('.btn_file_remove').hide();
            }

        }

        // convert size in bytes to KB, MB, GB
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';

            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            const i = Math.floor(Math.log(bytes) / Math.log(k));

            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        // convert file size to mb only
        function bytesToMegaBytes(bytes) {
            return bytes / (1024*1024);
        }


        function checkNone(value) {
            return value !== '' && value !== null && value !== undefined;
        }

        function resetOrderNo(){
            $('.branch_no').each(function(index, el) {
                $(el).text(index+1);
            });
        }

        function show_or_hide_remove(){
            if($('.branch_no').length > 1){
                $('.btn_remove').show();
            }else{
                $('.btn_remove').hide();
            }
        }

        function reset_name(){
            let rows = $('#box_list_branch').find('tr');
            let group_name = $('#box_list_branch').data('repeater-list');
            rows.each(function(index1, row){
                $(row).find('input, select').each(function(index2, el){

                    let old_name = $(el).data('name');

                    if(!!old_name){//มีการกำหนด attribute data-name
                        if($(el).hasClass('branch_id') || $(el).hasClass('old_id')){
                            $(el).attr('name', group_name+'['+index1+']['+old_name+'][]');
                        }else{
                            $(el).attr('name', group_name+'['+index1+']['+old_name+']');
                        }

                        //คอลัมภ์ มาตรฐาน มอก. เลขที่
                        if($(el).hasClass('input_array')){
                            $(el).attr('name', group_name+'['+index1+']['+old_name+'][]');
                        }
                    }
                });
            });
        }

        function reset_value(){
            $('#branch_group').val('').change();
            $('#input_branch').val('').change();
        }

        function open_scope_branches_tis_details(link_click) {
            let scope_branches_tis = link_click.closest('td').find('input.tis_details');
            $('#scope_branches_tis_details').html('');
            let rows = '';
            scope_branches_tis.each(function(index, item){
                rows += `
                    <tr>
                        <td class="text-center">${index+1}</td>
                        <td class="">${$(item).data('tis_no')} : ${$(item).val()}</td>
                        <td class="">${$(item).data('branch_title')}</td>
                    </tr>
                `;
            });
            $('#scope_branches_tis_details').append(rows);
        }

    </script>
@endpush
