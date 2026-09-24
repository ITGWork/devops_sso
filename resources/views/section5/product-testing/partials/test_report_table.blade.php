<style>
    #tbl-test-result td, #tbl-test-result th { vertical-align: middle; }
    #tbl-test-result .cell-wrap { min-width: 120px; }
    #tbl-test-result .input-sm { padding: 4px 6px; height: 28px; }
</style>


<style>
    /* เจาะเฉพาะ select_multiple ที่เราติด class นี้เท่านั้น */
    #tbl-test-result select.js-force-native-multi{
        display:block !important;
        visibility:visible !important;
        opacity:1 !important;
        height:auto;
        min-height:32px;
        position:relative;
        z-index:2;
        background:#fff;
    }

    /* ถ้า plugin สร้าง container มาทับ "ติดกับ select ของเรา" ให้ซ่อนตัวที่สร้างมาทับ */
    #tbl-test-result select.js-force-native-multi + .select2-container,
    #tbl-test-result select.js-force-native-multi + .bootstrap-select,
    #tbl-test-result select.js-force-native-multi + .dropdown,
    #tbl-test-result select.js-force-native-multi + .chosen-container{
        display:none !important;
    }
</style>


@php
    // แยกตารางตามรุ่นผลิตภัณฑ์ (product_detail_id) เพราะรายการทดสอบเดียวกันอาจต้องทำผลแยกกันคนละรุ่น
    $allTestItems = $test_items;
    $groupedTestItemsByDetail = $allTestItems->groupBy(function($x) { return $x->product_detail_id ?? 0; });
    $detailIdsForLabel = $groupedTestItemsByDetail->keys()->filter(function($v) { return $v != 0; })->values();
    $productDetailLabels = $detailIdsForLabel->isNotEmpty()
        ? \App\Models\Elicense\Rform\ProductDetail::on('mysql_elicense')->whereIn('id', $detailIdsForLabel)->pluck('product_detail', 'id')
        : collect();
@endphp

