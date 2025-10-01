<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mis Categorías
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">Añadir Nueva Categoría</h3>
                        @if (session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        <form action="{{ route('categories.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @csrf
                            <div>
                                <label for="name">Nombre</label>
                                <input type="text" name="name" id="name" class="block mt-1 w-full rounded-md" required>
                            </div>
                            <div>
                                <label for="type">Tipo</label>
                                <select name="type" id="type" class="block mt-1 w-full rounded-md" required>
                                    <option value="egress">Gasto</option>
                                    <option value="income">Ingreso</option>
                                </select>
                            </div>
                            <div class="pt-5">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase">Guardar</button>
                            </div>
                        </form>
                    </div>

                    <hr class="my-6">

                    <div>
                        <h3 class="text-lg font-medium mb-4">Categorías Existentes</h3>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Tipo</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($categories as $category)
                                    <tr>
                                        <td class="px-6 py-4">{{ $category->name }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $category->type == 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $category->type == 'income' ? 'Ingreso' : 'Gasto' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm">
                                            <a href="{{ route('categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Editar</a>
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center">No has añadido ninguna categoría.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
