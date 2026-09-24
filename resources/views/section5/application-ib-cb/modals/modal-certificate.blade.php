
<div class="modal fade" role="dialog" aria-labelledby="CerModalLabel" aria-hidden="true" id="Mcertificate" >
    <div class="modal-dialog modal-dialog-centere modal-lg" style="width: 1140px;max-width: 1140px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="CerModalLabel">ใบรับรองระบบงาน</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">

                        <div class="form-group">
                            {!! Form::label('modal_cer_search', 'คำค้น.', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-8">
                                {!! Form::text('modal_cer_search', null, ['class' => 'form-control', 'placeholder'=> 'เลขที่ใบรับรอง', 'id' => 'modal_cer_search']) !!}
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="myTableCertificate" data-toggle="table" >
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">ชื่อหน่วยงาน</th>
                                        <th class="text-center">มอก. ที่รับการรับรอง</th>
                                        <th class="text-center">เลขที่ได้รับรอง</th>
                                        <th class="text-center">วันที่ได้รับ</th>
                                        <th class="text-center">วันหมดอายุ</th>
                                        <th class="text-center">สถานะ</th>
                                        <th class="text-center">เลือก</th>
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
                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
