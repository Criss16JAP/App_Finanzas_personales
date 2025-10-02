<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_card_id', 'description', 'purchase_amount',
        'installments', 'installments_paid', 'remaining_balance', 'purchase_date',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function creditCard(): BelongsTo
    {
        return $this->belongsTo(CreditCard::class);
    }
}
