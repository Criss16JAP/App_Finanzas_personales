<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Historial de Créditos Completados
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('credits.index') }}" class="text-indigo-600 hover:underline">&larr; Volver a Créditos Activos</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs uppercase">Tasa Interés</th>
                                    <th class="px-6 py-3 text-right text-xs uppercase">Monto Original</th>
                                    <th class="px-6 py-3 text-center text-xs uppercase">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($credits as $credit)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('credits.show', $credit) }}" class="font-semibold text-indigo-600 hover:underline">
                                                {{ $credit->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-center">{{ number_format($credit->interest_rate * 100, 2) }} %</td>
                                        <td class="px-6 py-4 text-right">${{ number_format($credit->principal_amount, 2) }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Completado
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center">No tienes créditos completados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $credits->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
