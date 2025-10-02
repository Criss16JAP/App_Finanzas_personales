<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Credit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'principal_amount',
        'interest_rate',
        'fixed_monthly_fee',
        'term_months',
        'current_balance',
        'accrued_interest_balance',
        'accrued_fee_balance', // <-- AÑADIR
        'issued_date',
        'payment_day_of_month',
        'last_interest_accrued_on',
        'status',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'last_interest_accrued_on' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CreditPayment::class);
    }
}
