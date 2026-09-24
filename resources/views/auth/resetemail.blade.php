@extends('layouts.app')

@push('css')
    <style>
        .input-login{
            border-bottom: 1px solid black !important;
        }
    </style>
@endpush

@section('content')

    <section id="wrapper" class="login-register">
        <div class="login-box">
            <div class="white-box">
                <form class="form-horizontal form-material" method="POST"  action="{{  route('reset-email.inform') }}">
                    @csrf
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <h3 class="text-center">{{ __('ขอลิงค์ยืนยันตัวตนทางอีเมล') }}</h3>
                            <p class="text-muted">กรุณากรอกอีเมลที่ได้ลงทะเบียนไว้ของคุณ</p>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <input id="email" placeholder="อีเมล" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }} input-login" name="email" value="{{ old('email') }}" required>

                            @if ($errors->has('email'))
                                <span class="invalid-feedback "  >
                                    @if ($errors->has('email') == "We can't find a user with that e-mail address.")
                                        <strong style="color:red;">{{ 'ไม่พบบัญชีผู้ใช้งานที่มีอีเมลนี้' }}</strong>
                                    @else
                                        <strong style="color:red;">{{ $errors->first('email') }}</strong>
                                    @endif

                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-6 p-r-0">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">
                                ตกลง
                            </button>
                        </div>
                        <div class="col-xs-6">
                            <a href="{{ url('login') }}" class="btn btn-default btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">
                                ยกเลิก
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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
