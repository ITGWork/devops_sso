

@php

    $theme_name = 'default';
    $fix_header = false;
    $fix_sidebar = false;
    $theme_layout = 'normal';

    if(auth()->user()){

        $params = (object)json_decode(auth()->user()->params);

        if(!empty($params->theme_name)){
            if(is_file('css/colors/'.$params->theme_name.'.css')){
                $theme_name = $params->theme_name;
            }
        }

        if(!empty($params->fix_header) && $params->fix_header=="true"){
            $fix_header = true;
        }

        if(!empty($params->fix_sidebar) && $params->fix_sidebar=="true"){
            $fix_sidebar = true;
        }

        if(!empty($params->theme_layout)){
            $theme_layout = $params->theme_layout;
        }

    }

@endphp
<style>
    label.label_act_instead_color {
        background-color: #ccffff;
        padding-bottom: 3px;
        padding-top: 3px;
        opacity: 0.5;
    }
</style>
<nav class="navbar navbar-default navbar-static-top m-b-0">
    <div class="navbar-header">
        <a class="navbar-toggle font-20 hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse"
           data-target=".navbar-collapse">
            <i class="fa fa-bars"></i>
        </a>
        <div class="top-left-part">
            <a class="logo" href="{{url('/dashboard')}}">
                <b>
                    <img src="{{asset('images/logo01.png')}}"  width="35px" alt="home"/>
                </b>
                <span>
                      บริการอิเล็กทรอนิกส์ สมอ.
                    {{-- <img src="{{asset('plugins/images/logo_text.png')}}" alt="homepage" class="dark-logo"/> --}}
                </span>
            </a>
        </div>
        <ul class="nav navbar-top-links navbar-left hidden-xs">
            @if($theme_layout != 'fix-header' && auth()->check())
                <li class="sidebar-toggle">
                    <a href="javascript:void(0)" class="sidebartoggler font-20 waves-effect waves-light"><i class="icon-arrow-left-circle"></i></a>
                </li>
            @endif
        </ul>
        <ul class="nav navbar-top-links navbar-right pull-right">

            @php
                $authoritys   = HP::getAuthoritys(auth()->user()->id);
                $act_insteads = !empty($authoritys) ? $authoritys : [];
            @endphp

            @if (count($act_insteads) > 0)
                <li class="right-side-toggle b-r-0 m-t-15">
                    <span class="text-white font-20">
                        @php
                            $check_act_instead = App\Sessions::where('id', session()->getId())
                                                             ->where('user_id',auth()->user()->id)
                                                             ->WhereNotNull('act_instead')
                                                             ->first();
                        @endphp

                        @if (!is_null($check_act_instead))
                            {{ !empty($check_act_instead->get_act_instead) ? 'ทำธุรกรรมในนาม : '.$check_act_instead->get_act_instead->name : '' }}
                        @else
                            {{ !empty(auth()->user()->name) ? 'ทำธุรกรรมในนาม : '.auth()->user()->name: '' }}
                        @endif

                    </span>
                </li>
                <li class="right-side-toggle">
                    <a class="modal_act_instead waves-effect waves-light b-r-0 font-20 " id="modal_act_instead" href="javascript:void(0)"  >
                        <i class="fa fa-male"></i> เปลี่ยนผู้ทำธุรกรรม
                    </a>
                </li>
            @else
                <li class="right-side-toggle m-t-15">
                    <span class="text-white font-20">
                        {{ !empty(auth()->user()->name) ? 'ทำธุรกรรมในนาม : '.auth()->user()->name: '' }}
                    </span>
                </li>
            @endif

            <li class="right-side-toggle">
                <a class="right-side-toggler waves-effect waves-light b-r-0 font-20" href="javascript:void(0)">
                    <i class="icon-settings"></i>
                </a>
            </li>
        </ul>
    </div>
</nav>

