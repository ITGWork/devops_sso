{!! Form::hidden('application_type', $ibcbs->ibcb_type, ['id' => 'application_type']) !!}
{!! Form::hidden('applicant_taxid', $ibcbs->taxid, ['id' => 'applicant_taxid']) !!}

<fieldset class="scheduler-border">
    <legend class="scheduler-border">การได้รับใบรับรองระบบงาน</legend>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('audit_type', 'การได้รับใบรับรองระบบงาน', ['class' => 'col-md-2 control-label']) !!}
                @php
                    // เดิม hardcode checked='1' เสมอ ไม่ผูกกับคำขอเดิม (draft/ถูกตีกลับ) ที่อาจเคยเลือก
                    // "ภาคผนวก ก." (audit_type=2) ไว้ ทำให้เปิด "จัดการ" ซ้ำแล้ว radio รีเซ็ตกลับไปข้อ 1 ทุกครั้ง
                    // (มิเรอร์บั๊กเดียวกับที่แก้ใน Lab labs/form/infomation.blade.php)
                    $audit_type_value = !empty($draft_app->audit_type) ? $draft_app->audit_type : 1;
                @endphp
                <div class="col-md-8">
                    <label>{!! Form::radio('audit_type', '1', $audit_type_value == 1, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'audit_type_1']) !!} ได้รับ พร้อมแนบหลักฐาน</label>
                    <label class="lable_audit_type2">{!! Form::radio('audit_type', '2', $audit_type_value == 2, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'audit_type_2']) !!} ไม่ได้รับ ทำการตรวจประเมิน ภาคผนวก ก.</label>
                </div>
            </div>
        </div>
    </div>
</fieldset>

<fieldset class="scheduler-border box_audit_type_1">
    <legend class="scheduler-border">ใบรับรองระบบงานตามมาตรฐาน</legend>

    <div class="row">
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

    <div class="row">
        <div class="col-md-12">
            <div class="form-group required">
                {!! Form::label('certificate_issue_date', 'วันที่ออกใบรับรอง'.' :', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-4">
                    <div class="input-group">
                        {!! Form::text('certificate_issue_date', null, ['class' => 'form-control mydatepicker', 'placeholder'=>'dd/mm/yyyy']); !!}
                        <span class="input-group-addon"><i class="icon-calender"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group required">
                {!! Form::label('certificate_expire_date', 'วันที่หมดอายุใบรับรอง'.' :', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-4">
                    <div class="input-group">
                        {!! Form::text('certificate_expire_date', null, ['class' => 'form-control mydatepicker', 'placeholder'=>'dd/mm/yyyy']); !!}
                        <span class="input-group-addon"><i class="icon-calender"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group required">
                {!! Form::label('certificate_std_export', 'มอก. รับรองระบบงาน'.' :', ['class' => 'col-md-2 control-label']) !!}
                <div class="col-md-6">
                    {!! Form::select('certificate_std_export', App\Models\Bsection5\Standard::pluck('title', 'id')->all() , null, ['class' => 'form-control', 'placeholder'=>'เลือกมอก. รับรองระบบงาน']); !!}
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success show_tag_a" type="button" id="btn_cer_add"><i class="icon-plus"></i> เพิ่ม</button>
                </div>
            </div>
        </div>
    </div>

    @include ('section5.application-ib-cb.modals.modal-certificate')
    <hr>

    <div class="row">
        <div class="col-md-12">

            <div class="table-responsive">
                <table class="table table-bordered certificate-repeater" id="table-certificate">
                    <thead>
                        <tr>
                            <th class="text-center" width="25%">ใบรับรองเลขที่</th>
                            <th class="text-center" width="20%">วันที่ออก</th>
                            <th class="text-center" width="20%">วันที่หมด</th>
                            <th class="text-center" width="30%">มอก.</th>
                            <th class="text-center" width="5%">ลบ</th>
                        </tr>
                    </thead>
                    <tbody data-repeater-list="repeater-certificate" class="text-center">
                    </tbody>
                </table>
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
                    <p class="form-control-static">{!! $ibcbs->name !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">เลขนิติบุคคล :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! $ibcbs->taxid !!}</p>
                </div>
            </div>
        </div>
    </div>
</fieldset>

<fieldset class="scheduler-border">
    <legend class="scheduler-border">ที่อยู่หน่วยตรวจสอบ</legend>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">เลขที่ :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($ibcbs->ibcb_address)?$ibcbs->ibcb_address:' - ') !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">อาคาร/หมู่บ้าน :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($ibcbs->ibcb_building)?$ibcbs->ibcb_building:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">หมู่ที่ :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($ibcbs->ibcb_moo)?$ibcbs->ibcb_moo:' - ') !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">ตรอก/ซอย :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($ibcbs->ibcb_soi)?$ibcbs->ibcb_soi:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">ถนน :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($ibcbs->ibcb_road)?$ibcbs->ibcb_road:' - ') !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">ตำบล/แขวง :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($ibcbs->IbcbSubdistrictName)?$ibcbs->IbcbSubdistrictName:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">อำเภอ/เขต :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($ibcbs->IbcbDistrictName)?$ibcbs->IbcbDistrictName:' - ') !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">จังหวัด :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($ibcbs->IbcbProvinceName)?$ibcbs->IbcbProvinceName:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">รหัสไปรษณีย์ :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($ibcbs->ibcb_zipcode)?$ibcbs->ibcb_zipcode:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>
</fieldset>

<fieldset class="scheduler-border">
    <legend class="scheduler-border">ข้อมูลหน่วยตรวจสอบ</legend>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="col-md-2 control-label text-right"><span class="text-bold-600">ชื่อหน่วยตรวจสอบ :</span></label>
                <div class="col-md-10">
                    <p class="form-control-static">{!! (!empty($ibcbs->ibcb_name)?$ibcbs->ibcb_name:' - ') !!}</p>
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
                    <p class="form-control-static">{!! (!empty($ibcbs->co_name)?$ibcbs->co_name:' - ') !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">ตำแหน่ง :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($ibcbs->co_position)?$ibcbs->co_position:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">โทรศัพท์มือถือ :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($ibcbs->co_mobile)?$ibcbs->co_mobile:' - ') !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">โทรศัพท์ :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($ibcbs->co_phone)?$ibcbs->co_phone:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">โทรสาร :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($ibcbs->co_fax)?$ibcbs->co_fax:' - ') !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label text-right"><span class="text-bold-600">อีเมล :</span></label>
                <div class="col-md-8">
                    <p class="form-control-static">{!! (!empty($ibcbs->co_email)?$ibcbs->co_email:' - ') !!}</p>
                </div>
            </div>
        </div>
    </div>
</fieldset>
