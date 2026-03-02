<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    protected $fillable = ['triggered_by', 'type', 'message', 'is_read'];

    protected $casts = [
        'is_read'    => 'boolean',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Icon name based on notification type.
     */
    public function getIconTypeAttribute(): string
    {
        return match($this->type) {
            'new_signup'           => 'user-plus',
            'account_approved'     => 'check-circle',
            'account_rejected'     => 'x-circle',
            'account_deactivated'  => 'ban',
            'account_activated'    => 'check',
            default                => 'bell',
        };
    }
}