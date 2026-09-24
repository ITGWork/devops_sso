<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotUserMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($item)
    {
        $this->name  = $item['name'];
        $this->users = $item['users'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->view('mail/forgot-user')
                    ->subject('แจ้งข้อมูลชื่อผู้ใช้งานระบบบริการอิเล็กทรอนิกส์ สมอ.')
                    ->with([
                        'name'  => $this->name,
                        'users' => $this->users
                    ]);
    }
}
