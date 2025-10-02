<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalles del Crédito: {{ $credit->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-gray-500 text-sm font-medium uppercase">Resumen del Crédito</h3>
                    <div class="mt-4">
                        <div class="flex justify-between">
                            <span>Monto Original:</span>
                            <span class="font-semibold">${{ number_format($credit->principal_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="font-bold">Saldo Actual:</span>
                            <span
                                class="font-bold text-2xl text-orange-600">${{ number_format($credit->current_balance, 2) }}</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span>Tasa de Interés Mensual:</span>
                            <span class="font-semibold">{{ number_format($credit->interest_rate * 100, 2) }} %</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 mt-4">
                            @php
                                $progress =
                                    $credit->principal_amount > 0
                                        ? ($totalPrincipalPaid / $credit->principal_amount) * 100
                                        : 0;
                            @endphp
                            <div class="bg-green-500 h-4 rounded-full" style="width: {{ $progress }}%"></div>
                        </div>
                        <p class="text-right text-sm text-gray-600 mt-1">{{ number_format($progress, 1) }}% Pagado</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-gray-500 text-sm font-medium uppercase">Detalles de Cuotas</h3>
                    <div class="mt-4">
                        <div class="flex justify-between">
                            <span>Cuota Fija Estimada:</span>
                            <span class="font-semibold text-lg">${{ number_format($monthlyPayment, 2) }}</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span>Plazo Original:</span>
                            <span class="font-semibold">{{ $credit->term_months }} meses</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span>Cuotas Pagadas:</span>
                            <span class="font-semibold">{{ $installmentsPaid }}</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="font-bold">Cuotas Faltantes:</span>
                            <span class="font-bold text-lg">{{ $remainingInstallments }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-gray-500 text-sm font-medium uppercase">Costos Acumulados</h3>
                    <div class="mt-4">
                        <div class="flex justify-between">
                            <span>Abono a Capital:</span>
                            <span class="font-semibold">${{ number_format($totalPrincipalPaid, 2) }}</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span>Intereses Pagados:</span>
                            <span class="font-semibold">${{ number_format($totalInterestPaid, 2) }}</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span>Cargos Fijos Pagados:</span>
                            <span class="font-semibold">${{ number_format($totalFeesPaid, 2) }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between mt-2">
                            <span class="font-bold">Total Pagado:</span>
                            <span class="font-bold text-lg text-blue-600">${{ number_format($totalPaid, 2) }}</span>
                        </div>
                        <div class="flex justify-between mt-4 text-sm text-gray-600">
                            <span>Costo Total Proyectado:</span>
                            <span class="font-semibold">${{ number_format($totalProjectedPayment, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Historial de Pagos</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase">Fecha</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase">Monto Pagado</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase">Cargo Fijo</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase">Intereses</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase">Abono a Capital</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($credit->payments as $payment)
                                    <tr>
                                        <td class="px-4 py-4">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-4 text-right font-bold">
                                            ${{ number_format($payment->amount_paid, 2) }}</td>
                                        <td class="px-4 py-4 text-right text-red-600">
                                            ${{ number_format($payment->fee_paid, 2) }}</td>
                                        <td class="px-4 py-4 text-right text-orange-600">
                                            ${{ number_format($payment->interest_paid, 2) }}</td>
                                        <td class="px-4 py-4 text-right text-green-600 font-semibold">
                                            ${{ number_format($payment->principal_paid, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">Aún no se han
                                            realizado pagos para este crédito.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
