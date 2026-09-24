@extends('layouts.master')

@push('css')

@endpush

@section('content')

<div class="container-fluid">
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">
                <h3 class="box-title m-b-0">เปลี่ยนรหัสผ่าน</h3>
                <p class="text-muted m-b-30 font-13">เปลี่ยนรหัสผ่านผู้ใช้งาน</p>

                {!! Form::open([
                    'method' => 'POST',
                    'url' => ['profile/password_save'],
                    'class' => 'form-horizontal'
                ]) !!}

                <div class="form-group {{ $errors->has('password') ? 'has-error' : ''}}">
                    {!! HTML::decode(Form::label('password', 'รหัสผ่าน : <span class="text-danger">*</span>', ['class' => 'col-md-3 control-label'])) !!}
                    <div class="col-md-4">
                        <div class="input-group">
                            <input id="password" type="password" title="กรอกรหัสผ่าน" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }} check_format_en"
                                   minlength="8" name="password" required placeholder="รหัสผ่าน" >
                            <span class="input-group-btn">
                                <button type="button" class="btn waves-effect waves-light btn-info" id="eye_change" tabindex="-1"><i class="fa fa-eye-slash"></i></button>
                            </span>
                        </div>

                        <div id="span-warn-password" class="text-danger">
                            {{ $errors->has('password') ? $errors->first('password') : '' }}
                        </div>

                        <span class="text-primary">
                            <small>
                                กรุณากำหนดรหัสผ่านให้มีตัวอักษรความยาวไม่น้อยกว่า 8 อักษร โดยมีการผสมกันระหว่างตัวอักษรภาษาอังกฤษ ตัวพิมพ์ใหญ่หรือตัวพิมพ์เล็ก ตัวเลข และสัญลักษณ์เข้าด้วยกัน
                            </small>
                        </span>
                    </div>

                </div>
                <div class="form-group">
                    {!! HTML::decode(Form::label('password_confirmation', 'ยืนยันรหัสผ่าน : <span class="text-danger">*</span>', ['class' => 'col-md-3 control-label'])) !!}
                    <div class="col-md-4">
                        <input id="password_confirmation" type="password" title="กรอกรหัสผ่าน" class="form-control {{ $errors->has('password_confirmation') ? ' is-invalid' : '' }} check_format_en"
                               minlength="8" name="password_confirmation" required placeholder="กรอกรหัสผ่านอีกครั้ง">
                        <span id="span-warn-password_confirmation" class="text-danger"></span>
                    </div>
                </div>

                    <div class="form-group box-btn-action">

                        <div class="col-md-offset-4 col-md-4">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-paper-plane"></i> บันทึก
                            </button>
                            <a class="btn btn-default" href="{{ url('/') }}">
                                <i class="fa fa-rotate-left"></i> ยกเลิก
                            </a>
                        </div>

                    </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
    <!-- /.row -->

</div>

@endsection

