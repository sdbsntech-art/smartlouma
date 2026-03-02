<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message', 'status', 'ip_address',
    ];

    public function scopeUnread($q) { return $q->where('status', 'unread'); }
    public function scopeRead($q)   { return $q->where('status', 'read'); }

    public function markAsRead(): void
    {
        $this->update(['status' => 'read']);
    }
}
