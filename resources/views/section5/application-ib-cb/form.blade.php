@push('css')
    <link href="{{asset('plugins/components/icheck/skins/all.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/components/bootstrap-datepicker-thai/css/datepicker.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('plugins/components/switchery/dist/switchery.min.css')}}" rel="stylesheet" />
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <link href="{{asset('plugins/components/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <style>
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
        .scope-tis-summary-text {
            color: #777;
            display: none;
            margin-top: 6px;
        }
        .scope-loading-text {
            color: #777;
            display: none;
            margin-top: 6px;
        }
    </style>
@endpush


@php

    $option_list_ibcb = [];
    if( !isset($applicationibcb->id)  ){

        //ข้อมูลผู้ยื่นคำขอ
        $user_login = auth()->user();

        //
        $user_regitor = HP::AuthUserSSO(!empty($applicationibcb->created_by) ? $applicationibcb->created_by : (is_null($user_login->ActInstead) ? $user_login->getKey() : $user_login->ActInstead->getKey()) );
        $address = HP::GetIDAddress( $user_regitor->subdistrict, $user_regitor->district, $user_regitor->province );

        $address_co = HP::GetIDAddress( $user_regitor->contact_subdistrict, $user_regitor->contact_district, $user_regitor->contact_province );

        $applicationibcb = new stdClass;

        $applicationibcb->applicant_name =  !empty( $user_regitor->name )?$user_regitor->name:null;
        $applicationibcb->applicant_taxid = !empty( $user_regitor->tax_number )?$user_regitor->tax_number:null;

        if(  $user_regitor->applicanttype_id != 2 ){
            $applicationibcb->applicant_date_niti =  !empty( $user_regitor->date_niti )?$user_regitor->date_niti:null;
        }else{
            $applicationibcb->applicant_date_niti =  !empty( $user_regitor->date_of_birth )?$user_regitor->date_of_birth:null;
        }

        $applicationibcb->hq_address =  !empty( $user_regitor->address_no )?$user_regitor->address_no:null;
        $applicationibcb->hq_moo =  !empty( $user_regitor->moo )?$user_regitor->moo:null;
        $applicationibcb->hq_soi =  !empty( $user_regitor->soi )?$user_regitor->soi:null;
        $applicationibcb->hq_road =  !empty( $user_regitor->name )?$user_regitor->road:null;
        $applicationibcb->hq_building =  !empty( $user_regitor->building )?$user_regitor->building:null;

        $applicationibcb->hq_province_id =  !empty( $address->province_id )?$address->province_id:null;
        $applicationibcb->hq_district_id =  !empty( $address->district_id )?$address->district_id:null;
        $applicationibcb->hq_subdistrict_id =  !empty( $address->subdistrict_id )?$address->subdistrict_id:null;
        $applicationibcb->hq_zipcode =  !empty( $address->zipcode )?$address->zipcode:null;
        $applicationibcb->hq_phone =  !empty($user_regitor->tel) ? $user_regitor->tel : null;
        $applicationibcb->hq_fax =  !empty($user_regitor->fax) ? $user_regitor->fax : null;

        if( !is_null($address->province_id) ){
            $applicationibcb->HQProvinceName =  !empty( $user_regitor->province )?$user_regitor->province:null;
            $applicationibcb->HQDistrictName =  !empty( $user_regitor->district )?str_replace('เขต','',$user_regitor->district):null;
            $applicationibcb->HQSubdistrictName =  !empty( $user_regitor->subdistrict )?$user_regitor->subdistrict:null;
            $applicationibcb->HQPostcodeName =  !empty( $user_regitor->zipcode )?$user_regitor->zipcode:null;
        }

    }else{
        $option_list_ibcb = App\Models\Section5\Ibcbs::where('ibcb_type', $applicationibcb->application_type )
                                                        ->where(function ($query) use($applicationibcb){
                                                            $query->where('taxid', $applicationibcb->applicant_taxid );
                                                        })
                                                        ->select(DB::raw("CONCAT_WS(' : ', ibcb_code, IF( ibcb_name IS NULL, name, ibcb_name ) ) AS ibcb_title"), 'id')
                                                        ->orderBy('ibcb_code')
                                                        ->pluck('ibcb_title', 'id');
    }


@endphp

