<x-app-layout>
    <div class="p-6 max-w-5xl mx-auto">

        <!-- HEADER -->
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">
                {{ $employee->first_name }} {{ $employee->last_name }}
            </h1>
            <p class="text-gray-500">Program de lucru</p>
        </div>

        <form method="POST" action="{{ route('employees.working-hours.update', $employee->id) }}">
            @csrf

            <div class="bg-white shadow rounded-lg overflow-hidden">

                <!-- HEADER TABLE -->
                <div class="grid grid-cols-4 gap-4 px-4 py-3 bg-gray-50 text-sm font-medium text-gray-600 border-b">
                    <div>Zi</div>
                    <div>Start</div>
                    <div>End</div>
                    <div class="text-right">Activ</div>
                </div>

                @php
                    $days = [
                        1 => 'Luni',
                        2 => 'Marți',
                        3 => 'Miercuri',
                        4 => 'Joi',
                        5 => 'Vineri',
                        6 => 'Sâmbătă',
                        0 => 'Duminică',
                    ];
                @endphp
@php
    $grouped = $workingHours->groupBy('day_of_week');
@endphp

                @foreach($days as $dayKey => $dayName)
                    <div class="grid grid-cols-4 gap-4 items-center px-4 py-3 border-b hover:bg-gray-50">

                        <!-- ZI -->
                        <div class="font-medium text-gray-700">
                            {{ $dayName }}
                        </div>

                        <!-- START -->
                        <div>
                            <input type="time"
       name="days[{{ $dayKey }}][0][start]"
       value="{{ $grouped[$dayKey][0]->start_time ?? '' }}"
       class="w-full border rounded px-2 py-1 focus:ring-2">
                        </div>

                        <!-- END -->
                        <div>
                            <input type="time"
       name="days[{{ $dayKey }}][0][end]"
       value="{{ $grouped[$dayKey][0]->end_time ?? '' }}"
       class="w-full border rounded px-2 py-1 focus:ring-2">
                        </div>

                        <!-- ACTIV -->
                        <div class="text-right">
                            <input type="checkbox"
                                   class="h-5 w-5 text-blue-600">
                        </div>

                    </div>
                @endforeach

            </div>

            <!-- BUTTON -->
            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
                    Salvează programul
                </button>
            </div>

        </form>
    </div>
</x-app-layout>