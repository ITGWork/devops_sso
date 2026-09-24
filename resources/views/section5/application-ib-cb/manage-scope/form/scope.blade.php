    <fieldset class="scheduler-border">
        <legend class="scheduler-border">ขอบข่ายการตรวจสอบ</legend>

        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-5">
                        {!! Form::label('filter_search', 'ค้นหา'.': ', ['class' => 'col-md-3 control-label text-right']) !!}
                        <div class="form-group col-md-9">
                            {!! Form::text('filter_search', null, ['class' => 'form-control', 'placeholder'=>'ค้นหาจากหมวดอุตสาหกรรม/สาขา', 'id' => 'filter_search']); !!}
                        </div>
                    </div>
                    <div class="col-md-7 text-right">
                        <button class="btn btn-success" type="button" data-toggle="modal" data-target="#AddScopeModal">
                             <i class="icon-plus"></i> เพิ่มขอบข่าย
                        </button>
                        <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#MinusScopeModal">
                             <i class="icon-minus"></i> ลดขอบข่าย
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="table-scope">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th width="2%" class="text-center text-white">ลำดับ</th>
                                <th width="20%" class="text-center text-white">หมวดอุตสาหกรรม/สาขา</th>
                                <th width="10%" class="text-center text-white">ISIC NO</th>
                                <th width="13%" class="text-center text-white">เลขที่ มอก.</th>
                                <th width="25%" class="text-center text-white">รายสาขา</th>
                                <th width="15%" class="text-center text-white">ประเภท</th>
                                <th width="10%" class="text-center text-white">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_scope_rows = count($ibcbs->scopes_group) + (isset($pending_scopes) ? count($pending_scopes) : 0);
                                $row_number = 0;
                            @endphp
                            <tr id="row_no_data" @if($total_scope_rows > 0) style="display:none;" @endif>
                                <td colspan="7" class="text-center">ไม่มีข้อมูลในตาราง</td>
                            </tr>
                            @foreach ($ibcbs->scopes_group as $key => $item)
                            @php $row_number++; @endphp
                            <tr>
                                <td class="text-center">{{ $row_number }}</td>
                                <td class="text-center">{!! @$item->bs_branch_group->title !!}</td>
                                <td class="text-center">{!! !empty($item->isic_no)?$item->isic_no:'-' !!}</td>
                                <td class="text-center">{!! !empty($item->ScopeTisNoList)?$item->ScopeTisNoList:'-' !!}</td>
                                <td>{!! !empty($item->ScopeBranchs)?$item->ScopeBranchs:'-' !!}</td>
                                <td class="text-center"><span class="label label-default">ขอบข่ายเดิม</span></td>
                                <td class="text-center">{!! $item->state == 1 ? '<i class="fa fa-check-circle fa-lg text-success"></i>' : '<i class="fa fa-times-circle fa-lg text-danger"></i>' !!}</td>
                            </tr>
                            @endforeach

                            @if(isset($pending_scopes) && count($pending_scopes) > 0)
                                @foreach ($pending_scopes as $key => $item)
                                @php $row_number++; @endphp
                                <tr style="background-color: #fffde7;">
                                    <td class="text-center">{{ $row_number }}</td>
                                    <td class="text-center">{!! @$item->bs_branch_group->title !!}</td>
                                    <td class="text-center">{!! !empty($item->isic_no)?$item->isic_no:'-' !!}</td>
                                    <td class="text-center">{!! !empty($item->ScopeTisNoList)?$item->ScopeTisNoList:'-' !!}</td>
                                    <td>{!! !empty($item->ScopeBranchs)?$item->ScopeBranchs:'-' !!}</td>
                                    <td class="text-center">
                                        @if(@$item->type == 2)
                                            <span class="label label-info">ขอเพิ่มขอบข่าย</span>
                                        @elseif(@$item->type == 3)
                                            <span class="label label-danger">ขอลดขอบข่าย</span>
                                        @else
                                            <span class="label label-warning">{{ $item->ScopeTypeTitle }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="label label-warning"><i class="fa fa-clock-o"></i> ฉบับร่าง</span>
                                        <button type="button" class="btn btn-danger btn-xs btn_remove_pending_scope" title="ลบรายการนี้"><i class="fa fa-trash-o"></i></button>

                                        @if($item->type == 2) {{-- ขอเพิ่ม --}}
                                            <input type="hidden" name="add_scope[{{$row_number}}][branch_group_id]" value="{{ $item->branch_group_id }}">
                                            <input type="hidden" name="add_scope[{{$row_number}}][isic_no]" value="{{ $item->isic_no }}">
                                            @foreach($item->scopes_details as $detail)
                                                <input type="hidden" name="add_scope[{{$row_number}}][branch_id][]" value="{{ $detail->branch_id }}">
                                            @endforeach
                                            @foreach($item->scopes_tis as $tis_index => $tis)
                                                <input type="hidden" name="add_scope[{{$row_number}}][tis][{{$tis_index}}][tis_id]" value="{{ $tis->tis_id }}">
                                                <input type="hidden" name="add_scope[{{$row_number}}][tis][{{$tis_index}}][tis_no]" value="{{ $tis->tis_no }}">
                                                <input type="hidden" name="add_scope[{{$row_number}}][tis][{{$tis_index}}][tis_name]" value="{{ $tis->tis_name }}">
                                            @endforeach
                                        @elseif($item->type == 3) {{-- ขอลด --}}
                                            @php
                                                $live_scope = \App\Models\Section5\IbcbsScope::where('ibcb_id', $ibcbs->id)
                                                                ->where('branch_group_id', $item->branch_group_id)
                                                                ->where('isic_no', $item->isic_no)
                                                                ->first();
                                            @endphp
                                            <input type="hidden" name="minus_scope[{{$row_number}}][scope_id]" value="{{ !empty($live_scope->id)?$live_scope->id:'' }}">
                                            <input type="hidden" name="minus_scope_remarks" value="{{ $item->remarks_reduce }}">
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </fieldset>

    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><h5>เอกสารแนบ</h5></legend>

        @php
            $configs_evidences = DB::table((new App\Models\Config\ConfigsEvidence)->getTable().' AS evidences')
                                    ->leftjoin((new App\Models\Config\ConfigsEvidenceGroup)->getTable().' AS groups', 'groups.id', '=', 'evidences.evidence_group_id')
                                    ->where('groups.id', 1)
                                    ->where('evidences.state', 1)
                                    ->select('evidences.*')
                                    ->orderBy('evidences.ordering')
                                    ->get();

            // $draft_app ใช้ตัวที่ ApplicationIbcbController::ibcbs_show() ส่งมาให้แล้ว (resolve เฉพาะตอนมี
            // application_id ชัดเจนจากปุ่ม "แก้ไข" เท่านั้น) — ห้าม query ซ้ำในนี้แบบ auto-guess "ฉบับร่างล่าสุด"
            // เพราะจะไป override ทับค่าที่ controller ตั้งใจให้ null ตอนยื่นใหม่ (เพิ่ม/ลดขอบข่ายต้องได้ ID ใหม่เสมอ)
        @endphp

        <div class="row">
            <div class="col-md-12">

                @foreach ( $configs_evidences as $key => $evidences )
                    @php
                        $evidences = (object)$evidences;
                        $file_properties = null;

                        if(  !empty($evidences->file_properties)  ){
                            $list = [];
                            foreach ( json_decode($evidences->file_properties) as $value) {
                                $list[] = '.'.$value;
                            }
                            $evidences->file_properties_item =  $list;
                        }

                        $file_properties = !empty($evidences->file_properties_item) ? implode(',', $evidences->file_properties_item ):'';
                        $attachment = null;

                        if( isset($draft_app->id) ){
                            $attachment = App\AttachFile::where('ref_table', (new App\Models\Section5\ApplicationIbcb )->getTable() )
                                            ->where('ref_id', $draft_app->id )
                                            ->when(!empty($evidences->id) ? $evidences->id : null, function ($query, $setting_file_id){
                                                return $query->where('setting_file_id', $setting_file_id);
                                            })
                                            ->first();
                        }
                    @endphp

                    <div class="form-group @if($evidences->required == 1) required @endif">
                        {!! HTML::decode(Form::label('evidence_file_config', ($key+1).'. '.(!empty($evidences->title)?$evidences->title:null).' : ', ['class' => 'col-md-5 control-label'])) !!}
                        <div class="col-md-7">
                            @if( !empty($attachment) )
                                <div class="col-md-4" >
                                    {{-- ไฟล์อยู่บน NAS เท่านั้น ไม่ copy มา cache ในเครื่องเว็บ (HP::getFileStorage() ทำ URL
                                         พังเพราะ APP_URL ใน .env ขาด /public) — ลิงก์ตรงจาก FILESYSTEM_ROOT_URL แทน
                                         เหมือน existing-file.blade.php ที่ใช้ pattern นี้อยู่แล้ว --}}
                                    <a href="{!! rtrim(env('FILESYSTEM_ROOT_URL', ''), '/').'/'.ltrim(preg_replace('/\/+/', '/', $attachment->url), '/') !!}" target="_blank" title="{!! !empty($attachment->filename) ? $attachment->filename : 'ไฟล์แนบ' !!}">
                                        <i class="fa fa-folder-open fa-lg" style="color:#FFC000;" aria-hidden="true"></i>
                                    </a>
                                </div>
                                <div class="col-md-2" >
                                    <a class="btn btn-danger btn-xs show_tag_a" href="{!! url('funtions/get-delete/files/'.($attachment->id).'/'.base64_encode('request-section-5/ibcbs/show/'.$ibcbs->id) ) !!}" title="ลบไฟล์"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                </div>
                            @else
                                {!! Form::hidden('setting_title[]' ,(!empty($evidences->title)?$evidences->title:null), ['required' => false]) !!}
                                {!! Form::hidden('setting_id[]' ,(!empty($evidences->id)?$evidences->id:null), ['required' => false]) !!}
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput">
                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                        <span class="fileinput-filename"></span>
                                    </div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">เลือกไฟล์</span>
                                        <span class="fileinput-exists">เปลี่ยน</span>
                                        <input type="file"
                                            name="evidence_file_config[]"
                                            class="evidence_file_config" @if($evidences->required == 1) required @endif
                                            @if(  !empty($evidences->file_properties) )
                                                accept="{!! $file_properties !!}"
                                                data-accept="{!! base64_encode( $evidences->file_properties) !!}"
                                            @endif
                                            @if(  !empty($evidences->bytes) ) data-max-size="{!! ($evidences->bytes) !!}"  @endif
                                        >
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group repeater-form-other">
                    {!! HTML::decode(Form::label('', 'เอกสารเพิ่มเติม : ', ['class' => 'col-md-5 control-label text-right'])) !!}
                    <div class="col-md-6" data-repeater-list="repeater-file-other">
                        <div class="row" data-repeater-item>
                            <div class="col-md-5">
                                {!! Form::text('file_documents[]', null , ['class' => 'form-control', 'placeholder' => 'กรอกหมายเหตุ (ถ้ามี)']) !!}
                            </div>
                            <div class="col-md-6">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput">
                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                        <span class="fileinput-filename"></span>
                                    </div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">เลือกไฟล์</span>
                                        <span class="fileinput-exists">เปลี่ยน</span>
                                        <input type="file" name="evidence_file_other[]" class="evidence_file_other">
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">ลบ</a>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn_file_remove" data-repeater-delete><i class="fa fa-remove"></i> ลบ</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 col-custom-4">
                        <button type="button" class="btn btn-success pull-left" data-repeater-create><i class="icon-plus"></i> เพิ่ม</button>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>

    <div class="row mt-4 mb-4 text-center">
        <button type="button" class="btn btn-info" id="btn_draft_submit_scope" disabled>
            <i class="fa fa-file-o"></i> ฉบับร่าง
        </button>
        <button type="button" class="btn btn-primary" id="btn_final_submit_scope">
            <i class="fa fa-paper-plane"></i> บันทึก
        </button>
        <a class="btn btn-default" href="{{url('/request-section-5/application-ibcb')}}">
            <i class="fa fa-times"></i> ยกเลิก
        </a>
    </div>

@push('js')
<script>
    $(document).ready(function() {

        // ลบรายการ Draft (เพิ่ม/ลดขอบข่าย) ที่ยังไม่ได้บันทึก
        $(document).on('click', '.btn_remove_pending_scope', function () {
            if(confirm('ยืนยันการลบรายการนี้ออกจากร่างคำขอ?')){
                $(this).closest('tr').remove();
                $('#table-scope tbody tr').not('#row_no_data').each(function(index, tr) {
                    $(tr).find('td:first').text(index + 1);
                });
                if ($('#table-scope tbody tr').not('#row_no_data').length === 0) {
                    $('#row_no_data').show();
                }
            }
        });

        console.log('[DEBUG scope.blade] initial #table-scope tbody rows:', $('#table-scope tbody tr').not('#row_no_data').length);
        console.log('[DEBUG scope.blade] #form_final_submit found?', $('#form_final_submit').length);

        $('#btn_final_submit_scope').click(function () {
            console.log('[DEBUG scope.blade] btn_final_submit_scope clicked. #table-scope tbody rows now:', $('#table-scope tbody tr').not('#row_no_data').length);
            console.log('[DEBUG scope.blade] serialized form data:', $('#form_final_submit').serialize());

            if(confirm('ยืนยันการยื่นคำขอแก้ไขขอบข่ายทั้งหมดใช่หรือไม่?')){

                $.LoadingOverlay("show", {
                    image: "", text: "กำลังส่งข้อมูลเข้าสู่ระบบ กรุณารอสักครู่..."
                });

                var formData = new FormData($('#form_final_submit')[0]);

                $.ajax({
                    method: "POST",
                    url: "{{ url('/request-section-5/application-ibcb/submit-scope-final') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success : function (res){
                        console.log('[DEBUG scope.blade] submit-scope-final response:', res);
                        $.LoadingOverlay("hide");
                        if (res.status == "success") {
                            alert('ยื่นคำขอสำเร็จ !\nเลขที่อ้างอิง: '+res.app_no);
                            window.location.href = "{{ url('/request-section-5/application-ibcb') }}";
                        }else{
                            alert('เกิดข้อผิดพลาด !\n' + res.message);
                        }
                    },
                    error: function(xhr){
                        $.LoadingOverlay("hide");
                        alert('เกิดข้อผิดพลาด ระบบขัดข้อง !');
                    }
                });
            }
        });
    });
</script>
@endpush
