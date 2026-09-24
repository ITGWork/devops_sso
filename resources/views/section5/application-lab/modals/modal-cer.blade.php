@push('css')
    <link href="{{asset('plugins/components/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
@endpush


<div class="modal fade bs-example-modal-lg" role="dialog" aria-labelledby="CerModalLabel" aria-hidden="true" id="CerModal" >
    <div class="modal-dialog modal-dialog-centere modal-lg" style="width: 1140px;max-width: 1140px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="CerModalLabel">ใบรับรองระบบงาน 17025</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">

                        <div class="form-group required">
                            {!! Form::label('modal_cer_search', 'คำค้น.', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-8">
                                {!! Form::text('modal_cer_search', null, ['class' => 'form-control', 'placeholder'=> 'ชื่อห้องปฏิบัติการม, เลขที่ใบรับรอง', 'id' => 'modal_cer_search']) !!}
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
                                        <th class="text-center">ชื่อห้องปฏิบัติการ</th>
                                        <th class="text-center">เลขที่ใบรับรอง</th>
                                        <th class="text-center">หมายเลขการรับรอง</th>
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

@push('js')
    <script src="{{asset('plugins/components/datatables/jquery.dataTables.min.js')}}"></script>
    <script>
        $(document).ready(function () {

            var tableCer = $('#myTableCertificate').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                stateSave: true,
                stateDuration: 60 * 60 * 24,
                ajax: {
                    "url": '{!! url('/request-section-5/application-lab/data_list_cer') !!}',
                    "dataType": "json",
                    "data": function (d) {
                        d.filter_search = $('#modal_cer_search').val();
                        d.tax_id = $('#applicant_taxid').val();
                    }
                },
                columns: [
                    { data: 'DT_Row_Index', searchable: false, orderable: false},
                    { data: 'lab_name', name: 'lab_name' },
                    { data: 'certificate_no', name: 'certificate_no' },
                    { data: 'accereditatio_no', name: 'accereditatio_no' },
                    { data: 'certificate_date_start', name: 'certificate_date_start' },
                    { data: 'certificate_date_end', name: 'certificate_date_end' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action' },
                ],
                columnDefs: [
                    { className: "text-center", targets: [0,-1,-2] },
                    // { className: "text-left", targets: [1,2] }
                ],
                fnDrawCallback: function() {

                }
            });

            $("body").on('keyup', '#modal_cer_search', function () {
                tableCer.draw();
            });

            $("body").on('click', '.btn_select_cer', function () {

                var cerno =  $('#certificate_cerno_export');
                var issue_date =  $('#certificate_issue_date');
                var expire_date =  $('#certificate_expire_date');
                var accereditatio_no =  $('#certificate_accereditatio_no');
                
                var Mcer_no = $(this).data('certificate_no'); 
                var Mdate_start = $(this).data('date_start'); 
                var Mdate_end = $(this).data('date_end'); 
                var Mid = $(this).data('id');
                var Mtable = $(this).data('table'); 
                var Maccereditatio_no = $(this).data('accereditatio_no'); 

                $(cerno).val(Mcer_no);
                $(issue_date).val(Mdate_start);
                $(expire_date).val(Mdate_end);
                $(accereditatio_no).val(Maccereditatio_no);

                $( cerno ).attr( "data-id", (checkNone(Mid)?Mid:'') );
                $( cerno ).attr( "data-table", (checkNone(Mtable)?Mtable:'') );
                $( cerno ).attr( "data-accereditatio_no", (checkNone(Maccereditatio_no)?Maccereditatio_no:'') );
                
                $('#btn_std_export').val(2);

                ShowInputCertificate();

                $('#CerModal').modal('hide');
                
            });

        }); 
    </script>
@endpush