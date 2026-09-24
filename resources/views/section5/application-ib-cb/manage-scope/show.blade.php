@extends('layouts.master')

@push('css')
    <link href="{{asset('plugins/components/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>

    <link href="{{asset('plugins/components/icheck/skins/all.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/components/bootstrap-datepicker-thai/css/datepicker.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('plugins/components/custom-select/custom-select.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('plugins/components/switchery/dist/switchery.min.css')}}" rel="stylesheet" />
    <link href="{{asset('plugins/components/sweet-alert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('plugins/components/jasny-bootstrap/css/jasny-bootstrap.css')}}" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet"/>
    <style>
        .select2 {
            width: 100% !important;
        }
        fieldset.scheduler-border {
            border: 1px groove #ddd !important;
            padding: 0 1.4em 1.4em 1.4em !important;
            margin: 0 0 1.5em 0 !important;
            -webkit-box-shadow:  0px 0px 0px 0px #000;
                    box-shadow:  0px 0px 0px 0px #000;
        }

        legend.scheduler-border {
            font-size: 1.2em !important;
            font-weight: bold !important;
            text-align: left !important;
            width:auto;
            padding:0 10px;
            border-bottom:none;
        }
    </style>
@endpush

{{-- push select2 library ก่อน @section('content') เพื่อให้แน่ใจว่าโหลดก่อนสคริปต์ของ modal ที่ @include อยู่ข้างในเสมอ --}}
@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js" type="text/javascript"></script>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">รายละเอียดหน่วยตรวจสอบผลิตภัณฑ์อุตสาหกรรม (IB/CB)</h3>
                    <a class="btn btn-success btn-sm pull-right" href="{{ url('request-section-5/application-ibcb') }}">
                        <i class="icon-arrow-left-circle" aria-hidden="true"></i> กลับ
                    </a>
                    <div class="clearfix"></div>
                    <hr>

                    {!! Form::open(['url' => '/request-section-5/application-ibcb/submit-scope-final', 'method' => 'POST', 'id' => 'form_final_submit', 'class' => 'form-horizontal', 'files' => true]) !!}
                        {!! Form::hidden('ibcb_id', $ibcbs->id) !!}
                        {{-- มีค่าเฉพาะตอนเข้ามาแก้ไขคำขอเดิมผ่านปุ่ม "แก้ไข"/"จัดการ" ที่เจาะจง ID มา
                             (ดู $draft_app ใน ApplicationIbcbController::ibcbs_show()) — ถ้าว่าง final_submit_scope()
                             จะสร้างคำขอใหม่เสมอ ไม่ทับของเดิม --}}
                        @if(!empty($draft_app))
                            {!! Form::hidden('application_id', $draft_app->id) !!}
                        @endif
                        <div class="row mt-4">
                            <div class="col-md-12">
                                @include('section5.application-ib-cb.manage-scope.form.information')
                                <hr>
                                @include('section5.application-ib-cb.manage-scope.form.scope')
                            </div>
                        </div>
                    {!! Form::close() !!}

                    {{-- แสดงหมายเหตุ/ประวัติการพิจารณาของเจ้าหน้าที่ กรณีคำขอนี้เคยถูกตีกลับมาแก้ไข
                         (มิเรอร์ pattern เดียวกับ cancellation/details.blade.php และ change-info/details.blade.php
                         — $draft_app มาจาก ApplicationIbcbController::ibcbs_show() อยู่แล้ว) --}}
                    @if(!empty($draft_app) && $draft_app->application_ibcb_accepts()->count() > 0)
                        @include('section5.application-ib-cb.history', ['applicationibcb' => $draft_app])
                    @endif
                </div>

                @include('section5.application-ib-cb.manage-scope.modals.m-add-scope')
                @include('section5.application-ib-cb.manage-scope.modals.m-minus-scope')

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{asset('plugins/components/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/components/custom-select/custom-select.js')}}"></script>
    <script src="{{asset('plugins/components/custom-select/custom-select.min.js')}}"></script>
    <script src="{{asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>
    <script src="{{asset('plugins/components/loading-overlay/js/loadingoverlay.min.js')}}"></script>
    <script src="{{asset('plugins/components/repeater/jquery.repeater.min.js')}}"></script>
    <script src="{{asset('js/jasny-bootstrap.js')}}"></script>
    <script src="{{asset('plugins/components/icheck/icheck.min.js')}}"></script>
    <script src="{{asset('plugins/components/icheck/icheck.init.js')}}"></script>
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker-thai.js') }}"></script>
    <script src="{{ asset('plugins/components/bootstrap-datepicker-thai/js/locales/bootstrap-datepicker.th.js') }}"></script>
    <script>
        function checkNone(value) {
            return value !== '' && value !== null && value !== undefined;
        }

        function BoxAuditType1(){
            var audit_type = ($("input[name=audit_type]:checked").val() == 1) ? '1' : '2';
            if( audit_type == '1' ){
                $('.box_audit_type_1').show();
                $('.box_audit_type_1').find('input').prop('disabled', false);
            }else{
                $('.box_audit_type_1').hide();
                $('.box_audit_type_1').find('input').prop('disabled', true);
            }
        }

        function ShowInputCertificate(){
            var value_btn = $('#btn_std_export').val();

            $('#certificate_issue_date').prop('disabled', true);
            $('#certificate_expire_date').prop('disabled', true);
            $('body').find('.certificate_cerno_export').prop('disabled', true);

            if( value_btn == '1' ){
                $('body').find('.certificate_cerno_export').prop('disabled', false);
                $('#certificate_issue_date').prop('disabled', false);
                $('#certificate_expire_date').prop('disabled', false);
            }
        }

        function LoadStdType(){
            var application_type = $('#application_type').val();
            $('#certificate_std_export').html('<option value=""> -เลือกมอก. รับรองระบบงาน- </option>');

            if( checkNone(application_type) ){
                $.ajax({
                    url: "{!! url('/request-section-5/application-ibcb/get-standards') !!}" + "/" + application_type
                }).done(function( object ) {
                    if( checkNone(object) ){
                        $.each( object, function( key, value ) {
                            $('#certificate_std_export').append('<option value="'+key+'">'+value+'</option>');
                        });
                    }
                });
            }
        }

        $(document).ready(function () {
            // หมายเหตุ: #table-scope ไม่ใช้ DataTables — ตารางนี้ถูกเพิ่มแถวด้วย jQuery .append() ตรงๆ
            // จาก modal เพิ่ม/ลดขอบข่าย (คนละสคริปต์บล็อกกัน ไม่มีทางเรียก DataTables API ได้)
            // ถ้า init เป็น DataTables จะทำให้แถวที่ append เข้ามาไม่ถูกนับ/แสดงผลถูกต้อง
            $('#filter_search').on( 'keyup', function () {
                var keyword = $.trim(this.value).toLowerCase();
                $('#table-scope tbody tr').not('#row_no_data').each(function () {
                    var rowText = $(this).text().toLowerCase();
                    $(this).toggle(keyword === '' || rowText.indexOf(keyword) > -1);
                });
            } );

            jQuery('.mydatepicker').datepicker({
                toggleActive: true,
                language: 'th-th',
                format: 'dd/mm/yyyy',
            });

            LoadStdType();

            $('.repeater-form-other').repeater({
                show: function () {
                    $(this).slideDown();
                },
                hide: function (deleteElement) {
                    if (confirm('คุณต้องการลบแถวนี้ใช่หรือไม่ ?')) {
                        $(this).slideUp(deleteElement);
                    }
                }
            });

            $('input[name=audit_type]').on('ifChecked', function(event){
                BoxAuditType1();
            });
            BoxAuditType1();

            $('#btn_std_export').click(function (e) {
                $('#Mcertificate').modal('show');
            });

            var tableCer = $('#myTableCertificate').DataTable({
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    "url": '{!! url('/request-section-5/application-ibcb/getDataCertificate') !!}',
                    "dataType": "json",
                    "data": function (d) {
                        d.application_type = $('#application_type').val();
                        d.table = (( $('#application_type').val() == 1 ) ? 'app_certi_ib_export' : 'app_certi_cb_export');
                        d.applicant_taxid = $('#applicant_taxid').val();
                        d.search = $('#modal_cer_search').val();
                    }
                },
                columns: [
                    { data: 'DT_Row_Index', searchable: false, orderable: false},
                    { data: 'cb_name', name: 'cb_name' },
                    { data: 'formula', name: 'formula' },
                    { data: 'certificate', name: 'certificate' },
                    { data: 'date_start', name: 'date_start' },
                    { data: 'date_end', name: 'date_end' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action' },
                ],
            });

            $("body").on('keyup', '#modal_cer_search', function () {
                tableCer.draw();
            });

            $('body').on('click','.btn_select_cer', function () {
                var cerno = $('#certificate_cerno_export');
                var issue_date = $('#certificate_issue_date');
                var expire_date = $('#certificate_expire_date');

                var Mcer_no = $(this).data('certificate_no');
                var Mdate_start = $(this).data('date_start');
                var Mdate_end = $(this).data('date_end');
                var Mid = $(this).data('id');
                var Mtable = $(this).data('table');

                $(cerno).val(Mcer_no);
                $(issue_date).val(Mdate_start);
                $(expire_date).val(Mdate_end);

                $(cerno).attr("data-id", (checkNone(Mid) ? Mid : ''));
                $(cerno).attr("data-table", (checkNone(Mtable) ? Mtable : ''));

                $('#btn_std_export').val(2);

                ShowInputCertificate();

                $('#Mcertificate').modal('hide');
            });

            $('body').on('click','#btn_cer_add', function (e) {
                var std = $('#certificate_std_export').val();
                var std_txt = $('#certificate_std_export').find('option:selected').text();
                var cerno = $('#certificate_cerno_export').val();
                var issue_date = $('#certificate_issue_date').val();
                var expire_date = $('#certificate_expire_date').val();

                if( !checkNone(std) ){
                    alert("กรุณากรอก มอก. รับรองระบบงาน !");
                }else if( !checkNone(cerno) ){
                    alert("กรุณากรอก เลขที่ได้รับการรับรอง !");
                }else if( !checkNone(issue_date) ){
                    alert("กรุณากรอก วันที่ออกใบรับรอง !");
                }else if( !checkNone(expire_date) ){
                    alert("กรุณากรอก วันที่หมดอายุใบรับรอง !");
                }else{
                    var id = $('#certificate_cerno_export').data("id");
                    var table = $('#certificate_cerno_export').data("table");

                    var val_btn = $('#btn_std_export').val();
                    if( val_btn == 1 ){
                        id = '';
                        table = '';
                    }

                    var certificate_std_id = '<input type="hidden" name="certificate_std_id" value="'+std+'">';
                    var certificate_id = '<input type="hidden" class="certificate_id" name="certificate_id" value="'+(checkNone(id)?id:'')+'">';
                    var certificate_no = '<input type="hidden" class="certificate_no" name="certificate_no" value="'+(checkNone(cerno)?cerno:'')+'">';
                    var certificate_start_date = '<input type="hidden" name="certificate_start_date" value="'+issue_date+'">';
                    var certificate_end_date = '<input type="hidden" name="certificate_end_date" value="'+expire_date+'">';
                    var certificate_table = '<input type="hidden" name="certificate_table" value="'+(checkNone(table)?table:'')+'">';

                    var btn = '<button class="btn btn-sm btn-danger" type="button" data-repeater-delete> <i class="fa fa-minus"></i></button>';

                    var values = $('#table-certificate').find(".certificate_id").map(function(){ return $(this).val(); }).get();

                    var tag_a;
                    if( checkNone(id) ){
                        var url_center = '{!! isset( HP::getConfig()->url_center )?HP::getConfig()->url_center:'' !!}';
                        tag_a = '<a href="'+(url_center)+'/api/v1/certificate?cer='+(cerno)+'" target="_blank"><span class="text-info">'+(cerno)+'</span></a>';
                    }else{
                        tag_a = cerno;
                    }

                    if( !checkNone(id) || values.indexOf(String(id)) == -1 ){
                        var tr_ = '<tr data-repeater-item>';
                            tr_ += '<td>'+tag_a+' '+ certificate_id + certificate_no +'</td>';
                            tr_ += '<td>'+issue_date+' '+ certificate_start_date +'</td>';
                            tr_ += '<td>'+expire_date+' '+ certificate_end_date +'</td>';
                            tr_ += '<td>'+std_txt+' '+certificate_std_id+'</td>';
                            tr_ += '<td>'+btn+' '+ certificate_table +'</td>';
                            tr_ += '</tr>';

                        $('#table-certificate tbody').append(tr_);
                        $('.certificate-repeater').repeater();
                    }

                    setTimeout(function(){
                        $('#certificate_std_export').val('').trigger('change');
                        $('#certificate_cerno_export').val('');
                        $('#certificate_issue_date').val('');
                        $('#certificate_expire_date').val('');

                        $('#certificate_cerno_export').removeAttr("data-id");
                        $('#certificate_cerno_export').removeAttr("data-table");

                        $('#btn_std_export').val(1);
                        ShowInputCertificate();
                    }, 100);
                }
            });
        });
    </script>
@endpush
