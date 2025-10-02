<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'borrower_name',
        'total_amount',
        'paid_amount',
        'status',
        'loan_date',
    ];

    protected $casts = [
        'loan_date' => 'date',
    ];

    /**
     * Un préstamo pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
