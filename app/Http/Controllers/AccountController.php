<?php

namespace App\Http\Controllers;

use App\Models\Account; // <-- Importante añadir este 'use'
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function __construct(private AccountService $accountService)
    {
    }

    public function index()
    {
        $user = Auth::user();
        $accounts = $this->accountService->getAccountsForUser($user);

        return view('accounts.index', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:bank,cash,credit_card,loan',
            'balance' => 'required|numeric|min:0',
        ]);

        $this->accountService->createAccount(Auth::user(), $validatedData);

        return back()->with('success', '¡Cuenta creada exitosamente!');
    }

    // --- NUEVOS MÉTODOS AÑADIDOS AQUÍ ---

    /**
     * Muestra el formulario para editar una cuenta.
     */
    public function edit(Account $account)
    {
        // SEGURIDAD: Verifica que el usuario autenticado es el dueño de la cuenta.
        if (Auth::id() !== $account->user_id) {
            abort(403, 'Acción no autorizada.');
        }

        return view('accounts.edit', compact('account'));
    }

    /**
     * Actualiza una cuenta específica en la base de datos.
     */
    public function update(Request $request, Account $account)
    {
        // SEGURIDAD: Verifica la propiedad de la cuenta.
        if (Auth::id() !== $account->user_id) {
            abort(403, 'Acción no autorizada.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            // El tipo y el saldo no se suelen editar, pero podrías añadirlo si quieres.
        ]);

        $this->accountService->updateAccount($account, $validatedData);

        return redirect()->route('accounts.index')->with('success', '¡Cuenta actualizada exitosamente!');
    }

    /**
     * Elimina una cuenta de la base de datos.
     */
    public function destroy(Account $account)
    {
        // SEGURIDAD: Verifica la propiedad de la cuenta.
        if (Auth::id() !== $account->user_id) {
            abort(403, 'Acción no autorizada.');
        }

        $this->accountService->deleteAccount($account);

        return back()->with('success', '¡Cuenta eliminada exitosamente!');
    }
}
