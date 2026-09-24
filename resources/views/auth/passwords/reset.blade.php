@extends('layouts.app')

@section('content')

    <style>
        .input-login{
            border-bottom: 1px solid black !important;
        }
    </style>

    <section id="wrapper" class="login-register">
        <div class="login-box">
            <div class="white-box">
                <form class="form-horizontal form-material" method="post" action="{{ route('password.request') }}">
                    {{csrf_field()}}
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group ">
                        <div class="col-xs-12">
                            <h3>{{ __('รีเซ็ตรหัสผ่าน') }}</h3>
                        </div>
                    </div>

                    <div class="form-group">

                        <div class="col-xs-12">
                            <input placeholder="อีเมล" id="email" type="email"
                                   class="form-control input-login {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                                   value="{{ $email ?: old('email') }}" required autofocus>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback text-danger">
                                    {!! $errors->first('email') !!}
                                </span>
                            @endif

                            @if ($errors->first())
                                <span class="invalid-feedback text-danger">
                                    {!! $errors->first() !!}
                                </span>
                             @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input id="password" placeholder="รหัสผ่าน" type="password"
                                   class="form-control input-login {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                   name="password" required>

                            @if ($errors->has('password'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12">
                            <input placeholder="ยืนยันรหัสผ่าน" id="password-confirm" type="password" class="input-login form-control"
                                   name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-12">
                            <small>
                                กรุณากำหนดรหัสผ่านให้มีตัวอักษรความยาวไม่น้อยกว่า 8 อักษร โดยมีการผสมกันระหว่างตัวอักษรภาษาอังกฤษ ตัวพิมพ์ใหญ่หรือตัวพิมพ์เล็ก ตัวเลข และสัญลักษณ์เข้าด้วยกัน
                            </small>
                        </div>
                    </div>

                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light"
                                    type="submit">รีเซ็ต
                            </button>
                        </div>
                    </div>
                    <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                            <p><a href="{{ url('login') }}" class="text-primary m-l-5"><b>เข้าสู่ระบบ</b></a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script src="{{asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')}}"></script>
    <script type="text/javascript">

        $(document).ready(function() {

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

            $("#password-confirm").change(function(event) {
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
                        $('#password-confirm').focus();
                    });
                    $('#password-confirm').val('');
                }
            });

            {{-- ลบข้อมูลในช่องอีเมล กรณีที่อาจจะเติมโดยเบราเซอร์ --}}
            @if (!$errors->first())
                setTimeout(function(){
                    $('#email, #password').val('');
                }, 100);
            @endif

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