<div id="tbl-test-result">
@foreach($groupedTestItemsByDetail as $groupDetailId => $test_items)
    @php
        // max จำนวนครั้งในทั้งหน้า เพื่อให้ header ไม่พัง
        $maxTimes = 0;
        foreach ($test_items as $x) {
            $n = (int)($x->amount_test_list ?? 0);
            if ($n > $maxTimes) $maxTimes = $n;
        }
        if ($maxTimes <= 0) $maxTimes = 8; // fallback ตามของเดิม
    @endphp

    @if($groupDetailId != 0)
        <h4 style="margin-top:20px; margin-bottom:10px;">
            รุ่นผลิตภัณฑ์: {{ $productDetailLabels[$groupDetailId] ?? ('#' . $groupDetailId) }}
        </h4>
    @endif

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="bg-primary">
            {{-- ✅ header แบบ snippet: ซ้าย 7 + ขวา "ผลทดสอบ" colspan maxTimes --}}
            <tr>
                <th style="min-width:220px; color:#fff;">หัวข้อ/รายการทดสอบ</th>
                <th style="min-width:120px; color:#fff;">ข้อ</th>
                <th style="min-width:120px; color:#fff;">หน่วย</th>
                <th style="min-width:260px; color:#fff;">เกณฑ์กำหนด</th>
                <th style="min-width:220px; color:#fff;">อยู่ภายใต้หัวข้อทดสอบ</th>
                <th style="min-width:220px; color:#fff;">วิธีทดสอบ</th>
                <th style="min-width:220px; color:#fff;">เครื่องมือทดสอบ</th>

                <th colspan="{{ $maxTimes }}" style="text-align:center; color:#fff;">
                    ผลทดสอบ
                </th>
                <th style="min-width:140px; text-align:center; color:#fff;">
                    ผลสรุป
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse($test_items as $ti)

                @php
                    // ✅ ยึด logic เดิมเพื่อไม่พัง
                    $times = (int)($ti->amount_test_list ?? 0);
                    if ($times <= 0) $times = 8; // fallback

                    $type = trim((string)($ti->format_result ?? 'text'));

                    // JSON detail (รองรับทั้ง string JSON และ array ที่ถูก cast มาแล้ว)
                    $cfg = [];
                    if (!empty($ti->format_result_detail)) {
                        if (is_array($ti->format_result_detail)) {
                            $cfg = $ti->format_result_detail;
                        } else {
                            $tmp = json_decode($ti->format_result_detail, true);
                            if (is_array($tmp)) $cfg = $tmp;
                        }
                    }

                    // ✅ เตรียมค่าพื้นฐาน + กัน Undefined variable
                    $digit = isset($cfg['digit']) ? (int)$cfg['digit'] : null;

                    $min = isset($cfg['min']) ? $cfg['min'] : null;
                    $max = isset($cfg['max']) ? $cfg['max'] : null;
                    $opStart = '>=';
                    $opEnd   = '<=';

                    // เกณฑ์แบบมีโครงสร้างจากกลุ่ม bcertify (7.1) — ใช้แทนค่า min/max ของ 7.2 (ข้างบน) ถ้ามี
                    // ตั้งไว้ ใช้กับช่องกรอกค่าเดียว (integer/decimal) เท่านั้น เพราะ bcertify เก็บเป็นช่วง
                    // เดียว ไม่ใช่ค่าที่วัดได้เป็นช่วง (integer_range/decimal_range ยัง fallback ไปใช้ 7.2)
                    // และยังไม่รองรับ reference_tolerance (ต้องมีค่าอ้างอิงที่ยังไม่มีในระบบ) —
                    // ดูหัวข้อ 8.4 ข้อ 2.2/2.3 ของ docs/tiw/std-test-item-elicense-groups-exploration.md
                    // (devops_center)
                    $bcertifyType = $ti->bcertify_comparison_type ?? null;
                    if (!empty($bcertifyType) && in_array($type, ['integer', 'decimal'], true)) {
                        if ($bcertifyType === 'direct_range') {
                            $min = $ti->bcertify_value_start;
                            $max = $ti->bcertify_value_end;
                            $opStart = $ti->bcertify_operator_start ?: '>=';
                            $opEnd   = $ti->bcertify_operator_end ?: '<=';
                        } elseif ($bcertifyType === 'upper_limit') {
                            $min = null;
                            $max = $ti->bcertify_value_end;
                            $opEnd = $ti->bcertify_operator_end ?: '<=';
                        } elseif ($bcertifyType === 'lower_limit') {
                            $min = $ti->bcertify_value_start;
                            $max = null;
                            $opStart = $ti->bcertify_operator_start ?: '>=';
                        } elseif ($bcertifyType === 'exact') {
                            $min = $ti->bcertify_value_end;
                            $max = $ti->bcertify_value_end;
                            $opStart = '=';
                            $opEnd   = '=';
                        }
                        // reference_tolerance: ยังไม่รองรับ -> $min/$max คงค่าจาก 7.2 เดิมไว้ (ไม่ทับ)
                    }

                    $minStart = isset($cfg['min_start']) ? $cfg['min_start'] : null;
                    $maxStart = isset($cfg['max_start']) ? $cfg['max_start'] : null;
                    $minEnd   = isset($cfg['min_end']) ? $cfg['min_end'] : null;
                    $maxEnd   = isset($cfg['max_end']) ? $cfg['max_end'] : null;

                    // options (non-mix)
                    $optionListRaw = isset($cfg['option_list']) ? $cfg['option_list'] : '';
                    $options = [];
                    if (is_string($optionListRaw) && trim($optionListRaw) !== '') {
                        $tmpOps = array_map('trim', explode(',', $optionListRaw));
                        $options = array_values(array_filter($tmpOps, function($v){
                            return $v !== '';
                        }));
                    }

                    // step สำหรับ number
                    $step = '1';
                    if ($type === 'decimal' || $type === 'decimal_range') {
                        if ($digit !== null && $digit >= 0) {
                            $step = ($digit === 0) ? '1' : ('0.' . str_repeat('0', $digit-1) . '1');
                        } else {
                            $step = '0.01';
                        }
                    }

                    // ✅ กรณี mix: cfg จะเป็น array ของ sub-config
                    $mixList = [];
                    if ($type === 'mix') {
                        $mixList = is_array($cfg) ? $cfg : [];
                    }

                    // เจ้าหน้าที่ยังไม่ได้ตั้ง "รูปแบบข้อมูลผลทดสอบ" (7.2, format_result) ให้รายการนี้ — ยึด
                    // format_result เป็นตัวตัดสินเดียว (ยืนยันกับผู้ใช้แล้ว 2026-09-09: แค่ผูกรายการเข้ากลุ่ม
                    // bcertify หรือมีเกณฑ์ 7.1 อย่างเดียวไม่นับว่า "ตั้งค่าแล้ว" ถ้ายังไม่มี format_result)
                    // แสดงแค่ชื่อ/ข้อของรายการ (มาจากขั้นที่ 1 ซึ่งมีเสมออยู่แล้ว) ส่วนช่องอื่นที่ต้องพึ่งข้อมูล
                    // ที่ยังไม่ได้ตั้งให้บอกตรงๆ ว่ายังไม่ได้ตั้งค่า แทนช่องกรอกที่กรอกอะไรก็ได้แบบไม่มีความหมาย
                    // ห้องแล็บยังส่งรายงานผลได้ตามปกติผ่านการแนบไฟล์ PDF (บังคับแนบอยู่แล้ว) — ดูหัวข้อ 9/12
                    // ของ docs/tiw/std-test-item-elicense-groups-exploration.md (devops_center)
                    $isUnconfigured = empty($ti->format_result);
                    $notSetLabel = '<span class="text-muted"><em>ยังไม่ได้ตั้งค่าโดยเจ้าหน้าที่</em></span>';
                @endphp

                <tr class="js-test-row" data-times="{{ $times }}" data-test_item_id="{{ $ti->id }}" data-unconfigured="{{ $isUnconfigured ? '1' : '0' }}">
                    {{-- ✅ ซ้าย 7 ช่อง --}}
                    <td style="white-space:pre-wrap;">{{ $ti->title ?? '-' }}</td>
                    <td>{{ $ti->no ?? '-' }}</td>
                    <td>{!! !empty($ti->unit_text) ? e($ti->unit_text) : ($isUnconfigured ? $notSetLabel : ($ti->unit_id ?? '-')) !!}</td>
                    {{-- ใช้เกณฑ์แบบมีโครงสร้างจากกลุ่ม bcertify ก่อนถ้ามีตั้งไว้ (7.1) ไม่มีค่อย fallback
                         ไปใช้ค่า criteria แบบข้อความอิสระเดิม (7.2) — ดูหัวข้อ 8 ของ
                         docs/tiw/std-test-item-elicense-groups-exploration.md (devops_center) --}}
                    <td style="white-space:pre-wrap;">
                        @if(!empty($ti->bcertify_criteria_text))
                            {{ $ti->bcertify_criteria_text }}
                            <span class="label label-info" style="font-size:10px;vertical-align:middle;">ตามกลุ่ม bcertify</span>
                        @elseif(!empty($ti->criteria))
                            {{ $ti->criteria }}
                        @elseif($isUnconfigured)
                            {!! $notSetLabel !!}
                        @else
                            -
                        @endif
                    </td>
                    <td style="white-space:pre-wrap;">{!! !empty($ti->parent_text) ? e($ti->parent_text) : ($isUnconfigured ? $notSetLabel : ($ti->parent_id ?? '-')) !!}</td>
                    <td style="white-space:pre-wrap;">{!! !empty($ti->test_method_text) ? e($ti->test_method_text) : ($isUnconfigured ? $notSetLabel : ($ti->test_method_id ?? '-')) !!}</td>
                    <td style="white-space:pre-wrap;">{!! !empty($ti->tool_text) ? e($ti->tool_text) : ($isUnconfigured ? $notSetLabel : ($ti->tool_id ?? '-')) !!}</td>

                    {{-- ✅ ฝั่งขวา: แบบ Snippet --}}
                    @for($seq=1;$seq<=$maxTimes;$seq++)
                        @php
                            $detailIdKey = $ti->product_detail_id ?? 0;
                            $old = isset($existing_results[$detailIdKey][$ti->id][$seq]) ? $existing_results[$detailIdKey][$ti->id][$seq] : null;
                            $oldMeasured = is_array($old) && array_key_exists('measured_value', $old) ? $old['measured_value'] : null;
                            $oldResult   = is_array($old) && array_key_exists('item_result', $old) ? $old['item_result'] : null;

                            $isDisabledCell = ($seq > $times);

                            // ✅ สำหรับ mix: ถ้าค่าเดิมเก็บเป็น JSON ให้ decode เพื่อเติม autofill
                            $oldMeasuredMix = [];
                            if ($type === 'mix') {
                                if (is_array($oldMeasured)) {
                                    $oldMeasuredMix = $oldMeasured;
                                } elseif (is_string($oldMeasured) && trim($oldMeasured) !== '') {
                                    $tmp2 = json_decode($oldMeasured, true);
                                    if (is_array($tmp2)) $oldMeasuredMix = $tmp2;
                                }
                                if (!is_array($oldMeasuredMix)) $oldMeasuredMix = [];
                            }
                        @endphp

                        <td class="js-test-cell"
                            style="vertical-align:top; min-width:220px;"
                            data-test_item_id="{{ $ti->id }}"
                            data-seq="{{ $seq }}"
                            data-type="{{ $type }}"
                            data-digit="{{ $digit !== null ? $digit : '' }}"
                            data-min="{{ $min !== null ? $min : '' }}"
                            data-max="{{ $max !== null ? $max : '' }}"
                            data-min_start="{{ $minStart !== null ? $minStart : '' }}"
                            data-max_start="{{ $maxStart !== null ? $maxStart : '' }}"
                            data-min_end="{{ $minEnd !== null ? $minEnd : '' }}"
                            data-max_end="{{ $maxEnd !== null ? $maxEnd : '' }}"
                            data-options="{{ !empty($options) ? e(implode(',', $options)) : '' }}"
                        >
                            @if($isDisabledCell)
                                <span class="text-muted">-</span>
                            @else
                                {{-- หัวในช่อง: การทดสอบครั้งที่ X --}}
                                <div class="m-b-10">
                                    <input type="text"
                                           class="form-control input-sm"
                                           value="การทดสอบครั้งที่ {{ $seq }}"
                                           readonly>
                                </div>

                                @if(isset($readonly) && $readonly)
                                    @if($oldMeasured !== null || $oldResult !== null)
                                        <div style="font-weight:bold; color:#000; white-space:pre-wrap;">{{ $oldMeasured ?: '-' }}</div>
                                        <div style="margin-top: 5px;">
                                            @if($oldResult == 'pass')
                                                <span class="label label-success"><i class="fa fa-check"></i> ผ่าน</span>
                                            @elseif($oldResult == 'fail')
                                                <span class="label label-danger"><i class="fa fa-times"></i> ไม่ผ่าน</span>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    @else
                                        -
                                    @endif
                                @elseif($isUnconfigured)
                                    {{-- ยังไม่ได้ตั้งค่าทั้ง 7.2 และ 7.1 — ไม่ให้กรอกค่า/เลือกผลแบบไม่มี
                                         ความหมาย บอกตรงๆ แทน ห้องแล็บยังส่งรายงานผลได้ผ่านการแนบ PDF --}}
                                    {!! $notSetLabel !!}
                                @else

                                    {{-- ✅ กรณี mix: สร้างหลาย control ตาม cfg --}}
                                    @if($type === 'mix')

                                        @foreach($mixList as $k => $sub)
                                            @php
                                                $subType = isset($sub['format_result_mix']) ? trim((string)$sub['format_result_mix']) : 'text';

                                                $subDigit = isset($sub['digit']) ? (int)$sub['digit'] : null;

                                                $subMin = isset($sub['min']) ? $sub['min'] : null;
                                                $subMax = isset($sub['max']) ? $sub['max'] : null;

                                                $subMinStart = isset($sub['min_start']) ? $sub['min_start'] : null;
                                                $subMaxStart = isset($sub['max_start']) ? $sub['max_start'] : null;
                                                $subMinEnd   = isset($sub['min_end']) ? $sub['min_end'] : null;
                                                $subMaxEnd   = isset($sub['max_end']) ? $sub['max_end'] : null;

                                                $subOptions = [];
                                                $subOptionRaw = isset($sub['option_list']) ? $sub['option_list'] : '';
                                                if (is_string($subOptionRaw) && trim($subOptionRaw) !== '') {
                                                    $tmpOps2 = array_map('trim', explode(',', $subOptionRaw));
                                                    $subOptions = array_values(array_filter($tmpOps2, function($v){
                                                        return $v !== '';
                                                    }));
                                                }

                                                $subStep = '1';
                                                if ($subType === 'decimal' || $subType === 'decimal_range') {
                                                    if ($subDigit !== null && $subDigit >= 0) {
                                                        $subStep = ($subDigit === 0) ? '1' : ('0.' . str_repeat('0', $subDigit-1) . '1');
                                                    } else {
                                                        $subStep = '0.01';
                                                    }
                                                }

                                                // ค่าเดิมของแต่ละชิ้นใน mix (ถ้ามี)
                                                $subOld = array_key_exists($k, $oldMeasuredMix) ? $oldMeasuredMix[$k] : null;
                                                $subSelectedVal = old("measured_mix.$detailIdKey.$ti->id.$seq.$k", $subOld);

                                                $subSelectedArr = [];
                                                if (is_array($subSelectedVal)) {
                                                    $subSelectedArr = $subSelectedVal;
                                                } elseif (is_string($subSelectedVal)) {
                                                    $subSelectedArr = array_filter(array_map('trim', explode(',', $subSelectedVal)));
                                                }
                                            @endphp

                                            <div class="mix-block" style="margin-bottom:6px;"
                                                data-mix-index="{{ $k }}"
                                                data-mix-type="{{ $subType }}"
                                                data-mix-digit="{{ $subDigit !== null ? $subDigit : '' }}"
                                                data-mix-min="{{ $subMin !== null ? $subMin : '' }}"
                                                data-mix-max="{{ $subMax !== null ? $subMax : '' }}"
                                                data-mix-min_start="{{ $subMinStart !== null ? $subMinStart : '' }}"
                                                data-mix-max_start="{{ $subMaxStart !== null ? $subMaxStart : '' }}"
                                                data-mix-min_end="{{ $subMinEnd !== null ? $subMinEnd : '' }}"
                                                data-mix-max_end="{{ $subMaxEnd !== null ? $subMaxEnd : '' }}"
                                                data-mix-options="{{ !empty($subOptions) ? e(implode(',', $subOptions)) : '' }}"
                                            >
                                                @switch($subType)
                                                    @case('integer')
                                                    @case('decimal')
                                                        <input
                                                        type="number"
                                                        step="{{ $subStep }}"
                                                        name="measured_mix[{{ $detailIdKey }}][{{ $ti->id }}][{{ $seq }}][{{ $k }}]"
                                                        class="form-control input-sm js-measured"
                                                        value="{{ $subSelectedVal }}"
                                                        placeholder="ค่า"
                                                        style="width:100%;"
                                                        data-format="{{ $subType }}"
                                                        data-min="{{ $subMin !== null ? $subMin : '' }}"
                                                        data-max="{{ $subMax !== null ? $subMax : '' }}"
                                                        />
                                                        @break

                                                    @case('integer_range')
                                                    @case('decimal_range')
                                                        @php
                                                            $subRangeStartVal = '';
                                                            $subRangeEndVal = '';

                                                            if (is_array($subSelectedVal)) {
                                                                $subRangeStartVal = isset($subSelectedVal['start']) ? $subSelectedVal['start'] : '';
                                                                $subRangeEndVal   = isset($subSelectedVal['end']) ? $subSelectedVal['end'] : '';
                                                            } elseif (is_string($subSelectedVal) && trim($subSelectedVal) !== '') {
                                                                $tmpParts = preg_split('/\s*[-–—]\s*/', trim($subSelectedVal));
                                                                if (count($tmpParts) === 2) {
                                                                    $subRangeStartVal = $tmpParts[0];
                                                                    $subRangeEndVal   = $tmpParts[1];
                                                                }
                                                            }
                                                        @endphp

                                                        <div class="js-range-wrap" style="display:flex; align-items:center; gap:8px; width:100%;">
                                                            <input
                                                                type="number"
                                                                step="{{ $subType === 'decimal_range' ? '0.01' : '1' }}"
                                                                name="measured_mix[{{ $detailIdKey }}][{{ $ti->id }}][{{ $seq }}][{{ $k }}][start]"
                                                                class="form-control input-sm js-range-start"
                                                                value="{{ $subRangeStartVal }}"
                                                                placeholder="ค่าเริ่ม"
                                                                style="width:calc(50% - 12px);"
                                                                data-format="{{ $subType }}"
                                                                data-min-start="{{ $subMinStart !== null ? $subMinStart : '' }}"
                                                                data-max-start="{{ $subMaxStart !== null ? $subMaxStart : '' }}"
                                                                data-min-end="{{ $subMinEnd !== null ? $subMinEnd : '' }}"
                                                                data-max-end="{{ $subMaxEnd !== null ? $subMaxEnd : '' }}"
                                                            />

                                                            <span style="display:inline-block; width:8px; text-align:center;">-</span>

                                                            <input
                                                                type="number"
                                                                step="{{ $subType === 'decimal_range' ? '0.01' : '1' }}"
                                                                name="measured_mix[{{ $detailIdKey }}][{{ $ti->id }}][{{ $seq }}][{{ $k }}][end]"
                                                                class="form-control input-sm js-range-end"
                                                                value="{{ $subRangeEndVal }}"
                                                                placeholder="ค่าสิ้นสุด"
                                                                style="width:calc(50% - 12px);"
                                                                data-format="{{ $subType }}"
                                                                data-min-start="{{ $subMinStart !== null ? $subMinStart : '' }}"
                                                                data-max-start="{{ $subMaxStart !== null ? $subMaxStart : '' }}"
                                                                data-min-end="{{ $subMinEnd !== null ? $subMinEnd : '' }}"
                                                                data-max-end="{{ $subMaxEnd !== null ? $subMaxEnd : '' }}"
                                                            />
                                                        </div>
                                                        @break

                                                    @case('select')
                                                        <select
                                                            name="measured_mix[{{ $detailIdKey }}][{{ $ti->id }}][{{ $seq }}][{{ $k }}]"
                                                            class="form-control input-sm js-measured js-select2"
                                                            data-dropdown-parent="td"
                                                            style="width:100%;"
                                                        >
                                                            <option value="">-</option>
                                                            @foreach($subOptions as $op)
                                                                <option value="{{ $op }}" {{ (string)$subSelectedVal === (string)$op ? 'selected' : '' }}>{{ $op }}</option>
                                                            @endforeach
                                                        </select>
                                                        @break

                                                    @case('select_multiple')
                                                        <select
                                                            name="measured_mix[{{ $detailIdKey }}][{{ $ti->id }}][{{ $seq }}][{{ $k }}][]"
                                                            class="form-control input-sm js-measured js-force-native-multi"
                                                            multiple
                                                            style="width:100%;"
                                                        >
                                                            @foreach($subOptions as $op)
                                                                <option value="{{ $op }}" {{ in_array($op, $subSelectedArr) ? 'selected' : '' }}>{{ $op }}</option>
                                                            @endforeach
                                                        </select>
                                                        @break

                                                    @default
                                                        <input
                                                            type="text"
                                                            name="measured_mix[{{ $detailIdKey }}][{{ $ti->id }}][{{ $seq }}][{{ $k }}]"
                                                            class="form-control input-sm js-measured"
                                                            value="{{ $subSelectedVal }}"
                                                            placeholder="ค่า"
                                                            style="width:100%;"
                                                        />
                                                @endswitch
                                            </div>
                                        @endforeach

                                    @else
                                        {{-- ✅ ไม่ใช่ mix: ใช้ฐานเดิม --}}
                                        @switch($type)

                                            @case('integer')
                                            @case('decimal')
                                                <input
                                                    type="number"
                                                    step="{{ $step }}"
                                                    name="measured[{{ $detailIdKey }}][{{ $ti->id }}][{{ $seq }}]"
                                                    class="form-control input-sm js-measured"
                                                    value="{{ old("measured.$detailIdKey.$ti->id.$seq", $oldMeasured) }}"
                                                    placeholder="ค่า"
                                                    style="width:100%;"
                                                    data-format="{{ $type }}"
                                                    data-min="{{ $min !== null ? $min : '' }}"
                                                    data-max="{{ $max !== null ? $max : '' }}"
                                                    data-op-start="{{ $opStart }}"
                                                    data-op-end="{{ $opEnd }}"
                                                />
                                                @break

                                            @case('integer_range')
                                            @case('decimal_range')
                                                @php
                                                    $rangeOld = old("measured.$detailIdKey.$ti->id.$seq", $oldMeasured);

                                                    $rangeStartVal = '';
                                                    $rangeEndVal = '';

                                                    if (is_array($rangeOld)) {
                                                        $rangeStartVal = isset($rangeOld['start']) ? $rangeOld['start'] : '';
                                                        $rangeEndVal   = isset($rangeOld['end']) ? $rangeOld['end'] : '';
                                                    } elseif (is_string($rangeOld) && trim($rangeOld) !== '') {
                                                        $tmpParts = preg_split('/\s*[-–—]\s*/', trim($rangeOld));
                                                        if (count($tmpParts) === 2) {
                                                            $rangeStartVal = $tmpParts[0];
                                                            $rangeEndVal   = $tmpParts[1];
                                                        }
                                                    }
                                                @endphp

                                                <div class="js-range-wrap" style="display:flex; align-items:center; gap:8px; width:100%;">
                                                    <input
                                                        type="number"
                                                        step="{{ $type === 'decimal_range' ? '0.01' : '1' }}"
                                                        name="measured[{{ $detailIdKey }}][{{ $ti->id }}][{{ $seq }}][start]"
                                                        class="form-control input-sm js-range-start"
                                                        value="{{ $rangeStartVal }}"
                                                        placeholder="ค่าเริ่ม"
                                                        style="width:calc(50% - 12px);"
                                                        data-format="{{ $type }}"
                                                        data-min-start="{{ $minStart !== null ? $minStart : '' }}"
                                                        data-max-start="{{ $maxStart !== null ? $maxStart : '' }}"
                                                        data-min-end="{{ $minEnd !== null ? $minEnd : '' }}"
                                                        data-max-end="{{ $maxEnd !== null ? $maxEnd : '' }}"
                                                    />

                                                    <span style="display:inline-block; width:8px; text-align:center;">-</span>

                                                    <input
                                                        type="number"
                                                        step="{{ $type === 'decimal_range' ? '0.01' : '1' }}"
                                                        name="measured[{{ $detailIdKey }}][{{ $ti->id }}][{{ $seq }}][end]"
                                                        class="form-control input-sm js-range-end"
                                                        value="{{ $rangeEndVal }}"
                                                        placeholder="ค่าสิ้นสุด"
                                                        style="width:calc(50% - 12px);"
                                                        data-format="{{ $type }}"
                                                        data-min-start="{{ $minStart !== null ? $minStart : '' }}"
                                                        data-max-start="{{ $maxStart !== null ? $maxStart : '' }}"
                                                        data-min-end="{{ $minEnd !== null ? $minEnd : '' }}"
                                                        data-max-end="{{ $maxEnd !== null ? $maxEnd : '' }}"
                                                    />
                                                </div>
                                                @break

                                            @case('select')
                                                <select
                                                    name="measured[{{ $detailIdKey }}][{{ $ti->id }}][{{ $seq }}]"
                                                    class="form-control input-sm js-measured js-select2"
                                                    data-dropdown-parent="td"
                                                    style="width:100%;"
                                                >
                                                    <option value="">-</option>
                                                    @php $selectedVal = old("measured.$detailIdKey.$ti->id.$seq", $oldMeasured); @endphp
                                                    @foreach($options as $op)
                                                        <option value="{{ $op }}" {{ (string)$selectedVal === (string)$op ? 'selected' : '' }}>{{ $op }}</option>
                                                    @endforeach
                                                </select>
                                                @break

                                            @case('select_multiple')
                                                @php
                                                    $selectedVal = old("measured.$detailIdKey.$ti->id.$seq", $oldMeasured);
                                                    $selectedArr = [];
                                                    if (is_array($selectedVal)) {
                                                        $selectedArr = $selectedVal;
                                                    } elseif (is_string($selectedVal)) {
                                                        $selectedArr = array_filter(array_map('trim', explode(',', $selectedVal)));
                                                    }
                                                @endphp

                                                <select
                                                    name="measured[{{ $detailIdKey }}][{{ $ti->id }}][{{ $seq }}][]"
                                                    class="form-control input-sm js-measured js-force-native-multi"
                                                    multiple
                                                    style="width:100%;"
                                                >
                                                    @foreach($options as $op)
                                                        <option value="{{ $op }}" {{ in_array($op, $selectedArr) ? 'selected' : '' }}>{{ $op }}</option>
                                                    @endforeach
                                                </select>
                                                @break

                                            @default
                                                <input
                                                    type="text"
                                                    name="measured[{{ $detailIdKey }}][{{ $ti->id }}][{{ $seq }}]"
                                                    class="form-control input-sm js-measured"
                                                    value="{{ old("measured.$detailIdKey.$ti->id.$seq", $oldMeasured) }}"
                                                    placeholder="ค่า"
                                                    style="width:100%;"
                                                />
                                        @endswitch
                                    @endif

                                    {{-- PASS/FAIL --}}
                                    @php
                                        $selectedResult = old("item_result.$detailIdKey.$ti->id.$seq", $oldResult ?? 'fail');
                                    @endphp

                                    <select name="item_result[{{ $detailIdKey }}][{{ $ti->id }}][{{ $seq }}]"
                                            class="form-control input-sm js-item-result"
                                            onchange="window.__updateRowSummary && window.__updateRowSummary(this)"
                                            style="margin-top:4px; width:100%;">
                                        <option value="pass" {{ $selectedResult == 'pass' ? 'selected' : '' }}>ผ่าน</option>
                                        <option value="fail" {{ $selectedResult == 'fail' ? 'selected' : '' }}>ไม่ผ่าน</option>
                                    </select>
                                @endif
                            @endif
                        </td>
                    @endfor
                    {{-- ✅ สรุปผลของทั้ง row (default = ไม่ผ่าน, ยกเว้นแถวที่ยังไม่ได้ตั้งค่าจะไม่นับรวมกับ
                         ผลรวมทั้งฉบับ — ดู evalOverallResult() ที่ข้าม data-unconfigured="1") --}}
                    <td class="js-row-summary" style="text-align:center; vertical-align:middle; min-width:140px;">
                        @if($isUnconfigured)
                            <span class="label label-default">เจ้าหน้าที่ประเมิน</span>
                        @else
                            <span class="label label-danger"><i class="fa fa-times"></i> ไม่ผ่าน</span>
                        @endif
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="{{ 7 + $maxTimes + 1}}" class="text-center text-muted">
                        ยังไม่มีรายการทดสอบ
                        <span style="font-size:12px; display:block; margin-top:4px;">
                            — ไม่มีรายการทดสอบ (คำขอยื่นก่อนระบบเวอร์ชันใหม่) —
                        </span>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endforeach
