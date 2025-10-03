<?php

namespace App\Http\Controllers;

use App\Services\CreditCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CreditCard;

class CreditCardController extends Controller
{
    public function __construct(private CreditCardService $creditCardService)
    {
    }

    public function index()
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    // 1. Obtenemos los datos y los guardamos en una variable llamada '$creditCards'
    $creditCards = $this->creditCardService->getCreditCardsForUser($user);
    // 2. Usamos compact() para pasar una variable con el nombre EXACTO 'creditCards' a la vista.
    return view('credit-cards.index', compact('creditCards'));
}

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'credit_limit' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0',
            'monthly_fee' => 'nullable|numeric|min:0',
            'cutoff_day' => 'required|integer|min:1|max:31',
            'payment_day' => 'required|integer|min:1|max:31',
        ]);

        // Convertir el interés de % a decimal
        $validatedData['interest_rate'] = ($validatedData['interest_rate'] ?? 0) / 100;
        $validatedData['monthly_fee'] = $validatedData['monthly_fee'] ?? 0;

        $this->creditCardService->createCreditCard($validatedData, Auth::user());

        return back()->with('success', '¡Tarjeta de crédito añadida exitosamente!');
    }

    public function show(CreditCard $credit_card)
    {
        // Seguridad: verificar que la tarjeta pertenece al usuario
        if (Auth::id() !== $credit_card->user_id) {
            abort(403);
        }

        // Cargamos las compras de la tarjeta, paginadas
        $purchases = $credit_card->purchases()->latest('purchase_date')->paginate(15);

        return view('credit-cards.show', [
            'card' => $credit_card,
            'purchases' => $purchases,
        ]);
    }
}
