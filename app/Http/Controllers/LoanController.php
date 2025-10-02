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
            'amount' => 'required|numeric|min:0.01',
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
    // Seguridad: Verifica que el préstamo pertenece al usuario logueado
    if (Auth::id() !== $loan->user_id) {
        abort(403);
    }

    $remainingBalance = $loan->total_amount - $loan->paid_amount;

    $validatedData = $request->validate([
        // La cuenta a la que entrará el dinero
        'account_id' => 'required|exists:accounts,id',
        // El monto no puede ser mayor que el saldo pendiente
        'amount' => "required|numeric|min:0.01|lte:{$remainingBalance}",
        'category_id' => 'required|exists:categories,id',
    ]);

    try {
        $this->loanService->addRepayment($loan, $validatedData);
        return back()->with('success', '¡Abono registrado exitosamente!');
    } catch (\Exception $e) {
        return back()->with('error', 'Error al registrar el abono: ' . $e->getMessage());
    }
}
}
