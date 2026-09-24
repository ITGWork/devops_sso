<div id="edit-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            {!! Form::model($user, [
                'method' => 'PATCH',
                'url' => ['/profile/update'],
                'class' => 'form-horizontal',
                'files' => true
            ]) !!}

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="modal-title">แก้ไขโปรไฟล์</h3>
                </div>
                <div class="modal-body">

                    <div id="alert-message"></div>

                    <h5 class="m-t-0">
                        {{ $user->applicanttype_id==2 ? 'ที่อยู่ตามทะเบียนบ้าน' : 'ที่ตั้งสำนักงานใหญ่' }}
                    </h5>

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-8">
                                {!! HTML::decode(Form::label('address_no', 'เลขที่'.' <span class="text-danger">*</span>', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('address_no', !empty($prefillAddress['address']) ? $prefillAddress['address'] : null, ['class' => 'form-control', 'required'=> true, 'maxlength' => 150, ]) !!}
                                    {!! $errors->first('address_no', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>

                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('soi', 'ตรอก/ซอย', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12 " >
                                    {!! Form::text('soi', !empty($prefillAddress['soi']) ? $prefillAddress['soi'] : null, ['class' => 'form-control', 'maxlength' => 50]) !!}
                                    {!! $errors->first('soi', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('moo', 'หมู่', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('moo', !empty($prefillAddress['moo']) ? $prefillAddress['moo'] : null, ['class' => 'form-control', 'maxlength' => 80]) !!}
                                    {!! $errors->first('moo', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('street', 'ถนน', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('street', !empty($prefillAddress['road']) ? $prefillAddress['road'] : null, ['class' => 'form-control', 'required' => false, 'maxlength' => 80]) !!}
                                    {!! $errors->first('street', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                       
                        </div>

                        <div class="form-group">
                            <div class="col-md-8">
                                {!! Form::label('address_search', 'ค้นหา', ['class' => 'col-md-12']) !!}
                                <div class="col-md-12 ">
                                    {!! Form::text('address_search', null, ['class' => 'form-control', 'autocomplete' => 'off', 'data-provide' => 'typeahead', 'placeholder' => 'ค้นหา:ตำบล/แขวง,อำเภอ/เขต,จังหวัด,รหัสไปรษณีย์', 'id'=>'address_search' ]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('subdistrict', 'แขวง/ตำบล'.' <span class="text-danger">*</span>', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('subdistrict', !empty($prefillAddress['tumbol']) ? $prefillAddress['tumbol'] : null, ['class' => 'form-control', 'required' => true, 'readonly' => true, 'maxlength' => 70]) !!}
                                    {!! $errors->first('subdistrict', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('district', 'เขต/อำเภอ'.' <span class="text-danger">*</span>', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('district', !empty($prefillAddress['ampur']) ? $prefillAddress['ampur'] : null, ['class' => 'form-control', 'required' => true, 'readonly' => true, 'maxlength' => 70]) !!}
                                    {!! $errors->first('district', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('province', 'จังหวัด'.' <span class="text-danger">*</span>', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('province', !empty($prefillAddress['province']) ? $prefillAddress['province'] : null, ['class' => 'form-control', 'required' => true, 'readonly' => true, 'maxlength' => 70]) !!}
                                    {!! $errors->first('province', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('zipcode', 'รหัสไปรษณีย์'.' <span class="text-danger">*</span>', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('zipcode', !empty($prefillAddress['zipcode']) ? $prefillAddress['zipcode'] : null, ['class' => 'form-control zipcode', 'required' => true, 'minlength' => 5, "maxlength" => 5]) !!}
                                    {!! $errors->first('zipcode', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('tel', 'เบอร์โทรศัพท์'.' <span class="text-danger">*</span>', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('tel', null, ['class' => 'form-control', 'required' => true, 'maxlength' => 30]) !!}
                                    {!! $errors->first('tel', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('fax', 'เบอร์โทรสาร', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('fax', null, ['class' => 'form-control', 'maxlength' => 30]) !!}
                                    {!! $errors->first('fax', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="m-t-0">ที่อยู่ที่สามารถติดต่อได้</h5>

                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('contact_address_no', 'เลขที่'.' <span class="text-danger">*</span>', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('contact_address_no', null, ['class' => 'form-control', 'required'=> true, 'maxlength' => 100]) !!}
                                    {!! $errors->first('contact_address_no', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('contact_building', 'อาคาร/หมู่บ้าน', ['class' => 'col-md-12', 'maxlength' => 191])) !!}
                                <div class="col-md-12 " >
                                    {!! Form::text('contact_building', null, ['class' => 'form-control']) !!}
                                    {!! $errors->first('contact_building', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('contact_soi', 'ตรอก/ซอย', ['class' => 'col-md-12', 'maxlength' => 50])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('contact_soi', null, ['class' => 'form-control']) !!}
                                    {!! $errors->first('contact_soi', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('contact_moo', 'หมู่', ['class' => 'col-md-12', 'maxlength' => 80])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('contact_moo', null, ['class' => 'form-control']) !!}
                                    {!! $errors->first('contact_moo', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('contact_street', 'ถนน', ['class' => 'col-md-12  '])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('contact_street', null, ['class' => 'form-control', 'required' => false, 'maxlength' => 80]) !!}
                                    {!! $errors->first('contact_street', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                       
                        </div>

                        <div class="form-group">
                            <div class="col-md-8">
                                {!! Form::label('contact_address_search', 'ค้นหา', ['class' => 'col-md-12']) !!}
                                <div class="col-md-12 ">
                                    {!! Form::text('contact_address_search', null, ['class' => 'form-control', 'autocomplete' => 'off', 'data-provide' => 'typeahead', 'placeholder' => 'ค้นหา:ตำบล/แขวง,อำเภอ/เขต,จังหวัด,รหัสไปรษณีย์', 'id'=>'contact_address_search' ]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('contact_subdistrict', 'แขวง/ตำบล'.' <span class="text-danger">*</span>', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('contact_subdistrict', null, ['class' => 'form-control', 'required' => true, 'maxlength' => 70,'readonly'=>true]) !!}
                                    {!! $errors->first('contact_subdistrict', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('contact_district', 'เขต/อำเภอ'.' <span class="text-danger">*</span>', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('contact_district', null, ['class' => 'form-control', 'required' => true, 'maxlength' => 70,'readonly'=>true]) !!}
                                    {!! $errors->first('contact_district', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('contact_province', 'จังหวัด'.' <span class="text-danger">*</span>', ['class' => 'col-md-12  '])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('contact_province', null, ['class' => 'form-control', 'required' => true, 'maxlength' => 70,'readonly'=>true]) !!}
                                    {!! $errors->first('contact_province', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('contact_zipcode', 'รหัสไปรษณีย์'.' <span class="text-danger">*</span>', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('contact_zipcode', null, ['class' => 'form-control zipcode', 'required' => true, 'minlength' => 5, "maxlength" => 5]) !!}
                                    {!! $errors->first('contact_zipcode', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5>ข้อมูลผู้ติดต่อ</h5>
                    <div class="row">

                        <div class="form-group">

                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('contact_prefix_name', 'ชื่อผู้ติดต่อ'.' <span class="text-danger">*</span>', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::select('contact_prefix_name',
                                                     App\Models\Basic\Prefix::where('state', 1)->pluck('initial', 'id')->all(),
                                                     null,
                                                     ['class' => 'form-control',
                                                      'id'=>'contact_prefix_name',
                                                      'placeholder' =>'- เลือกคำนำหน้าชื่อ -',
                                                      'required'=> true
                                                     ])
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('', '&nbsp;', ['class' => 'col-md-12 '])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('contact_first_name', null, ['class' => 'form-control', 'placeholder' => 'ชื่อ', 'required' => true, 'maxlength' => 191]) !!}
                                    {!! $errors->first('contact_first_name', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                    {!! HTML::decode(Form::label('', '&nbsp;', ['class' => 'col-md-12'])) !!}
                                    <div class="col-md-12">
                                          {!! Form::text('contact_last_name', null, ['class' => 'form-control', 'placeholder' => 'นามสกุล', 'required'=> true, 'maxlength' => 191]) !!}
                                          {!! $errors->first('contact_last_name', '<p class="help-block">:message</p>') !!}
                                    </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('contact_tax_id', 'เลขบัตรประจำตัวประชาชน'.' <span class="text-danger">*</span>', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                      {!! Form::text('contact_tax_id', null, ['class' => 'form-control tax_id_format', 'required' => true]) !!}
                                      {!! $errors->first('contact_tax_id', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('contact_position', 'ตำแหน่ง', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('contact_position', null, ['class' => 'form-control', 'required' => false, 'maxlength' => 255]) !!}
                                    {!! $errors->first('contact_position', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('contact_tel', 'เบอร์โทรศัพท์', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('contact_tel', null, ['class' => 'form-control', 'required' => false, 'maxlength' => 191]) !!}
                                    {!! $errors->first('contact_tel', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('contact_phone_number', 'เบอร์โทรศัพท์มือถือ'.'  <span class="text-danger">*</span>', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('contact_phone_number', null, ['class' => 'form-control phone_number_format', 'id' => 'phone_number', 'required' => true]) !!}
                                    {!! $errors->first('contact_phone_number', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! HTML::decode(Form::label('contact_fax', 'เบอร์โทรสาร', ['class' => 'col-md-12'])) !!}
                                <div class="col-md-12">
                                    {!! Form::text('contact_fax', null, ['class' => 'form-control', 'id' => 'fax', 'required'=> false, 'maxlength' => 191]) !!}
                                    {!! $errors->first('contact_fax', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success waves-effect waves-light">บันทึก</button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">ปิด</button>
                </div>

            {!! Form::close() !!}

        </div>
    </div>
</div>

@push('js')
    <script src="{{asset('js/mask/jquery.inputmask.bundle.min.js')}}"></script>
    <script>

        $(document).ready(function() {

            var applicanttype_id = $('div#profile').find('button').attr("data-applicanttype_id");
               // console.log(typeof applicanttype_id);
            if(applicanttype_id == '5'){
                $(".tax_id_format").inputmask();
            } else {
                $('.tax_id_format').inputmask('9-9999-99999-99-9');
            }
       
            $('.phone_number_format').inputmask('999-999-9999');//รูปแบบเบอร์มือถือ
 
            $("#address_search").select2({
                dropdownAutoWidth: true,
                width: '100%',
                ajax: {
                    url: "{{ url('/funtions/search-addreess') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params // search term
                        };
                    },
                    results: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true,
                },
                placeholder: 'คำค้นหา',
                minimumInputLength: 1,
            });

            $("#address_search").on('change', function () {
                $.ajax({
                    url: "{!! url('/funtions/get-addreess/') !!}" + "/" + $(this).val() + '?khet=1'
                }).done(function( jsondata ) {
                    if(jsondata != ''){

                        $('#subdistrict').val(jsondata.sub_title);
                        $('#district').val(jsondata.dis_title);
                        $('#province').val(jsondata.pro_title);
                        $('#zipcode').val(jsondata.zip_code);

                        $("#address_search").select2('val','');

                    }
                });
            });

            $("#contact_address_search").select2({
                dropdownAutoWidth: true,
                width: '100%',
                ajax: {
                    url: "{{ url('/funtions/search-addreess') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params // search term
                        };
                    },
                    results: function (response) {
                        return {
                            results: response
                        };
                    },
                    cache: true,
                },
                placeholder: 'คำค้นหา',
                minimumInputLength: 1,
            });

            $("#contact_address_search").on('change', function () {
                $.ajax({
                    url: "{!! url('/funtions/get-addreess/') !!}" + "/" + $(this).val() + '?khet=1'
                }).done(function( jsondata ) {
                    if(jsondata != ''){

                        $('#contact_subdistrict').val(jsondata.sub_title);
                        $('#contact_district').val(jsondata.dis_title);
                        $('#contact_province').val(jsondata.pro_title);
                        $('#contact_zipcode').val(jsondata.zip_code);

                        $("#contact_address_search").select2('val','');

                    }
                });
            });

            //เมื่อเปลี่ยนรหัสไปรษณีย์
            $('.zipcode').change(function(event) {

                var value = $(this).val();
                var temps  = [];
                $.each(value.split(''), function(index, el) {
                    if(el.match(/[0-9]/g) !== null){
                        temps.push(el);
                    }
                });

                if(temps.length!=5){
                    $(this).val('');
                }

            });

        });

    </script>
@endpush
