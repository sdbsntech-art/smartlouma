<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name', 'category', 'quantity', 'price', 'zone',
        'harvest_date', 'description', 'image', 'rating',
        'available', 'sold_qty', 'producer_id',
    ];

    protected $casts = [
        'available'    => 'boolean',
        'harvest_date' => 'date',
        'rating'       => 'float',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function producer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'producer_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Scopes ─────────────────────────────────────────────────
    public function scopeAvailable($q)
    {
        return $q->where('available', true)->where('quantity', '>', 0);
    }

    // ── Photo automatique par nom ───────────────────────────────
    public static function defaultPhotoFor(string $name): string
    {
        $photos = [
            'carottes'        => 'https://images.unsplash.com/photo-1598170845058-78131a90f4bf?auto=format&fit=crop&w=800&q=80',
            'tomates'         => 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=800&q=80',
            'oignons'         => 'https://images.unsplash.com/photo-1587049633312-d628ae50a8ae?auto=format&fit=crop&w=800&q=80',
            'courgettes'      => 'https://images.unsplash.com/photo-1540420828642-fca2c5c18abb?auto=format&fit=crop&w=800&q=80',
            'pommes de terre' => 'https://images.unsplash.com/photo-1518977676601-b53f82aba655?auto=format&fit=crop&w=800&q=80',
            'aubergines'      => 'https://images.unsplash.com/photo-1621956838481-f8bc3bc2e9a5?auto=format&fit=crop&w=800&q=80',
            'choux'           => 'https://images.unsplash.com/photo-1655403454657-25e4f5ea7c3e?auto=format&fit=crop&w=800&q=80',
            'poivrons'        => 'https://images.unsplash.com/photo-1563565375-f3fdfdbefa83?auto=format&fit=crop&w=800&q=80',
            'mangues'         => 'https://images.unsplash.com/photo-1553279768-865429fa0078?auto=format&fit=crop&w=800&q=80',
            'bananes'         => 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?auto=format&fit=crop&w=800&q=80',
            'épinards'        => 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?auto=format&fit=crop&w=800&q=80',
            'ail'             => 'https://images.unsplash.com/photo-1615478503562-ec2d8aa0e24e?auto=format&fit=crop&w=800&q=80',
            'piment'          => 'https://images.unsplash.com/photo-1583119022894-19a68a3d0e3f?auto=format&fit=crop&w=800&q=80',
            'laitue'          => 'https://images.unsplash.com/photo-1621259182978-fbf93132d53d?auto=format&fit=crop&w=800&q=80',
            'concombre'       => 'https://images.unsplash.com/photo-1604977042946-1eecc30f269e?auto=format&fit=crop&w=800&q=80',
            'gombo'           => 'https://images.unsplash.com/photo-1601515167852-c1ef03df0e60?auto=format&fit=crop&w=800&q=80',
            'pastèque'        => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?auto=format&fit=crop&w=800&q=80',
        ];

        $lower = mb_strtolower($name);
        foreach ($photos as $key => $url) {
            if (str_contains($lower, $key) || str_contains($key, $lower)) {
                return $url;
            }
        }

        return 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=800&q=80';
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) return $this->image;
        return self::defaultPhotoFor($this->name);
    }
}
