<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Préstamos (Cuentas por Cobrar)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Registrar Nuevo Préstamo</h3>

                    @if (session('success'))
                        <div class="bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
                    @endif

                    @if($loanCategory)
                        <form action="{{ route('loans.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="category_id" value="{{ $loanCategory->id }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name">Descripción del Préstamo</label>
                                    <input type="text" name="name" class="block mt-1 w-full rounded-md" required placeholder="Ej: Préstamo a mi hermano">
                                </div>
                                <div>
                                    <label for="borrower_name">Nombre del Deudor</label>
                                    <input type="text" name="borrower_name" class="block mt-1 w-full rounded-md" required>
                                </div>
                                <div>
                                    <label for="amount">Monto a Prestar</label>
                                    <input type="number" name="amount" step="0.01" min="0.01" class="block mt-1 w-full rounded-md" required>
                                </div>
                                <div>
                                    <label for="account_id">Sale de la Cuenta</label>
                                    <select name="account_id" class="block mt-1 w-full rounded-md" required>
                                        <option value="">Selecciona una cuenta</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }} (${{ number_format($account->balance, 2) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="loan_date">Fecha del Préstamo</label>
                                    <input type="date" name="loan_date" value="{{ date('Y-m-d') }}" class="block mt-1 w-full rounded-md" required>
                                </div>
                            </div>
                            <div class="mt-6">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase">Registrar Préstamo</button>
                            </div>
                        </form>
                    @else
                        <div class="bg-yellow-100 border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                            <strong>Atención:</strong> Por favor, ve a la sección de <a href="{{ route('categories.index') }}" class="font-bold underline">Categorías</a> y crea una categoría de gasto llamada "Préstamos" para poder continuar.
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Préstamos Pendientes de Pago</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs uppercase">Descripción</th>
                                    <th class="px-6 py-3 text-left text-xs uppercase">Deudor</th>
                                    <th class="px-6 py-3 text-right text-xs uppercase">Saldo Pendiente</th>
                                    <th class="px-6 py-3 text-right text-xs uppercase">Total Prestado</th>
                                    <th class="px-6 py-3 text-right text-xs uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($loans as $loan)
                                    <tr>
                                        <td class="px-6 py-4">{{ $loan->loan_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4">{{ $loan->name }}</td>
                                        <td class="px-6 py-4">{{ $loan->borrower_name }}</td>
                                        <td class="px-6 py-4 text-right font-bold">${{ number_format($loan->total_amount - $loan->paid_amount, 2) }}</td>
                                        <td class="px-6 py-4 text-right">${{ number_format($loan->total_amount, 2) }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <button
                                                class="text-green-600 hover:text-green-900 repay-button"
                                                data-loan-id="{{ $loan->id }}"
                                                data-loan-name="{{ $loan->name }}"
                                                data-remaining-balance="{{ $loan->total_amount - $loan->paid_amount }}">
                                                Abonar
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center">No hay préstamos pendientes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="repayModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Registrar Abono</h3>
                <div class="mt-2 px-7 py-3">
                    <form id="repayForm" method="POST">
                        @csrf
                        @if($loanCategory)
                            <input type="hidden" name="category_id" value="{{ $loanCategory->id }}">
                        @endif
                        <div class="mb-4">
                            <label class="text-left block">Monto a Abonar</label>
                            <input type="number" name="amount" id="repayAmount" step="0.01" min="0.01" class="w-full px-3 py-2 text-gray-700 border rounded-lg" required>
                        </div>
                        <div class="mb-4">
                            <label class="text-left block">Ingresa a la Cuenta</label>
                            <select name="account_id" class="w-full px-3 py-2 text-gray-700 border rounded-lg" required>
                                <option value="">Selecciona una cuenta</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="items-center px-4 py-3">
                            <button id="submitRepay" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600">
                                Registrar Abono
                            </button>
                        </div>
                    </form>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="closeModal" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('repayModal');
            if (!modal) return; // Salir si el modal no existe

            const closeModalBtn = document.getElementById('closeModal');
            const repayButtons = document.querySelectorAll('.repay-button');
            const repayForm = document.getElementById('repayForm');
            const modalTitle = document.getElementById('modalTitle');
            const repayAmountInput = document.getElementById('repayAmount');

            repayButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const loanId = this.dataset.loanId;
                    const loanName = this.dataset.loanName;
                    const remainingBalance = parseFloat(this.dataset.remainingBalance);

                    repayForm.action = `/loans/${loanId}/repay`;
                    modalTitle.textContent = `Abonar a: ${loanName}`;
                    repayAmountInput.max = remainingBalance;
                    repayAmountInput.placeholder = `Máximo: ${remainingBalance.toFixed(2)}`;

                    modal.classList.remove('hidden');
                });
            });

            if(closeModalBtn) {
                closeModalBtn.addEventListener('click', function () {
                    modal.classList.add('hidden');
                });
            }

            window.addEventListener('click', function (event) {
                if (event.target == modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
