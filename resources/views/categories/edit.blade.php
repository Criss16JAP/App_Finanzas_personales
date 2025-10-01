<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Categoría: {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="name">Nombre</label>
                                <input type="text" name="name" id="name" class="block mt-1 w-full rounded-md" value="{{ old('name', $category->name) }}" required>
                            </div>
                            <div>
                                <label for="type">Tipo</label>
                                <select name="type" id="type" class="block mt-1 w-full rounded-md" required>
                                    <option value="egress" @selected(old('type', $category->type) == 'egress')>Gasto</option>
                                    <option value="income" @selected(old('type', $category->type) == 'income')>Ingreso</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase">Actualizar</button>
                            <a href="{{ route('categories.index') }}" class="ml-4">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
