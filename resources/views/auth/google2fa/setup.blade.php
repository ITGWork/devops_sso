@push('css')
    <link href="{{ asset('plugins/components/owl.carousel/owl.carousel.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('plugins/components/owl.carousel/owl.theme.default.css') }}" rel="stylesheet" />
    <style>
        @media only screen and (min-width:1281px) {
            .modal {
                text-align: center;
                padding: 0!important;
            }

            .modal:before {
                content: '';
                display: inline-block;
                height: 100%;
                vertical-align: middle;
                margin-right: -4px;
            }

            .modal-dialog {
                display: inline-block;
                text-align: left;
                vertical-align: middle;
            }

            .owl-carousel .owl-nav.disabled{
                display: block;
            }
            .owl-dots{
                display: none;
            }
        }
    </style>
@endpush

@php

    //get user
    //$user = App\User::find(1);

    if(empty($user->google2fa_secret)){//ถ้ายังไม่มีรหัสให้ gen ใหม่
        $randomBytes = random_bytes(10);

        $secret = \ParagonIE\ConstantTime\Base32::encodeUpper($randomBytes);

        // //encrypt and then save secret
        $user->google2fa_secret = Crypt::encrypt($secret);
        $user->google2fa_status = 0;
        $user->save();
    }else{
        $secret = Crypt::decrypt($user->google2fa_secret);
    }

    $image = Google2FA::getQRCodeInline(
        request()->getHost(),
        $user->email,
        $secret,
        200
    );

@endphp

