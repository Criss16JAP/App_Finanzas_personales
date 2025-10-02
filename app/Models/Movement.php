<?php

// app/Models/Movement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movement extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'account_id', 'category_id', 'related_account_id', 'type', 'amount', 'description', 'movement_date'];

    /**
     * Convierte automáticamente el campo de fecha a un objeto Carbon.
     */
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

    /**
     * Relación para la cuenta de destino/origen en una transferencia.
     */
    public function relatedAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'related_account_id');
    }

    public function creditPayment(): HasOne
    {
        // Añade: use Illuminate\Database\Eloquent\Relations\HasOne;
        return $this->hasOne(CreditPayment::class);
    }
}
