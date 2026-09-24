@extends('layouts.master')

@push('css')
 <link href="{{asset('plugins/components/bootstrap-treeview/css/bootstrap-treeview.min.css')}}" rel="stylesheet" />
     <style>
        .list-group-item {
             padding: 2px 15px;
        }
    </style>
@endpush


@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <h3 class="box-title">หน้าหลัก
                
                    @if ($almost_expire->count() > 0)
                        <button class="fcbtn btn btn-primary btn-outline btn-1e pull-right m-l-5" data-toggle="modal" data-target="#Almost_expire-Modal">
                            Lab ขอบข่ายใกล้หมดอายุ 
                        </button>
                    @endif

                    @if ($labs->count() > 0)
                        <button class="fcbtn btn btn-info btn-outline btn-1e pull-right" data-toggle="modal" data-target="#Lab-Modal">
                            Lab มอก.ยกเลิก 
                        </button>
                    @endif

                </h3>
                <div class="clearfix"></div>
                <hr>
                <div class="row colorbox-group-widget">

                    @can('view-'.str_slug('setion5-application-lab'))
                        <div class="col-md-4 col-sm-6 info-color-box waves-effect waves-light">
                            <a href="{{ url('request-section-5/application-lab') }}">
                            <div class="white-box">
                                <div class="media bg-dashboard5">
                                    <div class="media-body">
                                        <h3 class="info-count">ยื่นคำขอเป็นหน่วยตรวจสอบ (LAB)<br/>
                                            <span class="pull-right" style="font-size:45px;"><i class="mdi mdi-human-handsup"></i></span>
                                        </h3>
                                        <p class="info-text font-12"> ระบบยื่นคำขอรับการแต่งตั้งเป็นหน่วยตรวจสอบผลิตภัณฑ์อุตสาหกรรม (LAB) </p>
                                    </div>
                                </div>
                            </div>
                            </a>
                        </div>
                    @endcan

                    @can('view-'.str_slug('setion5-application-inspector'))
                        <div class="col-md-4 col-sm-6 info-color-box waves-effect waves-light">
                            <a href="{{ url('request_section5/application_inspectors') }}">
                                <div class="white-box">
                                    <div class="media bg-dashboard5">
                                        <div class="media-body">
                                            <h3 class="info-count">ยื่นคำขอเป็นผู้ตรวจ/ผู้ประเมิน<br />
                                                <span class="pull-right" style="font-size:45px;"><i class="mdi mdi-human"></i></span>
                                            </h3>
                                            <p class="info-text font-12"> ระบบยื่นคำขอขึ้นทะเบียนผู้ตรวจ และผู้ประเมิน (IB/CB)</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endcan

                    @can('view-'.str_slug('setion5-application-ibcb'))
                        <div class="col-md-4 col-sm-6 info-color-box waves-effect waves-light">
                            <a href="{{ url('request-section-5/application-ibcb') }}">
                                <div class="white-box">
                                    <div class="media bg-dashboard5">
                                        <div class="media-body">
                                            <h3 class="info-count">ยื่นคำขอเป็นผู้ตรวจสอบ (IB)<br/>
                                                <span class="pull-right" style="font-size:45px;"><i class="mdi mdi-human-handsup"></i></span>
                                            </h3>
                                            <p class="info-text font-12"> ระบบยื่นคำขอรับการแต่งตั้งเป็นผู้ตรวจสอบการทำผลิตภัณฑ์อุตสาหกรรม (IB) </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endcan

                    <div class="col-md-4 col-sm-6 info-color-box waves-effect waves-light">
                        <a href="{{ url('section5/factory-inspection') }}">
                            <div class="white-box">
                                <div class="media bg-dashboard5">
                                    <div class="media-body">
                                        <h3 class="info-count">ระบบรับคำขอตรวจโรงงาน<br/>
                                            <span class="pull-right" style="font-size:45px;"><i class="mdi mdi-factory"></i></span>
                                        </h3>
                                        <p class="info-text font-12"> ระบบรับคำขอตรวจโรงงาน IB/CB </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4 col-sm-6 info-color-box waves-effect waves-light">
                        <a href="{{ url('section5/product-testing') }}">
                            <div class="white-box">
                                <div class="media bg-dashboard5">
                                    <div class="media-body">
                                        <h3 class="info-count">ระบบรับคำขอทดสอบผลิตภัณฑ์<br/>
                                            <span class="pull-right" style="font-size:45px;"><i class="mdi mdi-test-tube"></i></span>
                                        </h3>
                                        <p class="info-text font-12"> ระบบติดตามคำขอทดสอบผลิตภัณฑ์สำหรับหน่วยตรวจสอบ </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4 col-sm-6 info-color-box waves-effect waves-light">
                        <a href="https://esrv2.tisi.go.th/e-lab/login">
                            <div class="white-box">
                                <div class="media bg-dashboard1">
                                    <div class="media-body">
                                        <h3 class="info-count text-white">จัดประชุมอนุกรรมการ<br/>
                                            <span class="pull-right" style="font-size:45px;"><i class="mdi mdi-account-multiple"></i></span>
                                        </h3>
                                        <p class="info-text font-12 text-white"> ระบบจัดการการประชุม ผู้เชี่ยวชาญ และลงคะแนนของหน่วยตรวจสอบ </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@if ($labs->count() > 0)
{{-- Modal Lab --}}
<div class="modal fade" id="Lab-Modal" tabindex="-1" role="dialog" aria-labelledby="Lab-ModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="Lab-ModalLabel">แจ้งเตือน มอก.ที่ถูกยกเลิก</h4> 
            </div>
            <div class="modal-body" style="max-height: calc(100vh - 225px); overflow-y: auto;">
                <h4>มีขอบข่ายของหน่วยตรวจสอบ Labs ที่มี มอก. ยกเลิก จำนวน {{ $labs->count() }} ราย</h4>

                <table class="font-14" width="100%">
                    @foreach ($labs as $lab)
                        <tr class="header">
                            <td width="10%">{{ $lab->lab_code }}</td>
                            <td width="70%">{{ $lab->lab_name }}</td>
                            <td width="15%">{{ $lab->tis_amount }} มอก. ยกเลิก</td>
                            <td width="5%"><a href="javascript:void(0)" class="fcbtn btn btn-link">ดู</a></td>
                        </tr>
                        <tr>
                            <td colspan="4"><p style="text-indent:20px">{{ $lab->tis_name }} </p></td>
                        </tr>
                    @endforeach
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
@endif

