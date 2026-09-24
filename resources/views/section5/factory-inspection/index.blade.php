@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="{{asset('plugins/components/jquery-datatables-editable/datatables.css')}}" />
@endpush

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="white-box">

                    <h3 class="box-title pull-left">รับคำขอตรวจโรงงาน (IB/CB)</h3>
                    <div class="pull-right">
                        <a href="{{ url('section5/factory-inspection') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> กลับ
                        </a>
                    </div>
                    <div class="clearfix"></div>
                    <hr>

                    @if(session('flash_message'))
                        <div class="alert alert-success">{{ session('flash_message') }}</div>
                    @endif
                    @if(session('error_message'))
                        <div class="alert alert-danger">{{ session('error_message') }}</div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-5 form-group">
                                    {{ Form::select('filter_status', App\Models\Elicense\Rform\FactoryDetail::status_list(), null, ['class' => 'form-control', 'placeholder' => '- เลือกสถานะ -', 'id' => 'filter_status']) }}
                                </div>
                                <div class="col-md-5 form-group">
                                    {{ Form::text('filter_search', null, ['class' => 'form-control', 'placeholder' => 'ค้นหา : เลขที่อ้างอิง, ผู้ยื่นคำขอ, เลข มอก.', 'id' => 'filter_search']) }}
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
                                        <th class="text-center" width="12%">เลขที่อ้างอิง</th>
                                        <th class="text-center" width="18%">ผู้ยื่นคำขอ</th>
                                        <th class="text-center" width="13%">เลขผู้เสียภาษี</th>
                                        <th class="text-center" width="15%">มอก.</th>
                                        <th class="text-center" width="10%">สถานะ</th>
                                        <th class="text-center" width="10%">ผลการตรวจ</th>
                                        <th class="text-center" width="12%">ผู้ตรวจสอบ</th>
                                        <th class="text-center" width="8%">วันที่ยื่น</th>
                                        <th class="text-center" width="7%">จัดการ</th>
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
                    url: '{!! url('/section5/factory-inspection/data_list') !!}',
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
                    { data: 'refno',            name: 'refno' },
                    { data: 'applicant_name',   name: 'applicant_name' },
                    { data: 'tax_number',       name: 'tax_number' },
                    { data: 'tis',              name: 'tis' },
                    { data: 'status',           name: 'status' },
                    { data: 'inspection_result',name: 'inspection_result' },
                    { data: 'checking_by',      name: 'checking_by' },
                    { data: 'date_sent',        name: 'date_sent' },
                    { data: 'action',           name: 'action', orderable: false, searchable: false }
                ],
                columnDefs: [
                    { className: "text-center", targets: [0, -2, -1] }
                ],
                order: [[8, 'desc']]
            });

            $('#btn-search').click(function () { table.draw(); });
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
