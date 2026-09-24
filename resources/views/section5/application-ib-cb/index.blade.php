@extends('layouts.master')

@section('title', 'ระบบยื่นคำขอ (IBCB)')

@push('css')
    <link href="{{asset('plugins/components/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{asset('plugins/components/toast-master/css/jquery.toast.css')}}">
@endpush

@section('content')
    <div class="container-fluid">
        <!-- .row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">ระบบยื่นคำขอรับการแต่งตั้งเป็นผู้ตรวจสอบการทำผลิตภัณฑ์อุตสาหกรรม (IB/CB)</h3>

                    <div class="pull-right">
                        <a class="btn btn-pink" href="{{ url('/dashboard/request-section-5') }}">หน้าหลักคำขอมาตรา 5</a>
                        <a class="btn btn-warning" href="{{ url('/request-section-5/application-ibcb/change-info') }}">เปลี่ยนแปลงข้อมูล</a>
                        <a class="btn btn-danger" href="{{ url('/request-section-5/application-ibcb/cancellation') }}">ยกเลิกการแต่งตั้ง</a>
                        <a class="btn btn-success" href="{{ url('/request-section-5/application-ibcb/create') }}"><i class="icon-plus"></i> ยื่นคำขอ</a>
                    </div>

                    <div class="clearfix"></div>
                    <hr>

                    <div class="row box_filter">

                        <div class="col-md-6">
                            {!! Form::label('filter_search', 'คำค้นหา:', ['class' => 'col-md-2 control-label label-filter']) !!}
                            <div class="form-group col-md-10">
                                {!! Form::text('filter_search', null, ['class' => 'form-control', 'placeholder'=>'ค้นหาจาก เลขที่คำขอ/ผู้ยื่นคำขอ/เลขผู้เสียภาษี']); !!}
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group  pull-left">
                                <button type="button" class="btn btn-info waves-effect waves-light" style="margin-bottom: -1px;" id="btn_filter_search">ค้นหา</button>
                            </div>

                            <div class="form-group  pull-left m-l-15">
                                <button type="button" class="btn btn-warning waves-effect waves-light" id="btn_filter_clear"> ล้าง </button>
                            </div>
                        </div><!-- /.col-lg-1 -->

                        <div class="col-md-3">
                            {!! Form::label('filter_state', 'สถานะ:', ['class' => 'col-md-2 control-label label-filter']) !!}
                            <div class="form-group col-md-10">
                                {!! Form::select('filter_state', App\Models\Section5\ApplicationIbcbStatus::pluck('title', 'id')->all(), null, ['class' => 'form-control', 'placeholder'=>'เลือกสถานะ']); !!}
                            </div>
                        </div>

                    </div>

                    <div class="clearfix"></div>
                    <hr>

                    <div class="table-responsive">
                        <table class="table table-borderless" id="myTable">
                            <thead>
                                <tr>
                                    <th width="2%" class="text-center">ลำดับ</th>
                                    <th width="10%" class="text-center">เลขที่คำขอ</th>
                                    <th width="18%" class="text-center">ผู้ยื่นคำขอ</th>
                                    <th width="20%" class="text-center">สาขา </th>
                                    <th width="6%" class="text-center">ประเภท</th>
                                    <th width="10%" class="text-center">ประเภทที่ยื่น</th>
                                    <th width="10%" class="text-center">ผู้ดำเนินการ</th>
                                    <th width="8%" class="text-center">วันที่ยื่นคำขอ</th>
                                    <th width="8%" class="text-center">สถานะ</th>
                                    <th width="8%" class="text-center">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>

                    @include ('section5.application-ib-cb.modals.modal-cancel')

                </div>
            </div>
        </div>
    </div>



@endsection

