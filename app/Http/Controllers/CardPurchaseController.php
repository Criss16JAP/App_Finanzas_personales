<?php

namespace App\Http\Controllers;

use App\Models\CreditCard;
use App\Services\CreditCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardPurchaseController extends Controller
{
    public function __construct(private CreditCardService $creditCardService)
    {
    }

    public function store(Request $request, CreditCard $credit_card)
    {
        if (Auth::id() !== $credit_card->user_id) {
            abort(403);
        }

        $validatedData = $request->validate([
            'description' => 'required|string|max:255',
            'purchase_amount' => 'required|numeric|min:0.01',
            'installments' => 'required|integer|min:1',
            'purchase_date' => 'required|date',
            'category_id' => 'required|exists:categories,id,type,egress', // Asegura que sea una categoría de gasto
        ]);

        try {
            $this->creditCardService->addPurchase($credit_card, $validatedData, Auth::user());
            return back()->with('success', '¡Compra registrada exitosamente!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
