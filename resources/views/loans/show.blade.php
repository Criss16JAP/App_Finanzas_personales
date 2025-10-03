<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalles del Préstamo: {{ $loan->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-gray-500 text-sm font-medium uppercase">Resumen del Préstamo</h3>
                    <div class="mt-4">
                        <div class="flex justify-between">
                            <span>Monto Prestado:</span>
                            <span class="font-semibold">${{ number_format($loan->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="font-bold">Saldo Pendiente por Cobrar:</span>
                            <span class="font-bold text-2xl text-orange-600">${{ number_format(($loan->total_amount - $loan->paid_amount) + $loan->accrued_interest_balance, 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 mt-4">
                            @php
                                $progress = $loan->total_amount > 0 ? ($loan->paid_amount / $loan->total_amount) * 100 : 0;
                            @endphp
                            <div class="bg-green-500 h-4 rounded-full" style="width: {{ $progress }}%"></div>
                        </div>
                        <p class="text-right text-sm text-gray-600 mt-1">{{ number_format($progress, 1) }}% Recibido</p>
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
                            <span>Tasa de Interés Mensual:</span>
                            <span class="font-semibold">{{ number_format($loan->interest_rate * 100, 2) }} %</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span>Cuotas Recibidas:</span>
                            <span class="font-semibold">{{ $installmentsReceived }} / {{ $loan->term_months }}</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span class="font-bold">Cuotas Faltantes:</span>
                            <span class="font-bold text-lg">{{ $remainingInstallments }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <h3 class="text-gray-500 text-sm font-medium uppercase">Ingresos Acumulados</h3>
                     <div class="mt-4">
                        <div class="flex justify-between">
                            <span>Capital Recibido:</span>
                            <span class="font-semibold">${{ number_format($totalPrincipalReceived, 2) }}</span>
                        </div>
                        <div class="flex justify-between mt-2">
                            <span>Intereses Ganados:</span>
                            <span class="font-semibold text-green-600">${{ number_format($totalInterestReceived, 2) }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between mt-2">
                            <span class="font-bold">Total Recibido:</span>
                            <span class="font-bold text-lg text-blue-600">${{ number_format($totalReceived, 2) }}</span>
                        </div>
                         <div class="flex justify-between mt-4 text-sm text-gray-600">
                            <span>Ingreso Total Proyectado:</span>
                            <span class="font-semibold">${{ number_format($totalProjectedIncome, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Historial de Abonos Recibidos</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs uppercase">Fecha</th>
                                    <th class="px-4 py-3 text-right text-xs uppercase">Monto Recibido</th>
                                    <th class="px-4 py-3 text-right text-xs uppercase">Intereses</th>
                                    <th class="px-4 py-3 text-right text-xs uppercase">Abono a Capital</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($loan->payments as $payment)
                                    <tr>
                                        <td class="px-4 py-4">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-4 text-right font-bold">${{ number_format($payment->amount_received, 2) }}</td>
                                        <td class="px-4 py-4 text-right text-green-600">${{ number_format($payment->interest_received, 2) }}</td>
                                        <td class="px-4 py-4 text-right font-semibold">${{ number_format($payment->principal_received, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-4 text-center">No se han recibido abonos para este préstamo.</td>
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