<div class="row">
    <div class="col-md-offset-1  col-md-10 col-sm-12">
        <div class="row">
            <div class="col-lg-12 col-sm-12">
                <div class="text-center">
                    <h3 style="color: black">คำขอรับการแต่งตั้ง</h3>
                    <h3 style="color: black">ผู้ตรวจสอบการทำผลิตภัณฑ์อุตสาหกรรม</h3>
                    <h3 style="color: black">ตามมาตรา 5 แห่งพระราชบัญญัติมาตรฐานผลิตภัณฑ์อุตสาหกรรม พ.ศ.2511 และที่แก้ไขเพิ่มเติม</h3>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix p-10"></div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('application_type') ? 'has-error' : ''}}">
            {!! Form::label('application_type', 'ประเภทหน่วยตรวจสอบ', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-8">
                <label>{!! Form::radio('application_type', '1', true, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'application_type_1']) !!} IB</label>
                <label>{!! Form::radio('application_type', '2', false, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'application_type_2']) !!} CB</label>
                {!! $errors->first('application_type', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('applicant_request_type') ? 'has-error' : ''}}">
            {!! Form::label('applicant_request_type', 'ประเภทคำขอ', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-3">
                <label>{!! Form::radio('applicant_request_type', '1', true, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'applicant_request_type_1']) !!} ขอขึ้นทะเบียนใหม่</label>
                <label>{!! Form::radio('applicant_request_type', '2', false, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'applicant_request_type_2']) !!} ขอเพิ่มเติมขอบข่าย</label>
                {{-- <label>{!! Form::radio('applicant_request_type', '3', false, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'applicant_request_type_3']) !!} ขอลดขอบข่าย</label>
                <label>{!! Form::radio('applicant_request_type', '4', false, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'applicant_request_type_4']) !!} ขอแก้ไขข้อมูล</label> --}}
                {!! $errors->first('applicant_request_type', '<p class="help-block">:message</p>') !!}
            </div>
            <div class="col-md-7 box_ibcb">
                {!! Form::select('ibcb_id',  $option_list_ibcb, !empty( $applicationlab->ibcb_id )?$applicationlab->ibcb_id:null,  ['class' => 'form-control select_ibcb_id_id', "placeholder" => '- เลือกหน่วยตรวจสอบ IB/CB -', 'id' => 'ibcb_id' , 'required' => true ]) !!}
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('audit_type') ? 'has-error' : ''}}">
            {!! Form::label('audit_type', 'การได้รับใบรับรองระบบงาน', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-8">
                <label>{!! Form::radio('audit_type', '1', true, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'audit_type_1']) !!} ได้รับ พร้อมแนบหลักฐาน</label>
                <label class="lable_audit_type2">{!! Form::radio('audit_type', '2', false, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'audit_type_2']) !!} ไม่ได้รับ ทำการตรวจประเมิน ภาคผนวก ก.</label>
                {!! $errors->first('audit_type', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<fieldset class="white-box">
    <legend class="legend"><h5>ข้อมูลผู้ยื่นคำขอ</h5></legend>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group required{{ $errors->has('applicant_name') ? 'has-error' : ''}}">
                {!! Form::label('applicant_name', 'ชื่อผู้ยื่นขอรับการแต่งตั้ง'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('applicant_name', !empty( $applicationibcb->applicant_name )?$applicationibcb->applicant_name:null,['class' => 'form-control input_show', 'required' => true, 'readonly' => true ]) !!}
                    {!! $errors->first('applicant_name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group required{{ $errors->has('applicant_taxid') ? 'has-error' : ''}}">
                {!! Form::label('applicant_taxid', 'เลขประจำตัวผู้เสียภาษีอากร'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('applicant_taxid', !empty( $applicationibcb->applicant_taxid )?$applicationibcb->applicant_taxid:null,  ['class' => 'form-control input_show', 'required' => true, 'readonly' => true , 'id' => 'applicant_taxid']) !!}
                    {!! $errors->first('applicant_taxid', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group required{{ $errors->has('applicant_date_niti') ? 'has-error' : ''}}">
                {!! Form::label('applicant_date_niti', 'วันที่จดทะเบียนนิติบุคคล'.' :', ['class' => 'col-md-4 control-label text-left']) !!}
                <div class="col-md-8">
                    <div class="input-group">
                        {!! Form::text('applicant_date_niti_show', !empty( $applicationibcb->applicant_date_niti )?HP::revertDate($applicationibcb->applicant_date_niti):null,  ['class' => 'form-control input_show', 'required' => true, 'readonly' => true ]) !!}
                        {!! $errors->first('applicant_date_niti', '<p class="help-block">:message</p>') !!}
                        <span class="input-group-addon"><i class="icon-calender"></i></span>
                        {!! Form::hidden('applicant_date_niti', !empty( $applicationibcb->applicant_date_niti )?$applicationibcb->applicant_date_niti:null, [ 'class' => 'form-control', 'id' => 'applicant_date_niti' ] ) !!}
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

    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('hq_address') ? 'has-error' : ''}}">
                {!! Form::label('hq_address', 'เลขที่'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('hq_address', !empty( $applicationibcb->hq_address )?$applicationibcb->hq_address:null,['class' => 'form-control', 'readonly' => true ]) !!}
                    {!! $errors->first('hq_address', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('hq_building') ? 'has-error' : ''}}">
                {!! Form::label('hq_building', 'อาคาร/หมู่บ้าน'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('hq_building', !empty( $applicationibcb->hq_building )?$applicationibcb->hq_building:null,  ['class' => 'form-control', 'readonly' => true ]) !!}
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
                    {!! Form::text('hq_soi', !empty( $applicationibcb->hq_soi )?$applicationibcb->hq_soi:null,  ['class' => 'form-control', 'readonly' => true ]) !!}
                    {!! $errors->first('hq_soi', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('hq_moo') ? 'has-error' : ''}}">
                {!! Form::label('hq_moo', 'หมู่'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('hq_moo', !empty( $applicationibcb->hq_moo )?$applicationibcb->hq_moo:null,['class' => 'form-control', 'readonly' => true ]) !!}
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
                    {!! Form::text('hq_road', !empty( $applicationibcb->hq_road )?$applicationibcb->hq_road:null,['class' => 'form-control', 'readonly' => true ]) !!}
                    {!! $errors->first('hq_road', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('hq_subdistrict_txt') ? 'has-error' : ''}}">
                {!! Form::label('hq_subdistrict_txt', 'แขวง/ตำบล'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('hq_subdistrict_txt', !empty( $applicationibcb->HQSubdistrictName )?$applicationibcb->HQSubdistrictName:null,  ['class' => 'form-control hq_input_show', 'disabled' => true ]) !!}
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
                    {!! Form::text('hq_district_txt', !empty( $applicationibcb->HQDistrictName )?$applicationibcb->HQDistrictName:null,['class' => 'form-control hq_input_show', 'disabled' => true ]) !!}
                    {!! $errors->first('hq_district_txt', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('hq_province_txt') ? 'has-error' : ''}}">
                {!! Form::label('hq_province_txt', 'จังหวัด'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('hq_province_txt', !empty( $applicationibcb->HQProvinceName )?$applicationibcb->HQProvinceName:null,  ['class' => 'form-control hq_input_show', 'disabled' => true ]) !!}
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
                    {!! Form::text('hq_zipcode_txt', !empty( $applicationibcb->HQPostcodeName )?$applicationibcb->HQPostcodeName:null,['class' => 'form-control hq_input_show', 'disabled' => true ]) !!}
                    {!! $errors->first('hq_zipcode_txt', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {!! Form::hidden('hq_subdistrict_id', !empty( $applicationibcb->hq_subdistrict_id )?$applicationibcb->hq_subdistrict_id:null, [ 'class' => 'hq_input_show', 'id' => 'hq_subdistrict_id' ] ) !!}
        {!! Form::hidden('hq_district_id', !empty( $applicationibcb->hq_district_id )?$applicationibcb->hq_district_id:null, [ 'class' => 'hq_input_show', 'id' => 'hq_district_id' ] ) !!}
        {!! Form::hidden('hq_province_id', !empty( $applicationibcb->hq_province_id )?$applicationibcb->hq_province_id:null, [ 'class' => 'hq_input_show', 'id' => 'hq_province_id' ] ) !!}
        {!! Form::hidden('hq_zipcode', !empty( $applicationibcb->hq_zipcode )?$applicationibcb->hq_zipcode:null, [ 'class' => 'hq_input_show', 'id' => 'hq_zipcode' ] ) !!}
        {!! Form::hidden('hq_phone', !empty( $applicationibcb->hq_phone ) ? $applicationibcb->hq_phone : null, [ 'class' => 'hq_input_show', 'id' => 'hq_phone' ] ) !!}
        {!! Form::hidden('hq_fax', !empty( $applicationibcb->hq_fax ) ? $applicationibcb->hq_fax : null, [ 'class' => 'hq_input_show', 'id' => 'hq_fax' ] ) !!}
    </div>
    <hr>

    <div class="box_hp_address">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group m-b-0">
                    <label class="control-label col-md-4"><h6>ที่ตั้งหน่วยงาน</h6></label>
                    <div class="col-md-9"></div>
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
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('ibcb_name') ? 'has-error' : ''}}">
                    {!! Form::label('ibcb_name', 'ชื่อหน่วยตรวจสอบ'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('ibcb_name', !empty( $applicationibcb->ibcb_name )?$applicationibcb->ibcb_name:null,['class' => 'form-control', 'required' => true ]) !!}
                        {!! $errors->first('ibcb_name', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('ibcb_address') ? 'has-error' : ''}}">
                    {!! Form::label('ibcb_address', 'เลขที่'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('ibcb_address', !empty( $applicationibcb->ibcb_address )?$applicationibcb->ibcb_address:null,['class' => 'form-control', 'required' => true ]) !!}
                        {!! $errors->first('ibcb_address', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('ibcb_building') ? 'has-error' : ''}}">
                    {!! Form::label('ibcb_building', 'อาคาร/หมู่บ้าน'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('ibcb_building', !empty( $applicationibcb->ibcb_building )?$applicationibcb->ibcb_building:null,  ['class' => 'form-control', ]) !!}
                        {!! $errors->first('ibcb_building', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('ibcb_soi') ? 'has-error' : ''}}">
                    {!! Form::label('ibcb_soi', 'ตรอก/ซอย'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('ibcb_soi', !empty( $applicationibcb->ibcb_soi )?$applicationibcb->ibcb_soi:null,  ['class' => 'form-control' ]) !!}
                        {!! $errors->first('ibcb_soi', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('ibcb_moo') ? 'has-error' : ''}}">
                    {!! Form::label('ibcb_moo', 'หมู่'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('ibcb_moo', !empty( $applicationibcb->ibcb_moo )?$applicationibcb->ibcb_moo:null,['class' => 'form-control']) !!}
                        {!! $errors->first('ibcb_moo', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group ">
                    {!! Form::label('ibcb_address_seach', 'ค้นหาที่อยู่'.' :', ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-10">
                        {!! Form::text('ibcb_address_seach', null,  ['class' => 'form-control ibcb_address_seach', 'autocomplete' => 'off', 'data-provide' => 'typeahead', 'placeholder' => 'ค้นหาที่อยู่' ]) !!}
                        {!! $errors->first('ibcb_address_seach', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('ibcb_subdistrict_txt') ? 'has-error' : ''}}">
                    {!! Form::label('ibcb_subdistrict_txt', 'แขวง/ตำบล'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('ibcb_subdistrict_txt', !empty( $applicationibcb->IbcbSubdistrictName )?$applicationibcb->IbcbSubdistrictName:null,  ['class' => 'form-control ibcb_input_show', 'required' => true, 'readonly' => true ]) !!}
                        {!! $errors->first('ibcb_subdistrict_txt', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('ibcb_district_txt') ? 'has-error' : ''}}">
                    {!! Form::label('ibcb_district_txt', 'เขต/อำเภอ'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('ibcb_district_txt', !empty( $applicationibcb->IbcbDistrictName )?$applicationibcb->IbcbDistrictName:null,['class' => 'form-control ibcb_input_show', 'required' => true, 'readonly' => true ]) !!}
                        {!! $errors->first('ibcb_district_txt', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('ibcb_province_txt') ? 'has-error' : ''}}">
                    {!! Form::label('ibcb_province_txt', 'จังหวัด'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('ibcb_province_txt', !empty( $applicationibcb->IbcbProvinceName )?$applicationibcb->IbcbProvinceName:null,  ['class' => 'form-control ibcb_input_show', 'required' => true, 'readonly' => true ]) !!}
                        {!! $errors->first('ibcb_province_txt', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('ibcb_zipcode_txt') ? 'has-error' : ''}}">
                    {!! Form::label('ibcb_zipcode_txt', 'รหัสไปรษณีย์'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('ibcb_zipcode_txt', !empty( $applicationibcb->IbcbPostcodeName )?$applicationibcb->IbcbPostcodeName:null,['class' => 'form-control ibcb_input_show', 'required' => true, 'readonly' => true  ]) !!}
                        {!! $errors->first('ibcb_zipcode_txt', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('ibcb_phone') ? 'has-error' : ''}}">
                    {!! Form::label('ibcb_phone', 'เบอร์โทรศัพท์'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('ibcb_phone', !empty( $applicationibcb->ibcb_phone )?$applicationibcb->ibcb_phone:null,['class' => 'form-control ibcb_input_show', 'required' => true ]) !!}
                        {!! $errors->first('ibcb_phone', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('ibcb_fax') ? 'has-error' : ''}}">
                    {!! Form::label('ibcb_fax', ' เบอร์โทรสาร'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('ibcb_fax', !empty( $applicationibcb->ibcb_fax )?$applicationibcb->ibcb_fax:null,  ['class' => 'form-control ibcb_input_show', ]) !!}
                        {!! $errors->first('ibcb_fax', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {!! Form::hidden('ibcb_subdistrict_id', !empty( $applicationibcb->ibcb_subdistrict_id )?$applicationibcb->ibcb_subdistrict_id:null, [ 'class' => 'ibcb_input_show', 'id' => 'ibcb_subdistrict_id' ] ) !!}
            {!! Form::hidden('ibcb_district_id', !empty( $applicationibcb->ibcb_district_id )?$applicationibcb->ibcb_district_id:null, [ 'class' => 'ibcb_input_show', 'id' => 'ibcb_district_id' ] ) !!}
            {!! Form::hidden('ibcb_province_id', !empty( $applicationibcb->ibcb_province_id )?$applicationibcb->ibcb_province_id:null, [ 'class' => 'ibcb_input_show', 'id' => 'ibcb_province_id' ] ) !!}
            {!! Form::hidden('ibcb_zipcode', !empty( $applicationibcb->ibcb_zipcode )?$applicationibcb->ibcb_zipcode:null, [ 'class' => 'ibcb_input_show', 'id' => 'ibcb_zipcode' ] ) !!}
        </div>
        <hr>
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
                        {!! Form::text('co_name', !empty( $applicationibcb->co_name )?$applicationibcb->co_name:null,['class' => 'form-control co_input_show', 'required' => true ]) !!}
                        {!! $errors->first('co_name', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required {{ $errors->has('co_position') ? 'has-error' : ''}}">
                    {!! Form::label('co_position', 'ตำแหน่ง'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('co_position', !empty( $applicationibcb->co_position )?$applicationibcb->co_position:null,  ['class' => 'form-control co_input_show', 'required' => true ]) !!}
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
                        {!! Form::text('co_mobile', !empty( $applicationibcb->co_mobile )?$applicationibcb->co_mobile:null,['class' => 'form-control co_input_show', 'required' => true ]) !!}
                        {!! $errors->first('co_mobile', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('co_phone') ? 'has-error' : ''}}">
                    {!! Form::label('co_phone', ' โทรศัพท์'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('co_phone', !empty( $applicationibcb->co_phone )?$applicationibcb->co_phone:null,  ['class' => 'form-control co_input_show' ]) !!}
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
                        {!! Form::text('co_fax', !empty( $applicationibcb->co_fax )?$applicationibcb->co_fax:null,['class' => 'form-control co_input_show', 'required' => true ]) !!}
                        {!! $errors->first('co_fax', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group required{{ $errors->has('co_email') ? 'has-error' : ''}}">
                    {!! Form::label('co_email', ' อีเมล'.' :', ['class' => 'col-md-4 control-label']) !!}
                    <div class="col-md-8">
                        {!! Form::text('co_email', !empty( $applicationibcb->co_email )?$applicationibcb->co_email:null,  ['class' => 'form-control co_input_show', 'required' => true ]) !!}
                        {!! $errors->first('co_email', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

</fieldset>

<fieldset class="white-box box_audit_type_1">
    <legend class="legend"><h5>ใบรับรองระบบงานตามมาตรฐาน</h5></legend>

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
            <div class="form-group required">
                {!! Form::label('certificate_issue_date', 'วันที่ออกใบรับรอง'.' :', ['class' => 'col-md-2 control-label']) !!}
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
                {!! Form::label('certificate_expire_date', 'วันที่หมดอายุใบรับรอง'.' :', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-4">
                    <div class="input-group">
                        {!! Form::text('certificate_expire_date', null, ['class' => 'form-control mydatepicker', 'placeholder'=>'dd/mm/yyyy']); !!}
                        <span class="input-group-addon"><i class="icon-calender"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group required">
                {!! Form::label('certificate_std_export', 'มอก. รับรองระบบงาน'.' :', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-6">
                    {!! Form::select('certificate_std_export', App\Models\Bsection5\Standard::pluck('title', 'id')->all() , null, ['class' => 'form-control', 'placeholder'=>'เลือกมอก. รับรองระบบงาน']); !!}
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success show_tag_a" type="button" id="btn_cer_add"><i class="icon-plus"></i> เพิ่ม</button>
                </div>
            </div>
        </div>
    </div>

    @include ('section5.application-ib-cb.modals.modal-certificate')
    <hr>

    <div class="row">
        <div class="col-md-12">

            <div class="table-responsive">
                <table class="table table-bordered certificate-repeater" id="table-certificate">
                    <thead>
                        <tr>
                            <th class="text-center" width="25%">ใบรับรองเลขที่</th>
                            <th class="text-center" width="20%">วันที่ออก</th>
                            <th class="text-center" width="20%">วันที่หมด</th>
                            <th class="text-center" width="30%">มอก.</th>
                            <th class="text-center" width="5%">ลบ</th>
                        </tr>
                    </thead>
                    <tbody data-repeater-list="repeater-certificate" class="text-center">

                        @if( isset($applicationibcb->id) )

                            @php
                                $certify = App\Models\Section5\ApplicationIbcbCertify::where('application_id', $applicationibcb->id )->get();
                                $url_center = isset( HP::getConfig()->url_center )?HP::getConfig()->url_center:null;
                            @endphp

                            @foreach ( $certify as $Icertify )
                                @php
                                    $tis_standard = $Icertify->tis_standard;
                                @endphp
                                <tr data-repeater-item>
                                    <td>
                                      
                                        <input type="hidden" name="certificate_id" class="certificate_id" value="{!! !empty($Icertify->certificate_id)?$Icertify->certificate_id:null !!}">
                                        <input type="hidden" class="certificate_no" name="certificate_no" value="{!! !empty($Icertify->certificate_no)?$Icertify->certificate_no:null !!}">
                                        <a href="{!! url($url_center.'/api/v1/certificate?cer='.(!empty($Icertify->certificate_no)?$Icertify->certificate_no:null)) !!}"  target="_blank"><span class="text-info">  {!! !empty($Icertify->certificate_no)?$Icertify->certificate_no:null !!}</span></a>
                                    </td>
                                    <td>
                                        {!! !empty($Icertify->certificate_start_date)?HP::revertDate($Icertify->certificate_start_date):null !!}
                                        <input type="hidden" name="certificate_start_date" value="{!! !empty($Icertify->certificate_start_date)?HP::revertDate($Icertify->certificate_start_date):null !!}">
                                    </td>
                                    <td>
                                        {!! !empty($Icertify->certificate_end_date)?HP::revertDate($Icertify->certificate_end_date):null !!}
                                        <input type="hidden" name="certificate_end_date" value="{!! !empty($Icertify->certificate_end_date)?HP::revertDate($Icertify->certificate_end_date):null !!}">
                                    </td>
                                    <td>
                                        {!! !empty($tis_standard->title)?$tis_standard->title:null !!}
                                        <input type="hidden" name="certificate_std_id" value="{!! !empty($Icertify->certificate_std_id)?$Icertify->certificate_std_id:null !!}">
                                        <input type="hidden" name="cer_id" value="{!! $Icertify->id !!}">
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" type="button" data-repeater-delete> <i class="fa fa-minus"></i></button>
                                        <input type="hidden" name="certificate_table" value="{!! !empty($Icertify->certificate_table)?$Icertify->certificate_table:null !!}">
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
    <legend class="legend"><h5>ข้อมูลขอรับบริการ</h5></legend>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label col-md-1"></label>
                <div class="col-md-11">
                    <p class="h5">ยื่นคำขอต่อสำนักงานมาตรฐานภัณฑ์อุตสาหกรรม กระทรวงอุตสาหกรรมเพื่อรับการแต่งตั้งเป็นผู้ตรวจสอบผลิตภัณฑ์อุตสาหกรรม ตามมาตรา 5 แห่งพระราชบัญญัติมาตรฐานผลิตภัณฑ์อุตสาหกรรม พ.ศ. 2511 และแก้ไขเพิ่มเติมขอบข่ายที่ขอรับการตั้งแต่ง คือ</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-12">
            <div class="form-group required">
                {!! Form::label('scope_branches_group', 'สาขาผลิตภัณฑ์'.' :', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::select('scope_branches_group', App\Models\Basic\BranchGroup::pluck('title', 'id')->all(), null, ['class' => 'form-control', 'placeholder'=>'เลือกสาขาผลิตภัณฑ์']); !!}
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group required">
                {!! Form::label('scope_branches', 'รายสาขา'.' :', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::select('scope_branches[]', [] , null, ['class' => 'select2-multiple scope_branches_multiple', 'id' => 'scope_branches_multiple',  'data-placeholder'=>'เลือกรายสาขา', 'multiple' => 'multiple']); !!}
                    <p class="scope-loading-text" id="scope_branches_loading_text">กำลังโหลดข้อมูลรายสาขา ...</p>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group required">
                {!! Form::label('scope_branches_tis', 'เลขที่ มอก.'.' :', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::select('scope_branches_tis[]', [] , null, ['class' => 'select2-multiple scope_branches_tis', 'id' => 'scope_branches_tis',  'data-placeholder'=>'เลือกมาตรฐาน', 'multiple' => 'multiple', 'disabled' => false]); !!}
                    <p class="scope-loading-text" id="scope_branches_tis_loading_text">กำลังโหลดข้อมูลเลขที่ มอก. ...</p>
                    <p class="scope-tis-summary-text" id="scope_tis_summary_text"></p>
                </div>
                <div class="col-md-2">
                    <div class="form-group" style="padding-top: 10px;">
                        {!! Form::checkbox('check_all_scope_branches_tis', '1', null, ['class' => 'form-control check', 'data-checkbox' => 'icheckbox_flat-blue', 'id'=>'check_all_scope_branches_tis','required' => false]) !!}
                        <label for="check_all_scope_branches_tis" class="font-medium-1">&nbsp;&nbsp; ทุกรายการ</label>
                        <p class="text-muted" style="margin-top: 6px; margin-bottom: 0;">เลือกได้เมื่อมีรายการเลขที่ มอก.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('scope_isic_no', 'ISIC NO'.' :', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-6">
                    {!! Form::text('scope_isic_no', null, ['class' => 'form-control', 'id' => 'scope_isic_no',]); !!}
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success show_tag_a" type="button" id="btn_branche_add"><i class="icon-plus"></i> เพิ่มข้อมูลขอรับบริการ</button>
                    <p class="text-danger" style="margin-top: 8px; margin-bottom: 0;">กรุณากดปุ่มนี้เพื่อเพิ่มรายการลงในตารางด้านล่าง</p>
                </div>
            </div>
        </div>

    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered scope-repeater" id="table-scope">
                    <thead>
                        <tr>
                            <th class="text-center" width="1%">รายการที่</th>
                            <th class="text-center" width="32%">สาขาผลิตภัณฑ์</th>
                            <th class="text-center" width="32%">รายสาขา</th>
                            <th class="text-center" width="15%">ISIC NO</th>
                            <th class="text-center" width="15%">มาตรฐาน มอก. เลขที่</th>
                            <th class="text-center" width="5%">ลบ</th>
                        </tr>
                    </thead>
                    <tbody data-repeater-list="repeater-scope" class="text-left" id="box_list_scpoe">
                        @if( isset($applicationibcb->id) )
                            @php
                                $scope = App\Models\Section5\ApplicationIbcbScope::where('application_id', $applicationibcb->id )->get();
                            @endphp

                            @foreach ( $scope as $ks => $Iscope )
                                @php
                                    $scopes_details =  $Iscope->scopes_details;
                                    $scopes_ties =  $Iscope->scopes_tis;
                                @endphp
                                <tr data-repeater-item>
                                    <td class="no text-center">{!! $ks+1 !!}</td>
                                    <td>
                                        {!! $Iscope->BranchGroupTitle !!}
                                        <input type="hidden" class="branch_group_id" name="branch_group_id" value="{!! !empty($Iscope->branch_group_id)?$Iscope->branch_group_id:null  !!}" data-name="branch_group_id">
                                        <input type="hidden" name="scope_id" value="{!! !empty($Iscope->id)?$Iscope->id:null  !!}"  data-name="scope_id">
                                    </td>
                                    <td>
                                        {!! !empty($scopes_details)?$Iscope->ScopeBranchs:null  !!}
                                        @foreach($scopes_details as $scopes_detail)
                                            <input type="hidden" name="branch_id" value="{!! $scopes_detail->branch_id !!}" class="input_array" data-name="branch_id">
                                        @endforeach
                                    </td>
                                    <td>
                                        {!! !empty($Iscope->isic_no)?$Iscope->isic_no:'-'  !!}
                                        <input type="hidden" name="isic_no" value="{!! !empty($Iscope->isic_no)?$Iscope->isic_no:null  !!}" data-name="isic_no">
                                    </td>
                                    <td class="text-ellipsis">
                                        <a class="open_scope_branches_tis_details" href="javascript:void(0)" title="คลิกดูรายละเอียด">{{ !empty($scopes_ties)?implode(', ', $scopes_ties->pluck('tis_no')->toArray()):'-' }}</a>
                                        @foreach($scopes_ties as $scopes_tis)
                                            @php
                                                $branch_title = !empty($scopes_tis->application_ibcb_scope_detail->bs_branch->title) ? $scopes_tis->application_ibcb_scope_detail->bs_branch->title : '' ;
                                            @endphp
                                            <input type="hidden" value="{!! $scopes_tis->tis_name !!}" data-tis_no="{!! $scopes_tis->tis_no !!}" data-branch_title="{!! $branch_title !!}" class="tis_details">
                                            <input type="hidden" name="tis_id" value="{!! $scopes_tis->tis_id !!}" class="input_array" data-name="tis_id">
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-danger btn_remove" type="button" data-repeater-delete> <i class="fa fa-minus"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include ('section5.application-ib-cb.modals.modal-scope-branches-tis-details')

</fieldset>

<fieldset class="white-box">
    <legend class="legend"><h5>รายชื่อผู้ตรวจที่ผ่านการแต่งตั้ง</h5></legend>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <button class="btn btn-primary show_tag_a" type="button" id="btn_modal_inspectors"><i class="fa fa-search"></i> ค้นหาผู้ตรวจ</button>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered inspectors-repeater" id="table-inspectors">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th width="25%" class="text-center">ชื่อผู้ตรวจ</th>
                            <th width="15%" class="text-center">เลขบัตร</th>
                            <th width="45%" class="text-center">สาขาผลิตภัณฑ์</th>
                            <th width="10%" class="text-center">ประเภทผู้ตรวจ</th>
                            <th class="text-center" width="5%">ลบ</th>
                        </tr>
                    </thead>
                    <tbody data-repeater-list="repeater-inspectors" class="text-left">

                        @php
                            //ดึงชื่อผู้ตรวจที่ลงทะเบียนไว้กับผู้ยื่นรายนี้
                            $inspector_owns = !empty($user_regitor)
                                ? App\Models\Section5\Inspectors::where('agency_taxid', $user_regitor->tax_number)->get()
                                : collect([]);
                            foreach ($inspector_owns as $inspector) {
                                $inspector->data_scopes = $inspector->ScopeDataSet;
                            }
                        @endphp

                        @if( isset($applicationibcb->id) ) {{-- แก้ไข ---}}

                            @php
                                $inspectors = App\Models\Section5\ApplicationIbcbInspectors::where('application_id', $applicationibcb->id )->get();
                            @endphp

                            @foreach ($inspectors as $ki => $Insp)

                                @php
                                    $branch_group =  $Insp->scopes()->select('branch_group_id')->groupBy('branch_group_id')->pluck('branch_group_id')->toArray();
                                @endphp

                                <tr data-repeater-item>
                                    <td class="ins_no text-center">{!! $ki+1 !!}</td>
                                    <td>
                                        {!! !empty($Insp->InspectorFullName)?$Insp->InspectorFullName:null  !!}
                                        <input type="hidden" name="insp_id" value="{!! $Insp->id !!}">
                                        <input type="hidden" class="inspector_id" name="inspector_id" value="{!! !empty($Insp->inspector_id)?$Insp->inspector_id:null !!}">
                                        <input type="hidden" name="inspector_prefix" value="{!! !empty($Insp->inspector_prefix)?$Insp->inspector_prefix:null !!}">
                                        <input type="hidden" name="inspector_first_name" value="{!! !empty($Insp->inspector_first_name)?$Insp->inspector_first_name:null !!}">
                                        <input type="hidden" name="inspector_last_name" value="{!! !empty($Insp->inspector_last_name)?$Insp->inspector_last_name:null !!}">
                                        <input type="hidden" name="inspector_taxid" value="{!! !empty($Insp->inspector_taxid)?$Insp->inspector_taxid:null !!}">
                                        <input type="hidden" name="inspector_type" value="{!! !empty($Insp->inspector_type)?$Insp->inspector_type:null !!}">
                                        <input type="hidden" name="branch_group_id" value="{!! (count($branch_group) > 0 )?implode(',', $branch_group):null !!}">
                                        {!! !empty($Insp->ScopeBranchInput)?$Insp->ScopeBranchInput:null  !!}
                                    </td>
                                    <td>
                                        {!! !empty($Insp->inspector_taxid)?$Insp->inspector_taxid:null  !!}
                                    </td>
                                    <td>
                                        {!! !empty($Insp->ScopeShow)?$Insp->ScopeShow:null !!}
                                    </td>
                                    <td>
                                        {!! $Insp->inspector_type==1 ? 'ผู้ตรวจของหน่วยตรวจ' : 'ผู้ตรวจอิสระ' !!}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-danger" type="button" data-repeater-delete> <i class="fa fa-minus"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        @else {{-- เพิ่ม --}}

                            @foreach ($inspector_owns as $ki => $inspector)

                                @php
                                    $branch_group = collect($inspector->data_scopes)->pluck('branch_group_id')->toArray();
                                @endphp

                                <tr data-repeater-item>
                                    <td class="ins_no text-center">{!! $ki+1 !!}</td>
                                    <td>
                                        {!! $inspector->AgencyFullName !!}
                                        <input type="hidden" class="inspector_id" name="inspector_id" value="{!! $inspector->id !!}">
                                        <input type="hidden" name="inspector_prefix" value="{!! $inspector->inspectors_prefix !!}">
                                        <input type="hidden" name="inspector_first_name" value="{!! $inspector->inspectors_first_name !!}">
                                        <input type="hidden" name="inspector_last_name" value="{!! $inspector->inspectors_last_name !!}">
                                        <input type="hidden" name="inspector_taxid" value="{!! $inspector->inspectors_taxid !!}">
                                        <input type="hidden" name="inspector_type" value="1">
                                        <input type="hidden" name="branch_group_id" value="{!! (count($branch_group) > 0 ) ? implode(',', $branch_group) : null !!}">
                                        {!! !empty($inspector->ScopeBranchInput)?$inspector->ScopeBranchInput:null !!}
                                    </td>
                                    <td>
                                        {!! !empty($inspector->inspectors_taxid) ? $inspector->inspectors_taxid : null !!}
                                    </td>
                                    <td>
                                        {!! !empty($inspector->ScopeShow) ? $inspector->ScopeShow : null !!}
                                    </td>
                                    <td>
                                        ผู้ตรวจของหน่วยตรวจ
                                    </td>
                                    <td class="text-center">
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

    @include ('section5.application-ib-cb.modals.modal-inspector')


</fieldset>

<fieldset class="scheduler-border">
    <legend class="scheduler-border"><h5>เอกสารแนบ</h5></legend>
    @php

        if( isset($applicationibcb->id) && !empty($applicationibcb->config_evidencce) ){
            $configs_evidences = json_decode($applicationibcb->config_evidencce);
        }else{
            $configs_evidences = DB::table((new App\Models\Config\ConfigsEvidence)->getTable().' AS evidences')
                                    ->leftjoin((new App\Models\Config\ConfigsEvidenceGroup)->getTable().' AS groups', 'groups.id', '=', 'evidences.evidence_group_id')
                                    ->where('groups.id', 1)
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

                    if( isset($applicationibcb->id) ){
                        $attachment = App\AttachFile::where('ref_table', (new App\Models\Section5\ApplicationIbcb )->getTable() )
                                        ->where('ref_id', $applicationibcb->id )
                                        ->when($evidences->id, function ($query, $setting_file_id){
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

                                @if(!empty($attachment) && HP::checkFileStorage($attachment->url))
                                    <div class="col-md-4" >
                                        {{-- ไฟล์อยู่บน NAS เท่านั้น ไม่ copy มา cache ในเครื่องเว็บ (HP::getFileStorage() ทำ URL
                                             พังเพราะ APP_URL ใน .env ขาด /public) — ลิงก์ตรงจาก FILESYSTEM_ROOT_URL แทน --}}
                                        <a href=" {!! rtrim(env('FILESYSTEM_ROOT_URL', ''), '/').'/'.ltrim(preg_replace('/\/+/', '/', $attachment->url), '/') !!}" target="_blank" title="{!! !empty($attachment->filename) ? $attachment->filename : 'ไฟล์แนบ' !!}">
                                            <i class="fa fa-folder-open fa-lg" style="color:#FFC000;" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div class="col-md-2" >
                                        <a class="btn btn-danger btn-xs show_tag_a" href="{!! url('funtions/get-delete/files/'.($attachment->id).'/'.base64_encode('request-section-5/application-ibcb/'.$applicationibcb->id.'/edit') ) !!}" title="ลบไฟล์"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
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
                if( isset($applicationibcb->id) ){
                    $file_other = App\AttachFile::where('section', 'evidence_file_other')->where('ref_table', (new App\Models\Section5\ApplicationIbcb )->getTable() )->where('ref_id', $applicationibcb->id )->get();
                }
            @endphp

            @foreach ( $file_other as $attach )
                @if(HP::checkFileStorage($attach->url))

                    <div class="form-group">
                        {!! HTML::decode(Form::label('personfile', 'เอกสารเพิ่มเติม : ', ['class' => 'col-md-5 control-label text-left'])) !!}
                        <div class="col-md-3">
                            {!! Form::text('file_documents', ( !empty($attach->caption) ? $attach->caption:null) , ['class' => 'form-control' , 'placeholder' => 'คำอธิบาย', 'disabled' => true]) !!}
                        </div>
                        <div class="col-md-3">
                            {{-- ไฟล์อยู่บน NAS เท่านั้น ไม่ copy มา cache ในเครื่องเว็บ (HP::getFileStorage() ทำ URL
                                 พังเพราะ APP_URL ใน .env ขาด /public) — ลิงก์ตรงจาก FILESYSTEM_ROOT_URL แทน --}}
                            <a href="{!! rtrim(env('FILESYSTEM_ROOT_URL', ''), '/').'/'.ltrim(preg_replace('/\/+/', '/', $attach->url), '/') !!}" target="_blank" title="{!! !empty($attach->filename) ? $attach->filename : 'ไฟล์แนบ' !!}">
                                <i class="fa fa-folder-open fa-lg" style="color:#FFC000;" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="col-md-1" >
                            <a class="btn btn-danger btn-xs show_tag_a " href="{!! url('funtions/get-delete/files/'.($attach->id).'/'.base64_encode(request()->path()) ) !!}" title="ลบไฟล์"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    
                @endif
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

@if( ( (isset( $applicationibcb->edited ) && $applicationibcb->edited == true  || isset( $applicationibcb->show ) && $applicationibcb->show == true ) ) && ($applicationibcb->application_ibcb_accepts()->count() > 0) )
    @include ('section5.application-ib-cb.history')
@endif

{!! Form::hidden('type_save', null , ['id' => 'type_save' ]) !!}

<center>
    <div class="form-group">
        <div class="col-md-offset-4 col-md-4">

            @if( !isset( $applicationibcb->id ) || empty($applicationibcb->application_status) || in_array($applicationibcb->application_status, [0]) )
                <button class="btn btn-info show_tag_a" type="button" id="btn_draft">
                    <i class="fa fa-file-o"></i> ฉบับร่าง
                </button>
            @endif

            <button class="btn btn-primary show_tag_a" type="button" id="btn_submit">
                <i class="fa fa-paper-plane"></i>   @if(isset($applicationibcb) && !empty($applicationibcb->id) && $applicationibcb->application_status!=0) บันทึกแก้ไข @else บันทึก @endif
            </button>

            <a class="btn btn-default show_tag_a" href="{{ url('/request-section-5/application-ibcb') }}">
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

    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    {{-- <script src="{{asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')}}"></script> --}}
    <script src="{{asset('plugins/components/datatables/jquery.dataTables.min.js')}}"></script>


    <script>
        var list_isic_no =  $.parseJSON('{!! json_encode(App\Models\Basic\BranchGroup::whereNotNull('isic_no')->select('isic_no', 'id')->get()->pluck('isic_no', 'id')->toArray()) !!}');
        var inspector_owns = $.parseJSON('{!! json_encode($inspector_owns->toArray()) !!}');//รายชื่อผู้ตรวจที่สังกัดหน่วยงานที่ยื่นคำขอนี้
        var tr_inspector_own = $('#table-inspectors tbody').html();//รายชื่อผู้ตรวจที่สังกัดหน่วยงานที่ยื่นคำขอนี้ เป็น html

        $(document).ready(function () {

            $('#btn_submit').click(function (e) {

                if (hasPendingScopeSelection()) {
                    Swal.fire({
                        type: 'warning',
                        title: 'กรุณากดปุ่ม "เพิ่มข้อมูลขอรับบริการ" ก่อนบันทึกข้อมูล',
                        width: 500
                    });
                    return false;
                }

                $('#type_save').val('save');

                var audit_type =  ($("input[name=audit_type]:checked").val() == 1 )?'1':'2';

                var certify =  $('#table-certificate').find(".certificate_no").map(function(){return $(this).val(); }).get();
                var scope =  $('#table-scope').find(".branch_group_id").map(function(){return $(this).val(); }).get();
                var inspector =  $('#table-inspectors').find(".inspector_id").map(function(){return $(this).val(); }).get();

                var applicant_taxid = $('#applicant_taxid').val();


                if( $('#applicant_request_type_1').is(':checked',true) ){
                    $('.box_ibcb').find('input, select, hidden, checkbox').prop('required', false);
                }

                if( applicant_taxid == '' ){
                    Swal.fire({
                        type: 'error',
                        title: 'ไม่สามารถบันทึกได้ เนื่องจากไม่มีเลขประจำตัวผู้เสียภาษีอากร',
                        // html: '<p class="h4"></p>',
                        width: 500
                    });
                }else if( audit_type == '1' && certify.length == 0){
                    Swal.fire({
                        type: 'error',
                        title: 'กรุณาเลือกใบรับรองระบบงานตามมาตรฐาน',
                        // html: '<p class="h4"></p>',
                        width: 500
                    });
                }else if( scope.length == 0){
                    Swal.fire({
                        type: 'error',
                        title: 'กรุณาเลือกข้อมูลขอรับบริการ',
                        // html: '<p class="h4"></p>',
                        width: 500
                    });
                }else if( inspector.length == 0){
                    Swal.fire({
                        type: 'error',
                        title: 'กรุณาเลือกรายชื่อผู้ตรวจที่ผ่านการแต่งตั้ง',
                        // html: '<p class="h4"></p>',
                        width: 500
                    });
                }else{
                    $('#from_box').submit();
                }

            });

            $('#btn_draft').click(function (e) {

                $('#type_save').val('draft');

                if( $('#applicant_request_type_1').is(':checked',true) ){
                    $('.box_ibcb').find('input, select, hidden, checkbox').prop('required', false);
                }

                var applicant_taxid = $('#applicant_taxid').val();

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

            //
            $('.repeater-form').repeater();

            $('.certificate-repeater').repeater({
                show: function () {
                    $(this).slideDown();
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

            $('#scope_branches_multiple, #scope_branches_tis').on('change', function () {
                toggleSubmitButtonState();
            });

            toggleSubmitButtonState();
            updateScopeTisAllToggleState();

            // $('.scope-repeater').repeater({
            //     show: function () {
            //         $(this).slideDown();
            //         resetOrderNo();

            //     },
            //     hide: function (deleteElement) {
            //         if (confirm('คุณต้องการลบแถวนี้ใช่หรือไม่ ?')) {
            //             $(this).slideUp(deleteElement);
            //             resetOrderNo();
            //         }
            //     }
            // });


            $('.inspectors-repeater').repeater({
                show: function () {
                    $(this).slideDown();
                    resetInsNo2();
                },
                hide: function (deleteElement) {
                    if (confirm('คุณต้องการลบแถวนี้ใช่หรือไม่ ?')) {
                        $(this).slideUp(deleteElement);
                        resetInsNo2();
                    }
                }
            });


            jQuery('.mydatepicker').datepicker({
                toggleActive: true,
                language:'th-th',
                format: 'dd/mm/yyyy',
            });


            $("#ibcb_address_seach").select2({
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

            $("#ibcb_address_seach").on('change', function () {
                $.ajax({
                    url: "{!! url('/funtions/get-addreess/') !!}" + "/" + $(this).val()
                }).done(function( jsondata ) {
                    if(jsondata != ''){

                        $('#ibcb_subdistrict_txt').val(jsondata.sub_title);
                        $('#ibcb_district_txt').val(jsondata.dis_title);
                        $('#ibcb_province_txt').val(jsondata.pro_title);
                        $('#ibcb_zipcode_txt').val(jsondata.zip_code);

                        $('#ibcb_subdistrict_id').val(jsondata.sub_ids);
                        $('#ibcb_district_id').val(jsondata.dis_id);
                        $('#ibcb_province_id').val(jsondata.pro_id);
                        $('#ibcb_zipcode').val(jsondata.zip_code);

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

            $('#btn_std_export').click(function (e) {
                $('#Mcertificate').modal('show');
            });

            var tableCer = $('#myTableCertificate').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    "url": '{!! url('/request-section-5/application-ibcb/getDataCertificate') !!}',
                    "dataType": "json",
                    "data": function (d) {
                        d.application_type = $("input[name=application_type]:checked").val();
                        d.table = (( $("input[name=application_type]:checked").val() == 1 )?'app_certi_ib_export':'app_certi_cb_export');
                        d.applicant_taxid = $('#applicant_taxid').val();
                        d.search = $('#modal_cer_search').val();
                    }
                },
                columns: [
                    { data: 'DT_Row_Index', searchable: false, orderable: false},
                    { data: 'cb_name', name: 'cb_name' },
                    { data: 'formula', name: 'formula' },
                    { data: 'certificate', name: 'certificate' },
                    { data: 'date_start', name: 'date_start' },
                    { data: 'date_end', name: 'date_end' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action' },
                ],
                columnDefs: [

                ],
                fnDrawCallback: function() {

                }
            });

            $('input[name=application_type]').on('ifChecked', function(event){
                tableCer.draw();

                if( $(this).val() == 2 ){
                    $('#audit_type_1').iCheck('check');
                    BoxAuditType1();
                    $('.lable_audit_type2').hide();
                    LoadStdType();
                    LoadListIBCB();
                }else{
                    $('#audit_type_2').iCheck('check');
                    BoxAuditType1();
                    $('.lable_audit_type2').show();
                    LoadStdType();
                    LoadListIBCB();
                }

            });
            LoadStdType();

            $('input[name=audit_type]').on('ifChecked', function(event){
                BoxAuditType1();
            });
            BoxAuditType1();

            $("body").on('keyup', '#modal_cer_search', function () {
                tableCer.draw();
            });

            $('body').on('click','.btn_select_cer', function () {

                // var std =  $('#certificate_std_export').val();
                var cerno =  $('#certificate_cerno_export');
                var issue_date =  $('#certificate_issue_date');
                var expire_date =  $('#certificate_expire_date');

                // var std =  $('#certificate_std_export').val();

                var Mcer_no = $(this).data('certificate_no');
                var Mdate_start = $(this).data('date_start');
                var Mdate_end = $(this).data('date_end');
                var Mid = $(this).data('id');
                var Mtable = $(this).data('table');

                $(cerno).val(Mcer_no);
                $(issue_date).val(Mdate_start);
                $(expire_date).val(Mdate_end);

                $( cerno ).attr( "data-id", (checkNone(Mid)?Mid:'') );
                $( cerno ).attr( "data-table", (checkNone(Mtable)?Mtable:'') );

                $('#btn_std_export').val(2);

                ShowInputCertificate();

                $('#Mcertificate').modal('hide');

            });

            $('body').on('click','#btn_cer_add', function (e) {

                var std =  $('#certificate_std_export').val();
                var std_txt = $('#certificate_std_export').find('option:selected').text();
                var cerno =  $('#certificate_cerno_export').val();
                var issue_date =  $('#certificate_issue_date').val();
                var expire_date =  $('#certificate_expire_date').val();

                if( !checkNone(std) ){
                    alert("กรุณากรอก มอก. รับรองระบบงาน !");
                }else if( !checkNone(cerno) ){
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

                    var certificate_std_id  = '<input type="hidden" name="certificate_std_id" value="'+std+'">';
                    var certificate_id  = '<input type="hidden" class="certificate_id" name="certificate_id" value="'+(checkNone(id)?id:'')+'">';
                    var certificate_no  = '<input type="hidden" class="certificate_no" name="certificate_no" value="'+(checkNone(cerno)?cerno:'')+'">';
                    var certificate_start_date  = '<input type="hidden" name="certificate_start_date" value="'+issue_date+'">';
                    var certificate_end_date  = '<input type="hidden" name="certificate_end_date" value="'+expire_date+'">';
                    var certificate_table  = '<input type="hidden" name="certificate_table" value="'+(checkNone(table)?table:'')+'">';

                    var btn = '<button class="btn btn-sm btn-danger" type="button" data-repeater-delete> <i class="fa fa-minus"></i></button>';

                    var values =  $('#table-certificate').find(".certificate_id").map(function(){return $(this).val(); }).get();

                    if(checkNone(id)){
                        var url_center = '{!! isset( HP::getConfig()->url_center )?HP::getConfig()->url_center:'' !!}';
                        var tag_a = '<a href="'+(url_center)+'/api/v1/certificate?cer='+(cerno)+'"  target="_blank"><span class="text-info">'+(cerno)+'</span></a>';
                    }else{
                        var tag_a = cerno;
                    }

                    if( checkNone(id) ){
                        if( values.indexOf( String(id) ) == -1 ){
                            var tr_ = '<tr data-repeater-item>';
                                tr_ += '<td>'+tag_a+' '+ certificate_id + certificate_no +'</td>';
                                tr_ += '<td>'+issue_date+' '+ certificate_start_date +'</td>';
                                tr_ += '<td>'+expire_date+' '+ certificate_end_date +'</td>';
                                tr_ += '<td>'+std_txt+' '+certificate_std_id+'</td>';
                                tr_ += '<td>'+btn+' '+ certificate_table +'</td>';
                                tr_ += '</tr>';

                            $('#table-certificate tbody').append(tr_);
                            $('.certificate-repeater').repeater();
                        }
                    }else{
                        var tr_ = '<tr data-repeater-item>';
                            tr_ += '<td>'+tag_a+' '+ certificate_id + certificate_no +'</td>';
                            tr_ += '<td>'+issue_date+' '+ certificate_start_date +'</td>';
                            tr_ += '<td>'+expire_date+' '+ certificate_end_date +'</td>';
                            tr_ += '<td>'+std_txt+' '+certificate_std_id+'</td>';
                            tr_ += '<td>'+btn+' '+ certificate_table +'</td>';
                            tr_ += '</tr>';

                        $('#table-certificate tbody').append(tr_);
                        $('.certificate-repeater').repeater();

                    }




                    setTimeout(function(){

                        $('#certificate_std_export').val('').select2();
                        $('#certificate_cerno_export').val('');
                        $('#certificate_issue_date').val('');
                        $('#certificate_expire_date').val('');

                        $('#certificate_cerno_export').removeAttr( "data-id" );
                        $('#certificate_cerno_export').removeAttr( "data-table" );

                        $('#btn_std_export').val(1);
                        ShowInputCertificate();
                    }, 100);
                }

            });

            $('#scope_branches_group').change(function (e) {
                LoadBranche();
                //LoadBrancheTis();
                $('#scope_isic_no').val('');
                if( checkNone($(this).val()) && checkNone(list_isic_no[ $(this).val() ])){
                    $('#scope_isic_no').val(list_isic_no[ $(this).val() ]);
                }
            });

            //เมื่อเลือกรายสาขา
            $('#scope_branches_multiple').change(function(event) {
                LoadBrancheTis();
            });

            $('#check_all_scope_branches_tis').on('ifChecked', function (event){
                if (!canSelectAllScopeTis()) {
                    $('#scope_branches_tis').data('all-selected', 0);
                    $('#check_all_scope_branches_tis').iCheck('uncheck');
                    Swal.fire({
                        type: 'warning',
                        title: 'กรุณาเลือกรายการเลขที่ มอก. ก่อน',
                        width: 500
                    });
                    return false;
                }

                $('#scope_branches_tis').data('all-selected', 1);
                $('#scope_branches_tis').val('').trigger("change");
            });

            $('#check_all_scope_branches_tis').on('ifUnchecked', function (event){
                $('#scope_branches_tis').data('all-selected', 0);
                $('#scope_branches_tis').val('').trigger("change");
            });

            $('#scope_branches_tis').on('change', function (e) {
                if ($(this).data('skip-change-handler') === 1) {
                    $(this).data('skip-change-handler', 0);
                    updateScopeTisSelectDisplay();
                    return;
                }

                var tis_length = $(this).find('option').length;
                var tis_length_selected = $(this).find('option:selected').length;
                if(tis_length != 0 && !isScopeTisAllSelected()){
                    if(tis_length == tis_length_selected){
                        $('#check_all_scope_branches_tis').iCheck('check');
                    }else{
                        $('#check_all_scope_branches_tis').iCheck('uncheck');
                    }
                }

                updateScopeTisSelectDisplay();
            });

            $('#btn_branche_add').click(function (e) {

                var branches_group =  $('#scope_branches_group').val();
                var branches =  $('#scope_branches_multiple').val();
                var branches_tis =  $('#scope_branches_tis').val();
                var is_all_tis = isScopeTisAllSelected();
                var isic_no =  $('#scope_isic_no').val();

                if( !checkNone(branches_group) ){
                    alert("กรุณาเลือกสาขาผลิตภัณฑ์");
                }else if( !checkNone(branches)  ){
                    alert("กรุณาเลือกรายสาขา");
                }else if( !is_all_tis && !checkNone(branches_tis) ){
                    alert("กรุณาเลือกเลขที่ มอก.");
                }else{

                    var branches_txt = [];
                    var branch_input = '';
                    $( "#scope_branches_multiple option:selected" ).each(function( index, data ) {
                        branches_txt.push($(data).text());
                        branch_input += '<input type="hidden" name="branch_id" value="'+$(data).val()+'" class="input_array" data-name="branch_id">';
                    });

                    var tis_arr = [];
                    var tis_details = '';
                    var tis_no_input  = '';
                    var tis_detail_arr = [];
                    $( is_all_tis ? "#scope_branches_tis option" : "#scope_branches_tis option:selected" ).each(function( index, data ) {

                        var tis_cut = $(data).text().split(':');
                        tis_detail_arr.push( $(data).text() );
                        tis_arr.push( checkNone(tis_cut[0])?tis_cut[0]:'' );
                        tis_details  += '<input type="hidden" data-tis_no="'+( checkNone(tis_cut[0])?tis_cut[0]:'' )+'" value="'+(checkNone(tis_cut[1])?tis_cut[1]:'')+'" data-branch_title="'+$(data).data('branch')+'" class="tis_details">';
                        tis_no_input  += '<input type="hidden" name="tis_id" value="'+$(data).val()+'" class="input_array" data-name="tis_id">';
                    });

                    var branches_group_txt = $( "#scope_branches_group option:selected" ).text();

                    var branch_group_id  = '<input type="hidden" class="branch_group_id" name="branch_group_id" value="'+branches_group+'" data-name="branch_group_id">';
                    var isic_no_input = '<input type="hidden" name="isic_no" value="'+isic_no+'" data-name="isic_no">';

                    var tis_show = `<a class="open_scope_branches_tis_details" data-detail="${tis_detail_arr}" href="javascript:void(0)" title="คลิกดูรายละเอียด">${tis_arr.join(', ')}</a>`;

                    var btn = '<button class="btn btn-sm btn-danger btn_remove" type="button" data-repeater-delete> <i class="fa fa-minus"></i></button>';

                    var tr_ =   `
                                    <tr data-repeater-item>
                                        <td class="no text-center"></td>
                                        <td class="text-left">
                                            ${branches_group_txt}
                                            ${branch_group_id}
                                        </td>
                                        <td>
                                            ${branches_txt.join(', ')}
                                            ${branch_input}
                                        </td>
                                        <td>
                                            ${( checkNone(isic_no)?isic_no:'-' )}
                                            ${isic_no_input}
                                        </td>
                                        <td class="text-ellipsis">
                                            ${tis_show}
                                            ${tis_details}
                                            ${tis_no_input}
                                        </td>
                                        <td class="text-center">
                                            ${btn}
                                        </td>
                                    </tr>
                                `;

                    var values =  $('#table-scope').find(".branch_group_id").map(function(){return $(this).val(); }).get();

                    if( values.indexOf( String(branches_group) ) == -1 ){
                        $('#box_list_scpoe').append(tr_);
                    }else{

                        Swal.fire({
                            type: 'warning',
                            title: 'เลือกสาขาผลิตภัณฑ์ '+(branches_group_txt)+' ซ้ำ',
                            html: '<p class="h5">หากต้องการเพิ่มรายสาขาให้ลบสาขาออกแล้วเพิ่ม สาขาผลิตภัณฑ์ใหม่</p>',
                            width: 500
                        });
                    }

                    reset_name();
                    resetOrderNo();

                    $('#scope_branches_group').val('').trigger("change");
                    $('#scope_isic_no').val('');

                    update_table_inspectors();//อัพเดทข้อมูลตารางผู้ตรวจ

                    $('#check_all_scope_branches_tis').iCheck('uncheck');
                    toggleSubmitButtonState();
                }

            });
            reset_name();

            $(document).on('click', '.btn_remove', function(){
                if (confirm('คุณต้องการลบแถวนี้ ?')) {
                    $(this).closest('tr').remove();
                    update_table_inspectors();
                    toggleSubmitButtonState();
                }
            });

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

            //เมื่อคลิกปุ่ม ค้นหาผู้ตรวจ
            $('#btn_modal_inspectors').click(function (e) {
                $('#Minspectors').modal('show'); //แสดง modal ผู้ตรวจ
            });


            //ขอขึ้นทะเบียนใหม่
            $('#applicant_request_type_1').on('ifChecked', function(event){
                BoxDataCBIB();
            });

            //ขอเพิ่มเติมขอบข่าย
            $('#applicant_request_type_2').on('ifChecked', function(event){
                BoxDataCBIB();
                LoadListIBCB();
            });

            // //ขอลดขอบข่าย
            // $('#applicant_request_type_3').on('ifChecked', function(event){
            //     BoxDataCBIB();
            // });

            // //ขอแก้ไขข้อมูล
            // $('#applicant_request_type_4').on('ifChecked', function(event){
            //     BoxDataCBIB();
            // });

            BoxDataCBIB();

            $('#ibcb_id').change(function (e) {

                //Clear Input
                $('#ibcb_name').val('');
                $('#ibcb_address').val('');
                $('#ibcb_moo').val('');
                $('#ibcb_soi').val('');
                $('#ibcb_road').val('');
                $('#ibcb_building').val('');
                $('.ibcb_input_show').val('');
                $('#ibcb_phone').val('');
                $('#ibcb_fax').val('');

                var id = $(this).val();
                if( checkNone(id)){
                    $.ajax({
                        url: "{!! url('/funtions/get-section5-ibcb/') !!}" + "/" + id
                    }).done(function( obj ) {
                        if(obj != ''){

                            $('#ibcb_name').val(obj.ibcb_name);

                            $('#ibcb_address').val(obj.ibcb_address);
                            $('#ibcb_moo').val(obj.ibcb_moo);
                            $('#ibcb_soi').val(obj.ibcb_soi);
                            $('#ibcb_road').val(obj.ibcb_road);
                            $('#ibcb_building').val(obj.ibcb_building);

                            $('#ibcb_subdistrict_txt').val(obj.ibcb_subdistrict);
                            $('#ibcb_district_txt').val(obj.ibcb_district);
                            $('#ibcb_province_txt').val(obj.ibcb_province);
                            $('#ibcb_zipcode_txt').val(obj.ibcb_zipcode);

                            $('#ibcb_subdistrict_id').val(obj.ibcb_subdistrict_id);
                            $('#ibcb_district_id').val(obj.ibcb_district_id);
                            $('#ibcb_province_id').val(obj.ibcb_province_id);
                            $('#ibcb_zipcode').val(obj.ibcb_zipcode);

                            $('#ibcb_phone').val(obj.ibcb_phone);
                            $('#ibcb_fax').val(obj.ibcb_fax);

                        }
                    });

                }

                // if( $('#applicant_type_3').is(':checked',true)  ){
                //     loadScopeLab();
                // }

            });
        });

        function BoxDataCBIB(){
            if( $('#applicant_request_type_1').is(':checked',true) ){

                $('.box_ibcb').find('select').select2('val', "");

                $('.box_ibcb').hide();
                $('.box_ibcb').find('input, select, hidden, checkbox').prop('disabled', true);

            }else if( $('#applicant_request_type_2').is(':checked',true) ){
                $('.box_ibcb').show();
                $('.box_ibcb').find('input, select, hidden, checkbox').prop('disabled', false);
            }
        }

        function LoadListIBCB(){

            $('#ibcb_id').html('<option value=""> - เลือกหน่วยตรวจสอบ IB/CB - </option>');
            var applicant_taxid = '{!!   $applicationibcb->applicant_taxid !!}';
            var applicant_type  = ($("input[name=application_type]:checked").val() == 1 )?'1':'2';

            if(  checkNone(applicant_taxid) && $('#applicant_request_type_2').is(':checked',true) ){
                $.ajax({
                    url: "{!! url('/funtions/get-section5-ibcb-list') !!}" + "?applicant_taxid=" + applicant_taxid + '&applicant_type=' + applicant_type
                 }).done(function( object ) {
                    $.each(object, function( index, data ) {
                        $('#ibcb_id').append('<option value="'+data.id+'">'+data.ibcb_title+'</option>');
                    });
                });
            }
        }
        //รีเซ็ตเลขลำดับ
        function resetOrderNo(){

            $('.no').each(function(index, el) {
                $(el).text(index+1);
            });

        }

        function resetInsNo2(){

            $('.ins_no').each(function(index, el) {
                $(el).text(index+1);
            });

        }

        //โหลดรายชื่อผู้ตรวจที่สังกัดผู้ยื่นคำขอรายนี้ลงตาราง รายชื่อผู้ตรวจที่ผ่านการแต่งตั้ง ตามสาขาที่ผู้ยื่นเลือก (ต้องมีขอบข่ายอย่างน้อย 1 มอก.)
        function update_table_inspectors(){

            $('#table-inspectors').find('tbody').html('');
            var branch_ids = $('#table-scope').find('[data-name="branch_id"]').map(function(){
                                return $(this).val();
                             }).get(); //ไอดีสาขาจากคำขอ

            var tis_nos    = $('#table-scope').find('input.tis_details').map(function(){
                                return $.trim($(this).data('tis_no'));
                            }).get(); //เลขมอก.จากคำขอ

            if(branch_ids.length > 0){//มีการเพิ่มสาขาผลิตภัณฑ์ลงตารางแล้ว
                var order = 0;
                var btn = '<button class="btn btn-sm btn-danger" type="button" data-repeater-delete> <i class="fa fa-minus"></i></button>';

                $.each(inspector_owns, function(index, inspector) {//วนรอบรายชื่อผู้ตรวจที่อยู่ในสังกัดทั้งหมด
                    var inspector_scopes = Array(); //ข้อมูลสาขา
                    $.each(inspector.data_scopes, function(index, scopes) {//วนรอบสาขาผลิตภัณฑ์
                        $.each(scopes.branch, function(index, branch) {//วนรอบสาขา

                            if(branch_ids.includes(branch.branch_id)){//ถ้ามีในสาขาที่เลือกไว้

                                let tis_no_intersects = tis_nos.filter(value => branch.tis_nos.includes(value));//หามอก.ในคำขอว่ามีในขอบข่ายของผู้ตรวจหรือไม่

                                if(tis_no_intersects.length > 0){//ถ้ามอก.ในคำขอมีในของผู้ตรวจด้วย
                                    if(inspector_scopes[scopes.branch_group_id] === undefined){//ถ้ายังไม่มีกลุ่มสาขา
                                        inspector_scopes[scopes.branch_group_id] = {
                                                                                        group_id: scopes.branch_group_id,
                                                                                        group_title: scopes.branch_group_title,
                                                                                        branch: []
                                                                                    };
                                    }
                                    inspector_scopes[scopes.branch_group_id].branch.push(branch);//เพิ่มสาขาเข้าไปในสาขาผลิตภัณฑ์
                                }
                            }
                        });
                    });

                    if(inspector_scopes.length > 0){//ถ้ามีสาขาผลิตภัณฑ์ตรงกับที่เลือกอย่างน้อย 1 สาขา

                        var html_branch  = '<ul class="list-unstyled">';
                        var group_id = [];
                        var inputB = '' ;
                        $.each(inspector_scopes, function(index, data) {

                            if(data===undefined){//เป็น array ที่ไม่มีข้อมูล
                                return true;
                            }
                            group_id.push(data.group_id);

                            html_branch += '<li>'+(data.group_title)+'</li>';
                            html_branch += '<li>';
                            var branch = data.branch;

                            var branch_title = [];
                            var branch_id = [];

                            $.each(branch, function(index2, ItemBranch) {
                                branch_title.push(ItemBranch.branch_title);
                                branch_id.push(ItemBranch.branch_id);
                            });

                            //id สาขา ทั้งหมดในสาขาผลิตภัณฑ์นี้ คั่นด้วย ,
                            inputB += '<input type="hidden" name="branch_id_'+(data.group_id)+'" value="'+branch_id+'">';

                            html_branch += '<ul>';
                            html_branch += '<li>'+(branch_title.join(', '))+'</li>';
                            html_branch += '</ul>';
                            html_branch += '</li>';
                        });
                        html_branch += '</ul>';

                        var input_  = '<input type="hidden" class="inspector_id" name="inspector_id" value="' + inspector.id + '">';
                            input_ += '<input type="hidden" name="inspector_taxid" value="' + inspector.inspectors_taxid + '">';
                            input_ += '<input type="hidden" name="inspector_prefix" value="' + inspector.inspectors_prefix + '">';
                            input_ += '<input type="hidden" name="inspector_first_name" value="' + inspector.inspectors_first_name + '">';
                            input_ += '<input type="hidden" name="inspector_last_name" value="' + inspector.inspectors_last_name + '">';
                            input_ += '<input type="hidden" name="inspector_type" value="1">';
                            input_ += '<input type="hidden" name="branch_group_id" value="' + group_id + '">';
                            input_ += inputB;

                        var full_name = inspector.inspectors_prefix + inspector.inspectors_first_name + ' ' + inspector.inspectors_last_name;

                        var inspector_tr  = '<tr data-repeater-item>';
                            inspector_tr += '  <td class="ins_no text-center">' + (++order) + '</td>';
                            inspector_tr += '  <td class="text-left">' + full_name + input_ + '</td>';
                            inspector_tr += '  <td class="text-left">' + inspector.inspectors_taxid + '</td>';
                            inspector_tr += '  <td class="text-left">' + html_branch + '</td>';
                            inspector_tr += '  <td class="text-left">ผู้ตรวจของหน่วยตรวจ</td>';
                            inspector_tr += '  <td class="text-center">' + btn + '</td>';
                            inspector_tr += '</tr>';

                        $('#table-inspectors').find('tbody').append(inspector_tr);
                        $('.inspectors-repeater').repeater();
                    }

                });

            }else{//ยังไม่มีการเลือกสาขาผลิตภัณฑ์

                $('#table-inspectors').find('tbody').html(tr_inspector_own);
                $('.inspectors-repeater').repeater();
            }

        }

        function LoadBranche(){

            var branches_group = $("#scope_branches_group").val();

            var select = $('#scope_branches_multiple');

            setScopeBranchesLoadingState(checkNone(branches_group));
            $(select).html('');
            $(select).val('').trigger('change');

            if( checkNone(branches_group) ){
                $.ajax({
                    url: "{!! url('/request-section-5/application-ibcb/get-branche') !!}" + "/" + branches_group
                }).done(function( object ) {

                    if( checkNone(object) ){
                        $.each(object, function( index, data ) {
                            $(select).append('<option value="'+data.id+'">'+data.title+'</option>');
                        });
                    }

                }).always(function() {
                    setScopeBranchesLoadingState(false);
                });
            }else{
                setScopeBranchesLoadingState(false);
            }

            setTimeout(function(){
                $("#scope_branches_multiple > option").prop("selected", "selected");
                $('#scope_branches_multiple').trigger("change");
            }, 500);

        }

        function LoadBrancheTis(){

            //รายสาขา
            var branch_ids = $("#scope_branches_multiple").val();

            //เลขที่ มอก.
            var select = $('#scope_branches_tis');
            setScopeBranchesTisLoadingState(checkNone(branch_ids));
            $(select).html('');
            $(select).val('').trigger('change');

            $('#check_all_scope_branches_tis').iCheck('uncheck');//เคลียร์เลือกทั้งหมด
            updateScopeTisAllToggleState();

            if(checkNone(branch_ids)){
                $.ajax({
                    url: "{!! url('/request-section-5/application-ibcb/get-branche-tis') !!}" + "/" + branch_ids
                }).done(function( object ) {

                    if( checkNone(object) ){
                        $.each(object, function( index, data ) {
                            $(select).append('<option value="'+data.id+'" data-branch="'+data.branch_title+'">'+data.title+'</option>');
                        });
                    }

                    updateScopeTisAllToggleState();

                }).always(function() {
                    setScopeBranchesTisLoadingState(false);
                });
            }else{
                setScopeBranchesTisLoadingState(false);
            }

            // setTimeout(function(){
            //     $("#scope_branches_tis > option").prop("selected", "selected");
            //     $('#scope_branches_tis').trigger("change");
            // }, 500);

        }

        function hasPendingScopeSelection()
        {
            var branch_group = $('#scope_branches_group').val();
            var branches = $('#scope_branches_multiple').val();
            var branches_tis = $('#scope_branches_tis').val();
            var has_tis = isScopeTisAllSelected() || checkNone(branches_tis);

            if (!checkNone(branch_group)) {
                return false;
            }

            return checkNone(branches) || !has_tis;
        }

        function toggleSubmitButtonState()
        {
            var has_pending = hasPendingScopeSelection();

            $('#btn_submit').toggleClass('disabled', has_pending);
            $('#btn_submit').prop('disabled', has_pending);
            $('#btn_submit').attr('aria-disabled', has_pending ? 'true' : 'false');
        }

        function updateScopeTisSelectDisplay()
        {
            var select = $('#scope_branches_tis');
            var total = select.find('option').length;
            var selected = select.find('option:selected').length;
            var is_all_selected = isScopeTisAllSelected();
            var summaryText = $('#scope_tis_summary_text');

            if (is_all_selected) {
                summaryText.text('เลือกเลขที่ มอก. ทั้งหมด ' + total + ' รายการ').show();
                select.data('skip-change-handler', 1);
                select.select2('val', []);
                setScopeTisSelectPlaceholder('เลือกมาตรฐานทุกรายการ');
            } else if (total > 0 && selected > 0) {
                summaryText.text('เลือกเลขที่ มอก. ทั้งหมด ' + selected + '/' + total + ' รายการ').show();
                setScopeTisSelectPlaceholder(select.data('placeholder'));
            } else {
                summaryText.hide().text('');
                setScopeTisSelectPlaceholder(select.data('placeholder'));
            }
        }

        function setScopeTisSelectPlaceholder(text)
        {
            var select = $('#scope_branches_tis');
            select.attr('data-placeholder', text);
            select.next('.select2-container').find('.select2-search-field input').attr('placeholder', text);
        }

        function setScopeBranchesLoadingState(isLoading)
        {
            var select = $('#scope_branches_multiple');
            var loadingText = $('#scope_branches_loading_text');

            select.prop('disabled', isLoading);

            if (isLoading) {
                loadingText.show();
            } else {
                loadingText.hide();
            }
        }

        function setScopeBranchesTisLoadingState(isLoading)
        {
            var select = $('#scope_branches_tis');
            var loadingText = $('#scope_branches_tis_loading_text');

            select.prop('disabled', isLoading);

            if (isLoading) {
                loadingText.show();
            } else {
                loadingText.hide();
            }
        }

        function isScopeTisAllSelected()
        {
            return $('#scope_branches_tis').data('all-selected') == 1;
        }

        function canSelectAllScopeTis()
        {
            return $('#scope_branches_tis').find('option').length > 0;
        }

        function updateScopeTisAllToggleState()
        {
            var checkbox = $('#check_all_scope_branches_tis');

            if (canSelectAllScopeTis()) {
                checkbox.iCheck('enable');
            } else {
                $('#scope_branches_tis').data('all-selected', 0);
                checkbox.iCheck('uncheck');
                checkbox.iCheck('disable');
            }
        }

        function ShowInputCertificate(){

            var value_btn = $('#btn_std_export').val();

            $('#certificate_issue_date').prop('disabled', true);
            $('#certificate_expire_date').prop('disabled', true);

            $('body').find('.certificate_cerno_export').prop('disabled', true);

            if( value_btn == '1'){
                $('body').find('.certificate_cerno_export').prop('disabled', false);
                $('#certificate_issue_date').prop('disabled', false);
                $('#certificate_expire_date').prop('disabled', false);
            }else if(  value_btn == '2' ){
                $('body').find('.certificate_cerno_export').prop('disabled', true);
            }
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

            $("#ibcb_address_seach").select2("val", "");

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
                var phone = $('#hq_phone').val();
                var fax = $('#hq_fax').val();

                $('#ibcb_address').val(address);
                $('#ibcb_moo').val(moo);
                $('#ibcb_soi').val(soi);
                $('#ibcb_road').val(road);
                $('#ibcb_building').val(building);

                $('#ibcb_subdistrict_txt').val(subdistrict_txt);
                $('#ibcb_district_txt').val(district_txt);
                $('#ibcb_province_txt').val(province_txt);
                $('#ibcb_zipcode_txt').val(postcode_txt);

                $('#ibcb_subdistrict_id').val(subdistrict_id);
                $('#ibcb_district_id').val(district_id);
                $('#ibcb_province_id').val(province_id);
                $('#ibcb_zipcode').val(postcode);
                $('#ibcb_phone').val(phone);
                $('#ibcb_fax').val(fax);

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
                var phone = '{!! isset($user_regitor) && !empty($user_regitor->contact_tel) ? $user_regitor->contact_tel : '' !!}';
                var fax = '{!! isset($user_regitor) && !empty($user_regitor->contact_fax) ? $user_regitor->contact_fax : '' !!}';

                $('#ibcb_address').val(address);
                $('#ibcb_moo').val(moo);
                $('#ibcb_soi').val(soi);
                $('#ibcb_road').val(road);
                $('#ibcb_building').val(building);

                $('#ibcb_subdistrict_txt').val(subdistrict_txt);
                $('#ibcb_district_txt').val(district_txt);
                $('#ibcb_province_txt').val(province_txt);
                $('#ibcb_zipcode_txt').val(postcode_txt);

                $('#ibcb_subdistrict_id').val(subdistrict_id);
                $('#ibcb_district_id').val(district_id);
                $('#ibcb_province_id').val(province_id);
                $('#ibcb_zipcode').val(postcode);

                $('#ibcb_phone').val(phone);
                $('#ibcb_fax').val(fax);

            }else{
                $('#ibcb_address').val('');
                $('#ibcb_moo').val('');
                $('#ibcb_soi').val('');
                $('#ibcb_road').val('');
                $('#ibcb_building').val('');
                $('.ibcb_input_show').val('');
            }

        }

        function reset_name(){
            let rows = $('#box_list_scpoe').find('tr');
            let group_name = $('#box_list_scpoe').data('repeater-list');
            rows.each(function(index1, row){
                $(row).find('input, select').each(function(index2, el){
                    let old_name = $(el).data('name');
                    if(!!old_name){
                        if($(el).hasClass('input_array')){
                            $(el).attr('name', group_name+'['+index1+']['+old_name+'][]');
                        }else{
                            $(el).attr('name', group_name+'['+index1+']['+old_name+']');
                        }
                    }
                });
            });
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

        function BoxAuditType1(){
            var audit_type =  ($("input[name=audit_type]:checked").val() == 1 )?'1':'2';
            if( audit_type == '1' ){
                $('.box_audit_type_1').show();
                $('.box_audit_type_1').find('input').prop('disabled', false);
            }else{
                $('.box_audit_type_1').hide();
                $('.box_audit_type_1').find('input').prop('disabled', true);
            }
        }

        function checkNone(value) {
            return value !== '' && value !== null && value !== undefined;
        }

        function LoadStdType(){

            var application_type =  ($("input[name=application_type]:checked").val() == 1 )?'1':'2';
            $('#certificate_std_export').html('<option value=""> -เลือกมอก. รับรองระบบงาน- </option>');

            if( checkNone(application_type) ){
                $.ajax({
                    url: "{!! url('/request-section-5/application-ibcb/get-standards') !!}" + "/" + application_type
                }).done(function( object ) {

                    if( checkNone(object) ){
                        $.each(object, function( index, data ) {
                            $('#certificate_std_export').append('<option value="'+data.id+'">'+data.title+'</option>');
                        });
                    }

                });
            }

        }
    </script>
@endpush
