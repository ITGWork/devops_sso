@extends('layouts.app')
@push('css')
@endpush

@section('content')
    <section id="wrapper" class="error-page">
        <div class="error-box">
            <div class="error-body text-center">
                <img src="{!! asset('plugins/images/error/404.png') !!}" width="22%" class="img-rounded" />
                <h3 class="text-uppercase text-dark">ขออภัย ไม่พบหน้าที่คุณต้องการ (รหัส 404)</h3>
                <p class="text-muted m-t-10 m-b-10">ลิงก์ที่คุณต้องการอาจเสียหายหรือหน้าอาจถูกลบ</p>
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
