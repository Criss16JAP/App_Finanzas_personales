<?php

namespace App\Services;

use App\Models\Account; // <-- Importante añadir este 'use'
use App\Models\User;

class AccountService
{
    /**
     * Obtiene todas las cuentas para un usuario específico.
     */
    public function getAccountsForUser(User $user)
    {
        return $user->accounts()->orderBy('name')->get();
    }

    /**
     * Crea una nueva cuenta para un usuario.
     */
    public function createAccount(User $user, array $data)
    {
        return $user->accounts()->create($data);
    }

    // --- NUEVOS MÉTODOS AÑADIDOS AQUÍ ---

    /**
     * Actualiza los datos de una cuenta.
     *
     * @param Account $account La cuenta a actualizar.
     * @param array $data Los nuevos datos validados.
     * @return bool
     */
    public function updateAccount(Account $account, array $data): bool
    {
        // Eloquent se encarga de la actualización de forma segura.
        return $account->update($data);
    }

    /**
     * Elimina una cuenta.
     *
     * @param Account $account La cuenta a eliminar.
     * @return bool|null
     */
    public function deleteAccount(Account $account): ?bool
    {
        // Eloquent se encarga de eliminar el registro.
        return $account->delete();
    }
}
