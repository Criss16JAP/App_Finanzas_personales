<?php

namespace App\Http\Controllers;

use App\Services\MovementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MovementController extends Controller
{
    public function __construct(private MovementService $movementService)
    {
    }

    public function index()
    {
        $user = Auth::user();
        // Pasamos a la vista los datos necesarios para los dropdowns y la lista
        $data = $this->movementService->getDataForMovementView($user);

        return view('movements.index', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', Rule::in(['income', 'egress', 'transfer'])],
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'related_account_id' => 'nullable|required_if:type,transfer|exists:accounts,id',
            'category_id' => 'nullable|required_if:type,income|required_if:type,egress|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'movement_date' => 'required|date',
        ]);

        try {
            $this->movementService->createMovement($request->all(), Auth::user());
            return back()->with('success', '¡Movimiento registrado exitosamente!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