@if ($almost_expire->count() > 0)
{{-- Modal Lab --}}
<div class="modal fade" id="Almost_expire-Modal" tabindex="-1" role="dialog" aria-labelledby="Almost_expire-ModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="Almost_expire-ModalLabel">แจ้งเตือน ขอบข่ายใกล้หมดอายุ</h4> 
            </div>
            <div class="modal-body" style="max-height: calc(100vh - 225px); overflow-y: auto;">
                <h4>มีขอบข่ายของหน่วยตรวจสอบ Labs ใกล้หมดอายุ จำนวน {{ $almost_expire->count() }} ราย</h4>

                <table class="font-14" width="100%">
                    @foreach ($almost_expire as $almost_ex)
                        <tr class="header">
                            <td width="10%">{{ $almost_ex->lab_code }}</td>
                            <td width="70%">{{ $almost_ex->lab_name }}</td>
                            <td width="15%">{{ $almost_ex->tis_amount }} มอก.</td>
                            <td width="5%"><a href="javascript:void(0)" class="fcbtn btn btn-link">ดู</a></td>
                        </tr>
                        <tr>
                         <td colspan="4">
                            <p style="text-indent:20px">
                           
                                
                                    @isset($almost_ex)
                                    @foreach ( $almost_ex->list_scope as $scopeSTD )
                                    @php
                                
                                    $tis_standards = $scopeSTD->tis_standards;
                                    $tis_standards_id = !is_null($tis_standards) ? $tis_standards->getKey() : null ;
                                    @endphp

                                    @if (!is_null($tis_standards_id))

                                        {!! (!empty($tis_standards->tb3_Tisno)?$tis_standards->tb3_Tisno.' : ':null).(!empty($tis_standards->tb3_TisThainame)?$tis_standards->tb3_TisThainame:null) !!}
                                        {!! $tis_standards->status=="5" ? '<span class="label label-rounded label-danger font-15" style="margin-bottom:-20px;">มอก. ยกเลิก</span>' : '' !!}
                                      
                                            <input type="hidden" class="scope_input_std" value="{!! $tis_standards_id !!}" data-lab_id="{!! $almost_ex->id !!}" >
                                            <div class="scope_show_std_{!! $tis_standards_id !!}"></div>
                                          
                                    @endif

                                    @endforeach

                                    @endisset
                              
                             </p>
                             </td>

                        </tr>
                    @endforeach
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('js')
    <script src="{{asset('plugins/components/bootstrap-treeview/js/bootstrap-treeview.min.js')}}"></script>
    <script>
        $(document).ready(function () {

            $('tr:not(.header)').hide();

                $('tr.header').click(function() {
                   /* $(this).find('span').text(function(_, value) {
                    return value == '-' ? '+' : '-'
                    }); */
                    
                    $(this).nextUntil('tr.header').slideToggle(100, function() {});
                   // LoadScopeShow();
                });

            @if($labs->count() > 0)
                $('#Lab-Modal').modal('show');
            @elseif($almost_expire->count() > 0)
                $('#Almost_expire-Modal').modal('show');
                 LoadScopeShow();
            @endif

            var open_modal_counter = 0;
            $('#Lab-Modal').on('hidden.bs.modal', function (e) {
                if($('#Almost_expire-Modal').length==1 && open_modal_counter==0){ //มีแจ้งของ Lab ใกล้หมดอายุด้วย ด้วย
                    $('#Almost_expire-Modal').modal('show');
                     LoadScopeShow();
                    open_modal_counter++;
                }
            })

        });

        function LoadScopeShow() {

            $('.scope_input_std').each(function(index, element){

                var tis_id = $(element).val();
                var lab_id = $(element).data('lab_id');

                if( tis_id != '' ){

                    $.ajax({
                        url: "{!! url('/funtions/treeview_scope') !!}" + "?lab_id=" + lab_id + "&tis_id=" + tis_id
                    }).done(function( object ) {
                        $('.scope_show_std_'+tis_id).treeview({
                            data: object,
                            collapseIcon:'fa fa-minus',
                            expandIcon:'fa fa-plus',
                            showBorder: false,
                            showTags: false,
                            highlightSelected: false,

                        });
                        $('.scope_show_std_'+tis_id).treeview('expandAll', { levels: 3, silent: true });
                    });
                }

            });

        }

    </script>
@endpush