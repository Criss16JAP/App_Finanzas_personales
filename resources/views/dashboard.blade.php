<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-gray-500 text-sm font-medium uppercase">Saldo en Cuentas</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        $ {{ number_format($totalBalance, 2) }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-gray-500 text-sm font-medium uppercase">Ingresos ({{ now()->translatedFormat('F') }})</h3>
                    <p class="text-3xl font-bold text-green-600 mt-2">
                        $ {{ number_format($monthlyIncome, 2) }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-gray-500 text-sm font-medium uppercase">Gastos ({{ now()->translatedFormat('F') }})</h3>
                    <p class="text-3xl font-bold text-red-600 mt-2">
                        $ {{ number_format($monthlyExpenses, 2) }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-gray-500 text-sm font-medium uppercase">Deuda Tarjetas de Crédito</h3>
                    <p class="text-3xl font-bold text-orange-600 mt-2">
                        $ {{ number_format($totalCreditCardDebt, 2) }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-gray-500 text-sm font-medium uppercase">Deuda Créditos</h3>
                    <p class="text-3xl font-bold text-orange-600 mt-2">
                        $ {{ number_format($totalLoanDebt, 2) }}
                    </p>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-gray-500 text-sm font-medium uppercase">Dinero Prestado</h3>
                    <p class="text-3xl font-bold text-blue-600 mt-2">
                        $ {{ number_format($totalLoaned, 2) }}
                    </p>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
