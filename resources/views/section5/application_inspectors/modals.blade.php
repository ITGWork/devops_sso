<div id="myModalDelete" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">× </button>
                <h4 class="modal-title" id="myModalLabel">ยกเลิกยื่นคำขอขึ้นทะเบียนผู้ตรวจฯ</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-4 text-right">เลขที่คำขอ :</label>
                            <div class="col-md-8">
                                <span class="form-control-static application_no_text"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-4 text-right">ผู้ยื่นคำขอ :</label>
                            <div class="col-md-8">
                                <span class="form-control-static applicant_full_name_text"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-4 text-right">เลขผู้เสียภาษี :</label>
                            <div class="col-md-8">
                                <span class="form-control-static applicant_taxid_text"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label col-md-4 text-right">วันที่ยื่น :</label>
                            <div class="col-md-8">
                                <span class="form-control-static created_at_text"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group required">
                            {!! Form::label('remarks_delete', 'ระบุเหตุผล ', ['class' => 'col-md-12 control-label']) !!}
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