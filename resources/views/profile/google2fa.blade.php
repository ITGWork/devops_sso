@extends('layouts.master')

@push('css')
    <link href="{!! asset('plugins/components/switchery/dist/switchery.min.css') !!}" rel="stylesheet" />
    <style>
        .input-login{
            border-bottom: 1px solid black !important;
        }
    </style>
@endpush

@section('content')

@php
    $config = HP::getConfig();
    $user = auth()->user();
    $auto_open = Session::has('one_time_password_error') ? true : false;
    $action_url = 'profile/google2fa/enabled';

    $edit_disabled = $config->sso_google2fa_status==2 && $user->google2fa_status==1;//ห้ามแก้ไข ถ้าบังคับใช้ 2FA ทุกคน และ User ผูกเรียบร้อยแล้ว
@endphp

<div class="container-fluid">
    <!-- .row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="white-box">
                <h3 class="box-title m-b-0">ตั้งค่า ลงชื่อเข้าใช้งาน 2 ขั้นตอน</h3>
                <p class="text-muted m-b-30 font-13">ตั้งค่า ลงชื่อเข้าใช้งาน 2 ขั้นตอน ด้วย Google Authenticator</p>

                {!! Form::model($user, [
                    'method' => 'POST',
                    'url' => ['profile/google2fa/disabled'],
                    'class' => 'form-horizontal'
                ]) !!}

                    <div class="form-group">
                        {!! Form::label('google2fa_status', 'เปิดใช้งาน ลงชื่อเข้าใช้งาน 2 ขั้นตอน :', ['class' => 'col-sm-6 control-label']) !!}
                        <div class="col-sm-6">
                            {!! Form::checkbox('google2fa_status', 1, null, ['class' => 'google2fa_checkbox', 'disabled' => $edit_disabled, 'data-color' => '#13dafe']) !!}
                        </div>
                    </div>

                    <div class="form-group box-btn-action">

                        @if($edit_disabled)
                            <div class="alert alert-info"><i class="mdi mdi-pencil-lock"></i> เปิดใช้งาน 2FA แล้ว คุณไม่สามารถปิดมันได้เนื่องจากเป็นการกำหนดจากระบบให้ทุกคนต้องใช้งาน</div>
                        @else
                            <div class="col-md-offset-4 col-md-4">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fa fa-paper-plane"></i> บันทึก
                                </button>
                                <a class="btn btn-default" href="{{ url('/') }}">
                                    <i class="fa fa-rotate-left"></i> ยกเลิก
                                </a>
                            </div>
                        @endif

                    </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
    <!-- /.row -->

</div>

    @include('auth/google2fa/setup', compact('user', 'auto_open', 'action_url'))

@endsection

@push('js')
    <script src="{!! asset('plugins/components/toast-master/js/jquery.toast.js') !!}"></script>
    <script src="{!! asset('plugins/components/switchery/dist/switchery.min.js') !!}"></script>
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

            $('.google2fa_checkbox').each(function() {
                new Switchery($(this)[0], $(this).data());
            });

            //เปิด-ปิด ใช้งาน 2fa
            $('#google2fa_status').change(function(event) {
                if($(this).prop('checked')){//เปิด
                    $('#responsive-modal').modal('show');

                    $('.google2fa_checkbox').prop('checked', true).trigger('click');
                }
            });

        });

    </script>

@endpush
