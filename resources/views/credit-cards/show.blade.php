<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalles de Tarjeta: {{ $card->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-gray-500 text-sm font-medium uppercase">Cupo Límite</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">${{ number_format($card->credit_limit, 2) }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-gray-500 text-sm font-medium uppercase">Deuda Actual</h3>
                    <p class="text-3xl font-bold text-red-600 mt-2">${{ number_format($card->current_debt, 2) }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-gray-500 text-sm font-medium uppercase">Cupo Disponible</h3>
                    <p class="text-3xl font-bold text-green-600 mt-2">${{ number_format($card->credit_limit - $card->current_debt, 2) }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Historial de Compras</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs uppercase">Descripción</th>
                                    <th class="px-6 py-3 text-right text-xs uppercase">Monto Total</th>
                                    <th class="px-6 py-3 text-right text-xs uppercase">Cuotas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($purchases as $purchase)
                                    <tr>
                                        <td class="px-6 py-4">{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4">{{ $purchase->description }}</td>
                                        <td class="px-6 py-4 text-right">${{ number_format($purchase->purchase_amount, 2) }}</td>
                                        <td class="px-6 py-4 text-right">{{ $purchase->installments_paid }} / {{ $purchase->installments }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center">No hay compras registradas para esta tarjeta.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $purchases->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
