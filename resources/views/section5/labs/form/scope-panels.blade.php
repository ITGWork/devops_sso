{{-- panel ขอบข่ายปัจจุบันของ lab (ต่อ มอก.) — ข้อมูล $lab_panels / $pending_add_keys มาจาก form/scope.blade.php
     เครื่องมือของแต่ละรายการทดสอบไม่แสดงในฟอร์ม เปิดดู/เพิ่มผ่าน modal (#LspToolsModal) เท่านั้น
     กด "ตกลง" ใน modal แล้วค่อยสร้าง hidden add_scope_*[] ไว้ในแถว (modal อยู่นอก <form> ส่งค่าเองไม่ได้) --}}
@push('css')
<style>
    #box_lab_scope_panels .panel { box-shadow: 0 1px 4px rgba(0,0,0,.1); margin-bottom: 10px; }
    #box_lab_scope_panels .lsp-panel .panel-heading { background: #f0f4ff; border-color: #c5d0e6; padding: 10px 15px; }
    #box_lab_scope_panels .lsp-panel .panel-title a { font-size: 15px; font-weight: bold; color: #333; text-decoration: none; }
    #box_lab_scope_panels .lsp-badge { font-size: 12px; margin-left: 8px; background: #009efb; vertical-align: middle; }
    #box_lab_scope_panels .lsp-table thead th { background: #f0f4ff; text-align: center; vertical-align: middle; }
    #box_lab_scope_panels .lsp-table td { vertical-align: middle; }
    #box_lab_scope_panels .lsp-st-ok { color: #2ecc71; font-weight: bold; }
    #box_lab_scope_panels a.lsp-st-ok { cursor: pointer; text-decoration: underline; }
    #box_lab_scope_panels .lsp-st-no { color: #e74c3c; font-weight: bold; }
    #box_lab_scope_panels .lsp-req { color: #e0a800; font-weight: bold; margin-left: 8px; }
    #box_lab_scope_panels .lsp-row-req > td { background: #fffdf2; }
    #box_lab_scope_panels .lsp-err { border: 2px solid #dd4b39 !important; background-color: #fff6f6; }
    #lsp_modal .lspm-old-table th { background: #dff0d8; font-size: 12px; white-space: nowrap; }
    #lsp_modal .lspm-new-table th { background: #eef2f5; font-size: 12px; white-space: nowrap; }
    #lsp_modal .lspm-old-table td, #lsp_modal .lspm-new-table td { font-size: 12px; vertical-align: middle; padding: 4px 6px; }
    #lsp_modal .lspm-err { border: 2px solid #dd4b39 !important; background-color: #fff6f6; }
    #lsp_modal .lspm-row-off input.lspm-f { background: #f3f3f3; }
</style>
@endpush

@push('js')
<script>
(function () {
    var panelsData  = @json($lab_panels);
    var pendingKeys = @json($pending_add_keys);
    var pendingRows = @json($pending_add_rows);   // รายการขอเพิ่มที่ร่างไว้ (คำขอที่ตีกลับ/แก้ไข) ใช้เติมค่าเดิมกลับเข้า panel
    var itemsUrl    = "{!! url('/request-section-5/application-lab/get-items-with-tools') !!}";
    var FIELDS = [
        ['no', 'รหัส/หมายเลข', false], ['capacity', 'ขีดความสามารถ', true], ['range', 'ช่วงการใช้งาน', true],
        ['true_value', 'ความละเอียดที่อ่านได้', true], ['fault_value', 'ความคลาดเคลื่อนที่ยอมรับ', true],
        ['test_duration', 'ระยะการทดสอบ(วัน)', true], ['test_price', 'ค่าใช้จ่าย/ชุดละ', false]
    ];
    var NAME_OF = { no: 'add_scope_test_tools_no', capacity: 'add_scope_capacity', range: 'add_scope_range', true_value: 'add_scope_true_value',
                    fault_value: 'add_scope_fault_value', test_duration: 'add_scope_test_duration', test_price: 'add_scope_test_price' };
    var store = {};      // key "tid|itemId" → { tid, tisno, item, appointed, oldTools, avail, tools:[], checked, price, method, methodOther, remark }
    var curKey = null;
    var $box;

    function esc(s) { return $('<div>').text(s == null ? '' : String(s)).html(); }
    function escAttr(s) { return esc(s).replace(/"/g, '&quot;'); }
    function hid(name, val) { return '<input type="hidden" name="' + name + '[]" value="' + escAttr(val) + '">'; }
    function keyOf(tid, itemId) { return tid + '|' + itemId; }

    // ชุด field ที่ final_submit_scope() อ่านเป็น array คู่ขนานกัน — ต้องส่งครบทุกตัวในทุกแถว ไม่งั้น index เลื่อน
    function rowInputs(s, toolId, custom, vals) {
        var h = hid('add_scope_tis_id', s.tid) + hid('add_scope_tis_tisno', s.tisno) + hid('add_scope_test_item_id', s.item.id)
              + hid('add_scope_test_tools_id', toolId) + hid('add_scope_test_tools_custom_name', custom) + hid('add_scope_type', 2);
        FIELDS.forEach(function (f) { h += hid(NAME_OF[f[0]], vals ? (vals[f[0]] || '') : ''); });
        // ช่องระดับรายการ (ตารางหน้า labs/show) ซ้ำในทุกแถวเครื่องมือของรายการนั้น; item_fields=1 = server บังคับ ราคา/วิธีทดสอบ
        h += hid('add_scope_test_price_set', s.price) + hid('add_scope_test_method_type', s.method)
           + hid('add_scope_test_method_other', (s.method === '2' || s.method === '3') ? s.methodOther : '') + hid('add_scope_lab_remark', s.remark) + hid('add_scope_item_fields', 1);
        return h;
    }

    function fieldLabelHead() {
        var h = '<th>เครื่องมือที่ใช้</th>';
        FIELDS.forEach(function (f) { h += '<th>' + f[1] + (f[2] ? ' <span class="text-danger">*</span>' : '') + '</th>'; });
        return h + '<th width="5%">ลบ</th>';
    }

    // ───────────────────────── modal ─────────────────────────
    function ensureModal() {
        if ($('#lsp_modal').length) return;
        $('body').append(
            '<div class="modal fade" id="lsp_modal" tabindex="-1" role="dialog" aria-hidden="true">'
          + '<div class="modal-dialog modal-lg" style="width:92%; max-width:1250px;"><div class="modal-content">'
          + '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button><h4 class="modal-title" id="lspm_title"></h4></div>'
          + '<div class="modal-body" id="lspm_body"></div>'
          + '<div class="modal-footer"><button type="button" class="btn btn-success" id="lspm_ok">ตกลง</button> '
          + '<button type="button" class="btn btn-warning" id="lspm_clear">ล้างรายการที่เพิ่ม</button> '
          + '<button type="button" class="btn btn-danger" data-dismiss="modal">ปิด</button></div>'
          + '</div></div></div>');
    }

    function modalToolRow(tool, vals, checked) {
        var exempt = String(tool.id) === '4';
        var h = '<tr class="lspm-tool-row' + (checked ? '' : ' lspm-row-off') + '" data-tool-id="' + escAttr(tool.id) + '" data-custom="' + (tool.isCustom ? 1 : 0) + '">';
        h += '<td><label style="font-weight:normal; margin:0; cursor:pointer;"><input type="checkbox" class="lspm-chk" ' + (checked ? 'checked' : '') + '> <span class="lspm-tname">' + esc(tool.title) + '</span></label></td>';
        FIELDS.forEach(function (f) {
            h += '<td><input type="text" class="form-control input-sm lspm-f" data-f="' + f[0] + '" value="' + escAttr(vals ? (vals[f[0]] || '') : '') + '" '
               + (checked ? '' : 'disabled') + (exempt ? ' readonly' : '') + '></td>';
        });
        // ลบได้เฉพาะเครื่องมือที่เพิ่มเอง (พิมพ์ชื่อเข้ามา) — เครื่องมือมาตรฐานของรายการทดสอบไม่มีปุ่มลบ ใช้ติ๊กออกแทน
        h += '<td class="text-center">' + (tool.isCustom ? '<button type="button" class="btn btn-danger btn-xs lspm-del" title="ลบเครื่องมือนี้"><i class="fa fa-trash-o"></i></button>' : '') + '</td>';
        return h + '</tr>';
    }

    function openModal(key) {
        var s = store[key];
        if (!s) return;
        curKey = key;
        ensureModal();
        $('#lspm_title').html('เครื่องมือที่ใช้ — ' + (s.item.no ? 'ข้อ ' + esc(s.item.no) + ' ' : '') + esc(s.item.title)
            + '<br><small class="text-muted">มอก. ' + esc(s.tisno) + '</small>');

        var h = '';
        if (s.appointed) {
            h += '<h5 style="color:#3c763d;"><b><i class="fa fa-check-circle"></i> เครื่องมือที่ได้รับการแต่งตั้งแล้ว (ดูอย่างเดียว ไม่ต้องยื่นซ้ำ)</b></h5>';
            if (s.oldTools.length) {
                h += '<div class="table-responsive"><table class="table table-bordered lspm-old-table"><thead><tr><th>เครื่องมือที่ได้รับแล้ว</th>';
                FIELDS.forEach(function (f) { h += '<th>' + f[1] + '</th>'; });
                h += '</tr></thead><tbody>';
                s.oldTools.forEach(function (t) {
                    h += '<tr><td><strong>' + esc(t.tool) + '</strong></td><td>' + esc(t.no) + '</td><td>' + esc(t.capacity) + '</td><td>' + esc(t.range) + '</td>'
                       + '<td>' + esc(t.true_value) + '</td><td>' + esc(t.fault_value) + '</td><td>' + esc(t.test_duration) + '</td><td>' + esc(t.test_price) + '</td></tr>';
                });
                h += '</tbody></table></div>';
            } else {
                h += '<p class="text-muted">ไม่มีข้อมูลเครื่องมือเดิม</p>';
            }
            h += '<hr>';
        }

        h += '<h5><b><i class="fa fa-plus-circle text-info"></i> ' + (s.appointed ? 'เพิ่มเครื่องมือใหม่' : 'เครื่องมือที่ขอรับการแต่งตั้ง') + '</b> <small class="text-muted">(ติ๊กเครื่องมือที่ต้องการยื่น แล้วกรอกรายละเอียด)</small></h5>';
        h += '<div class="table-responsive"><table class="table table-bordered lspm-new-table"><thead><tr>' + fieldLabelHead() + '</tr></thead><tbody id="lspm_tools">';
        var used = {};
        s.tools.forEach(function (t) { used[String(t.id)] = t; });
        s.avail.forEach(function (tool) {
            var saved = used[String(tool.id)];
            h += modalToolRow({ id: tool.id, title: tool.title, isCustom: false }, saved ? saved.vals : null, !!saved);
        });
        s.tools.forEach(function (t) {
            if (t.isCustom) { h += modalToolRow({ id: t.id, title: t.title, isCustom: true }, t.vals, true); }
        });
        h += '</tbody></table></div>';
        h += '<div class="row"><div class="col-md-6"><div class="input-group input-group-sm">'
           + '<input type="text" class="form-control" id="lspm_custom_name" placeholder="ไม่พบเครื่องมือ? ระบุชื่อเครื่องมือเอง">'
           + '<span class="input-group-btn"><button type="button" class="btn btn-info" id="lspm_custom_add"><i class="fa fa-plus"></i> เพิ่มเครื่องมือ</button></span></div></div></div>';
        $('#lspm_body').html(h);
        $('#lspm_clear').toggle(s.tools.length > 0);
        $('#lsp_modal').modal('show');
    }

    function readModal() {
        var out = [], firstBad = null, tick = 0;
        $('#lspm_tools .lspm-tool-row').each(function () {
            var $tr = $(this);
            if (!$tr.find('.lspm-chk').is(':checked')) return;
            tick++;
            var toolId = String($tr.attr('data-tool-id'));
            var vals = {};
            $tr.find('.lspm-f').each(function () {
                var f = $(this).data('f');
                vals[f] = $.trim($(this).val());
                var need = FIELDS.filter(function (x) { return x[0] === f; })[0][2] && toolId !== '4';
                if (need && vals[f] === '') { $(this).addClass('lspm-err'); if (!firstBad) firstBad = $(this); } else { $(this).removeClass('lspm-err'); }
            });
            out.push({ id: toolId, title: $.trim($tr.find('.lspm-tname').text()), isCustom: $tr.attr('data-custom') === '1', vals: vals });
        });
        return { tools: out, ticked: tick, bad: firstBad };
    }

    function applyStore(key) {
        var s = store[key];
        var $row = $box.find('.lsp-item-row[data-key="' + key + '"]');
        if (s.heading) { $row.find('.lsp-chk-req').prop('checked', !!s.checked); return; }
        var h = '';
        if (s.checked) {
            if (s.isExemptItem) { h += rowInputs(s, '', '', null); }
            else { s.tools.forEach(function (t) { h += rowInputs(s, t.isCustom ? '' : t.id, t.isCustom ? t.title : '', t.vals); }); }
        }
        $row.find('.lsp-hidden').html(h);
        $row.find('.lsp-cellbox').toggle(!!s.checked);
        $row.find('.lsp-chk-req').prop('checked', !!s.checked);
        $row.toggleClass('lsp-row-req', !!s.checked);

        var $req = $row.find('.lsp-req').empty();
        var $btn = $row.find('button.lsp-btn-open');
        if (!s.isExemptItem) {
            if (s.tools.length) {
                $btn.html('<i class="fa fa-pencil"></i> แก้ไขเครื่องมือ').removeClass('lsp-err');
                $req.text(s.tools.length + ' รายการ');
            } else {
                $btn.html('<i class="fa fa-plus"></i> เพิ่มเครื่องมือ');
                // ติ๊กขอรับการแต่งตั้งแล้วต้องเลือกเครื่องมืออย่างน้อย 1 รายการ (บังคับกรอก)
                if (s.checked) { $req.html('<span class="text-danger small">* กรุณาเลือกเครื่องมือ</span>'); }
            }
        }
        updateBadge(s.tid);
    }

    function updateBadge(tid) {
        var $p = $('#lsp-panel-' + tid);
        var total = $p.find('.lsp-item-row').not('.lsp-heading-row').length;
        var app = $p.find('.lsp-item-row[data-appointed="1"]').length;
        var added = 0;
        Object.keys(store).forEach(function (k) { var s = store[k]; if (String(s.tid) === String(tid) && s.checked && !s.heading) added++; });
        $p.find('.lsp-badge').text('ได้รับแล้ว ' + app + '/' + total + (added ? ' · ขอเพิ่ม ' + added + ' รายการ' : ''));
    }

    // ───────────────────────── panel ─────────────────────────
    // คีย์เรียงเลขข้อแบบธรรมชาติ (ตรงกับ buildTestItemNoSortKey ฝั่ง server)
    function noKey(no) {
        return String(no || '').split('.').map(function (seg) {
            var m = /^(\d*)(.*)$/.exec($.trim(seg));
            var num = m[1] !== '' ? m[1] : '0';
            return ('000000' + num).slice(-6) + '|' + m[2] + '~';
        }).join('');
    }
    // เรียงรายการเป็นต้นไม้ตาม parent_id: ข้อลูกอยู่ใต้ข้อแม่เสมอ ลูกที่ไม่มีเลขข้อเรียงตาม id (ลำดับในเอกสาร)
    function buildTree(items) {
        var byId = {}, kids = {}, roots = [], out = [];
        items.forEach(function (it) { byId[String(it.id)] = it; });
        items.forEach(function (it) {
            var pid = it.parent_id && byId[String(it.parent_id)] ? String(it.parent_id) : null;
            if (pid) { (kids[pid] = kids[pid] || []).push(it); } else { roots.push(it); }
        });
        function cmp(a, b) {
            var ea = !a.no, eb = !b.no;
            if (ea && eb) return a.id - b.id;
            if (ea !== eb) return ea ? 1 : -1;
            var ka = noKey(a.no), kb = noKey(b.no);
            return ka < kb ? -1 : (ka > kb ? 1 : a.id - b.id);
        }
        function walk(list, depth) {
            list.slice().sort(cmp).forEach(function (it) {
                it._depth = depth;
                out.push(it);
                if (kids[String(it.id)]) walk(kids[String(it.id)], depth + 1);
            });
        }
        walk(roots, 0);
        return out;
    }

    function renderPanel(tid, data, items) {
        var appointed = data.appointed || {};
        // รายการที่ได้รับแล้วแต่ endpoint ไม่คืนมา เติมต่อท้ายเพื่อให้แสดงครบตามที่ได้รับแต่งตั้งจริง
        var have = {};
        items.forEach(function (it) { have[String(it.id)] = 1; });
        (data.appointed_items || []).forEach(function (ai) { if (!have[String(ai.id)]) items.push(ai); });

        var listIds = {};
        items.forEach(function (it) { listIds[String(it.id)] = 1; });
        items = buildTree(items);

        var total = 0, appointedCount = 0;
        items.forEach(function (it) {
            if (it.selectable === false) return;
            total++;
            if (appointed.hasOwnProperty(it.id)) appointedCount++;
        });

        var h  = '<div class="panel panel-default lsp-panel" id="lsp-panel-' + tid + '" data-tis-id="' + tid + '" data-tisno="' + escAttr(data.tisno) + '">';
        h += '<div class="panel-heading"><h5 class="panel-title" style="margin:0; line-height:30px;">';
        h += '<a data-toggle="collapse" href="#lsp-collapse-' + tid + '" style="display:inline;"><i class="fa fa-chevron-up m-r-5"></i>มอก. ' + esc(data.tisno) + ' — ' + esc(data.tisname) + '</a>';
        h += '<span class="badge lsp-badge">ได้รับแล้ว ' + appointedCount + '/' + total + '</span></h5></div>';
        h += '<div id="lsp-collapse-' + tid + '" class="panel-collapse collapse in"><div class="panel-body" style="padding:10px;">';
        h += '<div class="table-responsive"><table class="table table-bordered lsp-table" style="margin-bottom:0;"><thead><tr>'
           + '<th width="6%">ข้อ</th><th width="17%">รายการทดสอบ</th><th width="11%">รายการทดสอบที่ได้รับการแต่งตั้งแล้ว</th><th width="6%">ขอรับการแต่งตั้ง</th>'
           + '<th width="12%">เครื่องมือ <span class="text-danger">*</span></th><th width="12%">ราคาค่าทดสอบ/ต่อชุดตัวอย่าง <span class="text-danger">*</span></th>'
           + '<th width="17%">วิธีทดสอบของ LAB <span class="text-danger">*</span></th><th width="19%">หมายเหตุ</th></tr></thead><tbody>';

        items.forEach(function (item) {
            var key0 = keyOf(tid, item.id);
            // หัวข้อแม่ที่ไม่ใช่รายการกรอกผล: แสดงเป็นแถวหัวข้อ ติ๊กเพื่อเลือกข้อลูกทั้งหมด (ตัวมันเองไม่ถูกยื่น)
            if (item.selectable === false) {
                store[key0] = { tid: tid, tisno: data.tisno, item: item, appointed: false, oldTools: [], avail: [], tools: [], checked: false, auto: false, childKeys: [], price: '', method: '', methodOther: '', remark: '', isExemptItem: false, heading: true };
                h += '<tr class="lsp-item-row lsp-heading-row" data-key="' + escAttr(key0) + '" data-appointed="0">'
                   + '<td style="padding-left:' + (8 + (item._depth || 0) * 20) + 'px; font-weight:bold; white-space:nowrap; background:#f7f7f7;">' + esc(item.no || '') + '</td>'
                   + '<td style="padding-left:' + (8 + (item._depth || 0) * 20) + 'px; font-weight:bold; color:#555; background:#f7f7f7;">' + esc(item.title) + '</td>'
                   + '<td style="background:#f7f7f7;"></td>'
                   + '<td class="text-center" style="background:#f7f7f7;"><input type="checkbox" class="lsp-chk-req" title="เลือกข้อย่อยทั้งหมดในหัวข้อนี้" style="width:18px;height:18px;cursor:pointer;"></td>'
                   + '<td colspan="4" style="background:#f7f7f7;"></td></tr>';
                return;
            }
            var isAppointed = appointed.hasOwnProperty(item.id);
            var oldTools = appointed[item.id] || [];
            var oldIds = {};
            oldTools.forEach(function (t) { oldIds[String(t.tool_id)] = 1; });
            var isExemptItem = String(item.test_method_id) === '18';
            var indent = (item._depth || 0) * 20;
            var key = keyOf(tid, item.id);
            var itemPending = pendingKeys.some(function (k) { return k.indexOf(tid + '|' + item.id + '|') === 0; });

            var avail = [];
            (item.tools || []).forEach(function (tool) {
                if (oldIds[String(tool.id)]) return;
                if (pendingKeys.indexOf(tid + '|' + item.id + '|' + tool.id) !== -1) return;
                avail.push(tool);
            });
            store[key] = { tid: tid, tisno: data.tisno, item: item, appointed: isAppointed, oldTools: oldTools, avail: avail, tools: [], checked: false, auto: false, childKeys: [], price: '', method: '', methodOther: '', remark: '', isExemptItem: isExemptItem };

            h += '<tr class="lsp-item-row" data-key="' + escAttr(key) + '" data-appointed="' + (isAppointed ? 1 : 0) + '">';
            // บางรายการย่อยใน DB ไม่มีเลขข้อของตัวเอง (no ว่าง) → แสดงเลขข้อของหัวข้อแม่แทนแบบจางๆ และบอกชื่อหัวข้อแม่ใต้ชื่อรายการ
            var noHtml = item.no ? esc(item.no) : (item.parent_no ? '<span class="text-muted" style="font-weight:normal;" title="ภายใต้ข้อ ' + escAttr(item.parent_no) + '">(ข้อ ' + esc(item.parent_no) + ')</span>' : '');
            var underHtml = (!item.no && item.parent_title && !listIds[String(item.parent_id)]) ? '<div class="small text-muted">ภายใต้ ' + (item.parent_no ? 'ข้อ ' + esc(item.parent_no) + ' ' : '') + esc(item.parent_title) + '</div>' : '';
            h += '<td style="padding-left:' + (8 + indent) + 'px; font-weight:bold; white-space:nowrap;">' + noHtml + '</td>';
            h += '<td style="padding-left:' + (8 + indent) + 'px;">' + esc(item.title) + underHtml + '</td>';

            if (isAppointed) {
                h += '<td class="text-center">' + (isExemptItem
                    ? '<span class="lsp-st-ok">ได้รับการแต่งตั้งแล้ว</span>'
                    : '<a class="lsp-st-ok lsp-btn-open" href="javascript:void(0);" title="ดูเครื่องมือที่ได้รับแล้ว">ได้รับการแต่งตั้งแล้ว' + (oldTools.length ? ' (' + oldTools.length + ' เครื่องมือ)' : '') + '</a>') + '</td>';
            } else {
                h += '<td class="text-center"><span class="lsp-st-no">ยังไม่ได้รับการแต่งตั้ง</span></td>';
            }

            // คอลัมน์ "ขอรับการแต่งตั้ง": checkbox อย่างเดียว — ติ๊กแล้วคอลัมน์ถัดไป (ปุ่มเครื่องมือ/ราคา/วิธีทดสอบ/หมายเหตุ) ถึงจะแสดง
            var lockChk = (isExemptItem && isAppointed);
            h += '<td class="text-center"><input type="checkbox" class="lsp-chk-req" ' + (lockChk ? 'disabled title="ได้รับการแต่งตั้งแล้ว"' : '') + ' style="width:18px;height:18px;cursor:pointer;"></td>';

            h += '<td class="text-center"><div class="lsp-cellbox" style="display:none;">';
            if (isExemptItem) {
                h += '<span class="text-muted small">ตรวจพินิจ — ไม่มีเครื่องมือ</span>';
            } else {
                h += '<button type="button" class="btn btn-info btn-xs lsp-btn-open"><i class="fa fa-plus"></i> เพิ่มเครื่องมือ</button>';
                h += '<div class="lsp-req"></div>';
            }
            if (itemPending) h += '<div class="small text-warning">มีในรายการขอเพิ่มด้านล่างแล้ว</div>';
            h += '</div></td>';

            h += '<td><div class="lsp-cellbox" style="display:none;"><input type="text" class="form-control input-sm lsp-in" data-f="price" placeholder="ระบุราคา"></div></td>';
            h += '<td><div class="lsp-cellbox" style="display:none;"><select class="form-control input-sm lsp-in not_select2" data-f="method">'
               + '<option value="">- เลือก -</option><option value="1">ตาม มอก.</option><option value="2">วิธีเทียบเท่า</option><option value="3">อื่นๆ โปรดระบุ</option></select>'
               + '<textarea rows="1" class="form-control input-sm lsp-in lsp-auto lsp-other" data-f="methodOther" placeholder="ระบุรายละเอียด" style="display:none; margin-top:4px; resize:none; overflow:hidden;"></textarea></div></td>';
            h += '<td><div class="lsp-cellbox" style="display:none;"><textarea rows="1" class="form-control input-sm lsp-in lsp-auto" data-f="remark" placeholder="หมายเหตุ" style="resize:none; overflow:hidden;"></textarea></div><span class="lsp-hidden"></span></td>';
            h += '</tr>';
        });

        // ผูกข้อแม่ → ข้อลูก ตาม parent_id (เฉพาะแม่ที่อยู่ในรายการของ มอก. นี้)
        items.forEach(function (it) {
            if (it.parent_id && store[keyOf(tid, it.parent_id)]) { store[keyOf(tid, it.parent_id)].childKeys.push(keyOf(tid, it.id)); }
        });

        h += '</tbody></table></div></div></div></div>';
        $('#lsp-holder-' + tid).replaceWith(h);
        updateBadge(tid);
        applyPrefill(tid);
    }

    // เติมค่าที่เคยยื่นไว้ (แถว type=2 ของคำขอเดิม) กลับเข้า panel: ติ๊กขอรับการแต่งตั้ง + เครื่องมือ/ราคา/วิธีทดสอบ/หมายเหตุ
    // ทำครั้งเดียวต่อการโหลดหน้า (ถ้าผู้ใช้เปลี่ยน มอก. แล้วกลับมา จะไม่เติมทับสิ่งที่กำลังแก้อยู่)
    var prefillDone = {};
    function applyPrefill(tid) {
        if (prefillDone[tid]) return;
        prefillDone[tid] = true;
        var byItem = {};
        pendingRows.forEach(function (r) {
            if (String(r.tis_id) !== String(tid)) return;
            (byItem[r.test_item_id] = byItem[r.test_item_id] || []).push(r);
        });
        Object.keys(byItem).forEach(function (itemId) {
            var key = keyOf(tid, itemId), s = store[key];
            if (!s || s.heading) return;
            var rows = byItem[itemId], first = rows[0];
            s.checked = true;
            s.auto = false;
            s.price = $.trim(first.price_set || '');
            s.method = first.method_type ? String(first.method_type) : '';
            s.methodOther = $.trim(first.method_other || '');
            s.remark = $.trim(first.lab_remark || '');
            var seenTool = {};
            s.tools = s.isExemptItem ? [] : rows.filter(function (r) {
                // เครื่องมือละ 1 แถวต่อรายการทดสอบ (กันข้อมูลซ้ำ)
                if (!r.tool_id || seenTool[String(r.tool_id)]) return false;
                seenTool[String(r.tool_id)] = 1;
                return true;
            }).map(function (r) {
                return { id: String(r.tool_id), title: r.tool_title || '', isCustom: false,
                         vals: { no: r.no || '', capacity: r.capacity || '', range: r.range || '', true_value: r.true_value || '',
                                 fault_value: r.fault_value || '', test_duration: r.test_duration || '', test_price: r.test_price || '' } };
            });
            var $row = $box.find('.lsp-item-row[data-key="' + key + '"]');
            $row.find('[data-f="price"]').val(s.price);
            $row.find('[data-f="method"]').val(s.method);
            $row.find('[data-f="methodOther"]').val(s.methodOther).toggle(s.method === '2' || s.method === '3');
            $row.find('[data-f="remark"]').val(s.remark);
            applyStore(key);
            $row.find('.lsp-auto:visible').each(function () { this.style.height = 'auto'; this.style.height = (this.scrollHeight + 2) + 'px'; });
        });
    }

    function bind() {
        // เปิด modal เครื่องมือ (ปุ่ม "เพิ่มเครื่องมือ" หรือลิงก์เขียว "ได้รับการแต่งตั้งแล้ว" เพื่อดูเครื่องมือเดิม)
        $box.on('click', '.lsp-btn-open', function () {
            openModal($(this).closest('.lsp-item-row').attr('data-key'));
        });

        // ข้อลูก/หลานทั้งหมดของข้อแม่ (recursive ตาม parent_id)
        function descendants(key, seen) {
            seen = seen || {};
            var out = [];
            (store[key].childKeys || []).forEach(function (ck) {
                if (seen[ck]) return;
                seen[ck] = 1;
                out.push(ck);
                out = out.concat(descendants(ck, seen));
            });
            return out;
        }
        function hasData(st) { return !st.heading && (st.tools.length || $.trim(st.price) || st.method || $.trim(st.remark)); }
        function resetItem(key) {
            var st = store[key];
            var $r = $box.find('.lsp-item-row[data-key="' + key + '"]');
            st.checked = false; st.auto = false; st.tools = []; st.price = ''; st.method = ''; st.methodOther = ''; st.remark = '';
            $r.find('.lsp-in').val('').removeClass('lsp-err');
            $r.find('.lsp-other').hide();
            applyStore(key);
        }

        // ติ๊กข้อแม่ → ติ๊กข้อลูก/หลานให้ทั้งหมด (แต่ละข้อกรอกเครื่องมือ/ราคา/วิธีทดสอบของตัวเอง)
        // ติ๊กข้อลูกเดี่ยวๆ ได้ ไม่กระทบข้อแม่ / ติ๊กแม่ออก → ข้อลูกที่ถูกติ๊กอัตโนมัติหลุดตามทั้งหมด (ลูกที่ติ๊กเองยังอยู่)
        $box.on('change', '.lsp-chk-req', function () {
            var key = $(this).closest('.lsp-item-row').attr('data-key');
            var s = store[key];
            if (this.checked) {
                s.checked = true;
                s.auto = false;
                applyStore(key);
                descendants(key).forEach(function (ck) {
                    var c = store[ck];
                    // ติ๊กตามทุกข้อ รวมข้อที่ได้รับแล้ว (ต้องกรอกเครื่องมือ/ราคา/วิธีทดสอบเพิ่มถึงบันทึกได้) ยกเว้นตรวจพินิจที่ได้รับแล้ว (ล็อกอยู่) และข้อที่ติ๊กอยู่แล้ว
                    if ((c.appointed && c.isExemptItem) || c.checked) return;
                    c.checked = true;
                    c.auto = true;
                    applyStore(ck);
                });
            } else {
                var kids = descendants(key).filter(function (ck) { return store[ck].auto && store[ck].checked; });
                var dirty = hasData(s) || kids.some(function (ck) { return hasData(store[ck]); });
                if (dirty && !confirm('ยกเลิกการขอรับการแต่งตั้งรายการนี้' + (kids.length ? ' (รวมข้อย่อยที่ถูกติ๊กอัตโนมัติ ' + kids.length + ' ข้อ)' : '') + ' จะล้างเครื่องมือและข้อมูลที่กรอกไว้ ต้องการดำเนินการต่อหรือไม่?')) {
                    $(this).prop('checked', true);
                    return;
                }
                kids.forEach(resetItem);
                resetItem(key);
            }
        });

        function autosize(el) { el.style.height = 'auto'; el.style.height = (el.scrollHeight + 2) + 'px'; }
        $box.on('input change', '.lsp-in', function () {
            var $row = $(this).closest('.lsp-item-row');
            var s = store[$row.attr('data-key')];
            var f = $(this).attr('data-f');
            s[f] = $.trim($(this).val());
            if (f === 'method') {
                var need = (s.method === '2' || s.method === '3');
                var $o = $row.find('.lsp-other');
                $o.toggle(need).attr('placeholder', s.method === '2' ? 'ระบุรายละเอียดวิธีเทียบเท่า' : 'ระบุวิธีทดสอบ (อื่นๆ)');
                if (!need) { s.methodOther = ''; $o.val(''); }
                if (need) autosize($o[0]);
            }
            if ($(this).hasClass('lsp-auto')) autosize(this);
            $(this).removeClass('lsp-err');
            applyStore($row.attr('data-key'));
        });

        $(document).on('click', '#lspm_custom_add', function () {
            var name = $.trim($('#lspm_custom_name').val());
            if (!name) { alert('กรุณาระบุชื่อเครื่องมือ'); return; }
            var dup = false;
            $('#lspm_tools .lspm-tname').each(function () { if ($.trim($(this).text()).toLowerCase() === name.toLowerCase()) dup = true; });
            if (dup) { alert('มีเครื่องมือนี้ในรายการแล้ว'); return; }
            $('#lspm_tools').append(modalToolRow({ id: 'custom_' + Date.now(), title: name, isCustom: true }, null, true));
            $('#lspm_custom_name').val('');
        });

        $(document).on('click', '#lspm_tools .lspm-del', function () {
            if (!confirm('ยืนยันลบเครื่องมือนี้ออกจากรายการ?')) return;
            $(this).closest('tr').remove();
        });

        $(document).on('change', '#lspm_tools .lspm-chk', function () {
            var $tr = $(this).closest('tr');
            var on = this.checked;
            $tr.toggleClass('lspm-row-off', !on);
            $tr.find('.lspm-f').prop('disabled', !on);
            if (!on) { $tr.find('.lspm-f:not([readonly])').val('').removeClass('lspm-err'); }
        });

        $(document).on('input', '#lspm_tools .lspm-err', function () {
            if ($.trim($(this).val()) !== '') $(this).removeClass('lspm-err');
        });

        $(document).on('click', '#lspm_ok', function () {
            var s = store[curKey];
            var r = readModal();
            if (!r.ticked) { alert('กรุณาติ๊กเลือกเครื่องมือที่ต้องการขอเพิ่มอย่างน้อย 1 รายการ (หรือกด "ล้างรายการที่เพิ่ม")'); return; }
            if (r.bad) { alert('กรุณากรอกข้อมูลให้ครบทุกช่องที่มีกรอบสีแดง (ยกเว้นรหัส/หมายเลข)'); r.bad.focus(); return; }
            s.tools = r.tools;
            s.checked = true;
            applyStore(curKey);
            $('#lsp_modal').modal('hide');
        });

        $(document).on('click', '#lspm_clear', function () {
            var s = store[curKey];
            if (!confirm('ยืนยันล้างเครื่องมือที่เพิ่มของรายการนี้?')) return;
            s.tools = [];
            applyStore(curKey);
            $('#lsp_modal').modal('hide');
        });
    }

    var hintHtml = '<p class="text-muted" style="padding:10px 0;"><i class="fa fa-info-circle"></i> กรุณาเลือก มอก. ที่ต้องการยื่นเพิ่มเติมจากช่องด้านบน (เลือกได้ 1 มอก. ต่อ 1 คำขอ)</p>';
    var prevTid = '';

    function hasSelections() {
        return Object.keys(store).some(function (k) { return store[k].checked && !store[k].heading; });
    }
    // แถวขอเพิ่มในตารางด้านล่าง (ฉบับร่าง/เพิ่ม มอก. ใหม่จากโมดัล) ที่เป็นคนละ มอก. กับที่เลือก
    function tableHasOtherTis(tid) {
        return $('#table-scope input[name="add_scope_tis_id[]"]').filter(function () { return String($(this).val()) !== String(tid); }).length > 0;
    }
    // ให้ modal "เพิ่มขอบข่าย" (มอก. ใหม่) เช็คก่อนว่าคำขอนี้มี มอก. อื่นอยู่แล้วหรือไม่
    window.lspSelectedTis = function () { return prevTid; };

    // เช็คก่อนกดบันทึก: รายการที่ติ๊ก "ขอรับการแต่งตั้ง" ต้องมีเครื่องมือ (ยกเว้นตรวจพินิจ) + ราคา + วิธีทดสอบ (อื่นๆ ต้องระบุ) — คืนข้อความ error หรือ null
    window.lspValidate = function () {
        var msg = null, $first = null;
        Object.keys(store).forEach(function (k) {
            var s = store[k];
            if (!s.checked || s.heading) return;
            var $row = $box.find('.lsp-item-row[data-key="' + k + '"]');
            var name = (s.item.no ? 'ข้อ ' + s.item.no + ' ' : '') + s.item.title;
            function bad(text, $el) {
                if (!msg) msg = 'รายการ "' + name + '" ' + text;
                if ($el && $el.length) { $el.addClass('lsp-err'); if (!$first) $first = $el; }
            }
            if (!s.isExemptItem && !s.tools.length) bad('กรุณากดปุ่ม "เพิ่มเครื่องมือ" และเลือกเครื่องมืออย่างน้อย 1 รายการ', $row.find('button.lsp-btn-open'));
            if (!$.trim(s.price)) bad('กรุณากรอก ราคาค่าทดสอบ/ต่อชุดตัวอย่าง', $row.find('[data-f="price"]'));
            if (!s.method) bad('กรุณาเลือก วิธีทดสอบของ LAB', $row.find('[data-f="method"]'));
            else if ((s.method === '2' || s.method === '3') && !$.trim(s.methodOther)) bad(s.method === '2' ? 'กรุณาระบุรายละเอียดวิธีเทียบเท่า' : 'กรุณาระบุ วิธีทดสอบของ LAB (อื่นๆ)', $row.find('[data-f="methodOther"]'));
        });
        if ($first) {
            $box.find('.panel-collapse').addClass('in').css('height', '');
            if ($first[0].scrollIntoView) $first[0].scrollIntoView({ block: 'center' });
        }
        return msg;
    };

    var newTisData = {};   // มอก. ใหม่ (โหมด "เพิ่ม มอก. ใหม่") — สร้างจาก data-* ของ option ไม่มีขอบข่ายเดิม
    var modeNow = '';
    function dataFor(tid) { return panelsData[tid] || newTisData[tid]; }

    function loadPanel(tid) {
        var data = dataFor(tid);
        $box.html('<div id="lsp-holder-' + tid + '" class="text-muted" style="padding:10px;"><i class="fa fa-spinner fa-spin"></i> กำลังโหลดรายการทดสอบของ มอก. ' + esc(data.tisno) + ' ...</div>');
        $.ajax({ url: itemsUrl + '/' + tid + '?with_headings=1', dataType: 'json' }).done(function (items) {
            if (prevTid !== String(tid)) return;
            if (!$.isArray(items)) { items = []; }
            if (!items.length && !(data.appointed_items || []).length) {
                $('#lsp-holder-' + tid).html('ไม่พบรายการทดสอบของ มอก. ' + esc(data.tisno));
                return;
            }
            renderPanel(tid, data, items);
        }).fail(function () {
            $('#lsp-holder-' + tid).html('<span class="text-danger">โหลดรายการทดสอบของ มอก. ' + esc(data.tisno) + ' ไม่สำเร็จ กรุณาเลือกใหม่อีกครั้ง</span>');
        });
    }

    function tableRows() { return $('#table-scope tbody tr').not('#table-scope-empty-msg'); }

    // แสดงเนื้อหาของ radio ที่เลือก (existing = มอก. เดิม, new = เพิ่ม มอก. ใหม่, minus = ลดขอบข่าย) ที่เหลือซ่อน
    function showMode(m) {
        modeNow = m;
        $('#lsp_block_existing, #lsp_block_new, #lsp_block_minus').hide();
        if (m) $('#lsp_block_' + m).show();
        if (m === 'existing' || m === 'new') { $box.show().html(hintHtml); } else { $box.hide().empty(); }
    }

    $(document).ready(function () {
        $box = $('#box_lab_scope_panels');
        var $selEx  = $('#lsp_tis_select');
        var $selNew = $('#lsp_tis_select_new');

        // โหมด "มอก. เดิม"
        var tids = Object.keys(panelsData).sort(function (a, b) { return String(panelsData[a].tisno).localeCompare(String(panelsData[b].tisno), 'th', { numeric: true }); });
        tids.forEach(function (tid) {
            $selEx.append('<option value="' + tid + '">' + esc(panelsData[tid].tisno) + ' : ' + esc(panelsData[tid].tisname) + '</option>');
        });
        if (!tids.length) {
            // lab ที่ยังไม่มีขอบข่าย: ไม่มี มอก. เดิมให้เลือก และไม่มีอะไรให้ลด
            $('input[name="lsp_mode"][value="existing"], input[name="lsp_mode"][value="minus"]').prop('disabled', true)
                .closest('label').append(' <small class="text-muted">(หน่วยตรวจสอบนี้ยังไม่มีขอบข่าย)</small>');
        }

        // โหมด "เพิ่ม มอก. ใหม่"
        $selNew.find('option[data-tisno]').each(function () {
            newTisData[$(this).val()] = { tisno: String($(this).data('tisno')), tisname: String($(this).data('tisname')), appointed: {}, appointed_items: [] };
        });

        $selEx.select2({ width: '100%', placeholder: '- เลือก มอก. ที่ได้รับการแต่งตั้ง (พิมพ์ค้นหาได้) -', allowClear: true });
        $selNew.select2({ width: '100%', placeholder: '- เลือก มอก. ที่ต้องการเพิ่ม (พิมพ์ค้นหาได้) -', allowClear: true });
        bind();

        function onTisChange($sel) {
            var tid = $sel.val() || '';
            if (tid === prevTid) return;

            if (hasSelections() && !confirm('การเปลี่ยน มอก. จะล้างเครื่องมือ/รายการที่เลือกไว้ใน มอก. เดิมทั้งหมด ต้องการเปลี่ยนใช่หรือไม่?')) {
                $sel.val(prevTid).trigger('change.select2');
                return;
            }
            if (tid && tableHasOtherTis(tid)) {
                alert('คำขอนี้ยื่นเพิ่มขอบข่ายได้เพียง 1 มอก. ต่อ 1 คำขอ กรุณาลบรายการขอเพิ่ม มอก. อื่นในตารางด้านล่างก่อน');
                $sel.val(prevTid).trigger('change.select2');
                return;
            }

            prevTid = String(tid);
            store = {};
            if (!tid) { $box.html(hintHtml); return; }
            loadPanel(tid);
        }
        $selEx.on('change', function () { onTisChange($selEx); });
        $selNew.on('change', function () { onTisChange($selNew); });

        // เลือกแบบการยื่น (radio) — เปลี่ยนแบบจะล้างทุกอย่างที่เลือก/กรอกไว้ รวมถึงแถวในตารางด้านล่าง กันยื่นปนกัน
        $(document).on('change', 'input[name="lsp_mode"]', function () {
            var m = $(this).val();
            if (m === modeNow) return;
            if ((hasSelections() || tableRows().length > 0) && !confirm('การเปลี่ยนแบบการยื่นจะล้างรายการที่เลือก/กรอกไว้ทั้งหมด (รวมรายการในตารางด้านล่าง) ต้องการเปลี่ยนใช่หรือไม่?')) {
                $('input[name="lsp_mode"]').prop('checked', false).filter('[value="' + modeNow + '"]').prop('checked', true);
                return;
            }
            store = {};
            prevTid = '';
            $selEx.val('').trigger('change.select2');
            $selNew.val('').trigger('change.select2');
            tableRows().remove();
            if (window.reloadScopeTable) { window.reloadScopeTable(); }
            showMode(m);
        });

        // แก้ไขคำขอฉบับร่างเดิม: เลือก radio ตามชนิดของร่างให้อัตโนมัติ
        var initMode = @json($draft_mode);
        if (initMode) {
            $('input[name="lsp_mode"][value="' + initMode + '"]').prop('checked', true);
            showMode(initMode);

            // คำขอเพิ่มที่ร่างไว้: เลือก มอก. ของร่างให้อัตโนมัติ แล้ว panel จะโหลดและเติมค่าเดิม (applyPrefill)
            if ((initMode === 'existing' || initMode === 'new') && pendingRows.length) {
                var draftTid = String(pendingRows[0].tis_id);
                var $draftSel = (initMode === 'new') ? $selNew : $selEx;
                $draftSel.val(draftTid).trigger('change');
            }
        }
    });
})();
</script>
@endpush
