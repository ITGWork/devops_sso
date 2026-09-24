<div class="modal fade bs-example-modal-lg" role="dialog" aria-labelledby="ScopeModalLabel" aria-hidden="true" id="ScopeModal" >
    <div class="modal-dialog modal-dialog-centere modal-lg" style="width: 1140px;max-width: 1140px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="ScopeModalLabel">เลือกรายการทดสอบที่ขอรับการแต่งตั้ง</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">

                        @php
                            $list_standard = App\Models\Basic\Tis::select('tb3_Tisno', 'tb3_TisThainame', 'tb3_TisAutono')->whereIn('status', ['-1', '0', '1', '2', '3'])->orderBy('tb3_Tisno')->get();

                            $option_standard = [];
                            foreach ($list_standard as $key => $item ) {
                                $option_standard[$item->tb3_TisAutono] = $item->tb3_Tisno.' : '.(strip_tags($item->tb3_TisThainame));
                            }
                        @endphp

                        <div class="form-group required">
                            {!! Form::label('modal_tis_id', 'มอก.', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-8">
                                {!! Form::select('modal_tis_id', $option_standard, null, ['class' => 'form-control', 'placeholder'=>'- เลือกมอก. -', 'id' => 'modal_tis_id']) !!}
                            </div>
                        </div>
                        <div class="form-group required">
                            {!! Form::label('modal_tis_name', 'ชื่อ มอก.', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-8">
                                {!! Form::text('modal_tis_name', null, ['class' => 'form-control', 'id' => 'modal_tis_name', 'disabled' => true]) !!}
                            </div>
                        </div>

                        <div class="form-group required">
                            {!! Form::label('modal_test_item', 'รายการทดสอบ', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-8">
                                {!! Form::select('modal_test_item', [], null, ['class' => 'form-control', 'placeholder'=>'- เลือกรายการทดสอบ -', 'id' => 'modal_test_item']) !!}
                            </div>
                        </div>

                        <div class="form-group required box_input_tools_select">
                            {!! Form::label('modal_test_tools', 'เครื่องมือที่ใช้', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::select('modal_test_tools', [], null, ['class' => 'form-control', 'placeholder'=>'- เลือกเครื่องมือที่ใช้ -', 'id' => 'modal_test_tools']) !!}
                                    <span class="input-group-btn">
                                        <button class="btn btn-success" type="button" id="modal_btn_test_tools_specify">ระบุเอง</button>
                                    </span>
                                </div>
                                <span class="text-danger"><i>(ระบุเครื่องมือที่ใช้ โดยไม่ต้องระบุ ยี่ห้อ/รุ่น)</i></span>
                            </div>
                        </div>

                        <div class="form-group required box_input_tools_txt">
                            {!! Form::label('modal_test_tools_txt', 'เครื่องมือที่ใช้', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('modal_test_tools_txt', null, ['class' => 'form-control', 'id' => 'modal_test_tools_txt']) !!}
                                    <span class="modal_test_tools_select">{!! Form::select('modal_test_tools_select', [], null, ['class' => 'form-control', 'id' => 'modal_test_tools_select']) !!}</span>
                                    <span class="input-group-btn">
                                        <button class="btn btn-info" type="button" id="modal_btn_test_tools_input" value="1">เลือก</button>
                                        <button class="btn btn-success" type="button" id="modal_btn_test_tools_add">เพิ่ม</button>
                                        <button class="btn btn-danger" type="button" id="modal_btn_test_tools_cancel">ยกเลิก</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            {!! Form::label('modal_test_tools_no', 'รหัส/หมายเลข', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-8">
                                {!! Form::text('modal_test_tools_no', null, ['class' => 'form-control', 'id' => 'modal_test_tools_no']) !!}
                            </div>
                        </div>

                        <div class="form-group required">
                            {!! Form::label('modal_capacity', 'ขีดความสามารถ', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-8">
                                {!! Form::text('modal_capacity', null, ['class' => 'form-control', 'id' => 'modal_capacity']) !!}
                            </div>
                        </div>

                        <div class="form-group required">
                            {!! Form::label('modal_range', 'ช่วงการใช้งาน', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-8">
                                {!! Form::text('modal_range', null, ['class' => 'form-control', 'id' => 'modal_range']) !!}
                            </div>
                        </div>

                        <div class="form-group required">
                            {!! Form::label('modal_true_value', 'ความละเอียดที่อ่านได้', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-8">
                                {!! Form::text('modal_true_value', null, ['class' => 'form-control', 'id' => 'modal_true_value']) !!}
                            </div>
                        </div>

                        <div class="form-group required">
                            {!! Form::label('modal_fault_value', 'ความคลาดเคลื่อนที่ยอมรับ', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-8">
                                {!! Form::text('modal_fault_value', null, ['class' => 'form-control', 'id' => 'modal_fault_value']) !!}
                            </div>
                        </div>

                        <div class="form-group required">
                            {!! Form::label('modal_test_duration', 'ระยะการทดสอบ(วัน)', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-8">
                                {!! Form::text('modal_test_duration', null, ['class' => 'form-control input_number', 'id' => 'modal_test_duration']) !!}
                            </div>
                        </div>

                        <div class="form-group required">
                            {!! Form::label('modal_test_price', 'ค่าใช้จ่ายในการทดสอบ/ชุดละ', ['class' => 'col-md-3 control-label']) !!}
                            <div class="col-md-8">
                                {!! Form::text('modal_test_price', null, ['class' => 'form-control input_number', 'id' => 'modal_test_price']) !!}
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-11">
                            <button type="button" class="btn btn-success waves-effect text-left pull-right" id="btn_get_tr">เพิ่ม</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-11">
                            <p class="text-danger">หมายเหตุ : เพิ่มข้อมูลในตารางรายการทดสอบภายใต้ มอก. เดียวกันเท่านั้น</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="myTableScopeCopy" data-toggle="table" >
                                <thead>
                                    <tr>
                                        <th align="top" width="2%" class="text-center">#</th>
                                        <th align="top" width="10%" class="text-center text-top">รายการทดสอบ</th>
                                        <th align="top" width="15%" class="text-center text-top">เครื่องมือที่ใช้</th>
                                        <th align="top" width="10%" class="text-center">รหัส/หมายเลข</th>
                                        <th align="top" width="15%" class="text-center">ขีดความสามารถ</th>
                                        <th align="top" width="10%" class="text-center">ช่วงการ<br>ใช้งาน</th>
                                        <th align="top" width="10%" class="text-center">ความละเอียดที่อ่านได้</th>
                                        <th align="top" width="10%" class="text-center">ความคลาดเคลื่อนที่ยอมรับ</th>
                                        <th align="top" width="10%" class="text-center">ระยะการทดสอบ(วัน)</th>
                                        <th align="top" width="10%" class="text-center">ค่าใช้จ่ายในการทดสอบ/ชุดละ</th>
                                        <th align="top" width="5%" class="text-center">ลบ</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                            <table class="table table-bordered table-sm" id="myTableScope" data-toggle="table" style="display: none" >
                                <thead>
                                    <tr>
                                        <th align="top" width="2%" class="text-center">#</th>
                                        <th align="top" width="10%" class="text-center text-top">รายการทดสอบ</th>
                                        <th align="top" width="15%" class="text-center text-top">เครื่องมือที่ใช้</th>
                                        <th align="top" width="10%" class="text-center">รหัส/หมายเลข</th>
                                        <th align="top" width="15%" class="text-center">ขีดความสามารถ</th>
                                        <th align="top" width="10%" class="text-center">ช่วงการ<br>ใช้งาน</th>
                                        <th align="top" width="10%" class="text-center">ความละเอียดที่อ่านได้</th>
                                        <th align="top" width="10%" class="text-center">ความคลาดเคลื่อนที่ยอมรับ</th>
                                        <th align="top" width="10%" class="text-center">ระยะการทดสอบ(วัน)</th>
                                        <th align="top" width="10%" class="text-center">ค่าใช้จ่ายในการทดสอบ/ชุดละ</th>
                                        <th align="top" width="5%" class="text-center">ลบ</th>
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
                <button type="button" class="btn btn-success waves-effect text-left" id="btn_gen_box">สร้าง</button>
                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    <!-- /.modal-content -->
    </div>
<!-- /.modal-dialog -->
</div>

@push('js')
    <script>

        var arr_tools = {};
        var arr_tst_item = {};
        $(document).ready(function () {

            $(".input_number").on("keypress",function(e){
                var eKey = e.which || e.keyCode;
                if((eKey<48 || eKey>57) && eKey!=46 && eKey!=44){
                    return false;
                }
            });

            $(".Mscope_number_only").on("keypress keyup blur",function (event) {
                $(this).val($(this).val().replace(/[^0-9\.]/g,''));
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });

            $('.box_input_tools_txt').hide();

            showInput();

            $('#modal_btn_test_tools_specify').click(function (e) { 
                var modal_test_item = $('#modal_test_item').val();
                if( modal_test_item != ''){
                    $('.box_input_tools_txt').show();
                    $('.box_input_tools_select').hide();
                }else{
                    alert('กรุณาเลือกรายการทดสอบ');
                }
            });

            $('#modal_btn_test_tools_cancel').click(function (e) { 
                $('.box_input_tools_txt').hide();
                $('.box_input_tools_select').show();
            });

            $('#modal_btn_test_tools_add').click(function (e) { 

                var btn = $('#modal_btn_test_tools_input').val();
            
                if( btn == 1 ){
                    var txtr = $('#modal_test_tools_txt').val();
                }else{
                    var txtr = $('#modal_test_tools_select').val();
                }

                if( !empty(txtr) ){
                    SaveTestTools();
                }else{  
                    alert('กรุณากรอกเครื่องมือที่ใช้ ?');
                }
            });

            $('#modal_btn_test_tools_input').click(function (e) { 
                
                var val = $(this).val();

                if( val == 1 ){
                    $('#modal_btn_test_tools_input').val(2);
                    $('#modal_btn_test_tools_input').text('ระบุ');
                }else{
                    $('#modal_btn_test_tools_input').val(1);
                    $('#modal_btn_test_tools_input').text('เลือก');
                }

                showInput();
            });

            $("#modal_tis_id").on('change', function () {
                var val = $(this).val();

                $('#modal_test_item').html('<option value=""> -เลือกรายการทดสอบ- </option>');

                $('#modal_test_tools').html('<option value=""> -เลือกเครื่องมือที่ใช้- </option>');

                $('#modal_tis_name').val('');

                $('#modal_test_item').val('').trigger('change').select2();
                $('#modal_test_tools').val('').trigger('change').select2();

                $('#modal_test_tools_no').val('');
                $('#modal_capacity').val('');
                $('#modal_range').val('');
                $('#modal_true_value').val('');
                $('#modal_fault_value').val('');

                $('#modal_test_duration').val('');
                $('#modal_test_price').val('');

                if(  val != '' && $.isNumeric(val) ){

                    $.ajax({
                        url: "{!! url('/request-section-5/application-lab/get-tis_name') !!}" + "/" + val
                    }).done(function( object ) {
                        $('#modal_tis_name').val( object.tb3_TisThainame );
                    });


                    $.ajax({
                        url: "{!! url('/request-section-5/application-lab/get-test-item') !!}" + "/" + val
                    }).done(function( object ) {

                        if( object.length > 0){
                            $.each(object, function( index, data ) {
                                $('#modal_test_item').append('<option value="'+data.id+'">'+data.title+'</option>');
                                arr_tst_item[ data.id ] = normalizeTestItemText(data.title);

                            });
                        }

                    });

                }
                data_list_disabled();
            });

            $("#modal_tis_id").change();

            $("#modal_test_item").on('change', function () {
                var val = $(this).val();

                $('#modal_test_tools').html('<option value=""> -เลือกเครื่องมือที่ใช้- </option>');

                if( val != ''){
                    
                    $.ajax({
                        url: "{!! url('/request-section-5/application-lab/get-test-tools') !!}" + "/" + val
                    }).done(function( object ) {

                        if( object.length > 0){
                            $.each(object, function( index, data ) {
                                $('#modal_test_tools').append('<option value="'+data.id+'">'+data.title+'</option>');
                                arr_tools[ data.id ] = data.title;

                            });
                        }

                    });

                }

            });

            $('#btn_get_tr').click(function (e) { 
    
                var tis_id = $('#modal_tis_id').val();
                var tis_name =  $('#modal_tis_name').val();
                var tis_num = $('#modal_tis_id').find('option:selected').text();

                var test_item = $('#modal_test_item').val();
                var test_item_txt = normalizeTestItemText($('#modal_test_item').find('option:selected').text());

                var test_tools = $('#modal_test_tools').val();
                var test_tools_txt = $('#modal_test_tools').find('option:selected').text();

               var test_tools_no = $('#modal_test_tools_no').val();
                var capacity = $('#modal_capacity').val();
                var range = $('#modal_range').val();
                var true_value = $('#modal_true_value').val();
                var fault_value = $('#modal_fault_value').val();

                var test_duration = $('#modal_test_duration').val();
                var test_price = $('#modal_test_price').val();

                var explode_tis_num = tis_num.split(':');

                if( tis_id == '' ){
                    alert('กรุณากรอก มอก.');
                }else if( test_item == '' ){
                    alert('กรุณากรอก รายการทดสอบ');
                }else if( test_tools == '' ){
                    alert('กรุณากรอก เครื่องมือที่ใช้');
              //  }else if( test_tools_no == '' ){
              //      alert('กรุณากรอก รหัส/หมายเลข');
                }else if( capacity == '' ){
                    alert('กรุณากรอก ขีดความสามารถ');
                }else if( capacity == '' ){
                    alert('กรุณากรอก ช่วงการใช้งาน');
                }else if( true_value == '' ){
                    alert('กรุณากรอก ความละเอียดที่อ่านได้');
                }else if( fault_value == '' ){
                    alert('กรุณากรอก ความคลาดเคลื่อนที่ยอมรับ');
                }else if( test_duration == '' ){
                    alert('กรุณากรอก ระยะการทดสอบ');
                }else if( test_price == '' ){
                    alert('กรุณากรอก ค่าใช้จ่ายในการทดสอบ');
                }else{

                    arr_tools[ test_tools ] = test_tools_txt;
                    arr_tst_item[ test_item ] = test_item_txt;

                    var id_row_tr = Math.floor(Math.random() * 26) + Date.now();

                    var LastRow = $('#myTableScope tbody').length;

                    var inputSTD = '<input type="hidden" class="myTableScope_tis_id" name="tis_id" value="'+(tis_id)+'"><input type="hidden" class="Mscope_tis_tisno" name="tis_tisno" value="'+($.trim(explode_tis_num[0]))+'">';
                    var idRows = '<input type="hidden" class="Mscope_id" name="scope_id" value="">';

                    var inputHidden = inputSTD;
                        inputHidden += idRows;
                        inputHidden += '<input type="hidden" class="Mscope_test_item_id" name="test_item_id" value="'+(test_item)+'">';
                        inputHidden += '<input type="hidden" class="Mscope_test_tools_id" name="test_tools_id" value="'+(test_tools)+'">';
                        inputHidden += '<input type="hidden" class="Mscope_test_tools_no" name="test_tools_no" value="'+(test_tools_no)+'">';
                        inputHidden += '<input type="hidden" class="Mscope_capacity" name="capacity" value="'+(capacity)+'">';
                        inputHidden += '<input type="hidden" class="Mscope_range" name="range" value="'+(range)+'">';
                        inputHidden += '<input type="hidden" class="Mscope_true_value" name="true_value" value="'+(true_value)+'">';
                        inputHidden += '<input type="hidden" class="Mscope_fault_value" name="fault_value" value="'+(fault_value)+'">';
                        inputHidden += '<input type="hidden" class="Mscope_test_duration" name="test_duration" value="'+(test_duration)+'">';
                        inputHidden += '<input type="hidden" class="Mscope_test_price" name="test_price" value="'+(test_price)+'">';

                    var _tr = '';
                        _tr += '<tr class="row_tr_'+(id_row_tr)+'">';
                        _tr += '<td class="text-center text-top"><span class="Modalno_'+(test_item)+'"></span></td>';
                        _tr += '<td class="text-center text-top">'+(test_item_txt)+'</td>';
                        _tr += '<td class="text-center text-top">'+(test_tools_txt)+'</td>';
                        _tr += '<td class="text-center text-top">'+(test_tools_no)+'</td>';
                        _tr += '<td class="text-center text-top">'+(capacity)+'</td>';
                        _tr += '<td class="text-center text-top">'+(range)+'</td>';
                        _tr += '<td class="text-center text-top">'+(true_value)+'</td>';
                        _tr += '<td class="text-center text-top">'+(fault_value)+'</td>';
                        _tr += '<td class="text-center text-top">'+(test_duration)+'</td>';
                        _tr += '<td class="text-center text-top">'+(test_price)+'</td>';
                        _tr += '<td class="text-center text-top"><button type="button" class="btn btn-danger btn-sm btn_remove_modalscope" data-tr="'+(id_row_tr)+'">ลบ</button>'+inputHidden+'</td>';
                        _tr += '</tr>';

                    var table = $('#myTableScope tbody');
                    var addRowsCheck = true;
                    if( table.find('.myTableScope_tis_id').length > 0 ){
                        $('.myTableScope_tis_id').each(function(index, element){
                            if( $(element).val() != tis_id ){
                                addRowsCheck = false;
                            }
                        });
                    }

                    if( addRowsCheck == true ){

                        if( table.find('.Mscope_test_item_id').length > 0 ){

                            var values_test_item = $('#myTableScope').find(".Mscope_test_item_id").map(function(){return $(this).val(); }).get();
                                values_test_item = jQuery.unique( values_test_item );

                            if(values_test_item.indexOf( test_item.toString() ) != -1){

                                var last_input = $('#myTableScope').find('.Mscope_test_item_id[value="'+ test_item +'"]').last().parent().parent();
                                var _tr = '';
                                    _tr += '<tr class="row_tr_'+(id_row_tr)+'">';
                                    _tr += '<td class="text-center text-top"><span class="Modalno_'+(test_item)+'"></span></td>';
                                    _tr += '<td class="text-center text-top">'+(test_item_txt)+'</td>';
                                    _tr += '<td class="text-center text-top">'+(test_tools_txt)+'</td>';
                                    _tr += '<td class="text-center text-top">'+(test_tools_no)+'</td>';
                                    _tr += '<td class="text-center text-top">'+(capacity)+'</td>';
                                    _tr += '<td class="text-center text-top">'+(range)+'</td>';
                                    _tr += '<td class="text-center text-top">'+(true_value)+'</td>';
                                    _tr += '<td class="text-center text-top">'+(fault_value)+'</td>';
                                    _tr += '<td class="text-center text-top">'+(test_duration)+'</td>';
                                    _tr += '<td class="text-center text-top">'+(test_price)+'</td>';
                                    _tr += '<td class="text-center text-top"><button type="button" class="btn btn-danger btn-sm btn_remove_modalscope"  data-tr="'+(id_row_tr)+'">ลบ</button>'+inputHidden+'</td>';
                                    _tr += '</tr>';

                                last_input.closest('tr').after(_tr);

                            }else{
                                $('#myTableScope tbody').append(_tr);
                            }

                        }else{
                            $('#myTableScope tbody').append(_tr);
                        }

                        
                        resetOrderNo();

                        CloneTableScope();

                        $('#modal_test_item').val('').select2();
                        $('#modal_test_tools').val('').select2();

                        $('#modal_test_tools_no').val('');
                        $('#modal_capacity').val('');
                        $('#modal_range').val('');
                        $('#modal_true_value').val('');
                        $('#modal_fault_value').val('');

                        $('#modal_test_duration').val('');
                        $('#modal_test_price').val('');

                    }else{
                        alert('กรุณาเลือกมอก. ให้ตรงกัน');
                    }

                    // data_test_item_list_disabled();
                }

            });

            $("body").on('click', '.btn_remove_modalscope', function () {
                if(confirm('ยืนยันการลบข้อมูล แถวนี้')){

                    $('#myTableScope tbody').find('.row_tr_'+ $(this).data('tr') ).remove();

                    resetOrderNo();

                    CloneTableScope();
                }
            });
            

            $('#btn_gen_box').click(function (e) { 

                if( $('#myTableScope tbody tr').length > 0 ){

                    var length =  $('body').find('.table_multiples').length;

                    var tis_num = $('#modal_tis_id').find('option:selected').text();
                    var tis_id = $('#modal_tis_id').val();
                    // var tis_name =  $('#modal_tis_name').val();

                    var html = "";
                        html += '<div class="row white-box repeater-table-scope">';
                        html += '<div class="col-md-12">';
                        html += '<div class="row">'
                        html += '<h5 class="pull-left">รายการทดสอบ ตามมาตรฐานเลขที่ มอก. '+(tis_num)+' </h5>';
                        html += '<div class="pull-right">';
                        html += '<button class="btn btn-warning btn_section_edit" data-tis_id="'+(tis_id)+'" data-table="table-group-'+( tis_id )+'" type="button">แก้ไข</button>';
                        html += ' ';
                        html += '<button class="btn btn-danger btn_section_remove" type="button">ลบชุดรายการทดสอบ</button>';
                        html += '</div>'; 
                        html += '</div>'; 
                        html += '<input type="hidden" name="section_box_tis[]" value="'+ tis_id +'" class="form-control section_box_tis">';
                        html += '<hr>';
                        html += '<div class="clearfix"></div>';
                        html += '<div class="table-responsive">';
                        html += '<table class="table table-bordered table_multiples inner-repeater" id="table-group-'+( tis_id )+'">';
                        html += '<thead>';
                        html += '<tr>';
                        html += '<th width="2%" class="text-center">#</th>';
                        html += '<th width="10%" class="text-center">รายการทดสอบ</th>';
                        html += '<th width="15%" class="text-center">เครื่องมือที่ใช้</th>';
                        html += '<th width="10%" class="text-center">รหัส/หมายเลข</th>';
                        html += '<th width="15%" class="text-center">ขีดความสามารถ</th>';
                        html += '<th width="10%" class="text-center">ช่วงการใช้งาน</th>';
                        html += '<th width="15%" class="text-center">ความละเอียดที่อ่านได้</th>';
                        html += '<th width="10%" class="text-center">ความคลาดเคลื่อนที่ยอมรับ</th>';
                        html += '<th width="10%" class="text-center">ระยะการทดสอบ(วัน)</th>';
                        html += '<th width="10%" class="text-center">ค่าใช้จ่ายในการทดสอบ/ชุดละ</th>';
                        html += '</tr>';
                        html += '</thead>';
                        html += '<tbody data-repeater-list="repeater-group-'+( tis_id )+'">';

                        var i = 0;
                        var _tr = '';
                        $('#myTableScope').find('.myTableScope_tis_id').each(function (index, rowId) {
                            i++;
                            var row = $(rowId).parent().parent();

                            var NumBerRow = row.children("td:nth-child(0)").text();

                            var tis_tisno = row.find('.Mscope_tis_tisno').val();
                            var test_item_id = row.find('.Mscope_test_item_id').val();
                            var test_tools_id = row.find('.Mscope_test_tools_id').val();
                            var test_tools_no = row.find('.Mscope_test_tools_no').val();
                            var capacity = row.find('.Mscope_capacity').val();
                            var range = row.find('.Mscope_range').val();
                            var true_value = row.find('.Mscope_true_value').val();
                            var fault_value = row.find('.Mscope_fault_value').val();

                            var test_duration = row.find('.Mscope_test_duration').val();
                            var test_price = row.find('.Mscope_test_price').val();

                            var test_item_txt = normalizeTestItemText(arr_tst_item[test_item_id]);
                            var test_tools_txt = arr_tools[test_tools_id];
                            var scope_id = row.find('.Mscope_id').val();

                            var GenInput =  '<input type="hidden" class="scope_tis_id" name="tis_id" value="'+(rowId.value)+'"><input type="hidden" class="scope_tis_tisno" name="tis_tisno" value="'+(tis_tisno)+'">';
                                GenInput += '<input type="hidden" class="scope_id" name="scope_id" value="'+(scope_id)+'">';
                                GenInput += '<input type="hidden" class="scope_test_item_id" name="test_item_id" value="'+(test_item_id)+'">';
                                GenInput += '<input type="hidden" class="scope_test_tools_id" name="test_tools_id" value="'+(test_tools_id)+'">';
                                GenInput += '<input type="hidden" class="scope_test_tools_no" name="test_tools_no" value="'+(test_tools_no)+'">';
                                GenInput += '<input type="hidden" class="scope_capacity" name="capacity" value="'+(capacity)+'">';
                                GenInput += '<input type="hidden" class="scope_range" name="range" value="'+(range)+'">'; 
                                GenInput += '<input type="hidden" class="scope_true_value" name="true_value" value="'+(true_value)+'">';
                                GenInput += '<input type="hidden" class="scope_fault_value" name="fault_value" value="'+(fault_value)+'">';
                                GenInput += '<input type="hidden" class="scope_test_duration" name="test_duration" value="'+(test_duration)+'">';
                                GenInput += '<input type="hidden" class="scope_test_price" name="test_price" value="'+(test_price)+'">';

                            _tr += '<tr data-repeater-item>';
                            _tr += '<td class="text-center text-top"><span class="Tscope_number-'+(test_item_id)+'"></span>'+(NumBerRow)+'</td>';
                            _tr += '<td class="text-center text-top">'+(test_item_txt)+'</td>';
                            _tr += '<td class="text-center text-top">'+(test_tools_txt)+'</td>';
                            _tr += '<td class="text-center text-top">'+(test_tools_no)+'</td>';
                            _tr += '<td class="text-center text-top">'+(capacity)+'</td>';
                            _tr += '<td class="text-center text-top">'+(range)+'</td>';
                            _tr += '<td class="text-center text-top">'+(true_value)+'</td>';
                            _tr += '<td class="text-center text-top">'+(fault_value)+'</td>';
                            _tr += '<td class="text-center text-top">'+(test_duration)+'</td>';
                            _tr += '<td class="text-center text-top">'+(test_price)+''+(GenInput)+'</td>';
                            _tr += '</tr>';

                        });
                        html += _tr;
                        html += '</tbody>';
                        html += '</table>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>'; 
     
                    var values = $('.section_box_tis').map(function(){return $(this).val(); }).get();

                    if ( length > 0 ) {;

                        if( $('#table-group-'+( tis_id )+' tbody').length == 0){
                            //.ให้เพิ่มแค่ มอก เดียว
                            $('#box_scope_request').html('');
                            $('#box_scope_request').append(html);
                        }else{
                            //.ให้เพิ่มแค่ มอก เดียว
                            $('#box_scope_request').html('');
                            $('#box_scope_request').append(html);
                            // $('#table-group-'+( tis_id )+' tbody').html('');
                            // $('#table-group-'+( tis_id )+' tbody').append(_tr);
                            NumberTableMScope( tis_id );
                        }

                        $('#myTableScope tbody').html('');
                        $('#modal_tis_id').val('').trigger('change').select2();
              
                    }else{ 

                        //.ให้เพิ่มแค่ มอก เดียว
                        $('#box_scope_request').html('');
                        $('#box_scope_request').append(html);

                        $('#myTableScope tbody').html('');
                        $('#modal_tis_id').val('').trigger('change').select2();
                    }
                    $('#ScopeModal').modal('hide');
                    merge_table_box_scope();
                    $('.repeater-table-scope').repeater();     
                }else{
                    alert('กรุณาเพิ่มข้อมูลในตาราง รายการทดสอบ');
                }
   
            });
            
            data_list_disabled();
        });

        function data_list_disabled(){
            $('#modal_tis_id').children('option').prop('disabled',false);
            $('.section_box_tis').each(function(index , item){
                var data_list = $(item).val();
                $('#modal_tis_id').children('option[value="'+data_list+'"]:not(:selected):not([value=""])').prop('disabled',true);
            });
        }

        function data_test_item_list_disabled(){
            $('#modal_test_item').children('option').prop('disabled',false);
            $('.Mscope_test_item_id').each(function(index , item){
                var data_list = $(item).val();
                $('#modal_test_item').children('option[value="'+data_list+'"]:not(:selected):not([value=""])').prop('disabled',true);
            });
        }

        function normalizeTestItemText(text){
            var value = $.trim(text || '');
            var startAt = value.indexOf('ข้อ');

            if(startAt > 0){
                var prefix = value.substring(0, startAt);
                if(/^[^A-Za-z0-9ก-๙]+$/u.test(prefix)){
                    return $.trim(value.substring(startAt));
                }
            }

            return value;
        }

        function resetOrderNo(){

            var values_test_item = $('#myTableScope').find(".Mscope_test_item_id").map(function(){return $(this).val(); }).get();
                values_test_item = jQuery.unique( values_test_item );

            var i = 0;
            $.each(values_test_item , function( index, item ) {
                i++;
                $('#myTableScope').find('span.Modalno_'+ item +'').each(function(index, el) {
                    $(el).text( i );
                });
            });

        }

        function NumberTableMScope(tis_id){
            var values_test_item = $( '#table-group-'+( tis_id ) ).find(".scope_test_item_id").map(function(){return $(this).val(); }).get();
                values_test_item = jQuery.unique( values_test_item );

            var i = 0;
            $.each(values_test_item , function( index, item ) {
                i++;
                $( '#table-group-'+( tis_id ) ).find('span.Tscope_number-'+ item +'').each(function(index, el) {
                    $(el).text( i );
                });
            });
        }

        function SaveTestTools(){

            var test_item = $('#modal_test_item').val();
            var test_tool = $('#modal_test_tools_txt').val();
            var test_tool_id = $('#modal_test_tools_select').val();

            var btn = $('#modal_btn_test_tools_input').val();

            $.ajax({
                method: "POST",
                url: "{{ url('/request-section-5/application-lab/save_test_tools') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "test_item": test_item,
                    "test_tool": test_tool,
                    "test_tool_id": test_tool_id,
                    "type": btn
                },
                success : function (data){
                    if (data.mgs == "success") {

                        $.toast({
                            heading: 'Compleate!',
                            text: 'บันทึกสำเร็จ',
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'success',
                            hideAfter: 1000,
                            stack: 6,
                        });

                        LoadItemTools( data );

                        $('#modal_test_tools_txt').val('');

                        $('.box_input_tools_txt').hide();
                        $('.box_input_tools_select').show();
                
                    }else{

                        $.toast({
                            heading: 'Compleate!',
                            text: 'บันทึกไม่สำเร็จ',
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'error',
                            hideAfter: 1000,
                            stack: 6,

                        });

                    }
                }
            });
        }

        function LoadItemTools( data ){
            
            $('#modal_test_tools').html('<option value=""> -เลือกเครื่องมือที่ใช้- </option>');
            var val  = $('#modal_test_item').val();
            if(  val != '' && $.isNumeric(val) ){

                $.LoadingOverlay("show", {
                    image       : "",
                    text        : "Loading..."
                });

                $.ajax({
                    url: "{!! url('/request-section-5/application-lab/get-test-tools') !!}" + "/" + val
                }).done(function( object ) {

                    if( object.length > 0){
                        $.each(object, function( index, data ) {
                            $('#modal_test_tools').append('<option value="'+data.id+'">'+data.title+'</option>');
                        });

                        $('#modal_test_tools').val( data.tools_id).trigger('change.select2');
                        
                        $.LoadingOverlay("hide", true);  
                    }else{
                        $.LoadingOverlay("hide", true);    
                    }

                });

            } 
        }

        function showInput(){

            var btn =   $('#modal_btn_test_tools_input').val();

            $('#modal_test_tools_select').html('<option value=""> -เลือกเครื่องมือที่ใช้- </option>');

            $('#modal_test_tools_txt').val('');
            $('#modal_test_tools_select').val('').trigger('change.select2');

            if( btn ==  2 ){
                $('#modal_test_tools_txt').hide();
                $('.modal_test_tools_select').show();
                LoadToolsBasic();
            }else{
                $('#modal_test_tools_txt').show();
                $('.modal_test_tools_select').hide();
            }

        }

        function LoadToolsBasic(){
            
            var val  = $('#modal_test_item').val();

            $.LoadingOverlay("show", {
                image       : "",
                text        : "Loading..."
            });

            $.ajax({
                url: "{!! url('/request-section-5/application-lab/get-basic-tools') !!}" + "/" + val
            }).done(function( object ) {

                if( object.length > 0){
                    $.each(object, function( index, data ) {
                        $('#modal_test_tools_select').append('<option value="'+data.id+'">'+data.title+'</option>');
                    });
                    $.LoadingOverlay("hide", true);  
                }else{
                    $.LoadingOverlay("hide", true);    
                }

            });

        }

        function merge_table_modal_scope(){
            const table = document.querySelector('#myTableScopeCopy'); //อยู่ใน form.php

            //Col 1
            let headerCell = null;
            for (let row of table.rows) {
                const Cell1 = row.cells[0];
                const Cell2 = row.cells[1];

                if (headerCell === null || Cell1.innerText !== headerCell.innerText) {
                    headerCell = Cell1;
                    header2Cell = Cell2;

                } else {
                    headerCell.rowSpan++;
                    header2Cell.rowSpan++;
                    Cell1.remove();//ลบคอลัมภ์แรก
                    Cell2.remove();//ลบคอลัมภ์สอง
                }
            }
        }

        function CloneTableScope(){

            resetOrderNo();

            $('#myTableScopeCopy tbody').html('');
            
            var Maintbody = $('#myTableScope tbody').clone();

            $('#myTableScopeCopy tbody').append( Maintbody.html() );

            merge_table_modal_scope();

        }
    </script>
@endpush


@push('js')
<script>
// ============================================================
// New Scope UI — Accordion + Nested table พร้อม checkbox
// ============================================================

$(document).ready(function () {

    // inject CSS
    $('<style id="scope-ui-css">').text([
        '.scope-panel .panel-heading { background: #f0f4ff; border-color: #c5d0e6; padding: 10px 15px; }',
        '.scope-panel .panel-title a { font-size: 15px; font-weight: bold; color: #333; text-decoration: none; }',
        '.scope-panel .panel-title a:hover { color: #009efb; }',
        '.scope-selected-badge { font-size: 12px; margin-left: 8px; background: #009efb; vertical-align: middle; }',
        '.scope-selected-badge.none { background: #aaa; }',
        '.tool-chk-group { padding: 2px 0; }',
        '.chk-tool-lbl { font-weight: normal; font-size: 13px; cursor: pointer; margin: 0 0 3px 0; display: block; }',
        '.chk-tool-lbl input { margin-right: 5px; cursor: pointer; }',
        '.scope-row-disabled td { background: #f8f8f8 !important; opacity: 0.55; }',
        '.scope-row-disabled .tool-chk-group { pointer-events: none; }',
        '.details-row > td { padding: 0 !important; border-top: none !important; }',
        '.details-row .inner-detail-table { margin: 0; border-left: 4px solid #009efb; }',
        '.details-row .inner-detail-table th { background: #eef2f5; font-size: 12px; padding: 5px 8px; white-space: nowrap; }',
        '.details-row .inner-detail-table td { padding: 4px 6px; vertical-align: middle; }',
        '#box_scope_request .panel { margin-bottom: 10px; }',
    ].join('\n')).appendTo('head');

    // Select2 สำหรับ dropdown มอก.
    if ($('#scope_tis_selector').length) {
        $('#scope_tis_selector').select2({ width: '100%', placeholder: '- เลือก มอก. -', allowClear: true });
    }

    // ── เตือนทันทีถ้าเลือก มอก. แล้วแต่ยังไม่ได้กดปุ่ม "เพิ่ม" ──
    $('#scope_tis_selector').on('change', function () {
        if ($(this).val()) {
            $('#tis_pending_warning').show();
            $('#btn_load_scope_items').addClass('btn-danger').removeClass('btn-primary');
        } else {
            $('#tis_pending_warning').hide();
            $('#btn_load_scope_items').addClass('btn-primary').removeClass('btn-danger');
        }
    });

    // ── ปุ่มโหลด ──
    $('#btn_load_scope_items').on('click', function () {
        var tis_id = $('#scope_tis_selector').val();
        if (!tis_id) { alert('กรุณาเลือก มอก. ก่อน'); return; }
        if ($('#panel-scope-' + tis_id).length) { alert('มอก. นี้ถูกโหลดแล้ว'); return; }
        if ($('#box_scope_request .scope-panel').not('#panel-scope-' + tis_id).length) {
            alert('สามารถเลือก มอก. ได้เพียงรายการเดียว กรุณาลบ มอก. เดิมออกก่อน หากต้องการเปลี่ยน');
            return;
        }
        var opt = $('#scope_tis_selector').find('option:selected');
        loadScopeByTis(tis_id, opt.data('tisno') || '', opt.data('tisname') || '', null);
        $('#scope_tis_selector').val('').trigger('change');
    });

    // ── scope_checkbox toggle (เลือก/ยกเลิก test item) ──
    $('body').on('change', '.scope_checkbox', function () {
        var $row    = $(this).closest('.main-item-row');
        var item_id = $(this).data('item-id');
        var tis_id  = $(this).data('tis-id');
        var isExempt = $row.data('exempt') == '1';

        if (isExempt) {
            // ตรวจพินิจ — show/hide row และ enable/disable inputs
            var $exemptRow = $(this).closest('tbody').find('.exempt-method-row[data-item-id="' + item_id + '"]');
            if (this.checked) {
                $row.removeClass('scope-row-disabled');
                $exemptRow.show().find('.scope_field').prop('disabled', false);
            } else {
                $row.addClass('scope-row-disabled');
                $exemptRow.hide().find('.scope_field').prop('disabled', true);
            }
        } else {
            if (this.checked) {
                $row.removeClass('scope-row-disabled');
                $row.find('.tool_chk').prop('disabled', false);
                // auto-check เครื่องมือตัวแรกถ้ายังไม่มีตัวไหนถูกเลือกเลย (checkbox เลือกได้หลายตัว)
                if ($row.find('.tool_chk:checked').length === 0) {
                    $row.find('.tool_chk').first().prop('checked', true);
                }
                syncToolSelectionForItem(tis_id, item_id);
            } else {
                $row.addClass('scope-row-disabled');
                $row.find('.tool_chk').prop('checked', false).prop('disabled', true);
                syncToolSelectionForItem(tis_id, item_id);
            }
        }
        updateScopeBadge(tis_id);
    });

    // ── tool_chk toggle (เลือก/ยกเลิก เครื่องมือ — checkbox: เลือกได้หลายตัวต่อรายการทดสอบ) ──
    $('body').on('change', '.tool_chk', function () {
        var item_id = $(this).data('item-id');
        var tis_id  = $(this).closest('table').attr('id').replace('table-group-', '');
        syncToolSelectionForItem(tis_id, item_id);
    });

    // ── ลบเครื่องมือออกจาก list ──
    $('body').on('click', '.btn-remove-tool', function (e) {
        e.stopPropagation();
        if (!confirm('ยืนยันการลบเครื่องมือนี้?')) return;
        var tool_id = $(this).data('tool-id');
        var item_id = $(this).data('item-id');
        var tis_id  = $(this).data('tis-id');
        var $panel  = $('#panel-scope-' + tis_id);

        // ลบ detail row ออกจากตาราง inner
        var $detailsRow = $panel.find('.details-row[data-item-id="' + item_id + '"]');
        $detailsRow.find('.tool-detail-row[data-tool-id="' + tool_id + '"]').remove();

        // ลบ label ออกจาก tool list
        $(this).closest('.chk-tool-lbl').remove();

        // sync สถานะใหม่ (เผื่อเครื่องมือที่เพิ่งลบเป็นตัวที่ถูกเลือกอยู่)
        syncToolSelectionForItem(tis_id, item_id);

        updateScopeBadge(tis_id);
    });

    // ── ลบ panel มอก. ──
    $('body').on('click', '.btn_section_remove', function () {
        if (!confirm('ยืนยันการลบชุดรายการทดสอบ มอก. นี้?')) return;
        var $panel = $(this).closest('.scope-panel');
        var tis_id = $panel.data('tis-id');
        $panel.remove();
        // คืน option ใน dropdown
        $('#scope_tis_selector').find('option[value="' + tis_id + '"]').prop('disabled', false).trigger('change.select2');
        data_list_disabled();
        BtnRemoveSection();
    });

    // ── auto-load (edit mode) ──
    if (typeof _init_scopes !== 'undefined' && !$.isEmptyObject(_init_scopes)) {
        $.each(_init_scopes, function (tis_id, data) {
            loadScopeByTis(tis_id, data.tisno, data.tisname, data.scopes);
        });
    }
});

// ─────────────────────────────────────────────────────────────
// sync สถานะ show/hide/disabled/required ของแถวรายละเอียดเครื่องมือ ให้ตรงกับ checkbox ที่ถูกเลือกอยู่จริง
// (เลือกได้หลายตัวต่อรายการทดสอบ)
function syncToolSelectionForItem(tis_id, item_id) {
    var $panel      = $('#panel-scope-' + tis_id);
    var $mainRow    = $panel.find('.main-item-row[data-item-id="' + item_id + '"]');
    var $detailsRow = $panel.find('.details-row[data-item-id="' + item_id + '"]');
    var anyToolChecked = false;

    $mainRow.find('.tool_chk[data-item-id="' + item_id + '"]').each(function () {
        var tool_id  = $(this).data('tool-id');
        var $toolRow = $detailsRow.find('.tool-detail-row[data-tool-id="' + tool_id + '"]');

        if (this.checked) {
            anyToolChecked = true;
            $toolRow.show().find('.scope_field').prop('disabled', false);
            var isExemptTool = (String(tool_id) === '4');
            if (!isExemptTool) {
                $toolRow.find('input.scope_field:not([type=hidden])[name!="test_tools_no"]').prop('required', true);
            }
        } else {
            $toolRow.hide();
            $toolRow.find('.scope_field').prop('disabled', true).prop('required', false);
            $toolRow.find('input.scope_field:not([type=hidden]):not([readonly])').val('');
        }
    });

    anyToolChecked ? $detailsRow.show() : $detailsRow.hide();

    // ถ้าไม่เหลือเครื่องมือที่เลือกเลย แต่รายการทดสอบยังติ๊กอยู่ ให้ยกเลิกรายการทดสอบนี้ไปด้วย
    if (!anyToolChecked && $mainRow.find('.scope_checkbox').is(':checked')) {
        alert('ต้องเลือกเครื่องมืออย่างน้อย 1 รายการ ระบบจึงยกเลิกการเลือกรายการทดสอบนี้ให้อัตโนมัติ');
        $mainRow.find('.scope_checkbox').prop('checked', false).trigger('change');
    }
}

// ─────────────────────────────────────────────────────────────
function updateScopeBadge(tis_id) {
    var $panel    = $('#panel-scope-' + tis_id);
    var total     = $panel.find('.scope_checkbox').length;
    var selected  = $panel.find('.scope_checkbox:checked').length;
    var $badge    = $panel.find('.scope-selected-badge');
    $badge.text(selected + '/' + total + ' เลือกแล้ว');
    selected > 0 ? $badge.removeClass('none') : $badge.addClass('none');
}

// ─────────────────────────────────────────────────────────────
function loadScopeByTis(tis_id, tisno, tisname, existingScopes) {
    $.LoadingOverlay('show', { image: '', text: 'Loading...' });
    $.ajax({
        url: "{!! url('/request-section-5/application-lab/get-items-with-tools') !!}" + '/' + tis_id
    }).done(function (items) {
        $.LoadingOverlay('hide', true);
        if (!items || items.length === 0) { alert('ไม่พบรายการทดสอบสำหรับ มอก. นี้'); return; }
        renderScopeBlock(tis_id, tisno, tisname, items, existingScopes);
        data_list_disabled();
        BtnRemoveSection();
    }).fail(function () {
        $.LoadingOverlay('hide', true);
        alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
    });
}

// ─────────────────────────────────────────────────────────────
function renderScopeBlock(tis_id, tisno, tisname, items, existingScopes) {

    // จัดกลุ่ม existingScopes ตาม test_item_id
    var existingByItem = {};
    if (existingScopes && existingScopes.length > 0) {
        existingScopes.forEach(function (s) {
            if (!existingByItem[s.test_item_id]) existingByItem[s.test_item_id] = [];
            existingByItem[s.test_item_id].push(s);
        });
    }

    var collapseIn   = 'in';
    var chevron      = 'fa-chevron-up';
    var totalItems   = items.length;
    var selectedItems = Object.keys(existingByItem).length;

    // ── Accordion Panel ──
    var html  = '<div class="panel panel-default scope-panel repeater-table-scope" id="panel-scope-' + tis_id + '" data-tis-id="' + tis_id + '" data-tisno="' + tisno + '">';

    // Panel Header
    // ใช้ flexbox แทน .col-xs-8/.col-xs-4 เพราะ layout เดิมเคยเจอปัญหาปุ่ม "ลบ มอก. นี้" หายไปจากจอ
    // (พึ่ง Bootstrap grid class ที่อาจถูก override/ไม่ทำงานตามที่คาดในบางหน้า) flexbox นี้ไม่พึ่ง
    // grid framework เลย รับประกันว่าปุ่มจะอยู่ชิดขวาเสมอและไม่หายไป
    html += '<div class="panel-heading" role="tab" id="heading-scope-' + tis_id + '">';
    html += '<div style="display:flex; align-items:center; justify-content:space-between; gap:10px;">';
    html += '<h5 class="panel-title" style="margin:0; line-height:30px; flex:1 1 auto; min-width:0;">';
    html += '<a data-toggle="collapse" href="#collapse-scope-' + tis_id + '" style="display:inline;">';
    html += '<i class="fa ' + chevron + ' m-r-5"></i>';
    html += 'มอก. ' + tisno + ' — ' + tisname;
    html += '</a>';
    html += '<span class="badge scope-selected-badge ' + (selectedItems === 0 ? 'none' : '') + '" style="margin-left:8px;">';
    html +=   selectedItems + '/' + totalItems + ' เลือกแล้ว';
    html += '</span>';
    html += '</h5>';
    html += '<div style="flex:0 0 auto; white-space:nowrap;">';
    html += '<button class="btn btn-danger btn-sm btn_section_remove" type="button"><i class="fa fa-trash"></i> ลบ มอก. นี้</button>';
    html += '</div></div></div>';

    // Panel Body (collapsible)
    html += '<div id="collapse-scope-' + tis_id + '" class="panel-collapse collapse ' + collapseIn + '">';
    html += '<div class="panel-body" style="padding: 10px;">';
    html += '<input type="hidden" name="section_box_tis[]" value="' + tis_id + '" class="section_box_tis">';
    html += '<div class="table-responsive">';
    html += '<table class="table table-bordered table_multiples inner-repeater" id="table-group-' + tis_id + '" style="margin-bottom:0;">';

    // thead หลัก
    html += '<thead><tr style="background:#f0f4ff;">';
    html += '<th width="5%"  class="text-center">รายการทดสอบ<br>ที่ขอรับการแต่งตั้ง</th>';
    html += '<th width="6%"  class="text-center">ข้อ</th>';
    html += '<th width="44%" class="text-center">รายการทดสอบ</th>';
    html += '<th width="45%" class="text-center">เครื่องมือที่ใช้</th>';
    html += '</tr></thead>';
    html += '<tbody data-repeater-list="repeater-group-' + tis_id + '">';

    items.forEach(function (item) {
        var savedRows    = existingByItem[item.id] || [];
        var isChecked    = savedRows.length > 0;
        var disClass     = isChecked ? '' : 'scope-row-disabled';
        var isExemptItem = (String(item.test_method_id) === '18');

        // แถวหลัก
        var indentPx = (item.level || 0) * 20;

        html += '<tr class="main-item-row ' + disClass + '" data-item-id="' + item.id + '" data-exempt="' + (isExemptItem ? '1' : '0') + '" style="background:' + (isChecked ? '#fff' : '#fafafa') + ';">';
        html += '<td class="text-center" style="vertical-align:top; padding-top:10px;">';
        html += '<input type="checkbox" class="scope_checkbox" ' + (isChecked ? 'checked' : '') + ' data-item-id="' + item.id + '" data-tis-id="' + tis_id + '" style="width:18px;height:18px;cursor:pointer;">';
        html += '</td>';
        html += '<td style="vertical-align:top; padding-top:10px; padding-left:' + (8 + indentPx) + 'px; font-weight:bold; white-space:nowrap;">' + (item.no || '') + '</td>';
        html += '<td style="vertical-align:top; padding-top:10px; padding-left:' + (8 + indentPx) + 'px;">' + item.title + '</td>';

        if (isExemptItem) {
            // ตรวจพินิจ — ไม่มีเครื่องมือ ไม่ต้องระบุรายละเอียด
            html += '<td style="vertical-align:top; padding-top:10px;"><em class="text-muted">ตรวจพินิจ — ไม่ต้องระบุรายละเอียด</em></td>';
        } else {
            // คอลัมน์เครื่องมือ — แสดงเป็น checkbox list
            html += '<td style="vertical-align:top;"><div class="tool-chk-group">';
            if (item.tools && item.tools.length > 0) {
                item.tools.forEach(function (tool) {
                    var savedTool    = savedRows.find(function(s){ return String(s.test_tools_id) === String(tool.id); });
                    var isToolChecked = !!savedTool;
                    var disAttr      = isChecked ? '' : 'disabled';
                    //เจ้าหน้าที่ระบุว่าเครื่องมือตัวนี้ต้องแก้ไข (audit_result=2 คู่กับ remark ตอนตรวจประเมิน)
                    var needsFix = !!(savedTool && savedTool.audit_result == 2 && savedTool.remark);
                    html += '<label class="chk-tool-lbl">';
                    html += '<input type="checkbox" name="tool_chk_' + item.id + '[]" class="tool_chk" ' + (isToolChecked ? 'checked' : '') + ' ' + disAttr;
                    html += ' data-item-id="' + item.id + '" data-tool-id="' + tool.id + '"> ' + tool.title;
                    if (needsFix) {
                        var remarkEsc = $('<div>').text(savedTool.remark).html();
                        html += ' <span class="label label-danger" title="' + remarkEsc + '"><i class="fa fa-exclamation-triangle"></i> ต้องแก้ไข</span>';
                    }
                    html += '</label>';
                    if (needsFix) {
                        html += '<div class="text-danger small" style="margin:2px 0 4px 20px;"><i class="fa fa-comment"></i> หมายเหตุเจ้าหน้าที่: ' + remarkEsc + '</div>';
                    }
                });
            } else {
                html += '<span class="text-muted small">— ไม่มีข้อมูลเครื่องมือ —</span>';
            }
            html += '</div>';
            html += '<div style="margin-top:4px;">';
            html += '<button type="button" class="btn btn-xs btn-info btn-add-custom-tool" onclick="toggleAddToolForm(' + item.id + '); return false;"><i class="fa fa-plus"></i> เพิ่มเครื่องมือ</button>';
            html += '</div>';
            html += '<div class="add-tool-form" id="add-tool-form-' + item.id + '" style="display:none; margin-top:5px;">';
            html += '<div class="input-group input-group-sm">';
            html += '<input type="text" class="form-control" id="add-tool-input-' + item.id + '" placeholder="ระบุชื่อเครื่องมือ">';
            html += '<span class="input-group-btn">';
            html += '<button type="button" class="btn btn-success btn-sm" onclick="saveCustomTool(' + item.id + ', \'' + tis_id + '\'); return false;"><i class="fa fa-save"></i> บันทึก</button>';
            html += '<button type="button" class="btn btn-default btn-sm" onclick="toggleAddToolForm(' + item.id + '); return false;">ยกเลิก</button>';
            html += '</span></div></div>';
            html += '</td>';
        }
        html += '</tr>';

        // แถวรายละเอียด
        if (isExemptItem) {
            // ตรวจพินิจ — text fields แบบ readonly
            var exemptDis     = isChecked ? '' : 'disabled';
            var exemptScopeId = (savedRows.length > 0 && savedRows[0].scope_id) ? savedRows[0].scope_id : '';
            html += '<tr class="details-row exempt-method-row" data-item-id="' + item.id + '" style="' + (isChecked ? '' : 'display:none;') + '">';
            html += '<td colspan="4">';
            html += '<table class="table inner-detail-table" style="margin:0;">';
            html += '<thead><tr>';
            html += '<th>เครื่องมือที่ใช้</th><th>รหัส/หมายเลข</th><th>ขีดความสามารถ</th>';
            html += '<th>ช่วงการใช้งาน</th><th>ความละเอียดที่อ่านได้</th>';
            html += '<th>ความคลาดเคลื่อนที่ยอมรับ</th><th>ระยะการทดสอบ(วัน)</th><th>ค่าใช้จ่าย/ชุดละ</th>';
            html += '</tr></thead>';
            html += '<tbody><tr data-repeater-item>';
            html += '<td><strong>ตรวจพินิจ</strong>';
            html += '<input type="hidden" name="test_item_id"           class="scope_field" value="' + item.id + '" ' + exemptDis + '>';
            html += '<input type="hidden" name="test_tools_id"          class="scope_field" value="" ' + exemptDis + '>';
            html += '<input type="hidden" name="test_tools_custom_name" class="scope_field" value="" ' + exemptDis + '>';
            html += '<input type="hidden" name="tis_id"                 class="scope_field" value="' + tis_id + '" ' + exemptDis + '>';
            html += '<input type="hidden" name="tis_tisno"              class="scope_field" value="' + tisno  + '" ' + exemptDis + '>';
            html += '<input type="hidden" name="scope_id"               class="scope_field" value="' + exemptScopeId + '" ' + exemptDis + '>';
            html += '</td>';
            html += '<td><input type="text" name="test_tools_no" class="form-control input-sm scope_field" value="" readonly ' + exemptDis + '></td>';
            html += '<td><input type="text" name="capacity"      class="form-control input-sm scope_field" value="" readonly ' + exemptDis + '></td>';
            html += '<td><input type="text" name="range"         class="form-control input-sm scope_field" value="" readonly ' + exemptDis + '></td>';
            html += '<td><input type="text" name="true_value"    class="form-control input-sm scope_field" value="" readonly ' + exemptDis + '></td>';
            html += '<td><input type="text" name="fault_value"   class="form-control input-sm scope_field" value="" readonly ' + exemptDis + '></td>';
            html += '<td><input type="text" name="test_duration" class="form-control input-sm scope_field" value="" readonly ' + exemptDis + '></td>';
            html += '<td><input type="text" name="test_price"    class="form-control input-sm scope_field" value="" readonly ' + exemptDis + '></td>';
            html += '</tr></tbody></table>';
            html += '</td></tr>';
        } else {
            // แถวรายละเอียด (nested table) สำหรับ item ปกติ
            html += '<tr class="details-row" data-item-id="' + item.id + '" style="' + (isChecked ? '' : 'display:none;') + '">';
            html += '<td colspan="4">';
            html += '<table class="table inner-detail-table" style="margin:0;">';
            html += '<thead><tr>';
            html += '<th>เครื่องมือที่ใช้</th><th>รหัส/หมายเลข</th><th>ขีดความสามารถ <span class="text-danger">*</span></th>';
            html += '<th>ช่วงการใช้งาน <span class="text-danger">*</span></th><th>ความละเอียดที่อ่านได้ <span class="text-danger">*</span></th>';
            html += '<th>ความคลาดเคลื่อนที่ยอมรับ <span class="text-danger">*</span></th><th>ระยะการทดสอบ(วัน) <span class="text-danger">*</span></th><th>ค่าใช้จ่าย/ชุดละ <span class="text-danger">*</span></th>';
            html += '</tr></thead>';
            html += '<tbody>';

            if (item.tools && item.tools.length > 0) {
                item.tools.forEach(function (tool) {
                    var savedTool     = savedRows.find(function(s){ return String(s.test_tools_id) === String(tool.id); });
                    var isToolChecked = !!savedTool;
                    var sVal          = savedTool || {};
                    var iDis          = isToolChecked ? '' : 'disabled';
                    var isExemptTool  = (String(tool.id) === '4');
                    var iReq          = (isToolChecked && !isExemptTool) ? 'required' : '';
                    var iReadonly     = isExemptTool ? 'readonly' : '';

                    html += '<tr data-repeater-item class="tool-detail-row" data-tool-id="' + tool.id + '" style="' + (isToolChecked ? '' : 'display:none;') + '">';
                    html += '<td><strong>' + tool.title + '</strong>';
                    html += '<input type="hidden" name="test_item_id"          class="scope_field" value="' + item.id + '" ' + iDis + '>';
                    html += '<input type="hidden" name="test_tools_id"         class="scope_field" value="' + tool.id + '" ' + iDis + '>';
                    html += '<input type="hidden" name="test_tools_custom_name" class="scope_field" value="" ' + iDis + '>';
                    html += '<input type="hidden" name="tis_id"                class="scope_field" value="' + tis_id  + '" ' + iDis + '>';
                    html += '<input type="hidden" name="tis_tisno"             class="scope_field" value="' + tisno   + '" ' + iDis + '>';
                    html += '<input type="hidden" name="scope_id"              class="scope_field h_scope_id" value="' + (sVal.scope_id || '') + '" ' + iDis + '>';
                    html += '</td>';
                    html += '<td><input type="text" name="test_tools_no" class="form-control input-sm scope_field" value="' + (sVal.test_tools_no || '') + '" ' + iDis + ' ' + iReadonly + '></td>';
                    html += '<td><input type="text" name="capacity"      class="form-control input-sm scope_field" value="' + (sVal.capacity      || '') + '" ' + iDis + ' ' + iReadonly + ' ' + iReq + '></td>';
                    html += '<td><input type="text" name="range"         class="form-control input-sm scope_field" value="' + (sVal.range         || '') + '" ' + iDis + ' ' + iReadonly + ' ' + iReq + '></td>';
                    html += '<td><input type="text" name="true_value"    class="form-control input-sm scope_field" value="' + (sVal.true_value    || '') + '" ' + iDis + ' ' + iReadonly + ' ' + iReq + '></td>';
                    html += '<td><input type="text" name="fault_value"   class="form-control input-sm scope_field" value="' + (sVal.fault_value   || '') + '" ' + iDis + ' ' + iReadonly + ' ' + iReq + '></td>';
                    html += '<td><input type="text" name="test_duration" class="form-control input-sm scope_field" value="' + (sVal.test_duration || '') + '" ' + iDis + ' ' + iReadonly + ' ' + iReq + '></td>';
                    html += '<td><input type="text" name="test_price"    class="form-control input-sm scope_field" value="' + (sVal.test_price    || '') + '" ' + iDis + ' ' + iReadonly + ' ' + iReq + '></td>';
                    html += '</tr>';
                });
            }

            html += '</tbody></table></td></tr>';
        }
    });

    html += '</tbody></table></div></div></div></div>';

    $('#box_scope_request').append(html);

    // collapse chevron toggle
    $('#collapse-scope-' + tis_id).on('show.bs.collapse',  function(){ $('#panel-scope-' + tis_id).find('.fa').removeClass('fa-chevron-down').addClass('fa-chevron-up'); });
    $('#collapse-scope-' + tis_id).on('hide.bs.collapse',  function(){ $('#panel-scope-' + tis_id).find('.fa').removeClass('fa-chevron-up').addClass('fa-chevron-down'); });

    // init repeater
    $('#panel-scope-' + tis_id).repeater();
}

// ─────────────────────────────────────────────────────────────
function toggleAddToolForm(item_id) {
    $('#add-tool-form-' + item_id).toggle();
    if ($('#add-tool-form-' + item_id).is(':visible')) {
        $('#add-tool-input-' + item_id).focus();
    }
}

// ─────────────────────────────────────────────────────────────
function saveCustomTool(item_id, tis_id) {
    var tool_name = $.trim($('#add-tool-input-' + item_id).val());
    if (!tool_name) { alert('กรุณาระบุชื่อเครื่องมือ'); return; }

    var $panel      = $('#panel-scope-' + tis_id);
    var tisno       = $panel.data('tisno');
    var $mainRow    = $panel.find('.main-item-row[data-item-id="' + item_id + '"]');
    var $detailsRow = $panel.find('.details-row[data-item-id="' + item_id + '"]');
    var isItemChecked = $mainRow.find('.scope_checkbox').is(':checked');
    var disAttr     = isItemChecked ? '' : 'disabled';
    var iReq        = isItemChecked ? 'required' : '';

    // ใช้ timestamp เป็น temp id สำหรับ data-tool-id (ไม่ใช่ DB id)
    var tempToolId  = 'custom_' + Date.now();

    // เพิ่ม checkbox ในรายการเครื่องมือ (เครื่องมือเดิมที่เลือกไว้แล้วไม่ถูกยกเลิก เพราะเลือกได้หลายตัว)
    var chkHtml  = '<label class="chk-tool-lbl">';
        chkHtml += '<input type="checkbox" name="tool_chk_' + item_id + '[]" class="tool_chk" ' + (isItemChecked ? 'checked' : '') + ' ' + disAttr;
        chkHtml += ' data-item-id="' + item_id + '" data-tool-id="' + tempToolId + '"> ' + tool_name;
        chkHtml += ' <button type="button" class="btn btn-danger btn-xs btn-remove-tool" data-tool-id="' + tempToolId + '" data-item-id="' + item_id + '" data-tis-id="' + tis_id + '" title="ลบเครื่องมือนี้"><i class="fa fa-times"></i></button>';
        chkHtml += '</label>';
    $mainRow.find('.tool-chk-group').append(chkHtml);

    // เพิ่ม detail row ในตาราง inner
    // หมายเหตุ: แถวนี้ถูก append เข้า DOM หลังจาก .repeater() init ไปแล้ว (บรรทัด ~1237)
    // jquery.repeater แปลงชื่อ input เป็น array bracket ("repeater-group-{tis}[idx][field]")
    // แค่ตอน init ครั้งเดียวเท่านั้น แถวที่เพิ่มทีหลังจะไม่ถูกแปลงให้ ต้องตั้งชื่อ
    // แบบ bracket เองตรงนี้ ไม่งั้น backend SaveScope() จะไม่เห็นข้อมูลแถวนี้เลย
    var listPrefix = 'repeater-group-' + tis_id + '[' + tempToolId + ']';
    var rowHtml  = '<tr data-repeater-item class="tool-detail-row" data-tool-id="' + tempToolId + '" style="' + (isItemChecked ? '' : 'display:none;') + '">';
        rowHtml += '<td><strong>' + tool_name + '</strong>';
        rowHtml += '<input type="hidden" name="' + listPrefix + '[test_item_id]"          class="scope_field" value="' + item_id  + '" ' + disAttr + '>';
        rowHtml += '<input type="hidden" name="' + listPrefix + '[test_tools_id]"         class="scope_field" value=""             ' + disAttr + '>';
        rowHtml += '<input type="hidden" name="' + listPrefix + '[test_tools_custom_name]" class="scope_field" value="' + tool_name + '" ' + disAttr + '>';
        rowHtml += '<input type="hidden" name="' + listPrefix + '[tis_id]"                class="scope_field" value="' + tis_id   + '" ' + disAttr + '>';
        rowHtml += '<input type="hidden" name="' + listPrefix + '[tis_tisno]"             class="scope_field" value="' + tisno    + '" ' + disAttr + '>';
        rowHtml += '<input type="hidden" name="' + listPrefix + '[scope_id]"              class="scope_field h_scope_id" value="" ' + disAttr + '>';
        rowHtml += '</td>';
        rowHtml += '<td><input type="text" name="' + listPrefix + '[test_tools_no]" class="form-control input-sm scope_field" value="" ' + disAttr + '></td>';
        rowHtml += '<td><input type="text" name="' + listPrefix + '[capacity]"      class="form-control input-sm scope_field" value="" ' + disAttr + ' ' + iReq + '></td>';
        rowHtml += '<td><input type="text" name="' + listPrefix + '[range]"         class="form-control input-sm scope_field" value="" ' + disAttr + ' ' + iReq + '></td>';
        rowHtml += '<td><input type="text" name="' + listPrefix + '[true_value]"    class="form-control input-sm scope_field" value="" ' + disAttr + ' ' + iReq + '></td>';
        rowHtml += '<td><input type="text" name="' + listPrefix + '[fault_value]"   class="form-control input-sm scope_field" value="" ' + disAttr + ' ' + iReq + '></td>';
        rowHtml += '<td><input type="text" name="' + listPrefix + '[test_duration]" class="form-control input-sm scope_field" value="" ' + disAttr + ' ' + iReq + '></td>';
        rowHtml += '<td><input type="text" name="' + listPrefix + '[test_price]"    class="form-control input-sm scope_field" value="" ' + disAttr + ' ' + iReq + '></td>';
        rowHtml += '</tr>';
    $detailsRow.find('tbody').append(rowHtml);

    // sync สถานะแถวรายละเอียดให้ตรงกับเครื่องมือที่เลือกไว้ (รวมตัวที่เพิ่งเพิ่มใหม่)
    syncToolSelectionForItem(tis_id, item_id);

    // ซ่อน form และล้าง input
    $('#add-tool-form-' + item_id).hide();
    $('#add-tool-input-' + item_id).val('');

    updateScopeBadge(tis_id);
}
</script>
@endpush
