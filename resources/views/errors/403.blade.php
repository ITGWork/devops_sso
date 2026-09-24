@extends('layouts.app')
@push('css')
@endpush

@section('content')
    <section id="wrapper" class="error-page">
        <div class="error-box">
            <div class="error-body text-center">
                <img src="{!! asset('plugins/images/error/403.png') !!}" width="18%" class="img-rounded" />
                <h3 class="text-uppercase text-dark">ไม่ได้รับอนุญาต (รหัส 403)</h3>
                <p class="text-muted m-t-10 m-b-10 text-uppercase">คุณไม่ได้รับอนุญาตให้เข้าถึงหน้านี้</p>
                <p class="text-muted m-t-10 m-b-10">{{ config('app.name').' '.date('d-m-Y H:i:s') }}</p>
                <a href="{{ url('/') }}" class="btn btn-info btn-rounded waves-effect waves-light m-b-40">กลับหน้าแรก</a>
            </div>
            <footer class="footer text-center">© 2565 สมอ.</footer>
        </div>
    </section>
@endsection

@push('js')
    <script type="text/javascript">
        $(function() {
            $(".preloader").fadeOut();
        });
    </script>
@endpush