</div>

@if(isset($readonly) && $readonly && !empty($report))
<div class="row m-t-20">
    <div class="col-md-6">
        <table class="table table-bordered">
            <tr>
                <th class="bg-primary text-white" style="width:40%;">ผลการทดสอบรวม</th>
                <td>
                    @if($report->overall_result == 'pass')
                        <span class="label label-success" style="font-size:14px;"><i class="fa fa-check"></i> ผ่าน</span>
                    @elseif($report->overall_result == 'fail')
                        <span class="label label-danger" style="font-size:14px;"><i class="fa fa-times"></i> ไม่ผ่าน</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="bg-primary text-white">ไฟล์รายงาน PDF ฉบับเต็ม</th>
                <td>
                    @if(!empty($report->report_file_path))
                        <a href="{{ HP::getFileStorage($report->report_file_path) }}" target="_blank" class="btn btn-info btn-sm">
                            <i class="fa fa-download"></i> ดาวน์โหลด PDF
                        </a>
                    @else
                        <span class="text-muted">ไม่มีไฟล์แนบ</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>
@endif

@if(!isset($readonly) || !$readonly)
<script>
(/*function(){
    function toNumber(v){
        if (v === null || v === undefined) return null;
        v = (''+v).trim();
        if (v === '') return null;
        var n = Number(v);
        return isNaN(n) ? null : n;
    }

    function parseRange(str){
        if (!str) return null;
        str = (''+str).trim();
        if (str === '') return null;
        // รองรับ "a-b" หรือ "a - b"
        var m = str.match(/^([+-]?\d+(?:\.\d+)?)\s*-\s*([+-]?\d+(?:\.\d+)?)$/);
        if (!m) return null;
        return { start: Number(m[1]), end: Number(m[2]) };
    }

    function lockResultSelectIfNumeric($cell){
        var type = ($cell.data('type') || '').toString();
        if (!(type === 'integer' || type === 'decimal' || type === 'integer_range' || type === 'decimal_range')) return;
        var $sel = $cell.find('select.js-item-result').first();
        if ($sel.length === 0) return;

        // ✅ ล็อคตั้งแต่โหลดหน้า แต่ยังคง "enabled" เพื่อให้ submit ส่งค่าได้
        if (!$sel.hasClass('js-locked-result')) {
            $sel.addClass('js-locked-result')
                .attr('tabindex', '-1')
                .attr('aria-disabled', 'true');
        }
    }

    function setResult($cell, passFail){
        var $sel = $cell.find('select.js-item-result').first();
        if ($sel.length === 0) return;

        if (passFail === 'pass' || passFail === 'fail') {
            $sel.val(passFail);
        } else {
            $sel.val('');
        }

        // ✅ numeric 4 แบบนี้ต้องล็อคเสมอ (ตั้งแต่โหลดหน้า + หลังกรอกค่า)
        lockResultSelectIfNumeric($cell);

        // ไม่สร้าง error log; แค่อัปเดตค่า
    }

    function evalCell($cell){
        var type = ($cell.data('type') || '').toString();
        var $input = $cell.find('.js-measured').first();

        // ล็อค dropdown ผลสำหรับ numeric 4 แบบทันทีตั้งแต่เริ่มประเมิน
        lockResultSelectIfNumeric($cell);
        if ($input.length === 0) return;

        // select/select_multiple/text -> ไม่ auto-evaluate ตามแผน (เฉพาะ numeric/range)
        if (type === 'select' || type === 'select_multiple' || type === 'text' || type === '') return;

        if (type === 'integer' || type === 'decimal') {
            var v = toNumber($input.val());
            if (v === null) { setResult($cell, null); return; }

            var min = toNumber($cell.data('min'));
            var max = toNumber($cell.data('max'));

            // ถ้าไม่มี min/max ใน config -> ไม่ตัดสิน
            if (min === null && max === null) { setResult($cell, null); return; }

            var ok = true;
            if (min !== null && v < min) ok = false;
            if (max !== null && v > max) ok = false;

            setResult($cell, ok ? 'pass' : 'fail');
            return;
        }

        if (type === 'integer_range' || type === 'decimal_range') {
            var r = parseRange($input.val());
            if (!r) { setResult($cell, null); return; }

            var minS = toNumber($cell.data('min_start'));
            var maxS = toNumber($cell.data('max_start'));
            var minE = toNumber($cell.data('min_end'));
            var maxE = toNumber($cell.data('max_end'));

            // ถ้าไม่มี config -> ไม่ตัดสิน
            if (minS===null && maxS===null && minE===null && maxE===null) { setResult($cell, null); return; }

            var ok = true;
            if (minS !== null && r.start < minS) ok = false;
            if (maxS !== null && r.start > maxS) ok = false;
            if (minE !== null && r.end   < minE) ok = false;
            if (maxE !== null && r.end   > maxE) ok = false;

            setResult($cell, ok ? 'pass' : 'fail');
            return;
        }
    }


    function preventDefaultLockedResult(e){
        var el = e.target;
        if (!el) return;
        if (el.tagName === 'SELECT' && el.classList && el.classList.contains('js-locked-result')) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }

    // กันการคลิก/กดคีย์เพื่อเปลี่ยนค่า (แม้บาง browser จะยังเปิด dropdown ได้)
    document.addEventListener('mousedown', preventDefaultLockedResult, true);
    document.addEventListener('keydown', function(e){
        // Space/Arrow/Enter
        if (e.key === ' ' || e.key === 'Spacebar' || e.key === 'Enter' || e.key === 'ArrowUp' || e.key === 'ArrowDown') {
            return preventDefaultLockedResult(e);
        }
    }, true);

    // bind events
    document.addEventListener('input', function(e){
        var el = e.target;
        if (!el.classList || !el.classList.contains('js-measured')) return;
        var $cell = window.jQuery ? window.jQuery(el).closest('td.js-test-cell') : null;
        if (!$cell || $cell.length === 0) return;
        evalCell($cell);
    });

    document.addEventListener('change', function(e){
        var el = e.target;
        if (!el.classList || !el.classList.contains('js-measured')) return;
        var $cell = window.jQuery ? window.jQuery(el).closest('td.js-test-cell') : null;
        if (!$cell || $cell.length === 0) return;
        evalCell($cell);
    });

    // initial evaluate (กรณีมีค่าที่เคยกรอกมาแล้ว)
    if (window.jQuery) {
        window.jQuery(function(){
            window.jQuery('td.js-test-cell').each(function(){
                evalCell(window.jQuery(this));
            });
        });
    }
*/})();
</script>
@endif

