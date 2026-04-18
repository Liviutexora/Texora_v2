<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Programări
        </h2>
    </x-slot>

    <div class="p-6">


        <form id="booking-form" method="POST" action="{{ route('appointments.store') }}" class="bg-white p-4 rounded shadow mb-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <select name="client_id" id="client_id" class="border rounded px-3 py-2" required>
                    <option value="">Selectează client</option>
                    @foreach(\App\Models\Client::all() as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>

                <select name="employee_id" id="employee_id" class="border rounded px-3 py-2" required>
                    <option value="">Selectează angajat</option>
                    @foreach(\App\Models\Employee::all() as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                    @endforeach
                </select>

                <select name="service_id" id="service_id" class="border rounded px-3 py-2" required>
                    <option value="">Selectează serviciu</option>
                </select>

                <input type="date" name="date" id="date" class="border rounded px-3 py-2" required>

                <input type="hidden" name="start_time" id="start_time">
            </div>

            <div class="mt-4">
                <button type="button" id="check-slots" class="bg-blue-500 text-white px-4 py-2 rounded">Verifică sloturi disponibile</button>
            </div>

            <div id="slots-container" class="mt-4 flex flex-wrap gap-2"></div>

            <div class="mt-4">
                <textarea name="notes" placeholder="Notițe" class="border rounded px-3 py-2 w-full"></textarea>
            </div>

            <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
                Salvează
            </button>
        </form>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const employeeSelect = document.getElementById('employee_id');
            const serviceSelect = document.getElementById('service_id');
            const slotsContainer = document.getElementById('slots-container');
            const dateInput = document.getElementById('date');
            const startTimeInput = document.getElementById('start_time');


            employeeSelect.addEventListener('change', function () {
                let employeeId = this.value;
                let serviceSelect = document.getElementById('service_id');

                // reset dropdown

                serviceSelect.innerHTML = '<option value="">Se încarcă...</option>';
                serviceSelect.disabled = true;

                if (!employeeId) {
                    serviceSelect.innerHTML = '<option value="">Selectează serviciu</option>';
                    return;
                }

                fetch(`/employees/${employeeId}/services`)
                    .then(response => response.json())
                    .then(data => {
                        serviceSelect.innerHTML = '<option value="">Selectează serviciu</option>';
                        serviceSelect.disabled = false;
                        data.forEach(service => {
                            let option = document.createElement('option');
                            option.value = service.id;
                            option.textContent = service.name + (service.duration ? ' (' + service.duration + ' min)' : '');
                            serviceSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading services:', error);
                        serviceSelect.innerHTML = '<option value="">Eroare la încărcare</option>';
                        serviceSelect.disabled = true;
                    });
            });


            function loadSlots() {
                const employeeId = employeeSelect.value;
                const serviceId = serviceSelect.value;
                const date = dateInput.value;

                if (!employeeId || !serviceId || !date) {
                    slotsContainer.innerHTML = '';
                    return;
                }

                slotsContainer.innerHTML = 'Se încarcă sloturi...';

                fetch(`/slots?employee_id=${employeeId}&service_id=${serviceId}&date=${date}`)
                    .then(res => res.json())
                    .then(slots => {
                        slotsContainer.innerHTML = '';

                        if (!slots.length) {
                            slotsContainer.innerHTML = '<span class="text-red-500">Nu există sloturi disponibile.</span>';
                            return;
                        }

                        slots.forEach(slot => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'slot-btn bg-green-200 hover:bg-green-400 px-3 py-1 rounded m-1';
                            btn.textContent = slot;

                            btn.onclick = function() {
                                startTimeInput.value = slot;
                                document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('bg-green-600', 'text-white'));
                                btn.classList.add('bg-green-600', 'text-white');
                            };

                            slotsContainer.appendChild(btn);
                        });
                    });
            }

            employeeSelect.addEventListener('change', loadSlots);
            serviceSelect.addEventListener('change', loadSlots);
            dateInput.addEventListener('change', loadSlots);
        });
        </script>

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
                            <td class="p-2">{{ $appointment->client->name ?? '-' }}</td>
                            <td class="p-2">{{ $appointment->service->name ?? '-' }}</td>
                            <td class="p-2">{{ \Carbon\Carbon::parse($appointment->start_time)->format('d M Y, H:i') }}</td>
                            <td class="p-2">{{ $appointment->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
