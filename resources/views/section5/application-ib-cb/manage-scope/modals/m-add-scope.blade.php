<div class="modal fade bs-example-modal-lg" role="dialog" aria-labelledby="ScopeModalLabel" aria-hidden="true" id="AddScopeModal" >
    <div class="modal-dialog modal-dialog-centere modal-xl" style="width: 90%; max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="ScopeModalLabel">เพิ่มขอบข่าย</h4>
            </div>
            <div class="modal-body">
                <div class="row form-horizontal">
                    <div class="col-md-12">
                        @php
                            $list_isic_no = App\Models\Basic\BranchGroup::whereNotNull('isic_no')->select('isic_no', 'id')->get()->pluck('isic_no', 'id')->toArray();

                            $list_standard = App\Models\Basic\Tis::select('tb3_Tisno', 'tb3_TisThainame', 'tb3_TisAutono AS id')->orderBy('tb3_Tisno')->get();
                            $option_standard = [];
                            foreach ($list_standard as $key => $item) {
                                $option_standard[$item->id] = $item->tb3_Tisno.' : '.(strip_tags($item->tb3_TisThainame));
                            }
                        @endphp
                        <div class="row">
                            <div class="form-group required">
                                {!! Form::label('modal_branch_group_id', 'หมวดอุตสาหกรรม/สาขา', ['class' => 'col-md-3 control-label text-right']) !!}
                                <div class="col-md-8">
                                    {!! Form::select('modal_branch_group_id', App\Models\Basic\BranchGroup::pluck('title', 'id')->all(), null, ['class' => 'form-control not_select2', 'placeholder'=>'- เลือกหมวดอุตสาหกรรม/สาขา -', 'id' => 'modal_branch_group_id', 'style' => 'width: 100%;']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                {!! Form::label('modal_isic_no', 'ISIC NO', ['class' => 'col-md-3 control-label text-right']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('modal_isic_no', null, ['class' => 'form-control', 'id' => 'modal_isic_no']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row box_input_detail">
                            <div class="form-group required">
                                {!! Form::label('modal_tis_id', 'เลขที่ มอก.', ['class' => 'col-md-3 control-label text-right']) !!}
                                <div class="col-md-8">
                                    {!! Form::select('modal_tis_id[]', $option_standard, null, ['class' => 'form-control not_select2', 'id' => 'modal_tis_id', 'style' => 'width: 100%;', 'multiple' => 'multiple']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row box_input_detail">
                            <div class="form-group required">
                                {!! Form::label('modal_branch_id', 'รายสาขา', ['class' => 'col-md-3 control-label text-right']) !!}
                                <div class="col-md-8">
                                    {!! Form::select('modal_branch_id[]', [], null, ['class' => 'form-control not_select2', 'placeholder'=>'- เลือกรายสาขา -', 'id' => 'modal_branch_id', 'style' => 'width: 100%;', 'multiple' => 'multiple']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-11">
                            <div class="pull-right">
                                <button type="button" class="btn btn-success waves-effect text-left" id="btn_add_to_table"><i class="fa fa-plus"></i> เพิ่มเข้าสู่ตาราง</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">

                        <form enctype="multipart/form-data" class="form-horizontal" id="form_add_scope" onsubmit="return false">
                            <div class="table-responsive repeater-table-scope">
                                <table class="table table-bordered table-sm" id="myTableScope" data-toggle="table" >
                                    <thead>
                                        <tr>
                                            <th align="top" width="5%" class="text-center">#</th>
                                            <th align="top" width="25%" class="text-center align-top">หมวดอุตสาหกรรม/สาขา</th>
                                            <th align="top" width="10%" class="text-center align-top">ISIC NO</th>
                                            <th align="top" width="20%" class="text-center align-top">เลขที่ มอก.</th>
                                            <th align="top" width="35%" class="text-center">รายสาขา</th>
                                            <th align="top" width="5%" class="text-center">ลบ</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-top" data-repeater-list="repeater-scope">

                                    </tbody>
                                </table>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect text-left" id="btn_submit_add_scope" style="display:none;">ยื่นคำขอเพิ่มเติมขอบข่าย</button>
                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function () {

            var list_isic_no = $.parseJSON('{!! json_encode($list_isic_no) !!}');

            console.log('[DEBUG m-add-scope] select2 available?', typeof $.fn.select2);
            console.log('[DEBUG m-add-scope] #modal_branch_group_id found?', $('#modal_branch_group_id').length);
            console.log('[DEBUG m-add-scope] #modal_tis_id found?', $('#modal_tis_id').length);
            console.log('[DEBUG m-add-scope] #modal_branch_id found?', $('#modal_branch_id').length);

            try {
                $('#modal_branch_group_id').select2({ dropdownParent: $('#AddScopeModal') });
            } catch (e) {
                console.error('[DEBUG m-add-scope] select2 init FAILED on #modal_branch_group_id:', e);
            }
            try {
                $('#modal_tis_id').select2({ dropdownParent: $('#AddScopeModal') });
            } catch (e) {
                console.error('[DEBUG m-add-scope] select2 init FAILED on #modal_tis_id (options count: ' + $('#modal_tis_id option').length + '):', e);
            }
            try {
                $('#modal_branch_id').select2({ dropdownParent: $('#AddScopeModal') });
            } catch (e) {
                console.error('[DEBUG m-add-scope] select2 init FAILED on #modal_branch_id:', e);
            }

            console.log('[DEBUG m-add-scope] select2 init done, has select2 class?',
                $('#modal_branch_group_id').hasClass('select2-hidden-accessible'),
                $('#modal_tis_id').hasClass('select2-hidden-accessible'),
                $('#modal_branch_id').hasClass('select2-hidden-accessible')
            );

            $("#modal_branch_group_id").on('change', function () {
                var val = $(this).val();
                $('#modal_branch_id').html('');
                $('#modal_branch_id').val('').trigger('change.select2');
                $('#modal_isic_no').val('');

                if( val != '' && $.isNumeric(val) ){

                    if( list_isic_no[val] !== undefined ){
                        $('#modal_isic_no').val(list_isic_no[val]);
                    }

                    $.LoadingOverlay("show", { image: "", text: "กำลังเรียกข้อมูลรายสาขา..." });
                    $.ajax({
                        url: "{!! url('/request-section-5/application-ibcb/get-branche') !!}" + "/" + val
                    }).done(function( object ) {
                        console.log('[DEBUG m-add-scope] get-branche response for branch_group_id=' + val, object);
                        if( object.length > 0){
                            $.each(object, function( index, data ) {
                                $('#modal_branch_id').append('<option value="'+data.id+'">'+data.title+'</option>');
                            });
                        }else{
                            $('#modal_branch_id').append('<option value="" disabled> - ไม่พบข้อมูลรายสาขา - </option>');
                        }
                    }).fail(function(xhr) {
                        console.log('[DEBUG m-add-scope] get-branche AJAX failed', xhr.status, xhr.responseText);
                        alert('เกิดข้อผิดพลาด\nไม่สามารถโหลดข้อมูลรายสาขาได้ กรุณาลองใหม่อีกครั้ง');
                    }).always(function() {
                        $.LoadingOverlay("hide");
                    });

                }
            });

            $('#btn_add_to_table').click(function (e) {
                var branch_group_id = $('#modal_branch_group_id').val();
                var branch_group_text = $('#modal_branch_group_id').find('option:selected').text();
                var isic_no = $('#modal_isic_no').val();
                var tis_ids = $('#modal_tis_id').val();
                var tis_texts = $('#modal_tis_id').find('option:selected').map(function(){ return $(this).text(); }).get();
                var branch_ids = $('#modal_branch_id').val();
                var branch_texts = $('#modal_branch_id').find('option:selected').map(function(){ return $(this).text(); }).get();

                console.log('[DEBUG m-add-scope] btn_add_to_table clicked. captured values:', {
                    branch_group_id: branch_group_id,
                    isic_no: isic_no,
                    tis_ids: tis_ids,
                    branch_ids: branch_ids
                });

                if( !branch_group_id ){
                    alert('กรุณาเลือกหมวดอุตสาหกรรม/สาขา');
                }else if( !tis_ids || tis_ids.length == 0 ){
                    alert('กรุณาเลือกเลขที่ มอก.');
                }else if( !branch_ids || branch_ids.length == 0 ){
                    alert('กรุณาเลือกรายสาขา');
                }else{
                    var LastRow = $('#myTableScope tbody tr').length;

                    var branch_inputs = '';
                    $.each(branch_ids, function(index, branch_id) {
                        branch_inputs += '<input type="hidden" class="Mscope_branch_id" name="repeater-scope['+LastRow+'][branch_id][]" value="'+branch_id+'">';
                    });

                    var tis_inputs = '';
                    $.each(tis_ids, function(index, tis_id) {
                        var tis_text = tis_texts[index];
                        var explode_tis = tis_text.split(':');
                        var tis_no = $.trim(explode_tis[0]);
                        var tis_name = $.trim(explode_tis.slice(1).join(':'));
                        tis_inputs += '<input type="hidden" class="Mscope_tis_id" name="repeater-scope['+LastRow+'][tis_id][]" value="'+tis_id+'">';
                        tis_inputs += '<input type="hidden" class="Mscope_tis_no" name="repeater-scope['+LastRow+'][tis_no][]" value="'+tis_no+'">';
                        tis_inputs += '<input type="hidden" class="Mscope_tis_name" name="repeater-scope['+LastRow+'][tis_name][]" value="'+tis_name+'">';
                    });

                    var _tr = '';
                        _tr += '<tr data-repeater-item>';
                        _tr += '<td class="text-top"><span class="Modalno">'+(LastRow+1)+'</span></td>';
                        _tr += '<td class="text-top"><input type="hidden" class="Mscope_branch_group_id" name="repeater-scope['+LastRow+'][branch_group_id]" value="'+branch_group_id+'">'+branch_group_text+'</td>';
                        _tr += '<td class="text-top"><input type="hidden" class="Mscope_isic_no" name="repeater-scope['+LastRow+'][isic_no]" value="'+isic_no+'">'+(isic_no?isic_no:'-')+'</td>';
                        _tr += '<td class="text-top">'+(tis_inputs)+''+tis_texts.join(', ')+'</td>';
                        _tr += '<td class="text-top">'+(branch_inputs)+''+branch_texts.join(', ')+'</td>';
                        _tr += '<td class="text-top"><button type="button" class="btn btn-danger btn-sm btn_remove_modalscope">ลบ</button></td>';
                        _tr += '</tr>';

                    $('#myTableScope tbody').append(_tr);

                    $('.Modalno').each(function(index, el) {
                        $(el).text(index+1);
                    });

                    $('#modal_branch_group_id').val('').trigger('change.select2');
                    $('#modal_tis_id').val('').trigger('change.select2');
                    $('#modal_branch_id').html('').trigger('change.select2');
                    $('#modal_isic_no').val('');

                    // ปุ่ม "ยื่นคำขอเพิ่มเติมขอบข่าย" จะแสดงก็ต่อเมื่อมีรายการในตารางแล้วเท่านั้น
                    $('#btn_submit_add_scope').show();
                }
            });

            $(document).on('click', '.btn_remove_modalscope', function () {
                if(confirm('ยืนยันการลบข้อมูล แถวนี้')){
                    $(this).closest('tr').remove();

                    $('#myTableScope tbody tr').each(function(index, tr) {
                        $(tr).find('.Modalno').text(index+1);
                        $(tr).find('.Mscope_branch_group_id').attr('name', 'repeater-scope['+index+'][branch_group_id]');
                        $(tr).find('.Mscope_isic_no').attr('name', 'repeater-scope['+index+'][isic_no]');
                        $(tr).find('.Mscope_tis_id').attr('name', 'repeater-scope['+index+'][tis_id][]');
                        $(tr).find('.Mscope_tis_no').attr('name', 'repeater-scope['+index+'][tis_no][]');
                        $(tr).find('.Mscope_tis_name').attr('name', 'repeater-scope['+index+'][tis_name][]');
                        $(tr).find('.Mscope_branch_id').attr('name', 'repeater-scope['+index+'][branch_id][]');
                    });

                    if ($('#myTableScope tbody tr').length === 0) {
                        $('#btn_submit_add_scope').hide();
                    }
                }
            });

            $('#btn_submit_add_scope').click(function () {
                var tbody = $('#myTableScope tbody tr').length;
                if(tbody == 0){
                    alert('กรุณาเพิ่มรายการเข้าตารางก่อนยื่นคำขอ');
                    return false;
                }

                if(confirm('ยืนยันการเพิ่มขอบข่ายลงในรายการ?')){
                    $('#row_no_data').hide();
                    var currentRowCount = $('#table-scope tbody tr').not('#row_no_data').length;

                    $('#myTableScope tbody tr').each(function(index, tr) {
                        currentRowCount++;
                        var branch_group_id = $(tr).find('.Mscope_branch_group_id').val();
                        var branch_group_text = $(tr).find('td:eq(1)').text().trim();
                        var isic_no = $(tr).find('.Mscope_isic_no').val();
                        var tis_text = $(tr).find('td:eq(3)').text().trim();
                        var branch_text = $(tr).find('td:eq(4)').text().trim();

                        var branch_inputs = '';
                        $(tr).find('.Mscope_branch_id').each(function() {
                            branch_inputs += '<input type="hidden" name="add_scope['+currentRowCount+'][branch_id][]" value="'+$(this).val()+'">';
                        });

                        var tis_inputs = '';
                        $(tr).find('.Mscope_tis_id').each(function(tis_index) {
                            var tis_id = $(this).val();
                            var tis_no = $(tr).find('.Mscope_tis_no').eq(tis_index).val();
                            var tis_name = $(tr).find('.Mscope_tis_name').eq(tis_index).val();
                            tis_inputs += '<input type="hidden" name="add_scope['+currentRowCount+'][tis]['+tis_index+'][tis_id]" value="'+tis_id+'">';
                            tis_inputs += '<input type="hidden" name="add_scope['+currentRowCount+'][tis]['+tis_index+'][tis_no]" value="'+tis_no+'">';
                            tis_inputs += '<input type="hidden" name="add_scope['+currentRowCount+'][tis]['+tis_index+'][tis_name]" value="'+tis_name+'">';
                        });

                        var _newRow = '';
                        _newRow += '<tr style="background-color: #fffde7;" class="pending-scope-row">';
                        _newRow += '<td class="text-center">' + currentRowCount + '</td>';
                        _newRow += '<td class="text-center">' + branch_group_text + '</td>';
                        _newRow += '<td class="text-center">' + (isic_no?isic_no:'-') + '</td>';
                        _newRow += '<td class="text-center">' + tis_text + '</td>';
                        _newRow += '<td>' + branch_text + '</td>';
                        _newRow += '<td class="text-center"><span class="label label-info">ขอเพิ่มขอบข่าย</span></td>';
                        _newRow += '<td class="text-center"><span class="label label-warning"><i class="fa fa-clock-o"></i> ฉบับร่าง</span>';
                        _newRow += '  <button type="button" class="btn btn-danger btn-xs btn_remove_pending_scope" title="ลบรายการนี้"><i class="fa fa-trash-o"></i></button>';
                        _newRow += '<input type="hidden" name="add_scope['+currentRowCount+'][branch_group_id]" value="' + branch_group_id + '">';
                        _newRow += '<input type="hidden" name="add_scope['+currentRowCount+'][isic_no]" value="' + isic_no + '">';
                        _newRow += branch_inputs;
                        _newRow += tis_inputs;
                        _newRow += '</td>';
                        _newRow += '</tr>';

                        $('#table-scope tbody').append(_newRow);
                    });

                    $('#myTableScope tbody').empty();
                    $('#btn_submit_add_scope').hide();
                    $('#AddScopeModal').modal('hide');

                    alert('เพิ่มลงรายการสำเร็จ !\nกรุณาตรวจสอบรายการในตารางและกดบันทึกเมื่อพร้อม');
                }
            });
        });
    </script>
@endpush
