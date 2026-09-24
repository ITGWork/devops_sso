@extends('layouts.master')

@section('title', 'ระบบยื่นคำขอ (LAB)')

@push('css')
    <link href="{{asset('plugins/components/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{asset('plugins/components/toast-master/css/jquery.toast.css')}}">
    <style>
        .btn_delete{
            margin-left: 7px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- .row -->
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">ระบบยื่นคำขอขึ้นทะเบียนผู้ตรวจ และผู้ประเมินของผู้ตรวจสอบการทำผลิตภัณฑ์อุตสาหกรรม</h3>

                    <div class="pull-right">
                        <a class="btn btn-pink" href="{{ url('/dashboard/request-section-5') }}">ระบบคำขอเป็น IB/CB</a>
                        <a class="btn btn-success" href="{{ url('/request_section5/application_inspectors/create') }}"><i class="icon-plus"></i> ยื่นคำขอ</a>
                    </div>

                    <div class="clearfix"></div>
                    <hr>

                    <div class="row box_filter">

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('filter_search', 'คำค้นหา :', ['class' => 'col-md-12 control-label label-filter']) !!}
                                <div class="col-md-9">
                                    {!! Form::text('filter_search', null, ['class' => 'form-control', 'placeholder'=>'ค้นหาจาก เลขที่คำขอ/ผู้ยื่นคำขอ/เลขผู้เสียภาษี']); !!}
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group  pull-left">
                                        <button type="button" class="btn btn-info waves-effect waves-light" style="margin-bottom: -1px;" id="btn_filter_search">ค้นหา</button>
                                    </div>
                                    <div class="form-group  pull-left m-l-15">
                                        <button type="button" class="btn btn-warning waves-effect waves-light" id="btn_filter_clear"> ล้าง </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_branch', 'สาขา :', ['class' => 'col-md-12 control-label label-filter']) !!}
                                <div class="col-md-12">
                                    {!! Form::select('filter_branch',
                                    App\Models\Basic\BranchGroup::pluck('title','id'),
                                    null, ['class' => 'form-control', 'placeholder'=>'เลือกสาขา']); !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('filter_state', 'สถานะ :', ['class' => 'col-md-12 control-label label-filter']) !!}
                                <div class="col-md-12">
                                    {!! Form::select('filter_state',
                                    App\Models\Section5\ApplicationInspectorStatus::pluck('title','id'),
                                    null, ['class' => 'form-control', 'placeholder'=>'เลือกสถานะ']); !!}
                                </div>
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
                                    {{-- <th width="11%" class="text-center">เลขผู้เสียภาษี</th> --}}
                                    <th width="41%" class="text-center">สาขา/รายสาขา</th>
                                    <th width="9%" class="text-center">วันที่ยื่นคำขอ</th>
                                    <th width="12%" class="text-center">สถานะ</th>
                                    <th width="10%" class="text-center">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <div class="pagination-wrapper">

                        </div>
                    </div>

                    @include ('section5.application_inspectors.modals')
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
                ajax: {
                    "url": '{!! url('/request_section5/application_inspectors/data_list') !!}',
                    "dataType": "json",
                    "data": function (d) {
                        d.filter_search = $('#filter_search').val();
                        d.filter_branch = $('#filter_branch').val();
                        d.filter_state = $('#filter_state').val();
                    }
                },
                columns: [
                    { data: 'DT_Row_Index', searchable: false, orderable: false},
                    { data: 'application_no', name: 'application_no' },
                    { data: 'applicant_full_name', name: 'applicant_full_name' },
                 //   { data: 'applicant_taxid', name: 'applicant_taxid' },
                    { data: 'branch_id', name: 'branch_id' },
                    { data: 'application_date', name: 'application_date' },
                    { data: 'application_status', name: 'application_status' },
                    { data: 'action', name: 'action' },
                ],
                columnDefs: [
                     { className: "text-center", targets: [0,-1,-2,-3] },
                ],
                fnDrawCallback: function() {

                },
                stateSaveParams: function (settings, data) {
                    data.search.filter_search = $('#filter_search').val();
                    data.search.filter_branch = $('#filter_branch').val();
                    data.search.filter_state = $('#filter_state').val();
                },
                stateLoadParams: function (settings, data) {
                    $('#filter_search').val(data.search.filter_search);
                    $('#filter_branch').val(data.search.filter_branch).trigger('change.select2');
                    $('#filter_state').val(data.search.filter_state).trigger('change.select2');
                }
            });


            $('#btn_filter_search').click(function (e) {
                table.draw();
            });

            $('#btn_filter_clear').click(function (e) {
                $('.box_filter').find('input').val('');
                $('.box_filter').find('select').val('').trigger('change.select2');
                table.draw();
            });

            $('body').on('click', '.btn_delete', function(){

                var id = $(this).data('id');
                var application_no = $(this).data('application_no');
                var applicant_full_name = $(this).data('applicant_full_name');
                var applicant_taxid = $(this).data('applicant_taxid');
                var created_at = $(this).data('created_at');

                $('.input_show').val('');

                $('#myModalLabel').text('ยกเลิกยื่นคำขอขึ้นทะเบียนผู้ตรวจฯ #');
                $('.application_no_text').text('');
                $('.applicant_full_name_text').text('');
                $('.applicant_taxid_text').text('');
                $('.created_at_text').text('');
                $('#modal_delete_id').val(id);

                if( id != ''){

                    $('#myModalLabel').text('ยกเลิกยื่นคำขอขึ้นทะเบียนผู้ตรวจฯ #'+id);
                    $('.application_no_text').text(application_no);
                    $('.applicant_full_name_text').text(applicant_full_name);
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
                            url: "{{ url('/request_section5/application_inspectors/delete_update') }}",
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
