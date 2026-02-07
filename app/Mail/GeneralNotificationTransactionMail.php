<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GeneralNotificationTransactionMail extends Mailable
{
    use Queueable, SerializesModels;

   public $message;
   public $title;
    public $tenant;
    public $company;
    public $table;

    public function __construct($title,$tenant,$company,$table,$message = null)
    {
        $this->title   = $title;
        $this->tenant  = $tenant;
        $this->company = $company;
        $this->table   = $table;
        $this->message = $message;
    }

    public function build()
    {
        return $this->subject($this->title)
            ->view('emails.general_transaction_notification');
    }
}
