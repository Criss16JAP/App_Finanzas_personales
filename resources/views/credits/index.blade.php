<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mis Créditos (Deudas)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Registrar Nuevo Crédito</h3>
                    @if (session('success'))
                        <div class="bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('credits.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-2">
                                <label for="name">Nombre del Crédito</label>
                                <input type="text" name="name" class="block mt-1 w-full rounded-md" required placeholder="Ej: Crédito de Vehículo">
                            </div>
                            <div>
                                <label for="principal_amount">Monto Recibido</label>
                                <input type="number" name="principal_amount" step="0.01" min="0.01" class="block mt-1 w-full rounded-md" required>
                            </div>
                            <div>
                                <label for="interest_rate">Tasa de Interés Mensual (%)</label>
                                <input type="number" name="interest_rate" step="0.01" min="0" class="block mt-1 w-full rounded-md" required placeholder="Ej: 1.5">
                            </div>
                            <div>
                                <label for="term_months">Plazo (en meses)</label>
                                <input type="number" name="term_months" min="1" class="block mt-1 w-full rounded-md" required>
                            </div>
                            <div>
                                <label for="payment_day_of_month">Día de Pago Mensual</label>
                                <input type="number" name="payment_day_of_month" min="1" max="31" class="block mt-1 w-full rounded-md" required placeholder="Ej: 15">
                            </div>
                             <div>
                                <label for="issued_date">Fecha de Recepción</label>
                                <input type="date" name="issued_date" value="{{ date('Y-m-d') }}" class="block mt-1 w-full rounded-md" required>
                            </div>
                            <div class="md:col-span-2">
                                <label for="account_id_deposit">Depositar en la Cuenta</label>
                                <select name="account_id_deposit" class="block mt-1 w-full rounded-md" required>
                                    <option value="">Selecciona una cuenta</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                             <div class="md:col-span-3">
                                <label for="description">Descripción (Opcional)</label>
                                <textarea name="description" rows="2" class="block mt-1 w-full rounded-md"></textarea>
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase">Registrar Crédito</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Créditos Activos</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs uppercase">Fecha de Emisión</th>
                                    <th class="px-6 py-3 text-right text-xs uppercase">Monto Original</th>
                                    <th class="px-6 py-3 text-right text-xs uppercase">Saldo Actual</th>
                                    <th class="px-6 py-3 text-right text-xs uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($credits as $credit)
                                    <tr>
                                        <td class="px-6 py-4">{{ $credit->name }}</td>
                                        <td class="px-6 py-4">{{ $credit->issued_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 text-right">${{ number_format($credit->principal_amount, 2) }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-orange-600">${{ number_format($credit->current_balance, 2) }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <button
                                                class="text-green-600 hover:text-green-900 pay-button"
                                                data-credit-id="{{ $credit->id }}"
                                                data-credit-name="{{ $credit->name }}"
                                                data-current-balance="{{ $credit->current_balance }}">
                                                Realizar Pago
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center">No tienes créditos activos.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-lg text-center font-medium text-gray-900" id="modalTitle">Realizar Pago</h3>
            <form id="paymentForm" method="POST" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label>Monto a Pagar</label>
                    <input type="number" name="amount" id="paymentAmount" step="0.01" min="0.01" class="w-full px-3 py-2 border rounded" required>
                </div>
                <div class="mb-4">
                    <label>Pagar desde la Cuenta</label>
                    <select name="account_id" class="w-full px-3 py-2 border rounded" required>
                        <option value="">Selecciona una cuenta</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" id="closeModal" class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Confirmar Pago</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('paymentModal');
            const closeModalBtn = document.getElementById('closeModal');
            const payButtons = document.querySelectorAll('.pay-button');
            const paymentForm = document.getElementById('paymentForm');
            const modalTitle = document.getElementById('modalTitle');
            const paymentAmountInput = document.getElementById('paymentAmount');

            payButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const creditId = this.dataset.creditId;
                    const creditName = this.dataset.creditName;
                    const currentBalance = parseFloat(this.dataset.currentBalance);

                    paymentForm.action = `/credits/${creditId}/pay`;
                    modalTitle.textContent = `Pagar: ${creditName}`;
                    paymentAmountInput.max = currentBalance;
                    paymentAmountInput.placeholder = `Máximo: ${currentBalance.toFixed(2)}`;

                    modal.classList.remove('hidden');
                });
            });

            closeModalBtn.addEventListener('click', () => modal.classList.add('hidden'));
            window.addEventListener('click', (event) => {
                if (event.target == modal) modal.classList.add('hidden');
            });
        });
    </script>
</x-app-layout>
