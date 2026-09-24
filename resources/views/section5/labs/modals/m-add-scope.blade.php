<div class="modal fade bs-example-modal-lg" role="dialog" aria-labelledby="ScopeModalLabel" aria-hidden="true" id="AddScopeModal" >
    <div class="modal-dialog modal-dialog-centere modal-xl" style="width: 90%; max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title" id="ScopeModalLabel">เพิ่มขอบข่าย</h4>
            </div>
            <div class="modal-body">

                @php
                    // ปุ่มนี้ใช้เพิ่ม "มอก. ใหม่" ที่ lab ยังไม่มีเท่านั้น — มอก. ที่ lab มีอยู่แล้วให้เพิ่มรายการทดสอบ/เครื่องมือใน panel ของตารางแทน
                    $lab_owned_tis_ids = $labs->scope_standard()->pluck('tis_id')->filter()->unique()->all();
                    $list_standard_add_scope = App\Models\Basic\Tis::select('tb3_Tisno', 'tb3_TisThainame', 'tb3_TisAutono')
                        ->whereIn('status', ['-1', '0', '1', '2', '3'])
                        ->whereNotIn('tb3_TisAutono', $lab_owned_tis_ids)
                        ->orderBy('tb3_Tisno')
                        ->get();
                @endphp

                <div class="row">
                    <div class="col-md-7">
                        <div class="input-group">
                            <select id="addscope_tis_selector" class="form-control not_select2" style="width:100%">
                                <option value="">- เลือก มอก. -</option>
                                @foreach($list_standard_add_scope as $std)
                                    <option value="{{ $std->tb3_TisAutono }}"
                                            data-tisno="{{ $std->tb3_Tisno }}"
                                            data-tisname="{{ strip_tags($std->tb3_TisThainame) }}">
                                        {{ $std->tb3_Tisno }} : {{ strip_tags($std->tb3_TisThainame) }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="input-group-btn">
                                <button type="button" id="btn_load_addscope_items" class="btn btn-primary">
                                    <i class="fa fa-search"></i> เพิ่ม
                                </button>
                            </span>
                        </div>
                        <span class="text-danger" id="addscope_tis_pending_warning" style="display:none;">
                            <i class="fa fa-exclamation-triangle"></i> เลือก มอก. แล้ว แต่ยังไม่ได้กดปุ่ม "เพิ่ม" กรุณากดปุ่ม "เพิ่ม" เพื่อโหลดรายการทดสอบ
                        </span>
                    </div>
                </div>

                <br>
                <div class="row">
                    <div class="col-md-12">
                        <div id="box_add_scope_request"></div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success waves-effect text-left" id="btn_submit_add_scope" style="display:none;">ยื่นคำขอเพิ่มเติมขอบข่าย</button>
                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    #box_add_scope_request .panel { box-shadow: 0 1px 4px rgba(0,0,0,.1); margin-bottom: 10px; }
    .addscope-panel .panel-heading { background: #f0f4ff; border-color: #c5d0e6; padding: 10px 15px; }
    .addscope-panel .panel-title a { font-size: 15px; font-weight: bold; color: #333; text-decoration: none; }
    .addscope-panel .panel-title a:hover { color: #009efb; }
    .addscope-selected-badge { font-size: 12px; margin-left: 8px; background: #009efb; vertical-align: middle; }
    .addscope-selected-badge.none { background: #aaa; }
    .addscope-tool-chk-group { padding: 2px 0; }
    .addscope-chk-tool-lbl { font-weight: normal; font-size: 13px; cursor: pointer; margin: 0 0 3px 0; display: block; }
    .addscope-chk-tool-lbl input { margin-right: 5px; cursor: pointer; }
    .addscope-row-disabled td { background: #f8f8f8 !important; opacity: 0.55; }
    .addscope-row-disabled .addscope-tool-chk-group { pointer-events: none; }
    .addscope-details-row > td { padding: 0 !important; border-top: none !important; }
    .addscope-details-row .inner-detail-table { margin: 0; border-left: 4px solid #009efb; }
    .addscope-details-row .inner-detail-table th { background: #eef2f5; font-size: 12px; padding: 5px 8px; white-space: nowrap; }
    .addscope-details-row .inner-detail-table td { padding: 4px 6px; vertical-align: middle; }
    .addscope_field_error { border: 2px solid #dd4b39 !important; box-shadow: 0 0 4px rgba(221,75,58,.6) !important; background-color: #fff6f6; }
    .addscope-exempt-method-row input[disabled] { background-color: #eceff1 !important; color: #888 !important; cursor: not-allowed; }
    .addscope-exempt-tool-row input[readonly] { background-color: #eceff1 !important; color: #888 !important; cursor: not-allowed; }
</style>
@endpush

@push('js')
    <script>
        $(document).ready(function () {

            // Select2 สำหรับ dropdown มอก. (อยู่ใน modal ต้องกำหนด dropdownParent)
            if ($('#addscope_tis_selector').length) {
                $('#addscope_tis_selector').select2({
                    width: '100%',
                    placeholder: '- เลือก มอก. -',
                    allowClear: true,
                    dropdownParent: $('#AddScopeModal')
                });
            }

            // ── เตือนทันทีถ้าเลือก มอก. แล้วแต่ยังไม่ได้กดปุ่ม "เพิ่ม" ──
            $('#addscope_tis_selector').on('change', function () {
                if ($(this).val()) {
                    $('#addscope_tis_pending_warning').show();
                    $('#btn_load_addscope_items').addClass('btn-danger').removeClass('btn-primary');
                } else {
                    $('#addscope_tis_pending_warning').hide();
                    $('#btn_load_addscope_items').addClass('btn-primary').removeClass('btn-danger');
                }
            });

            // ── ลบกรอบแดงออกทันทีที่ผู้ใช้กรอกช่องนั้นแล้ว ──
            $('#AddScopeModal').on('input change', '.addscope_field_error', function () {
                if ($.trim($(this).val() || '') !== '') {
                    $(this).removeClass('addscope_field_error');
                }
            });

            // ── ปุ่มโหลดรายการทดสอบของ มอก. ──
            $('#btn_load_addscope_items').on('click', function () {
                var tis_id = $('#addscope_tis_selector').val();
                if (!tis_id) { alert('กรุณาเลือก มอก. ก่อน'); return; }
                if ($('#panel-addscope-' + tis_id).length) { alert('มอก. นี้ถูกโหลดแล้ว'); return; }
                if ($('#box_add_scope_request .addscope-panel').not('#panel-addscope-' + tis_id).length) {
                    alert('สามารถเลือก มอก. ได้เพียงรายการเดียว กรุณาลบ มอก. เดิมออกก่อน หากต้องการเปลี่ยน');
                    return;
                }
                var opt = $('#addscope_tis_selector').find('option:selected');
                loadAddScopeByTis(tis_id, opt.data('tisno') || '', opt.data('tisname') || '');
                $('#addscope_tis_selector').val('').trigger('change');
            });

            // ── scope_checkbox toggle (เลือก/ยกเลิก รายการทดสอบ) ──
            $('#AddScopeModal').on('change', '.addscope_checkbox', function () {
                var $row     = $(this).closest('.addscope-main-item-row');
                var item_id  = $(this).data('item-id');
                var tis_id   = $(this).data('tis-id');
                var isExempt = $row.data('exempt') == '1';

                if (isExempt) {
                    // ตรวจพินิจ — แค่แสดงให้ดูเฉยๆ ห้ามกรอก/แก้ไขข้อมูลใดๆ ทั้งสิ้น (คงสถานะ disabled ตลอด)
                    var $exemptRow = $(this).closest('tbody').find('.addscope-exempt-method-row[data-item-id="' + item_id + '"]');
                    if (this.checked) {
                        $row.removeClass('addscope-row-disabled');
                        $exemptRow.show();
                    } else {
                        $row.addClass('addscope-row-disabled');
                        $exemptRow.hide();
                    }
                } else {
                    if (this.checked) {
                        $row.removeClass('addscope-row-disabled');
                        $row.find('.addscope_tool_chk').prop('disabled', false);
                        // auto-check เครื่องมือตัวแรกถ้ายังไม่มีตัวไหนถูกเลือกเลย (checkbox เลือกได้หลายตัว)
                        if ($row.find('.addscope_tool_chk:checked').length === 0) {
                            $row.find('.addscope_tool_chk').first().prop('checked', true);
                        }
                        syncAddScopeToolSelectionForItem(tis_id, item_id);
                    } else {
                        $row.addClass('addscope-row-disabled');
                        $row.find('.addscope_tool_chk').prop('checked', false).prop('disabled', true);
                        syncAddScopeToolSelectionForItem(tis_id, item_id);
                    }
                }
                updateAddScopeBadge(tis_id);
                toggleAddScopeSubmitButton();
            });

            // ── tool_chk toggle (เลือก/ยกเลิก เครื่องมือ — checkbox: เลือกได้หลายตัวต่อรายการทดสอบ) ──
            $('#AddScopeModal').on('change', '.addscope_tool_chk', function () {
                var item_id = $(this).data('item-id');
                var tis_id  = $(this).closest('table').attr('id').replace('table-addscope-group-', '');
                syncAddScopeToolSelectionForItem(tis_id, item_id);
            });

            // ── ลบ panel มอก. ──
            $('#AddScopeModal').on('click', '.addscope_btn_section_remove', function () {
                if (!confirm('ยืนยันการลบชุดรายการทดสอบ มอก. นี้?')) return;
                $(this).closest('.addscope-panel').remove();
                toggleAddScopeSubmitButton();
            });

            // ── ปุ่ม "ยื่นคำขอเพิ่มเติมขอบข่าย" ──
            $('#btn_submit_add_scope').click(function () {

                var $panels = $('#box_add_scope_request .addscope-panel');
                if ($panels.length === 0) {
                    alert('กรุณาเลือก มอก. และรายการทดสอบก่อน');
                    return;
                }

                var totalChecked = $('#box_add_scope_request .addscope_checkbox:checked').length;
                if (totalChecked === 0) {
                    alert('กรุณาเลือกรายการทดสอบที่ต้องการขอเพิ่มขอบข่ายอย่างน้อย 1 รายการ');
                    return;
                }

                var invalidItem = $('#box_add_scope_request .addscope-main-item-row[data-exempt="0"]').filter(function () {
                    return $(this).find('.addscope_checkbox').is(':checked') && $(this).find('.addscope_tool_chk:checked').length === 0;
                }).length > 0;
                if (invalidItem) {
                    alert('มีรายการทดสอบที่เลือกไว้แต่ยังไม่ได้ระบุเครื่องมือ กรุณาระบุเครื่องมือให้ครบทุกรายการ');
                    return;
                }

                // บังคับกรอก ขีดความสามารถ/ช่วงการใช้งาน/ความละเอียดที่อ่านได้/ความคลาดเคลื่อนที่ยอมรับ/ระยะการทดสอบ/ค่าใช้จ่าย
                // ให้ครบทุกแถวเครื่องมือที่ถูกเลือก (ยกเว้นเคส "ตรวจพินิจ" ที่ไม่ต้องมีรายละเอียด)
                $('.addscope_field_error').removeClass('addscope_field_error');
                var $firstMissing = null;
                var requiredFieldNames = ['capacity', 'range', 'true_value', 'fault_value', 'test_duration', 'test_price'];

                $('#box_add_scope_request .addscope-main-item-row[data-exempt="0"]').each(function () {
                    var $mainRow = $(this);
                    if (!$mainRow.find('.addscope_checkbox').is(':checked')) return;

                    var item_id = $mainRow.data('item-id');
                    var $panel  = $mainRow.closest('.addscope-panel');
                    var $detailsRow = $panel.find('.addscope-details-row[data-item-id="' + item_id + '"]');

                    $mainRow.find('.addscope_tool_chk:checked').each(function () {
                        var tool_id  = $(this).data('tool-id');
                        var $toolRow = $detailsRow.find('.addscope-tool-detail-row[data-tool-id="' + tool_id + '"]');

                        // เครื่องมือ "ตรวจพินิจ" ไม่ต้องกรอกข้อมูลใดๆ ข้ามการเช็ค required
                        if ($toolRow.hasClass('addscope-exempt-tool-row')) return;

                        requiredFieldNames.forEach(function (fieldName) {
                            var $field = $toolRow.find('[name="' + fieldName + '"]');
                            if ($.trim($field.val() || '') === '') {
                                $field.addClass('addscope_field_error');
                                if (!$firstMissing) { $firstMissing = $field; }
                            }
                        });
                    });
                });

                if ($firstMissing) {
                    alert('กรุณากรอกข้อมูล ขีดความสามารถ, ช่วงการใช้งาน, ความละเอียดที่อ่านได้, ความคลาดเคลื่อนที่ยอมรับ, ระยะการทดสอบ(วัน), ค่าใช้จ่าย/ชุดละ ให้ครบทุกช่องที่มีกรอบสีแดง (เฉพาะแถวที่เลือกเครื่องมือไว้)');
                    $firstMissing.focus();
                    return;
                }

                // 1 คำขอ ยื่นเพิ่มได้ 1 มอก. เท่านั้น — ห้ามซ้อนกับ มอก. ที่เลือกในช่อง "เลือก มอก." ด้านบน หรือแถวขอเพิ่ม มอก. อื่นในตาราง
                var newTisId = String($panels.first().data('tis-id'));
                var selectedAbove = window.lspSelectedTis ? window.lspSelectedTis() : '';
                var otherInTable = $('#table-scope input[name="add_scope_tis_id[]"]').filter(function () { return String($(this).val()) !== newTisId; }).length > 0;
                if (selectedAbove || otherInTable) {
                    alert('คำขอนี้ยื่นเพิ่มขอบข่ายได้เพียง 1 มอก. ต่อ 1 คำขอ
' + (selectedAbove ? 'กรุณายกเลิกการเลือก มอก. ในช่อง "เลือก มอก." ด้านบนก่อน' : 'กรุณาลบรายการขอเพิ่ม มอก. อื่นในตารางก่อน'));
                    return;
                }

                if (!confirm('ยืนยันการเพิ่มขอบข่ายลงในรายการ?')) return;

                var currentRowCount = $('#table-scope tbody tr').not('#table-scope-empty-msg').length;

                $panels.each(function () {
                    var $panel  = $(this);
                    var tis_id  = $panel.data('tis-id');
                    var tisno   = $panel.data('tisno');

                    // แสดง "ขอบข่ายเดิม" ของ มอก. นี้ในตารางหลักด้วย (ถ้ายังไม่เคยแสดง) เพื่อให้เห็นบริบทว่า
                    // มอก. นี้เดิมมีขอบข่ายอะไรอยู่แล้วบ้าง คู่กับรายการที่กำลังขอเพิ่มใหม่
                    var hasExistingRowAlready = $('#table-scope tbody tr.existing-scope-row[data-existing-tis="' + tis_id + '"]').length > 0;
                    var hasExistingData = window.existingScopeRowsByTis && window.existingScopeRowsByTis[tis_id];
                    if (!hasExistingRowAlready && hasExistingData) {
                        window.existingScopeRowsByTis[tis_id].forEach(function (rowHtml) {
                            currentRowCount++;
                            $('#table-scope tbody').append(rowHtml.replace('##ROWNO##', currentRowCount));
                        });
                    }

                    $panel.find('.addscope-main-item-row').each(function () {
                        var $mainRow = $(this);
                        if (!$mainRow.find('.addscope_checkbox').is(':checked')) return;

                        var item_id    = $mainRow.data('item-id');
                        var isExempt   = $mainRow.data('exempt') == '1';
                        var test_item_text = $mainRow.find('.addscope-item-title').text();

                        if (isExempt) {
                            currentRowCount++;
                            pushAddScopeRow(currentRowCount, tisno, test_item_text, {
                                tis_id: tis_id, tis_tisno: tisno, test_item_id: item_id,
                                test_tools_id: '', test_tools_custom_name: '', test_tools_no: '',
                                tool_name: 'ตรวจพินิจ',
                                capacity: '', range: '', true_value: '', fault_value: '',
                                test_duration: '', test_price: ''
                            });
                        } else {
                            $mainRow.find('.addscope_tool_chk:checked').each(function () {
                                var tool_id  = $(this).data('tool-id');
                                var $detailsRow = $panel.find('.addscope-details-row[data-item-id="' + item_id + '"]');
                                var $toolRow    = $detailsRow.find('.addscope-tool-detail-row[data-tool-id="' + tool_id + '"]');

                                var isCustom = String(tool_id).indexOf('custom_') === 0;

                                currentRowCount++;
                                pushAddScopeRow(currentRowCount, tisno, test_item_text, {
                                    tis_id: tis_id,
                                    tis_tisno: tisno,
                                    test_item_id: item_id,
                                    test_tools_id: isCustom ? '' : tool_id,
                                    test_tools_custom_name: isCustom ? $toolRow.find('.addscope_field_custom_name').val() : '',
                                    test_tools_no: $toolRow.find('[name="test_tools_no"]').val() || '',
                                    tool_name: $toolRow.find('td:first strong').first().text(),
                                    capacity: $toolRow.find('[name="capacity"]').val() || '',
                                    range: $toolRow.find('[name="range"]').val() || '',
                                    true_value: $toolRow.find('[name="true_value"]').val() || '',
                                    fault_value: $toolRow.find('[name="fault_value"]').val() || '',
                                    test_duration: $toolRow.find('[name="test_duration"]').val() || '',
                                    test_price: $toolRow.find('[name="test_price"]').val() || ''
                                });
                            });
                        }
                    });
                });

                // อัปเดตสถานะแถว "ไม่มีข้อมูล" ของ #table-scope (ซ่อนเพราะตอนนี้มีแถวจริงแล้ว)
                if (window.reloadScopeTable) { window.reloadScopeTable(); }

                // เคลียร์ modal พร้อมใช้งานรอบถัดไป
                $('#box_add_scope_request').empty();
                $('#btn_submit_add_scope').hide();
                $('#AddScopeModal').modal('hide');

                alert('เพิ่มลงรายการสำเร็จ !\nกรุณาตรวจสอบรายการในตารางและกดบันทึกเมื่อพร้อม');
            });

        });

        function toggleAddScopeSubmitButton() {
            var hasChecked = $('#box_add_scope_request .addscope_checkbox:checked').length > 0;
            hasChecked ? $('#btn_submit_add_scope').show() : $('#btn_submit_add_scope').hide();
        }

        function pushAddScopeRow(rowNo, tisno, testItemText, data) {
            var _newRow = '';
            _newRow += '<tr style="background-color: #fffde7;" class="pending-scope-row">';
            _newRow += '<td class="text-center">' + rowNo + '</td>';
            _newRow += '<td class="text-center">' + tisno + '</td>';
            _newRow += '<td>' + testItemText;
            _newRow += '<div class="table-responsive" style="margin-top:6px;">';
            _newRow += '<table class="table table-condensed table-bordered" style="margin:0;font-size:12px;background:#fff;">';
            _newRow += '<thead><tr>';
            _newRow += '<th>เครื่องมือที่ใช้</th><th>รหัส/หมายเลข</th><th>ขีดความสามารถ</th>';
            _newRow += '<th>ช่วงการใช้งาน</th><th>ความละเอียดที่อ่านได้</th>';
            _newRow += '<th>ความคลาดเคลื่อนที่ยอมรับ</th><th>ระยะการทดสอบ(วัน)</th><th>ค่าใช้จ่าย/ชุดละ</th>';
            _newRow += '</tr></thead><tbody><tr>';
            _newRow += '<td>' + (data.tool_name || '-') + '</td>';
            _newRow += '<td>' + (data.test_tools_no || '-') + '</td>';
            _newRow += '<td>' + (data.capacity || '-') + '</td>';
            _newRow += '<td>' + (data.range || '-') + '</td>';
            _newRow += '<td>' + (data.true_value || '-') + '</td>';
            _newRow += '<td>' + (data.fault_value || '-') + '</td>';
            _newRow += '<td>' + (data.test_duration || '-') + '</td>';
            _newRow += '<td>' + (data.test_price || '-') + '</td>';
            _newRow += '</tr></tbody></table>';
            _newRow += '</div>';
            _newRow += '</td>';
            _newRow += '<td class="text-center"><span class="label label-info">ขอเพิ่มขอบข่าย</span></td>';
            _newRow += '<td class="text-center"><span class="label label-warning"><i class="fa fa-clock-o"></i> ฉบับร่าง</span>';
            _newRow += '  <button type="button" class="btn btn-danger btn-xs btn_remove_pending_scope" title="ลบรายการนี้"><i class="fa fa-trash-o"></i></button>';
            _newRow += '<input type="hidden" name="add_scope_tis_id[]" value="' + data.tis_id + '">';
            _newRow += '<input type="hidden" name="add_scope_tis_tisno[]" value="' + data.tis_tisno + '">';
            _newRow += '<input type="hidden" name="add_scope_test_item_id[]" value="' + data.test_item_id + '">';
            _newRow += '<input type="hidden" name="add_scope_test_tools_id[]" value="' + data.test_tools_id + '">';
            _newRow += '<input type="hidden" name="add_scope_test_tools_custom_name[]" value="' + data.test_tools_custom_name + '">';
            _newRow += '<input type="hidden" name="add_scope_test_tools_no[]" value="' + data.test_tools_no + '">';
            _newRow += '<input type="hidden" name="add_scope_capacity[]" value="' + data.capacity + '">';
            _newRow += '<input type="hidden" name="add_scope_range[]" value="' + data.range + '">';
            _newRow += '<input type="hidden" name="add_scope_true_value[]" value="' + data.true_value + '">';
            _newRow += '<input type="hidden" name="add_scope_fault_value[]" value="' + data.fault_value + '">';
            _newRow += '<input type="hidden" name="add_scope_test_duration[]" value="' + data.test_duration + '">';
            _newRow += '<input type="hidden" name="add_scope_test_price[]" value="' + data.test_price + '">';
            // ช่องระดับรายการ (ราคา/วิธีทดสอบ/หมายเหตุ) ไม่มีในโมดัลนี้ — ส่งค่าว่างเพื่อให้ array คู่ขนานไม่เลื่อน และ item_fields=0 = ไม่บังคับ
            _newRow += '<input type="hidden" name="add_scope_test_price_set[]" value="">';
            _newRow += '<input type="hidden" name="add_scope_test_method_type[]" value="">';
            _newRow += '<input type="hidden" name="add_scope_test_method_other[]" value="">';
            _newRow += '<input type="hidden" name="add_scope_lab_remark[]" value="">';
            _newRow += '<input type="hidden" name="add_scope_item_fields[]" value="0">';
            _newRow += '<input type="hidden" name="add_scope_type[]" value="2">';
            _newRow += '</td>';
            _newRow += '</tr>';
            $('#table-scope tbody').append(_newRow);
        }

        // sync สถานะ show/hide/disabled/required ของแถวรายละเอียดเครื่องมือ ให้ตรงกับ checkbox ที่ถูกเลือกอยู่จริง
        // (เลือกได้หลายตัวต่อรายการทดสอบ)
        function syncAddScopeToolSelectionForItem(tis_id, item_id) {
            var $panel      = $('#panel-addscope-' + tis_id);
            var $mainRow    = $panel.find('.addscope-main-item-row[data-item-id="' + item_id + '"]');
            var $detailsRow = $panel.find('.addscope-details-row[data-item-id="' + item_id + '"]');
            var anyToolChecked = false;

            $mainRow.find('.addscope_tool_chk[data-item-id="' + item_id + '"]').each(function () {
                var tool_id  = $(this).data('tool-id');
                var $toolRow = $detailsRow.find('.addscope-tool-detail-row[data-tool-id="' + tool_id + '"]');

                if (this.checked) {
                    anyToolChecked = true;
                    $toolRow.show().find('.addscope_field').prop('disabled', false);
                    var isExemptTool = (String(tool_id) === '4');
                    if (!isExemptTool) {
                        $toolRow.find('input.addscope_field:not([type=hidden])[name!="test_tools_no"]').prop('required', true);
                    }
                } else {
                    $toolRow.hide();
                    $toolRow.find('.addscope_field').prop('disabled', true).prop('required', false);
                    $toolRow.find('input.addscope_field:not([type=hidden]):not([readonly])').val('');
                }
            });

            anyToolChecked ? $detailsRow.show() : $detailsRow.hide();

            // ถ้าไม่เหลือเครื่องมือที่เลือกเลย แต่รายการทดสอบยังติ๊กอยู่ ให้ยกเลิกรายการทดสอบนี้ไปด้วย
            if (!anyToolChecked && $mainRow.find('.addscope_checkbox').is(':checked')) {
                alert('ต้องเลือกเครื่องมืออย่างน้อย 1 รายการ ระบบจึงยกเลิกการเลือกรายการทดสอบนี้ให้อัตโนมัติ');
                $mainRow.find('.addscope_checkbox').prop('checked', false).trigger('change');
            }
        }

        function updateAddScopeBadge(tis_id) {
            var $panel   = $('#panel-addscope-' + tis_id);
            var total    = $panel.find('.addscope_checkbox').length;
            var selected = $panel.find('.addscope_checkbox:checked').length;
            var $badge   = $panel.find('.addscope-selected-badge');
            $badge.text(selected + '/' + total + ' เลือกแล้ว');
            selected > 0 ? $badge.removeClass('none') : $badge.addClass('none');
        }

        function loadAddScopeByTis(tis_id, tisno, tisname) {
            var ajaxUrl = "{!! url('/request-section-5/application-lab/get-items-with-tools') !!}" + '/' + tis_id;
            $.LoadingOverlay('show', { image: '', text: 'Loading...' });
            $.ajax({
                url: ajaxUrl,
                dataType: 'json'
            }).done(function (items) {
                $.LoadingOverlay('hide', true);
                if (!Array.isArray(items) || items.length === 0) { alert('ไม่พบรายการทดสอบสำหรับ มอก. นี้'); return; }
                renderAddScopeBlock(tis_id, tisno, tisname, items);
            }).fail(function (xhr) {
                $.LoadingOverlay('hide', true);
                if (xhr.status === 401 || xhr.status === 302 || (xhr.responseText || '').indexOf('login') !== -1) {
                    alert('เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่แล้วลองอีกครั้ง');
                } else {
                    alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
                }
            });
        }

        function renderAddScopeBlock(tis_id, tisno, tisname, items) {

            var totalItems = items.length;

            var html  = '<div class="panel panel-default addscope-panel" id="panel-addscope-' + tis_id + '" data-tis-id="' + tis_id + '" data-tisno="' + tisno + '">';

            html += '<div class="panel-heading" role="tab">';
            html += '<div class="row" style="margin:0;">';
            html += '<div class="col-xs-8" style="padding:0;">';
            html += '<h5 class="panel-title" style="margin:0; line-height:30px;">';
            html += '<a data-toggle="collapse" href="#collapse-addscope-' + tis_id + '" style="display:inline;">';
            html += '<i class="fa fa-chevron-up m-r-5"></i>';
            html += 'มอก. ' + tisno + ' — ' + tisname;
            html += '</a>';
            html += '<span class="badge addscope-selected-badge none" style="margin-left:8px;">0/' + totalItems + ' เลือกแล้ว</span>';
            html += '</h5></div>';
            html += '<div class="col-xs-4 text-right" style="padding:0;">';
            html += '<button class="btn btn-danger btn-sm addscope_btn_section_remove" type="button"><i class="fa fa-trash"></i> ลบ มอก. นี้</button>';
            html += '</div></div></div>';

            html += '<div id="collapse-addscope-' + tis_id + '" class="panel-collapse collapse in">';
            html += '<div class="panel-body" style="padding: 10px;">';
            html += '<div class="table-responsive">';
            html += '<table class="table table-bordered inner-repeater" id="table-addscope-group-' + tis_id + '" style="margin-bottom:0;">';

            html += '<thead><tr style="background:#f0f4ff;">';
            html += '<th width="5%"  class="text-center">รายการทดสอบ<br>ที่ต้องการเพิ่ม</th>';
            html += '<th width="6%"  class="text-center">ข้อ</th>';
            html += '<th width="44%" class="text-center">รายการทดสอบ</th>';
            html += '<th width="45%" class="text-center">เครื่องมือที่ใช้</th>';
            html += '</tr></thead>';
            html += '<tbody>';

            items.forEach(function (item) {
                var isExemptItem = (String(item.test_method_id) === '18');
                var indentPx = (item.level || 0) * 20;

                html += '<tr class="addscope-main-item-row addscope-row-disabled" data-item-id="' + item.id + '" data-exempt="' + (isExemptItem ? '1' : '0') + '" style="background:#fafafa;">';
                html += '<td class="text-center" style="vertical-align:top; padding-top:10px;">';
                html += '<input type="checkbox" class="addscope_checkbox" data-item-id="' + item.id + '" data-tis-id="' + tis_id + '" style="width:18px;height:18px;cursor:pointer;">';
                html += '</td>';
                html += '<td style="vertical-align:top; padding-top:10px; padding-left:' + (8 + indentPx) + 'px; font-weight:bold; white-space:nowrap;">' + (item.no || '') + '</td>';
                html += '<td class="addscope-item-title" style="vertical-align:top; padding-top:10px; padding-left:' + (8 + indentPx) + 'px;">' + item.title + '</td>';

                if (isExemptItem) {
                    html += '<td style="vertical-align:top; padding-top:10px;"><em class="text-muted">ตรวจพินิจ — ไม่ต้องระบุรายละเอียด</em></td>';
                } else {
                    html += '<td style="vertical-align:top;"><div class="addscope-tool-chk-group">';
                    if (item.tools && item.tools.length > 0) {
                        item.tools.forEach(function (tool) {
                            html += '<label class="addscope-chk-tool-lbl">';
                            html += '<input type="checkbox" name="addscope_tool_chk_' + item.id + '[]" class="addscope_tool_chk" disabled data-item-id="' + item.id + '" data-tool-id="' + tool.id + '"> ' + tool.title;
                            html += '</label>';
                        });
                    } else {
                        html += '<span class="text-muted small">— ไม่มีข้อมูลเครื่องมือ —</span>';
                    }
                    html += '</div>';
                    html += '<div style="margin-top:4px;">';
                    html += '<button type="button" class="btn btn-xs btn-info addscope-btn-add-custom-tool" onclick="toggleAddScopeToolForm(' + item.id + '); return false;"><i class="fa fa-plus"></i> เพิ่มเครื่องมือ</button>';
                    html += '</div>';
                    html += '<div class="addscope-add-tool-form" id="addscope-add-tool-form-' + item.id + '" style="display:none; margin-top:5px;">';
                    html += '<div class="input-group input-group-sm">';
                    html += '<input type="text" class="form-control" id="addscope-add-tool-input-' + item.id + '" placeholder="ระบุชื่อเครื่องมือ">';
                    html += '<span class="input-group-btn">';
                    html += '<button type="button" class="btn btn-success btn-sm" onclick="saveAddScopeCustomTool(' + item.id + ', \'' + tis_id + '\'); return false;"><i class="fa fa-save"></i> บันทึก</button>';
                    html += '<button type="button" class="btn btn-default btn-sm" onclick="toggleAddScopeToolForm(' + item.id + '); return false;">ยกเลิก</button>';
                    html += '</span></div></div>';
                    html += '</td>';
                }
                html += '</tr>';

                if (isExemptItem) {
                    html += '<tr class="addscope-details-row addscope-exempt-method-row" data-item-id="' + item.id + '" style="display:none;">';
                    html += '<td colspan="4">';
                    html += '<table class="table inner-detail-table" style="margin:0;">';
                    html += '<thead><tr>';
                    html += '<th>เครื่องมือที่ใช้</th><th>รหัส/หมายเลข</th><th>ขีดความสามารถ</th>';
                    html += '<th>ช่วงการใช้งาน</th><th>ความละเอียดที่อ่านได้</th>';
                    html += '<th>ความคลาดเคลื่อนที่ยอมรับ</th><th>ระยะการทดสอบ(วัน)</th><th>ค่าใช้จ่าย/ชุดละ</th>';
                    html += '</tr></thead>';
                    html += '<tbody><tr>';
                    html += '<td><strong>ตรวจพินิจ</strong>';
                    html += '<input type="hidden" name="test_tools_custom_name" class="addscope_field addscope_field_custom_name" value="" disabled>';
                    html += '</td>';
                    html += '<td><input type="text" name="test_tools_no" class="form-control input-sm addscope_field" value="" readonly disabled></td>';
                    html += '<td><input type="text" name="capacity"      class="form-control input-sm addscope_field" value="" readonly disabled></td>';
                    html += '<td><input type="text" name="range"         class="form-control input-sm addscope_field" value="" readonly disabled></td>';
                    html += '<td><input type="text" name="true_value"    class="form-control input-sm addscope_field" value="" readonly disabled></td>';
                    html += '<td><input type="text" name="fault_value"   class="form-control input-sm addscope_field" value="" readonly disabled></td>';
                    html += '<td><input type="text" name="test_duration" class="form-control input-sm addscope_field" value="" readonly disabled></td>';
                    html += '<td><input type="text" name="test_price"    class="form-control input-sm addscope_field" value="" readonly disabled></td>';
                    html += '</tr></tbody></table>';
                    html += '</td></tr>';
                } else {
                    html += '<tr class="addscope-details-row" data-item-id="' + item.id + '" style="display:none;">';
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
                            // เครื่องมือ "ตรวจพินิจ" (id = 4) ห้ามกรอกข้อมูลใดๆ แสดงเป็นสีเทาอย่างเดียว
                            var isExemptTool = (String(tool.id) === '4');
                            var readonlyAttr = isExemptTool ? 'readonly' : '';

                            html += '<tr class="addscope-tool-detail-row' + (isExemptTool ? ' addscope-exempt-tool-row' : '') + '" data-tool-id="' + tool.id + '" style="display:none;">';
                            html += '<td><strong>' + tool.title + '</strong>';
                            html += '<input type="hidden" name="test_tools_custom_name" class="addscope_field addscope_field_custom_name" value="" disabled>';
                            html += '</td>';
                            html += '<td><input type="text" name="test_tools_no" class="form-control input-sm addscope_field" value="" disabled ' + readonlyAttr + '></td>';
                            html += '<td><input type="text" name="capacity"      class="form-control input-sm addscope_field" value="" disabled ' + readonlyAttr + '></td>';
                            html += '<td><input type="text" name="range"         class="form-control input-sm addscope_field" value="" disabled ' + readonlyAttr + '></td>';
                            html += '<td><input type="text" name="true_value"    class="form-control input-sm addscope_field" value="" disabled ' + readonlyAttr + '></td>';
                            html += '<td><input type="text" name="fault_value"   class="form-control input-sm addscope_field" value="" disabled ' + readonlyAttr + '></td>';
                            html += '<td><input type="text" name="test_duration" class="form-control input-sm addscope_field" value="" disabled ' + readonlyAttr + '></td>';
                            html += '<td><input type="text" name="test_price"    class="form-control input-sm addscope_field" value="" disabled ' + readonlyAttr + '></td>';
                            html += '</tr>';
                        });
                    }
                    html += '</tbody></table></td></tr>';
                }
            });

            html += '</tbody></table></div></div></div></div>';

            $('#box_add_scope_request').append(html);

            $('#collapse-addscope-' + tis_id).on('show.bs.collapse', function () { $('#panel-addscope-' + tis_id).find('.fa').removeClass('fa-chevron-down').addClass('fa-chevron-up'); });
            $('#collapse-addscope-' + tis_id).on('hide.bs.collapse', function () { $('#panel-addscope-' + tis_id).find('.fa').removeClass('fa-chevron-up').addClass('fa-chevron-down'); });
        }

        function toggleAddScopeToolForm(item_id) {
            $('#addscope-add-tool-form-' + item_id).toggle();
            if ($('#addscope-add-tool-form-' + item_id).is(':visible')) {
                $('#addscope-add-tool-input-' + item_id).focus();
            }
        }

        function saveAddScopeCustomTool(item_id, tis_id) {
            var tool_name = $.trim($('#addscope-add-tool-input-' + item_id).val());
            if (!tool_name) { alert('กรุณาระบุชื่อเครื่องมือ'); return; }

            var $panel      = $('#panel-addscope-' + tis_id);
            var $mainRow    = $panel.find('.addscope-main-item-row[data-item-id="' + item_id + '"]');
            var $detailsRow = $panel.find('.addscope-details-row[data-item-id="' + item_id + '"]');
            var isItemChecked = $mainRow.find('.addscope_checkbox').is(':checked');
            var disAttr = isItemChecked ? '' : 'disabled';

            var tempToolId = 'custom_' + Date.now();

            // เพิ่ม checkbox ในรายการเครื่องมือ (เครื่องมือเดิมที่เลือกไว้แล้วไม่ถูกยกเลิก เพราะเลือกได้หลายตัว)
            var chkHtml  = '<label class="addscope-chk-tool-lbl">';
                chkHtml += '<input type="checkbox" name="addscope_tool_chk_' + item_id + '[]" class="addscope_tool_chk" ' + (isItemChecked ? 'checked' : '') + ' ' + disAttr;
                chkHtml += ' data-item-id="' + item_id + '" data-tool-id="' + tempToolId + '"> ' + tool_name;
                chkHtml += '</label>';
            $mainRow.find('.addscope-tool-chk-group').append(chkHtml);

            var rowHtml  = '<tr class="addscope-tool-detail-row" data-tool-id="' + tempToolId + '" style="' + (isItemChecked ? '' : 'display:none;') + '">';
                rowHtml += '<td><strong>' + tool_name + '</strong>';
                rowHtml += '<input type="hidden" class="addscope_field addscope_field_custom_name" value="' + tool_name + '" ' + disAttr + '>';
                rowHtml += '</td>';
                rowHtml += '<td><input type="text" name="test_tools_no" class="form-control input-sm addscope_field" value="" ' + disAttr + '></td>';
                rowHtml += '<td><input type="text" name="capacity"      class="form-control input-sm addscope_field" value="" ' + disAttr + '></td>';
                rowHtml += '<td><input type="text" name="range"         class="form-control input-sm addscope_field" value="" ' + disAttr + '></td>';
                rowHtml += '<td><input type="text" name="true_value"    class="form-control input-sm addscope_field" value="" ' + disAttr + '></td>';
                rowHtml += '<td><input type="text" name="fault_value"   class="form-control input-sm addscope_field" value="" ' + disAttr + '></td>';
                rowHtml += '<td><input type="text" name="test_duration" class="form-control input-sm addscope_field" value="" ' + disAttr + '></td>';
                rowHtml += '<td><input type="text" name="test_price"    class="form-control input-sm addscope_field" value="" ' + disAttr + '></td>';
                rowHtml += '</tr>';
            $detailsRow.find('tbody').append(rowHtml);

            // sync สถานะแถวรายละเอียดให้ตรงกับเครื่องมือที่เลือกไว้ (รวมตัวที่เพิ่งเพิ่มใหม่)
            syncAddScopeToolSelectionForItem(tis_id, item_id);

            $('#addscope-add-tool-form-' + item_id).hide();
            $('#addscope-add-tool-input-' + item_id).val('');

            updateAddScopeBadge(tis_id);
        }
    </script>
@endpush
