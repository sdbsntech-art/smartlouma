<?php
namespace App\Mail;
use App\Models\ContactMessage;
use Illuminate\Bus\Queueable; use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Content, Envelope}; use Illuminate\Queue\SerializesModels;

class ContactAdminMail extends Mailable {
    use Queueable, SerializesModels;
    public function __construct(public ContactMessage $contactMessage) {}
    public function envelope(): Envelope { return new Envelope(subject: '📩 Nouveau message — ' . $this->contactMessage->name . ' (' . $this->contactMessage->subject . ')'); }
    public function content(): Content   { return new Content(view: 'emails.contact-admin'); }
}
