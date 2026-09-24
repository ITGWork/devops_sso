@component('mail::message')
@php

    $email =  request()->email;
    $user = App\User::where('email',$email)->first();
@endphp
@if (!is_null($user))

# @lang('[SSO] Reset Password | ขอเปลี่ยนรหัสผ่าน')


<p>เรียน {{ $user->name}} </p>
<p>เนื่องจาก สมอ. ได้รับคำร้องขอเปลี่ยนรหัสผ่านใหม่จากบัญชีผู้ใช้งานของคุณ</p>
<p>คุณสามารถรีเซ็ตรหัสผ่านใหม่ได้ที่นี่</p>

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
            $color = 'green';
            break;
        case 'error':
            $color = 'red';
            break;
        default:
            $color = 'blue';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{-- {{ $actionText }} --}}
@lang('email.'.$actionText)
@endcomponent
@endisset

<p style="color:red">หมายเหตุ : ลิงค์สำหรับรีเซ็ตรหัสผ่านสามารถใช้ได้ครั้งเดียวเท่านั้น</p>
<p>หากคุณไม่ต้องการรีเซ็ตรหัสผ่านแล้ว คุณไม่จำเป็นต้องสนใจอีเมลฉบับนี้</p>
<p>ขอแสดงความนับถือ</p>
<p>สำนักงานมาตรฐานผลิตภัณฑ์อุตสาหกรรม (สมอ.)</p>


{{-- Subcopy --}}
@isset($actionText)
@component('mail::subcopy')
@lang(
    "หากคุณประสบปัญหาในการคลิกปุ่ม \":actionText\", ให้คัดลอกและวาง URL ต่อไปนี้\n".
    'ลงในเว็บเบราเซอร์: [:actionURL](:actionURL)',
    [
        'actionText' => __('email.'.$actionText),
        'actionURL' => $actionUrl
    ]
)
@endcomponent
@endisset
@endif

@endcomponent
