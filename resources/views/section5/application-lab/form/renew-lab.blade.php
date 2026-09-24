<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label col-md-4"><h6>ข้อมูลเดิมห้องปฏิบัติการ</h6></label>
            <div class="col-md-9">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group{{ $errors->has('lab_old_name') ? 'has-error' : ''}}">
            {!! Form::label('lab_old_name', 'ชื่อห้องปฏิบัติการ'.' :', ['class' => 'col-md-2 control-label']) !!}
            <div class="col-md-10">
                {!! Form::text('lab_old_name', !empty( $applicationlab->lab_name )?$applicationlab->lab_name:null,['class' => 'form-control', 'readonly' => true ]) !!}
                {!! $errors->first('lab_old_name', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('lab_old_address') ? 'has-error' : ''}}">
            {!! Form::label('lab_old_address', 'เลขที่'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('lab_old_address', !empty( $applicationlab->lab_address )?$applicationlab->lab_address:null,['class' => 'form-control', 'readonly' => true ]) !!}
                {!! $errors->first('lab_old_address', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('lab_old_building') ? 'has-error' : ''}}">
            {!! Form::label('lab_old_building', 'อาคาร/หมู่บ้าน'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('lab_old_building', !empty( $applicationlab->lab_building )?$applicationlab->lab_building:null,  ['class' => 'form-control','readonly' => true  ]) !!}
                {!! $errors->first('lab_old_building', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('lab_old_soi') ? 'has-error' : ''}}">
            {!! Form::label('lab_old_soi', 'ตรอก/ซอย'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('lab_old_soi', !empty( $applicationlab->lab_soi )?$applicationlab->lab_soi:null,  ['class' => 'form-control', 'readonly' => true  ]) !!}
                {!! $errors->first('lab_old_soi', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('lab_old_moo') ? 'has-error' : ''}}">
            {!! Form::label('lab_old_moo', 'หมู่'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('lab_old_moo', !empty( $applicationlab->lab_moo )?$applicationlab->lab_moo:null,['class' => 'form-control', 'readonly' => true ]) !!}
                {!! $errors->first('lab_old_moo', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('lab_old_road') ? 'has-error' : ''}}">
            {!! Form::label('lab_old_road', 'ถนน'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('lab_old_road', !empty( $applicationlab->lab_road )?$applicationlab->lab_road:null,['class' => 'form-control', 'readonly' => true ]) !!}
                {!! $errors->first('lab_old_road', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('lab_old_subdistrict_txt') ? 'has-error' : ''}}">
            {!! Form::label('lab_old_subdistrict_txt', 'แขวง/ตำบล'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('lab_old_subdistrict_txt', !empty( $applicationlab->LabSubdistrictName )?$applicationlab->LabSubdistrictName:null,  ['class' => 'form-control lab_old_input_show', 'readonly' => true ]) !!}
                {!! $errors->first('lab_old_subdistrict_txt', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('lab_old_district_txt') ? 'has-error' : ''}}">
            {!! Form::label('lab_old_district_txt', 'เขต/อำเภอ'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('lab_old_district_txt', !empty( $applicationlab->LabDistrictName )?$applicationlab->LabDistrictName:null,['class' => 'form-control lab_old_input_show', 'readonly' => true ]) !!}
                {!! $errors->first('lab_old_district_txt', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('lab_old_province_txt') ? 'has-error' : ''}}">
            {!! Form::label('lab_old_province_txt', 'จังหวัด'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('lab_old_province_txt', !empty( $applicationlab->LabProvinceName )?$applicationlab->LabProvinceName:null,  ['class' => 'form-control lab_old_input_show', 'readonly' => true ]) !!}
                {!! $errors->first('lab_old_province_txt', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('lab_old_zipcode_txt') ? 'has-error' : ''}}">
            {!! Form::label('lab_old_zipcode_txt', 'รหัสไปรษณีย์'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('lab_old_zipcode_txt', !empty( $applicationlab->LabPostcodeName )?$applicationlab->LabPostcodeName:null,['class' => 'form-control lab_old_input_show', 'readonly' => true  ]) !!}
                {!! $errors->first('lab_old_zipcode_txt', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('lab_old_phone') ? 'has-error' : ''}}">
            {!! Form::label('lab_old_phone', 'เบอร์โทรศัพท์'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('lab_old_phone', !empty( $applicationlab->lab_phone )?$applicationlab->lab_phone:null,['class' => 'form-control', 'readonly' => true ]) !!}
                {!! $errors->first('lab_old_phone', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('lab_old_fax') ? 'has-error' : ''}}">
            {!! Form::label('lab_old_fax', ' เบอร์โทรสาร'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('lab_old_fax', !empty( $applicationlab->lab_fax )?$applicationlab->lab_fax:null,  ['class' => 'form-control', 'readonly' => true ]) !!}
                {!! $errors->first('lab_old_fax', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    {!! Form::hidden('lab_old_subdistrict_id', !empty( $applicationlab->lab_subdistrict_id )?$applicationlab->lab_subdistrict_id:null, [ 'class' => 'lab_old_input_show', 'id' => 'lab_old_subdistrict_id' ] ) !!}
    {!! Form::hidden('lab_old_district_id', !empty( $applicationlab->lab_district_id )?$applicationlab->lab_district_id:null, [ 'class' => 'lab_old_input_show', 'id' => 'lab_old_district_id' ] ) !!}
    {!! Form::hidden('lab_old_province_id', !empty( $applicationlab->lab_province_id )?$applicationlab->lab_province_id:null, [ 'class' => 'lab_old_input_show', 'id' => 'lab_old_province_id' ] ) !!}
    {!! Form::hidden('lab_old_zipcode', !empty( $applicationlab->lab_zipcode )?$applicationlab->lab_zipcode:null, [ 'class' => 'lab_old_input_show', 'id' => 'lab_old_zipcode' ] ) !!}
</div>