<div id="responsive-modal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><img src="{{ url('plugins/images/google-authen/google-authen.png') }}" width="5%" class="m-b-10"/> ตั้งค่าการล็อคอิน 2 ขั้นตอนกับ Google Authenticator</h4>
            </div>
            <div class="modal-body p-l-30 p-r-30">
                <div class="row">
                    <div class="panel-wrapper p-b-10 collapse in">
                        <div id="owl-demo" class="owl-carousel owl-theme">

                            {{-- Step 1 --}}
                            <div class="item">

                                <h5 class="m-t-5 m-b-40">ขั้นตอนที่ 1 ดาวน์โหลดแอปพลิเคชันตามระบบปฏิบัติการที่ท่านใช้งาน</h5>

                                <div class="col-md-6 col-xs-6">
                                    <img src="{{ url('plugins/images/google-authen/play-store-qr.png') }}" width="100%" />
                                    <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">
                                        <img src="{{ url('plugins/images/google-authen/play-store.png') }}" width="100%" />
                                    </a>
                                </div>

                                <div class="col-md-6 col-xs-6">
                                    <img src="{{ url('plugins/images/google-authen/app-store-qr.png') }}" width="100%" />
                                    <a href="https://apps.apple.com/th/app/google-authenticator/id388497605" target="_blank">
                                        <img src="{{ url('plugins/images/google-authen/app-store.png') }}" width="100%" />
                                    </a>
                                </div>

                            </div>

                            {{-- Step 2 --}}
                            <div class="item hide show-after">
                                <h5 class="m-t-5 m-b-0">ขั้นตอนที่ 2 เปิดแอป Google Authenticator และสแกน QR Code หรือกรอก Setup key</h5>

                                <div class="col-md-12 text-center">
                                    {!! $image !!}

                                    <p class="m-t-0 m-b-20">กรอก Setup Key <code>{!! $secret !!}</code></p>
                                </div>

                                <h5 class="m-t-10">ขั้นตอนที่ 3 กรอกรหัสจากแอป Google Authenticator</h5>

                                <div class="m-t-0 p-r-30 p-l-30">
                                    <form class="form-horizontal form-material" role="form" method="POST" action="{{ url($action_url) }}" id="google2fa-form">
                                        {!! csrf_field() !!}
                                        <div class="form-group">
                                            <label for="one_time_password" class="control-label">กรอกรหัส 6 ตัว จากแอพ Google Authenticator</label>

                                            <div class="row">
                                                <div class="col-md-2 col-xs-2">
                                                    <input type="text" class="form-control input-login text-center font-22 one_time_password" maxlength="1" name="one_time_password[]" autofocus>
                                                </div>
                                                <div class="col-md-2 col-xs-2">
                                                    <input type="text" class="form-control input-login text-center font-22 one_time_password" maxlength="1" name="one_time_password[]">
                                                </div>
                                                <div class="col-md-2 col-xs-2">
                                                    <input type="text" class="form-control input-login text-center font-22 one_time_password" maxlength="1" name="one_time_password[]">
                                                </div>
                                                <div class="col-md-2 col-xs-2">
                                                    <input type="text" class="form-control input-login text-center font-22 one_time_password" maxlength="1" name="one_time_password[]">
                                                </div>
                                                <div class="col-md-2 col-xs-2">
                                                    <input type="text" class="form-control input-login text-center font-22 one_time_password" maxlength="1" name="one_time_password[]">
                                                </div>
                                                <div class="col-md-2 col-xs-2">
                                                    <input type="text" class="form-control input-login text-center font-22 one_time_password" maxlength="1" name="one_time_password[]">
                                                </div>
                                            </div>

                                            <div id="one_time_password_required" class="text-center m-t-10 text-danger hide">
                                                กรุณากรอกรหัสให้ครบถ้วน
                                            </div>

                                            @if(Session::has('one_time_password_error'))
                                                <div class="text-center m-t-10 text-danger">
                                                    {{ Session::get('one_time_password_error') }}
                                                </div>
                                            @endif

                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success waves-effect waves-light" id="btn-2fa">ตกลง</button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script src="{{ asset('plugins/components/owl.carousel/owl.carousel.min.js') }}"></script>
    <script>

        $(document).ready(function() {

            //Owl Carousel
            $("#owl-demo").owlCarousel({
                navigation : true, // Show next and prev buttons
                slideSpeed : 300,
                paginationSpeed : 400,
                items : 1,
                navText : ["ขั้นตอนที่ 1", "ขั้นตอนที่ 2"]
                @if(Session::has('one_time_password_error'))
                    ,startPosition: 1
                @endif
            });

            //เมื่อคลิกขั้นตอนที่ 1
            $('.owl-prev').click(function(){
                $(this).css('background', '#869791');//active
                $('.owl-next').css('background', '#D6D6D6');
            });

            //เมื่อคลิกขั้นตอนที่ 2
            $('.owl-next').click(function(){
                $(this).css('background', '#869791');//active
                $('.owl-prev').css('background', '#D6D6D6');
            });

            setTimeout(function (){//เซต active slide ตอนเปิดมา
                if($('.owl-item.active').next().length == 1 || $('.owl-item.active').length==0){//อยู่ที่ slide 1
                    $('.owl-prev').css('background', '#869791');
                }else{
                    $('.owl-next').css('background', '#869791');
                }
            }, 500);

            //เปิด Modal
            $('#username').blur();
            @if(Session::has('one_time_password_error'))//เปิด modal และ focus ไปที่ input
                $('#responsive-modal').on('show.bs.modal', function (event) {
                    setTimeout(function (){
                        $('.one_time_password:first').select();
                    }, 1000);
                });
            @else
                $('#responsive-modal').on('show.bs.modal', function (event) {
                    $('.show-after').addClass('hide');
                    //โชว์ slide ที่ 2 ที่ซ่อนไว้ตอนโหลด page
                    setTimeout(function (){
                        $('.show-after').removeClass('hide');
                    }, 700);
                });
            @endif

            @if($auto_open)

                $('#responsive-modal').modal('show');

                //โชว์ slide ที่ 2 ที่ซ่อนไว้ตอนโหลด page
                setTimeout(function (){
                    $('.show-after').removeClass('hide');
                }, 500);

            @endif
            
            //เมื่อคีย์รหัส
            $('.one_time_password').keypress(function(event) {
                var eKey = event.which || event.keyCode;
                if(eKey<48 || eKey>57){//เฉพาะตัวเลข
                    return false;
                }
            });

            //เมื่อคีย์รหัส
            $('.one_time_password').keydown(function(event) {
                if(event.keyCode!=8){//เมื่อไม่ใช่ Backspace
                    $(this).val('');
                }else{
                    if($(this).val()==''){
                        var prev_input = $(this).parent().prev().find('.one_time_password');
                        if($(prev_input).length!=0){
                            $(prev_input).val('');
                            $(prev_input).focus();
                        }
                    }
                }
            });

            //เมื่อคีย์รหัส
            $('.one_time_password').keyup(function(event) {
                if($(this).val().length == $(this).prop('maxlength')){
                    var next_input = $(this).parent().next().find('.one_time_password');
                    if($(next_input).length!=0){
                        $(next_input).focus();
                    }else{
                        check_and_submit();
                    }
                }
            });

            //เมื่อวางข้อมูล
            $(".one_time_password:first").bind("paste", function(e){

                $('.one_time_password:first').prop('maxlength', 6);
                setTimeout(function (){
                    var paste = $('.one_time_password:first').val();
                    $.each(paste.split(""), function(index, item) {
                        $('.one_time_password:eq('+index+')').val(item);
                    });
                    $('.one_time_password:first').prop('maxlength', 1);
                    check_and_submit();
                });
            });

            //เมื่อคลิกตกลง
            $('#btn-2fa').click(function(event) {

                check_and_submit();

                var one_time_length = one_time_count();

                if(one_time_length!=6){
                    if($('.owl-item.active').next().length == 1){//อยู่ที่ slide 1
                        $('.owl-next').click();//ไป slide 2
                    }
                    $('#one_time_password_required').removeClass('hide');
                }
            });

            //ปิด Modal
            $('#responsive-modal').on('hidden.bs.modal', function (event) {
                $.ajax({
                    type: "POST",
                    data: {_token: '{!! csrf_token() !!}'},
                    url: "{!! url('2fa/clear_session') !!}"
                });
            });

        });

        function check_and_submit(){

            var one_time_length = one_time_count();

            if(one_time_length==6){//คีย์ครบ 6 ตัวให้ Submit
                $('#google2fa-form').submit();
            }

        }

        function one_time_count(){
            return $('.one_time_password').filter(function () {
                                    return !!this.value;
                                  }).length;
        }

    </script>
@endpush
