@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="{{asset('plugins/components/jquery-datatables-editable/datatables.css')}}" />
@endpush

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">

                    <h3 class="box-title pull-left">
                        ติดตามคำขอทดสอบผลิตภัณฑ์
                        @if(request()->query('type') == 'elicense')
                            <small class="text-primary">(E-license)</small>
                        @elseif(request()->query('type') == 'esurveillance')
                            <small class="text-success">(E-surveillance)</small>
                        @endif
                    </h3>
                    <div class="pull-right">
                        <a href="{{ url('section5/product-testing') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> กลับ
                        </a>
                    </div>
                    <div class="clearfix"></div>
                    <hr>

                    <div class="row">
                        <div class="col-md-12">

                            <div class="row">
                                <div class="col-md-5 form-group">
                                    <select name="filter_status" id="filter_status" class="form-control">
                                        <option value="">- เลือกสถานะ -</option>
                                        <option value="1">รอการตอบรับ</option>
                                        <option value="2">รับคำขอ</option>
                                        <option value="3">ไม่รับคำขอ</option>
                                        <option value="4">ยกเลิก</option>
                                        <option value="5">อยู่ระหว่างการทดสอบ</option>
                                        <option value="6">ขอตัวอย่างเพิ่มเติม</option>
                                        <option value="7">แจ้งผล</option>
                                        <option value="8">สรุปผล</option>
                                    </select>
                                </div>
                                <div class="col-md-5 form-group">
                                    <input type="text" name="filter_search" id="filter_search"
                                           class="form-control"
                                           placeholder="ค้นหา : เลขที่อ้างอิง, ชื่อโรงงาน, เลข มอก.">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-info waves-effect waves-light" id="btn-search">ค้นหา</button>
                                    <button type="button" class="btn btn-default waves-effect waves-light" id="btn-clear">ล้าง</button>
                                </div>
                            </div>

                            <table class="table table-striped" id="myTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="5%">No.</th>
                                        <th class="text-center" width="15%">เลขที่อ้างอิง</th>
                                        <th class="text-center" width="20%">มอก.</th>
                                        <th class="text-center" width="15%">ชื่อโรงงาน</th>
                                        <th class="text-center" width="15%">ห้องปฏิบัติการ</th>
                                        <th class="text-center" width="10%">สถานะ</th>
                                        <th class="text-center" width="10%">วันที่ยื่น</th>
                                        <th class="text-center" width="10%">รายละเอียด</th>
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
                    url: '{!! url('/section5/product-testing/data_list') !!}',
                    data: function (d) {
                        d.filter_search = $('#filter_search').val();
                        d.filter_status = $('#filter_status').val();
                        d.type = '{{ request()->query("type") }}';
                    }
                },
                columns: [
                    { data: null, searchable: false, orderable: false, render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }},
                    { data: 'refno', name: 'refno' },
                    { data: 'tis', name: 'tis' },
                    { data: 'factory_name', name: 'factory_name' },
                    { data: 'lab_name', name: 'lab_name' },
                    { data: 'status', name: 'status' },
                    { data: 'date_sent', name: 'date_sent' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                columnDefs: [
                    { className: "text-center", targets: [0, 5, 7] }
                ],
                language: {
                    processing: 'กำลังโหลด...',
                    zeroRecords: 'ไม่พบข้อมูลคำขอทดสอบผลิตภัณฑ์ของท่าน'
                }
            });

            $('#btn-search').click(function () {
                table.draw();
            });

            $('#btn-clear').click(function () {
                $('#filter_search').val('');
                $('#filter_status').val('').change();
                table.draw();
            });

            $('#filter_search').keypress(function (e) {
                if (e.which === 13) table.draw();
            });
        });
    </script>
@endpush
