@push('css')
    <style type="text/css">

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
        }
    </style>

@endpush

<div id="responsive-modal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><img src="{{ url('plugins/images/google-authen/google-authen.png') }}" width="5%" class="m-b-10"/> กรอกรหัสจาก Google Authenticator</h4>
            </div>
            <div class="modal-body p-l-30 p-r-30">
                <form class="form-horizontal form-material" role="form" method="POST" action="{{ url('2fa/validate') }}" id="google2fa-form">
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
            <div class="modal-footer">
                <a href="{{ url('contact') }}" class="pull-left m-t-10" target="_blank">โทรศัพท์ใช้งานไม่ได้/สูญหาย? ติดต่อเจ้าหน้าที่</a>
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success waves-effect waves-light" id="btn-2fa">ตกลง</button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>

        $(document).ready(function() {

            //เปิด Modal
            $('#responsive-modal').on('show.bs.modal', function (event) {
                setTimeout(function (){
                    $('.one_time_password:first').select();
                }, 500);
            });
            $('#responsive-modal').modal('show');

            //เมื่อคีย์รหัส
            $('.one_time_password').keypress(function(event) {
                var eKey = event.which || event.keyCode;
                 if(eKey<48 || eKey>57){
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
                var one_time_length = $('.one_time_password').filter(function () {
                                          return !!this.value;
                                      }).length;
                if(one_time_length==6){
                    $('#google2fa-form').submit();
                }else{
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

            var one_time_length = $('.one_time_password').filter(function () {
                                    return !!this.value;
                                  }).length;
            if(one_time_length==6){//คีย์ครบ 6 ตัวให้ Submit
                $('#google2fa-form').submit();
            }

        }

    </script>
@endpush
