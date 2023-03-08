<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailResetPasswordRequest extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    public function __construct($data)
    {
      $this->data = $data;  
    }
   
    /**
     * Create a new message instance.
     *
     * @return void

    
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Mail Reset Password Request',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.reset_password_request',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * 
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
