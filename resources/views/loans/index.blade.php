<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Préstamos (Cuentas por Cobrar)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <button
                class="text-green-600 hover:text-green-900 repay-button"
                data-loan-id="{{ $loan->id }}"
                data-loan-name="{{ $loan->name }}"
                data-remaining-balance="{{ $loan->total_amount - $loan->paid_amount }}">
                Abonar
            </button>
        </div>
    </div>

    <div id="repayModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Registrar Abono</h3>
                <div class="mt-2 px-7 py-3">
                    <form id="repayForm" method="POST">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ $loanCategory->id ?? '' }}">
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

                    // Configurar el formulario del modal
                    repayForm.action = `/loans/${loanId}/repay`;
                    modalTitle.textContent = `Abonar a: ${loanName}`;
                    repayAmountInput.max = remainingBalance;
                    repayAmountInput.placeholder = `Máximo: ${remainingBalance.toFixed(2)}`;

                    // Mostrar el modal
                    modal.classList.remove('hidden');
                });
            });

            closeModalBtn.addEventListener('click', function () {
                modal.classList.add('hidden');
            });

            // Opcional: cerrar el modal si se hace clic fuera de él
            window.addEventListener('click', function (event) {
                if (event.target == modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
