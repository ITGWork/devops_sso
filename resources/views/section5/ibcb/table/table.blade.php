@push('css')
    <link href="{{asset('plugins/components/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
@endpush

@push('css')
    <style>
        .label-filter{
            margin-top: 7px;
            text-align: right;
        }
    </style>
@endpush

<div class="row">
    <div class="col-md-6">
        {!! Form::label('filter_search', 'คำค้นหา'.': ', ['class' => 'col-md-2 control-label label-filter']) !!}
        <div class="form-group col-md-10">
            {!! Form::text('filter_search', null, ['class' => 'form-control', 'placeholder'=>'ค้นหาจาก รหัส/ชื่อผู้ตรวจสอบ/สาขา/รายสาขา/มอก.', 'id' => 'filter_search']); !!}
        </div>
    </div>
    <div class="col-md-3">
        {!! Form::label('filter_status', 'สถานะ'.': ', ['class' => 'col-md-2 control-label label-filter']) !!}
        <div class="form-group col-md-10">
            {!! Form::select('filter_status', [ 1 => 'Active', 2 => 'Not Active' ], null, ['class' => 'form-control', 'placeholder'=>'เลือกสถานะ', 'id' => 'filter_status']); !!}
        </div>
    </div>

    <div class="col-lg-2">
        <div class="form-group">
            <button type="button" class="btn btn-primary waves-effect waves-light" data-parent="#capital_detail" href="#search-btn" data-toggle="collapse" id="search_btn_all">
                <small>เครื่องมือค้นหา</small> <span class="glyphicon glyphicon-menu-up"></span>
            </button>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-12">
        <div id="search-btn" class="panel-collapse collapse">
            <div class="white-box form-horizontal" style="display: flex; flex-direction: column;">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('filter_branch_group', 'สาขา', ['class' => 'col-md-12']) !!}
                            <div class="col-md-12">
                                {!! Form::select('filter_branch_group', App\Models\Basic\BranchGroup::pluck('title', 'id'), null, ['class' => 'form-control', 'id'=> 'filter_branch_group', 'placeholder'=>'- เลือกสาขา -']); !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('filter_branch', 'รายสาขา', ['class' => 'col-md-12']) !!}
                            <div class="col-md-12">
                                {!! Form::select('filter_branch', App\Models\Basic\Branch::pluck('title', 'id'), null, ['class' => 'form-control', 'id'=> 'filter_branch', 'placeholder'=>'- เลือกรายสาขา -']); !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('filter_tis_id', 'มอก.', ['class' => 'col-md-12']) !!}
                            <div class="col-md-12">
                                {!! Form::text('filter_tis_id', null, ['class' => 'form-control', 'id'=> 'filter_tis_id', 'placeholder'=>'- เลือกมอก. -']); !!}
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-right">

                            <button type="button" class="btn btn-info waves-effect waves-light" style="margin-bottom: -1px;" id="btn_search">ค้นหา</button>

                            <button type="button" class="btn btn-warning waves-effect waves-light" id="btn_clean"> ล้าง </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">

    <div class="col-md-12">
        <button class="btn btn-info btn-sm pull-right" data-toggle="modal" data-target="#modal-cost-info">
            <i class="fa fa-money" aria-hidden="true"></i> อัตราค่าใข้จ่าย
        </button>
    </div>

    <div class="col-md-12">

        <div class="table-responsive">
            <table class="table table-borderless" id="myTable">
                <thead>
                    <tr>
                        <th width="2%" class="text-center">ลำดับ</th>
                        <th width="9%" class="text-center">รหัส</th>
                        <th width="18%" class="text-center">ชื่อผู้ตรวจสอบ</th>
                        <th width="26%" class="text-center">หมวดอุตสาหกรรม/สาขา</th>
                        <th width="9%" class="text-center">วันที่เป็นผู้ตรวจสอบ</th>
                        <th width="9%" class="text-center">วันที่สิ้นสุดผู้ตรวจสอบ</th>
                        <th width="9%" class="text-center">สถานะ</th>
                        <th width="9%" class="text-center">ประกาศ</th>
                        <th width="9%" class="text-center">รายละเอียด</th>
                    </tr>
                </thead>
                <body>

                </body>
            </table>
        </div>
    </div>
</div>

@push('js')
    <script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>
    <script src="{{asset('plugins/components/datatables/jquery.dataTables.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            //Create Data Table
            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    "url": '{!! url('/section5/data_ibcb_list') !!}',
                    "dataType": "json",
                    "data": function (d) {

                        d.filter_search = $('#filter_search').val();
                        d.filter_status = $('#filter_status').val();

                        d.filter_tis_id = $('#filter_tis_id').val();

                        d.filter_branch = $('#filter_branch').val();
                        d.filter_branch_group = $('#filter_branch_group').val();

                        d.filter_layout  = '{!! isset($layout)?$layout:'' !!}';

                    }
                },
                columns: [
                    { data: 'DT_Row_Index', searchable: false, orderable: false},       
                    { data: 'ibcb_code', name: 'ibcb_code' },
                    { data: 'ibcb_name', name: 'ibcb_name' },
                    { data: 'scope_group', name: 'scope_group' },
                    { data: 'start_date', name: 'start_date' },
                    { data: 'end_date', name: 'end_date' },
                    { data: 'state', name: 'state' },
                    { data: 'gazette', name: 'gazette' },
                    { data: 'action', name: 'action' },
                ],
                columnDefs: [
                    { className: "text-top text-center", targets: [0,-1,-2,-3,-4,-5] },
                    { className: "text-top", targets: "_all" },

                ],
                fnDrawCallback: function() {

                }
            });

            $('#btn_search').click(function () {
                table.draw();
            });

            $('#filter_status').change(function () {
                table.draw();
            });

            $('#filter_search').keyup(function () {
                table.draw();
            });

            $('#btn_clean').click(function () {
                $('#filter_search').val('');
                $('#search-btn').find('select').val('').select2();
                $('#search-btn').find('input').val('');
                $('#filter_status').val('').select2();
                $("#filter_tis_id").select2("val", "");
                table.draw();
            });

            $('#filter_branch_group').change(function (e) {

                $('#filter_branch').html('<option value=""> -เลือกรายสาขา- </option>');
                var value = ( $(this).val() != "" )?$(this).val():'ALL';
                if(value){
                    $.ajax({
                        url: "{!! url('/section5/get-branch-data') !!}" + "/" + value
                    }).done(function( object ) {
                        $.each(object, function( index, data ) {
                            $('#filter_branch').append('<option value="'+data.id+'">'+data.title+'</option>');
                        });
                    });
                }

            });

            $("#filter_tis_id").select2({
                dropdownAutoWidth: true,
                width: '100%',
                ajax: {
                    url: "{{ url('/funtions/search-standards') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params // search term
                        };
                    },
                    results: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true,
                },
                placeholder: 'คำค้นหา',
                minimumInputLength: 1,
            });
        });
    </script>
@endpush
