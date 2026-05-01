<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Samity extends Model
{
    protected $fillable = [
        'name', 'description', 'cycle_type',
        'deposit_amount', 'start_date', 'meeting_day', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'deposit_amount' => 'decimal:2',
            'start_date'     => 'date',
            'is_active'      => 'boolean',
        ];
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'samity_members')
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
}
