{!! Form::hidden('applicant_taxid', $labs->taxid, ['id' => 'applicant_taxid']) !!}

<fieldset class="scheduler-border">
    <legend class="scheduler-border">ได้รับใบรับรองระบบงานตามฐาน</legend>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('audit_type', 'ได้รับใบรับรองระบบงานตามฐาน 17025', ['class' => 'col-md-2 control-label']) !!}
                @php
                    // เดิม hardcode checked='1' เสมอ ไม่ผูกกับคำขอเดิม (draft/ถูกตีกลับ) ที่อาจเคยเลือก
                    // "ภาคผนวก ก." (audit_type=2) ไว้ ทำให้เปิด "จัดการ" ซ้ำแล้ว radio รีเซ็ตกลับไปข้อ 1 ทุกครั้ง
                    $audit_type_value = !empty($draft_app->audit_type) ? $draft_app->audit_type : 1;
                @endphp
                <div class="col-md-8">
                    <label>{!! Form::radio('audit_type', '1', $audit_type_value == 1, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'audit_type_1']) !!} ได้รับ พร้อมแนบหลักฐาน</label>
                    <label data-toggle="tooltip" title="เป็นส่วนราชการ องค์การของรัฐ รัฐวิสาหกิจ หน่วยงานของรัฐ รวมทั้งสถาบันอิสระภายใต้สังกัดกระทรวงอุตสาหกรรม ที่ยังไม่ได้รับการรับรองตาม มอก. 17025 ในขอบข่าย มอก. ที่เกี่ยวข้อง แต่มีการดำเนินงานที่เป็นไปตามหลักเกณฑ์ฯ ตามภาคผนวก ก">
                        {!! Form::radio('audit_type', '2', $audit_type_value == 2, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'audit_type_2']) !!} ไม่ได้รับ ทำการตรวจประเมิน ภาคผนวก ก.
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row box_audit_type_1">
        <div class="col-md-12">
            <div class="form-group required">
                {!! Form::label('certificate_cerno_export', 'เลขที่ได้รับการรับรอง'.' :', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-6">
                    {!! Form::text('certificate_cerno_export', null, ['class' => 'form-control certificate_cerno_export', 'id' => 'certificate_cerno_export', 'placeholder'=>'กรอกเลขที่ได้รับการรับรอง']); !!}
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary" type="button" id="btn_std_export" value="1"><i class="fa fa-database"></i> ดึงจากฐานของ สมอ.</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row box_audit_type_1">
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('certificate_accereditatio_no', 'หมายเลขการรับรอง'.' :', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-6">
                    {!! Form::text('certificate_accereditatio_no', null, ['class' => 'form-control certificate_accereditatio_no', 'id' => 'certificate_accereditatio_no']); !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row box_audit_type_1">
        <div class="col-md-12">
            <div class="form-group required">
                {!! Form::label('certificate_issue_date', 'วันที่ได้รับ'.' :', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-4">
                    <div class="input-group">
                        {!! Form::text('certificate_issue_date', null, ['class' => 'form-control mydatepicker', 'placeholder'=>'dd/mm/yyyy']); !!}
                        <span class="input-group-addon"><i class="icon-calender"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row box_audit_type_1">
        <div class="col-md-12">
            <div class="form-group required">
                {!! Form::label('certificate_expire_date', 'วันที่หมดอายุ'.' :', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-4">
                    <div class="input-group">
                        {!! Form::text('certificate_expire_date', null, ['class' => 'form-control mydatepicker', 'placeholder'=>'dd/mm/yyyy']); !!}
                        <span class="input-group-addon"><i class="icon-calender"></i></span>
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success show_tag_a" type="button" id="btn_cer_add"><i class="icon-plus"></i> เพิ่ม</button>
                </div>
            </div>
        </div>
    </div>

    @include ('section5.application-lab.modals.modal-cer')

    <div class="row box_audit_type_1">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered repeater_audit_type_1" id="table-certificate">
                    <thead>
                        <tr>
                            <th class="text-center" width="20%">ใบรับรองเลขที่</th>
                            <th class="text-center" width="20%">หมายเลขการรับรอง</th>
                            <th class="text-center" width="15%">วันที่ได้รับ</th>
                            <th class="text-center" width="15%">วันที่หมดอายุ</th>
                            <th class="text-center" width="20%">ไฟล์ใบรับรอง</th>
                            <th class="text-center" width="10%">ลบ</th>
                        </tr>
                    </thead>
                    <tbody data-repeater-list="repeater-audit-1" class="text-center"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row box_audit_type_2" style="display:none;">
        <div class="col-md-12">
            <div class="repeater_audit_type_2">
                <div data-repeater-list="repeater-audit-2">
                    <div class="form-group" data-repeater-item>
                        {!! Form::label('audit_date', 'ช่วงวันที่พร้อมให้เข้าตรวจประเมิน'.' :', ['class' => 'col-md-2 control-label']) !!}
                        <div class="col-md-7">
                            <div class="input-daterange input-group date-range">
                                <div class="input-group">
                                    {!! Form::text('audit_date_start', null, ['class' => 'form-control audit_date_start', 'placeholder'=>'dd/mm/yyyy', 'required' => true]) !!}
                                    <span class="input-group-addon"><i class="icon-calender"></i></span>
                                </div>
                                <label class="input-group-addon bg-white b-0 control-label"> ถึงวันที่ </label>
                                <div class="input-group">
                                    {!! Form::text('audit_date_end', null, ['class' => 'form-control audit_date_end', 'placeholder'=>'dd/mm/yyyy', 'required' => true]) !!}
                                    <span class="input-group-addon"><i class="icon-calender"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-danger rounded-circle btn_remove_audi2" type="button" data-repeater-delete>
                                <i class="fa fa-trash-o"></i>
                            </button>
                            <button class="btn btn-success btn-primary btn_add_audi2" type="button" data-repeater-create>
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</fieldset>

<fieldset class="scheduler-border">
    <legend class="scheduler-border">ข้อมูลผู้ยื่นคำขอ</legend>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">ชื่อนิติบุคคล :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! $labs->name !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">เลขนิติบุคคล :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! $labs->taxid !!}</p>
                </div>
            </div>
        </div>
    </div>
</fieldset>

<fieldset class="scheduler-border">
    <legend class="scheduler-border">ที่อยู่สำนักงานใหญ่</legend>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">เลขที่ :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($labs->lab_address)?$labs->lab_address:' - ') !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">อาคาร/หมู่บ้าน :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($labs->lab_building)?$labs->lab_building:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">หมู่ที่ :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($labs->lab_moo)?$labs->lab_moo:' - ') !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">ตรอก/ซอย :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($labs->lab_soi)?$labs->lab_soi:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">ถนน :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($labs->lab_road)?$labs->lab_road:' - ') !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">ตำบล/แขวง :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($labs->LabSubdistrictName)?$labs->LabSubdistrictName:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">อำเภอ/เขต :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($labs->LabDistrictName)?$labs->LabDistrictName:' - ') !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">จังหวัด :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($labs->LabProvinceName)?$labs->LabProvinceName:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">รหัสไปรษณีย์ :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($labs->lab_zipcode)?$labs->lab_zipcode:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>
</fieldset>

<fieldset class="scheduler-border">
    <legend class="scheduler-border">ข้อมูลห้องปฏิบัติการ</legend>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="col-md-2 control-label text-right"><span class="text-bold-600">ชื่อห้องปฏิบัติการ :</span></label>
                <div class="col-md-10">
                    <p class="form-control-static">{!! (!empty($labs->lab_name)?$labs->lab_name:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>
</fieldset>

<fieldset class="scheduler-border">
    <legend class="scheduler-border">ผู้ประสานงาน</legend>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">ชื่อผู้ประสานงาน :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($labs->co_name)?$labs->co_name:' - ') !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">ตำแหน่ง :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($labs->co_position)?$labs->co_position:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">โทรศัพท์มือถือ :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($labs->co_mobile)?$labs->co_mobile:' - ') !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">โทรศัพท์ :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($labs->co_phone)?$labs->co_phone:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">โทรสาร :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($labs->co_fax)?$labs->co_fax:' - ') !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">อีเมล :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($labs->co_email)?$labs->co_email:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>
</fieldset>