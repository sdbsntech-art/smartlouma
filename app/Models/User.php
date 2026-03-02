<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone',
        'role', 'status', 'company', 'zone', 'approved_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'approved_at'       => 'datetime',
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'producer_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    // ── Scopes ─────────────────────────────────────────────────
    public function scopeProducers($q)   { return $q->where('role', 'producer'); }
    public function scopeConsumers($q)   { return $q->where('role', 'consumer'); }
    public function scopeActive($q)      { return $q->where('status', 'active'); }
    public function scopePending($q)     { return $q->where('status', 'pending'); }

    // ── Helpers ─────────────────────────────────────────────────
    public function isAdmin():    bool { return $this->role === 'admin'; }
    public function isProducer(): bool { return $this->role === 'producer'; }
    public function isConsumer(): bool { return $this->role === 'consumer'; }
    public function isActive():   bool { return $this->status === 'active'; }
    public function isPending():  bool { return $this->status === 'pending'; }

    public function canManageProducts(): bool
    {
        return $this->isAdmin() || ($this->isProducer() && $this->isActive());
    }

    public function roleLabel(): string
    {
        return match($this->role) {
            'admin'    => 'Administrateur',
            'producer' => 'Producteur',
            'consumer' => 'Restaurateur',
            default    => 'Utilisateur',
        };
    }
}
