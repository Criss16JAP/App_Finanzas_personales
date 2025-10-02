<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'credit_limit', 'interest_rate',
        'monthly_fee', 'cutoff_day', 'payment_day', 'current_debt',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(CardPurchase::class);
    }

    public function billingCycles(): HasMany
    {
        return $this->hasMany(BillingCycle::class);
    }
}
