@extends('layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">รายละเอียดคำขอเป็นผู้ตรวจสอบ (LAB) #{{ $applicationlab->id }}</h3>
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
                        'files' => true
                    ]) !!}

                    <div id="box-readonly">
                        @include ('section5.application-lab.form', ['submitButtonText' => 'Update'])
                    </div>
      
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')

    <script>
        function lockReadonlyBox() {
            $('#box-readonly').find('button[type="submit"]').remove();
            $('#box-readonly').find('.icon-close').parent().remove();
            $('#box-readonly').find('.fa-copy').parent().remove();
            $('#box-readonly').find('input').prop('disabled', true);
            $('#box-readonly').find('textarea').prop('disabled', true);
            $('#box-readonly').find('select').prop('disabled', true);
            $('#box-readonly').find('.bootstrap-tagsinput').prop('disabled', true);
            $('#box-readonly').find('span.tag').children('span[data-role="remove"]').remove();
            $('#box-readonly').find('button').prop('disabled', true);
            $('#box-readonly').find('button').remove();
            $('#box-readonly').find('.btn-remove-file').parent().remove();
            $('#box-readonly').find('.show_tag_a').hide();
            $('#box-readonly').find('.input_show_file').hide();
        }

        jQuery(document).ready(function() {

            // form.blade.php (ใช้ร่วมกับหน้า create/edit) เซ็ต checked ของ radio
            // "ประเภทคำขอ" (applicant_type_display) และ "audit_type" แบบ hardcode ไว้เสมอ
            // (ไม่ได้ผูกกับข้อมูลจริงของคำขอ) ต้องแก้ให้ตรงกับค่าจริงเฉพาะหน้า show นี้เท่านั้น
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

            lockReadonlyBox();

            // รายการทดสอบ/เครื่องมือใน #box_scope_request ถูกสร้างแบบ async
            // (โหลดผ่าน ajax ใน modal-scope.blade.php หลัง document ready)
            // จึงยังไม่มีอยู่ตอนที่ lockReadonlyBox() รันครั้งแรก ต้อง disable ซ้ำทุกครั้งที่มี element ใหม่ถูกเพิ่มเข้ามา
            var scopeBox = document.getElementById('box_scope_request');
            if (scopeBox) {
                var scopeObserver = new MutationObserver(function() {
                    lockReadonlyBox();
                });
                scopeObserver.observe(scopeBox, { childList: true, subtree: true });
            }

        });
    </script>

@endpush