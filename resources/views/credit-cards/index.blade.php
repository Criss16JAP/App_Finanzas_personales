<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tarjetas de Crédito
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Añadir Nueva Tarjeta de Crédito</h3>
                    @if (session('success'))
                        <div class="bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}</div>
                    @endif

                    <form action="{{ route('credit-cards.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="lg:col-span-2">
                                <label for="name">Nombre de la Tarjeta</label>
                                <input type="text" name="name" class="block mt-1 w-full rounded-md" required
                                    placeholder="Ej: Visa Bancolombia">
                            </div>
                            <div>
                                <label for="credit_limit">Cupo Límite</label>
                                <input type="number" name="credit_limit" step="0.01" min="0"
                                    class="block mt-1 w-full rounded-md" required>
                            </div>
                            <div>
                                <label for="interest_rate">Tasa de Interés Mensual (%)</label>
                                <input type="number" name="interest_rate" step="0.01" min="0"
                                    class="block mt-1 w-full rounded-md" required placeholder="Ej: 1.84">
                            </div>
                            <div>
                                <label for="monthly_fee">Cuota de Manejo</label>
                                <input type="number" name="monthly_fee" step="0.01" min="0"
                                    class="block mt-1 w-full rounded-md" placeholder="Opcional">
                            </div>
                            <div>
                                <label for="cutoff_day">Día de Corte</label>
                                <input type="number" name="cutoff_day" min="1" max="31"
                                    class="block mt-1 w-full rounded-md" required placeholder="Día del mes (ej: 15)">
                            </div>
                            <div>
                                <label for="payment_day">Día de Pago</label>
                                <input type="number" name="payment_day" min="1" max="31"
                                    class="block mt-1 w-full rounded-md" required placeholder="Día del mes (ej: 30)">
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase">Añadir
                                Tarjeta</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Mis Tarjetas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse ($creditCards as $card)
                            <div class="p-4 border rounded-lg shadow">
                                <h4 class="font-bold text-lg">
                                    <a href="{{ route('credit-cards.show', $card) }}"
                                        class="text-indigo-600 hover:underline">
                                        {{ $card->name }}
                                    </a>
                                </h4>
                                <div class="text-sm text-gray-600 mt-2">
                                    <p>Cupo Límite: <span
                                            class="font-semibold">${{ number_format($card->credit_limit, 2) }}</span>
                                    </p>
                                    <p>Deuda Actual: <span
                                            class="font-semibold text-red-600">${{ number_format($card->current_debt, 2) }}</span>
                                    </p>

                                    <p>Cupo Disponible: <span
                                            class="font-semibold text-green-600">${{ number_format($card->credit_limit - $card->current_debt, 2) }}</span>
                                    </p>

                                    <p class="mt-2">Interés Mensual: <span
                                            class="font-semibold">{{ number_format($card->interest_rate * 100, 2) }}%</span>
                                    </p>
                                    <p>Día de corte: <span class="font-semibold">{{ $card->cutoff_day }}</span> | Día de
                                        pago: <span class="font-semibold">{{ $card->payment_day }}</span></p>
                                </div>
                            </div>
                        @empty
                            <p>No has añadido ninguna tarjeta de crédito.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
