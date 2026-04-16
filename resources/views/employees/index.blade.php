
<x-app-layout>

<div class="max-w-6xl mx-auto p-4">

    <h1 class="text-2xl font-bold mb-4">Profesioniști</h1>

    {{-- FORM ADD --}}
    <div class="bg-white p-4 rounded shadow mb-6">
        <form method="POST" action="{{ route('employees.store') }}">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="first_name" placeholder="Nume" class="border p-2 rounded" required>
                <input type="text" name="last_name" placeholder="Prenume" class="border p-2 rounded" required>

                <input type="text" name="phone" placeholder="Telefon" class="border p-2 rounded">
                <input type="email" name="email" placeholder="Email" class="border p-2 rounded">

                <select name="status" class="border p-2 rounded">
                    <option value="active">Activ</option>
                    <option value="inactive">Inactiv</option>
                    <option value="vacation">Vacanță</option>
                </select>
            </div>

            {{-- SERVICII --}}
            <div class="mt-4">
                <h3 class="font-semibold mb-2">Servicii</h3>

                <div class="grid grid-cols-3 gap-2">
                    @foreach($services as $service)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="services[]" value="{{ $service->id }}">
                            <span>{{ $service->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
                Salvează
            </button>
        </form>
    </div>

    {{-- LISTĂ --}}
    <div class="bg-white p-4 rounded shadow">
        <table class="w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2">Nume</th>
                    <th class="p-2">Telefon</th>
                    <th class="p-2">Email</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Servicii</th>
                    <th class="p-2">Acțiuni</th>
                </tr>
            </thead>

            <tbody>
                @foreach($employees as $employee)
                    <tr class="border-t">
                        <td class="p-2">{{ $employee->full_name }}</td>
                        <td class="p-2">{{ $employee->phone }}</td>
                        <td class="p-2">{{ $employee->email }}</td>
                        <td class="p-2">{{ $employee->status }}</td>
                        <td class="p-2">
                            @foreach($employee->services as $s)
                                <span class="text-sm bg-gray-200 px-3 py-1 rounded">
                                    {{ $s->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="p-2">
                            <a href="{{ route('employees.show', $employee->id) }}" class="bg-emerald-600 text-white px-2 py-1 rounded inline-block hover:bg-emerald-700">
                                Profil
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

</x-app-layout>
