<div id="myModalDelete" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">× </button>
                <h4 class="modal-title" id="myModalLabel">ยกเลิกข้อมูลมอบสิทธิ์การเข้าใช้งานระบบ</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-4 text-right">ผู้รับมอบสิทธิ์ :</label>
                            <div class="col-md-8">
                                <p class="form-control-static agent_name_txt"></p>
                            </div>
                        </div>
                    </div>
                    <!--/span-->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-4 text-right">เลขประจำตัวผู้เสียภาษี :</label>
                            <div class="col-md-8">
                                <p class="form-control-static agent_taxid_txt"></p>
                            </div>
                        </div>
                    </div>
                    <!--/span-->
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-4 text-right">วันที่มอบสิทธิ์ :</label>
                            <div class="col-md-8">
                                <p class="form-control-static created_at_txt"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group required">
                            {!! Form::label('remarks_delete', 'หมายเหตุ ', ['class' => 'col-md-12 control-label']) !!}
                            <div class="col-md-12">
                                {!! Form::textarea('remarks_delete', null,  ['class' => 'form-control input_show', 'rows' => 4 ]) !!}
                            </div>
                        </div>
                    </div>
                </div>

                {!! Form::hidden('modal_delete_id',  null, [ 'class' => 'input_show', 'id' => 'modal_delete_id' ] ) !!}
    

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect" id="btn_delete_save">บันทึก</button>
                <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    <!-- /.modal-content -->
    </div>
<!-- /.modal-dialog -->
</div>