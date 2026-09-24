<div class="modal fade bs-example-modal-lg" role="dialog" aria-labelledby="MinusScopeModalLabel" aria-hidden="true" id="MinusScopeModal" >
    <div class="modal-dialog modal-dialog-centere modal-xl" style="width: 90%; max-width: 1000px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="MinusScopeModalLabel">ลดขอบข่าย</h4>
            </div>
            <div class="modal-body">

                {!! Form::hidden('ibcb_id', $ibcbs->id) !!}

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group required">
                            {!! Form::label('mn_remarks', 'เหตุผล/หมายเหตุการลดขอบข่าย', ['class' => 'col-md-3 control-label text-right']) !!}
                            <div class="col-md-8">
                                {!! Form::textarea('mn_remarks', null, ['class' => 'form-control', 'id' => 'mn_remarks', 'rows' => 2, 'placeholder' => 'ระบุเหตุผลการขอลดขอบข่าย']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <label class="text-bold-600">เลือกขอบข่ายที่ต้องการลด (ลดทั้งกลุ่ม)</label>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center"></th>
                                        <th width="25%" class="text-center">หมวดอุตสาหกรรม/สาขา</th>
                                        <th width="10%" class="text-center">ISIC NO</th>
                                        <th width="20%" class="text-center">เลขที่ มอก.</th>
                                        <th width="40%" class="text-center">รายสาขา</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ibcbs->scopes_group as $key => $item)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" class="scope_id_checkbox"
                                                value="{{ $item->id }}"
                                                data-branch-group-title="{{ @$item->bs_branch_group->title }}"
                                                data-isic-no="{{ $item->isic_no }}"
                                                data-tis-text="{{ !empty($item->ScopeTisNoList)?$item->ScopeTisNoList:'-' }}"
                                                data-branch-text="{{ !empty($item->ScopeBranchs)?$item->ScopeBranchs:'-' }}">
                                        </td>
                                        <td>{!! @$item->bs_branch_group->title !!}</td>
                                        <td class="text-center">{!! !empty($item->isic_no)?$item->isic_no:'-' !!}</td>
                                        <td class="text-center">{!! !empty($item->ScopeTisNoList)?$item->ScopeTisNoList:'-' !!}</td>
                                        <td>{!! !empty($item->ScopeBranchs)?$item->ScopeBranchs:'-' !!}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">- ไม่พบขอบข่ายปัจจุบัน -</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect text-left" id="btn_submit_minus_scope">ยื่นขอลดขอบข่าย</button>
                <button type="button" class="btn btn-default waves-effect text-left" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function () {

            $('#btn_submit_minus_scope').click(function () {
                var checked = $('.scope_id_checkbox:checked');
                var remarks = $('#mn_remarks').val();

                console.log('[DEBUG m-minus-scope] btn_submit_minus_scope clicked. checked count:', checked.length, 'remarks:', remarks);

                if( checked.length == 0 ){
                    alert('กรุณาเลือกขอบข่ายที่ต้องการลดอย่างน้อย 1 รายการ');
                    return false;
                }

                if( !remarks ){
                    alert('กรุณาระบุเหตุผล/หมายเหตุการลดขอบข่าย');
                    return false;
                }

                if(confirm('ยืนยันการขอลดขอบข่ายที่เลือกไว้ทั้งหมด?')){
                    $('#row_no_data').hide();
                    var currentRowCount = $('#table-scope tbody tr').not('#row_no_data').length;

                    checked.each(function () {
                        currentRowCount++;
                        var scope_id = $(this).val();
                        var branch_group_title = $(this).data('branch-group-title');
                        var isic_no = $(this).data('isic-no');
                        var tis_text = $(this).data('tis-text');
                        var branch_text = $(this).data('branch-text');

                        var _newRow = '';
                        _newRow += '<tr style="background-color: #fffde7;" class="pending-scope-row">';
                        _newRow += '<td class="text-center">' + currentRowCount + '</td>';
                        _newRow += '<td class="text-center">' + branch_group_title + '</td>';
                        _newRow += '<td class="text-center">' + (isic_no?isic_no:'-') + '</td>';
                        _newRow += '<td class="text-center">' + tis_text + '</td>';
                        _newRow += '<td>' + branch_text + '</td>';
                        _newRow += '<td class="text-center"><span class="label label-danger">ขอลดขอบข่าย</span></td>';
                        _newRow += '<td class="text-center"><span class="label label-warning"><i class="fa fa-clock-o"></i> ฉบับร่าง</span>';
                        _newRow += '  <button type="button" class="btn btn-danger btn-xs btn_remove_pending_scope" title="ลบรายการนี้"><i class="fa fa-trash-o"></i></button>';
                        _newRow += '<input type="hidden" name="minus_scope['+currentRowCount+'][scope_id]" value="' + scope_id + '">';
                        _newRow += '<input type="hidden" name="minus_scope_remarks" value="' + remarks + '">';
                        _newRow += '</td>';
                        _newRow += '</tr>';

                        $('#table-scope tbody').append(_newRow);
                    });

                    $('.scope_id_checkbox').prop('checked', false);
                    $('#mn_remarks').val('');
                    $('#MinusScopeModal').modal('hide');

                    alert('เพิ่มลงรายการสำเร็จ !\nกรุณาตรวจสอบรายการในตารางและกดบันทึกเมื่อพร้อม');
                }
            });
        });
    </script>
@endpush
