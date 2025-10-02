<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Historial de Movimientos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        @php
                                            $dateDirection =
                                                $sort === 'movement_date' && $direction === 'asc' ? 'desc' : 'asc';
                                        @endphp
                                        <a
                                            href="{{ route('movements.history', ['sort' => 'movement_date', 'direction' => $dateDirection]) }}">Fecha</a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Descripción</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cuenta
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Categoría</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                        @php
                                            $amountDirection =
                                                $sort === 'amount' && $direction === 'asc' ? 'desc' : 'asc';
                                        @endphp
                                        <a
                                            href="{{ route('movements.history', ['sort' => 'amount', 'direction' => $amountDirection]) }}">Monto</a>
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($movements as $movement)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $movement->movement_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4">{{ $movement->description ?? '--' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($movement->type === 'transfer')
                                                <span class="font-semibold">{{ $movement->account->name }}</span>
                                                <span class="text-gray-500">→</span>
                                                <span
                                                    class="font-semibold">{{ $movement->relatedAccount->name ?? 'N/A' }}</span>
                                            @else
                                                {{ $movement->account->name }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $movement->category->name ?? '--' }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-right font-semibold
                    @if ($movement->type == 'income') text-green-600 @endif
                    @if ($movement->type == 'egress') text-red-600 @endif">
                                            @if ($movement->type == 'egress')
                                                -
                                            @endif
                                            ${{ number_format($movement->amount, 2) }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('movements.edit', $movement) }}"
                                                class="text-indigo-600 hover:text-indigo-900 mr-4">Editar</a>
                                            <form action="{{ route('movements.destroy', $movement) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('¿Estás seguro? Al eliminar este movimiento se revertirá el efecto en los saldos de las cuentas.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay
                                            movimientos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $movements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