<script>
(function(){
    function forceNativeMulti(){
        var root = document.getElementById('tbl-test-result');
        if(!root) return;

        var list = root.querySelectorAll('select.js-force-native-multi');

        for (var i=0; i<list.length; i++){
            var sel = list[i];

            // เปิดให้ select native กลับมาแสดง (บาง plugin จะซ่อนด้วย style/attribute)
            sel.style.display = '';
            sel.style.visibility = 'visible';
            sel.style.opacity = '1';

            // ลบ wrapper ที่ "สร้างมาทับ" ข้างหลัง select (กรณี select2/chosen/bootstrap-select)
            // ทำแบบลบ DOM ตรงๆ ไม่เรียก destroy ไม่ไปแตะตัวอื่น
            var next = sel.nextElementSibling;
            if(next){
                var cls = (next.className || '');
                if(
                    cls.indexOf('select2') !== -1 ||
                    cls.indexOf('bootstrap-select') !== -1 ||
                    cls.indexOf('chosen-container') !== -1 ||
                    cls.indexOf('dropdown') !== -1
                ){
                    next.parentNode.removeChild(next);
                }
            }

            // บาง plugin อาจวาง wrapper ไว้ใน parent เดียวกัน (ไม่ใช่แค่ next)
            var p = sel.parentElement;
            if(p){
                var killers = p.querySelectorAll(':scope > .select2, :scope > .select2-container, :scope > .bootstrap-select, :scope > .chosen-container, :scope > .dropdown');
                for (var k=0; k<killers.length; k++){
                    killers[k].parentNode.removeChild(killers[k]);
                }
            }
        }
    }

    if(document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', forceNativeMulti);
    }else{
        forceNativeMulti();
    }

    // เผื่อหน้ามีสคริปต์อื่นยิงซ้ำภายหลัง (คุณจะเรียกเองตอน debug ก็ได้)
    window.forceNativeMultiSelectInTestReport = forceNativeMulti;
})();
</script>

