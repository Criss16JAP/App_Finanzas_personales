<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_id', 'movement_id', 'amount_paid',
        'principal_paid', 'interest_paid', 'payment_date',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function credit(): BelongsTo
    {
        return $this->belongsTo(Credit::class);
    }

    public function movement(): BelongsTo
    {
        return $this->belongsTo(Movement::class);
    }
}