@push('js')
    {{-- <script src="{{asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')}}"></script> --}}
    <script src="{!! asset('plugins/components/toast-master/js/jquery.toast.js') !!}"></script>
    <script>

        $(document).ready(function() {

            @if(Session::has('save_success'))
                $.toast({
                    heading: 'Complete!',
                    text: '{{ Session::get('save_success') }}',
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'success',
                    hideAfter: 3000,
                    stack: 6
                });
            @endif

            //เปลี่ยน type ของรหัสผ่านเพื่อแสดงข้อมูล
            $('#eye_change').click(function(){
                var type = $('#password').prop('type');
                if (type == 'password') {
                   $('#password').prop('type', 'text');
                   $(this).html('<i class="fa fa-eye"></i>');
                } else {
                   $('#password').prop('type', 'password');
                   $(this).html('<i class="fa fa-eye-slash"></i>');
                }
            });

            //เมือ่คีย์รหัสผ่านเสร็จ
            $("#password").change(function(event) {
                var password = $(this).val();
                if(checkNone(password)){
                    var html = check_password_and_number(password);
                    if(html != ''){
                        Swal.fire({
                            title:'กรุณากรอกรหัสผ่านให้ตรงตามรูปแบบที่กำหนด',
                            html:html,
                            width: 700,
                            showDenyButton: true,
                            showCancelButton: false,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            $('#password').focus();
                        });
                        $("#password").val('');
                    }
                }
            });

            $("#password_confirmation").change(function(event) {
                var row = $(this).val();
                var password = $('#password').val();
                if(checkNone(row) && row != password){
                    Swal.fire({
                        title: 'ยืนยันรหัสผ่าน ไม่ตรงกับรหัสผ่าน!',
                        html: 'กรุณากรอกยืนยันรหัสผ่านใหม่อีกครั้ง',
                        width: 700,
                        showDenyButton: true,
                        showCancelButton: false,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        $('#password_confirmation').focus();
                    });
                    $('#password_confirmation').val('');
                }
            });

        });

        function checkNone(value) {
            return value !== '' && value !== null && value !== undefined;
        }

        function check_password_and_number(value) {

            var html = '';
            var password = value.toString();
            var passwords= password.split("");
            var format   = {upper: false, lower: false, number: false, symbol: false}
            var allows   = Array(' ', '!', '"', '#', '$', '%',
                                 '&', "'", '(', ')', '*', '+',
                                 ',', '-', '.', '/', ':', ';',
                                 '<', '=', '>', '?', '@', '[',
                                 '\\', ']', '^', '_', '`', '{',
                                 '|', '}', '~'
                                );
            var not_prefix = ' ';
            var not_allows = Array();

            $.each(passwords, function(index, value) {

                if(value.match(/[A-Z]/g) !== null){
                    format.upper = true;
                }

                if(value.match(/[a-z]/g) !== null){
                    format.lower = true;
                }

                if(value.match(/[0-9]/g) !== null){
                    format.number = true;
                }

                if(value.match(/[A-Z]/g) == null && value.match(/[a-z]/g) == null && value.match(/[0-9]/g) == null){//นอกเหนือจากที่กำหนดไว้
                    if(!allows.includes(value)){//ไม่อยู่ในอักขระพิเศษที่อนุญาต
                        if(!not_allows.includes('<p>-ไม่อนุญาตให้ใช้ตัวอักษร '+value+'</p>')){//ยังไม่มี
                            not_allows.push('<p>-ไม่อนุญาตให้ใช้ตัวอักษร '+value+'</p>');
                        }
                    }else{
                        format.symbol = true;
                        if(value===not_prefix && index==0){
                            html += '<p>-ไม่อนุญาตให้ใช้ <code>ช่องว่าง</code> นำหน้า</p>';
                        }
                        if(value===not_prefix && index==(passwords.length-1)){
                            html += '<p>-ไม่อนุญาตให้ใช้ <code>ช่องว่าง</code> ตัวสุดท้าย</p>';
                        }
                    }
                }

            });

            // if(format.upper===false){
            //     html += '<p>-อักษรภาษาอังกฤษตัวพิมพ์ใหญ่ อย่างน้อย 1 ตัว</p>';
            // }

            if(format.lower===false && format.upper===false){
                html += '<p>-อักษรภาษาอังกฤษตัวพิมพ์ใหญ่ หรือ ตัวพิมพ์เล็ก อย่างน้อย 1 ตัว</p>';
            }

            if(format.number===false){
                html += '<p>-ตัวเลข อย่างน้อย 1 ตัว</p>';
            }

            if(format.symbol===false){
                html += '<p>-สัญลักษณ์พิเศษอย่างน้อย 1 ตัว</p>';
            }

            if(not_allows.length > 0){
                html += not_allows.join('');
            }

            if(password.length < 8){
                html += '<p>-คุณกรอกรหัสผ่านได้ '+password.length +' อักษร</p>';
            }

            return html ;

        }

    </script>

@endpush
