@extends('layouts.master')

@section('title', 'ยกเลิกการเป็นหน่วยตรวจสอบ')

@push('css')
    <link href="{{asset('plugins/components/icheck/skins/all.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('plugins/components/toast-master/css/jquery.toast.css')}}">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="white-box">
                    <h3 class="box-title pull-left">
                        ยกเลิกการเป็นหน่วยตรวจสอบ — {{ $ibcbs->ibcb_name }}
                        @if($readonly ?? false)
                            <span class="label label-default" style="font-size:12px; vertical-align:middle;">ดูรายละเอียด (อ่านอย่างเดียว)</span>
                        @endif
                    </h3>
                    <a class="btn btn-success pull-right" href="{{ url('/request-section-5/application-ibcb/cancellation') }}">
                        <i class="icon-arrow-left-circle"></i> กลับ
                    </a>
                    <div class="clearfix"></div>
                    <hr>

                    @if ($errors->any())
                        <ul class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="alert alert-warning">
                        <strong>คำเตือน:</strong> เมื่อคำขอนี้ได้รับการอนุมัติและประกาศราชกิจจานุเบกษาแล้ว
                        ระบบจะปิดเฉพาะขอบข่ายที่เลือกไว้ด้านล่างเท่านั้น หากไม่เหลือขอบข่ายที่ยัง active อยู่เลย
                        หน่วยตรวจสอบนี้จะถูกปิดการใช้งานไปด้วยโดยอัตโนมัติ
                    </div>

                    {!! Form::open(['url' => '/request-section-5/application-ibcb/cancellation/save', 'method' => 'POST', 'files' => true, 'class' => 'form-horizontal', 'id' => 'cancel_form']) !!}
                    {!! Form::hidden('ibcb_id', $ibcbs->id) !!}
                    @if(!empty($existingApplication))
                        {!! Form::hidden('application_id', $existingApplication->id) !!}
                    @endif

                    {{-- ข้อมูลผู้ยื่นคำขอ --}}
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border"><h5>ข้อมูลผู้ยื่นคำขอ</h5></legend>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('applicant_name', 'ชื่อ - นามสกุล :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('applicant_name', $ibcbs->name, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('applicant_taxid', 'เลขผู้เสียภาษี :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('applicant_taxid', $ibcbs->taxid, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    {{-- ข้อมูลหน่วยตรวจสอบ --}}
                    <fieldset class="scheduler-border" style="margin-top:20px;">
                        <legend class="scheduler-border"><h5>ข้อมูลหน่วยตรวจสอบที่ขอยกเลิก</h5></legend>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('ibcb_name', 'ชื่อหน่วยตรวจสอบ :', ['class' => 'col-md-2 control-label']) !!}
                                    <div class="col-md-10">
                                        {!! Form::text('ibcb_name', $ibcbs->ibcb_name, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('ibcb_address', 'ที่อยู่ :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('ibcb_address_show', trim(implode(' ', array_filter([
                                                $ibcbs->ibcb_address,
                                                $ibcbs->ibcb_moo ? 'หมู่ '.$ibcbs->ibcb_moo : null,
                                                $ibcbs->ibcb_soi ? 'ซ.'.$ibcbs->ibcb_soi : null,
                                                $ibcbs->ibcb_road ? 'ถ.'.$ibcbs->ibcb_road : null,
                                                $ibcbs->IbcbSubdistrictName,
                                                $ibcbs->IbcbDistrictName,
                                                $ibcbs->IbcbProvinceName,
                                                $ibcbs->ibcb_zipcode,
                                            ]))), ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('ibcb_phone', 'เบอร์โทรศัพท์ :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('ibcb_phone', $ibcbs->ibcb_phone, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label col-md-4"><h6>ผู้ประสานงาน</h6></label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('co_name', 'ชื่อผู้ประสานงาน :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('co_name', $ibcbs->co_name, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('co_email', 'อีเมล :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::text('co_email', $ibcbs->co_email, ['class' => 'form-control', 'readonly' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    {{-- ประวัติราชกิจจานุเบกษา --}}
                    <fieldset class="scheduler-border" style="margin-top:20px;">
                        <legend class="scheduler-border"><h5>ประวัติราชกิจจานุเบกษา</h5></legend>
                        @forelse ($gazettes as $g)
                            <div class="row" style="margin:5px 0;">
                                <div class="col-md-12">
                                    เล่ม/ตอนที่ {{ $g->issue }} ปี {{ $g->year }}
                                    ({{ $g->announcement_date ? HP::revertDate($g->announcement_date) : '-' }})
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">ไม่มีข้อมูล</p>
                        @endforelse
                    </fieldset>

                    {{-- ขอบข่ายที่ยังใช้งานอยู่ --}}
                    <fieldset class="scheduler-border" style="margin-top:20px;">
                        <legend class="scheduler-border"><h5>ขอบข่ายที่ยังใช้งานอยู่</h5></legend>
                        <p class="text-muted">โปรดเลือกขอบข่ายที่ต้องการระบุในเอกสารคำขอ (การอนุมัติจะปิดขอบข่ายที่ยังใช้งานอยู่ทั้งหมดของหน่วยนี้เสมอ)</p>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="5%">เลือก</th>
                                        <th class="text-center" width="20%">สาขาผลิตภัณฑ์</th>
                                        <th class="text-center" width="25%">รายสาขา</th>
                                        <th class="text-center" width="10%">ISIC NO</th>
                                        <th class="text-center" width="30%">มาตรฐาน มอก. เลขที่</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($scopes as $scope)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="scope_ids[]" value="{{ $scope->id }}"
                                                    @if(in_array($scope->id, $selectedScopeIds ?? [])) checked @endif>
                                            </td>
                                            <td>{{ $scope->ScopeBranchGroupName }}</td>
                                            <td>{{ $scope->ScopeBranchs ?: '-' }}</td>
                                            <td class="text-center">{{ $scope->isic_no ?: '-' }}</td>
                                            <td>{{ $scope->ScopeTisNoList ?: '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">ไม่มีข้อมูล</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </fieldset>

                    {{-- ไฟล์ราชกิจจา (ถ้าต้องการแนบใหม่) --}}
                    <fieldset class="scheduler-border" style="margin-top:20px;">
                        <legend class="scheduler-border"><h5>ไฟล์ราชกิจจานุเบกษา (แนบใหม่ ถ้ามี)</h5></legend>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('gazette_file', 'ไฟล์ราชกิจจา :', ['class' => 'col-md-4 control-label']) !!}
                                    <div class="col-md-8">
                                        {!! Form::file('gazette_file', ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    {{-- เอกสารแนบ --}}
                    <fieldset class="scheduler-border" style="margin-top:20px;">
                        <legend class="scheduler-border"><h5>เอกสารแนบ</h5></legend>

                        <p class="text-muted">โปรดเลือกประเภทหน่วยงาน</p>

                        @php
                            $cancelAttachType = $existingAttachmentType ?? null;
                        @endphp
                        <div class="form-group m-b-0">
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <label>
                                {!! Form::radio('attachment_type', '1', $cancelAttachType == 1, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'cancel_attachment_type_1', 'required' => true]) !!}
                                หน่วยงานของรัฐ
                            </label>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <label>
                                {!! Form::radio('attachment_type', '2', $cancelAttachType == 2, ['class'=>'check', 'data-radio'=>'iradio_square-blue', 'id' => 'cancel_attachment_type_2', 'required' => true]) !!}
                                หน่วยงานเอกชน
                            </label>
                        </div>

                        {{-- หน่วยงานของรัฐ --}}
                        <div id="cancel_section_gov" style="display:none; margin-top:15px;">
                            <h5>แนบไฟล์ประกอบคำขอ (หน่วยงานของรัฐ)</h5>
                            <div class="row" style="margin:15px;">
                                <div class="col-md-6">1. หนังสือขอยกเลิกการแต่งตั้งจากหน่วยงานที่ลงนามโดยผู้มีอำนาจสูงสุด (ต้องมี) โดย "เรียน เลขาธิการสำนักงานมาตรฐานอุตสาหกรรม"</div>
                                <div class="col-md-6">
                                    @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'cancellation_gov_file_1'])
                                    {!! Form::file('gov_file_1', ['class' => 'form-control'] + (empty(($existingAttachFiles ?? collect())->get('cancellation_gov_file_1')) ? ['data-force-required' => '1'] : [])) !!}
                                </div>
                            </div>
                            <div class="row" style="margin:15px;">
                                <div class="col-md-6">2. หนังสือคำสั่งแต่งตั้งผู้อำนวยการศูนย์/สำนัก/อื่น ๆ จากหน่วยงานหลัก (กรณีหน่วยงานภายใต้การกำกับดูแล)</div>
                                <div class="col-md-6">
                                    @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'cancellation_gov_file_2'])
                                    {!! Form::file('gov_file_2', ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row" style="margin:15px;">
                                <div class="col-md-6">3. หนังสือมอบอำนาจ ลงนามโดยผู้มีอำนาจ ผู้รับมอบอำนาจ และพยาน (กรณีมอบอำนาจ)</div>
                                <div class="col-md-6">
                                    @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'cancellation_gov_file_3'])
                                    {!! Form::file('gov_file_3', ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        {{-- หน่วยงานเอกชน --}}
                        <div id="cancel_section_pri" style="display:none; margin-top:15px;">
                            <h5>แนบไฟล์ประกอบคำขอ (หน่วยงานเอกชน)</h5>
                            <div class="row" style="margin:15px;">
                                <div class="col-md-6">1. หนังสือขอยกเลิกการแต่งตั้งจากหน่วยงานที่ลงนามโดยผู้มีอำนาจสูงสุด (ต้องมี)</div>
                                <div class="col-md-6">
                                    @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'cancellation_pri_file_1'])
                                    {!! Form::file('pri_file_1', ['class' => 'form-control'] + (empty(($existingAttachFiles ?? collect())->get('cancellation_pri_file_1')) ? ['data-force-required' => '1'] : [])) !!}
                                </div>
                            </div>
                            <div class="row" style="margin:15px;">
                                <div class="col-md-6">2. หนังสือมอบอำนาจ ลงนามโดยผู้มีอำนาจ ผู้รับมอบอำนาจ และพยาน พร้อมประทับตราบริษัท</div>
                                <div class="col-md-6">
                                    @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'cancellation_pri_file_2'])
                                    {!! Form::file('pri_file_2', ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row" style="margin:15px;">
                                <div class="col-md-6">3. สำเนาบัตรประชาชน/หนังสือเดินทาง ของผู้มอบอำนาจและผู้รับมอบอำนาจ</div>
                                <div class="col-md-6">
                                    @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'cancellation_pri_file_3'])
                                    {!! Form::file('pri_file_3', ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row" style="margin:15px;">
                                <div class="col-md-6">4. หนังสือรับรองบริษัท อายุไม่เกิน 6 เดือน ประทับตราบริษัทและกรรมการ ลงนามทุกแผ่น</div>
                                <div class="col-md-6">
                                    @include('section5.application-lab.cancellation.partials.existing-file', ['section' => 'cancellation_pri_file_4'])
                                    {!! Form::file('pri_file_4', ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <center style="margin-top:20px;">
                        <div class="form-group">
                            <div class="col-md-offset-4 col-md-4">
                                @if(!($readonly ?? false))
                                    <button class="btn btn-danger" type="submit">
                                        ยืนยันยื่นคำขอยกเลิก
                                    </button>
                                @endif
                                <a class="btn btn-default" href="{{ url('/request-section-5/application-ibcb/cancellation') }}">
                                    {{ ($readonly ?? false) ? 'กลับ' : 'ยกเลิก' }}
                                </a>
                            </div>
                        </div>
                    </center>

                    {!! Form::close() !!}

                    @if(!empty($existingApplication) && $existingApplication->application_ibcb_accepts()->count() > 0)
                        @include('section5.application-ib-cb.history', ['applicationibcb' => $existingApplication])
                    @endif

                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function () {

        @if($readonly ?? false)
            // โหมด "ดูรายละเอียด" — ล็อกทุกช่องไม่ให้แก้ไข
            var $form = $('#cancel_form');
            $form.find('input, select, textarea').prop('disabled', true);
            $form.find('.fileinput-remove, .btn-file').hide();
            if ($('#cancel_attachment_type_1').is(':checked')) { $('#cancel_section_gov').show(); }
            else if ($('#cancel_attachment_type_2').is(':checked')) { $('#cancel_section_pri').show(); }
            return;
        @endif

        // เลือก "หน่วยงานของรัฐ" ต้องบังคับแนบเฉพาะไฟล์ฝั่งรัฐ ไม่บังคับฝั่งเอกชน (และกลับกัน) —
        // มิเรอร์จาก change-info/details.blade.php (Lab) [data-force-required] มาจาก server
        // (true เฉพาะไฟล์ที่ยังไม่เคยแนบไว้)
        function applyRequiredBySection() {
            $('#cancel_section_gov input[type="file"]').prop('required', false);
            $('#cancel_section_pri input[type="file"]').prop('required', false);

            var $active = $('#cancel_section_gov').is(':visible') ? $('#cancel_section_gov')
                        : ($('#cancel_section_pri').is(':visible') ? $('#cancel_section_pri') : null);

            if ($active) {
                $active.find('input[type="file"]').each(function () {
                    if ($(this).data('force-required') == 1) {
                        $(this).prop('required', true);
                    }
                });
            }
        }

        $('#cancel_attachment_type_1').on('ifChecked', function () {
            $('#cancel_section_gov').show();
            $('#cancel_section_pri').hide();
            applyRequiredBySection();
        });

        $('#cancel_attachment_type_2').on('ifChecked', function () {
            $('#cancel_section_gov').hide();
            $('#cancel_section_pri').show();
            applyRequiredBySection();
        });

        // กรณีแก้ไขคำขอที่เคยยื่นไว้ (มี attachment_type เลือกไว้แล้วจาก server) iCheck ไม่ยิง ifChecked
        // ให้เองตอน init ต้องเช็คค่า checked จริงแล้วโชว์ section ที่ตรงกันเอง
        if ($('#cancel_attachment_type_1').is(':checked')) {
            $('#cancel_section_gov').show();
        } else if ($('#cancel_attachment_type_2').is(':checked')) {
            $('#cancel_section_pri').show();
        }
        applyRequiredBySection();

    });
</script>
@endpush
