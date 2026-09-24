@extends('layouts.app')

@push('css')
    <style>
        .login-box2 {
            display: flex;
            align-items: center;
            height: 100vh;
            justify-content: center;
        }
        a.detail-title:hover{
            color: DarkTurquoise !important;
            text-decoration:underline;
        }
        .form-container{
            background-color: #fff;
            /* font-family: 'Poppins', sans-serif; */
            font-size: 0;
            box-shadow: 0 0 25px -15px rgba(0,0,0,0.3);
        }
        .form-container .left-content{
            background-color: #0078bc;
            /* font-family: 'Oswald', sans-serif; */
            width: 40%;
            height: 100%;
            /* padding: 40px 50px; */
            padding-top: 40px !important;
            padding-left: 20px !important;
            padding-right: 15px !important;
            padding-bottom: 50px !important;
            display: inline-block;
            vertical-align: top;
        }
        .form-container .left-content .title{
            color: #fff;
            font-size: 50px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin: 0 0 30px;
        }
        .form-container .left-content .title2{
            color: #fff;
            font-size: 25px;
            font-weight: 500;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 0 0 55px;
        }
        .form-container .left-content .sub-title{
            color: #fff;
            font-size: 25px;
            font-weight: 300;
            text-transform: uppercase;
            margin: 0;
        }

        .form-container .left-content .detail-title{
            color: #fff;
            /* font-size: 25px; */
            font-weight: 300;
            /* text-transform: uppercase; */
            margin: 0;
        }
        .form-container .right-content{
            text-align: center;
            width: 60%;
            height: 100%;
            padding: 60px 50px;
            display: inline-block;
        }
        .form-container .right-content .form-title{
            color: #0078bc;
            /* font-family: 'Oswald', sans-serif; */
            font-size: 30px;
            font-weight: 400;
            text-align: left;
            text-transform: uppercase;
            padding: 0 0 15px;
            margin: 0 0 30px;
            border-bottom: 1px solid #beb9ba;
        }
        .form-container .right-content .form-horizontal {
            color: #999;
            font-size: 14px;
            text-align: left;
            margin: 0 0 15px;
        }
        .form-container .form-horizontal .form-group{ margin: 0 0 20px; }
        .form-container .form-horizontal .form-group:nth-of-type(2){ margin-bottom: 35px; }
        .form-container .form-horizontal .form-group label{ font-weight: 500; }
        .form-container .form-horizontal .form-control{
            color: #888;
            background: #f9f9f9;
            font-weight: 400;
            letter-spacing: 1px;
            height: 40px;
            padding: 6px 12px;
            border-radius: 5px;
            border: none;
            box-shadow: none;
        }
        .form-container .form-horizontal .form-control:focus{ box-shadow: 0 0 5px #FF97A8; }
        .form-container .form-horizontal .signin{
            color: #fff;
            background: linear-gradient(to right, #FF638E, #FF97A8);
            font-size: 15px;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: capitalize;
            width: 100%;
            padding: 9px 5px;
            margin: 0 0 20px;
            border-radius: 50px;
            transition: all 0.3s ease 0s;
        }
        .form-container .form-horizontal .btn:hover,
        .form-container .form-horizontal .btn:focus{
            box-shadow: 0 0 10px #FF97A8;
            outline: none;
        }
        .form-container .form-horizontal .remember-me{
            width: calc(100% - 105px);
            display: inline-block;
        }
        .form-container .form-horizontal .remember-me .check-label{
            color: #999;
            font-size: 12px;
            font-weight: 400;
            vertical-align: top;
            display: inline-block;
        }
        .form-container .form-horizontal .remember-me .checkbox{
            height: 17px;
            width: 17px;
            min-height: auto;
            margin: 0 1px 0 0;
            border: 2px solid #FF97A8;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
            position: relative;
            appearance: none;
            -moz-appearance: none;
            -webkit-appearance: none;
            transition: all 0.3s ease 0s;
        }
        .form-container .form-horizontal .remember-me .checkbox:before{
            content: '';
            height: 5px;
            width: 10px;
            border-bottom: 2px solid #fff;
            border-left: 2px solid #fff;
            transform: rotate(-45deg);
            position: absolute;
            left: 2px;
            top: 2.5px;
            transition: all 0.3s ease;
        }
        .form-container .form-horizontal .remember-me .checkbox:checked{ background-color: #FF97A8; }
        .form-container .form-horizontal .remember-me .checkbox:checked:before{ opacity: 1; }
        .form-container .form-horizontal .remember-me .checkbox:not(:checked):before{ opacity: 0; }
        .form-container .form-horizontal .remember-me .checkbox:focus{ outline: none; }
        .form-container .form-horizontal .forgot{
            color: #999;
            font-size: 12px;
            text-align: right;
            width: 100px;
            vertical-align: top;
            display: inline-block;
            transition: all 0.3s ease 0s;
        }
        .form-container .form-horizontal .forgot:hover{ text-decoration: underline; }
        .form-container .right-content .separator{
            color: #999;
            font-size: 15px;
            text-align: center;
            margin: 0 0 15px;
            display: block;
        }
        .form-container .right-content .social-links{
            text-align: center;
            padding: 0;
            margin: 0 0 -1px;
            list-style: none;
        }
        .form-container .right-content .social-links li{
            margin: 0 2px 0px;
            display: inline-block;
        }
        .form-container .right-content .social-links li a{
            font-size: 16px;
            padding: 9px 7px;
            border-radius: 5px;
            display: block;
            transition: all 0.3s ease 0s;
        }

        .form-container .right-content .social-links li a i{ margin-right: 10px; }
        .form-container .right-content .social-links li a:hover{ box-shadow: 0 0 5px rgba(0,0,0,0.5); }
        .form-container .right-content .signup-link{
            color: #999;
            font-size: 13px;
        }
        .form-container .right-content .signup-link a{
            color: #0078bc;
            transition: all 0.3s ease 0s;
        }
        .form-container .right-content .signup-link a:hover{ text-decoration: underline; }
        @media only screen and (max-width:767px){
            .form-container .left-content,
            .form-container .right-content{
                width: 100%;
                padding: 30px;
            }
            .form-container .left-content .title{ margin: 0 0 20px; }
            .form-container .left-content .sub-title{ font-size: 40px; }
            .form-container .left-content{ display: none;}
        }


    </style>
@endpush

@section('content')

@php
    //URL หลัก Login เสร็จ
    $redirect_uri = request()->get('redirect_uri');
@endphp

<section id="wrapper" class="login-register flexbox-container">
    <form class="form-horizontal form-material" id="loginform" method="post" action="{{ route('login') }}">

        {{csrf_field()}}
        <input type="hidden" name="redirect_uri" value="{{ $redirect_uri }}">

        <div class="login-box2">
            <div class="col-xl-7 col-md-6 col-sm-12">

                <div class="card">

                    {{-- <div class="alert alert-warning text-dark alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button> --}}
                        {{-- <b>แจ้งปิดปรับปรุงระบบ : </b><br>วันอาทิตย์ ที่ 20 พฤศจิกายน พ.ศ. 2565 เวลา 18.00 น. <u>ถึง</u> วันจันทร์ ที่ 21 พฤศจิกายน พ.ศ. 2565 เวลา 08.00 น. --}}
                        {{-- <b>📢 ประกาศสำนักงานมาตรฐานผลิตภัณฑ์อุตสาหกรรม (สมอ.)</b> <br>เรื่อง การปฏิบัติงานนอกสถานที่ตั้งเป็นกรณีพิเศษ (Work from home) <br>ในวันอังคารที่ 27 กุมภาพันธ์ 2567  

ทั้งนี้ ผู้มาติดต่อราชการ สามารถติดต่อ สมอ. ได้ในช่องทางอิเล็กทรอนิกส์ตามประกาศฉบับนี้ และ<a href="https://i.tisi.go.th/e-license/index.php/using-joomla/extensions/components/content-component/article-categories/98-27-2567" target="_blank">ตามเอกสารแนบ</a>

ขออภัยในความไม่สะดวกค่ะ 🙏 --}}
                    {{-- </div> --}}

                <div class="alert alert-info">
                    <a href="https://i-next.industry.go.th/"><img src="https://service.tisi.go.th/images/tisiweb-carousel/0748cd22c1edece63b288263ecfa4ec5.jpg" width="100%" height="100%" border=0></a>
                     <!-- <a href="#"><img src="https://appdb.tisi.go.th/TISINSW/pic/ok4.png" width="40%" height="40%" border=0></a> -->
                </div> 

                 <!-- <div class="alert alert-info">
                    <a href="#"><img src="https://appdb.tisi.go.th/TISINSW/pic/ok4.png" width="40%" height="40%" border=0></a>
                </div>  -->
                
                <!-- <div class="alert alert-info" align ="center">
                    <a href="https://docs.google.com/forms/d/e/1FAIpQLSfiaBJa22WSV347jAbZED1-y9KvBReXsATjR1XPw_3-jn6XOw/viewform" target="_blank"><img src="https://appdb.tisi.go.th/tis/banner.jpg"  width="850px" height="300px" border=0></a>
                </div>  -->
                    <div class="row m-0">

                        <div class="form-container">

                            <div class="right-content form-horizontal">

                                <h3 class="form-title"> บริการอิเล็กทรอนิกส์ สมอ.</h3>

                                <span class="signup-link">
                                    <div id="alert-message"></div>
                                </span>

                                <div class="form-group">
                                    <div class="col-xs-12 signup-link m-b-5">
                                        <span class="pull-left font-15">
                                            <span class="text-dark">ชื่อผู้ใช้งาน (TaxID หรือ Passport NO.)</span>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#username-info" title="คลิกเพื่ออ่านเพิ่มเติม"><i class="fa fa-info-circle"></i></a>
                                        </span>
                                    </div>
                                    <div class="col-xs-12 signup-link">
                                        <input id="username" placeholder="ชื่อผู้ใช้งาน" class="form-control input-login{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" required autofocus>
                                        <span class="text-danger" id="username-error"></span>
                                        @if ($errors->first())
                                            <span class="text-danger">
                                                {{ $errors->first() }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-xs-12 signup-link ">
                                        <input id="password" type="password" class="form-control input-login{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="รหัสผ่าน">
                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group signup-link">
                                    <div class="col-md-12">
                                        <a href="{{ url('forgot-user') }}" class="text-dark pull-left"><i class="fa fa-user"></i> ลืมชื่อผู้ใช้?</a>
                                        <a href="{{ url('check-email') }}" class="text-dark text-center"><i class="fa fa-envelope"></i> ตรวจสอบอีเมล</a>
                                        <a href="{{ route('password.request') }}" class="text-dark pull-right"><i class="fa fa-lock"></i> ลืมรหัสผ่าน?</a>
                                    </div>
                                </div>

                                <div class="form-group text-center m-t-20">
                                    <div class="col-xs-12">
                                        <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit"> เข้าใช้งาน </button>
                                    </div>
                                </div>

                                <hr>
                                <div class="form-group text-center m-t-20">
                                    <div class="col-xs-12">
                                        <a href="https://www3.tisi.go.th/session/LoginThaID2.asp?state=6"><input type="button" class="btn btn-primary btn-lg  btn-block" value="เข้าใช้ระบบ e-scope ผ่าน ThaiD"></a>
                                    </div>
                                </div>
                                <div class="form-group text-center m-t-20">
                                    <div class="col-xs-12">
                                        <a href="https://www3.tisi.go.th/session/LoginThaID2.asp?state=3"><input type="button" class="btn btn-primary btn-lg btn-block" value="เข้าใช้ระบบ TISI-NSW ผ่าน ThaiD"></a>
                                    </div>
                                </div>

                                <span class="separator">
                                <font color="red">*</font><font color="red">ระบบเชื่อมโยงข้อมูลระหว่างกระทรวงอุตสาหกรรม กับ กรมการปกครอง ขัดข้อง ไม่สามารถลงทะเบียนใหม่ได้ ผปก. สามารถเข้าใช้งานผ่าน ThaiD แทนชั่วคราว</font>
                                </span>

                                <span class="signup-link">
                                    <h4><a href="{{url('register')}}" class="m-l-5"><b>ลงทะเบียนสำหรับผู้ประกอบการ</b></a></h4>
                                </span>

                                <div style="border-top: 0.1px solid rgb(218, 213, 213);"><br></div>

                                <ul class="social-links">
                                    {{-- <li><a href="{{ asset('downloads/manual/SSO65 - User_Manual -20062022_V1.0_Baseline.pdf') }}" target="_blank"><b><i class="mdi mdi-book-open-page-variant"></i>คู่มือการใช้งาน</b></a></li> --}}
                                    <li><a href="{{ url('manual') }}" target="_blank"><b><i class="mdi mdi-book-open-page-variant"></i>คู่มือการใช้งาน</b></a></li>
                                    <li><a href="{{ url('/help') }}" class="m-l-10" target="_blank"><b><i class="mdi mdi-help-circle"></i>พบปัญหาการใช้งาน</b></a></li>
                                </ul>

                            </div>

                            <div class="left-content">
                                <center>
                                    <h4>
                                        <img src="{!! asset('images/logo01.png') !!}" width="90" height="90">
                                    </h4>
                                    <h4 class="title">TiSI</h4>
                                    <h4 class="sub-title">@Line</h4>
                                    <h4>
                                        <img src="{!! asset('images/QR-Code.png') !!}" width="120" height="120">
                                    </h4>
                                </center>
                                <h6 class="detail-title">
                                    ศูนย์เทคโนโลยีสารสนเทศและการสื่อสาร
                                </h6>
                                <h6 class="detail-title">
                                    โทร. 0 2430 6834 ต่อ 2450, 2451
                                </h6>
                                <h6 class="detail-title">
                                    e-Mail : nsw@tisi.mail.go.th
                                </h6><br>
                                <h6 class="detail-title" style="padding-top: 10px">
                                    กองควบคุมมาตรฐาน
                                </h6>
                                <h6 class="detail-title">
                                    โทร. 0 2430 6821 ต่อ 1002, 1003
                                </h6><br>
                                <h6 class="detail-title" style="padding-top: 10px">
                                    สำนักงานคณะกรรมการการมาตรฐานแห่งชาติ
                                </h6>
                                <h6 class="detail-title">
                                    โทร 024306825 ต่อ 1402
                                </h6>
                                <h6 class="detail-title" style="padding-top: 10px">
                                    กรณีนำเข้ามาใช้เองจำนวนน้อย  (กรณียกเลิก EXEMPT5) <a href='https://www.tisi.go.th/data/pdf/about/announce_18_100366.pdf' target="blank" style="color:white;">อ่านประกาศ</a>
                                </h6>
                            <h6 class="detail-title">
                                    โทร 024306815 ต่อ 3001 - 3003
                                </h6>
                            </div> 
                            <!-- ปิด leftcontent -->
                        </div>

                    </div>
                 
                </div>
            </div>
        </div>
    </form>

    <div id="username-info" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="username-info-label" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title h3 text-primary" id="username-info-label">คำอธิบายเกี่ยวกับชื่อผู้ใช้งาน</h4>
                </div>
                <div class="modal-body">
                    <p class="font-20">กรอกชื่อผู้ใช้งาน เป็นเลขประจำตัวผู้เสียภาษี/เลขหนังสือเดินทาง ตามที่ลงทะเบียนไว้</p>
                    <p class="font-20">สำหรับผู้ประกอบการที่ใช้งานระบบ e-Accreditation <br>ให้กรอกเลขประจำตัวผู้เสียภาษี ตามด้วยเลขสาขา 4 หลัก</p>
                    <p class="font-20">หากไม่ทราบชื่อผู้ใช้งาน คลิกที่ "ลืมชื่อผู้ใช้ ?" หรือติดต่อเจ้าหน้าที่</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    @php
        $user_id = Session::get('2fa:user:id', null);
    @endphp
    @if(!is_null($user_id)){{-- ส่งฟอร์มและตรวจสอบ username & password ถูกต้อง --}}
        @php $user = App\User::find($user_id); @endphp
        @if($user->google2fa_status==1){{-- เชื่อมต่อ google authen แล้ว --}}
            @include('auth/google2fa/validate')
        @else{{-- ยังไม่ได้เชื่อมต่อ google authen --}}
            @php
                $auto_open = true;
                $action_url = '2fa/setup';
            @endphp
            @include('auth/google2fa/setup', compact('user', 'auto_open', 'action_url'))
        @endif
    @endif

</section>

@endsection

@push('js')
    <script src="{{asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')}}"></script>
    <script>
        $(document).ready(function () {

            // กันพิมพ์ภาษาไทยในช่อง password (ต้องเป็นภาษาอังกฤษ/ตัวเลข/สัญลักษณ์เท่านั้น)
            // กันเคส IME ภาษาไทยเปิดค้างอยู่แล้วผู้ใช้พิมพ์รหัสผ่านเป็นภาษาไทยโดยไม่รู้ตัว
            // (พิมพ์ผิดแบบมองไม่เห็น เพราะช่อง password ซ่อนตัวอักษรด้วยจุด)
            $('#password').on('keypress', function (event) {
                var k = event.which || event.keyCode;
                // เช็คช่วงตัวอักษรไทยทั้งแบบ non-unicode และ unicode
                if ((k >= 161 && k <= 255) || (k >= 3585 && k <= 3675)) {
                    event.preventDefault();
                    return false;
                }
            });

            @if(session()->has('flash_message'))
                Swal.fire({
                        position: 'center',
                        html: '<h4 class="text-dark">{!! session()->get('flash_message') !!}</h3>',
                        showConfirmButton: true,
                        width: 800
                });
            @endif

            @if(session()->has('flash_lock_login'))
                @php
                    $config = HP::getConfig();
                    $sso_login_fail_lock_time = property_exists($config, 'sso_login_fail_lock_time') ? $config->sso_login_fail_lock_time : 15 ;
                    $sso_login_fail_amount    = property_exists($config, 'sso_login_fail_amount') ? (int)$config->sso_login_fail_amount : 5 ;

                @endphp
                /* Lock การใช้งาน */
                $('#alert-message').html('<div class="alert alert-danger"> คุณกรอกชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้องครบ {{$sso_login_fail_amount}} ครั้ง ระบบจะระงับบัญชีผู้ใช้งานของคุณเป็นเวลา {{$sso_login_fail_lock_time}} นาที </div>');
                //$('button[type="submit"]').prop('disabled', true);
            @endif

            @if(session()->has('message'))
                Swal.fire({
                    title: '{{session()->get('message')}}',
                    width: 800,
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'ลงทะเบียน',
                    cancelButtonText: 'ยกเลิก',
                }).then((result) => {

                        if (result.value) {
                                window.location.assign("{{ url('register') }}");
                        }
                });
            @endif

            $('#username').keyup(function(event) {
                checkMail($(this).val());
            });
            $('#username').change(function(event) {
                checkMail($(this).val());
            });
            $('#username').blur(function(event) {
                checkMail($(this).val());
            });
        });

        function checkMail(email){
            var result = String(email)
                   .toLowerCase()
                   .match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
            var check = result===null ? false : true ;
            if(check===true){//ถ้าเป็น mail ไม่ให้ไปต่อ
                $('#username-error').text('ชื่อผู้ใช้งาน เป็นเลขประจำตัวผู้เสียภาษี/เลขหนังสือเดินทาง');
            }else{
                $('#username-error').text('');
            }
        }

    </script>
@endpush
