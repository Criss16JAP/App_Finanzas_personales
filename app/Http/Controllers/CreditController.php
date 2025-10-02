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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $this->creditService->getDataForCreditView($user);
        // Buscamos la categoría y la añadimos a los datos que van a la vista
        $data['paymentCategory'] = $user->categories()->where('name', 'Pago de Créditos')->first();

        return view('credits.index', $data);
    }

    public function history()
{
    /** @var \App\Models\User $user */
    $user = Auth::user();

    $credits = $user
        ->credits()
        ->where('status', 'paid') // <-- La clave: solo obtenemos los pagados
        ->orderBy('issued_date', 'desc')
        ->paginate(15);

    return view('credits.history', compact('credits'));
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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->id !== $credit->user_id) {
            abort(403);
        }

        $validatedData = $request->validate([
            'amount' => "required|numeric|min:0.01|lte:{$credit->current_balance}",
            'account_id' => 'required|exists:accounts,id',
        ]);

        $paymentCategory = $user->categories()->where('name', 'Pago de Créditos')->first();
        if (!$paymentCategory) {
            return back()->with('error', 'Por favor, crea una categoría de gasto llamada "Pago de Créditos".');
        }
        $validatedData['category_id'] = $paymentCategory->id;

        try {
            $this->creditService->addPayment($credit, $validatedData); // Ahora solo llamamos a addPayment
            return back()->with('success', '¡Pago registrado exitosamente!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    // Muestra la página de detalle para un crédito específico.

    public function show(Credit $credit)
    {
        // Seguridad: Verificar que el crédito pertenece al usuario autenticado
        if (Auth::id() !== $credit->user_id) {
            abort(403);
        }

        // Le pedimos al servicio todos los datos calculados para la vista de detalle
        $data = $this->creditService->getDataForCreditDetailView($credit);

        return view('credits.show', $data);
    }

}
