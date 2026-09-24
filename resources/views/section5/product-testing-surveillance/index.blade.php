@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="{{asset('plugins/components/jquery-datatables-editable/datatables.css')}}" />
@endpush

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">

                    <h3 class="box-title pull-left">รับคำขอทดสอบผลิตภัณฑ์</h3>
                    <div class="pull-right">
                    </div>
                    <div class="clearfix"></div>
                    <hr>

                    <div class="row">
                        <div class="col-md-12">

                            <div class="clearfix"></div>

                            <div class="row">
                                <div class="col-md-5 form-group">
                                    <select name="filter_status" class="form-control" id="filter_status">
                                        <option value="">- เลือกสถานะ -</option>
                                        <option value="1">รอการตอบรับ</option>
                                        <option value="2">รับคำขอ</option>
                                        <option value="3">ไม่รับคำขอ</option>
                                        <option value="4">อยู่ระหว่างการทดสอบ</option>
                                        <option value="5">แจ้งผล</option>
                                        <option value="6">สรุปผล</option>
                                    </select>
                                </div>
                                <div class="col-md-5 form-group">
                                    <input type="text" name="filter_search" class="form-control" id="filter_search"
                                           placeholder="ค้นหา : เลขที่อ้างอิง, ผู้ยื่นคำขอ, เลข มอก.">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-info waves-effect waves-light" id="btn-search">ค้นหา</button>
                                    <button type="button" class="btn btn-default waves-effect waves-light" id="btn-clear">ล้าง</button>
                                </div>
                            </div>

                            <table class="table table-striped" id="myTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="4%">No.</th>
                                        <th class="text-center" width="12%">เลขที่อ้างอิง</th>
                                        <th class="text-center" width="15%">ผู้ยื่นคำขอ</th>
                                        <th class="text-center" width="12%">เลขประจำตัวผู้เสียภาษี</th>
                                        <th class="text-center" width="14%">มอก.</th>
                                        <th class="text-center" width="9%">รายการทดสอบ</th>
                                        <th class="text-center" width="9%">สถานะ</th>
                                        <th class="text-center" width="8%">วันที่ยื่น</th>
                                        <th class="text-center" width="11%">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>
    <script src="{{asset('plugins/components/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/components/jquery-datatables-editable/jquery.dataTables.js')}}"></script>
    <script src="{{asset('plugins/components/datatables/dataTables.bootstrap.js')}}"></script>

    <script>
        $(document).ready(function () {

            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: '{!! url('/section5/product-testing-surveillance/data_list') !!}',
                    data: function (d) {
                        d.filter_search = $('#filter_search').val();
                        d.filter_status = $('#filter_status').val();
                    }
                },
                columns: [
                    { data: null, searchable: false, orderable: false, render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }},
                    { data: 'refno', name: 'refno' },
                    { data: 'applicant_name', name: 'applicant_name' },
                    { data: 'tax_number', name: 'tax_number' },
                    { data: 'tis', name: 'tis' },
                    { data: null, orderable: false, searchable: false, render: function(data, type, row) {
                        return row.items.length + ' รายการ';
                    }},
                    { data: 'status', name: 'status' },
                    { data: 'date_sent', name: 'date_sent' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                columnDefs: [
                    { className: "text-center", targets:[0,5,6,7,8] }
                ],
                order: [[1, 'desc']],
            });

            $('#btn-search').click(function () {
                table.draw();
            });

            $('#btn-clear').click(function () {
                $('#filter_search').val('');
                $('#filter_status').val('').change();
                table.draw();
            });
        });
    </script>
@endpush
