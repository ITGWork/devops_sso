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
                <form class="form-horizontal form-material" method="POST" action="{{ url('forgot-user') }}">
                    @csrf
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <h3>ลืมชื่อผู้ใช้งาน</h3>
                            <p class="text-muted">กรุณากรอกอีเมลเราจะส่งชื่อผู้ใช้งานให้คุณ</p>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <input id="email" placeholder="อีเมล" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }} input-login" name="email" value="{{ old('email') }}" required>
                            @if ($errors->has('email'))
                                <span class="invalid-feedback">
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
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

            @if(session()->has('message'))
                Swal.fire({
                    title: '{{ session()->get('message') }}',
                    width: 800,
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'ไปหน้าลงชื่อเข้าใช้งาน',
                    cancelButtonText: 'อยู่ต่อ',
                }).then((result) => {

                        if (result.value) {
                                window.location.assign("{{ url('login') }}");
                        }
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
