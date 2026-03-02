<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'avatar', 'phone', 'bio',
        'role', 'status', 'approval_status',
        'approved_at', 'approved_by',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'approved_at'       => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getFirstNameAttribute(): string
    {
        return explode(' ', $this->name)[0] ?? '';
    }

    public function getLastNameAttribute(): string
    {
        return explode(' ', $this->name, 2)[1] ?? '';
    }

    /**
     * Returns a URL to the user's avatar.
     * If no avatar is uploaded, generates a clean initials avatar via UI Avatars.
     */
    public function getAvatarUrlAttribute(): string
    {
        // Use uploaded avatar if it exists on disk
        if ($this->avatar && file_exists(storage_path('app/public/' . $this->avatar))) {
            return asset('storage/' . $this->avatar);
        }

        // Generate initials-based avatar — no hardcoded face
        $name        = urlencode($this->name ?: 'User');
        $background  = 'E0E7FF'; // indigo-100
        $color       = '4F46E5'; // indigo-600
        $size        = 128;

        return "https://ui-avatars.com/api/?name={$name}&size={$size}&background={$background}&color={$color}&bold=true&format=svg";
    }

    // ── Role / status helpers ─────────────────────────────────────────────────

    public function isSuperAdmin(): bool { return $this->role === 'superadmin'; }
    public function isActive(): bool     { return $this->status === 'active'; }
    public function isApproved(): bool   { return $this->approval_status === 'approved'; }
    public function isPending(): bool    { return $this->approval_status === 'pending'; }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function adminNotifications(): HasMany
    {
        return $this->hasMany(AdminNotification::class, 'triggered_by');
    }
}