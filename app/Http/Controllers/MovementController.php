<?php

namespace App\Http\Controllers;

use App\Services\MovementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Movement;

class MovementController extends Controller
{
    public function __construct(private MovementService $movementService) {}

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

    public function history(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validar los parámetros de ordenamiento
        $sortableColumns = ['movement_date', 'amount'];
        $sort = in_array($request->query('sort'), $sortableColumns) ? $request->query('sort') : 'movement_date';
        $direction = in_array($request->query('direction'), ['asc', 'desc']) ? $request->query('direction') : 'desc';

        $movements = $user
            ->movements()
            ->with(['account', 'category', 'relatedAccount'])
            ->orderBy($sort, $direction) // <-- Ordena según los parámetros
            ->paginate(25)
            ->appends(['sort' => $sort, 'direction' => $direction]); // <-- Añade los parámetros a la paginación

        return view('movements.history', [
            'movements' => $movements,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function edit(Movement $movement)
    {
        // Seguridad: Verificar que el movimiento pertenece al usuario
        if (Auth::id() !== $movement->user_id) {
            abort(403);
        }

        // Reutilizamos el método del servicio para obtener las cuentas y categorías
        $data = $this->movementService->getDataForMovementView(Auth::user());
        $data['movement'] = $movement; // Añadimos el movimiento que se va a editar

        return view('movements.edit', $data);
    }

    /**
     * Actualiza un movimiento en la base de datos.
     */
    public function update(Request $request, Movement $movement)
    {
        // Seguridad: Verificar la propiedad
        if (Auth::id() !== $movement->user_id) {
            abort(403);
        }

        // La validación es idéntica a la del método store
        $validatedData = $request->validate([
            'type' => ['required', Rule::in(['income', 'egress', 'transfer'])],
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'related_account_id' => 'nullable|required_if:type,transfer|exists:accounts,id',
            'category_id' => 'nullable|required_if:type,income|required_if:type,egress|exists:categories,id',
            'description' => 'nullable|string|max:255',
            'movement_date' => 'required|date',
        ]);

        try {
            // Llamaremos a un nuevo método en el servicio
            $this->movementService->updateMovement($movement, $validatedData);
            return redirect()->route('movements.history')->with('success', '¡Movimiento actualizado!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Elimina un movimiento de la base de datos.
     */
    public function destroy(Movement $movement)
    {
        // Seguridad: Verificar la propiedad
        if (Auth::id() !== $movement->user_id) {
            abort(403);
        }

        try {
            // Llamaremos a un nuevo método en el servicio
            $this->movementService->deleteMovement($movement);
            return back()->with('success', '¡Movimiento eliminado!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
