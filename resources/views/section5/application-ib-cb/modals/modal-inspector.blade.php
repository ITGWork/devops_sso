
<div class="modal fade" role="dialog" aria-labelledby="InspectorsModalLabel" aria-hidden="true" id="Minspectors" >
    <div class="modal-dialog modal-dialog-centere modal-lg" style="width: 1140px;max-width: 1140px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="InspectorsModalLabel">รายชื่อผู้ตรวจ</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::text('modal_inspectors_search', null, ['class' => 'form-control', 'placeholder'=> 'ชื่อผู้ตรวจ/เลขบัตร', 'id' => 'modal_inspectors_search']) !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">

                                {!! Form::select('modal_inspectors_branch_group',  App\Models\Basic\BranchGroup::pluck('title', 'id')->all() , null, ['class' => 'form-control', 'id'=> 'modal_inspectors_branch_group', 'placeholder'=>'-เลือกสาขา-']); !!}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::select('modal_inspectors_branch',  App\Models\Basic\Branch::pluck('title', 'id')->all() , null, ['class' => 'form-control', 'id'=> 'modal_inspectors_branch', 'placeholder'=>'-เลือกรายสาขา-']); !!}
                            </div>
                        </div>
                        {{-- <div class="col-md-4">
                            <div class="form-group">
                                <div class="checkbox checkbox-info">
                                    <input id="modal_inspectors_freelance" type="checkbox" value="1">
                                    <label for="modal_inspectors_freelance">ผู้ตรวจ Freelance !</label>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-info pull-right" type="button" id="btn_inspectors_search"> ค้นหา</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <i class="text-danger pull-right">* รายชื่อผู้ตรวจจะแสดงตามสาขา และมีขอบข่าย มอก. อย่างน้อย 1 มอก. ตามที่เลือกไว้ในใบสมัคร</i>
                            <table class="table table-bordered table-sm" id="myTableInspectors" data-toggle="table" >
                                <thead>
                                    <tr>
                                        <th class="text-center"><input type="checkbox" id="inspectors_checkall"></th>
                                        <th class="text-center">#</th>
                                        <th class="text-center">ชื่อผู้ตรวจ</th>
                                        <th class="text-center">เลขบัตร</th>
                                        <th class="text-center">ประเภทผู้ตรวจ</th>
                                        <th class="text-center">สาขาผลิตภัณฑ์</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect text-left" id="bulk_select_inspectors">เลือก</button>
                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script type="text/javascript">

        $(document).ready(function () {

            $('#modal_inspectors_branch_group').change(function (e) {

                $('#modal_inspectors_branch').html('<option value=""> -เลือกรายสาขา- </option>');
                var value = ( $(this).val() != "" )?$(this).val():'ALL';
                if(value){
                    $.ajax({
                        url: "{!! url('/funtions/get-branch-data') !!}" + "/" + value
                    }).done(function( object ) {
                        $.each(object, function( index, data ) {
                            $('#modal_inspectors_branch').append('<option value="'+data.id+'">'+data.title+'</option>');
                        });
                    });
                }

            });

            var tableInspectors = $('#myTableInspectors').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    "url": '{!! url('/request-section-5/application-ibcb/getDataInspectors') !!}',
                    "type": "POST",
                    "dataType": "json",
                    "data": function (d) {
                        d._token = $('meta[name="csrf-token"]').attr('content');
                        d.search = $('#modal_inspectors_search').val();
                        d.freelance = (( $("#modal_inspectors_freelance:checked").val() == 1 )?'All':'1');
                        d.branch_group = $('#modal_inspectors_branch_group').val();
                        d.branch = $('#modal_inspectors_branch').val();
                        d.agency_taxid = $('#applicant_taxid').val();
                        // d.branch_group_id = $('#table-scope').find(".branch_group_id").map(function(){return $(this).val(); }).get();
                        d.branch_id = $('#table-scope').find('[data-name="branch_id"]').map(function(){ return $(this).val(); }).get(); //ไอดีสาขาจากคำขอ
                        d.tis_nos   = $('#table-scope').find('input.tis_details').map(function(){ return $.trim($(this).data('tis_no')); }).get(); //เลขมอก.จากคำขอ
                    }
                },
                columns: [
                    { data: 'checkbox', searchable: false, orderable: false },
                    { data: 'DT_Row_Index', searchable: false, orderable: false},
                    { data: 'full_name', name: 'full_name' },
                    { data: 'inspectors_taxid', name: 'inspectors_taxid' },
                    { data: 'inspector_type', name: 'inspector_type' },
                    { data: 'scope', name: 'scope' },
                ],
                columnDefs: [
                    { className: "text-center col-md-1 text-top", targets: [0, 1] },
                    { className: "col-md-2 text-top", targets: [2] },
                    { className: "col-md-2 text-top", targets: [3, 4] },
                    { className: "col-md-4", targets: [5] }
                ],
                fnDrawCallback: function() {

                }
            });

            //เมื่อ modal เปิดขึ้นมาr
            $('#Minspectors').on('show.bs.modal', function (e) {
                tableInspectors.draw(); //โหลดข้อมูลผู้ตรวจมาใหม่
            })

            $("body").on('click', '#btn_inspectors_search', function () {
                tableInspectors.draw();
            });

            // เลือกทั้งหมด checkbox
            $('#inspectors_checkall').on('click', function(e) {
                if($(this).is(':checked',true)){
                    $(".item_checkbox").prop('checked', true);
                } else {
                    $(".item_checkbox").prop('checked',false);
                }
            });


            $("body").on('click', '#bulk_select_inspectors', function(){

                var id = [];

                var values = $('.inspectors-repeater').find(".inspector_id").map(function(){return $(this).val(); }).get();

                if( $('.item_checkbox:checked').length == 0 ){
                    alert('กรุณาเลือกผู้ตรวจ !');
                }else{

                    //วนตามรายชื่อผู้ตรวจที่เลือกจาก checkbox
                    $('.item_checkbox:checked').each(function(index, element){

                        var scope = $(element).data('scope');
                        var full_name = $(element).data('full_name');
                        var taxid = $(element).data('taxid');
                        var id = $(element).data('id');

                        var prefix = $(element).data('inspectors_prefix');
                        var first_name = $(element).data('inspectors_first_name');
                        var last_name = $(element).data('inspectors_last_name');
                        var type = $(element).data('inspector_type');

                        var branch_group_title = [];

                        var htmls = '';
                        var group_id = [];
                        var inputB = '';
                        if( checkNone(scope) ){
                            var replace = scope.replaceAll("'", '"');
                            var object = JSON.parse(replace);
                            htmls += '<ul class="list-unstyled">';
                            $.each(object, function( index, data ) {

                                group_id.push( data.branch_group_id );

                                htmls += '<li>'+(data.branch_group_title)+'</li>';
                                htmls += '<li>';
                                var branch = data.branch;

                                var branch_title = [];
                                var branch_id = [];

                                $.each(branch, function( index2, ItemBranch ) {
                                    branch_title.push( ItemBranch.branch_title );
                                    branch_id.push( ItemBranch.branch_id );
                                });

                                inputB += '<input type="hidden" name="branch_id_'+(data.branch_group_id)+'" value="'+branch_id+'">';

                                htmls += '<ul>';
                                htmls += '<li>'+(branch_title.join(', '))+'</li>';
                                htmls += '</ul>';
                                htmls += '</li>';
                            });
                            htmls += '</ul>';
                        }

                        var input_  = '<input type="hidden" class="inspector_id" name="inspector_id" value="'+id+'">';
                            input_  += '<input type="hidden" name="inspector_taxid" value="'+taxid+'">';
                            input_  += '<input type="hidden" name="inspector_prefix" value="'+prefix+'">';
                            input_  += '<input type="hidden" name="inspector_first_name" value="'+first_name+'">';
                            input_  += '<input type="hidden" name="inspector_last_name" value="'+last_name+'">';
                            input_  += '<input type="hidden" name="inspector_type" value="'+type+'">';
                            input_  += '<input type="hidden" name="branch_group_id" value="'+group_id+'">';
                            input_  += inputB;

                        var btn = '<button class="btn btn-sm btn-danger" type="button" data-repeater-delete> <i class="fa fa-minus"></i></button>';


                        if( values.indexOf( String(id) ) == -1 ){//ถ้ายังไม่มีรายชื่อใน ตารางรายชื่อผู้ตรวจที่ผ่านการแต่งตั้ง

                            var tr_ = '<tr data-repeater-item>';
                                tr_ += '<td class="ins_no text-center"></td>';
                                tr_ += '<td class="text-left">'+(full_name)+''+(input_)+'</td>';
                                tr_ += '<td class="text-left">'+taxid+'</td>';
                                tr_ += '<td class="text-left">'+htmls+'</td>';
                                tr_ += '<td class="text-left">'+(type==1 ? 'ผู้ตรวจของหน่วยตรวจ' : 'ผู้ตรวจอิสระ')+'</td>';
                                tr_ += '<td class="text-center">'+btn+'</td>';
                                tr_ += '</tr>';

                                $('#table-inspectors tbody').append(tr_);
                                $('.inspectors-repeater').repeater();

                                resetInsNo2();

                        }

                    });

                    //ปิด Modal
                    $('#Minspectors').modal('hide');

                    $(".item_checkbox").prop('checked', false);
                    $("#inspectors_checkall").prop('checked', false);

                }

            });

        });



    </script>
@endpush
