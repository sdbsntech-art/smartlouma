<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'reference', 'buyer_id', 'subtotal', 'delivery_fee',
        'total', 'status', 'delivery_address', 'notes',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Génération référence ────────────────────────────────────
    public static function generateReference(): string
    {
        $count = static::count() + 1;
        return 'CMD-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'pending'   => 'En attente',
            'confirmed' => 'Confirmée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            default     => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'pending'   => '#92400E',
            'confirmed' => '#1D4ED8',
            'delivered' => '#2D6A4F',
            'cancelled' => '#DC2626',
            default     => '#6B7280',
        };
    }
}
