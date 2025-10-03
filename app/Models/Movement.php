<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne; // <-- LA LÍNEA QUE FALTA

class Movement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'related_account_id',
        'type',
        'amount',
        'description',
        'movement_date',
        'installments',
    ];

    protected $casts = [
        'movement_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function relatedAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'related_account_id');
    }

    /**
     * Define la relación con un pago de crédito.
     */
    public function creditPayment(): HasOne
    {
        return $this->hasOne(CreditPayment::class);
    }

    /**
     * Define la relación con un abono de préstamo.
     */
    public function loanPayment(): HasOne
    {
        return $this->hasOne(LoanPayment::class);
    }
}
