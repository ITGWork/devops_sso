@extends('layouts.app')

@push('css')
    <style>
        .login-register {
            background: url("{{asset('plugins/images/login-register.jpg')}}") center center/cover no-repeat !important;
            height: 100%;
        }
        .input-login{
            border-bottom: 1px solid black !important;
        }
    </style>
@endpush

@section('content')

@php
    $request = request();
    $check_lock = App\LoginFail::CheckLock($request->ip());
@endphp

<section id="wrapper" class="login-register">
    <div class="login-box">
        <div class="white-box">
            <form class="form-horizontal form-material" id="loginform" method="post" action="{{ route('login') }}">
                {{csrf_field()}}
                <h3 class="text-center box-title m-b-20">
                    {{-- เข้าสู่ระบบ (SSO) --}}
                     บริการอิเล็กทรอนิกส์ สมอ.
                </h3>

                <div id="alert-message"></div>

                <div class="form-group ">
                    <div class="col-xs-12">
                        <input id="username" placeholder="ชื่อผู้ใช้" class="form-control input-login {{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" required autofocus>
                        @if ($errors->first())
                            <span class="text-danger">
                                {{ $errors->first() }}
                            </span>
                         @endif
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <input id="password" type="password" class="form-control input-login {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="รหัสผ่าน">
                        @if ($errors->has('password'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <a href="{{ route('password.request') }}" id="to-recover" class="text-dark pull-right"><i class="fa fa-lock m-r-5"></i>ลืมรหัสผ่าน?</a>
                    </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit"> เข้าใช้งาน </button>
                    </div>
                </div>

                <div class="form-group m-b-0">
                    <div class="col-sm-12 text-center">
                        <p><a href="{{url('register')}}" class="m-l-5"><b>ลงทะเบียนสำหรับผู้ประกอบการ</b></a></p>
                    </div>
                </div>
                <div style="border-top: 0.1px solid rgb(218, 213, 213);"><br></div>
                
                <div class="form-group m-b-0">
                    <div class="col-sm-6 text-left">
                        <p><a href="{{ asset('downloads/manual/SSO65 - User_Manual -13062022_V1.0_Baseline.pdf') }}" class="m-l-5" target="_blank"><b><i class="mdi mdi-book-open-page-variant"></i> คู่มือการใช้งาน</b></a></p>
                    </div>
                    <div class="col-sm-6 text-right">
                        <p><a href="{{ url('/contact') }}" class="m-l-5" target="_blank"><b><i class="mdi mdi-cellphone"></i> ติดต่อสอบถาม</b></a></p>
                    </div>
                </div>

                <div class="row">
                    <center>
                        <h4><strong>@Line</strong></h4>
                        <img src="{!! asset('images/QR-Code.png') !!}" width="200" height="200">
                    </center>
                </div>

            </form>

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

            @if(session()->has('flash_message'))
                Swal.fire({
                        position: 'center',
                        html: '<h4 class="text-dark">{!! session()->get('flash_message') !!}</h3>',
                        showConfirmButton: true,
                        width: 800
                });
            @endif

            @if(session()->has('flash_lock_login') || $check_lock===true)
                @php
                    $config = HP::getConfig();
                    $sso_login_fail_lock_time = property_exists($config, 'sso_login_fail_lock_time') ? $config->sso_login_fail_lock_time : 15 ;
                    $sso_login_fail_amount    = property_exists($config, 'sso_login_fail_amount') ? (int)$config->sso_login_fail_amount : 5 ;

                @endphp
                /* Lock การใช้งาน */
                $('#alert-message').html('<div class="alert alert-danger"> คุณกรอกชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้องครบ {{$sso_login_fail_amount}} ครั้ง ระบบจะระงับการใช้งานของคุณเป็นเวลา {{$sso_login_fail_lock_time}} นาที </div>');
                $('button[type="submit"]').prop('disabled', true);
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

        });
    </script>
@endpush
