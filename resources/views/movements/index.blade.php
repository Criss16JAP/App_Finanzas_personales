<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Movimientos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Registrar Nuevo Movimiento</h3>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('movements.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 mb-2">Tipo de Movimiento</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center"><input type="radio" name="type" value="egress"
                                        class="mr-2" checked>Gasto</label>
                                <label class="flex items-center"><input type="radio" name="type" value="income"
                                        class="mr-2">Ingreso</label>
                                <label class="flex items-center"><input type="radio" name="type" value="transfer"
                                        class="mr-2">Transferencia</label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div id="account_id_wrapper">
                                    <label for="account_id" class="block font-medium text-sm text-gray-700">Cuenta de
                                        Origen</label>
                                    <select name="account_id" id="account_id"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                        <option value="">Selecciona una cuenta</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }}
                                                (${{ number_format($account->balance, 2) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="egress_category_wrapper" class="mt-4">
                                    <label for="egress_category_id"
                                        class="block font-medium text-sm text-gray-700">Categoría del Gasto</label>
                                    <select name="category_id" id="egress_category_id"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                        <option value="">Selecciona una categoría</option>
                                        @foreach ($egressCategories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="amount"
                                        class="block font-medium text-sm text-gray-700 mt-4">Monto</label>
                                    <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                </div>
                            </div>
                            <div>
                                <div id="related_account_id_wrapper" class="hidden">
                                    <label for="related_account_id"
                                        class="block font-medium text-sm text-gray-700">Cuenta de Destino</label>
                                    <select name="related_account_id" id="related_account_id"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                        <option value="">Selecciona una cuenta</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }}
                                                (${{ number_format($account->balance, 2) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="income_category_wrapper" class="hidden mt-4">
                                    <label for="income_category_id"
                                        class="block font-medium text-sm text-gray-700">Categoría del Ingreso</label>
                                    <select name="category_id" id="income_category_id"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                        <option value="">Selecciona una categoría</option>
                                        @foreach ($incomeCategories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="movement_date"
                                        class="block font-medium text-sm text-gray-700 mt-4">Fecha</label>
                                    <input type="date" name="movement_date" id="movement_date"
                                        value="{{ date('Y-m-d') }}"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                </div>
                                <div class="mt-4">
                                    <label for="description" class="block font-medium text-sm text-gray-700">Descripción
                                        (Opcional)</label>
                                    <input type="text" name="description" id="description"
                                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">
                                Guardar Movimiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Últimos 15 Movimientos</h3>
                    <a href="{{ route('movements.history') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md font-semibold text-xs text-gray-800 uppercase hover:bg-gray-300">
                        Ver Historial Completo
                    </a>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Descripción</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cuenta
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Categoría</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($movements as $movement)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $movement->movement_date->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4">{{ $movement->description ?? '--' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($movement->type === 'transfer')
                                                <span class="font-semibold">{{ $movement->account->name }}</span>
                                                <span class="text-gray-500">→</span>
                                                <span
                                                    class="font-semibold">{{ $movement->relatedAccount->name ?? 'N/A' }}</span>
                                            @else
                                                {{ $movement->account->name }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $movement->category->name ?? '--' }}</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-right font-semibold
                                            @if ($movement->type == 'income') text-green-600 @endif
                                            @if ($movement->type == 'egress') text-red-600 @endif">
                                            @if ($movement->type == 'egress')
                                                -
                                            @endif
                                            ${{ number_format($movement->amount, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay
                                            movimientos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
