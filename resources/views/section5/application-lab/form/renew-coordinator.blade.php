<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label col-md-4"><h6>ข้อมูลเดิมผู้ประสานงาน</h6></label>
            <div class="col-md-9">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group readonly{{ $errors->has('co_old_name') ? 'has-error' : ''}}">
            {!! Form::label('co_old_name', 'ชื่อผู้ประสานงาน'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('co_old_name', !empty( $applicationlab->co_name )?$applicationlab->co_name:null,['class' => 'form-control co_old_input_show', 'readonly' => true ]) !!}
                {!! $errors->first('co_old_name', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group readonly {{ $errors->has('co_old_position') ? 'has-error' : ''}}">
            {!! Form::label('co_old_position', 'ตำแหน่ง'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('co_old_position', !empty( $applicationlab->co_position )?$applicationlab->co_position:null,  ['class' => 'form-control co_old_input_show', 'readonly' => true ]) !!}
                {!! $errors->first('co_old_position', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group readonly{{ $errors->has('co_old_mobile') ? 'has-error' : ''}}">
            {!! Form::label('co_old_mobile', 'โทรศัพท์มือถือ'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('co_old_mobile', !empty( $applicationlab->co_mobile )?$applicationlab->co_mobile:null,['class' => 'form-control co_old_input_show', 'readonly' => true ]) !!}
                {!! $errors->first('co_old_mobile', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group {{ $errors->has('co_old_tel') ? 'has-error' : ''}}">
            {!! Form::label('co_old_tel', ' โทรศัพท์'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('co_old_tel', !empty( $applicationlab->co_tel )?$applicationlab->co_tel:null,  ['class' => 'form-control co_old_input_show', 'readonly' => true ]) !!}
                {!! $errors->first('co_old_tel', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group readonly{{ $errors->has('co_old_fax') ? 'has-error' : ''}}">
            {!! Form::label('co_old_fax', 'โทรสาร'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('co_old_fax', !empty( $applicationlab->co_fax )?$applicationlab->co_fax:null,['class' => 'form-control co_old_input_show', 'readonly' => true ]) !!}
                {!! $errors->first('co_old_fax', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group readonly{{ $errors->has('co_old_email') ? 'has-error' : ''}}">
            {!! Form::label('co_old_email', ' อีเมล'.' :', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-8">
                {!! Form::text('co_old_email', !empty( $applicationlab->co_email )?$applicationlab->co_email:null,  ['class' => 'form-control co_old_input_show', 'readonly' => true ]) !!}
                {!! $errors->first('co_old_email', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
    </div>
</div>
