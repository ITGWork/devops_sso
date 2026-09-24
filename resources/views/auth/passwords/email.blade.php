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
                <form class="form-horizontal form-material" method="POST" action="{{ url('forgot-password') }}">
                    @csrf
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <h3>{{ __('ลืมรหัสผ่าน') }}</h3>
                            <p class="text-muted">กรุณากรอกอีเมลเราจะส่งลิงค์เพื่อรีเซ็ตรหัสผ่านให้คุณ</p>
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

            @if ($errors->has('message'))
            Swal.fire({
                    position: 'center',
                    html: '{!! $errors->first('message') !!}',
                    showConfirmButton: true,
                    width: 800
            });
            @endif

            {{-- ลบข้อมูลในช่องอีเมล กรณีที่อาจจะเติมโดยเบราเซอร์ --}}
            @if (!$errors->first())
                setTimeout(function(){
                    $('#email').val('');
                }, 100);
            @endif

        });
    </script>
@endpush
