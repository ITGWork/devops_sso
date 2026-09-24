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

        .select2-drop-auto-width{ /* มอก. */
            left: 62.7px !important;
        }

        
    </style>
@endpush

<div class="row">
    <div class="col-md-6">
        {!! Form::label('filter_search', 'คำค้นหา'.': ', ['class' => 'col-md-2 control-label label-filter']) !!}
        <div class="form-group col-md-10">
            {!! Form::text('filter_search', null, ['class' => 'form-control', 'placeholder'=>'ค้นหาจาก รหัส / ชื่อหน่วยตรวจสอบ / เลข มอก.', 'id' => 'filter_search']); !!}
        </div>
    </div>
    <div class="col-md-3">
        {!! Form::label('filter_status', 'สถานะ'.': ', ['class' => 'col-md-2 control-label label-filter']) !!}
        <div class="form-group col-md-10">
            {!! Form::select('filter_status', [ 1 => 'Active', 2 => 'Not Active' ], null, ['class' => 'form-control', 'placeholder'=>'เลือก สถานะ', 'id' => 'filter_status']); !!}
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
                            {!! Form::label('filter_tis_id', 'มอก.', ['class' => 'col-md-12']) !!}
                            <div class="col-md-12">
                                {!! Form::text('filter_tis_id', null, ['class' => 'form-control', 'id'=> 'filter_tis_id', 'placeholder'=>'- เลือก มอก. -']); !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Html::decode(Form::label('filter_test_item_id', 'รายการทดสอบ'.' <em class="text-warning">(แสดงตาม มอก.)</em>', ['class' => 'col-md-12'])) !!}
                            <div class="col-md-12">
                                {!! Form::select('filter_test_item_id', [], null, ['class' => '' , 'multiple' => 'multiple' , 'id'=> 'filter_test_item_id', 'data-placeholder'=>'- เลือก รายการทดสอบ -']); !!}
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
        <div class="table-responsive">
            <table class="table table-borderless" id="myTable">
                <thead>
                    <tr>
                        <th width="2%" class="text-center">ลำดับ</th>
                        <th width="9%" class="text-center">รหัส</th>
                        <th width="18%" class="text-center">ห้องปฏิบัติการ</th>
                        <th width="9%" class="text-center">วันที่เป็นหน่วยตรวจสอบ</th>
                        <th width="9%" class="text-center">วันที่สิ้นสุดหน่วยตรวจสอบ</th>
                        <th width="26%" class="text-center">เลข มอก.</th>
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
                    "url": '{!! url('/section5/data_labs_list') !!}',
                    "dataType": "json",
                    "data": function (d) {

                        d.filter_search = $('#filter_search').val();
                        d.filter_status = $('#filter_status').val();

                        d.filter_tis_id = $('#filter_tis_id').val();

                        d.filter_layout  = '{!! isset($layout)?$layout:'' !!}';
                        d.filter_test_item_id = $('#filter_test_item_id').val();
                        // d.filter_branch_group = $('#filter_branch_group').val();

                    }
                },
                columns: [
                    { data: 'DT_Row_Index', searchable: false, orderable: false},       
                    { data: 'lab_code', name: 'lab_code' },
                    { data: 'lab_name', name: 'lab_name' },
                    { data: 'start_date', name: 'start_date' },
                    { data: 'end_date', name: 'end_date' },
                    { data: 'standards', name: 'standards' },
                    { data: 'state', name: 'state' },
                    { data: 'gazette', name: 'gazette' },
                    { data: 'action', name: 'action' },
                ],
                columnDefs: [
                    { className: "text-top text-center", targets: [ 0, -1, -2, -3, -5, -6 ] },
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

            $('#filter_tis_id').change(function (e) {

                var select = $('#filter_test_item_id');
                $(select).html('');
                $(select).val('').trigger('change');

                var val = $(this).val();

                if( val != ''){
                    $.ajax({
                        url: "{!! url('/section5/get-test-item') !!}" + "/" + val
                    }).done(function( object ) {

                        if( object.length > 0){
                            $.each(object, function( index, data ) {
                                $('#filter_test_item_id').append('<option value="'+data.id+'">'+data.title+'</option>');
                            });
                        }

                    });
                }

            });

            let filter_tis_id = $("#filter_tis_id").select2({
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