<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Cuenta: {{ $account->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h3 class="text-lg font-medium mb-4">Actualizar datos de la cuenta</h3>

                    <form action="{{ route('accounts.update', $account) }}" method="POST">
                        @csrf
                        @method('PUT') <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="name" class="block font-medium text-sm text-gray-700">Nombre</label>
                                <input type="text" name="name" id="name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" value="{{ old('name', $account->name) }}" required>
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="type" class="block font-medium text-sm text-gray-700">Tipo</label>
                                <input type="text" id="type" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 bg-gray-100" value="{{ ucfirst(str_replace('_', ' ', $account->type)) }}" disabled>
                            </div>

                            <div>
                                <label for="balance" class="block font-medium text-sm text-gray-700">Saldo</label>
                                <input type="text" id="balance" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 bg-gray-100" value="$ {{ number_format($account->balance, 2) }}" disabled>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">
                                Actualizar Cuenta
                            </button>
                            <a href="{{ route('accounts.index') }}" class="ml-4 text-sm text-gray-600 hover:text-gray-900">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
