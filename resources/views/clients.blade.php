<x-app-layout>
    <div class="max-w-7xl mx-auto p-6">

        <div class="bg-white p-6 rounded shadow mb-6">
            <h2 class="text-xl font-bold mb-4">Adaugă Client</h2>

            <form method="POST" action="{{ route('clients.store') }}">
                @csrf

                <div class="mb-3">
                    <label>Nume</label>
                    <input type="text" name="name" class="w-full border rounded p-2" required>
                </div>

                <div class="mb-3">
                    <label>Telefon</label>
                    <input type="text" name="phone" class="w-full border rounded p-2">
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="w-full border rounded p-2">
                </div>
                <button class="bg-blue-600 text-white px-6 py-3 rounded mt-4">
                   Salvează
              </button>

            </form>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold mb-4">Lista Clienți</h2>

            <table class="w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border">Nume</th>
                        <th class="p-2 border">Telefon</th>
                        <th class="p-2 border">Email</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clients as $client)
                        <tr>
                            <td class="p-2 border">{{ $client->name }}</td>
                            <td class="p-2 border">{{ $client->phone }}</td>
                            <td class="p-2 border">{{ $client->email }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
