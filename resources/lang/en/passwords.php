<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'password' => 'Passwords must be at least six characters and match the confirmation.',
    'reset' => 'Your password has been reset!',
    'sent' => 'เราได้ส่งลิงค์รีเซ็ตรหัสผ่านไปยังอีเมลของคุณแล้ว!',
    'token' => 'โทเค็นการรีเซ็ตรหัสผ่านนี้ไม่ถูกต้อง คุณอาจเคยใช้ลิงค์นี้แล้ว ลองร้องขออีเมลเปลี่ยนรหัสผ่านอีกครั้ง <a href="'.url('password/reset').'">คลิก</a>',
    'user' => "We can't find a user with that e-mail address.",
    'no_email' => 'ไม่พบการร้องขอเปลี่ยนรหัสผ่านของอีเมลนี้ คุณอาจพิมพ์อีเมลผิดหรือไม่ได้ร้องขอ ลองร้องขออีเมลเปลี่ยนรหัสผ่านอีกครั้ง <a href="'.url('password/reset').'">คลิก</a>'
];
