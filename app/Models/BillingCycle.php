<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_card_id', 'statement_date', 'due_date',
        'total_installments_due', 'interest_charged', 'fees_charged',
        'closing_balance', 'amount_paid', 'status',
    ];

    protected $casts = [
        'statement_date' => 'date',
        'due_date' => 'date',
    ];

    public function creditCard(): BelongsTo
    {
        return $this->belongsTo(CreditCard::class);
    }
}
