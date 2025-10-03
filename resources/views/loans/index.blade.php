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
                        <div class="bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}
                        </div>
                    @endif

                    @if ($loanCategory)
                        <form action="{{ route('loans.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="category_id" value="{{ $loanCategory->id }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name">Descripción del Préstamo</label>
                                    <input type="text" name="name" class="block mt-1 w-full rounded-md" required
                                        placeholder="Ej: Préstamo a mi hermano">
                                </div>
                                <div>
                                    <label for="borrower_name">Nombre del Deudor</label>
                                    <input type="text" name="borrower_name" class="block mt-1 w-full rounded-md"
                                        required>
                                </div>
                                <div>
                                    <label for="amount">Monto a Prestar</label>
                                    <input type="number" name="amount" step="0.01" min="0.01"
                                        class="block mt-1 w-full rounded-md" required>
                                </div>
                                <div>
                                    <label for="account_id">Sale de la Cuenta</label>
                                    <select name="account_id" class="block mt-1 w-full rounded-md" required>
                                        <option value="">Selecciona una cuenta</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }}
                                                (${{ number_format($account->balance, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="loan_date">Fecha del Préstamo</label>
                                    <input type="date" name="loan_date" value="{{ date('Y-m-d') }}"
                                        class="block mt-1 w-full rounded-md" required>
                                </div>
                            </div>
                            <div class="mt-6">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase">Registrar
                                    Préstamo</button>
                            </div>
                        </form>
                    @else
                        <div class="bg-yellow-100 border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                            <strong>Atención:</strong> Por favor, ve a la sección de <a
                                href="{{ route('categories.index') }}" class="font-bold underline">Categorías</a> y crea
                            una categoría de gasto llamada "Préstamos" para poder continuar.
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
                                        <td class="px-6 py-4 text-right font-bold">
                                            ${{ number_format($loan->total_amount - $loan->paid_amount, 2) }}</td>
                                        <td class="px-6 py-4 text-right">${{ number_format($loan->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button class="text-green-600 hover:text-green-900 repay-button"
                                                data-loan-id="{{ $loan->id }}"
                                                data-loan-name="{{ $loan->name }}"
                                                data-remaining-balance="{{ $loan->total_amount - $loan->paid_amount }}">
                                                Abonar
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center">No hay préstamos pendientes.
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

    <div id="repayModal"
        class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 hidden opacity-0 invisible transition-opacity duration-300 ease-in-out">

        <div id="modalBox"
            class="p-6 border shadow-lg rounded-xl bg-white w-full max-w-sm transform scale-95 transition-transform duration-300 ease-in-out">
            <h3 class="text-lg text-center font-medium text-gray-900" id="modalTitle">Registrar Abono</h3>
            <form id="repayForm" method="POST" class="mt-4">
                @csrf
                @if ($loanCategory ?? null)
                    <input type="hidden" name="category_id" value="{{ $loanCategory->id }}">
                @endif
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-1">
                        <label for="repayAmount">Monto a Abonar</label>
                        <a href="#" id="payFullLoanBtn" class="text-sm text-blue-600 hover:underline">Pagar
                            Total</a>
                    </div>
                    <input type="number" name="amount" id="repayAmount" step="0.01" min="0.01"
                        class="w-full px-3 py-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label>Ingresa a la Cuenta</label>
                    <select name="account_id" class="w-full px-3 py-2 border rounded-md" required>
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
                        Confirmar Abono
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('repayModal');
            const modalBox = document.getElementById('modalBox');
            const closeModalBtn = document.getElementById('closeModal');
            const repayButtons = document.querySelectorAll('.repay-button');
            const repayForm = document.getElementById('repayForm');
            const modalTitle = document.getElementById('modalTitle');
            const repayAmountInput = document.getElementById('repayAmount');
            const payFullLoanBtn = document.getElementById('payFullLoanBtn');

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

            repayButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevenir cualquier acción por defecto
                    const loanId = this.dataset.loanId;
                    const loanName = this.dataset.loanName;
                    currentBalanceForModal = parseFloat(this.dataset.remainingBalance);

                    repayForm.action = `/loans/${loanId}/repay`;
                    modalTitle.textContent = `Abonar a: ${loanName}`;
                    repayAmountInput.max = currentBalanceForModal;
                    repayAmountInput.value = '';
                    repayAmountInput.placeholder = `Máximo: ${currentBalanceForModal.toFixed(2)}`;

                    openModal();
                });
            });

            payFullLoanBtn.addEventListener('click', function(e) {
                e.preventDefault();
                repayAmountInput.value = currentBalanceForModal.toFixed(2);
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
