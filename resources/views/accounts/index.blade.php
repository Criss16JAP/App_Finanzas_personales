<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mis Cuentas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">Añadir Nueva Cuenta</h3>

                        @if (session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('accounts.store') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="name" class="block font-medium text-sm text-gray-700">Nombre</label>
                                    <input type="text" name="name" id="name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                </div>
                                <div>
                                    <label for="type" class="block font-medium text-sm text-gray-700">Tipo</label>
                                    <select name="type" id="type" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                        <option value="bank">Cuenta Bancaria</option>
                                        <option value="cash">Efectivo</option>
                                        <option value="credit_card">Tarjeta de Crédito</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="balance" class="block font-medium text-sm text-gray-700">Saldo Inicial</label>
                                    <input type="number" name="balance" id="balance" step="0.01" min="0" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">
                                    Guardar Cuenta
                                </button>
                            </div>
                        </form>
                    </div>

                    <hr class="my-6">

                    <div>
                        <h3 class="text-lg font-medium mb-4">Cuentas Existentes</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th> </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($accounts as $account)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">{{ $account->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst(str_replace('_', ' ', $account->type)) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right font-bold">$ {{ number_format($account->balance, 2) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"> <a href="{{ route('accounts.edit', $account) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Editar</a>

                    <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta cuenta?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-6 py-4 text-center text-gray-500"> Aún no has añadido ninguna cuenta.
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
    </div>
</x-app-layout>
