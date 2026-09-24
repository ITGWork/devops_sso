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
        .text-bold-300 { font-weight: 300; }
        .text-bold-400 { font-weight: 400; }
        .text-bold-500 { font-weight: 500; }
        .text-bold-600 { font-weight: 600; }
        .text-bold-700 { font-weight: 700; }

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
                    <h3 class="box-title pull-left">รายละเอียดหน่วยตรวจสอบผลิตภัณฑ์อุตสาหกรรม</h3>
                    <a class="btn btn-success btn-sm pull-right" href="{{ url('request-section-5/application-lab') }}">
                        <i class="icon-arrow-left-circle" aria-hidden="true"></i> กลับ
                    </a>
                    <div class="clearfix"></div>
                    <hr>

                    {!! Form::open(['url' => '/request-section-5/application-lab/submit-scope-final', 'method' => 'POST', 'id' => 'form_final_submit', 'class' => 'form-horizontal', 'files' => true]) !!}
                        {!! Form::hidden('lab_id', $labs->id) !!}
                        {{-- มีค่าเฉพาะตอนเข้ามาแก้ไขคำขอเดิมผ่านปุ่ม "แก้ไข"/"จัดการ" ที่เจาะจง ID มา
                             (ดู $draft_app ใน ApplicationLabController::labs_show()) — ถ้าว่าง final_submit_scope()
                             จะสร้างคำขอใหม่เสมอ ไม่ทับของเดิม --}}
                        @if(!empty($draft_app))
                            {!! Form::hidden('application_id', $draft_app->id) !!}
                        @endif
                        <div class="row mt-4">
                            <div class="col-md-12">
                                @include('section5.labs.form.infomation')
                                <hr>
                                @include('section5.labs.form.scope')
                            </div>
                        </div>
                    {!! Form::close() !!}

                    {{-- แสดงหมายเหตุ/ประวัติการพิจารณาของเจ้าหน้าที่ กรณีคำขอนี้เคยถูกตีกลับมาแก้ไข
                         (มิเรอร์ pattern เดียวกับ cancellation/details.blade.php และ change-info/details.blade.php
                         — $draft_app มาจาก ApplicationLabController::labs_show() อยู่แล้ว) --}}
                    @if(!empty($draft_app) && $draft_app->app_accept()->count() > 0)
                        @include('section5.application-lab.history', ['applicationlab' => $draft_app])
                    @endif
                </div>

                @include('section5.labs.modals.m-minus-scope')

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{asset('plugins/components/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/components/custom-select/custom-select.js')}}"></script>
    <script src="{{asset('plugins/components/custom-select/custom-select.min.js')}}"></script>
    <script src="{{asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('plugins/components/bootstrap-datepicker-thai/js/bootstrap-datepicker-thai.js')}}"></script>
    <script src="{{asset('plugins/components/bootstrap-datepicker-thai/js/locales/bootstrap-datepicker.th.js')}}"></script>
    <script src="{{asset('plugins/components/sweet-alert2/sweetalert2.all.min.js')}}"></script>
    <script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>
    <script src="{{asset('plugins/components/loading-overlay/js/loadingoverlay.min.js')}}"></script>
    <script src="{{asset('plugins/components/repeater/jquery.repeater.min.js')}}"></script>
    <script src="{{asset('plugins/components/icheck/icheck.min.js')}}"></script>
    <script src="{{asset('plugins/components/icheck/icheck.init.js')}}"></script>
    <script>
        function checkNone(value) {
            return value !== '' && value !== null && value !== undefined;
        }

        function use_audit(){
            if( $('#audit_type_1').is(':checked',true) ){
                $('.box_audit_type_1').show();
                $('.box_audit_type_1').find('input, select, hidden, checkbox').prop('disabled', false);
                $('.box_audit_type_1').find('.certificate_end_date, .certificate_start_date').prop('required', true);

                $('.box_audit_type_2').hide();
                $('.box_audit_type_2').find('input, select, hidden, checkbox').prop('disabled', true);
                $('.box_audit_type_2').find('.audit_date_start, .audit_date_end').prop('required', false);
            }else if( $('#audit_type_2').is(':checked',true) ){
                $('.box_audit_type_1').hide();
                $('.box_audit_type_1').find('input, select, hidden, checkbox').prop('disabled', true);
                $('.box_audit_type_1').find('.certificate_end_date, .certificate_start_date').prop('required', false);

                $('.box_audit_type_2').show();
                $('.box_audit_type_2').find('input, select, hidden, checkbox').prop('disabled', false);
                $('.box_audit_type_2').find('.audit_date_start, .audit_date_end').prop('required', true);
            }
        }

        function ShowInputCertificate(){
            var value_btn = $('#btn_std_export').val();

            $('#certificate_issue_date').prop('disabled', true);
            $('#certificate_expire_date').prop('disabled', true);
            $('#certificate_accereditatio_no').prop('disabled', true);
            $('body').find('.certificate_cerno_export').prop('disabled', true);

            if( value_btn == '1' ){
                $('body').find('.certificate_cerno_export').prop('disabled', false);
                $('#certificate_issue_date').prop('disabled', false);
                $('#certificate_expire_date').prop('disabled', false);
                $('#certificate_accereditatio_no').prop('disabled', false);
            }
        }

        $(document).ready(function () {

            @if(\Session::has('flash_message'))
                $.toast({
                    heading: 'สำเร็จ!',
                    position: 'top-center',
                    text: '{{session()->get('flash_message')}}',
                    loaderBg: '#ff6849',
                    icon: 'success',
                    hideAfter: 3000,
                    stack: 6
                });
            @endif

            @if(\Session::has('message_error'))
                $.toast({
                    heading: 'เกิดข้อผิดพลาด!',
                    position: 'top-center',
                    text: '{{session()->get('message_error')}}',
                    loaderBg: '#ff6849',
                    icon: 'error',
                    hideAfter: 4000,
                    stack: 6
                });
            @endif

            // #table-scope "ไม่" ใช้ DataTables แล้ว เพราะปุ่ม "เพิ่มขอบข่าย/ลดขอบข่าย" เพิ่มแถวใหม่ด้วย
            // jQuery.append() ตรงๆ (ไม่ผ่าน DataTables API) — DataTables เวอร์ชันนี้ (fnDestroy/legacy) เวลาเรียก
            // .destroy() จะ "restore" DOM ของตารางกลับไปเป็นสถานะตอน initialize ครั้งแรกเสมอ (ล้างแถวที่ถูก
            // เพิ่มด้วย jQuery หลังจากนั้นทิ้งหมด แม้จะลบแถว dataTables_empty ออกก่อนแล้วก็ตาม) ทำให้ข้อมูลที่
            // เพิ่งเพิ่มหายไปทุกครั้งที่มีการ reload ตาราง — ใช้ plain table ธรรมดา + filter เอง ปลอดภัยกว่า
            window.reloadScopeTable = function () {
                var hasRows = $('#table-scope tbody tr').not('#table-scope-empty-msg').length > 0;
                $('#table-scope-empty-msg').toggle(!hasRows);
                // ยังไม่มีรายการขอเพิ่ม มอก. ใหม่/ขอลด → ซ่อนทั้งหัวข้อและตาราง
                $('#box_scope_table_wrap').toggle(hasRows);
            };
            window.reloadScopeTable();

            $('#filter_search').on( 'keyup', function () {
                var keyword = $.trim(this.value).toLowerCase();
                var $rows = $('#table-scope tbody tr').not('#table-scope-empty-msg');
                if (keyword === '') {
                    $rows.show();
                } else {
                    $rows.each(function () {
                        var text = $(this).text().toLowerCase();
                        $(this).toggle(text.indexOf(keyword) !== -1);
                    });
                }
            } );

            jQuery('.mydatepicker').datepicker({
                toggleActive: true,
                language: 'th-th',
                format: 'dd/mm/yyyy',
                autoclose: true,
            });

            $('.repeater_audit_type_1').repeater({
                show: function () {
                    $(this).slideDown();
                },
                hide: function (deleteElement) {
                    if (confirm('คุณต้องการลบแถวนี้ใช่หรือไม่ ?')) {
                        $(this).slideUp(deleteElement);
                    }
                }
            });

            $('.repeater_audit_type_2').repeater({
                show: function () {
                    $(this).slideDown();
                    jQuery('.date-range').datepicker({
                        toggleActive: true,
                        language: 'th-th',
                        format: 'dd/mm/yyyy',
                        autoclose: true,
                    });
                },
                hide: function (deleteElement) {
                    if (confirm('คุณต้องการลบแถวนี้ใช่หรือไม่ ?')) {
                        $(this).slideUp(deleteElement);
                    }
                }
            });

            jQuery('.date-range').datepicker({
                toggleActive: true,
                language: 'th-th',
                format: 'dd/mm/yyyy',
                autoclose: true,
            });

            $('input[name=audit_type]').on('ifChecked', function(event){
                use_audit();
            });
            use_audit();

            $('body').on('click', '#btn_std_export', function () {
                $('#CerModal').modal('show');
            });

            $('body').on('click', '#btn_cer_add', function (e) {
                var cerno = $('#certificate_cerno_export').val();
                var issue_date = $('#certificate_issue_date').val();
                var expire_date = $('#certificate_expire_date').val();
                var accereditatio_no = $('#certificate_accereditatio_no').val();

                var values = $('.repeater_audit_type_1').find('.certificate_ids').map(function(){ return $(this).val(); }).get();

                if( !checkNone(cerno) ){
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

                    var certificate_id = '<input type="hidden" class="certificate_ids" name="certificate_id" value="'+(checkNone(id)?id:'')+'">';
                    var certificate_no = '<input type="hidden" class="certificate_no" name="certificate_no" value="'+(checkNone(cerno)?cerno:'')+'">';
                    var certificate_start_date = '<input type="hidden" name="certificate_start_date" value="'+issue_date+'">';
                    var certificate_end_date = '<input type="hidden" name="certificate_end_date" value="'+expire_date+'">';
                    var certificate_table = '<input type="hidden" name="certificate_table" value="'+(checkNone(table)?table:'')+'">';
                    var certificate_accereditatio_no = '<input type="hidden" name="accereditatio_no" value="'+(checkNone(accereditatio_no)?accereditatio_no:'')+'">';

                    var btn = '<button class="btn btn-sm btn-danger" type="button" data-repeater-delete> <i class="fa fa-minus"></i></button>';

                    var url_center = '{!! isset( HP::getConfig()->url_center )?HP::getConfig()->url_center:'' !!}';

                    var inputFile = '';
                    if( !checkNone(id) ){
                        inputFile += '<div class="fileinput fileinput-new input-group" data-provides="fileinput">';
                        inputFile +=  '<div class="form-control" data-trigger="fileinput"><span class="fileinput-filename"></span></div>';
                        inputFile +=  '<span class="input-group-addon btn btn-default btn-file">';
                        inputFile +=  '<span class="fileinput-exists" data-dismiss="fileinput">ลบ</span>';
                        inputFile +=  '<span class="fileinput-new">เลือกไฟล์</span>';
                        inputFile +=  '<span class="fileinput-exists">เปลี่ยน</span>';
                        inputFile +=  '<input type="file" name="certificate_file" class="certificate_file" accept=".pdf,.jpg,.png">';
                        inputFile +=  '</span>';
                        inputFile +=  '</div>';
                    }else{
                        inputFile += '<a href="'+(url_center)+'/api/v1/certificate?cer='+(cerno)+'" target="_blank"><span class="text-info"><i class="fa fa-file"></i></span></a>';
                    }

                    var tr_ = '<tr data-repeater-item>';
                        tr_ += '<td>'+cerno+' '+ certificate_id + certificate_no +'</td>';
                        tr_ += '<td>'+accereditatio_no+' '+ certificate_accereditatio_no +'</td>';
                        tr_ += '<td>'+issue_date+' '+ certificate_start_date +'</td>';
                        tr_ += '<td>'+expire_date+' '+ certificate_end_date +'</td>';
                        tr_ += '<td>'+ inputFile +'</td>';
                        tr_ += '<td>'+btn+' '+ certificate_table +'</td>';
                        tr_ += '</tr>';

                    if( !checkNone(id) || values.indexOf(String(id)) == -1 ){
                        $('#table-certificate tbody').append(tr_);
                        $('.repeater_audit_type_1').repeater();
                    }

                    setTimeout(function(){
                        $('#certificate_cerno_export').val('');
                        $('#certificate_issue_date').val('');
                        $('#certificate_expire_date').val('');
                        $('#certificate_accereditatio_no').val('');

                        $('#certificate_cerno_export').removeAttr("data-id");
                        $('#certificate_cerno_export').removeAttr("data-table");
                        $('#certificate_cerno_export').removeAttr("data-accereditatio_no");

                        $('#btn_std_export').val(1);
                        ShowInputCertificate();
                    }, 100);
                }
            });
        });
    </script>
@endpush
