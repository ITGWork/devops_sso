@push('css')

@endpush
@php
    $user_login = auth()->user();
@endphp
<div class="form-group {{ $errors->has('entrepreneur_name') ? 'has-error' : ''}}">
  {!! Form::label('entrepreneur_name', 'ผู้ประกอบการ :', ['class' => 'col-md-4 control-label']) !!}
  <div class="col-md-4">
      {!! Form::text('entrepreneur_name', $user_login->name, ['class' => 'form-control', 'readonly' => true]) !!}
      {!! $errors->first('entrepreneur_name', '<p class="help-block">:message</p>') !!}
  </div>
</div>

<div class="form-group {{ $errors->has('contact_name') ? 'has-error' : ''}}">
  {!! Form::label('contact_name', 'ชื่อผู้ติดต่อ :', ['class' => 'col-md-4 control-label']) !!}
  <div class="col-md-4">
      {!! Form::text('contact_name', $user_login->contact_name, ['class' => 'form-control', 'readonly' => true]) !!}
      {!! $errors->first('contact_name', '<p class="help-block">:message</p>') !!}
  </div>
</div>

<div class="form-group {{ $errors->has('contact_tel') ? 'has-error' : ''}}">
  {!! Form::label('contact_tel', 'เบอร์โทร ผู้ติดต่อ :', ['class' => 'col-md-4 control-label']) !!}
  <div class="col-md-4">
      {!! Form::text('contact_tel', $user_login->contact_tel, ['class' => 'form-control', 'readonly' => true]) !!}
      {!! $errors->first('contact_tel', '<p class="help-block">:message</p>') !!}
  </div>
</div>

<div class="form-group {{ $errors->has('contact_email') ? 'has-error' : ''}}">
  {!! Form::label('contact_email', 'E-Mail ผู้ติดต่อ :', ['class' => 'col-md-4 control-label']) !!}
  <div class="col-md-4">
      {!! Form::text('contact_email', $user_login->email, ['class' => 'form-control', 'readonly' => true]) !!}
      {!! $errors->first('contact_email', '<p class="help-block">:message</p>') !!}
  </div>
</div>

<div class="form-group required{{ $errors->has('feedback') ? 'has-error' : ''}}">
  {!! Form::label('feedback', 'ข้อเสนอแนะ :', ['class' => 'col-md-4 control-label']) !!}
  <div class="col-md-4">
      {!! Form::textarea('feedback', null,  ['class' => 'form-control input_show', 'rows' => 4, 'required' => true]) !!}
      {!! $errors->first('feedback', '<p class="help-block">:message</p>') !!}
  </div>
</div>

<div class="form-group">
  <div class="col-md-offset-4 col-md-4">

    <button class="btn btn-primary" type="submit">
      <i class="fa fa-paper-plane"></i> บันทึก
    </button>
    <button class="btn btn-default" type="reset">
      <i class="fa fa-rotate-left"></i> ล้าง
    </button>
    <a class="btn btn-default" href="{{url('/')}}">
      <i class="fa fa-close"></i> ยกเลิก
    </a>
  </div>
</div>

@push('js')

@endpush
