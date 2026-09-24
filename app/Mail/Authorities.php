<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Authorities extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->name = $item['name'];
        $this->link = $item['link']; 
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from( config('mail.from.address'),config('mail.from.name')) // $this->email 
                    ->view('mail/authorities')
                    ->subject('แจ้งข้อมูลการลงทะเบียนระบบบริการอิเล็กทรอนิกส์ สมอ.')
                    ->with([
                        'name' => $this->name,
                        'link' => $this->link
                    ]);
    }
}
