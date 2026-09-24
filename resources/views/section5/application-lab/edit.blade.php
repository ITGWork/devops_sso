@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">แก้ไขคำขอเป็นผู้ตรวจสอบ (LAB) #{{ $applicationlab->id }}</h3>
                        <a class="btn btn-success pull-right" href="{{ url('/request-section-5/application-lab') }}">
                            <i class="icon-arrow-left-circle" aria-hidden="true"></i> กลับ</a>
  
                    <div class="clearfix"></div>
                    <hr>

                    @if ($errors->any())
                        <ul class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    {!! Form::model($applicationlab, [
                        'method' => 'PATCH',
                        'url' => ['/request-section-5/application-lab', $applicationlab->id],
                        'class' => 'form-horizontal',
                        'files' => true,
                        'id' => 'from_box'
                    ]) !!}

                    @include ('section5.application-lab.form', ['submitButtonText' => 'Update'])
      
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        jQuery(document).ready(function() {

            // form.blade.php (ใช้ร่วมกับหน้า create/edit/show) เซ็ต checked ของ radio
            // "ประเภทคำขอ" (applicant_type_display) และ "audit_type" แบบ hardcode ไว้เสมอ
            // (ไม่ได้ผูกกับข้อมูลจริงของคำขอ) ต้องแก้ให้ตรงกับค่าจริงตอนเปิดแก้ไข
            var auditType = '{{ $applicationlab->audit_type }}';
            if (auditType == '2') {
                $('#applicant_type_3').iCheck('uncheck');
                $('#applicant_type_4').iCheck('check');
                $('#audit_type_1').iCheck('uncheck');
                $('#audit_type_2').iCheck('check');
            } else {
                $('#applicant_type_3').iCheck('check');
                $('#applicant_type_4').iCheck('uncheck');
                $('#audit_type_1').iCheck('check');
                $('#audit_type_2').iCheck('uncheck');
            }

        });
    </script>
@endpush