@push('css')
    <link href="{{asset('plugins/components/icheck/skins/all.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/components/bootstrap-datepicker-thai/css/datepicker.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('plugins/components/switchery/dist/switchery.min.css')}}" rel="stylesheet" />
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('plugins/components/toast-master/css/jquery.toast.css')}}">

    <style>
        #box_scope_request { margin-top: 10px; }
        #box_scope_request .panel { box-shadow: 0 1px 4px rgba(0,0,0,.1); }
        .field-required-error { border: 2px solid #dd4b39 !important; box-shadow: 0 0 4px rgba(221,75,58,.6) !important; background-color: #fff6f6; }
    </style>
@endpush

@php
    $option_lab = [];

    if(!isset($applicationlab->id)){//สร้างคำขอ

        $user_login = auth()->user();

        $user_regitor = HP::AuthUserSSO(!empty($applicationlab->created_by) ? $applicationlab->created_by: (is_null($user_login->ActInstead) ? $user_login->getKey() : $user_login->ActInstead->getKey()) );

        $address = HP::GetIDAddress( $user_regitor->subdistrict, $user_regitor->district, $user_regitor->province );

        $address_co = HP::GetIDAddress( $user_regitor->contact_subdistrict, $user_regitor->contact_district, $user_regitor->contact_province );

        $applicationlab = new stdClass;

        $applicationlab->applicant_name =  !empty( $user_regitor->name )?$user_regitor->name:null;
        $applicationlab->applicant_taxid = !empty( $user_regitor->tax_number )?$user_regitor->tax_number:null;

        if(  $user_regitor->applicanttype_id != 2 ){
            $applicationlab->applicant_date_niti =  !empty( $user_regitor->date_niti )?$user_regitor->date_niti:null;
        }else{
            $applicationlab->applicant_date_niti =  !empty( $user_regitor->date_of_birth )?$user_regitor->date_of_birth:null;
        }

        $applicationlab->hq_address =  !empty( $user_regitor->address_no )?$user_regitor->address_no:null;
        $applicationlab->hq_moo =  !empty( $user_regitor->moo )?$user_regitor->moo:null;
        $applicationlab->hq_soi =  !empty( $user_regitor->soi )?$user_regitor->soi:null;
        $applicationlab->hq_road =  !empty( $user_regitor->street )?$user_regitor->street:null;

        $applicationlab->hq_building =  !empty( $user_regitor->building )?$user_regitor->building:null;

        $applicationlab->hq_province_id =  !empty( $address->province_id )?$address->province_id:null;
        $applicationlab->hq_district_id =  !empty( $address->district_id )?$address->district_id:null;
        $applicationlab->hq_subdistrict_id =  !empty( $address->subdistrict_id )?$address->subdistrict_id:null;
        $applicationlab->hq_zipcode =  !empty( $address->zipcode )?$address->zipcode:(!empty( $user_regitor->zipcode )?$user_regitor->zipcode:null);

        if( !is_null($address->province_id) ){
            $applicationlab->HQProvinceName =  !empty( $user_regitor->province )?$user_regitor->province:null;
            $applicationlab->HQDistrictName =  !empty( $user_regitor->district )?str_replace('เขต','',$user_regitor->district):null;
            $applicationlab->HQSubdistrictName =  !empty( $user_regitor->subdistrict )?$user_regitor->subdistrict:null;
            $applicationlab->HQPostcodeName =  !empty( $user_regitor->zipcode )?$user_regitor->zipcode:null;
        }

    }else{

        $user_login = auth()->user();

        $user_regitor = HP::AuthUserSSO(!empty($applicationlab->created_by) ? $applicationlab->created_by: (is_null($user_login->ActInstead) ? $user_login->getKey() : $user_login->ActInstead->getKey()) );

    }
    //
    $option_lab = App\Models\Section5\Labs::where('taxid', $applicationlab->applicant_taxid )->select(DB::raw("CONCAT_WS(' : ', lab_code, lab_name) AS lab_title"), 'id')->pluck('lab_title', 'id');
        
@endphp

<div class="row">
    <div class="col-md-12">
        {{-- <div class="form-group {{ $errors->has('applicant_type') ? 'has-error' : ''}}">
            {!! Form::label('applicant_type', 'ประเภทคำขอ', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-5">
                <label>{!! Form::radio('applicant_type', '1', true, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'applicant_type_1']) !!} ขอขึ้นทะเบียนใหม่ <em>(กรณียังไม่เคยขึ้นทะเบียนห้องปฏิบัติการในระบบ)</em></label>
                <label>{!! Form::radio('applicant_type', '2', false, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'applicant_type_2']) !!} ขอเพิ่มเติมขอบข่าย <em>(กรณีขึ้นทะเบียนห้องปฏิบัติการแล้ว มีการขอ มอก. เพิ่มเติม)</em></label>
                {!! $errors->first('applicant_type', '<p class="help-block">:message</p>') !!}
            </div>
            <div class="col-md-5 box_lab">
                {!! Form::select('lab_id', $option_lab, !empty( $applicationlab->lab_id )?$applicationlab->lab_id:null,  ['class' => 'form-control select_lab_id', "placeholder" => '- เลือกห้องปฏิบัติการ -', 'id' => 'lab_id' , 'required' => true ]) !!}
            </div>
        </div>
         --}}
        <div class="form-group {{ $errors->has('application_type') ? 'has-error' : '' }}">
            {!! Form::label('applicant_type', 'ประเภทคำขอ', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-10">
                {{-- applicant_type ส่งค่า 1 (ขอขึ้นทะเบียนใหม่) เสมอ --}}
                <input type="hidden" name="applicant_type" value="1">
                {{-- radio แสดง UI ให้เลือก แต่ใช้ name ต่างหาก ไม่ override applicant_type --}}
                &nbsp;
                <label>{!! Form::radio('applicant_type_display', '3', true, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'applicant_type_3']) !!} ใบรับรอง 17025</label>
                &nbsp;
                <label>{!! Form::radio('applicant_type_display', '4', false, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'applicant_type_4']) !!} ภาคผนวก ก.</label>
                <a href="{{ asset('downloads/manual/หลักเกณฑ์แต่งตั้งผู้ตรวจสอบ_LAB.pdf') }}" data-toggle="tooltip" title="คลิ๊กเพื่อดาวน์โหลด หลักเกณฑ์แต่งตั้งผู้ตรวจสอบ LAB" class="m-l-5 btn btn-info btn-xs" target="_blank">
                    <i class="fa fa-info fa-lg"></i>
                </a>
                {{-- hidden radio id applicant_type_1 สำหรับ BoxLab() JS --}}
                <input type="radio" id="applicant_type_1" name="_applicant_type_ui" checked style="display:none">
            </div>
        </div>
    </div>
</div>

<fieldset class="scheduler-border">
    <legend class="scheduler-border"><h5>ข้อมูลผู้ยื่นคำขอ</h5></legend>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group required{{ $errors->has('applicant_name') ? 'has-error' : ''}}">
                {!! Form::label('applicant_name', 'ชื่อ - นามสกุลผู้ยื่นคำขอ'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('applicant_name', !empty( $applicationlab->applicant_name )?$applicationlab->applicant_name:null,['class' => 'form-control input_show', 'required' => true, 'readonly' => true ]) !!}
                    {!! $errors->first('applicant_name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group required{{ $errors->has('applicant_taxid') ? 'has-error' : ''}}">
                {!! Form::label('applicant_taxid', 'เลขประจำตัวผู้เสียภาษีอากร'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('applicant_taxid', !empty( $applicationlab->applicant_taxid )?$applicationlab->applicant_taxid:null,  ['class' => 'form-control input_show', 'required' => true, 'readonly' => true ]) !!}
                    {!! $errors->first('applicant_taxid', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group required{{ $errors->has('applicant_date_niti') ? 'has-error' : ''}}">
                {!! Form::label('applicant_date_niti', 'วันเกิด/วันที่จดทะเบียนนิติบุคคล'.' :', ['class' => 'col-md-4 control-label text-left']) !!}
                <div class="col-md-8">
                    <div class="input-group">
                        {!! Form::text('applicant_date_niti_show', !empty( $applicationlab->applicant_date_niti )?HP::revertDate($applicationlab->applicant_date_niti):null,  ['class' => 'form-control input_show', 'required' => true, 'readonly' => true ]) !!}
                        {!! $errors->first('applicant_date_niti', '<p class="help-block">:message</p>') !!}
                        <span class="input-group-addon"><i class="icon-calender"></i></span>
                        {!! Form::hidden('applicant_date_niti', !empty( $applicationlab->applicant_date_niti )?$applicationlab->applicant_date_niti:null, [ 'class' => 'form-control', 'id' => 'applicant_date_niti' ] ) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4"><h6>ที่อยู่สำนักงานใหญ่</h6></label>
                <div class="col-md-9">
                </div>
            </div>
        </div>
    </div>

    <div class="box_hp_address">

        <div class="row">
            <div class="col-md-6">
                <div class="form-group{{ $errors->has('hq_address') ? 'has-error' : ''}}">
                    {!! Form::label('hq_address', 'เลขที่'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('hq_address', !empty( $applicationlab->hq_address )?$applicationlab->hq_address:null,['class' => 'form-control', 'readonly' => true ]) !!}
                        {!! $errors->first('hq_address', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('hq_building') ? 'has-error' : ''}}">
                    {!! Form::label('hq_building', 'อาคาร/หมู่บ้าน'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('hq_building', !empty( $applicationlab->hq_building )?$applicationlab->hq_building:null,  ['class' => 'form-control', 'readonly' => true ]) !!}
                        {!! $errors->first('hq_building', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('hq_soi') ? 'has-error' : ''}}">
                    {!! Form::label('hq_soi', 'ตรอก/ซอย'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('hq_soi', !empty( $applicationlab->hq_soi )?$applicationlab->hq_soi:null,  ['class' => 'form-control', 'readonly' => true ]) !!}
                        {!! $errors->first('hq_soi', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('hq_moo') ? 'has-error' : ''}}">
                    {!! Form::label('hq_moo', 'หมู่'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('hq_moo', !empty( $applicationlab->hq_moo )?$applicationlab->hq_moo:null,['class' => 'form-control', 'readonly' => true ]) !!}
                        {!! $errors->first('hq_moo', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('hq_road') ? 'has-error' : ''}}">
                    {!! Form::label('hq_road', 'ถนน'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('hq_road', !empty( $applicationlab->hq_road )?$applicationlab->hq_road:null,['class' => 'form-control', 'readonly' => true ]) !!}
                        {!! $errors->first('hq_road', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('hq_subdistrict_txt') ? 'has-error' : ''}}">
                    {!! Form::label('hq_subdistrict_txt', 'แขวง/ตำบล'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('hq_subdistrict_txt', !empty( $applicationlab->HQSubdistrictName )?$applicationlab->HQSubdistrictName:null,  ['class' => 'form-control hq_input_show', 'disabled' => true ]) !!}
                        {!! $errors->first('hq_subdistrict_txt', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('hq_district_txt') ? 'has-error' : ''}}">
                    {!! Form::label('hq_district_txt', 'เขต/อำเภอ'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('hq_district_txt', !empty( $applicationlab->HQDistrictName )?$applicationlab->HQDistrictName:null,['class' => 'form-control hq_input_show', 'disabled' => true ]) !!}
                        {!! $errors->first('hq_district_txt', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('hq_province_txt') ? 'has-error' : ''}}">
                    {!! Form::label('hq_province_txt', 'จังหวัด'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('hq_province_txt', !empty( $applicationlab->HQProvinceName )?$applicationlab->HQProvinceName:null,  ['class' => 'form-control hq_input_show', 'disabled' => true ]) !!}
                        {!! $errors->first('hq_province_txt', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('hq_zipcode_txt') ? 'has-error' : ''}}">
                    {!! Form::label('hq_zipcode_txt', 'รหัสไปรษณีย์'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('hq_zipcode_txt', !empty( $applicationlab->HQPostcodeName )?$applicationlab->HQPostcodeName:null,['class' => 'form-control hq_input_show', 'disabled' => true ]) !!}
                        {!! $errors->first('hq_zipcode_txt', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {!! Form::hidden('hq_subdistrict_id', !empty( $applicationlab->hq_subdistrict_id )?$applicationlab->hq_subdistrict_id:null, [ 'class' => 'hq_input_show', 'id' => 'hq_subdistrict_id' ] ) !!}
            {!! Form::hidden('hq_district_id', !empty( $applicationlab->hq_district_id )?$applicationlab->hq_district_id:null, [ 'class' => 'hq_input_show', 'id' => 'hq_district_id' ] ) !!}
            {!! Form::hidden('hq_province_id', !empty( $applicationlab->hq_province_id )?$applicationlab->hq_province_id:null, [ 'class' => 'hq_input_show', 'id' => 'hq_province_id' ] ) !!}
            {!! Form::hidden('hq_zipcode', !empty( $applicationlab->hq_zipcode )?$applicationlab->hq_zipcode:null, [ 'class' => 'hq_input_show', 'id' => 'hq_zipcode' ] ) !!}
        </div>
        <hr>

        <div class="box_renew_data">
            @include('section5.application-lab.form.renew-lab')
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-md-4"><h6>ข้อมูลห้องปฏิบัติการ</h6></label>
                    <div class="col-md-9">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-10">
                <div class="form-group">
                    <div class="col-md-4">
                        {!! Form::radio('use_address_office', '1',null, ['class' => 'form-control check', 'data-radio' => 'iradio_flat-blue', 'id'=>'use_address_office-1']) !!}
                        {!! Form::label('use_address_office-1', 'ที่อยู่เดียวกับที่อยู่สำนักงานใหญ่', ['class' => 'control-label font-medium-1 text-capitalize']) !!}
                    </div>
                    <div class="col-md-4">
                        {!! Form::radio('use_address_office', '2',null, ['class' => 'form-control check', 'data-radio' => 'iradio_flat-blue', 'id'=>'use_address_office-2']) !!}
                        {!! Form::label('use_address_office-2', 'ที่อยู่เดียวกับที่อยู่ติดต่อได้', ['class' => 'control-label font-medium-1 text-capitalize']) !!}
                    </div>
                    <div class="col-md-4">
                        {!! Form::radio('use_address_office', '3',null, ['class' => 'form-control check', 'data-radio' => 'iradio_flat-blue', 'id'=>'use_address_office-3']) !!}
                        {!! Form::label('use_address_office-3', 'ระบุที่ตั้งใหม่', ['class' => 'control-label font-medium-1 text-capitalize']) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group required{{ $errors->has('lab_name') ? 'has-error' : ''}}">
                    {!! Form::label('lab_name', 'ชื่อห้องปฏิบัติการ'.' :', ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-10">
                        {!! Form::text('lab_name', !empty( $applicationlab->lab_name )?$applicationlab->lab_name:null,['class' => 'form-control', 'required' => true ]) !!}
                        {!! $errors->first('lab_name', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('lab_address') ? 'has-error' : ''}}">
                    {!! Form::label('lab_address', 'เลขที่'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('lab_address', !empty( $applicationlab->lab_address )?$applicationlab->lab_address:null,['class' => 'form-control', 'required' => true ]) !!}
                        {!! $errors->first('lab_address', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('lab_building') ? 'has-error' : ''}}">
                    {!! Form::label('lab_building', 'อาคาร/หมู่บ้าน'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('lab_building', !empty( $applicationlab->lab_building )?$applicationlab->lab_building:null,  ['class' => 'form-control', ]) !!}
                        {!! $errors->first('lab_building', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('lab_soi') ? 'has-error' : ''}}">
                    {!! Form::label('lab_soi', 'ตรอก/ซอย'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('lab_soi', !empty( $applicationlab->lab_soi )?$applicationlab->lab_soi:null,  ['class' => 'form-control' ]) !!}
                        {!! $errors->first('lab_soi', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('lab_moo') ? 'has-error' : ''}}">
                    {!! Form::label('lab_moo', 'หมู่'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('lab_moo', !empty( $applicationlab->lab_moo )?$applicationlab->lab_moo:null,['class' => 'form-control']) !!}
                        {!! $errors->first('lab_moo', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('lab_road') ? 'has-error' : ''}}">
                    {!! Form::label('lab_road', 'ถนน'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('lab_road', !empty( $applicationlab->lab_road )?$applicationlab->lab_road:null,['class' => 'form-control']) !!}
                        {!! $errors->first('lab_road', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group ">
                    {!! Form::label('lab_address_seach', 'ค้นหาที่อยู่'.' :', ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-10">
                        {!! Form::text('lab_address_seach', null,  ['class' => 'form-control lab_address_seach', 'autocomplete' => 'off', 'data-provide' => 'typeahead', 'placeholder' => 'ค้นหาที่อยู่' ]) !!}
                        {!! $errors->first('lab_address_seach', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('lab_subdistrict_txt') ? 'has-error' : ''}}">
                    {!! Form::label('lab_subdistrict_txt', 'แขวง/ตำบล'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('lab_subdistrict_txt', !empty( $applicationlab->LabSubdistrictName )?$applicationlab->LabSubdistrictName:null,  ['class' => 'form-control lab_input_show', 'required' => true, 'readonly' => true ]) !!}
                        {!! $errors->first('lab_subdistrict_txt', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('lab_district_txt') ? 'has-error' : ''}}">
                    {!! Form::label('lab_district_txt', 'เขต/อำเภอ'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('lab_district_txt', !empty( $applicationlab->LabDistrictName )?$applicationlab->LabDistrictName:null,['class' => 'form-control lab_input_show', 'required' => true, 'readonly' => true ]) !!}
                        {!! $errors->first('lab_district_txt', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('lab_province_txt') ? 'has-error' : ''}}">
                    {!! Form::label('lab_province_txt', 'จังหวัด'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('lab_province_txt', !empty( $applicationlab->LabProvinceName )?$applicationlab->LabProvinceName:null,  ['class' => 'form-control lab_input_show', 'required' => true, 'readonly' => true ]) !!}
                        {!! $errors->first('lab_province_txt', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('lab_zipcode_txt') ? 'has-error' : ''}}">
                    {!! Form::label('lab_zipcode_txt', 'รหัสไปรษณีย์'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('lab_zipcode_txt', !empty( $applicationlab->LabPostcodeName )?$applicationlab->LabPostcodeName:null,['class' => 'form-control lab_input_show', 'required' => true, 'readonly' => true  ]) !!}
                        {!! $errors->first('lab_zipcode_txt', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('lab_phone') ? 'has-error' : ''}}">
                    {!! Form::label('lab_phone', 'เบอร์โทรศัพท์'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('lab_phone', !empty( $applicationlab->lab_phone )?$applicationlab->lab_phone:null,['class' => 'form-control', 'required' => true ]) !!}
                        {!! $errors->first('lab_phone', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('lab_fax') ? 'has-error' : ''}}">
                    {!! Form::label('lab_fax', ' เบอร์โทรสาร'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('lab_fax', !empty( $applicationlab->lab_fax )?$applicationlab->lab_fax:null,  ['class' => 'form-control', ]) !!}
                        {!! $errors->first('lab_fax', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {!! Form::hidden('lab_subdistrict_id', !empty( $applicationlab->lab_subdistrict_id )?$applicationlab->lab_subdistrict_id:null, [ 'class' => 'lab_input_show', 'id' => 'lab_subdistrict_id' ] ) !!}
            {!! Form::hidden('lab_district_id', !empty( $applicationlab->lab_district_id )?$applicationlab->lab_district_id:null, [ 'class' => 'lab_input_show', 'id' => 'lab_district_id' ] ) !!}
            {!! Form::hidden('lab_province_id', !empty( $applicationlab->lab_province_id )?$applicationlab->lab_province_id:null, [ 'class' => 'lab_input_show', 'id' => 'lab_province_id' ] ) !!}
            {!! Form::hidden('lab_zipcode', !empty( $applicationlab->lab_zipcode )?$applicationlab->lab_zipcode:null, [ 'class' => 'lab_input_show', 'id' => 'lab_zipcode' ] ) !!}
        </div>
    </div>
    <hr>

    <div class="box_renew_data">
        @include('section5.application-lab.form.renew-coordinator')
    </div>

    <div class="box_contact">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-md-4"><h6>ผู้ประสานงาน</h6></label>
                    <div class="col-md-9">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <label class="control-label col-md-4">&nbsp;</label>
                <div class="col-md-8">
                    <div class="form-group ">
                        {!! Form::checkbox('use_data_contact', '1', null, ['class' => 'form-control check', 'data-checkbox' => 'icheckbox_flat-blue', 'id'=>'use_data_contact','required' => false]) !!}
                        <label for="use_data_contact" class="font-medium-1">&nbsp;&nbsp; ใช้ข้อมูลเดียวกับผู้ติดต่อตอนลงทะเบียน</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('co_name') ? 'has-error' : ''}}">
                    {!! Form::label('co_name', 'ชื่อผู้ประสานงาน'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('co_name', !empty( $applicationlab->co_name )?$applicationlab->co_name:null,['class' => 'form-control co_input_show', 'required' => true ]) !!}
                        {!! $errors->first('co_name', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required {{ $errors->has('co_position') ? 'has-error' : ''}}">
                    {!! Form::label('co_position', 'ตำแหน่ง'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('co_position', !empty( $applicationlab->co_position )?$applicationlab->co_position:null,  ['class' => 'form-control co_input_show', 'required' => true ]) !!}
                        {!! $errors->first('co_position', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('co_mobile') ? 'has-error' : ''}}">
                    {!! Form::label('co_mobile', 'โทรศัพท์มือถือ'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('co_mobile', !empty( $applicationlab->co_mobile )?$applicationlab->co_mobile:null,['class' => 'form-control co_input_show', 'required' => true ]) !!}
                        {!! $errors->first('co_mobile', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('co_phone') ? 'has-error' : ''}}">
                    {!! Form::label('co_phone', ' โทรศัพท์'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('co_phone', !empty( $applicationlab->co_phone )?$applicationlab->co_phone:null,  ['class' => 'form-control co_input_show' ]) !!}
                        {!! $errors->first('co_phone', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('co_fax') ? 'has-error' : ''}}">
                    {!! Form::label('co_fax', 'โทรสาร'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('co_fax', !empty( $applicationlab->co_fax )?$applicationlab->co_fax:null,['class' => 'form-control co_input_show', 'required' => true ]) !!}
                        {!! $errors->first('co_fax', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('co_email') ? 'has-error' : ''}}">
                    {!! Form::label('co_email', ' อีเมล'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('co_email', !empty( $applicationlab->co_email )?$applicationlab->co_email:null,  ['class' => 'form-control co_input_show', 'required' => true ]) !!}
                        {!! $errors->first('co_email', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

</fieldset>

<fieldset class="scheduler-border">
    <legend class="scheduler-border"><h5>ข้อมูลขอรับบริการ</h5></legend>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label col-md-1"></label>
                <div class="col-md-11">
                    <p class="h5">ยื่นคำขอต่อสำนักงานมาตรฐานภัณฑ์อุตสาหกรรม กระทรวงอุตสาหกรรมเพื่อรับการแต่งตั้งเป็นผู้ตรวจสอบผลิตภัณฑ์อุตสาหกรรม ตามมาตร5แห่งพระราชบัญญัติมาตรฐานผลิตภัณฑ์อุตสาหกรรม ดังนี้</p>
                </div>
            </div>
        </div>
    </div>
    
    @php
    // devops_sso\resources\views\section5\application-lab\modals\modal-scope.blade.php
        $list_standard_scope = App\Models\Basic\Tis::select('tb3_Tisno', 'tb3_TisThainame', 'tb3_TisAutono')
            ->whereIn('status', ['-1', '0', '1', '2', '3'])
            ->orderBy('tb3_Tisno')
            ->get();

        $existing_scopes_init = [];
        if (isset($applicationlab->id)) {
            $list_group_tis_ids = App\Models\Section5\ApplicationLabScope::where('application_lab_id', $applicationlab->id)
                ->select('tis_id')->groupBy('tis_id')->pluck('tis_id')->toArray();
            // โหลด Tis records ทั้งหมดในครั้งเดียว
            $tis_map = App\Models\Basic\Tis::whereIn('tb3_TisAutono', $list_group_tis_ids)
                ->get()->keyBy('tb3_TisAutono');
            foreach ($list_group_tis_ids as $tid) {
                $tis_std = $tis_map->get($tid);
                $saved_scopes = App\Models\Section5\ApplicationLabScope::where('application_lab_id', $applicationlab->id)
                    ->where('tis_id', $tid)->get();
                $existing_scopes_init[$tid] = [
                    'tis_id'  => $tid,
                    'tisno'   => !is_null($tis_std) ? $tis_std->tb3_Tisno : '',
                    'tisname' => !is_null($tis_std) ? strip_tags($tis_std->tb3_TisThainame) : '',
                    'scopes'  => $saved_scopes->map(function($s) {
                        return [
                            'scope_id'      => $s->id,
                            'test_item_id'  => $s->test_item_id,
                            'test_tools_id' => $s->test_tools_id,
                            'test_tools_no' => $s->test_tools_no,
                            'capacity'      => $s->capacity,
                            'range'         => $s->range,
                            'true_value'    => $s->true_value,
                            'fault_value'   => $s->fault_value,
                            'test_duration' => $s->test_duration,
                            'test_price'    => $s->test_price,
                            'tis_tisno'     => $s->tis_tisno,
                            //หมายเหตุจากเจ้าหน้าที่ตอนตรวจประเมิน (audit_result=2 คือเครื่องมือตัวนี้ต้องแก้ไข)
                            'remark'        => $s->remark,
                            'audit_result'  => $s->audit_result,
                        ];
                    })->toArray(),
                ];
            }
        }
    @endphp

    <script>var _init_scopes = {!! json_encode($existing_scopes_init) !!};</script>

    <div class="row">
        <div class="col-md-7">
            <div class="input-group">
                <select id="scope_tis_selector" class="form-control not_select2" style="width:100%">
                    <option value="">- เลือก มอก. -</option>
                    @foreach($list_standard_scope as $std)
                        <option value="{{ $std->tb3_TisAutono }}"
                                data-tisno="{{ $std->tb3_Tisno }}"
                                data-tisname="{{ strip_tags($std->tb3_TisThainame) }}">
                            {{ $std->tb3_Tisno }} : {{ strip_tags($std->tb3_TisThainame) }}
                        </option>
                    @endforeach
                </select>
                <span class="input-group-btn">
                    <button type="button" id="btn_load_scope_items" class="btn btn-primary">
                        <i class="fa fa-search"></i> เพิ่ม
                    </button>
                </span>
            </div>
            <span class="text-danger" id="tis_pending_warning" style="display:none;">
                <i class="fa fa-exclamation-triangle"></i> เลือก มอก. แล้ว แต่ยังไม่ได้กดปุ่ม "เพิ่ม" กรุณากดปุ่ม "เพิ่ม" เพื่อโหลดรายการทดสอบ
            </span>
        </div>

        @include ('section5.application-lab.modals.modal-scope')
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div id="box_scope_request">
            </div>
        </div>
    </div>
</fieldset>

<fieldset class="scheduler-border">
    <legend class="scheduler-border"><h5>ข้อมูลใบรับรอง</h5></legend>
    <div class="row">
        <div class="col-md-7">
            <div class="form-group {{ $errors->has('audit_type') ? 'has-error' : ''}}">
                {!! Form::label('audit_type', 'ได้รับใบรับรองระบบงานตามฐาน 17025', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::hidden('audit_type', !empty($applicationlab->audit_type) ? $applicationlab->audit_type : '1') !!}
                    <label>{!! Form::radio('audit_type_display', '1', true, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'audit_type_1', 'disabled' => true]) !!} ได้รับ พร้อมแนบหลักฐาน</label>
                    <label data-toggle="tooltip" title="เป็นส่วนราชการ องค์การของรัฐ รัฐวิสาหกิจ หน่วยงานของรัฐ รวมทั้งสถาบันอิสระภายใต้สังกัดกระทรวงอุตสาหกรรม ที่ยังไม่ได้รับการรับรองตาม มอก. 17025 ในขอบข่าย มอก. ที่เกี่ยวข้อง แต่มีการดำเนินงานที่เป็นไปตามหลักเกณฑ์ฯ ตามภาคผนวก ก">
                        {!! Form::radio('audit_type_display', '2', false, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'audit_type_2', 'disabled' => true]) !!}
                            ไม่ได้รับ ทำการตรวจประเมิน ภาคผนวก ก.
                    </label>
                    {!! $errors->first('audit_type', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row box_audit_type_1">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group required">
                    {!! Form::label('certificate_cerno_export', 'เลขที่ได้รับการรับรอง'.' :', ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-6">
                        {!! Form::text('certificate_cerno_export', null, ['class' => 'form-control certificate_cerno_export', 'id' => 'certificate_cerno_export', 'placeholder'=>'กรอกเลขที่ได้รับการรับรอง']); !!}
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" type="button" id="btn_std_export" value="1"><i class="fa fa-database"></i> ดึงจากฐานของ สมอ.</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('certificate_accereditatio_no', 'หมายเลขการรับรอง'.' :', ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-6">
                        {!! Form::text('certificate_accereditatio_no', null, ['class' => 'form-control certificate_accereditatio_no', 'id' => 'certificate_accereditatio_no']); !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group required">
                    {!! Form::label('certificate_issue_date', 'วันที่ได้รับ'.' :', ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('certificate_issue_date', null, ['class' => 'form-control mydatepicker', 'placeholder'=>'dd/mm/yyyy']); !!}
                            <span class="input-group-addon"><i class="icon-calender"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group required">
                    {!! Form::label('certificate_expire_date', 'วันที่หมดอายุ'.' :', ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            {!! Form::text('certificate_expire_date', null, ['class' => 'form-control mydatepicker', 'placeholder'=>'dd/mm/yyyy']); !!}
                            <span class="input-group-addon"><i class="icon-calender"></i></span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success show_tag_a" type="button" id="btn_cer_add"><i class="icon-plus"></i> เพิ่ม</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <span class="text-danger" id="cer_pending_warning" style="display:none;">
                    <i class="fa fa-exclamation-triangle"></i> กรอกข้อมูลใบรับรองไว้แล้ว แต่ยังไม่ได้กดปุ่ม "เพิ่ม" กรุณากดปุ่ม "เพิ่ม" เพื่อเพิ่มรายการใบรับรอง
                </span>
            </div>
        </div>

        @include ('section5.application-lab.modals.modal-cer')
    </div>

    <div class="row box_audit_type_1">
        <div class="col-md-12">

            <div class="table-responsive">
                <table class="table table-bordered repeater_audit_type_1" id="table-certificate">
                    <thead>
                        <tr>
                            <th class="text-center" width="25%">ใบรับรองเลขที่</th>
                            <th class="text-center" width="20%">หมายเลขการรับรอง</th>
                            <th class="text-center" width="15%">วันที่ได้รับ</th>
                            <th class="text-center" width="15%">วันที่หมดอายุ</th>
                            <th class="text-center" width="20%">ไฟล์ใบรับรอง</th>
                            <th class="text-center" width="5%">ลบ</th>
                        </tr>
                    </thead>
                    <tbody data-repeater-list="repeater-audit-1" class="text-center">
                        @if( isset($applicationlab->id) )

                            @php
                                $list_cer = App\Models\Section5\ApplicationLabCertificate::where('application_lab_id', $applicationlab->id )->get();
                            @endphp

                            @foreach ( $list_cer as $cer )
                                @php
                                    $certificate_export = $cer->certificate_export;
                                @endphp
                                <tr data-repeater-item>
                                    <td>
                                        {!! !empty($cer->certificate_no)?$cer->certificate_no:null !!}
                                        <input type="hidden" class="certificate_ids"  name="certificate_id" value="{!! !empty($cer->certificate_id)?$cer->certificate_id:null !!}">
                                        <input type="hidden" class="certificate_no" name="certificate_no" value="{!! !empty($cer->certificate_no)?$cer->certificate_no:null !!}">
                                        <input type="hidden" class="certificate_modal_id" name="cer_id" value="{!! $cer->id !!}">
                                    </td>
                                    <td>
                                        {!! !empty($certificate_export->accereditatio_no)?$certificate_export->accereditatio_no:(!empty($cer->accereditatio_no)?$cer->accereditatio_no:null) !!}
                                        <input type="hidden" name="accereditatio_no" value="{!! !empty($certificate_export->accereditatio_no)?$certificate_export->accereditatio_no:(!empty($cer->accereditatio_no)?$cer->accereditatio_no:null) !!}">
                                    </td>
                                    <td>
                                        {!! !empty($cer->certificate_start_date)?HP::revertDate($cer->certificate_start_date):null !!}
                                        <input type="hidden" name="certificate_start_date" value="{!! !empty($cer->certificate_start_date)?HP::revertDate($cer->certificate_start_date):null !!}">
                                    </td>
                                    <td>
                                        {!! !empty($cer->certificate_end_date)?HP::revertDate($cer->certificate_end_date):null !!}
                                        <input type="hidden" name="certificate_end_date" value="{!! !empty($cer->certificate_end_date)?HP::revertDate($cer->certificate_end_date):null !!}">
                                    </td>
                                    <td>
                                        @if( !empty($cer->certificate_file) )
                                            <a href="{!! HP::getFileStorage($cer->certificate_file->url) !!}" class="m-l-5" target="_blank" title="{!! !empty($cer->certificate_file->filename) ? $cer->certificate_file->filename : 'ไฟล์แนบ' !!}">
                                                <i class="fa fa-folder-open fa-lg" style="color:#FFC000;" aria-hidden="true"></i>
                                            </a>

                                            <a class="btn btn-danger btn-xs show_tag_a m-l-5" href="{!! url('funtions/get-delete/files/'.($cer->certificate_file->id).'/'.base64_encode('request-section-5/application-lab/'.$applicationlab->id.'/edit') ) !!}" title="ลบไฟล์"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                        @elseif( empty($cer->certificate_file) && empty($cer->certificate_id) )
                                            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                                <div class="form-control" data-trigger="fileinput">
                                                    <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                                    <span class="fileinput-filename"></span>
                                                </div>
                                                <span class="input-group-addon btn btn-default btn-file">
                                                    <span class="fileinput-new">เลือกไฟล์</span>
                                                    <span class="fileinput-exists">เปลี่ยน</span>
                                                    <input type="file" name="certificate_file">
                                                </span>
                                                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                                            </div>
                                        @elseif( !empty($cer->certificate_id)  )

                                            @php
                                                $url_center = isset( HP::getConfig()->url_center )?HP::getConfig()->url_center:null;
                                            @endphp
                                        
                                        <a href="{!! url($url_center.'/api/v1/certificate?cer='.(!empty($cer->certificate_no)?$cer->certificate_no:null)) !!}"  target="_blank"><span class="text-info"><i class="fa fa-file"></i></span></a>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" type="button" data-repeater-delete> <i class="fa fa-minus"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

            <div class="box_audit_type_2 repeater_audit_type_2">
                <div data-repeater-list="repeater-audit-2">

                    @if(  isset($applicationlab->id) && !empty($applicationlab->audit_date) )

                        @php
                            $audit_date = json_decode($applicationlab->audit_date);
                        @endphp

                        @foreach (  $audit_date as $audit_dates )

                            <div class="form-group"  data-repeater-item>
                                {!! Form::label('co_email', 'ช่วงวันที่พร้อมให้เข้าตรวจประเมิน'.' :', ['class' => 'col-md-2 control-label']) !!}
                                <div class="col-md-7">
                                    <div class="input-daterange input-group date-range">
                                        <div class="input-group">
                                            {!! Form::text('audit_date_start',  !empty($audit_dates->audit_date_start)?HP::revertDate($audit_dates->audit_date_start):null,  ['class' => 'form-control audit_date_start','placeholder'=>"dd/mm/yyyy", 'required' => true]) !!}
                                            <span class="input-group-addon"><i class="icon-calender"></i></span>
                                        </div>
                                        <label class="input-group-addon bg-white b-0 control-label "> ถึงวันที่ </label>
                                        <div class="input-group">
                                            {!! Form::text('audit_date_end',  !empty($audit_dates->audit_date_end)?HP::revertDate($audit_dates->audit_date_end):null, ['class' => 'form-control audit_date_end','placeholder'=>"dd/mm/yyyy", 'required' => true]) !!}
                                            <span class="input-group-addon"><i class="icon-calender"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-danger rounded-circle btn_remove_audi2" type="button" data-repeater-delete>
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                    <button class="btn btn-success btn-primary btn_add_audi2" type="button" data-repeater-create>
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                        @endforeach


                    @else

                        <div class="form-group"  data-repeater-item>
                            {!! Form::label('co_email', 'ช่วงวันที่พร้อมให้เข้าตรวจประเมิน'.' :', ['class' => 'col-md-2 control-label']) !!}
                            <div class="col-md-7">
                                <div class="input-daterange input-group date-range">
                                    <div class="input-group">
                                        {!! Form::text('audit_date_start',  null,  ['class' => 'form-control audit_date_start','placeholder'=>"dd/mm/yyyy", 'required' => true]) !!}
                                        <span class="input-group-addon"><i class="icon-calender"></i></span>
                                    </div>
                                    <label class="input-group-addon bg-white b-0 control-label "> ถึงวันที่ </label>
                                    <div class="input-group">
                                        {!! Form::text('audit_date_end',  null, ['class' => 'form-control audit_date_end','placeholder'=>"dd/mm/yyyy", 'required' => true]) !!}
                                        <span class="input-group-addon"><i class="icon-calender"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button class="btn btn-danger rounded-circle btn_remove_audi2" type="button" data-repeater-delete>
                                    <i class="fa fa-trash-o"></i>
                                </button>
                                <button class="btn btn-success btn-primary btn_add_audi2" type="button" data-repeater-create>
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>

                    @endif


                </div>
            </div>

        </div>
    </div>
</fieldset>

<fieldset class="scheduler-border">
    <legend class="scheduler-border"><h5>เอกสารแนบ</h5></legend>
    @php

        if( isset($applicationlab->id) && !empty($applicationlab->config_evidencce) ){
            $configs_evidences = json_decode($applicationlab->config_evidencce);
        }else{
            $configs_evidences = DB::table((new App\Models\Config\ConfigsEvidence)->getTable().' AS evidences')
                                    ->leftjoin((new App\Models\Config\ConfigsEvidenceGroup)->getTable().' AS groups', 'groups.id', '=', 'evidences.evidence_group_id')
                                    ->where('groups.id', 3)
                                    ->where('evidences.state', 1)
                                    ->select('evidences.*')
                                    ->orderBy('evidences.ordering')
                                    ->get();

        }

    @endphp

    <div class="repeater-form">
        <div data-repeater-list="evidences">
            @foreach ( $configs_evidences as $evidences )
                @php
                    $evidences = (object)$evidences;
                    $file_properties = null;


                    if(  !empty($evidences->file_properties)  ){
                        $list = [];
                        foreach ( json_decode($evidences->file_properties) as $value) {

                            $list[] = '.'.$value;
                        }
                        $evidences->file_properties_item =  $list;

                    }

                    $file_properties = !empty($evidences->file_properties_item) ? implode(',', $evidences->file_properties_item ):'';

                    $attachment = null;

                    if( isset($applicationlab->id) ){
                        $attachment = App\AttachFile::where('ref_table', (new App\Models\Section5\ApplicationLab )->getTable() )
                                        // ->where('tax_number', $applicationlab->applicant_taxid)
                                        ->where('ref_id', $applicationlab->id )
                                        ->when(!empty($evidences->id) ? $evidences->id : null, function ($query, $setting_file_id){
                                            return $query->where('setting_file_id', $setting_file_id);
                                        })
                                        ->first();
                    }

                @endphp


                <div class="row" data-repeater-item>
                    <div class="col-md-12">
                        <div class="form-group @if($evidences->required == 1) required @endif">
                            {!! HTML::decode(Form::label('evidence_file_config', (!empty($evidences->title)?$evidences->title:null).' : ', ['class' => 'col-md-5 control-label'])) !!}
                            <div class="col-md-4">

                                @if( !empty($attachment) )
                                    <div class="col-md-4" >
                                        <a href="{!! HP::getFileStorage($attachment->url) !!}" target="_blank" title="{!! !empty($attachment->filename) ? $attachment->filename : 'ไฟล์แนบ' !!}">
                                            <i class="fa fa-folder-open fa-lg" style="color:#FFC000;" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div class="col-md-2" >
                                        <a class="btn btn-danger btn-xs show_tag_a" href="{!! url('funtions/get-delete/files/'.($attachment->id).'/'.base64_encode('request-section-5/application-lab/'.$applicationlab->id.'/edit') ) !!}" title="ลบไฟล์"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                    </div>
                                @else

                                    {!! Form::hidden('setting_title' ,(!empty($evidences->title)?$evidences->title:null), ['required' => false]) !!}
                                    {!! Form::hidden('setting_id' ,(!empty($evidences->id)?$evidences->id:null), ['required' => false]) !!}
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <div class="form-control" data-trigger="fileinput">
                                            <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                            <span class="fileinput-filename"></span>
                                        </div>
                                        <span class="input-group-addon btn btn-default btn-file">
                                            <span class="fileinput-new">เลือกไฟล์</span>
                                            <span class="fileinput-exists">เปลี่ยน</span>
                                            <input type="file"
                                                name="evidence_file_config"
                                                class="evidence_file_config" @if($evidences->required == 1) required @endif
                                                @if(  !empty($evidences->file_properties) )
                                                    accept="{!! $file_properties !!}"
                                                    data-accept="{!! base64_encode( $evidences->file_properties) !!}"
                                                @endif
                                                @if(  !empty($evidences->bytes) ) data-max-size="{!! ($evidences->bytes) !!}"  @endif
                                            >
                                        </span>
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

            @endforeach
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">


            @php
                $file_other = [];
                if( isset($applicationlab->id) ){
                    $file_other = App\AttachFile::where('section', 'evidence_file_other')->where('ref_table', (new App\Models\Section5\ApplicationLab )->getTable() )->where('ref_id', $applicationlab->id )->get();
                }
            @endphp

            @foreach ( $file_other as $attach )

                <div class="form-group">
                    {!! HTML::decode(Form::label('personfile', 'เอกสารเพิ่มเติม : ', ['class' => 'col-md-5 control-label text-left'])) !!}
                    <div class="col-md-3">
                        {!! Form::text('file_documents', ( !empty($attach->caption) ? $attach->caption:null) , ['class' => 'form-control' , 'placeholder' => 'คำอธิบาย', 'disabled' => true]) !!}
                    </div>
                    <div class="col-md-3">
                        <a href="{!! HP::getFileStorage($attach->url) !!}" target="_blank" title="{!! !empty($attach->filename) ? $attach->filename : 'ไฟล์แนบ' !!}">
                            <i class="fa fa-folder-open fa-lg" style="color:#FFC000;" aria-hidden="true"></i>
                        </a>
                    </div>
                    <div class="col-md-1" >
                        <a class="btn btn-danger btn-xs show_tag_a " href="{!! url('funtions/get-delete/files/'.($attach->id).'/'.base64_encode(request()->path()) ) !!}" title="ลบไฟล์"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                    </div>
                </div>

            @endforeach

            <div class="form-group repeater-form-other">
                {!! HTML::decode(Form::label('personfile', 'เอกสารเพิ่มเติม : ', ['class' => 'col-md-5 control-label text-left'])) !!}
                <div class="col-md-6" data-repeater-list="repeater-file-other">
                    <div class="row" data-repeater-item>
                        <div class="col-md-5">
                            {!! Form::text('file_documents', null , ['class' => 'form-control' , 'placeholder' => 'กรอกหมายเหตุ']) !!}
                        </div>
                        <div class="col-md-6">
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
                <div class="col-md-1 col-custom-4">
                    <button type="button" class="btn btn-success pull-left" data-repeater-create><i class="icon-plus"></i>เพิ่ม</button>
                </div>
            </div>

        </div>
    </div>

</fieldset>

{!! Form::hidden('type_save', null , ['id' => 'type_save' ]) !!}

{!! Form::hidden('application_lab_id', !empty($applicationlab->id) ? $applicationlab->id:null ) !!}
{!! Form::hidden('application_no', !empty($applicationlab->application_no) ? $applicationlab->application_no:null ) !!}
{!! Form::hidden('application_status', !empty($applicationlab->application_status) ? $applicationlab->application_status:null ) !!}

<fieldset class="scheduler-border" style="display: {{ (isset($applicationlab->edit_page) && $applicationlab->edit_page==true && $applicationlab->application_status!='0')?'':'none'}}">
    <legend class="scheduler-border"><h5>การแก้ไข</h5></legend>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group required">
                    {!! HTML::decode(Form::label('edit_detail', 'รายละเอียดที่แก้ไข : ', ['class' => 'col-md-3 control-label text-left'])) !!}
                    <div class="col-md-7">
                        {!! Form::textarea('edit_detail', null, ['class' => 'form-control', 'id' => 'edit_detail', 'rows'=>'5', 'required'=> (isset($applicationlab->edit_page) && $applicationlab->edit_page==true && $applicationlab->application_status!='0')?true:false]); !!}
                    </div>
                </div>
            </div>
        </div>

</fieldset>

<center>
    <div class="form-group">
        <div class="col-md-offset-4 col-md-4">

            @if( !isset( $applicationlab->id ) || empty($applicationlab->application_status) || in_array($applicationlab->application_status, [0]) )
                <button class="btn btn-info show_tag_a" type="button" id="btn_draft">
                    <i class="fa fa-file-o"></i> ฉบับร่าง
                </button>
            @endif

            <button class="btn btn-primary show_tag_a" type="button" id="btn_submit">
                <i class="fa fa-paper-plane"></i> บันทึก
            </button>

            <a class="btn btn-default show_tag_a" href="{{ url('/request-section-5/application-lab') }}">
                ยกเลิก
            </a>
        </div>
    </div>
</center>

@if( ( (isset( $applicationlab->edited ) && $applicationlab->edited == true  || isset( $applicationlab->show ) && $applicationlab->show == true ) ) && ($applicationlab->app_accept()->count() > 0) )
    @include ('section5.application-lab.history')
@endif

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

    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    {{-- <script src="{{asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')}}"></script> --}}

    <script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>
    <script src="{{asset('plugins/components/loading-overlay/js/loadingoverlay.min.js')}}"></script>
    <script>

        $(document).ready(function () {

            $(".input_number").on("keypress",function(e){
                var eKey = e.which || e.keyCode;
                if((eKey<48 || eKey>57) && eKey!=46 && eKey!=44){
                    return false;
                }
            });

            //ช่วงวันที่
            jQuery('.date-range').datepicker({
                toggleActive: true,
                language:'th-th',
                format: 'dd/mm/yyyy',
                autoclose: true,
            });

            jQuery('.mydatepicker').datepicker({
                toggleActive: true,
                language:'th-th',
                format: 'dd/mm/yyyy',
                autoclose:true
            });

            $('.repeater-form').repeater();

            $('.repeater_audit_type_1').repeater({
                show: function () {
                    $(this).slideDown();

                    jQuery('.date-range').datepicker({
                        toggleActive: true,
                        language:'th-th',
                        format: 'dd/mm/yyyy',
                        autoclose: true,
                    });

                    BoxAuditType1();

                    $(this).find('.btn_add_audi1').hide();
                    reBuiltToggle($(this).find('.certificate_issued_by'));
                    LoadAuditor1();

                },
                hide: function (deleteElement) {
                    if (confirm('คุณต้องการลบแถวนี้ใช่หรือไม่ ?')) {
                        $(this).slideUp(deleteElement);
                    }
                }
            });

            BoxAuditType1();

            $('.repeater_audit_type_2').repeater({
                show: function () {
                    $(this).slideDown();

                    jQuery('.date-range').datepicker({
                        toggleActive: true,
                        language:'th-th',
                        format: 'dd/mm/yyyy',
                    });

                    BoxAuditType2();

                    $(this).find('.btn_add_audi2').hide();

                },
                hide: function (deleteElement) {
                    if (confirm('คุณต้องการลบแถวนี้ใช่หรือไม่ ?')) {
                        $(this).slideUp(deleteElement);
                    }
                }
            });

            $('.repeater-form-other').repeater({
                show: function () {
                    $(this).slideDown();
                },
                hide: function (deleteElement) {
                    if (confirm('คุณต้องการลบแถวนี้ใช่หรือไม่ ?')) {
                        $(this).slideUp(deleteElement);
                    }
                }
            });

            BoxAuditType2();

            $('.repeater-table-scope').repeater();

            // ลบกรอบแดงออกทันทีที่ผู้ใช้กรอก/เลือกช่องนั้นแล้ว
            $('body').on('input change', '#from_box .field-required-error', function () {
                var $el = $(this);
                var filled = $el.is(':checkbox') || $el.is(':radio')
                    ? $('#from_box').find(':input[name="'+$el.attr('name')+'"]:checked').length > 0
                    : $.trim($el.val() || '') !== '';
                if( filled ){
                    $el.removeClass('field-required-error');
                }
            });

            $('#btn_submit').click(function (e) {

                $('#type_save').val('save');

                var values =  $('#box_scope_request').find(".section_box_tis").map(function(){return $(this).val(); }).get();

                var total_scope_checked = $('#box_scope_request').find('.scope_checkbox:checked').length;

                var empty_tis_panel = $('#box_scope_request').find('.scope-panel').filter(function(){
                    return $(this).find('.scope_checkbox:checked').length === 0;
                }).length > 0;

                var invalid_scope_item = $('#box_scope_request').find('.main-item-row[data-exempt="0"]').filter(function(){
                    return $(this).find('.scope_checkbox').is(':checked') && $(this).find('.tool_chk:checked').length === 0;
                }).length > 0;

                var pending_tis_selected = !!$('#scope_tis_selector').val();

                var pending_cer = $.trim($('#certificate_cerno_export').val()     || '') !== '' ||
                                   $.trim($('#certificate_issue_date').val()      || '') !== '' ||
                                   $.trim($('#certificate_expire_date').val()     || '') !== '' ||
                                   $.trim($('#certificate_accereditatio_no').val() || '') !== '';

                // เลือก "ใบรับรอง 17025" (audit_type_1) แล้วต้องมีใบรับรองอย่างน้อย 1 รายการในตาราง
                var missing_certificate = $('#audit_type_1').is(':checked') && $('#table-certificate tbody tr').length === 0;

                var applicant_taxid = $('#applicant_taxid').val();

                if( $('#applicant_type_1').is(':checked',true) ){
                    $('.box_lab').find('input, select, hidden, checkbox').prop('required', false);
                }

                if( applicant_taxid == '' ){
                    Swal.fire({
                        type: 'error',
                        title: 'ไม่สามารถบันทึกได้ เนื่องจากไม่มีเลขประจำตัวผู้เสียภาษีอากร',
                        // html: '<p class="h4"></p>',
                        width: 500
                    });
                }else if(values.length == 0 || total_scope_checked == 0){
                    Swal.fire({
                        type: 'error',
                        title: 'กรุณาเลือกรายการทดสอบที่ขอรับการแต่งตั้งอย่างน้อย 1 รายการ',
                        // html: '<p class="h4"></p>',
                        width: 500
                    });
                }else if(empty_tis_panel){
                    Swal.fire({
                        type: 'error',
                        title: 'มี มอก. ที่โหลดไว้แต่ยังไม่ได้เลือกรายการทดสอบเลย กรุณาเลือกรายการทดสอบ หรือลบ มอก. นั้นออก',
                        width: 500
                    });
                }else if(invalid_scope_item){
                    Swal.fire({
                        type: 'error',
                        title: 'มีรายการทดสอบที่เลือกไว้แต่ยังไม่ได้ระบุเครื่องมือ กรุณาระบุเครื่องมือให้ครบทุกรายการ',
                        width: 500
                    });
                }else if(missing_certificate){
                    Swal.fire({
                        type: 'error',
                        title: 'กรุณากรอกข้อมูลใบรับรอง (เลขที่ได้รับการรับรอง, วันที่ได้รับ, วันที่หมดอายุ) และกดปุ่ม "เพิ่ม" อย่างน้อย 1 รายการ',
                        width: 500
                    });
                }else if(pending_tis_selected){
                    Swal.fire({
                        type: 'error',
                        title: 'มีการเลือก มอก. ไว้แต่ยังไม่ได้กดปุ่ม "เพิ่ม" กรุณากดเพิ่มก่อนบันทึก',
                        width: 500
                    });
                }else if(pending_cer){
                    Swal.fire({
                        type: 'error',
                        title: 'มีการกรอกข้อมูลใบรับรองไว้แต่ยังไม่ได้กดปุ่ม "เพิ่ม" กรุณากดเพิ่มรายการใบรับรองก่อนบันทึก',
                        width: 500
                    });
                }else if( !highlightRequiredEmpty() ){
                    Swal.fire({
                        type: 'error',
                        title: 'กรุณากรอกข้อมูลในช่องที่มีกรอบสีแดงให้ครบถ้วน',
                        width: 500
                    });
                }else{
                    $('#from_box').submit();
                }

            });

            $('#btn_draft').click(function (e) {

                $('#type_save').val('draft');

                var applicant_taxid = $('#applicant_taxid').val();

                if( $('#applicant_type_1').is(':checked',true) ){
                    $('.box_lab').find('input, select, hidden, checkbox').prop('required', false);
                }

                $('.box_audit_type_1,.box_audit_type_2,.box_hp_address,.box_contact').find('input, select, hidden, checkbox').prop('required', false);

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

            $("#lab_address_seach").select2({
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

            $("#lab_address_seach").on('change', function () {
                $.ajax({
                    url: "{!! url('/funtions/get-addreess/') !!}" + "/" + $(this).val()
                }).done(function( jsondata ) {
                    if(jsondata != ''){

                        $('#lab_subdistrict_txt').val(jsondata.sub_title);
                        $('#lab_district_txt').val(jsondata.dis_title);
                        $('#lab_province_txt').val(jsondata.pro_title);
                        $('#lab_zipcode_txt').val(jsondata.zip_code);

                        $('#lab_subdistrict_id').val(jsondata.sub_ids);
                        $('#lab_district_id').val(jsondata.dis_id);
                        $('#lab_province_id').val(jsondata.pro_id);
                        $('#lab_zipcode').val(jsondata.zip_code);

                    }
                });
            });

            $('#use_data_contact').on('ifChecked', function(event){
                use_data_contacts( 1 );
            });

            $('#use_data_contact').on('ifUnchecked', function(event){
                use_data_contacts( 0 );
            });

            $('#use_address_office-1').on('ifChecked', function(event){
                use_address_offices();
            });

            $('#use_address_office-2').on('ifChecked', function(event){
                use_address_offices();
            });

            $('#use_address_office-3').on('ifChecked', function(event){
                use_address_offices();
            });

            $('.modal_create_modal').click(function (e) {

                $('#myTableScope tbody').html('');
                $('#modal_tis_id').val('').trigger('change').select2();

                $('#modal_test_item').val('').trigger('change').select2();
                $('#modal_test_tools').val('').trigger('change').select2();

                $('#modal_test_tools_no').val('');
                $('#modal_capacity').val('');
                $('#modal_range').val('');
                $('#modal_true_value').val('');
                $('#modal_fault_value').val('');
                $('#modal_tis_id').prop('disabled', false);

                data_tis_disabled();

            });

            $("body").on('click', '.btn_section_edit', function () {

                var tb = $(this).data('table');
                var tis_id = $(this).data('tis_id');

                if( tb != '' && tis_id != '' ){

                    $.ajax({
                        url: "{!! url('/request-section-5/application-lab/get-test-item') !!}" + "/" + tis_id
                    }).done(function( object ) {
                        if( object.length > 0){
                            $.each(object, function( index, data ) {
                                arr_tst_item[ data.id ] = data.title;
                            });
                        }
                    });

                    $.ajax({
                        url: "{!! url('/request-section-5/application-lab/get-test-tools-std') !!}" + "/" + tis_id
                    }).done(function( object ) {

                        if( object.length > 0){
                            $.each(object, function( index, data ) {
                                arr_tools[ data.id ] = data.title;
                            });
                        }
                    });

                    setTimeout(function(){
                        var i = 0;
                        $('#modal_tis_id').val(tis_id);
                        $('#modal_tis_id').trigger('change');

                        $('#modal_tis_id').prop('disabled', true);

                        $('#myTableScope tbody').html('');

                        $('#'+tb ).find('.scope_tis_id').each(function (index, rowId) {
                            i++;
                            var row = $(rowId).parent().parent();

                            var tis_ids = $(rowId).val();

                            var tis_num = row.find('.scope_tis_tisno').val();
                            var test_item = row.find('.scope_test_item_id').val();
                            var test_tools = row.find('.scope_test_tools_id').val();
                            var test_tools_no = row.find('.scope_test_tools_no').val();
                            var capacity = row.find('.scope_capacity').val();
                            var range = row.find('.scope_range').val();
                            var true_value = row.find('.scope_true_value').val();
                            var fault_value = row.find('.scope_fault_value').val();

                            var test_duration = row.find('.scope_test_duration').val();
                            var test_price = row.find('.scope_test_price').val();

                            var test_item_txt = row.children("td:nth-child(2)").text();

                            var test_tools_txt = row.children("td:nth-child(3)").text();

                            var scope_id = row.find('.scope_id').val();

                            var inputSTD = '<input type="hidden" class="myTableScope_tis_id" name="tis_id" value="'+(tis_id)+'"><input type="hidden" class="Mscope_tis_tisno" name="tis_tisno" value="'+(tis_num)+'">';
                            var idRows = '<input type="hidden" class="Mscope_id" name="scope_id" value="'+(scope_id)+'">';

                            var id_row_tr = Math.floor(Math.random() * 26) + Date.now();

                            var test_item_name = arr_tst_item[test_item];
                            var test_tools_name = arr_tools[test_tools];

                            var inputHidden = inputSTD;
                                inputHidden += idRows;
                                inputHidden += '<input type="hidden" class="Mscope_test_item_id" name="test_item_id" value="'+(test_item)+'">';
                                inputHidden += '<input type="hidden" class="Mscope_test_tools_id" name="test_tools_id" value="'+(test_tools)+'">';
                                inputHidden += '<input type="hidden" class="Mscope_test_tools_no" name="test_tools_no" value="'+(test_tools_no)+'">';
                                inputHidden += '<input type="hidden" class="Mscope_capacity" name="capacity" value="'+(capacity)+'">';
                                inputHidden += '<input type="hidden" class="Mscope_range" name="range" value="'+(range)+'">';
                                inputHidden += '<input type="hidden" class="Mscope_true_value" name="true_value" value="'+(true_value)+'">';
                                inputHidden += '<input type="hidden" class="Mscope_fault_value" name="fault_value" value="'+(fault_value)+'">';
                                inputHidden += '<input type="hidden" class="Mscope_test_duration" name="test_duration" value="'+(test_duration)+'">';
                                inputHidden += '<input type="hidden" class="Mscope_test_price" name="test_price" value="'+(test_price)+'">';

                            var _tr = '';
                                _tr += '<tr class="row_tr_'+(id_row_tr)+'">';
                                _tr += '<td class="text-center text-top"><span class="Modalno_'+(test_item)+'"></span></td>';
                                _tr += '<td class="text-center text-top">'+(test_item_name)+'</td>';
                                _tr += '<td class="text-center text-top">'+(test_tools_name)+'</td>';
                                _tr += '<td class="text-center text-top">'+(test_tools_no)+'</td>';
                                _tr += '<td class="text-center text-top">'+(capacity)+'</td>';
                                _tr += '<td class="text-center text-top">'+(range)+'</td>';
                                _tr += '<td class="text-center text-top">'+(true_value)+'</td>';
                                _tr += '<td class="text-center text-top">'+(fault_value)+'</td>';
                                _tr += '<td class="text-center text-top">'+(test_duration)+'</td>';
                                _tr += '<td class="text-center text-top">'+(test_price)+'</td>';
                                _tr += '<td class="text-center text-top"><button type="button" class="btn btn-danger btn-sm btn_remove_modalscope" data-tr="'+(id_row_tr)+'">ลบ</button>'+inputHidden+'</td>';
                                _tr += '</tr>';

                            $('#myTableScope tbody').append(_tr);
                        });

                    }, 2000);


                    setTimeout(function(){

                        CloneTableScope();

                        $('#ScopeModal').modal('show');

                        $('#modal_tis_id').trigger('change');
                    }, 2000);

                }

            });


            $("body").on('click', '.btn_remove_scope', function () {
                if(  $('.Tscope_number-'+tis_id).length > 1 ){
                    if(confirm('ยืนยันการลบข้อมูล แถวนี้')){
                        var tis_id = $(this).data('tis_id');
                        $(this).parent().parent().remove();
                        NumberTableScope(tis_id);
                    }
                }else{
                    alert('ไม่สามารถลบได้ !!');
                }
            });

            $('body').on( 'click', '.btn_section_remove',function (e) {
                if (confirm('คุณต้องการลบชุดรายการทดสอบ?')) {
                    $(this).parent().parent().parent().parent().remove();
                    setTimeout(function(){
                        $('.repeater-table-scope').repeater();
                        BtnRemoveSection();
                    }, 400);
                }
            });

            BtnRemoveSection();

            $('#audit_type_1').on('ifChecked', function(event){
                use_audit();
            });

            $('#audit_type_2').on('ifChecked', function(event){
                use_audit();
            });

            use_audit();
            LoadBtnAddAudit1();

            $("body").on('change', '.certificate_issued_by', function () {
                var row = $(this).parent().parent();
                var rows = $(this).parent().parent().parent().parent().parent();

                if( $(this).is(':checked',true) ){
                    row.find('.btn_cer_modal').show();
                    rows.find('.cer_input').prop('readonly', true);
                    row.find('.certificate_modal_id').val('');
                }else{
                    row.find('.btn_cer_modal').hide();
                    rows.find('.cer_input').prop('readonly', false);
                    row.find('.certificate_modal_id').val('');
                }
            });
            LoadAuditor1();
            LoadBtnAddAudit2();

            $("body").on('click', '#btn_std_export', function () {
                $('#CerModal').modal('show');
            });

            // ── เตือนทันทีถ้ากรอกข้อมูลใบรับรองแล้วแต่ยังไม่ได้กดปุ่ม "เพิ่ม" ──
            $('body').on('input change', '#certificate_cerno_export, #certificate_issue_date, #certificate_expire_date, #certificate_accereditatio_no', function () {
                var hasPending = $.trim($('#certificate_cerno_export').val()     || '') !== '' ||
                                  $.trim($('#certificate_issue_date').val()      || '') !== '' ||
                                  $.trim($('#certificate_expire_date').val()     || '') !== '' ||
                                  $.trim($('#certificate_accereditatio_no').val() || '') !== '';

                if( hasPending ){
                    $('#cer_pending_warning').show();
                    $('#btn_cer_add').addClass('btn-danger').removeClass('btn-success');
                }else{
                    $('#cer_pending_warning').hide();
                    $('#btn_cer_add').addClass('btn-success').removeClass('btn-danger');
                }
            });

            $('body').on('click','#btn_cer_add', function (e) {

                var cerno =  $('#certificate_cerno_export').val();
                var issue_date =  $('#certificate_issue_date').val();
                var expire_date =  $('#certificate_expire_date').val();
                var accereditatio_no = $('#certificate_accereditatio_no').val()

                var values = $('.repeater_audit_type_1').find('.certificate_ids').map(function(){return $(this).val(); }).get();

                if( !checkNone(cerno) ){
                    alert("กรุณากรอก เลขที่ได้รับการรับรอง !");
                }else if( !checkNone(issue_date) ){
                    alert("กรุณากรอก วันที่ออกใบรับรอง !");
                }else if( !checkNone(expire_date) ){
                    alert("กรุณากรอก วันที่หมดอายุใบรับรอง !");
                }else{

                    var id = $('#certificate_cerno_export').data( "id" );
                    var table = $('#certificate_cerno_export').data( "table" );

                    var val_btn = $('#btn_std_export').val();

                    if( val_btn == 1){
                        id = '';
                        table = '';
                    }

                    var certificate_id  = '<input type="hidden" class="certificate_ids" name="certificate_id" value="'+(checkNone(id)?id:'')+'">';
                    var certificate_no  = '<input type="hidden" class="certificate_no" name="certificate_no" value="'+(checkNone(cerno)?cerno:'')+'">';
                    var certificate_start_date  = '<input type="hidden" name="certificate_start_date" value="'+issue_date+'">';
                    var certificate_end_date  = '<input type="hidden" name="certificate_end_date" value="'+expire_date+'">';
                    var certificate_table  = '<input type="hidden" name="certificate_table" value="'+(checkNone(table)?table:'')+'">';
                    var certificate_accereditatio_no  = '<input type="hidden" name="accereditatio_no" value="'+(checkNone(accereditatio_no)?accereditatio_no:'')+'">';

                    var btn = '<button class="btn btn-sm btn-danger" type="button" data-repeater-delete> <i class="fa fa-minus"></i></button>';

                    var url_center = '{!! isset( HP::getConfig()->url_center )?HP::getConfig()->url_center:'' !!}';

                    var inputFile = '';

                    if(!checkNone(id)){
                        inputFile += '<div class="fileinput fileinput-new input-group" data-provides="fileinput" id="modal_form_file">';
                        inputFile +=  '<div class="form-control" data-trigger="fileinput"><span class="fileinput-filename"></span></div>';
                        inputFile +=  '<span class="input-group-addon btn btn-default btn-file">';
                        inputFile +=  '<span class="input-group-text fileinput-exists" data-dismiss="fileinput">ลบ</span>';
                        inputFile +=  '<span class="input-group-text btn-file">';
                        inputFile +=  '<span class="fileinput-new">เลือกไฟล์</span>';
                        inputFile +=  '<span class="fileinput-exists">เปลี่ยน</span>';
                        inputFile +=  '<input type="file" name="certificate_file" class="certificate_file"  accept=".pdf,.jpg,.png" >';
                        inputFile +=  '</span>';
                        inputFile +=  '</span>';
                        inputFile +=  '</div>';
                    }else{
                        inputFile += '<a href="'+(url_center)+'/api/v1/certificate?cer='+(cerno)+'"  target="_blank"><span class="text-info"><i class="fa fa-file"></i></span></a>';
                    }

                    var tr_ = '<tr data-repeater-item>';
                        tr_ += '<td>'+cerno+' '+ certificate_id + certificate_no +'</td>';
                        tr_ += '<td>'+accereditatio_no+' '+ certificate_accereditatio_no +'</td>';
                        tr_ += '<td>'+issue_date+' '+ certificate_start_date +'</td>';
                        tr_ += '<td>'+expire_date+' '+ certificate_end_date +'</td>';
                        tr_ += '<td>'+ inputFile +'</td>';
                        tr_ += '<td>'+btn+' '+ certificate_table +'</td>';
                        tr_ += '</tr>';

                    if(checkNone(id)){
                        if(values.indexOf(String(id)) == -1){
                            $('#table-certificate tbody').append(tr_);
                        }
                    }else{
                        $('#table-certificate tbody').append(tr_);
                    }


                    $('.repeater_audit_type_1').repeater();

                    setTimeout(function(){

                        $('#certificate_std_export').val('').select2();
                        $('#certificate_cerno_export').val('');
                        $('#certificate_issue_date').val('');
                        $('#certificate_expire_date').val('');
                        $('#certificate_accereditatio_no').val('');

                        $('#certificate_cerno_export').removeAttr( "data-id" );
                        $('#certificate_cerno_export').removeAttr( "data-table" );
                        $('#certificate_cerno_export').removeAttr( "data-accereditatio_no" );

                        $('#btn_std_export').val(1);
                        ShowInputCertificate();

                        $('#cer_pending_warning').hide();
                        $('#btn_cer_add').addClass('btn-success').removeClass('btn-danger');
                    }, 100);
                }

            });


            $('.evidence_file_config').change(function (e) {

                var result = true;

                if(!!$(this).val()){

                    var filesize = this.files[0].size;
                    var fileName = $(this).val();
                    var ext = fileName.substring(fileName.lastIndexOf('.') + 1);
                    var maxZise = $(this).data('max-size');
                    var accept = $(this).data('accept');

                    if( !empty(maxZise) && filesize > maxZise  ){

                        alert('ไฟล์ขนาดต้องไม่เกิน '+formatBytes( maxZise ));
                        $(this).val('');

                        $(this).next(".custom-file-label").html('Choose file')
                        result = false;

                    }else if( !empty(accept) && (JSON.parse(atob( accept )).filter((a) => a)).length > 0  ){

                        if ($.inArray(ext, JSON.parse(atob( accept ))) === -1) {//ถ้าเป็นประเภทไฟล์ที่กำหนด และขนาดไม่เกิน 2MB
                            alert('นามสกุลไฟล์แนบต้องเป็น '+JSON.parse(atob( accept )).map((number) => '.'+number).join(', ')+' เท่านั้น');
                            $(this).val('');
                            $(this).next(".custom-file-label").html('Choose file')
                            result = false;
                        }

                    }

                }

                return result;

            });

            //ขอขึ้นทะเบียนใหม่
            $('#applicant_type_1').on('ifChecked', function(event){
                BoxLab();
            });

            //ขอเพิ่มเติมขอบข่าย
            $('#applicant_type_2').on('ifChecked', function(event){
                BoxLab();
            });

            // ใบรับรอง 17025 → audit_type = 1
            $('#applicant_type_3').on('ifChecked', function(event){
                BoxLab();
                $('#audit_type_1').iCheck('check');
            });

            // ภาคผนวก ก. → audit_type = 2
            $('#applicant_type_4').on('ifChecked', function(event){
                BoxLab();
                $('#audit_type_2').iCheck('check');
            });

            BoxLab();

            $('#lab_id').change(function (e) { 

                //Clear Input
                $('#lab_name').val('');
                $('#lab_address').val('');
                $('#lab_moo').val('');
                $('#lab_soi').val('');
                $('#lab_road').val('');
                $('#lab_building').val('');
                $('.lab_input_show').val('');
                $('#lab_phone').val('');
                $('#lab_fax').val('');

                var id = $(this).val();
                if( !empty(id)){
                    $.ajax({
                        url: "{!! url('/funtions/get-section5-lab/') !!}" + "/" + id
                    }).done(function( obj ) {
                        if(obj != ''){

                            $('#lab_name').val(obj.lab_name);

                            $('#lab_address').val(obj.lab_address);
                            $('#lab_moo').val(obj.lab_moo);
                            $('#lab_soi').val(obj.lab_soi);
                            $('#lab_road').val(obj.lab_road);
                            $('#lab_building').val(obj.lab_building);

                            $('#lab_subdistrict_txt').val(obj.lab_subdistrict);
                            $('#lab_district_txt').val(obj.lab_district);
                            $('#lab_province_txt').val(obj.lab_province);
                            $('#lab_zipcode_txt').val(obj.lab_zipcode);

                            $('#lab_subdistrict_id').val(obj.lab_subdistrict_id);
                            $('#lab_district_id').val(obj.lab_district_id);
                            $('#lab_province_id').val(obj.lab_province_id);
                            $('#lab_zipcode').val(obj.lab_zipcode);

                            $('#lab_phone').val(obj.lab_phone);
                            $('#lab_fax').val(obj.lab_fax);

                        }
                    });
    
                }

                // if( $('#applicant_type_3').is(':checked',true)  ){
                //     loadScopeLab();
                // }
                
            });

            merge_table_box_scope();
        });

        // ไล่หาช่อง required ที่ยังไม่ได้กรอก ทำกรอบแดงให้ และเลื่อนไปที่ช่องแรกที่ตกหล่น
        function highlightRequiredEmpty(){

            $('#from_box .field-required-error').removeClass('field-required-error');

            var $invalid = $('#from_box').find(':input[required]:not(:disabled)').filter(function(){
                var $el = $(this);

                if( $el.is(':checkbox') || $el.is(':radio') ){
                    var name = $el.attr('name');
                    return $('#from_box').find(':input[name="'+name+'"]:checked').length === 0;
                }

                return $.trim($el.val() || '') === '';
            });

            $invalid.addClass('field-required-error');

            if( $invalid.length > 0 ){

                var $first = $invalid.first();

                // เปิด panel/section ที่ถูกซ่อนอยู่ (accordion ขอบข่าย) ให้เห็นช่องที่ error
                $first.parents('.collapse:not(.in)').collapse('show');

                setTimeout(function(){
                    $('html, body').animate({ scrollTop: $first.offset().top - 150 }, 400);
                    $first.focus();
                }, 300);

                return false;
            }

            return true;
        }

        // Check Empty
        function empty(variable){
            switch (variable) {
                case "":
                case 0:
                case "0":
                case null:
                case false:
                case typeof(variable) == "undefined":
                return true;
                default:
                return false;
            }
        }

        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';

            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            const i = Math.floor(Math.log(bytes) / Math.log(k));

            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        function reBuiltToggle(toggle){
            //Clear Toggle
            $(toggle).prependTo( $(toggle).parent().parent() );
            $(toggle).next().remove();
            $(toggle).bootstrapToggle();
        }

        function LoadBtnAddAudit1(){

            $('.btn_add_audi1').each(function(index, el) {

                if( index >= 1){
                    $(el).hide();
                }

            });

        }

        function LoadBtnAddAudit2(){

            $('.btn_add_audi2').each(function(index, el) {

                if( index >= 1){
                    $(el).hide();
                }

            });

        }

        function LoadAuditor1(){
            $('.certificate_issued_by').each(function(index, el) {
                var row = $(el).parent().parent();
                var rows = $(this).parent().parent().parent().parent().parent();
                if( $(el).is(':checked',true) ){
                    row.find('.btn_cer_modal').show();
                    rows.find('.cer_input').prop('readonly', true);
                }else{
                    row.find('.btn_cer_modal').hide();
                    rows.find('.cer_input').prop('readonly', false);

                }
            });
        }

        function BtnRemoveSection(){
            // เดิมซ่อนปุ่ม "ลบ มอก. นี้" ถ้ามี มอก. โหลดอยู่แค่ 1 รายการ (ใช้ตอนระบบเก่าที่ยอมให้เพิ่มได้
            // หลาย มอก. พร้อมกัน) แต่ระบบ scope ปัจจุบันจำกัดให้เลือกได้แค่ มอก. เดียวเสมอ ผลคือปุ่มลบ
            // ไม่มีทางโชว์เลยแม้แต่ครั้งเดียว ต้องลบให้ได้เสมอไม่ว่าจะมี มอก. โหลดอยู่กี่รายการก็ตาม
            $('.btn_section_remove').show();
        }

        function data_tis_disabled(){
            $('#modal_tis_id').children('option').prop('disabled',false);
            $('.section_box_tis').each(function(index , item){
                var data_list = $(item).val();
                $('#modal_tis_id').children('option[value="'+data_list+'"]:not(:selected):not([value=""])').prop('disabled',true);
            });
        }

        function NumberTableScope(tis_id){
            $('.Tscope_number-'+tis_id).each(function(index, el) {
                $(el).text(index+1);
            });
        }

        function use_data_contacts(val){

            if( val == 1){

                $('#co_name').val('{!! isset($user_regitor)?$user_regitor->contact_prefix_text:'' !!}'+'{!! isset($user_regitor)?$user_regitor->contact_first_name:'' !!}'+' '+'{!! isset($user_regitor)?$user_regitor->contact_last_name:'' !!}');
                $('#co_position').val('{!! isset($user_regitor)?$user_regitor->contact_position:'' !!}');
                $('#co_mobile').val('{!! isset($user_regitor)?$user_regitor->contact_phone_number:'' !!}');
                $('#co_phone').val('{!! isset($user_regitor)?$user_regitor->contact_tel:'' !!}');
                $('#co_fax').val('{!! isset($user_regitor)?$user_regitor->contact_fax:'' !!}');
                $('#co_email').val('{!! isset($user_regitor)?$user_regitor->email:'' !!}');

            }else{
                $('.co_input_show').val('');
            }

        }

        function use_address_offices(){

            $("#lab_address_seach").select2("val", "");

            if( $('#use_address_office-1').is(':checked',true) ){
                var address =  $('#hq_address').val();
                var moo =  $('#hq_moo').val();
                var soi =  $('#hq_soi').val();
                var road =  $('#hq_road').val();
                var building =  $('#hq_building').val();

                var subdistrict_txt =  $('#hq_subdistrict_txt').val();
                var district_txt = $('#hq_district_txt').val();
                var province_txt = $('#hq_province_txt').val();
                var postcode_txt = $('#hq_zipcode_txt').val();

                var subdistrict_id = $('#hq_subdistrict_id').val();
                var district_id = $('#hq_district_id').val();
                var province_id = $('#hq_province_id').val();
                var postcode = $('#hq_zipcode').val();

                $('#lab_address').val(address);
                $('#lab_moo').val(moo);
                $('#lab_soi').val(soi);
                $('#lab_road').val(road);
                $('#lab_building').val(building);

                $('#lab_subdistrict_txt').val(subdistrict_txt);
                $('#lab_district_txt').val(district_txt);
                $('#lab_province_txt').val(province_txt);
                $('#lab_zipcode_txt').val(postcode_txt);

                $('#lab_subdistrict_id').val(subdistrict_id);
                $('#lab_district_id').val(district_id);
                $('#lab_province_id').val(province_id);
                $('#lab_zipcode').val(postcode);

            }else if( $('#use_address_office-2').is(':checked',true) ){

                var address =  '{!! isset($user_regitor) && !empty($user_regitor->contact_address_no) ?$user_regitor->contact_address_no:'' !!}';
                var moo =  '{!! isset($user_regitor) && !empty($user_regitor->contact_moo) ?$user_regitor->contact_moo:'' !!}';
                var soi =  '{!! isset($user_regitor) && !empty($user_regitor->contact_soi) ?$user_regitor->contact_soi:'' !!}';
                var road =  '{!! isset($user_regitor) && !empty($user_regitor->contact_street) ?$user_regitor->contact_street:'' !!}';
                var building =  '{!! isset($user_regitor) && !empty($user_regitor->contact_building) ?$user_regitor->contact_building:'' !!}';

                var subdistrict_txt =  '{!! isset($user_regitor) && !empty($address_co) && !empty($address_co->subdistrict_id) ?$user_regitor->contact_subdistrict:'' !!}';
                var district_txt = '{!! isset($user_regitor) && !empty($address_co) && !empty($address_co->district_id) ?$user_regitor->contact_district:'' !!}';
                var province_txt = '{!! isset($user_regitor) && !empty($address_co) && !empty($address_co->province_id) ?$user_regitor->contact_province:'' !!}';
                var postcode_txt = '{!! isset($user_regitor) && !empty($address_co) && !empty($address_co->zipcode) ?$address_co->zipcode:'' !!}';

                var subdistrict_id = '{!! isset($user_regitor) && !empty($address_co) && !empty($address_co->subdistrict_id) ?$address_co->subdistrict_id:'' !!}';
                var district_id = '{!! isset($user_regitor) && !empty($address_co) && !empty($address_co->district_id) ?$address_co->district_id:'' !!}';
                var province_id = '{!! isset($user_regitor) && !empty($address_co) && !empty($address_co->province_id) ?$address_co->province_id:'' !!}';
                var postcode = '{!! isset($user_regitor) && !empty($address_co) && !empty($address_co->zipcode) ?$address_co->zipcode:'' !!}';

                $('#lab_address').val(address);
                $('#lab_moo').val(moo);
                $('#lab_soi').val(soi);
                $('#lab_road').val(road);
                $('#lab_building').val(building);

                $('#lab_subdistrict_txt').val(subdistrict_txt);
                $('#lab_district_txt').val(district_txt);
                $('#lab_province_txt').val(province_txt);
                $('#lab_zipcode_txt').val(postcode_txt);

                $('#lab_subdistrict_id').val(subdistrict_id);
                $('#lab_district_id').val(district_id);
                $('#lab_province_id').val(province_id);
                $('#lab_zipcode').val(postcode);

            }else{
                $('#lab_address').val('');
                $('#lab_moo').val('');
                $('#lab_soi').val('');
                $('#lab_road').val('');
                $('#lab_building').val('');
                $('.lab_input_show').val('');
            }

        }

        function BoxAuditType1(){
            if( $('.btn_remove_audi1').length > 1 ){
                $('.btn_remove_audi1').show();
            }else{
                $('.btn_remove_audi1').hide();
            }
        }

        function BoxAuditType2(){
            if( $('.btn_remove_audi2').length > 1 ){
                $('.btn_remove_audi2').show();
            }else{
                $('.btn_remove_audi2').hide();
            }
        }

        function use_audit(){

            if( $('#audit_type_1').is(':checked',true) ){

                $('#audit_type').val('1');

                $('.box_audit_type_1').show();
                $('.box_audit_type_1').find('input, select, hidden, checkbox').prop('disabled', false);
                $('.box_audit_type_1').find('.certificate_end_date, .certificate_start_date').prop('required', true);

                $('.box_audit_type_2').hide();
                $('.box_audit_type_2').find('input, select, hidden, checkbox').prop('disabled', true);
                $('.box_audit_type_2').find('.audit_date_start, .audit_date_end').prop('required', false);

            }else if( $('#audit_type_2').is(':checked',true) ){

                $('#audit_type').val('2');

                $('.box_audit_type_1').hide();
                $('.box_audit_type_1').find('input, select, hidden, checkbox').prop('disabled', true);
                $('.box_audit_type_1').find('.certificate_end_date, .certificate_start_date').prop('required', false);

                $('.box_audit_type_2').show();
                $('.box_audit_type_2').find('input, select, hidden, checkbox').prop('disabled', false);
                $('.box_audit_type_2').find('.audit_date_start, .audit_date_end').prop('required', true);
            }

        }


        function ShowInputCertificate(){

            var value_btn = $('#btn_std_export').val();

            $('#certificate_issue_date').prop('disabled', true);
            $('#certificate_expire_date').prop('disabled', true);
            $('#certificate_accereditatio_no').prop('disabled', true);
            $('body').find('.certificate_cerno_export').prop('disabled', true);

            if( value_btn == '1'){
                $('body').find('.certificate_cerno_export').prop('disabled', false);
                $('#certificate_issue_date').prop('disabled', false);
                $('#certificate_expire_date').prop('disabled', false);
                $('#certificate_accereditatio_no').prop('disabled', false);
            }else if(  value_btn == '2' ){
                $('body').find('.certificate_cerno_export').prop('disabled', true);
            }
        }

        function checkNone(value) {
            return value !== '' && value !== null && value !== undefined;
        }

        function BoxLab(){

            if( $('#applicant_type_1').is(':checked',true) ){

                $('.box_lab').find('select').select2('val', "");

                $('.box_lab').hide();
                $('.box_lab').find('input, select, hidden, checkbox').prop('disabled', true);
                // $('.box_lab').find('input, select, hidden, checkbox').prop('required', false);

                $('.box_renew_data').hide();
                $('.box_renew_data').find('input, select, hidden, checkbox').prop('disabled', true);

            }else if( $('#applicant_type_2').is(':checked',true) || $('#applicant_type_3').is(':checked',true) ){
                $('.box_lab').show();
                $('.box_lab').find('input, select, hidden, checkbox').prop('disabled', false);
                // $('.box_lab').find('input, select, hidden, checkbox').prop('required', true);

                $('.box_renew_data').hide();
                $('.box_renew_data').find('input, select, hidden, checkbox').prop('disabled', true);

            }else if( $('#applicant_type_4').is(':checked',true)  ){
                
                $('.box_renew_data').show();
                $('.box_renew_data').find('input, select, hidden, checkbox').prop('disabled', false);

                $('.box_lab').show();
                $('.box_lab').find('input, select, hidden, checkbox').prop('disabled', false);
                // $('.box_lab').find('input, select, hidden, checkbox').prop('required', true);
            }
        }

        function loadScopeLab(){
            var lab_id = $('#lab_id').val();

            //ล้างค่า div scope
            $('#box_scope_request').html('');

            if( !empty(lab_id) ){

                $.ajax({
                    url: "{!! url('/funtions/get-section5-lab-scope/') !!}" + "/" + lab_id
                }).done(function( obj ) {
                    if(obj != ''){

                        $('#box_scope_request').html(obj);
                    }

                });

            }

        }

        function merge_table_box_scope(){

            $('.section_box_tis').each(function(index , item){
                var tis_id = $(item).val();

                const table = document.querySelector('#table-group-'+tis_id );

                if( checkNone(table)  ){

                    NumberTableMScope(tis_id);

                    //Col 1
                    let headerCell = null;
                        for (let row of table.rows) {
                            const Cell1 = row.cells[0];
                            const Cell2 = row.cells[1];

                            if (headerCell === null || Cell1.innerText !== headerCell.innerText) {
                                headerCell = Cell1;
                                header2Cell = Cell2;

                            } else {
                                headerCell.rowSpan++;
                                header2Cell.rowSpan++;
                                Cell1.remove();//ลบคอลัมภ์แรก
                                Cell2.remove();//ลบคอลัมภ์สอง
                            }
                        }
                }

            })
        }

    </script>
@endpush
