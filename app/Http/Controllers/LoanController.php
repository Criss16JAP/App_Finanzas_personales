<?php

namespace App\Http\Controllers;

use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Loan;

class LoanController extends Controller
{
    public function __construct(private LoanService $loanService)
    {
    }

    public function index()
    {
        /** @var \App\Models\User $user */ // <-- AÑADE ESTA LÍNEA
        $user = Auth::user();

        // Le pedimos al servicio los datos que la vista necesita
        $data = $this->loanService->getDataForLoanView($user);
        $data['loanCategory'] = $user->categories()->where('name', 'Préstamos')->first();

        return view('loans.index', $data);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'borrower_name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0.01',
            'interest_rate' => 'required|numeric|min:0',
            'term_months' => 'required|integer|min:1',
            'payment_day_of_month' => 'required|integer|min:1|max:31',
            'account_id' => 'required|exists:accounts,id',
            'loan_date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
        ]);

        try {
            $this->loanService->createLoan($validatedData, Auth::user());
            return back()->with('success', '¡Préstamo registrado exitosamente!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al registrar el préstamo: ' . $e->getMessage());
        }
    }

    public function repay(Request $request, Loan $loan)
    {
        /** @var \App\Models\User $user */ // <-- AÑADE ESTA LÍNEA
        $user = Auth::user();

        // Seguridad
        if ($user->id !== $loan->user_id) { // <-- También actualiza esta línea para usar $user
            abort(403);
        }

        $remainingBalance = ($loan->total_amount - $loan->paid_amount) + $loan->accrued_interest_balance;

        $validatedData = $request->validate([
            'amount' => "required|numeric|min:0.01|lte:{$remainingBalance}",
            'account_id' => 'required|exists:accounts,id',
        ]);

        // El pago de un préstamo es un INGRESO en la categoría 'Préstamos'
        $loanCategory = $user->categories()->where('name', 'Préstamos')->where('type', 'income')->first();
        if (!$loanCategory) {
            return back()->with('error', 'Por favor, crea una categoría de INGRESO llamada "Préstamos".');
        }
        $validatedData['category_id'] = $loanCategory->id;

        try {
            $this->loanService->addRepayment($loan, $validatedData);
            return back()->with('success', '¡Abono recibido exitosamente!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al registrar el abono: ' . $e->getMessage());
        }
    }
}