@if (count($act_insteads) > 0)
    <!-- Modal -->
    <div class="modal fade" id="ModalActInstead" aria-labelledby="ModalActInsteadLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalActInsteadLabel">เลือกทำธุรกรรมในนามแทน</h5>
                </div>
                <div class="modal-body">
                    <div class="row">

                        {{-- @if (!is_null($check_act_instead)) --}}
                            <div class="col-md-12">
                                <div class="{{ $errors->has('act_instead') ? 'has-error' : ''}}">
                                    {!! HTML::decode(Form::label(' ', ' ', ['class' => 'col-md-1 control-label text-right ' ])) !!}
                                    <div class="form-group col-md-11">
                                        <div class=" col-md-12">
                                            {!! Form::radio('act_instead', '0', ((isset($check_act_instead) && !is_null($check_act_instead)) ? false : true), ['class'=>'check act_instead', 'data-radio'=>'iradio_square-green','id'=>"act_instead0"]) !!}
                                            <label for="act_instead0" class="label_act_instead ">&nbsp;{{ auth()->user()->name }}&nbsp;&nbsp;</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {{-- @endif --}}

                        <div class="col-md-12">
                            <div class="{{ $errors->has('act_instead') ? 'has-error' : ''}}">
                                {!! HTML::decode(Form::label('act_instead', 'ผู้มอบสิทธิ์', ['class' => 'col-md-3 control-label text-right'])) !!}
                                <div class="col-md-9">
                                    @php
                                        $authority_waits = HP::getAuthoritys(auth()->user()->id, 1);
                                    @endphp
                                    @if(count($authority_waits) > 0)
                                        <a class="label label-rounded label-info" href="{{ url('confirm-agents?filter_state=1') }}">
                                            มีรายการรอยืนยัน {{ count($authority_waits) }} รายการ <i class="fa fa-external-link"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="{{ $errors->has('act_instead') ? 'has-error' : ''}}">
                                {!! HTML::decode(Form::label('', ' ', ['class' => 'col-md-1 control-label text-right'])) !!}
                                <div class="form-group col-md-11">
                                    @if (count($act_insteads) > 0)
                                        @foreach ($act_insteads as $key => $act_instead)
                                            @php
                                                $act_checked = (!is_null($check_act_instead) && ($check_act_instead->act_instead == $key) ) ? true : false;
                                            @endphp
                                            <div class=" col-md-12">
                                                {!! Form::radio('act_instead', $key, $act_checked, ['class'=>'check act_instead', 'data-radio'=>'iradio_square-green','id'=>"act_instead$key"]) !!}
                                                <label for="act_instead{{$key}}" class="label_act_instead ">
                                                    &nbsp;&nbsp;
                                                    {{ $act_instead->name }}
                                                    @if($act_instead->applicanttype_id!=2) {{-- ไม่ใช่บุคคลธรรมดา --}}
                                                        @if($act_instead->branch_type==1)
                                                            (<span class="text-primary">สำนักงานใหญ่</span>)
                                                        @elseif($act_instead->branch_type==2)
                                                            (<span class="text-info">รหัสสาขา {{ $act_instead->branch_code }}</span>)
                                                        @endif
                                                    @endif
                                                    &nbsp;&nbsp;
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="save_modal_act_instead">ยืนยัน</button>
                </div>
            </div>
        </div>
    </div>

@endif

@push('js')

<script type="text/javascript">

    @if (count($act_insteads) > 0)

        $(document).ready(function () {

            //update_instead_api(1, '');

            $("#modal_act_instead").click(function() {
                $('#ModalActInstead').modal('show');
                $('#act_instead').val('');
                $('#act_instead').change();
            });

            $("#save_modal_act_instead").click(function() {

                $(this).prop('disabled', true);
                $(this).html('<i class="fa fa-spin fa-circle-o-notch"></i> กำลังบันทึก...');

                // var act_instead = $('#act_instead').val();
                var act_instead = $('input[name="act_instead"]:checked').val();
                if(act_instead != "" && act_instead !== undefined){
                    $.ajax({
                        type: "POST",
                        url: "{!! url('agents/up-act_instead') !!}",
                        data: {
                                _token: '{!! csrf_token() !!}',
                                act_instead_id: act_instead,
                                urls: "{!! url()->full() !!}"

                              }
                    }).done(function( object ) {
                        if(object.status == true){

                            if(object.message!=''){//มีข้อมูลไม่ตรงกับ API แสดง popup ให้เลือกว่าจะอัพเดทข้อมูลหรือไม่

                                Swal.fire({
                                        position: 'center',
                                        title: object.message,
                                        showCancelButton: false,
                                        focusConfirm: false,
                                        confirmButtonText: '<i class="mdi mdi-account-check"></i> รับทราบ',
                                        width: 800
                                }).then((result) => {
                                    // if (result != undefined && result.value===true) {//เลือกให้อัพเดทข้อมูล
                                    //     update_instead_api(act_instead, object.urls, object._token);
                                    // }else{
                                    //     window.location.assign(object.urls);
                                    // }
                                    window.location.assign(object.urls);
                                });

                            }else{
                                window.location.assign(object.urls);
                            }
                        }else{//ไม่สามารถเปลี่ยนผู้มอบอำนาจได้
                            $('#ModalActInstead').modal('hide');

                            Swal.fire({
                                type: 'error',
                                position: 'center',
                                title: object.message,
                                width: 500
                            }).then((result) => {
                                window.location.assign(object.urls);
                            });
                        }
                    });
                }else{
                    alert('กรุณาเลือกดำเนินการแทน!');
                }
            });

            act_instead();
            $('.act_instead').on('ifChecked', function(event){
                act_instead();
            });

            function act_instead() {
                $('.act_instead').parent().parent().find('.label_act_instead').removeClass('label_act_instead_color');
                $('.act_instead:checked').parent().parent().find('.label_act_instead').addClass('label_act_instead_color');
            }

            /*function update_instead_api(act_instead, url, _token){
                $.ajax({
                    type: "POST",
                    url: "{!! url('agents/update_instead_api') !!}",
                    data: {
                            act_instead_id: act_instead,
                            _token : _token
                          }
                }).done(function(object) {
                    if(object.status=='success'){
                        Swal.fire({
                            type: 'success',
                            position: 'center',
                            title: 'บันทึกข้อมูลเรียบร้อย!'
                        }).then((result) => {
                            window.location.assign(url);
                        });
                    }else{
                        Swal.fire({
                            type: 'error',
                            position: 'center',
                            title: 'บันทึกข้อมูลไม่สำเร็จ!'
                        }).then((result) => {
                            window.location.assign(url);
                        });
                    }



                });
            }*/

        });

    @endif

</script>

@endpush
