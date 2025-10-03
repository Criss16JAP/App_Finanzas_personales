<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\CreditCard;

class AccountObserver
{
    /**
     * Handle the Account "created" event.
     */
    public function created(Account $account): void
    {
        //
    }

    /**
     * Handle the Account "updated" event.
     */
    public function updated(Account $account): void
    {
        //
    }

    /**
     * Handle the Account "deleted" event.
     */
    public function deleted(Account $account): void
    {
        // Si la cuenta que se está eliminando es de tipo 'credit_card'
        if ($account->type === 'credit_card') {
            // Buscamos y eliminamos la tarjeta de crédito especializada con el mismo nombre y dueño
            CreditCard::where('name', $account->name)
                      ->where('user_id', $account->user_id)
                      ->delete();
        }
    }


    /**
     * Handle the Account "restored" event.
     */
    public function restored(Account $account): void
    {
        //
    }

    /**
     * Handle the Account "force deleted" event.
     */
    public function forceDeleted(Account $account): void
    {
        //
    }
}
