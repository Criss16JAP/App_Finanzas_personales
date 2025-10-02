<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Movimiento
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Actualizar Datos del Movimiento</h3>

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('movements.update', $movement) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 mb-2">Tipo de Movimiento</label>
                            <div class="flex space-x-4">
                                <label><input type="radio" name="type" value="egress" @checked(old('type', $movement->type) == 'egress')> Gasto</label>
                                <label><input type="radio" name="type" value="income" @checked(old('type', $movement->type) == 'income')> Ingreso</label>
                                <label><input type="radio" name="type" value="transfer" @checked(old('type', $movement->type) == 'transfer')> Transferencia</label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div id="account_id_wrapper">
                                    <label for="account_id" class="block font-medium text-sm text-gray-700">Cuenta de Origen</label>
                                    <select name="account_id" id="account_id" class="block mt-1 w-full rounded-md" required>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" @selected(old('account_id', $movement->account_id) == $account->id)>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="egress_category_wrapper" class="mt-4">
                                    <label for="egress_category_id" class="block font-medium text-sm text-gray-700">Categoría del Gasto</label>
                                    <select name="category_id" id="egress_category_id" class="block mt-1 w-full rounded-md">
                                        @foreach ($egressCategories as $category)
                                            <option value="{{ $category->id }}" @selected(old('category_id', $movement->category_id) == $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="amount" class="block font-medium text-sm text-gray-700 mt-4">Monto</label>
                                    <input type="number" name="amount" id="amount" value="{{ old('amount', $movement->amount) }}" step="0.01" min="0.01" class="block mt-1 w-full rounded-md" required>
                                </div>
                            </div>
                            <div>
                                <div id="related_account_id_wrapper" class="hidden">
                                    <label for="related_account_id" class="block font-medium text-sm text-gray-700">Cuenta de Destino</label>
                                    <select name="related_account_id" id="related_account_id" class="block mt-1 w-full rounded-md">
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" @selected(old('related_account_id', $movement->related_account_id) == $account->id)>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="income_category_wrapper" class="hidden mt-4">
                                    <label for="income_category_id" class="block font-medium text-sm text-gray-700">Categoría del Ingreso</label>
                                    <select name="category_id" id="income_category_id" class="block mt-1 w-full rounded-md">
                                        @foreach ($incomeCategories as $category)
                                            <option value="{{ $category->id }}" @selected(old('category_id', $movement->category_id) == $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="movement_date" class="block font-medium text-sm text-gray-700 mt-4">Fecha</label>
                                    <input type="date" name="movement_date" id="movement_date" value="{{ old('movement_date', $movement->movement_date->format('Y-m-d')) }}" class="block mt-1 w-full rounded-md" required>
                                </div>
                                <div class="mt-4">
                                    <label for="description" class="block font-medium text-sm text-gray-700">Descripción</label>
                                    <input type="text" name="description" id="description" value="{{ old('description', $movement->description) }}" class="block mt-1 w-full rounded-md">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase">Actualizar Movimiento</button>
                            <a href="{{ route('movements.history') }}" class="ml-4">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeRadios = document.querySelectorAll('input[name="type"]');

            const accountWrapper = document.getElementById('account_id_wrapper');
            const relatedAccountWrapper = document.getElementById('related_account_id_wrapper');
            const incomeCategoryWrapper = document.getElementById('income_category_wrapper');
            const egressCategoryWrapper = document.getElementById('egress_category_wrapper');

            const accountSelect = document.getElementById('account_id');
            const relatedAccountSelect = document.getElementById('related_account_id');
            const incomeCategorySelect = document.getElementById('income_category_id');
            const egressCategorySelect = document.getElementById('egress_category_id');

            function updateFormVisibility() {
                const selectedType = document.querySelector('input[name="type"]:checked').value;

                // Ocultar todo primero
                relatedAccountWrapper.classList.add('hidden');
                incomeCategoryWrapper.classList.add('hidden');

                // Deshabilitar selects para que no se envíen si están ocultos
                relatedAccountSelect.name = '';
                incomeCategorySelect.name = '';
                egressCategorySelect.name = 'category_id'; // Por defecto es gasto

                accountWrapper.querySelector('label').textContent = 'Cuenta de Origen';
                accountSelect.name = 'account_id';

                if (selectedType === 'income') {
                    accountWrapper.querySelector('label').textContent = 'Cuenta de Destino';
                    incomeCategoryWrapper.classList.remove('hidden');
                    egressCategoryWrapper.classList.add('hidden');

                    incomeCategorySelect.name = 'category_id';
                    egressCategorySelect.name = '';

                } else if (selectedType === 'egress') {
                    egressCategoryWrapper.classList.remove('hidden');

                } else if (selectedType === 'transfer') {
                    relatedAccountWrapper.classList.remove('hidden');
                    egressCategoryWrapper.classList.add('hidden');

                    relatedAccountSelect.name = 'related_account_id';
                    egressCategorySelect.name = '';
                }
            }

            typeRadios.forEach(radio => radio.addEventListener('change', updateFormVisibility));

            // Llamar a la función al cargar la página para establecer el estado inicial
            updateFormVisibility();
        });
    </script>
</x-app-layout>
