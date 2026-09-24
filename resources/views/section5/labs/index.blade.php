@extends('layouts.master')

@section('title', 'รายชื่อหน่วยตรวจสอบ (LAB)')

@push('css')
    <link href="{{asset('plugins/components/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{asset('plugins/components/toast-master/css/jquery.toast.css')}}">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title">รายชื่อหน่วยตรวจสอบ (LAB)</h3>
                    <p class="text-muted">แสดงเฉพาะข้อมูลของผู้ใช้งานที่เข้าสู่ระบบ</p>
                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    {!! Form::label('filter_search', 'คำค้นหา:', ['class' => 'col-md-2 control-label']) !!}
                                    <div class="form-group col-md-10">
                                        {!! Form::text('filter_search', null, ['class' => 'form-control', 'placeholder'=>'ค้นหาจาก หน่วยงาน / เลขนิติบุคคล', 'id' => 'filter_search']); !!}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-info waves-effect waves-light" id="btn_search">ค้นหา</button>
                                    <button type="button" class="btn btn-warning waves-effect waves-light" id="btn_clean">ล้าง</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-borderless" id="myTable">
                            <thead>
                                <tr>
                                    <th width="2%" class="text-center">ลำดับ</th>
                                    <th width="10%" class="text-center">รหัส</th>
                                    <th width="20%" class="text-center">ห้องปฏิบัติการ</th>
                                    <th width="12%" class="text-center">เลขนิติบุคคล</th>
                                    <th width="15%" class="text-center">มอก.ที่ตรวจสอบได้</th>
                                    <th width="10%" class="text-center">วันที่เป็นหน่วยตรวจสอบ</th>
                                    <th width="10%" class="text-center">วันที่สิ้นสุดเป็นหน่วยตรวจสอบ</th>
                                    <th width="10%" class="text-center">สถานะ</th>
                                    <th width="5%" class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
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

            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    "url": '{!! url('/section5/data_labs_list') !!}',
                    "dataType": "json",
                    "data": function (d) {
                        d.filter_search = $('#filter_search').val();
                        d.filter_trader = true; // Use a flag to filter by current user's tax ID in backend if necessary
                        d.lab_id = '{{ !empty($lab_id) ? $lab_id : '' }}';
                        d.item_no = '{{ !empty($item_no) ? $item_no : '' }}';
                    }
                },
                columns: [
                    { data: 'DT_Row_Index', searchable: false, orderable: false},
                    { data: 'lab_code', name: 'lab_code' },
                    { data: 'lab_name', name: 'lab_name' },
                    { data: 'taxid', name: 'taxid' },
                    { data: 'standards', name: 'standards' },
                    { data: 'start_date', name: 'start_date' },
                    { data: 'end_date', name: 'end_date' },
                    { data: 'state', name: 'state' },
                    { data: 'action', name: 'action' },
                ],
                columnDefs: [
                    { className: "text-center", targets: [0, 1, 3, 5, 6, 7, 8] },
                    { className: "text-left", targets: [2, 4] }
                ],
                fnDrawCallback: function() {
                }
            });

            $('#btn_search').click(function () {
                table.draw();
            });

            $('#btn_clean').click(function () {
                $('#filter_search').val('');
                table.draw();
            });

        });
    </script>
@endpush
