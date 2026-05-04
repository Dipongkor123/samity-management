<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'nid', 'address',
        'photo', 'date_of_birth', 'blood_group', 'occupation',
        'father_name', 'mother_name', 'spouse_name',
        'emergency_contact', 'emergency_phone',
        'role', 'is_active', 'password',
        'designation', 'assigned_area', 'joining_date', 'is_staff',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'is_staff'          => 'boolean',
            'date_of_birth'     => 'date',
            'joining_date'      => 'date',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('uploads/members/' . $this->photo)
            : '';
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /* ─── Relationships ─── */

    public function samities(): BelongsToMany
    {
        return $this->belongsToMany(Samity::class, 'samity_members')
            ->withPivot('joined_at', 'is_active')
            ->withTimestamps();
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(MemberStatusLog::class)->latest();
    }
}
