<fieldset class="white-box">
    <legend>ข้อมูลผู้ตรวจสอบ</legend>
    <div class="row">

        <div class="col-md-12 col-sm-12 h5">

            @php
                $type_arr = [1 => 'IB', 2 => 'CB'];
            @endphp

            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">ชื่อหน่วยตรวจสอบ :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->ibcb_name)?$ibcb->ibcb_name: '-') !!}</span></p>
                </div>
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">รหัสหน่วยตรวจสอบ :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->ibcb_code)?$ibcb->ibcb_code: '-') !!}</span></p>
                </div>
            </div>

            
            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">ชื่อหน่วยงานที่ยื่นขอ :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->name)?$ibcb->name:' - ') !!}</span></p>
                </div>
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">เลขนิติบุคคล :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->taxid)?$ibcb->taxid:' - ') !!}</span></p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">วันที่เริ่มเป็นผู้ตรวจสอบ :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->ibcb_start_date)?HP::DateThaiFull($ibcb->ibcb_start_date): '-') !!}</span></p>
                </div>
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">วันที่สิ้นสุดเป็นผู้ตรวจสอบ :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    @php
                        $end_date = $ibcb->scopes_group()->select('end_date')->max('end_date');
                    @endphp
                    <p class="div_dotted"><span class="text-bold-300">{!! !empty( $ibcb->ibcb_end_date )?HP::DateThaiFull($ibcb->ibcb_end_date):(!empty($end_date)?HP::DateThaiFull($end_date): '-') !!}</span></p>
                </div>
            </div>
           
            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">ประเภท :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (array_key_exists( $ibcb->ibcb_type,  $type_arr )?$type_arr [ $ibcb->ibcb_type ]:'-') !!}</span></p>
                </div>
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">สถานะหน่วยตรวจสอบ :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class=" {!! (!empty($ibcb->state) && $ibcb->state == 1 ?'text-success': 'text-danger') !!}">{!! (!empty($ibcb->state) && $ibcb->state == 1 ?'Active': 'Not Active') !!}</span></p>
                </div>
            </div>

            <hr>
            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">ที่อยู่ :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->ibcb_address)?$ibcb->ibcb_address:' - ') !!}</span></p>
                </div>
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">หมู่ที่ :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->ibcb_moo)?$ibcb->ibcb_moo:' - ') !!}</span></p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">หมู่บ้าน/อาคาร :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->ibcb_building)?$ibcb->ibcb_building:' - ') !!}</span></p>
                </div>
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">ตรอก/ซอย :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->ibcb_soi)?$ibcb->ibcb_soi:' - ') !!}</span></p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">ถนน :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->ibcb_road)?$ibcb->ibcb_road:' - ') !!}</span></p>
                </div>
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">ตำบล/แขวง :</span></p>
                </div>
                 <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->IbcbSubdistrictName)?$ibcb->IbcbSubdistrictName:' - ') !!}</span></p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">อำเภอ/เขต :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->IbcbDistrictName)?$ibcb->IbcbDistrictName:' - ') !!}</span></p>
                </div>
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">จังหวัด :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->IbcbProvinceName)?$ibcb->IbcbProvinceName:' - ') !!}</span></p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">รหัสไปรษณีย์ :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->ibcb_zipcode)?$ibcb->ibcb_zipcode:' - ') !!}</span></p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">โทรศัพท์ :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->ibcb_phone)?$ibcb->ibcb_phone: '-') !!}</span></p>
                </div>
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">โทรสาร :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->ibcb_fax)?$ibcb->ibcb_fax: '-') !!}</span></p>
                </div>
            </div>

            <hr>
            
            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">ชื่อผู้ประสานงาน :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->co_name)?$ibcb->co_name: '-') !!}</span></p>
                </div>
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">ตำแหน่งผู้ประสานงาน :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->co_position)?$ibcb->co_position: '-') !!}</span></p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">โทรศัพท์มือถือ :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->co_mobile)?$ibcb->co_mobile: '-') !!}</span></p>
                </div>
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">โทรศัพท์ :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->co_phone)?$ibcb->co_phone: '-') !!}</span></p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">โทรสาร :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->co_fax)?$ibcb->co_fax: '-') !!}</span></p>
                </div>
                <div class="col-md-2 col-sm-12">
                    <p class="text-right"><span class="text-bold-300">อีเมล :</span></p>
                </div>
                <div class="col-md-4 col-sm-12">
                    <p class="div_dotted"><span class="text-bold-300">{!! (!empty($ibcb->co_email)?$ibcb->co_email: '-') !!}</span></p>
                </div>
            </div>

        </div>

    </div>
    
</fieldset>