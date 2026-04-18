<x-profile-layout 
    title="{{ $employee->first_name }} {{ $employee->last_name }}"
    subtitle="Activ"
>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('warning') }}
        </div>
    @endif

    <div class="text-sm text-gray-500 mb-2">
        <a href="{{ route('employees.index') }}" class="hover:text-gray-700">
            Angajați
        </a>
        <span class="mx-1">/</span>
        <span class="text-gray-700">
            {{ $employee->first_name }} {{ $employee->last_name }}
        </span>
    </div>

    {{-- ACTION BUTTON --}}
    <x-slot:action>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm text-sm font-medium">
            Editează profil
        </button>
    </x-slot:action>

    {{-- SIDEBAR --}}
    <x-slot:sidebar>
        <div class="bg-white rounded-2xl shadow p-5">
            <img src="https://i.pravatar.cc/300" class="rounded-xl mb-4" />

            <h2 class="font-semibold text-lg mb-1">
                {{ $employee->first_name }} {{ $employee->last_name }}
            </h2>

            <p class="text-sm text-gray-500 mb-4">0731 456 789</p>

            <div class="text-sm text-gray-600">
                <p>📍 Strada Exemplu, nr. 12</p>
                <p>București, RO</p>
            </div>
        </div>
    </x-slot:sidebar>

    {{-- MAIN CONTENT --}}
    <div class="bg-white rounded-2xl shadow mb-8">

        {{-- TABS --}}
        <div class="border-b px-6 pt-4 flex gap-6 text-sm">
            <button class="tab-btn pb-2 border-b-2 border-blue-600" data-tab="info">Informații</button>
            <button class="tab-btn pb-2 text-gray-500" data-tab="schedule">Program de lucru</button>
            <button class="tab-btn pb-2 text-gray-500" data-tab="appointments">Programări</button>
            <button class="tab-btn pb-2 text-gray-500" data-tab="stats">Statistici</button>
        </div>

        {{-- CONTENT --}}
        <div class="p-6">

            {{-- INFO --}}
            <div class="tab-content" id="tab-info">
                <h3 class="font-semibold mb-4">Informații</h3>

                <div class="space-y-2 text-sm text-gray-600">
                    <div><span class="text-gray-500">Nume:</span> {{ $employee->first_name }} {{ $employee->last_name }}</div>
                    <div><span class="text-gray-500">Telefon:</span> +40 731 456 789</div>
                    <div><span class="text-gray-500">Email:</span> test@email.com</div>
                    <div><span class="text-gray-500">Adresă:</span> Strada Exemplu, nr. 12</div>
                </div>
            </div>

{{-- SCHEDULE --}}
<div class="tab-content hidden" id="tab-schedule">
    <form method="POST" action="{{ route('employees.working-hours.update', $employee->id) }}">
        @csrf

        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold">Program de lucru</h3>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Salvează
            </button>
        </div>

        @php
            $days = ['Luni','Marți','Miercuri','Joi','Vineri','Sâmbătă','Duminică'];
            $workingHoursByDay = $workingHours->groupBy('day_of_week');
        @endphp

        <div class="space-y-3">
            @foreach($days as $index => $day)

                @php
                    $intervals = $workingHoursByDay[$index] ?? collect();
                @endphp

                <div class="border rounded-xl px-4 py-3">

                    <div class="flex items-center gap-3 mb-3">
                        <input type="checkbox"
                               name="days[{{ $index }}][active]"
                               {{ $intervals->count() ? 'checked' : '' }}>

                        <span class="font-medium">{{ $day }}</span>
                    </div>

                    <div class="intervals flex flex-wrap items-center gap-2">
                        @foreach($intervals as $i => $wh)
                            <div class="interval-row flex items-center gap-2">

                                <input type="time"
                                       name="days[{{ $index }}][{{ $i }}][start]"
                                       value="{{ substr($wh->start_time, 0, 5) }}"
                                       class="border rounded px-2 py-1"
                                       step="60">

                                <input type="time"
                                       name="days[{{ $index }}][{{ $i }}][end]"
                                       value="{{ substr($wh->end_time, 0, 5) }}"
                                       class="border rounded px-2 py-1"
                                       step="60">

                                <button type="button" class="remove-interval text-red-500 bg-red-100 hover:bg-red-200 rounded-full w-7 h-7 flex items-center justify-center">✕</button>

                            </div>
                        @endforeach
                    </div>

                    <button type="button"
                            class="add-interval text-blue-600 text-lg"
                            data-day="{{ $index }}">
                        +
                    </button>

                </div>
            @endforeach
        </div>

    </form>
</div>
            </div>

            {{-- APPOINTMENTS --}}
            <div class="tab-content hidden" id="tab-appointments">
                <p class="text-gray-500">Programările vor apărea aici</p>
            </div>

            {{-- STATS --}}
            <div class="tab-content hidden" id="tab-stats">
                <p class="text-gray-500">Statistici în curând</p>
            </div>

        </div>
    </div>

    {{-- TAB SCRIPT --}}
    <script>
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {

                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('border-blue-600');
                    b.classList.add('text-gray-500');
                });

                btn.classList.add('border-blue-600');
                btn.classList.remove('text-gray-500');

                document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));

                document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
            });
        });
    </script>

    {{-- ADD INTERVAL SCRIPT --}}
    <script>
        document.querySelectorAll('.add-interval').forEach(button => {

            button.addEventListener('click', function () {

                const day = this.dataset.day;

                const parent = this.closest('div.border.rounded-xl');
                const container = parent.querySelector('.intervals');

                const index = container.children.length;

                const row = document.createElement('div');
                row.classList.add('interval-row', 'flex', 'items-center', 'gap-2');

                row.innerHTML = `
                    <input type="time" name="days[${day}][${index}][start]" class="border rounded px-2 py-1" step="60">
                    <input type="time" name="days[${day}][${index}][end]" class="border rounded px-2 py-1" step="60">
                    <button type="button" class="remove-interval text-red-500 bg-red-100 hover:bg-red-200 rounded-full w-7 h-7 flex items-center justify-center">✕</button>
                `;

                container.appendChild(row);
            });

        });
    </script>

    <script>
        document.addEventListener('click', function (event) {
            const button = event.target.closest('.remove-interval');
            if (!button) {
                return;
            }

            const row = button.closest('.interval-row');
            if (row) {
                row.remove();
            }
        });
    </script>

</x-profile-layout>