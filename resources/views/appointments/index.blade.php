<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Programări
        </h2>
    </x-slot>

    <div class="p-6">

        <form method="POST" action="{{ route('appointments.store') }}" class="bg-white p-4 rounded shadow mb-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <input type="text" name="client" placeholder="Client"
                       class="border rounded px-3 py-2">

                <input type="datetime-local" name="start_time"
                       class="border rounded px-3 py-2">

                <input type="text" name="service" placeholder="Serviciu"
                       class="border rounded px-3 py-2">

                <input type="text" name="notes" placeholder="Notițe"
                       class="border rounded px-3 py-2">

            </div>

            <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
                Salvează
            </button>
        </form>

        <div class="bg-white p-4 rounded shadow">
            <h3 class="mb-4 font-semibold">Lista Programări</h3>

            <table class="w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2">Client</th>
                        <th class="p-2">Serviciu</th>
                        <th class="p-2">Data</th>
                        <th class="p-2">Notițe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appointment)
                        <tr class="border-t">
                            <td class="p-2">{{ $appointment->client }}</td>
                            <td class="p-2">{{ $appointment->service }}</td>
                            <td class="p-2">{{ $appointment->start_time }}</td>
                            <td class="p-2">{{ $appointment->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
