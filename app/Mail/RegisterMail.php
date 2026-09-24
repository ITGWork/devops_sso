<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($item)
    {
        $this->name      = $item['name'];
        $this->username  = array_key_exists('username', $item) ? $item['username'] : null;
        $this->email     = $item['email'];
        $this->link      = $item['link'];
        $this->check_api = $item['check_api'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from( config('mail.from.address'),config('mail.from.name')) // $this->email 'แจ้งข้อมูลการลงทะเบียนระบบบริการอิเล็กทรอนิกส์ สมอ.'
                    ->view('mail/register')
                    ->subject('แจ้งข้อมูลการลงทะเบียนระบบบริการอิเล็กทรอนิกส์ สมอ.')
                    ->with([
                        'name'      => $this->name,
                        'username'  => $this->username,
                        'link'      => $this->link,
                        'check_api' => $this->check_api
                    ]);
    }
}
