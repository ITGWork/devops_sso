    <fieldset class="scheduler-border">
        <legend class="scheduler-border">ข้อมูลขอรับบริการ</legend>

        <div class="row">
            <div class="col-md-12">
                @php
                    // ขอบข่ายที่หน่วยตรวจสอบนี้ "ได้รับแต่งตั้งแล้ว" — ใช้ชุดเดียวกับคอลัมน์ "มอก.ที่ตรวจสอบได้" ของหน้า list
                    // (section5_labs_scopes ทุกแถวของ lab, group ตาม tis_id) แต่ถือว่า "ได้รับแล้ว" เฉพาะ state=1
                    $lab_scope_all = $labs->scope_standard()->get();
                    $lab_tis_ids   = $lab_scope_all->pluck('tis_id')->filter()->unique()->values();
                    $lab_tis_map   = App\Models\Basic\Tis::whereIn('tb3_TisAutono', $lab_tis_ids)->get()->keyBy('tb3_TisAutono');

                    // เครื่องมือ/ขีดความสามารถเดิม ดึงจากคำขอที่ประกาศราชกิจจาฯ แล้ว (status 99) ของ lab นี้ เฉพาะแถวที่เจ้าหน้าที่
                    // ตรวจผ่าน (audit_result=1) และไม่ใช่แถวขอลด (type=3) — section5_labs_scopes เก็บแค่ test_item_id ไม่มีรายละเอียดเครื่องมือ
                    $lab_ref_nos = $lab_scope_all->pluck('ref_lab_application_no')->filter()->unique()->values();
                    $lab_approved_app_ids = App\Models\Section5\ApplicationLab::where(function ($q) use ($labs, $lab_ref_nos) {
                                                    $q->where('lab_id', $labs->id)->orWhereIn('application_no', $lab_ref_nos);
                                                })
                                                ->where('application_status', 99)
                                                ->pluck('id');
                    $lab_old_tool_rows = App\Models\Section5\ApplicationLabScope::with('test_tools')
                                                ->whereIn('application_lab_id', $lab_approved_app_ids)
                                                ->where('audit_result', 1)
                                                ->where(function ($q) { $q->whereNull('type')->orWhere('type', '!=', 3); })
                                                ->whereNotNull('test_tools_id')
                                                ->orderBy('id')
                                                ->get()
                                                ->groupBy('test_item_id');

                    $lab_panels = [];
                    foreach ($lab_tis_ids as $lab_tid) {
                        $lab_tis = $lab_tis_map->get($lab_tid);
                        $appointed = [];
                        $appointed_items = [];
                        foreach ($lab_scope_all->where('tis_id', $lab_tid)->where('state', 1) as $lab_ls) {
                            $old_rows = collect($lab_old_tool_rows->get($lab_ls->test_item_id, []))
                                            ->unique('test_tools_id')
                                            ->map(function ($r) {
                                                return [
                                                    'tool_id'       => $r->test_tools_id,
                                                    'tool'          => !empty($r->test_tools) ? $r->test_tools->title : '-',
                                                    'no'            => $r->test_tools_no,
                                                    'capacity'      => $r->capacity,
                                                    'range'         => $r->range,
                                                    'true_value'    => $r->true_value,
                                                    'fault_value'   => $r->fault_value,
                                                    'test_duration' => $r->test_duration,
                                                    'test_price'    => $r->test_price,
                                                ];
                                            })->values()->all();
                            $appointed[$lab_ls->test_item_id] = $old_rows;
                            $appointed_items[] = [
                                'id'             => $lab_ls->test_item_id,
                                'no'             => @$lab_ls->test_item->no,
                                'title'          => @$lab_ls->test_item->title,
                                'test_method_id' => @$lab_ls->test_item->test_method_id,
                                'tools'          => [],
                            ];
                        }
                        $lab_panels[$lab_tid] = [
                            'tisno'     => @$lab_tis->tb3_Tisno,
                            'tisname'   => strip_tags((string) @$lab_tis->tb3_TisThainame),
                            'appointed' => (object) $appointed,
                            'appointed_items' => $appointed_items,
                        ];
                    }

                    // รายการ "ขอเพิ่ม" ที่ร่างไว้ (เช่นคำขอที่ถูกตีกลับมาแก้ไข) ไม่แสดงเป็นตารางฉบับร่างด้านล่างแล้ว
                    // แต่เติมค่าเดิมกลับเข้า panel ต่อ มอก. ด้านบน (ติ๊ก/เครื่องมือ/ราคา/วิธีทดสอบ/หมายเหตุ) แทน
                    // → ไม่ต้องกันเครื่องมือซ้ำอีก ($pending_add_keys ว่างเสมอ) ให้เลือกเครื่องมือเดิมใน modal ได้ตามปกติ
                    $pending_add_keys = [];
                    $pending_add_rows = collect($pending_scopes ?? [])
                                            ->filter(function ($p) { return $p->type == 2; })
                                            ->map(function ($p) {
                                                return [
                                                    'tis_id'        => $p->tis_id,
                                                    'test_item_id'  => $p->test_item_id,
                                                    'tool_id'       => $p->test_tools_id,
                                                    'tool_title'    => $p->ToolsName,
                                                    'no'            => $p->test_tools_no,
                                                    'capacity'      => $p->capacity,
                                                    'range'         => $p->range,
                                                    'true_value'    => $p->true_value,
                                                    'fault_value'   => $p->fault_value,
                                                    'test_duration' => $p->test_duration,
                                                    'test_price'    => $p->test_price,
                                                    'price_set'     => $p->test_price_per_set,
                                                    'method_type'   => $p->test_method_type,
                                                    'method_other'  => $p->test_method_other,
                                                    'lab_remark'    => $p->lab_remark,
                                                ];
                                            })->values()->all();
                    $pending_minus_scopes = collect($pending_scopes ?? [])->filter(function ($p) { return $p->type == 3; })->values();

                    // มอก. ที่ lab ยังไม่มี — ใช้ในโหมด "เพิ่ม มอก. ใหม่" (เดิมอยู่ในโมดัล m-add-scope)
                    $lab_new_tis = App\Models\Basic\Tis::select('tb3_Tisno', 'tb3_TisThainame', 'tb3_TisAutono')
                                        ->whereIn('status', ['-1', '0', '1', '2', '3'])
                                        ->whereNotIn('tb3_TisAutono', $lab_tis_ids->all())
                                        ->orderBy('tb3_Tisno')
                                        ->get();

                    // แก้ไขคำขอฉบับร่างเดิม → เลือก radio ให้ตรงกับชนิดของร่าง (ลด / เพิ่ม มอก. เดิม / เพิ่ม มอก. ใหม่)
                    $draft_mode = '';
                    $pend_all = collect($pending_scopes ?? []);
                    if ($pend_all->where('type', 3)->count() > 0) {
                        $draft_mode = 'minus';
                    } elseif ($pend_all->where('type', 2)->count() > 0) {
                        $draft_mode = $lab_tis_ids->contains($pend_all->where('type', 2)->first()->tis_id) ? 'existing' : 'new';
                    }
                @endphp

                {{-- เลือกก่อนว่าจะยื่นแบบไหน (เลือกได้แบบเดียวต่อ 1 คำขอ) เนื้อหาของแต่ละแบบจะขึ้นเมื่อเลือก radio นั้นเท่านั้น --}}
                <div class="row" style="margin-bottom:10px;">
                    <div class="col-md-12">
                        <label class="radio-inline" style="margin-right:25px; font-weight:bold;"><input type="radio" name="lsp_mode" value="existing"> มอก. เดิมที่ได้รับการแต่งตั้ง</label>
                        <label class="radio-inline" style="margin-right:25px; font-weight:bold;"><input type="radio" name="lsp_mode" value="new"> เพิ่ม มอก. ใหม่</label>
                        <label class="radio-inline" style="font-weight:bold;"><input type="radio" name="lsp_mode" value="minus"> ลดขอบข่าย</label>
                    </div>
                </div>

                <div class="row" id="lsp_block_existing" style="display:none;">
                    <div class="col-md-8">
                        <label for="lsp_tis_select" class="col-md-3 control-label text-right">เลือก มอก.:</label>
                        <div class="form-group col-md-9">
                            <select id="lsp_tis_select" class="form-control not_select2" style="width:100%;">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row" id="lsp_block_new" style="display:none;">
                    <div class="col-md-8">
                        <label for="lsp_tis_select_new" class="col-md-3 control-label text-right">เลือก มอก. ใหม่:</label>
                        <div class="form-group col-md-9">
                            <select id="lsp_tis_select_new" class="form-control not_select2" style="width:100%;">
                                <option value=""></option>
                                @foreach($lab_new_tis as $std)
                                    <option value="{{ $std->tb3_TisAutono }}" data-tisno="{{ $std->tb3_Tisno }}" data-tisname="{{ strip_tags($std->tb3_TisThainame) }}">{{ $std->tb3_Tisno }} : {{ strip_tags($std->tb3_TisThainame) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div id="lsp_block_minus" style="display:none; margin-bottom:10px;">
                    <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#MinusScopeModal">
                        <i class="icon-minus"></i> เลือกรายการที่ขอลดขอบข่าย
                    </button>
                    <span class="text-muted">รายการที่เลือกจะแสดงในตารางด้านล่าง</span>
                </div>

                {{-- ขอบข่ายปัจจุบันของ lab: แสดง panel ต่อ มอก. อัตโนมัติ (สร้างด้วย JS ด้านล่างไฟล์) --}}
                <div id="box_lab_scope_panels" style="margin-top:10px;"></div>
                @include('section5.labs.form.scope-panels')

                {{-- ซ่อนทั้งหัวข้อและตารางจนกว่าจะมีรายการขอเพิ่ม มอก. ใหม่/ขอลด (window.reloadScopeTable ใน show.blade.php สลับให้) --}}
                <div id="box_scope_table_wrap" @if($pending_minus_scopes->count() == 0) style="display:none;" @endif>
                <h5 style="margin:20px 0 5px 0;"><b>รายการขอลดขอบข่าย (ฉบับร่าง)</b></h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="table-scope">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th width="2%" class="text-center text-white">ลำดับ</th>
                                <th width="15%" class="text-center text-white">มอก.</th>
                                <th width="55%" class="text-center text-white">รายการทดสอบ</th>
                                <th width="8%" class="text-center text-white">ประเภท</th>
                                <th width="8%" class="text-center text-white">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- แถวข้อความ "ไม่มีข้อมูล" แบบ static (ไม่ใช้ของ DataTables auto-generate แล้ว)
                                 ควบคุมการโชว์/ซ่อนด้วย JS (window.reloadScopeTable ใน show.blade.php) --}}
                            <tr id="table-scope-empty-msg" style="display:none;">
                                <td colspan="5" class="text-center text-muted">ยังไม่มีรายการที่ขอเพิ่ม/ลดขอบข่าย กรุณากดปุ่ม "เพิ่มขอบข่าย" หรือ "ลดขอบข่าย" ด้านบน</td>
                            </tr>
                            @php
                                $row_number = 0;
                                // ขอบข่ายเดิมแสดงเป็น panel ด้านบน (#box_lab_scope_panels) แล้ว ตารางนี้เก็บเฉพาะแถวขอเพิ่ม/ลดที่ร่างไว้
                                $existing_scopes_to_show = collect();
                            @endphp
                            @foreach ($existing_scopes_to_show as $key => $item)
                            @php $row_number++; @endphp
                            <tr>
                                <td class="text-center">{{ $row_number }}</td>
                                <td class="text-center">
                                    {!! @$item->tis_standards->tb3_Tisno !!}
                                    <br>
                                    <span class="text-muted">({!! @$item->tis_standards->tb3_TisThainame !!})</span>
                                </td>
                                <td>
                                    @php
                                        $test_item = $item->test_item;
                                        $test_item_main = $item->test_item_main;
                                    @endphp

                                    {!! (!empty($test_item->no) ? 'ข้อ ' . $test_item->no . ' ' : '') . @$test_item->title !!}

                                    @if(!empty($test_item_main->id) && @$test_item_main->id != @$test_item->id)
                                        <br>
                                        <span class="text-muted">
                                            <em>(ภายใต้หัวข้อทดสอบ: {!! (!empty($test_item_main->no) ? 'ข้อ ' . $test_item_main->no . ' ' : '') . @$test_item_main->title !!})</em>
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center"><span class="label label-default">ขอบข่ายเดิม</span></td>
                                <td class="text-center">{!! $item->StateIcon !!}</td>
                            </tr>
                            @endforeach

                            {{-- ตารางนี้เก็บเฉพาะแถว "ขอลด" ที่ร่างไว้ — แถว "ขอเพิ่ม" ย้ายไปเติมใน panel ต่อ มอก. ด้านบนแล้ว --}}
                            @if($pending_minus_scopes->count() > 0)
                                @foreach ($pending_minus_scopes as $key => $item)
                                @php $row_number++; @endphp
                                <tr style="background-color: #fffde7;">
                                    <td class="text-center">{{ $row_number }}</td>
                                    <td class="text-center">
                                        {!! @$item->standards->tb3_Tisno !!}
                                        <br>
                                        <span class="text-muted">({!! @$item->standards->tb3_TisThainame !!})</span>
                                    </td>
                                    <td>
                                        @php
                                            $test_item = $item->test_items;
                                            $test_item_main = $item->test_item_main;
                                        @endphp

                                        {!! (!empty($test_item->no) ? 'ข้อ ' . $test_item->no . ' ' : '') . @$test_item->title !!}

                                        @if(!empty($test_item_main->id) && @$test_item_main->id != @$test_item->id)
                                            <br>
                                            <span class="text-muted">
                                                <em>(ภายใต้หัวข้อทดสอบ: {!! (!empty($test_item_main->no) ? 'ข้อ ' . $test_item_main->no . ' ' : '') . @$test_item_main->title !!})</em>
                                            </span>
                                        @endif

                                        @if(@$item->type == 2)
                                            <div class="table-responsive" style="margin-top:6px;">
                                                <table class="table table-condensed table-bordered" style="margin:0;font-size:12px;background:#fff;">
                                                    <thead>
                                                        <tr>
                                                            <th>เครื่องมือที่ใช้</th>
                                                            <th>รหัส/หมายเลข</th>
                                                            <th>ขีดความสามารถ</th>
                                                            <th>ช่วงการใช้งาน</th>
                                                            <th>ความละเอียดที่อ่านได้</th>
                                                            <th>ความคลาดเคลื่อนที่ยอมรับ</th>
                                                            <th>ระยะการทดสอบ(วัน)</th>
                                                            <th>ค่าใช้จ่าย/ชุดละ</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{ $item->ToolsName ?? '-' }}</td>
                                                            <td>{{ $item->test_tools_no ?? '-' }}</td>
                                                            <td>{{ $item->capacity ?? '-' }}</td>
                                                            <td>{{ $item->range ?? '-' }}</td>
                                                            <td>{{ $item->true_value ?? '-' }}</td>
                                                            <td>{{ $item->fault_value ?? '-' }}</td>
                                                            <td>{{ $item->test_duration ?? '-' }}</td>
                                                            <td>{{ $item->test_price ?? '-' }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                @php $method_titles = [1 => 'ตาม มอก.', 2 => 'วิธีเทียบเท่า', 3 => 'อื่นๆ']; @endphp
                                                <div class="small" style="margin-top:4px;">
                                                    <b>ราคาค่าทดสอบ/ต่อชุดตัวอย่าง:</b> {{ $item->test_price_per_set ?: '-' }}
                                                    &nbsp;|&nbsp; <b>วิธีทดสอบของ LAB:</b> {{ isset($method_titles[$item->test_method_type]) ? $method_titles[$item->test_method_type] : '-' }}{{ in_array($item->test_method_type, [2, 3]) && $item->test_method_other ? ' ('.$item->test_method_other.')' : '' }}
                                                    &nbsp;|&nbsp; <b>หมายเหตุ:</b> {{ $item->lab_remark ?: '-' }}
                                                </div>
                                            </div>
                                        @endif
                                    </td>
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
                                            <input type="hidden" name="add_scope_tis_id[]" value="{{ $item->tis_id }}">
                                            <input type="hidden" name="add_scope_tis_tisno[]" value="{{ $item->tis_tisno }}">
                                            <input type="hidden" name="add_scope_test_item_id[]" value="{{ $item->test_item_id }}">
                                            <input type="hidden" name="add_scope_test_tools_id[]" value="{{ $item->test_tools_id }}">
                                            <input type="hidden" name="add_scope_test_tools_custom_name[]" value="">
                                            <input type="hidden" name="add_scope_test_tools_no[]" value="{{ $item->test_tools_no }}">
                                            <input type="hidden" name="add_scope_capacity[]" value="{{ $item->capacity }}">
                                            <input type="hidden" name="add_scope_range[]" value="{{ $item->range }}">
                                            <input type="hidden" name="add_scope_true_value[]" value="{{ $item->true_value }}">
                                            <input type="hidden" name="add_scope_fault_value[]" value="{{ $item->fault_value }}">
                                            <input type="hidden" name="add_scope_test_duration[]" value="{{ $item->test_duration }}">
                                            <input type="hidden" name="add_scope_test_price[]" value="{{ $item->test_price }}">
                                            <input type="hidden" name="add_scope_test_price_set[]" value="{{ $item->test_price_per_set }}">
                                            <input type="hidden" name="add_scope_test_method_type[]" value="{{ $item->test_method_type }}">
                                            <input type="hidden" name="add_scope_test_method_other[]" value="{{ $item->test_method_other }}">
                                            <input type="hidden" name="add_scope_lab_remark[]" value="{{ $item->lab_remark }}">
                                            <input type="hidden" name="add_scope_item_fields[]" value="{{ (!empty($item->test_price_per_set) || !empty($item->test_method_type)) ? 1 : 0 }}">
                                        @elseif($item->type == 3) {{-- ขอลด --}}
                                            <input type="hidden" name="minus_scope_id[]" value="{{ $item->id }}"> {{-- This might need careful matching if it's already from LabsScope --}}
                                            <input type="hidden" name="minus_scope_tis_id[]" value="{{ $item->tis_id }}">
                                            <input type="hidden" name="minus_scope_tis_tisno[]" value="{{ $item->tis_tisno }}">
                                            <input type="hidden" name="minus_scope_test_item_id[]" value="{{ $item->test_item_id }}">
                                            <input type="hidden" name="minus_scope_remarks_reduce[]" value="{{ $item->remarks_reduce }}">
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                </div>{{-- /#box_scope_table_wrap --}}

            </div>
        </div>
    </fieldset>

    @php
        // ข้อมูล "ขอบข่ายเดิม" ของทุก มอก. ที่ lab นี้มี (ไม่ใช่แค่ตัวที่กำลัง pending ตอนโหลดหน้า) เตรียมไว้ให้ฝั่ง JS
        // ใช้แทรกแถว "ขอบข่ายเดิม" เข้าตาราง #table-scope เองตอนผู้ใช้กด "เพิ่มขอบข่าย" ของ มอก. นั้นแบบ
        // real-time (การเพิ่ม/ลดขอบข่ายในตารางเป็นแค่ client-side draft ก่อนกด "บันทึก" จริง ไม่ได้ reload หน้า
        // จึงต้องมีข้อมูลนี้พร้อมใช้ฝั่ง JS ตั้งแต่โหลดหน้า จะ query จาก server ใหม่ตอนนั้นไม่ได้)
        $existing_scope_rows_by_tis = [];
        foreach ($labs->scopes_group as $item) {
            $test_item = $item->test_item;
            $test_item_main = $item->test_item_main;

            $row_html  = '<tr class="existing-scope-row" data-existing-tis="' . $item->tis_id . '">';
            $row_html .= '<td class="text-center">##ROWNO##</td>';
            $row_html .= '<td class="text-center">' . @$item->tis_standards->tb3_Tisno
                       . '<br><span class="text-muted">(' . @$item->tis_standards->tb3_TisThainame . ')</span></td>';
            $row_html .= '<td>' . (!empty($test_item->no) ? 'ข้อ ' . $test_item->no . ' ' : '') . @$test_item->title;
            if (!empty($test_item_main->id) && @$test_item_main->id != @$test_item->id) {
                $row_html .= '<br><span class="text-muted"><em>(ภายใต้หัวข้อทดสอบ: '
                           . (!empty($test_item_main->no) ? 'ข้อ ' . $test_item_main->no . ' ' : '') . @$test_item_main->title
                           . ')</em></span>';
            }
            $row_html .= '</td>';
            $row_html .= '<td class="text-center"><span class="label label-default">ขอบข่ายเดิม</span></td>';
            $row_html .= '<td class="text-center">' . $item->StateIcon . '</td>';
            $row_html .= '</tr>';

            $existing_scope_rows_by_tis[$item->tis_id][] = $row_html;
        }
    @endphp
    <script>
        window.existingScopeRowsByTis = @json($existing_scope_rows_by_tis);
    </script>

    <fieldset class="scheduler-border">
        <legend class="scheduler-border"><h5>เอกสารแนบ</h5></legend>
        
        @php
            $configs_evidences = DB::table((new App\Models\Config\ConfigsEvidence)->getTable().' AS evidences')
                                    ->leftjoin((new App\Models\Config\ConfigsEvidenceGroup)->getTable().' AS groups', 'groups.id', '=', 'evidences.evidence_group_id')
                                    ->where('groups.id', 3)
                                    ->where('evidences.state', 1)
                                    ->select('evidences.*')
                                    ->orderBy('evidences.ordering')
                                    ->get();

            // $draft_app ใช้ตัวที่ ApplicationLabController::labs_show() ส่งมาให้แล้ว (resolve เฉพาะตอนมี
            // application_id ชัดเจนจากปุ่ม "แก้ไข" เท่านั้น) — เดิม view นี้ query ซ้ำเองแบบ auto-guess
            // "ฉบับร่างล่าสุด" ซึ่งจะไปทับค่าที่ controller ตั้งใจให้ null ตอนยื่นใหม่ (บั๊กเดียวกับที่แก้ที่
            // controller ไปแล้ว แต่ query ซ้ำในนี้ยังไม่ได้ลบออก จะ override ทับกลับไปเป็นแบบเดิม)
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
                            $attachment = App\AttachFile::where('ref_table', (new App\Models\Section5\ApplicationLab )->getTable() )
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
                                    <a href="{!! HP::getFileStorage($attachment->url) !!}" target="_blank" title="{!! !empty($attachment->filename) ? $attachment->filename : 'ไฟล์แนบ' !!}">
                                        <i class="fa fa-folder-open fa-lg" style="color:#FFC000;" aria-hidden="true"></i>
                                    </a>
                                </div>
                                <div class="col-md-2" >
                                    <a class="btn btn-danger btn-xs show_tag_a" href="{!! url('funtions/get-delete/files/'.($attachment->id).'/'.base64_encode('request-section-5/application-lab/'.$draft_app->id.'/edit') ) !!}" title="ลบไฟล์"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
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
                                {!! Form::text('file_documents', null , ['class' => 'form-control', 'placeholder' => 'กรอกหมายเหตุ (ถ้ามี)']) !!}
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
                                        <input type="file" name="evidence_file_other" class="evidence_file_other">
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
        <button type="button" class="btn btn-info" id="btn_draft_submit_scope">
            <i class="fa fa-file-o"></i> ฉบับร่าง
        </button>
        <button type="button" class="btn btn-primary" id="btn_final_submit_scope">
            <i class="fa fa-paper-plane"></i> บันทึก
        </button>
        <a class="btn btn-default" href="{{url('/section5/labs_list')}}">
            <i class="fa fa-times"></i> ยกเลิก
        </a>
    </div>

@push('js')
<script src="{{asset('plugins/components/jasny-bootstrap/js/jasny-bootstrap.js')}}"></script>
<script>
    $(document).ready(function() {

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

        $('#btn_add_attach').click(function () {
            var tr = $('#table_attach_files tbody tr:first').clone();
            tr.find('input[type="text"]').val('');
            tr.find('.fileinput').fileinput('clear');
            tr.find('.btn-success').removeClass('btn-success').addClass('btn-danger class_remove_attach').html('<i class="fa fa-remove"></i> ลบ');
            $('#table_attach_files tbody').append(tr);
        });

        $(document).on('click', '.class_remove_attach', function () {
            $(this).closest('tr').remove();
        });

        // ลบรายการ Draft (เพิ่ม/ลดขอบข่าย) ที่ยังไม่ได้บันทึก
        $(document).on('click', '.btn_remove_pending_scope', function () {
            if(confirm('ยืนยันการลบรายการนี้ออกจากร่างคำขอ?')){
                var $row  = $(this).closest('tr');
                var tis_id = $row.find('input[name="add_scope_tis_id[]"], input[name="minus_scope_tis_id[]"]').val();
                $row.remove();

                // ถ้าไม่มีรายการ pending ของ มอก. นี้เหลืออยู่แล้ว ให้ลบแถว "ขอบข่ายเดิม" ของ มอก. นั้นออกด้วย
                // (กติกาเดียวกับตอนโหลดหน้า: แสดงขอบข่ายเดิมเฉพาะ มอก. ที่มี pending อยู่เท่านั้น)
                if (tis_id) {
                    var stillHasPending = $('#table-scope tbody tr.pending-scope-row').filter(function () {
                        return $(this).find('input[name="add_scope_tis_id[]"], input[name="minus_scope_tis_id[]"]').val() == tis_id;
                    }).length > 0;
                    if (!stillHasPending) {
                        $('#table-scope tbody tr.existing-scope-row[data-existing-tis="' + tis_id + '"]').remove();
                    }
                }

                // Re-index row numbers
                $('#table-scope tbody tr').not('#table-scope-empty-msg').each(function(index, tr) {
                    $(tr).find('td:first').text(index + 1);
                });

                if (window.reloadScopeTable) { window.reloadScopeTable(); }
            }
        });

        $('#btn_final_submit_scope').click(function () {
            var formEl = $('#form_final_submit')[0];

            // panel ที่พับอยู่ทำให้ browser โฟกัสช่อง required ที่ว่างไม่ได้ (reportValidity เงียบ) จึงกางทุก panel ก่อนเช็ค
            $('#box_lab_scope_panels .panel-collapse').addClass('in').css('height', '');

            // เรียก $(form).submit() ตรงๆ ด้วย JS ไม่ทำให้ HTML5 required validation ทำงาน
            // (ต่างจากการคลิก submit button จริงๆ) ต้องเช็ค checkValidity() เองก่อนเสมอ ไม่งั้นฟิลด์ที่
            // required (เช่น เอกสารแนบข้อ 1-4) ว่างก็ยังกด "บันทึก" ผ่านได้โดยไม่มีการเตือนใดๆ
            // รายการที่ติ๊ก "ขอรับการแต่งตั้ง" ต้องมีเครื่องมือ/ราคา/วิธีทดสอบครบ
            if (window.lspValidate) {
                var lspMsg = window.lspValidate();
                if (lspMsg) { alert(lspMsg); return; }
            }

            // ต้องติ๊กเลือกรายการที่จะขอเพิ่ม (หรือมีแถวขอลด) อย่างน้อย 1 รายการ — เฉพาะช่องที่ enable เท่านั้นที่ถูกส่งจริง
            // (เครื่องมือเดิมที่ได้รับแล้วเป็นแบบอ่านอย่างเดียว ไม่มี input จึงไม่ถูกยื่นซ้ำ)
            var addCount   = $('#form_final_submit input[name="add_scope_tis_id[]"]:not(:disabled)').length;
            var minusCount = $('#form_final_submit input[name="minus_scope_tis_id[]"]:not(:disabled)').length;
            if (addCount === 0 && minusCount === 0) {
                alert('กรุณาติ๊กเลือกรายการทดสอบ/เครื่องมือที่ต้องการขอเพิ่ม หรือกดปุ่ม "ลดขอบข่าย" อย่างน้อย 1 รายการ ก่อนกดบันทึก');
                return;
            }
            if (addCount > 0 && minusCount > 0) {
                alert('กรุณายื่นคำขอเพิ่มขอบข่าย และคำขอลดขอบข่าย แยกคำขอกัน ไม่สามารถยื่นรวมกันในคำขอเดียวได้');
                return;
            }

            if (!formEl.checkValidity()) {
                formEl.reportValidity();
                return;
            }

            if(confirm('ยืนยันการยื่นคำขอแก้ไขขอบข่ายทั้งหมด พร้อมเอกสารแนบใช่หรือไม่?')){
                $.LoadingOverlay("show", {
                    image: "", text: "กำลังส่งข้อมูลเข้าสู่ระบบ กรุณารอสักครู่..."
                });
                formEl.submit();
            }
        });
    });
</script>
@endpush
