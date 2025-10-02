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
                        <div class="bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('credits.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-2">
                                <label for="name">Nombre del Crédito</label>
                                <input type="text" name="name" class="block mt-1 w-full rounded-md" required
                                    placeholder="Ej: Crédito de Vehículo">
                            </div>
                            <div>
                                <label for="principal_amount">Monto Recibido</label>
                                <input type="number" name="principal_amount" step="0.01" min="0.01"
                                    class="block mt-1 w-full rounded-md" required>
                            </div>
                            <div>
                                <label for="interest_rate">Tasa de Interés Mensual (%)</label>
                                <input type="number" name="interest_rate" step="0.01" min="0"
                                    class="block mt-1 w-full rounded-md" required placeholder="Ej: 1.5">
                            </div>
                            <div>
                                <label for="term_months">Plazo (en meses)</label>
                                <input type="number" name="term_months" min="1"
                                    class="block mt-1 w-full rounded-md" required>
                            </div>
                            <div>
                                <label for="fixed_monthly_fee">Cargo Fijo Mensual (Seguro, etc.)</label>
                                <input type="number" name="fixed_monthly_fee" step="0.01" min="0"
                                    class="block mt-1 w-full rounded-md" placeholder="Opcional, ej: 7000">
                            </div>
                            <div>
                                <label for="payment_day_of_month">Día de Pago Mensual</label>
                                <input type="number" name="payment_day_of_month" min="1" max="31"
                                    class="block mt-1 w-full rounded-md" required placeholder="Ej: 15">
                            </div>
                            <div>
                                <label for="issued_date">Fecha de Recepción</label>
                                <input type="date" name="issued_date" value="{{ date('Y-m-d') }}"
                                    class="block mt-1 w-full rounded-md" required>
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
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase">Registrar
                                Crédito</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium">Créditos Activos</h3>
                    <a href="{{ route('credits.history') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md font-semibold text-xs text-gray-800 uppercase hover:bg-gray-300">
                        Ver Historial
                    </a>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs uppercase">Tasa Interés</th>
                                    <th class="px-6 py-3 text-right text-xs uppercase">Monto Original</th>
                                    <th class="px-6 py-3 text-right text-xs uppercase">Saldo Actual</th>
                                    <th class="px-6 py-3 text-right text-xs uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($credits as $credit)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('credits.show', $credit) }}"
                                                class="font-semibold text-indigo-600 hover:underline">
                                                {{ $credit->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            {{ number_format($credit->interest_rate * 100, 2) }} %</td>
                                        <td class="px-6 py-4 text-right">
                                            ${{ number_format($credit->principal_amount, 2) }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-orange-600">
                                            ${{ number_format($credit->current_balance, 2) }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <button class="text-green-600 hover:text-green-900 pay-button"
                                                data-credit-id="{{ $credit->id }}"
                                                data-credit-name="{{ $credit->name }}"
                                                data-current-balance="{{ $credit->current_balance }}">
                                                Realizar Pago
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center">No tienes créditos activos.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="paymentModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 hidden opacity-0 invisible transition-opacity duration-300 ease-in-out">
        <div id="modalBox"
            class="p-6 border shadow-lg rounded-xl bg-white w-full max-w-md transform scale-95 transition-transform duration-300 ease-in-out">
            <h3 class="text-lg text-center font-medium text-gray-900" id="modalTitle">Realizar Pago</h3>
            <form id="paymentForm" method="POST" class="mt-4">
                @csrf
                @if ($paymentCategory ?? null)
                    <input type="hidden" name="category_id" value="{{ $paymentCategory->id }}">
                @else
                @endif
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-1">
                        <label for="paymentAmount">Monto a Pagar</label>
                        <a href="#" id="payFullAmountBtn" class="text-sm text-blue-600 hover:underline">Pagar
                            Total</a>
                    </div>
                    <input type="number" name="amount" id="paymentAmount" step="0.01" min="0.01"
                        class="w-full px-3 py-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="account_id">Pagar desde la Cuenta</label>
                    <select name="account_id" id="account_id" class="mt-1 w-full px-3 py-2 border rounded-md"
                        required>
                        <option value="">Selecciona una cuenta</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" id="closeModal"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md font-semibold text-xs uppercase hover:bg-gray-300">Cancelar</button>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Confirmar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('paymentModal');
            const modalBox = document.getElementById('modalBox');
            const closeModalBtn = document.getElementById('closeModal');
            const payButtons = document.querySelectorAll('.pay-button');
            const paymentForm = document.getElementById('paymentForm');
            const modalTitle = document.getElementById('modalTitle');
            const paymentAmountInput = document.getElementById('paymentAmount');
            const payFullAmountBtn = document.getElementById('payFullAmountBtn');

            let currentBalanceForModal = 0;

            const openModal = () => {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0', 'invisible');
                    modalBox.classList.remove('scale-95');
                }, 10);
            };

            const closeModal = () => {
                modal.classList.add('opacity-0');
                modalBox.classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden', 'invisible');
                }, 300);
            };

            payButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const creditId = this.dataset.creditId;
                    const creditName = this.dataset.creditName;
                    currentBalanceForModal = parseFloat(this.dataset.currentBalance);

                    paymentForm.action = `/credits/${creditId}/pay`;
                    modalTitle.textContent = `Pagar: ${creditName}`;
                    paymentAmountInput.max = currentBalanceForModal;
                    paymentAmountInput.value = '';
                    paymentAmountInput.placeholder = `Máximo: ${currentBalanceForModal.toFixed(2)}`;

                    openModal();
                });
            });

            payFullAmountBtn.addEventListener('click', function(e) {
                e.preventDefault();
                paymentAmountInput.value = currentBalanceForModal.toFixed(2);
            });

            closeModalBtn.addEventListener('click', closeModal);
            window.addEventListener('click', (event) => {
                if (event.target == modal) {
                    closeModal();
                }
            });
        });
    </script>
</x-app-layout>
