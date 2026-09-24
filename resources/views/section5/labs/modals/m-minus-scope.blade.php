<div class="modal fade bs-example-modal-lg" role="dialog" aria-labelledby="MinusScopeModalLabel" aria-hidden="true" id="MinusScopeModal" >
    <div class="modal-dialog modal-dialog-centere modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="MinusScopeModalLabel">ขอลดขอบข่าย</h4>
            </div>
            <div class="modal-body">
                <div class="row form-horizontal">
                    <div class="col-md-12">

                        <form id="form_minus_scope" onsubmit="return false">

                            {!! Form::hidden('lab_id', $labs->id) !!}
                            {!! Form::hidden('_token', csrf_token()) !!}

                            <div class="row">
                                <div class="form-group required">
                                    {!! Form::label('mn_close_date', 'วันที่ลดขอบข่าย', ['class' => 'control-label text-right col-md-3']) !!}
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            {!! Form::text('mn_close_date', HP::revertDate(date('Y-m-d'),true),  ['class' => 'form-control', 'disabled']) !!}
                                            <span class="input-group-addon"><i class="icon-calender"></i></span>
                                        </div>
                                    </div>
                                </div>                    
                            </div>

                            <div class="row">
                                <div class="form-group required">
                                    {!! Form::label('mn_close_remarks', 'เหตุผล/หมายเหตุ', ['class' => 'col-md-3 control-label text-right']) !!}
                                    <div class="col-md-8">
                                        {!! Form::textarea('mn_close_remarks', null, ['class' => 'form-control', 'rows' => 3, 'id' => 'mn_close_remarks']) !!}
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="box_minus_scope">
                                <p class="text-info"><i class="fa fa-info-circle"></i> เลือกขอบข่ายที่ต้องการขอลด</p>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="5%"><input type="checkbox" id="checkall_scope"></th>
                                            <th class="text-center" width="95%">รายการทดสอบที่รับการแต่งตั้ง</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $scope_standard  = $labs->scope_standard()->get()->groupBy('tis_id');
                                            $scope_standard_name = $labs->scope_standard()->get()->pluck('StandardTisNoName', 'tis_id')->toArray();
                                        @endphp

                                        @foreach ( $scope_standard as $tis_key => $scope )
                                            <tr class="group" style="background:#f1f1f1;">
                                                <td colspan="2"><b>{!! array_key_exists( $tis_key, $scope_standard_name )?$scope_standard_name[ $tis_key ]:''  !!}</b></td>
                                            </tr>
                                            @foreach ($scope as $item )
                                                @php
                                                    $test_item = $item->test_item;
                                                    if(!is_null($test_item)){
                                                        if(  $test_item->type == 1 ){
                                                            $item->test_item_title = ( !empty( $test_item->no )?$test_item->no.' ' :null ).$test_item->title;
                                                        }else{
                                                            $mains  =  $test_item->test_item_main;
                                                            $mains_title = !is_null($mains) ? ( ( !empty( $mains->no )?$mains->no.' ' :null ).$mains->title ) : '' ;
                                                            $item->test_item_title = ( !empty( $test_item->no )?$test_item->no.' ' :null ).$test_item->title.' <em>(ภายใต้หัวข้อทดสอบ '.$mains_title.')</em>';
                                                        }
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="scope_id[]" class="scope_id_checkbox" 
                                                            value="{!! $item->id !!}"
                                                            data-tis-id="{!! $item->tis_id !!}"
                                                            data-tis-tisno="{!! @$item->tis_standards->tb3_Tisno !!}"
                                                            data-test-item-id="{!! $item->test_item_id !!}"
                                                        >
                                                    </td>
                                                    <td class="minus-scope-title">
                                                        {!! !empty( $item->test_item_title )?$item->test_item_title:null !!}
                                                        Exp. {!! !empty($item->end_date)?HP::revertDate($item->end_date,true):'-' !!}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect" id="save_minus_scope" >ยื่นขอลดขอบข่าย</button>
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            
            $('#checkall_scope').change(function() {
                $('.scope_id_checkbox').prop('checked', $(this).prop('checked'));
            });

            $(document).on('click', '#save_minus_scope', function (e) {
                console.log('Save Minus Scope Clicked');

                var close_remarks = $('#mn_close_remarks').val();
                
                var id = [];
                $('.scope_id_checkbox:checked').each(function(index, element){
                    id.push($(element).val());
                });

                console.log('IDs to reduce:', id);
                console.log('Remarks:', close_remarks);

                if( $.trim(close_remarks) != '' ){
                    if( id.length > 0 ){
                        if(confirm('ยืนยันการเพิ่มรายการขอลดขอบข่ายลงในตาราง?')){
                            var currentRowCount = $('#table-scope tbody tr').not('#table-scope-empty-msg').length;

                            $('.scope_id_checkbox:checked').each(function(index, el) {
                                currentRowCount++;
                                var scope_id = $(el).val();
                                var tis_id = $(el).data('tis-id');
                                var tis_tisno = $(el).data('tis-tisno');
                                var test_item_id = $(el).data('test-item-id');
                                var test_item_text = $(el).closest('tr').find('.minus-scope-title').text().trim();

                                var _newRow = '';
                                _newRow += '<tr style="background-color: #fff3e0;" class="pending-scope-row">';
                                _newRow += '<td class="text-center">' + currentRowCount + '</td>';
                                _newRow += '<td class="text-center">' + tis_tisno + '</td>';
                                _newRow += '<td>' + test_item_text + '</td>';
                                _newRow += '<td class="text-center"><span class="label label-danger">ขอลดขอบข่าย</span></td>';
                                _newRow += '<td class="text-center"><span class="label label-warning"><i class="fa fa-clock-o"></i> ฉบับร่าง</span>';
                                _newRow += '  <button type="button" class="btn btn-danger btn-xs btn_remove_pending_scope" title="ลบรายการนี้"><i class="fa fa-trash-o"></i></button>';
                                // Hidden inputs for final submission
                                _newRow += '<input type="hidden" name="minus_scope_id[]" value="' + scope_id + '">';
                                _newRow += '<input type="hidden" name="minus_scope_tis_id[]" value="' + tis_id + '">';
                                _newRow += '<input type="hidden" name="minus_scope_tis_tisno[]" value="' + tis_tisno + '">';
                                _newRow += '<input type="hidden" name="minus_scope_test_item_id[]" value="' + test_item_id + '">';
                                _newRow += '<input type="hidden" name="minus_scope_remarks_reduce[]" value="' + close_remarks + '">';
                                _newRow += '<input type="hidden" name="minus_scope_type[]" value="3">';
                                _newRow += '</td>';
                                _newRow += '</tr>';

                                $('#table-scope tbody').append(_newRow);
                            });

                            // อัปเดตสถานะแถว "ไม่มีข้อมูล" ของ #table-scope (ซ่อนเพราะตอนนี้มีแถวจริงแล้ว)
                            if (window.reloadScopeTable) { window.reloadScopeTable(); }

                            // Uncheck all and close modal
                            $('.scope_id_checkbox').prop('checked', false);
                            $('#checkall_scope').prop('checked', false);
                            $('#mn_close_remarks').val('');
                            $('#MinusScopeModal').modal('hide');

                            alert('เพิ่มลงรายการสำเร็จ !\nกรุณาตรวจสอบรายการในตารางและกดบันทึกเมื่อพร้อม');
                        }
                    }else {
                        alert("โปรดเลือกรายการทดสอบอย่างน้อย 1 รายการที่ต้องการขอลด");
                    }
                }else{
                    alert('กรุณากรอกเหตุผล/หมายเหตุในการขอลด !');
                }
            });

        });
    </script>
@endpush
