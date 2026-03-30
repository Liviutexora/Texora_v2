<x-app-layout>
    <div class="p-6">

        <h2 class="text-2xl font-semibold mb-6">Servicii</h2>

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- ADD SERVICE --}}
        <div class="bg-white p-4 rounded shadow mb-6">
            <h3 class="text-lg font-semibold mb-4">Adaugă serviciu</h3>

            <form method="POST" action="/services">
                @csrf

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm">Nume</label>
                        <input type="text" name="name" class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label class="block text-sm">Durată (minute)</label>
                        <input type="number" name="duration" class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label class="block text-sm">Preț (lei)</label>
                        <input type="number" step="0.01" name="price" class="w-full border rounded p-2">
                    </div>
                </div>

                <button class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Salvează
                </button>
            </form>
        </div>

        {{-- LIST SERVICES --}}
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Lista servicii</h3>

            <table class="w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border">Nume</th>
                        <th class="p-2 border">Durată</th>
                        <th class="p-2 border">Preț</th>
                        <th class="p-2 border text-center">Acțiuni</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td class="p-2 border">{{ $service->name }}</td>
                            <td class="p-2 border">{{ $service->duration }} min</td>
                            <td class="p-2 border">
                                {{ $service->price ? $service->price . ' lei' : '-' }}
                            </td>

<td class="p-2 border">
    <div class="flex justify-center gap-3">

        {{-- EDIT --}}
        <a href="#"
           class="bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1 rounded text-sm font-medium">
            Editează
        </a>

        {{-- DELETE --}}
        <form method="POST" action="/services/{{ $service->id }}"
              onsubmit="return confirm('Sigur ștergi?')">
            @csrf
            @method('DELETE')

            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                Șterge
            </button>
        </form>

    </div>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center p-4 text-gray-500">
                                Nu există servicii încă.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
