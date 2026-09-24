@extends('layouts.master')

@section('title', 'เปลี่ยนแปลงข้อมูลหน่วยตรวจสอบ')

@push('css')
    <link href="{{asset('plugins/components/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{asset('plugins/components/toast-master/css/jquery.toast.css')}}">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">
                        เปลี่ยนแปลงข้อมูลหน่วยตรวจสอบการทำผลิตภัณฑ์อุตสาหกรรม (IB/CB)
                    </h3>
                    <a class="btn btn-default pull-right" href="{{ url('/request-section-5/application-ibcb') }}">
                        <i class="icon-arrow-left-circle"></i> กลับ
                    </a>

                    <div class="clearfix"></div>
                    <hr>

                    <div class="row box_filter">
                        <div class="col-md-6">
                            {!! Form::label('filter_search', 'คำค้นหา:', ['class' => 'col-md-2 control-label label-filter']) !!}
                            <div class="form-group col-md-10">
                                {!! Form::text('filter_search', null, ['class' => 'form-control', 'placeholder'=>'ค้นหาจาก รหัสหน่วยตรวจสอบ/ชื่อ/เลขผู้เสียภาษี']); !!}
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group pull-left">
                                <button type="button" class="btn btn-info waves-effect waves-light" style="margin-bottom: -1px;" id="btn_filter_search">ค้นหา</button>
                            </div>
                            <div class="form-group pull-left m-l-15">
                                <button type="button" class="btn btn-warning waves-effect waves-light" id="btn_filter_clear"> ล้าง </button>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <hr>

                    <div class="table-responsive">
                        <table class="table table-borderless" id="myTable">
                            <thead>
                                <tr>
                                    <th class="text-center">ลำดับ</th>
                                    <th class="text-center">รหัส / ชื่อหน่วยตรวจสอบ</th>
                                    <th class="text-center">ที่อยู่ปัจจุบัน</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <div class="pagination-wrapper"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>
    <script src="{{asset('plugins/components/datatables/jquery.dataTables.min.js')}}"></script>

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
        });

        $(function () {
            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                stateSave: false,
                ajax: {
                    "url": '{!! url('/request-section-5/application-ibcb/change-info/data_list') !!}',
                    "dataType": "json",
                    "data": function(d) {
                        d.filter_search = $('#filter_search').val();
                    }
                },
                columns: [
                    { data: 'DT_Row_Index',     searchable: false, orderable: false },
                    { data: 'ibcb_info',        name: 'ibcb_name', searchable: false, orderable: false },
                    { data: 'ibcb_address_col', name: 'ibcb_address', searchable: false, orderable: false },
                    { data: 'manage',           name: 'manage', searchable: false, orderable: false },
                ],
                columnDefs: [
                    { className: "text-top", targets: [0, 1, 2, 3] },
                ],
                order: [[0, 'desc']],
            });

            $('#btn_filter_search').click(function () { table.draw(); });

            $('#btn_filter_clear').click(function () {
                $('#filter_search').val('');
                table.draw();
            });
        });
    </script>
@endpush
