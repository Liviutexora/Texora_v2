<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Programări
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- FORM --}}
        <div class="bg-white p-6 rounded shadow mb-6">
            <h3 class="text-lg font-bold mb-4">Adaugă Programare</h3>

            <form method="POST" action="/appointments">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label>Client</label>
                        <input type="text" name="client_name" class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label>Serviciu</label>
                        <input type="text" name="service" class="w-full border p-2 rounded">
                    </div>

                    <div>
                        <label>Data & Ora</label>
                        <input type="datetime-local" name="appointment_time" class="w-full border p-2 rounded" required>
                    </div>

                    <div>
                        <label>Notițe</label>
                        <input type="text" name="notes" class="w-full border p-2 rounded">
                    </div>

                </div>

                <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
                    Salvează
                </button>
            </form>
        </div>

        {{-- LIST --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-bold mb-4">Lista Programări</h3>

            <table class="w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border">Client</th>
                        <th class="p-2 border">Serviciu</th>
                        <th class="p-2 border">Data</th>
                        <th class="p-2 border">Notițe</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($appointments as $app)
                        <tr>
                            <td class="p-2 border">{{ $app->client_name }}</td>
                            <td class="p-2 border">{{ $app->service }}</td>
                            <td class="p-2 border">{{ $app->appointment_time }}</td>
                            <td class="p-2 border">{{ $app->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">
                                Nu există programări
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

    </div>
</x-app-layout>