<script>
(function () {
  const NUMERIC_TYPES = new Set(['integer','decimal','integer_range','decimal_range']);

  function parseNum(v) {
    if (v === null || v === undefined) return null;
    const s = String(v).trim().replace(/,/g,'');
    if (s === '') return null;
    const n = Number(s);
    return Number.isFinite(n) ? n : null;
  }

  function parseRange(str) {
    const s = String(str || '').trim();
    if (!s) return null;
    const parts = s.split(/[-–—]/).map(x => x.trim()).filter(Boolean);
    if (parts.length !== 2) return null;
    const a = parseNum(parts[0]);
    const b = parseNum(parts[1]);
    if (a === null || b === null) return null;
    return { start: a, end: b };
  }

  // เทียบค่าตาม operator จริง (>, >=, <, <=, =) — เกณฑ์ 7.1 (bcertify) แยก > กับ >= ได้ ต่างจาก
  // ค่า min/max ของ 7.2 เดิมที่ inclusive เสมอ (>= / <=) ดูหัวข้อ 8.4 ข้อ 2.1 ของ
  // docs/tiw/std-test-item-elicense-groups-exploration.md (devops_center)
  function compareOp(value, op, bound) {
    switch (op) {
      case '>':  return value > bound;
      case '>=': return value >= bound;
      case '<':  return value < bound;
      case '<=': return value <= bound;
      case '=':  return value === bound;
      default:   return true;
    }
  }

  // ล็อค select แต่ต้อง "ยังส่งค่าได้" ตอน submit:
  // disable select + mirror hidden input (ชื่อเดียวกัน)
  function lockSelect(selectEl) {
    if (!selectEl || selectEl.dataset.locked === '1') return;

    const name = selectEl.getAttribute('name');
    if (!name) return;

    // สร้าง hidden mirror
    let hidden = selectEl.parentElement.querySelector('input[type="hidden"][data-mirror="'+name+'"]');
    if (!hidden) {
      hidden = document.createElement('input');
      hidden.type = 'hidden';
      hidden.setAttribute('name', name);
      hidden.setAttribute('data-mirror', name);
      selectEl.parentElement.appendChild(hidden);
    }

    hidden.value = selectEl.value || '';
    selectEl.disabled = true;
    selectEl.dataset.locked = '1';
    selectEl._mirrorHidden = hidden;
  }

    function setResult(selectEl, value) {
        if (!selectEl) return;

        // ✅ ตั้งค่าจริง
        selectEl.value = value;
        if (selectEl._mirrorHidden) selectEl._mirrorHidden.value = value;

        // ✅ บังคับให้ UI (เช่น select2) refresh ตามค่าใหม่
        if (window.jQuery) {
            const $el = window.jQuery(selectEl);
            if ($el.data('select2')) {
                $el.val(value).trigger('change.select2'); // สำคัญมาก
            } else {
                $el.trigger('change');
            }
        } else {
            // กรณีไม่มี jQuery ก็ dispatch change เฉย ๆ
            selectEl.dispatchEvent(new Event('change', { bubbles: true }));
        }

        // ✅ อัปเดตสรุปทั้งแถวทันที (ของที่เราเพิ่มไป)
        evalRowSummaryFromAnyEl(selectEl);
    }

  function renderRowSummary(rowEl, isPass) {
    const cell = rowEl ? rowEl.querySelector('td.js-row-summary') : null;
    if (!cell) return;
    
    rowEl.dataset.rowResult = isPass ? 'pass' : 'fail';
    if (isPass) {
      cell.innerHTML = '<span class="label label-success"><i class="fa fa-check"></i> ผ่าน</span>';
    } else {
      cell.innerHTML = '<span class="label label-danger"><i class="fa fa-times"></i> ไม่ผ่าน</span>';
    }
    // ✅ ให้สรุปรวมไหลตาม "ทุกครั้งที่สรุปแถวเปลี่ยน"
    evalOverallResult();
  }

  function evalRowSummaryByRow(rowEl) {
    if (!rowEl) return;

    // แถวที่เจ้าหน้าที่ยังไม่ได้ตั้งค่าเลย (ดู test_report_table.blade.php บรรทัด ~181/588) ไม่มี select
    // ผลทดสอบให้เลือกอยู่แล้ว — ถ้าปล่อยให้ไหลลงไปคำนวณด้านล่างจะนับเป็น "ไม่ผ่าน" เสมอ (selects.length===0)
    // ทับ badge สีเทาที่ฝั่ง server render ไว้ตอนโหลดหน้า ต้องกันไว้ตรงนี้ก่อน
    if (rowEl.dataset.unconfigured === '1') {
      const cell = rowEl.querySelector('td.js-row-summary');
      if (cell) cell.innerHTML = '<span class="label label-default">เจ้าหน้าที่ประเมิน</span>';
      delete rowEl.dataset.rowResult;
      return;
    }

    const times = Number(rowEl.getAttribute('data-times') || '0') || 0;

    // เลือกเฉพาะ select ของ cell ที่ "อยู่ในช่วง seq ที่ใช้งานจริง"
    const selects = Array.from(rowEl.querySelectorAll('td.js-test-cell select.js-item-result'))
      .filter(sel => {
        const td = sel.closest('td.js-test-cell');
        const seq = td ? Number(td.getAttribute('data-seq') || td.dataset.seq || '0') : 0;
        return seq > 0 && seq <= times;
      });

    // default fail (ถ้าไม่มี select เลยก็ fail ตามที่คุณต้องการ)
    let pass = selects.length > 0;
    for (let i = 0; i < selects.length; i++) {
      if ((selects[i].value || '').toLowerCase() !== 'pass') { // เจอ fail/ว่าง => fail
        pass = false;
        break;
      }
    }

    renderRowSummary(rowEl, pass);
  }

    function lockOverallSelect(sel){
        // ใช้หลักเดียวกับ lockSelect: disable + mirror hidden เพื่อให้ submit ส่งค่าได้
        if (!sel || sel.dataset.lockedOverall === '1') return;

        const name = sel.getAttribute('name');
        if (!name) return;

        let hidden = sel.parentElement.querySelector('input[type="hidden"][data-mirror-overall="'+name+'"]');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.setAttribute('name', name);
            hidden.setAttribute('data-mirror-overall', name);
            sel.parentElement.appendChild(hidden);
        }

        hidden.value = sel.value || '';
        sel.disabled = true;
        sel.dataset.lockedOverall = '1';
        sel._mirrorHiddenOverall = hidden;
    }

    function setOverallValue(sel, value){
        if (!sel) return;

        sel.value = value;
        if (sel._mirrorHiddenOverall) sel._mirrorHiddenOverall.value = value;

        // ถ้าเป็น select2 ให้ refresh UI
        if (window.jQuery) {
            const $el = window.jQuery(sel);
            if ($el.data('select2')) $el.val(value).trigger('change.select2');
            else $el.trigger('change');
        } else {
            sel.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    function evalOverallResult() {
        const overallSel = document.querySelector('select[name="overall_result"]');
        if (!overallSel) return;

        // แถวที่เจ้าหน้าที่ยังไม่ได้ตั้งค่าเลย (data-unconfigured="1") ไม่มี select ผลทดสอบให้กรอก จึง
        // ไม่นับรวมในผลรวม (ไม่ใช่ทั้ง pass และ fail) — กันไม่ให้รายการที่ยังไม่ได้ตั้งค่าไปบล็อกผลรวม
        // ทั้งฉบับเป็น "ไม่ผ่าน" ถาวร ห้องแล็บยังส่งรายงานผลได้ตามปกติผ่านการแนบไฟล์ PDF
        const rows = Array.from(document.querySelectorAll('tr.js-test-row'))
            .filter(function (row) { return row.dataset.unconfigured !== '1'; });

        // ไม่มีรายการที่ตั้งค่าไว้เลยสักแถว (ทั้งฉบับยังไม่ได้ตั้งค่า) — ไม่มีฐานให้คำนวณอัตโนมัติ ปล่อยให้
        // เจ้าหน้าที่ห้องแล็บเลือกผลรวมเอง (ไม่ล็อค) แทนที่จะบังคับเป็น "ไม่ผ่าน" ถาวรทั้งที่ยังไม่ได้ทดสอบ
        // อะไรเลย — ยังคงต้องเลือกก่อน submit ได้ (attribute required เดิมของ select นี้)
        if (rows.length === 0) return;

        let allPass = true;

        for (let i = 0; i < rows.length; i++) {
            const r = (rows[i].dataset.rowResult || '').toLowerCase();

            // default fail (ถ้ายังไม่มีค่าใน dataset)
            if (r !== 'pass') { allPass = false; break; }
        }

        setOverallValue(overallSel, allPass ? 'pass' : 'fail');
        lockOverallSelect(overallSel);
    }
    window.__evalOverallResult = evalOverallResult;

    function evalRowSummaryFromAnyEl(el) {
        const row = el ? el.closest('tr.js-test-row') : null;
        if (!row) return;
        evalRowSummaryByRow(row);
    }

    function evalNumeric(inputEl) {
        const cell = inputEl.closest('td.js-test-cell') || inputEl.closest('td') || inputEl.parentElement;
        if (!cell) return;

        // ✅ ใช้ "type หลักของ cell" เท่านั้น (กัน mix พลอยโดน)
        const cellType = (cell.getAttribute('data-type') || '').trim();
        if (!NUMERIC_TYPES.has(cellType)) return; // <- mix จะไม่เข้า

        // fmt ของ input เอาไว้แยก integer vs range (ในเคส non-mix มันจะตรงกับ cellType อยู่แล้ว)
        const fmt = (inputEl.dataset.format || '').trim();

        const dd = cell.querySelector('select.js-item-result');
        if (!dd) return;

        // ✅ ล็อคตั้งแต่โหลดหน้าและหลังกรอก
        lockSelect(dd);

        let pass = true;

        if (fmt === 'integer' || fmt === 'decimal') {
            const v = parseNum(inputEl.value);
            const min = parseNum(inputEl.dataset.min);
            const max = parseNum(inputEl.dataset.max);
            // operator จริงของแต่ละฝั่ง — ค่าเริ่มต้น >=/<= ตรงกับพฤติกรรมเดิมของ 7.2 ทุกประการ ถ้าไม่มี
            // เกณฑ์ 7.1 มาทับ (ดู test_items query ใน ProductTestingController::getTestItemsForLab())
            const opStart = inputEl.dataset.opStart || '>=';
            const opEnd   = inputEl.dataset.opEnd || '<=';

            // กรณีกรอกไม่ครบ/parse ไม่ได้ หรือไม่มีเกณฑ์ให้เทียบเลยสักฝั่ง => ให้ fail (ล็อคทุกกรณี)
            if (v === null || (min === null && max === null)) {
                pass = false;
            } else {
                // เกณฑ์ 7.1 บางแบบ (upper_limit/lower_limit) มีแค่ฝั่งเดียว — ฝั่งที่ไม่มีค่าถือว่าไม่มี
                // ข้อจำกัด ไม่ใช่ fail (ต่างจาก 7.2 เดิมที่มี min/max มาคู่กันเสมอ จึงไม่เคยเจอเคสนี้)
                if (min !== null && !compareOp(v, opStart, min)) pass = false;
                if (max !== null && !compareOp(v, opEnd, max)) pass = false;
            }

        } else if (fmt === 'integer_range' || fmt === 'decimal_range') {
            const r = parseRange(inputEl.value);
            const minS = parseNum(inputEl.dataset.minStart);
            const maxS = parseNum(inputEl.dataset.maxStart);
            const minE = parseNum(inputEl.dataset.minEnd);
            const maxE = parseNum(inputEl.dataset.maxEnd);

            if (!r) {
                pass = false;
            } else if (minS === null && maxS === null && minE === null && maxE === null) {
                pass = false;
            } else {
                if (minS !== null && r.start < minS) pass = false;
                if (maxS !== null && r.start > maxS) pass = false;
                if (minE !== null && r.end   < minE) pass = false;
                if (maxE !== null && r.end   > maxE) pass = false;
            }
        }

        setResult(dd, pass ? 'pass' : 'fail');
    }

  function init() {
    // ✅ ล็อค+ประเมินตั้งแต่โหลดหน้า สำหรับ 4 แบบนี้เท่านั้น
    document.querySelectorAll('.js-measured').forEach(evalNumeric);

    // ✅ คำนวณสรุปทุกแถวรอบแรกตอนโหลดหน้า
    document.querySelectorAll('tr.js-test-row').forEach(evalRowSummaryByRow);

    document.addEventListener('input', function (e) {
      const el = e.target;
      if (el && el.classList && el.classList.contains('js-measured')) evalNumeric(el);
    });

    document.addEventListener('change', function (e) {
      const el = e.target;
      if (el && el.classList && el.classList.contains('js-measured')) evalNumeric(el);
    });

    document.addEventListener('change', function(e){
        const el = e.target;
        if (el && el.matches && el.matches('select.js-item-result')) {
            evalRowSummaryFromAnyEl(el);
        }
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
</script>

<script>
window.__updateRowSummary = function(sel){
  if(!sel) return;

  // หาแถวแบบไม่พึ่ง .js-test-row (กันกรณี class ไม่ตรง)
  var tr = sel.closest('tr');
  if(!tr) return;

  var times = parseInt(tr.getAttribute('data-times') || '0', 10) || 0;

  // เอา select ผลทั้งหมดในแถว
  var sels = Array.from(tr.querySelectorAll('td.js-test-cell select.js-item-result'));

  // กรองเฉพาะช่องที่ใช้งานจริงตาม seq <= times
  if(times > 0){
    sels = sels.filter(function(s){
      var td = s.closest('td.js-test-cell');
      var seq = td ? parseInt(td.getAttribute('data-seq') || '0', 10) || 0 : 0;
      return seq > 0 && seq <= times;
    });
  }

  // default fail ตาม requirement คุณ
  var pass = sels.length > 0;
  for(var i=0;i<sels.length;i++){
    var v = (sels[i].value || '').toLowerCase();
    if(v !== 'pass'){ pass = false; break; }
  }

  var sumTd = tr.querySelector('td.js-row-summary');
  if(!sumTd) return;

  sumTd.innerHTML = pass
    ? '<span class="label label-success"><i class="fa fa-check"></i> ผ่าน</span>'
    : '<span class="label label-danger"><i class="fa fa-times"></i> ไม่ผ่าน</span>';

  // ✅ ปักธงผลสรุปแถว
  if (tr) tr.dataset.rowResult = pass ? 'pass' : 'fail';

  // ✅ แล้วคำนวณสรุปรวม
  if (typeof window.__evalOverallResult === 'function') {
    window.__evalOverallResult();
  }
};
</script>

<script>
(function () {
    function parseNum(v) {
        if (v === null || v === undefined) return null;
        const s = String(v).trim().replace(/,/g, '');
        if (s === '') return null;
        const n = Number(s);
        return Number.isFinite(n) ? n : null;
    }

    function lockSelect(selectEl) {
        if (!selectEl) return;
        selectEl.classList.add('js-locked-result');
        selectEl.setAttribute('tabindex', '-1');
        selectEl.setAttribute('aria-disabled', 'true');
    }

    function setResult(selectEl, value) {
        if (!selectEl) return;
        if (value === 'pass' || value === 'fail') {
            selectEl.value = value;
        }
    }

    function evalRangeWrap(wrap) {
        if (!wrap) return;

        const startInput = wrap.querySelector('.js-range-start');
        const endInput   = wrap.querySelector('.js-range-end');
        if (!startInput || !endInput) return;

        const fmt = (startInput.dataset.format || '').trim();
        if (fmt !== 'integer_range' && fmt !== 'decimal_range') return;

        const isMix = /^measured_mix\[/.test(startInput.name || '');
        if (isMix) return; // ✅ mix sub-field ต้องไม่ auto-evaluate และไม่ lock

        const td = wrap.closest('td') || wrap.parentElement;
        if (!td) return;

        const dd = td.querySelector('select.js-item-result');
        if (!dd) return;

        lockSelect(dd);

        const startVal = parseNum(startInput.value);
        const endVal   = parseNum(endInput.value);

        const minS = parseNum(startInput.dataset.minStart);
        const maxS = parseNum(startInput.dataset.maxStart);
        const minE = parseNum(startInput.dataset.minEnd);
        const maxE = parseNum(startInput.dataset.maxEnd);

        let startOk = true;
        let endOk = true;

        if (startVal === null) {
            startOk = false;
        } else if (minS === null && maxS === null) {
            startOk = false;
        } else {
            if (minS !== null && startVal < minS) startOk = false;
            if (maxS !== null && startVal > maxS) startOk = false;
        }

        if (endVal === null) {
            endOk = false;
        } else if (minE === null && maxE === null) {
            endOk = false;
        } else {
            if (minE !== null && endVal < minE) endOk = false;
            if (maxE !== null && endVal > maxE) endOk = false;
        }

        // ✅ ถูกคู่ = ผ่าน, ผิดหนึ่งหรือผิดคู่ = ไม่ผ่าน
        const finalResult = (startOk && endOk) ? 'pass' : 'fail';
        setResult(dd, finalResult);
    }

    function initRangeAutoEval() {
        document.querySelectorAll('.js-range-wrap').forEach(evalRangeWrap);
    }

    document.addEventListener('input', function (e) {
        const el = e.target;
        if (!el || !el.classList) return;

        if (el.classList.contains('js-range-start') || el.classList.contains('js-range-end')) {
            const wrap = el.closest('.js-range-wrap');
            if (wrap) evalRangeWrap(wrap);
        }
    });

    document.addEventListener('change', function (e) {
        const el = e.target;
        if (!el || !el.classList) return;

        if (el.classList.contains('js-range-start') || el.classList.contains('js-range-end')) {
            const wrap = el.closest('.js-range-wrap');
            if (wrap) evalRangeWrap(wrap);
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRangeAutoEval);
    } else {
        initRangeAutoEval();
    }
})();
</script>