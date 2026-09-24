<?php
return [
    // เวลาอนุโลมของ st/entry (นาที)
    'max_skew_minutes' => 10,

    // IP allowlist (ปิดไว้ก่อน)
    'ip_allowlist_enabled' => false,
    'ip_allowlist' => [
        // 'sample.ip',
    ],

    // map JuristicType (i-industry) -> juristic_status (ระบบเรา)
    // สอดคล้องกับ Controller: jt=1 => นิติบุคคล, jt=2 => บุคคลธรรมดา
    'status_map' => [
        '1' => '2', // jt=1 (นิติบุคคล) → juristic_status=2
        '2' => '1', // jt=2 (บุคคลธรรมดา) → juristic_status=1
        '4' => '4', // jt=4 (อื่นๆ) → คงค่าเดิม
    ],

    // iindustry uid -> pid
    'strict_uid_match_for_juristic' => false, // ถ้าบริษัทอนุญาตหลายคน true/false ได้

    // blocklist 
    'blocklist_tax' => array_filter(array_map('trim', explode(',', env('SSO_BLOCKLIST_TAX', '')))),
];
