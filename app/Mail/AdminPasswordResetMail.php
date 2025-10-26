<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;


    public function __construct($otp)
    {
        $this->otp = $otp;
    }

 
    public function build()
    {
        return $this->subject('รหัส OTP สำหรับรีเซ็ตรหัสผ่าน (Admin)')
                    ->view('emails.admin-password-reset', ['otp' => $this->otp]);
    }
}