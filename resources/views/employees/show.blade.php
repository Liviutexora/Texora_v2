<!-- resources/views/employees/show.blade.php -->
<x-app-layout>

<div class="max-w-7xl mx-auto py-8 px-6">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-6">
        

            <div>
                <h1 class="text-2xl font-semibold text-gray-800">
                    {{ $employee->first_name }} {{ $employee->last_name }}
                </h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                    <span class="text-sm text-gray-600">Activ</span>
                </div>
            </div>
        </div>

        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm text-sm font-medium">
            Editează profil
        </button>
    </div>

    <div class="grid grid-cols-12 gap-6">

        <!-- SIDEBAR -->
        <div class="col-span-3">
            <div class="bg-white rounded-2xl shadow p-5">
                <img src="https://i.pravatar.cc/300" class="w-32 h-32 object-cover rounded-xl mb-3" />

                <h2 class="font-semibold text-lg mb-1">
                    {{ $employee->first_name }} {{ $employee->last_name }}
                </h2>

                <p class="text-sm text-gray-500 mb-4">0731 456 789</p>

                <div class="text-sm text-gray-600">
                    <p>📍 Strada Exemplu, nr. 12</p>
                    <p>București, RO</p>
                </div>
            </div>
        </div>

        <!-- MAIN -->
        <div class="col-span-9">

            <!-- TABS -->
            <div class="bg-white rounded-2xl shadow mb-4">
                <div class="flex border-b">
                    <button onclick="switchTab('info')" id="tab-info" class="tab-btn active">Informații</button>
                    <button onclick="switchTab('schedule')" id="tab-schedule" class="tab-btn">Program de lucru</button>
                    <button onclick="switchTab('appointments')" id="tab-appointments" class="tab-btn">Programări</button>
                    <button onclick="switchTab('stats')" id="tab-stats" class="tab-btn">Statistici</button>
                </div>

                <div class="p-6">

                    <!-- INFO TAB -->
                    <div id="content-info" class="tab-content">
                        <h3 class="font-semibold mb-4">Informații</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><span class="text-gray-500">Nume</span><br>{{ $employee->first_name }} {{ $employee->last_name }}</div>
                            <div><span class="text-gray-500">Telefon</span><br>+40 731 456 789</div>
                            <div><span class="text-gray-500">Email</span><br>andrei@example.com</div>
                            <div><span class="text-gray-500">Adresă</span><br>Strada Exemplu</div>
                        </div>
                    </div>

                    <!-- SCHEDULE TAB -->
                    <div id="content-schedule" class="tab-content hidden">
                        <div class="flex items-center justify-between mb-4">
    <h3 class="font-semibold">Program de lucru</h3>

    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm text-sm font-medium">
    Salvează
</button>
</div>

                        @php
                            $days = ['Luni','Marți','Miercuri','Joi','Vineri','Sâmbătă','Duminică'];
                        @endphp

                        <div class="space-y-3">
                            @foreach($days as $day)
                                <div class="flex items-center justify-between border rounded-xl px-4 py-3">

                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" class="toggle-day">
                                        <span class="font-medium">{{ $day }}</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <div class="flex gap-2 intervals">
                                            <div class="flex gap-2">
                                                <input type="time" class="input-time">
                                                <input type="time" class="input-time">
                                            </div>
                                        </div>

                                        <button type="button" onclick="addInterval(this)" class="text-indigo-600 font-bold text-lg">+</button>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- APPOINTMENTS TAB -->
                    <div id="content-appointments" class="tab-content hidden">
                        <p class="text-gray-500">Programările vor apărea aici.</p>
                    </div>

                    <!-- STATS TAB -->
                    <div id="content-stats" class="tab-content hidden">
                        <p class="text-gray-500">Statistici în curând.</p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<style>
.tab-btn {
    padding: 12px 20px;
    font-size: 14px;
    color: #6b7280;
}
.tab-btn.active {
    border-bottom: 2px solid #4f46e5;
    color: #111827;
    font-weight: 600;
}
.tab-content.hidden { display:none; }
.input-time {
    border:1px solid #e5e7eb;
    border-radius:8px;
    padding:6px 10px;
}
</style>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

    document.getElementById('content-' + tab).classList.remove('hidden');
    document.getElementById('tab-' + tab).classList.add('active');
}

function addInterval(btn) {
    let container = btn.parentElement.querySelector('.intervals');

    let div = document.createElement('div');
    div.classList.add('flex','gap-2','mt-1');

    div.innerHTML = `
        <input type="time" class="input-time">
        <input type="time" class="input-time">
    `;

    container.appendChild(div);
}
</script>

</x-app-layout>
