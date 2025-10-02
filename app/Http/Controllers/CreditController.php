<?php

namespace App\Http\Controllers;

use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Credit;

class CreditController extends Controller
{
    public function __construct(private CreditService $creditService)
    {
    }

    public function index()
    {
        $data = $this->creditService->getDataForCreditView(Auth::user());
        return view('credits.index', $data);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'principal_amount' => 'required|numeric|min:0.01',
            'interest_rate' => 'required|numeric|min:0',
            'term_months' => 'required|integer|min:1',
            'fixed_monthly_fee' => 'nullable|numeric|min:0', // <-- AÑADIR
            'issued_date' => 'required|date',
            'payment_day_of_month' => 'required|integer|min:1|max:31',
            'account_id_deposit' => 'required|exists:accounts,id',
        ]);

        try {
            $this->creditService->createCredit($validatedData, Auth::user());
            return back()->with('success', '¡Crédito registrado exitosamente!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al registrar el crédito: ' . $e->getMessage());
        }
    }

    public function pay(Request $request, Credit $credit)
    {
        /** @var \App\Models\User $user */ // <-- CORRECCIÓN AÑADIDA AQUÍ
        $user = Auth::user();

        // Seguridad
        if ($user->id !== $credit->user_id) {
            abort(403);
        }

        $validatedData = $request->validate([
            'amount' => "required|numeric|min:0.01|lte:{$credit->current_balance}",
            'account_id' => 'required|exists:accounts,id',
        ]);

        // Asumimos que existe una categoría para los pagos de créditos
        $paymentCategory = $user->categories()->where('name', 'Pago de Créditos')->first();
        if (!$paymentCategory) {
            return back()->with('error', 'Por favor, crea una categoría de gasto llamada "Pago de Créditos".');
        }
        $validatedData['category_id'] = $paymentCategory->id;

        try {
            $this->creditService->addPayment($credit, $validatedData);
            return back()->with('success', '¡Pago registrado exitosamente!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

}
