<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'borrower_name', 'total_amount', 'paid_amount', 'status', 'loan_date',
        // Nuevos campos
        'interest_rate', 'term_months', 'payment_day_of_month',
        'accrued_interest_balance', 'last_interest_accrued_on'
    ];

    protected $casts = [
        'loan_date' => 'date',
        'last_interest_accrued_on' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Nueva relación
    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class);
    }
}
