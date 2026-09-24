@extends('layouts.app')

@section('content')
    <section id="wrapper" class="login-register">
        <div class="login-box">
            <div class="white-box text-center">
                <p class="text-muted">กำลังนำท่านไปยังระบบปลายทาง กรุณารอสักครู่...</p>
            </div>
        </div>
    </section>
@endsection

@push('js')
    <script src="{{asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            Swal.fire({
                position: 'center',
                html: '<h4 class="text-dark">{!! $message !!}</h4>',
                showConfirmButton: true,
                confirmButtonText: 'ยืนยัน',
                allowOutsideClick: false,
                allowEscapeKey: false,
                width: 800
            }).then((result) => {
                if (result.value) {
                    window.location.assign("{{ route('login.api_data_confirm.proceed') }}");
                }
            });
        });
    </script>
@endpush
