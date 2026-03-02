<?php
namespace App\Mail;
use App\Models\Order;
use Illuminate\Bus\Queueable; use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Content, Envelope}; use Illuminate\Queue\SerializesModels;
class NewOrderAdminMail extends Mailable {
    use Queueable, SerializesModels;
    public function __construct(public Order $order) {}
    public function envelope(): Envelope { return new Envelope(subject: '🛍️ Nouvelle commande — ' . $this->order->reference . ' (' . number_format($this->order->total, 0, ',', ' ') . ' FCFA)'); }
    public function content(): Content   { return new Content(view: 'emails.order-admin'); }
}
