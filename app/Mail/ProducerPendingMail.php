<?php
// ── ProducerPendingMail.php ─────────────────────────────────────
namespace App\Mail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Content, Envelope};
use Illuminate\Queue\SerializesModels;

class ProducerPendingMail extends Mailable {
    use Queueable, SerializesModels;
    public function __construct(public User $user) {}
    public function envelope(): Envelope { return new Envelope(subject: '⚠️ Nouveau producteur en attente — ' . $this->user->name); }
    public function content(): Content   { return new Content(view: 'emails.producer-pending'); }
}