@push('js')
    <script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>

    <script src="{{asset('plugins/components/datatables/jquery.dataTables.min.js')}}"></script>
    {{-- <script src="{{asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')}}"></script> --}}
    <!-- start - This is for export functionality only -->
    <!-- end - This is for export functionality only -->
    <script>
        $(document).ready(function () {

            @if(\Session::has('message'))
            $.toast({
                heading: 'Success!',
                position: 'top-center',
                text: '{{session()->get('message')}}',
                loaderBg: '#ff6849',
                icon: 'success',
                hideAfter: 3000,
                stack: 6
            });
            @endif

            @if(\Session::has('flash_message'))
                Swal.fire({
                    type: 'success',
                    title: 'บันทึกเรียบร้อย',
                    // html: '<p class="h4"></p>',
                    width: 500
                });
            @endif


        })

        $(function () {

            //Create Data Table
            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                stateSave: true,
                stateDuration: 60 * 60 * 24,
                order: [[7, 'desc']],
                ajax: {
                    "url": '{!! url('/request-section-5/application-ibcb/data_list') !!}',
                    "dataType": "json",
                    "data": function (d) {
                        d.filter_search = $('#filter_search').val();
                        d.filter_state = $('#filter_state').val();
                    }
                },
                columns: [
                    { data: 'DT_Row_Index', searchable: false, orderable: false},
                    { data: 'application_no', name: 'application_no', orderable: true },
                    { data: 'applicant_name', name: 'applicant_name', orderable: true },
                    { data: 'scope', name: 'scope', orderable: true },
                    { data: 'application_type', name: 'application_type', orderable: true },
                    { data: 'applicant_request_type', name: 'applicant_request_type', orderable: true },
                    { data: 'creater', name: 'creater', orderable: true },
                    { data: 'application_date', name: 'application_date', orderable: true },
                    { data: 'status_application', name: 'status_application', orderable: true },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                columnDefs: [
                     { className: "text-center", targets: [0,4,5,7] },
                    // { className: "text-left", targets: [1,2] }
                ],
                fnDrawCallback: function() {

                },
                stateSaveParams: function (settings, data) {
                    data.search.filter_search = $('#filter_search').val();
                    data.search.filter_state = $('#filter_state').val();
                },
                stateLoadParams: function (settings, data) {
                    $('#filter_search').val(data.search.filter_search);
                    $('#filter_state').val(data.search.filter_state).trigger('change.select2');
                }
            });


            $('#btn_filter_search').click(function (e) {
                table.draw();
            });

            $('#btn_filter_clear').click(function (e) {
                $('#filter_search').val('');
                $('#filter_state').val('').select2();
                table.draw();
            });

            $('body').on('click', '.btn_delete', function(){

                var id = $(this).data('id');
                var application_no = $(this).data('application_no');
                var applicant_name = $(this).data('applicant_name');
                var applicant_taxid = $(this).data('applicant_taxid');
                var created_at = $(this).data('created_at');

                $('.input_show').val('');

                $('#myModalLabel').text('ยกเลิกยื่นคำขอเป็น IB/CB #');
                $('.application_no_text').text('');
                $('.applicant_name_text').text('');
                $('.applicant_taxid_text').text('');
                $('.created_at_text').text('');
                $('#modal_delete_id').val(id);

                if( id != ''){

                    $('#myModalLabel').text('ยกเลิกยื่นคำขอเป็น IB/CB #'+id);
                    $('.application_no_text').text(application_no);
                    $('.applicant_name_text').text(applicant_name);
                    $('.applicant_taxid_text').text(applicant_taxid);
                    $('.created_at_text').text(created_at);
                    $('#modal_delete_id').val(id);
                    $('#myModalDelete').modal('show');

                }

            });

            $('body').on('click', '#btn_delete_save', function(){

                var id = $('#modal_delete_id').val();
                var remarks_delete = $('#remarks_delete').val();

                if( id != '' && remarks_delete != ''){

                    if( confirm("Confirm delete?") ){

                        $.ajax({
                            method: "POST",
                            url: "{{ url('/request-section-5/application-ibcb/delete_update') }}",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "id": id,
                                "remarks_delete": remarks_delete
                            },
                            success : function (msg){
                                if (msg == "success") {

                                    $.toast({
                                        heading: 'Compleate!',
                                        text: 'บันทึกสำเร็จ',
                                        position: 'top-right',
                                        loaderBg: '#ff6849',
                                        icon: 'success',
                                        hideAfter: 1000,
                                        stack: 6,
                                        afterShown: function () {
                                            $('#myModalDelete').modal('hide');
                                            table.draw();
                                        }
                                    });

                                }else{

                                    $.toast({
                                        heading: 'Compleate!',
                                        text: 'บันทึกไม่สำเร็จ',
                                        position: 'top-right',
                                        loaderBg: '#ff6849',
                                        icon: 'error',
                                        hideAfter: 1000,
                                        stack: 6,
                                        afterShown: function () {
                                            $('#myModalDelete').modal('hide');
                                            table.draw();
                                        }
                                    });

                                }
                            }
                        });
                    }

                }else{
                    alert('กรุณาระบุหมายเหตุ');
                }

            });

        });
    </script>
@endpush
