@push('css')
    <link href="{{asset('plugins/components/icheck/skins/all.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/components/bootstrap-datepicker-thai/css/datepicker.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('plugins/components/switchery/dist/switchery.min.css')}}" rel="stylesheet" />
@endpush

@php
    $user_assignor = HP::AuthUserSSO(!empty($agent->user_id) ? $agent->user_id : null);
    $agent_list = isset($agent_list) ? $agent_list : [];//รายการมอบสิทธิ์ที่เคยมอบไว้ที่ยังรออนุมัติหรือกำลังดำเนินการใช้อยู่
@endphp

<div class="form-body">
    <h3 class="box-title">ข้อมูลผู้มอบสิทธิ์</h3>
    <hr class="m-t-0 m-b-40">

    {!! Form::hidden('user_id', !empty( $agent->user_id )?$agent->user_id:(!empty( $user_assignor->id )?$user_assignor->id:null) , [ 'class' => '', 'id' => 'user_id' ] ) !!}
    {!! Form::hidden('user_taxid', !empty( $agent->user_taxid )?$agent->user_taxid:(!empty( $user_assignor->tax_number )?$user_assignor->tax_number:null) , [ 'class' => '' ] ) !!}

    <!-- ชื่อผู้ประกอบการ -->
    {!! Form::hidden('head_name', !empty( $agent->head_name )?$agent->head_name:(!empty( $user_assignor->name )?$user_assignor->name:null) , [ 'class' => '', 'id' => 'user_id' ] ) !!}

    <!-- ที่ตั้ง ผู้มอบ -->
    {!! Form::hidden('head_address_no', !empty( $agent->head_address_no )?$agent->head_address_no:(!empty( $user_assignor->address_no )?$user_assignor->address_no:null) , [ 'class' => ''  ] ) !!}

    <!-- อาคาร/หมู่บ้าน ผู้มอบ -->
    {!! Form::hidden('head_village', !empty( $agent->head_village )?$agent->head_village:(!empty( $user_assignor->building )?$user_assignor->building:null) , [ 'class' => '' ] ) !!}

    <!-- หมู่ ผู้มอบ -->
    {!! Form::hidden('head_moo',!empty( $agent->head_moo )?$agent->head_moo: (!empty( $user_assignor->moo )?$user_assignor->moo:null) , [ 'class' => '' ] ) !!}

    <!-- ซอย ผู้มอบ -->
    {!! Form::hidden('head_soi', !empty( $agent->head_soi )?$agent->head_soi:(!empty( $user_assignor->soi )?$user_assignor->soi:null) , [ 'class' => '' ] ) !!}

    <!-- ถนน ผู้มอบ -->
    {!! Form::hidden('head_street', !empty( $agent->head_street )?$agent->head_street:(!empty( $user_assignor->street )?$user_assignor->street:null) , [ 'class' => '' ] ) !!}

    <!-- ตำบล/แขวง ผู้มอบ -->
    {!! Form::hidden('head_subdistrict', !empty( $agent->head_subdistrict )?$agent->head_subdistrict:(!empty( $user_assignor->subdistrict )?$user_assignor->subdistrict:null) , [ 'class' => '' ] ) !!}

    <!-- อำเภอ/เขต ผู้มอบ -->
    {!! Form::hidden('head_district', !empty( $agent->head_district )?$agent->head_district:(!empty( $user_assignor->district )?$user_assignor->district:null) , [ 'class' => '' ] ) !!}

    <!-- จังหวัด ผู้มอบ -->
    {!! Form::hidden('head_province', !empty( $agent->head_province )?$agent->head_province:(!empty( $user_assignor->province )?$user_assignor->province:null) , [ 'class' => '' ] ) !!}

    <!-- รหัสไปรษณีย์ ผู้มอบ -->
    {!! Form::hidden('head_zipcode', !empty( $agent->head_zipcode )?$agent->head_zipcode:(!empty( $user_assignor->zipcode )?$user_assignor->zipcode:null) , [ 'class' => '' ] ) !!}

    <!-- หมายเลขโทรศัพท์  ผู้มอบ -->
    {!! Form::hidden('head_telephone', !empty( $agent->head_telephone )?$agent->head_telephone:(!empty( $user_assignor->contact_tel )?$user_assignor->contact_tel:null) , [ 'class' => '' ] ) !!}

    <!-- หมายเลขโทรศัพท์มือถือ  ผู้มอบ -->
    {!! Form::hidden('head_mobile', !empty( $agent->head_mobile )?$agent->head_mobile:(!empty( $user_assignor->contact_phone_number )?$user_assignor->contact_phone_number:null)  , [ 'class' => '' ] ) !!}

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">ชื่อผู้ประกอบการ :</label>
                <div class="col-md-8">
                    <p class="form-control-static"> {!! !empty( $user_assignor->name )?$user_assignor->name:null !!} </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">เลขประจำตัวผู้เสียภาษี :</label>
                <div class="col-md-8">
                    <p class="form-control-static"> {!! !empty( $user_assignor->tax_number )?$user_assignor->tax_number:null !!} </p>
                </div>
            </div>
        </div>
    </div>

    <!--/row-->

    {{-- <hr class="m-t-0 m-b-40"> --}}

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4"><h6>ที่ตั้ง/สำนักงานใหญ่</h6></label>
                <div class="col-md-8">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">เลขที่ :</label>
                <div class="col-md-8">
                    <p class="form-control-static">  {!! !empty( $user_assignor->address_no )?$user_assignor->address_no:null !!} </p>
                </div>
            </div>
        </div>
        <!--/span-->
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">อาคาร/หมู่บ้าน :</label>
                <div class="col-md-8">
                    <p class="form-control-static">  {!! !empty( $user_assignor->building )?$user_assignor->building:null !!}  </p>
                </div>
            </div>
        </div>
        <!--/span-->
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">ตรอก/ซอย :</label>
                <div class="col-md-8">
                    <p class="form-control-static">  {!! !empty( $user_assignor->soi )?$user_assignor->soi:null !!} </p>
                </div>
            </div>
        </div>
        <!--/span-->
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">หมู่ :</label>
                <div class="col-md-8">
                    <p class="form-control-static">  {!! !empty( $user_assignor->moo )?$user_assignor->moo:null !!}  </p>
                </div>
            </div>
        </div>
        <!--/span-->
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">ถนน :</label>
                <div class="col-md-8">
                    <p class="form-control-static">  {!! !empty( $user_assignor->street )?$user_assignor->street:null !!} </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">แขวง/ตำบล :</label>
                <div class="col-md-8">
                    <p class="form-control-static">  {!! !empty( $user_assignor->subdistrict )?$user_assignor->subdistrict:null !!} </p>
                </div>
            </div>
        </div>
        <!--/span-->
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">เขต/อำเภอ :</label>
                <div class="col-md-8">
                    <p class="form-control-static">  {!! !empty( $user_assignor->district )?$user_assignor->district:null !!}  </p>
                </div>
            </div>
        </div>
        <!--/span-->
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">จังหวัด :</label>
                <div class="col-md-8">
                    <p class="form-control-static">  {!! !empty( $user_assignor->province )?$user_assignor->province:null !!} </p>
                </div>
            </div>
        </div>
        <!--/span-->
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">รหัสไปรษณีย์ :</label>
                <div class="col-md-8">
                    <p class="form-control-static">  {!! !empty( $user_assignor->zipcode )?$user_assignor->zipcode:null !!}  </p>
                </div>
            </div>
        </div>
        <!--/span-->
    </div>

    <!--/row-->
    <h3 class="box-title">รายละเอียดการมอบสิทธิ์</h3>
    <hr class="m-t-0 m-b-40">

    @php
        $full_name = null;
        if( isset($agent) && !empty($agent)){
            $full_name = $agent->agent_name;
        }
    @endphp

    <div class="row">
        <div class="col-md-6">
            <div class="form-group required {{ $errors->has('tax_number') ? 'has-error' : ''}}">
                {!! Form::label('tax_number', 'ผู้รับมอบสิทธิ์'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('tax_number', $full_name,['class' => 'form-control input-show auto-show typeahead', 'required' => 'required', 'id' => 'tax_number', 'autocomplete' => 'off', 'data-provide' => 'typeahead', 'placeholder' => 'กรอกชื่อ/เลขประจำตัวผู้เสียภาษี' ]) !!}
                    {!! $errors->first('tax_number', '<p class="help-block">:message</p>') !!}

                    {!! Form::hidden('agent_id', !empty( $agent->agent_id )?$agent->agent_id:null, [ 'class' => 'input_show', 'id' => 'agent_id' ] ) !!}
                    {!! Form::hidden('agent_taxid', !empty( $agent->agent_taxid )?$agent->agent_taxid:null, [ 'class' => 'input_show', 'id' => 'agent_taxid' ] ) !!}

                    <!-- ชื่อผู้ประกอบการ -->
                    {!! Form::hidden('agent_name', !empty( $agent->agent_name )?$agent->agent_name:null , [ 'class' => 'input_show', 'id' => 'agent_name' ] ) !!}

                    <!-- ที่ตั้ง ผู้รับมอบ -->
                    {!! Form::hidden('agent_address_no', !empty( $agent->agent_address_no )?$agent->agent_address_no:null , [ 'class' => 'input_show' , 'id' => 'agent_address_no' ] ) !!}

                    <!-- อาคาร/หมู่บ้าน ผู้รับมอบ -->
                    {!! Form::hidden('agent_village', !empty( $agent->agent_village )?$agent->agent_village:null , [ 'class' => 'input_show', 'id' => 'agent_village' ] ) !!}

                    <!-- หมู่ ผู้รับมอบ -->
                    {!! Form::hidden('agent_moo', !empty( $agent->agent_moo )?$agent->agent_moo:null , [ 'class' => 'input_show', 'id' => 'agent_moo' ] ) !!}

                    <!-- ถนน ผู้รับมอบ -->
                    {!! Form::hidden('agent_street', !empty( $agent->agent_street )?$agent->agent_street:null , [ 'class' => 'input_show', 'id' => 'agent_street' ] ) !!}

                    <!-- ซอย ผู้รับมอบ -->
                    {!! Form::hidden('agent_soi', !empty( $agent->agent_soi )?$agent->agent_soi:null , [ 'class' => 'input_show', 'id' => 'agent_soi' ] ) !!}

                    <!-- ตำบล/แขวง ผู้รับมอบ -->
                    {!! Form::hidden('agent_subdistrict', !empty( $agent->agent_subdistrict )?$agent->agent_subdistrict:null , [ 'class' => 'input_show', 'id' => 'agent_subdistrict' ] ) !!}

                    <!-- อำเภอ/เขต ผู้รับมอบ -->
                    {!! Form::hidden('agent_district', !empty( $agent->agent_district )?$agent->agent_district:null , [ 'class' => 'input_show', 'id' => 'agent_district' ] ) !!}

                    <!-- จังหวัด ผู้รับมอบ -->
                    {!! Form::hidden('agent_province', !empty( $agent->agent_province )?$agent->agent_province:null , [ 'class' => 'input_show', 'id' => 'agent_province' ] ) !!}

                    <!-- รหัสไปรษณีย์ ผู้รับมอบ -->
                    {!! Form::hidden('agent_zipcode', !empty( $agent->agent_zipcode )?$agent->agent_zipcode:null , [ 'class' => 'input_show', 'id' => 'agent_zipcode' ] ) !!}

                    <!-- หมายเลขโทรศัพท์  ผู้รับมอบ -->
                    {!! Form::hidden('agent_telephone', !empty( $agent->agent_telephone )?$agent->agent_telephone:null , [ 'class' => 'input_show', 'id' => 'agent_telephone' ] ) !!}

                    <!-- หมายเลขโทรศัพท์มือถือ  ผู้รับมอบ -->
                    {!! Form::hidden('agent_mobile', !empty( $agent->agent_mobile )?$agent->agent_mobile:null , [ 'class' => 'input_show', 'id' => 'agent_mobile' ] ) !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group required{{ $errors->has('tax_number_txt') ? 'has-error' : ''}}">
                {!! Form::label('tax_number_txt', 'เลขประจำตัวผู้เสียภาษี', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('tax_number_txt', !empty( $agent->agent_taxid )?$agent->agent_taxid:null,  ['class' => 'form-control input_show', 'disabled' => true, 'required' => true ]) !!}
                    {!! $errors->first('tax_number_txt', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4"><h6>ที่ตั้ง/สำนักงานใหญ่</h6></label>
                <div class="col-md-9">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('agent_address_no_txt') ? 'has-error' : ''}}">
                {!! Form::label('agent_address_no_txt', 'เลขที่'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('agent_address_no_txt', !empty( $agent->agent_address_no )?$agent->agent_address_no:null,['class' => 'form-control input_show', 'disabled' => true ]) !!}
                    {!! $errors->first('agent_address_no_txt', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('agent_village_txt') ? 'has-error' : ''}}">
                {!! Form::label('agent_village_txt', 'อาคาร/หมู่บ้าน'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('agent_village_txt', !empty( $agent->agent_village )?$agent->agent_village:null,  ['class' => 'form-control input_show', 'disabled' => true ]) !!}
                    {!! $errors->first('agent_village_txt', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('agent_soi_txt') ? 'has-error' : ''}}">
                {!! Form::label('agent_soi_txt', 'ตรอก/ซอย'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('agent_soi_txt', !empty( $agent->agent_soi )?$agent->agent_soi:null,  ['class' => 'form-control input_show', 'disabled' => true ]) !!}
                    {!! $errors->first('agent_soi_txt', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('agent_moo_txt') ? 'has-error' : ''}}">
                {!! Form::label('agent_moo_txt', 'หมู่'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('agent_moo_txt', !empty( $agent->agent_moo )?$agent->agent_moo:null,['class' => 'form-control input_show', 'disabled' => true ]) !!}
                    {!! $errors->first('agent_moo_txt', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('agent_street_txt') ? 'has-error' : ''}}">
                {!! Form::label('agent_street_txt', 'ถนน'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('agent_street_txt', !empty( $agent->agent_street )?$agent->agent_street:null,  ['class' => 'form-control input_show', 'disabled' => true ]) !!}
                    {!! $errors->first('agent_street_txt', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('agent_subdistrict_txt') ? 'has-error' : ''}}">
                {!! Form::label('agent_subdistrict_txt', 'แขวง/ตำบล'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('agent_subdistrict_txt', !empty( $agent->agent_subdistrict )?$agent->agent_subdistrict:null,  ['class' => 'form-control input_show', 'disabled' => true ]) !!}
                    {!! $errors->first('agent_subdistrict_txt', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('agent_district_txt') ? 'has-error' : ''}}">
                {!! Form::label('agent_district_txt', 'เขต/อำเภอ'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('agent_district_txt', !empty( $agent->agent_district )?$agent->agent_district:null,['class' => 'form-control input_show', 'disabled' => true ]) !!}
                    {!! $errors->first('agent_district_txt', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('agent_province_txt') ? 'has-error' : ''}}">
                {!! Form::label('agent_province_txt', 'จังหวัด'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('agent_province_txt', !empty( $agent->agent_province )?$agent->agent_province:null,  ['class' => 'form-control input_show', 'disabled' => true ]) !!}
                    {!! $errors->first('agent_province_txt', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('agent_zipcode_txt') ? 'has-error' : ''}}">
                {!! Form::label('agent_zipcode_txt', 'รหัสไปรษณีย์'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('agent_zipcode_txt', !empty( $agent->agent_zipcode )?$agent->agent_zipcode:null,['class' => 'form-control input_show', 'disabled' => true ]) !!}
                    {!! $errors->first('agent_zipcode_txt', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('contact_tel') ? 'has-error' : ''}}">
                {!! Form::label('contact_tel', 'เบอร์โทรศัพท์'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('contact_tel', !empty( $agent->agent_telephone )?$agent->agent_telephone:null,['class' => 'form-control input_show', 'disabled' => true ]) !!}
                    {!! $errors->first('contact_tel', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('contact_phone_number') ? 'has-error' : ''}}">
                {!! Form::label('contact_phone_number', 'เบอร์โทรศัพท์มือถือ'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::text('contact_phone_number', !empty( $agent->agent_mobile )?$agent->agent_mobile:null,  ['class' => 'form-control input_show', 'disabled' => true ]) !!}
                    {!! $errors->first('contact_phone_number', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <hr>

    @php
        $setting_system = App\Models\Setting\SettingSystem::where('state', 1)->pluck('title', 'id');
        $system_ids = null;
        if( isset($agent) && !empty($agent) && empty($agent->select_all)  ){
            $system_ids = App\Models\Agents\AgentSystem::where('sso_agent_id', $agent->id )->pluck('setting_systems_id', 'setting_systems_id')->toArray();
        }

    @endphp

    <div class="row">
        <div class="col-md-6">
            <div class="form-group required{{ $errors->has('select_all') ? 'has-error' : ''}}">
                {!! Form::label('select_all', 'ระบบที่มอบสิทธิ์'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::checkbox('select_all', '1', (!empty( $agent->select_all ) && $agent->select_all == 1) ?true:( empty($agent)?true:null ), ['class' => 'form-control check', 'data-checkbox' => 'icheckbox_flat-blue', 'id'=>'select_all']) !!}
                    <label for="signature">ทั้งหมด</label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('') ? 'has-error' : ''}}">
                {!! Form::label('', ''.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    {!! Form::select('setting_system[]', $setting_system, (is_array($system_ids) ? $system_ids : null), ['class' => 'setting_system_multiple', 'multiple' => 'multiple', 'data-placeholder' => '-เลือกระบบที่มอบสิทธิ์-', ]) !!}
                    {!! $errors->first('setting_system', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group required{{ $errors->has('issue_type') ? 'has-error' : ''}}">
                {!! Form::label('issue_type', 'ช่วงเวลาที่มอบสิทธิ์'.' :', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    <div class="col-md-6">
                        {!! Form::radio('issue_type', '1',(!empty( $agent->issue_type ) && $agent->issue_type == 1) ?true:( empty($agent)?true:null ), ['class' => 'form-control check', 'data-radio' => 'iradio_flat-blue', 'id'=>'issue_type-1' ,'required' => 'required']) !!}
                        <label for="signature">ตลอดไป</label>
                    </div>
                    <div class="col-md-6">
                        {!! Form::radio('issue_type', '2',( !empty( $agent->issue_type ) && $agent->issue_type == 2 ) ?true:null, ['class' => 'form-control check', 'data-radio' => 'iradio_flat-blue', 'id'=>'issue_type-2','required' => 'required']) !!}
                        <label for="signature">ตามกำหนด</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row box_time">
        <div class="col-md-6">

            <div class="form-group">
                {!! Form::label('', '', ['class' => 'col-md-4 control-label']) !!}
                <div class="col-md-8">
                    <div class="input-daterange input-group date-range">
                        {!! Form::text('start_date',  !empty($agent->start_date)?HP::revertDate($agent->start_date):null, ['class' => 'form-control ','id'=>'start_date','required' => 'required','placeholder'=>'วันที่เริ่มต้น'] )!!}
                        <span class="input-group-addon bg-info b-0 text-white"> ถึง </span>
                        {!! Form::text('end_date', !empty($agent->end_date)?HP::revertDate($agent->end_date):null,['class' => 'form-control ','id'=>'end_date','required' => 'required','placeholder'=>'วันที่สิ้นสุด'])!!}
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div class="row repeater-form" id="div_attach">
        <div class="col-md-12" data-repeater-list="repeater-file">

            @php
                $file_agen = [];
                if( isset($agent) && !empty($agent) ){
                    $file_agen = App\AttachFile::where('section', 'file_attach')->where('ref_table', (new App\Models\Agents\Agent )->getTable() )->where('ref_id', $agent->id )->get();
                }
            @endphp

            @foreach ( $file_agen as $attach )

                <div class="form-group">
                    {!! HTML::decode(Form::label('personfile', 'เอกสารแนบ'.' : ', ['class' => 'col-md-2 control-label'])) !!}
                    <div class="col-md-5">
                        {!! Form::text('file_documents', ( !empty($attach->caption) ? $attach->caption:null) , ['class' => 'form-control' , 'placeholder' => 'คำอธิบาย', 'disabled' => true]) !!}
                    </div>
                    <div class="col-md-4">
                        <a href="{{url('funtions/get-view/'.$attach->url.'/'.( !empty($attach->caption) ? $attach->caption :  basename($attach->url)  ))}}" target="_blank" title="{!! !empty($attach->filename) ? $attach->filename : 'ไฟล์แนบ' !!}">
                            {!!  (!empty($attach->filename) ? $attach->filename:'ไฟล์แนบ').('.'.$attach->file_properties ?? '' ) !!}
                        </a>
                    </div>
                </div>

            @endforeach



            <div class="form-group input_show_file" data-repeater-item>
                {!! HTML::decode(Form::label('personfile', 'เอกสารแนบ'.' : ', ['class' => 'col-md-2 control-label'])) !!}
                <div class="col-md-5">
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
                            <input type="file" name="file_attach" id="file_attach">
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
        {{-- <div class="col-md-12">
            <div class="form-group">
                <div class="col-md-11">
                    <button type="button" class="btn btn-success pull-right" data-repeater-create><i class="icon-plus"></i>เพิ่ม</button>
                </div>
            </div>
        </div> --}}
    </div>


</div>

<center>
    <div class="form-group">
        <div class="col-md-offset-4 col-md-4">
            @if(isset($agent) && !empty($agent) )
                {!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'บันทึก', ['class' => 'btn btn-primary show_tag_a']) !!}
            @else
                <button class="btn btn-primary show_tag_a" type="button" id="btn_submit">
                    <i class="fa fa-paper-plane"></i> บันทึก
                </button>
            @endif


            {{-- @can('view-'.Str::slug('banks')) --}}
                <a class="btn btn-default show_tag_a" href="{{ url('/agents') }}">
                    ยกเลิก
                </a>
            {{-- @endcan --}}
        </div>
    </div>
</center>

{{-- Modal รายการที่มอบสิทธิ์ทับซ้อน --}}
<div id="modal-overlap" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">พบการทับซ้อนกับการมอบสิทธิ์ที่เคยมอบไว้ รายการดังต่อไปนี้</h4>
            </div>
            <div class="modal-body">
                <table class="table color-bordered-table danger-bordered-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="font-12">ผู้รับมอบสิทธิ์</th>
                            <th class="font-12">วันที่มอบสิทธิ์</th>
                            <th class="font-12">วันที่ยืนยันการมอบ</th>
                            <th class="font-12">ระบบที่มอบสิทธิ์</th>
                            <th class="font-12">เงื่อนไข</th>
                            <th class="font-12">สถานะ</th>
                            <th class="font-12">ดูรายละเอียด</th>
                        </tr>
                    </thead>
                    <tbody id="box-overlap">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

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
    <script>

        $(document).ready(function () {

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

            //บันทึกข้อมูล
            $('#btn_submit').click(function (e) {

                var agent_list = JSON.parse('{!! json_encode($agent_list) !!}'); //รายการที่เคยมอบสิทธิ์ไว้

                var agent_name = $('#agent_name').val();
                var agent_id   = $('#agent_id').val();

                if(checkNone(agent_id)){

                    var issue_type = $('input[name="issue_type"]:checked').val();
                    var start_date = $('#start_date').val();
                        start_date = convertDate(start_date);
                    var end_date   = $('#end_date').val();
                        end_date   = convertDate(end_date);
                    var select_all = $('#select_all').prop('checked')===true ? 1 : null;

                    var now_system_ids = {};
                    var overlap_list = [];

                    //ตรวจสอบการทับซ้อนของการมอบสิทธิ์
                    $.each(agent_list, function(index, agent_item) {//วนรายการที่เคยมอบสิทธิ์
                        if(agent_item.user_id==agent_id){//ตรงกับที่จะมอบสิทธิ์

                            //จัดรูปแบบระบบที่จะมอบสิทธิ์เป็น {id : name, id : name ...}
                            $('select[name="setting_system[]"]').children('option:selected').each(function(index, el) {
                                var value = $(el).val();
                                now_system_ids[value] = $(el).text();
                            });

                            var result = checkOverlap(agent_item.issue_type,
                                                      agent_item.start_date,
                                                      agent_item.end_date,
                                                      agent_item.select_all,
                                                      agent_item.system_ids,
                                                      issue_type,
                                                      start_date,
                                                      end_date,
                                                      select_all,
                                                      now_system_ids
                                                    );
                            if(result){//ทับซ้อน
                                overlap_list.push(agent_item);
                            }
                        }
                    });

                    if(overlap_list.length == 0){//ไม่มีการทับซ้อน
                        Swal.fire({
                            title: 'คุณต้องการยืนยันมอบหมายให้ '+ agent_name,
                            showDenyButton: true,
                            showCancelButton: true,
                            confirmButtonText: 'บันทึก',
                            cancelButtonText: 'ยกเลิก',
                        }).then((result) => {
                            /* Read more about isConfirmed, isDenied below */
                            if (result.value) {
                                $('#from_box').submit();
                            } else if (result.isDenied) {

                            }
                        });
                    }else{//มีการทับซ้อน

                        $('#box-overlap').html('');
                        $.each(overlap_list, function(index, overlap_item) {
                            var html  = '<tr>';
                                html += ' <td class="font-12">' + (index+1) + '</td>';
                                html += ' <td class="font-12">' + overlap_item.name + '<br>(' + overlap_item.tax_number + ')' + '</td>';
                                html += ' <td class="font-12">' + overlap_item.created_at + '</td>';
                                html += ' <td class="font-12">' + overlap_item.confirm_date + '</td>';
                                html += ' <td class="font-12">' + overlap_item.system_text + '</td>';
                                html += ' <td class="font-12">' + overlap_item.condition_text + '</td>';
                                html += ' <td class="font-12">' + overlap_item.state_text + '</td>';
                                html += ' <td class="font-12"><a href="{{ url('/agents/') }}/' + overlap_item.id + '" title="ดูรายละเอียด" class="btn btn-info" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i></a></td>';
                                html += '</tr>';
                            $('#box-overlap').append(html);
                        });

                        $('#modal-overlap').modal('show');
                    }

                }else{
                    Swal.fire({
                        type: 'warning',
                        title: 'กรุณาเลือกผู้รับมอบสิทธิ์ !',
                        confirmButtonText: 'รับทราบ',
                        footer: '<p class="h5">หากไม่พบมอบผู้รับมอบสิทธิ์ <a href="{!! url('contact') !!}" target="_blank">ติดต่อ สมอ.</a></p>'
                    });
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
            //ช่วงวันที่
            $('.date-range').datepicker({
              toggleActive: true,
              language:'th-th',
              format: 'dd/mm/yyyy',
            });



            var path = '{{ url('agents/search-users-sso') }}';
            $('#tax_number').typeahead({
                minLength: 13,
                source:  function (query, process) {

                    $('.input_show').val('');

                    return $.get(path, { query: query }, function (data) {
                        return process(data);
                    });
                },

                autoSelect: true,
                afterSelect: function (jsondata) {

                    $('#tax_number').val(jsondata.name_full);

                    $('#tax_number_txt').val(jsondata.tax_number);
                    $('#contact_tel').val(jsondata.contact_tel);
                    $('#contact_phone_number').val(jsondata.contact_phone_number);


                    var contact_name = jsondata.contact_prefix_text + jsondata.contact_first_name + ' ' + jsondata.contact_last_name;
                    $('#agent_id').val(jsondata.id);
                    $('#agent_taxid').val(jsondata.tax_number);
                    $('#agent_name').val(jsondata.name_full);
                    $('#agent_telephone').val(jsondata.contact_tel);
                    $('#agent_mobile').val(jsondata.contact_phone_number);

                    var address = '';

                    if( checkNone(jsondata.address_no)  ){
                        address += 'เลขที่ ' + jsondata.address_no+' ';
                        $('#agent_address_no').val(jsondata.address_no);
                        $('#agent_address_no_txt').val(jsondata.address_no);
                    }

                    if( checkNone(jsondata.building)  ){
                        address += 'อาคาร/หมู่บ้าน ' + jsondata.building+' ';
                        $('#agent_village').val(jsondata.building);
                        $('#agent_village_txt').val(jsondata.building);
                    }

                    if( checkNone(jsondata.soi)  ){
                        address += 'ตรอก/ซอย ' + jsondata.soi+' ';
                        $('#agent_soi').val(jsondata.soi);
                        $('#agent_soi_txt').val(jsondata.soi);
                    }

                    if( checkNone(jsondata.moo)  ){
                        address += 'หมู่ ' + jsondata.moo+' ';
                        $('#agent_moo').val(jsondata.moo);
                        $('#agent_moo_txt').val(jsondata.moo);
                    }

                    if( checkNone(jsondata.street)  ){
                        address += 'ถนน ' + jsondata.street+' ';
                        $('#agent_street').val(jsondata.street);
                        $('#agent_street_txt').val(jsondata.street);
                    }

                    var province = jsondata.province
                    var province_txt = province.replace(/ |_/g, '');

                    if( checkNone(jsondata.subdistrict)  ){

                        if( province_txt  == 'กรุงเทพมหานคร' ){
                            address += 'แขวง ' + jsondata.subdistrict+' ';
                        }else{
                            address += 'ตำบล ' + jsondata.subdistrict+' ';
                        }

                        $('#agent_subdistrict').val(jsondata.subdistrict);
                        $('#agent_subdistrict_txt').val(jsondata.subdistrict);
                    }

                    if( checkNone(jsondata.district)  ){

                        if( province_txt  == 'กรุงเทพมหานคร' ){
                            address += 'เขต ' + jsondata.district+' ';
                        }else{
                            address += 'อำเภอ ' + jsondata.district+' ';
                        }
                        $('#agent_district').val(jsondata.district);
                        $('#agent_district_txt').val(jsondata.district);
                    }

                    if( checkNone(jsondata.province)  ){

                        if( province_txt  == 'กรุงเทพมหานคร' ){
                            address += jsondata.province+' ';
                        }else{
                            address += 'จังหวัด ' + jsondata.province+' ';
                        }

                        $('#agent_province_txt').val(jsondata.province);
                        $('#agent_province').val(jsondata.province);
                    }

                    if( checkNone(jsondata.zipcode)  ){
                        address +=  jsondata.zipcode+' ';
                        $('#agent_zipcode').val(jsondata.zipcode);
                        $('#agent_zipcode_txt').val(jsondata.zipcode);
                    }

                    $('#contact_address').val(address);

                }
            });

            $('#tax_number').keyup(function (e) {

                var taxid = $(this).val();
                if( $.isNumeric(taxid) && taxid.length == 13  ){
                    $.ajax({
                        url: path +'?query='+ $(this).val()
                    }).done(function( jsondata ) {

                        if( jsondata == ''){

                            Swal.fire({
                                type: 'warning',
                                title: 'ไม่พบข้อมูล',
                                footer: 'กรุณาติดต่อเจ้าหน้าที่',
                                // showConfirmButton: true,
                                // confirmButtonText: 'รับทราบ'

                            })
                        }

                    });
                }

            });


            $('#select_all').on('ifChecked', function(event){
                BoxSettingSystem( 1 );
            });

            $('#select_all').on('ifUnchecked', function(event){
                BoxSettingSystem( 0 );
            });
            BoxSettingSystem(  $('input[name="select_all"]:checked').val() );

            $('#issue_type-1').on('ifChecked', function(event){
                BoxTime( 1 );
            });

            $('#issue_type-2').on('ifChecked', function(event){
                BoxTime( 2 );
            });
            BoxTime(  $('input[name="issue_type"]:checked').val() );

            resetOrderNoFile();
        });

        function resetOrderNoFile(){

            if($('.btn_file_remove').length > 1){
                $('button[data-repeater-delete]').show();
            }else{
                $('button[data-repeater-delete]').hide();
            }

        }

        function BoxTime(val){
            if( val == 1){
                $('#start_date').prop("disabled", true);
                $('#end_date').prop("disabled", true);

                $('#start_date').prop("required", false);
                $('#end_date').prop("required", false);

                $('.box_time').hide();
            }else if( val == 2){
                $('#start_date').prop("disabled", false);
                $('#end_date').prop("disabled", false);

                $('#start_date').prop("required", true);
                $('#end_date').prop("required", true);

                $('.box_time').show();
            }else{
                $('#start_date').prop("disabled", true);
                $('#end_date').prop("disabled", true);

                $('#start_date').prop("required", false);
                $('#end_date').prop("required", false);

                $('.box_time').hide();
            }

        }

        function BoxSettingSystem(val){

            if( val == 1){
                $('.setting_system_multiple').val('').trigger('change');
                $('.setting_system_multiple').prop("disabled", true);
                $('.setting_system_multiple').prop("required", false);

                $(".setting_system_multiple > option").prop("selected", "selected");
                $(".setting_system_multiple").trigger("change");
            }else{
                $('.setting_system_multiple').prop("disabled", false);
                $('.setting_system_multiple').prop("required", true);

                if('{!! empty($agent) !!}'){
                    $('.setting_system_multiple').val('').trigger('change');
                }

            }

        }

        function convertDate(date){//แปลงรูปแบบวันที่
            if(date!=''){
                var dates = date.split('/');
                    date  = dates[2]-543 + '-' + dates[1] + '-' + dates[0];
            }
            return date;
        }

        function checkOverlap(agent_issue_type,
                              agent_start_date,
                              agent_end_date,
                              agent_select_all,
                              agent_system_ids,
                              now_issue_type,
                              now_start_date,
                              now_end_date,
                              now_select_all,
                              now_system_ids,
                             ){//เช็คการมอบสิทธิ์ที่ทับซ้อน

            if(agent_issue_type==1){//ถ้ารายการของที่เคยมอบไว้มอบตลอดไป
                agent_start_date = '0000-00-01';
                agent_end_date   = '9999-11-31';
            }

            if(now_issue_type==1){//ถ้ารายการที่จะมอบ มอบตลอดไป
                now_start_date = '0000-00-01';
                now_end_date   = '9999-11-31';
            }

            var result = false;
            if(now_select_all==1 || agent_select_all==1){//มอบให้ทั้งหมด
                result = checkTimeOverlap(agent_start_date, agent_end_date, now_start_date, now_end_date);
            }else{//มอบบางระบบทั้ง 2
                $.each(now_system_ids, function(now_system_id, now_system_name) {
                    if(agent_system_ids.hasOwnProperty(now_system_id)){//มีระบบที่เหมือนกัน ให้เทียบเวลา
                        result = checkTimeOverlap(agent_start_date, agent_end_date, now_start_date, now_end_date);
                        return false; // breaks
                    }
                });
            }

            return result;

        }

        function checkTimeOverlap(agent_start_date, agent_end_date, now_start_date, now_end_date){//เช็คเวลาที่ทับซ้อนกัน
            var result = false;
            agent_start_date = toDate(agent_start_date);
            agent_end_date   = toDate(agent_end_date);
            now_start_date   = toDate(now_start_date);
            now_end_date     = toDate(now_end_date);
            if(now_start_date >= agent_start_date && now_start_date <= agent_end_date){//วันที่เริ่มที่้เลือกคาบเกี่ยวกับช่วงเวลาเดิม
                result = true;
            }else if(now_end_date >= agent_start_date && now_end_date <= agent_end_date){//วันที่สิ้นสุดที่้เลือกคาบเกี่ยวกับช่วงเวลาเดิม
                result = true;
            }else if(agent_start_date >= now_start_date && agent_start_date <= now_end_date){//วันที่เริ่มเดิมคาบเกี่ยวกับช่วงเวลาที่เลือก
                result = true;
            }else if(agent_end_date >= now_start_date && agent_end_date <= now_end_date){//วันที่สิ้นสุดเดิมคาบเกี่ยวกับช่วงเวลาที่เลือก
                result = true;
            }
            return result;
        }

        function toDate(date){
            var dates = date.split('-');
            var date  = new Date(dates[0], dates[1]-1, dates[2]);
            return date;
        }

        function checkNone(value) {
            return value !== '' && value !== null && value !== undefined;
        }

    </script>
@endpush
