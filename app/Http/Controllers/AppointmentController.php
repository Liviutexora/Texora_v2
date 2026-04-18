<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppointmentService;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function index()
    {
        $appointments = \App\Models\Appointment::with(['employee', 'client', 'service'])
            ->latest()
            ->get();

        return view('appointments.index', compact('appointments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => [
                'required',
                Rule::exists('employees', 'id')
                    ->where('business_id', auth()->user()->business_id),
            ],
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')
                    ->where('business_id', auth()->user()->business_id),
            ],
            'service_id' => [
                'required',
                Rule::exists('services', 'id')
                    ->where('business_id', auth()->user()->business_id),
            ],
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'notes' => 'nullable|string',
        ]);

        $data['business_id'] = auth()->user()->business_id;

        try {
            $this->appointmentService->create($data);
            return redirect()->back()->with('success', 'Programare creată cu succes');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Slotul nu mai este disponibil. Te rugăm să alegi alt interval.');
        }

    }

    public function destroy($id)
    {
        $appointment = \App\Models\Appointment::where('business_id', auth()->user()->business_id)
            ->findOrFail($id);

        $appointment->delete();

        return redirect()->back()->with('success', 'Programare ștearsă');
    }

    public function update(Request $request, $id)
    {
        $appointment = \App\Models\Appointment::where('business_id', auth()->user()->business_id)
            ->findOrFail($id);

        $data = $request->validate([
            'employee_id' => [
                'required',
                Rule::exists('employees', 'id')
                    ->where('business_id', auth()->user()->business_id),
            ],
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')
                    ->where('business_id', auth()->user()->business_id),
            ],
            'service_id' => [
                'required',
                Rule::exists('services', 'id')
                    ->where('business_id', auth()->user()->business_id),
            ],
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'notes' => 'nullable|string',
        ]);

        $data['business_id'] = auth()->user()->business_id;

        $this->appointmentService->update($appointment, $data);

        return redirect()->back()->with('success', 'Programare actualizată');
    }
    public function getSlots()
    {
        $data = request()->validate([
            'employee_id' => [
                'required',
                Rule::exists('employees', 'id')
                    ->where('business_id', auth()->user()->business_id),
            ],
            'service_id' => [
                'required',
                Rule::exists('services', 'id')
                    ->where('business_id', auth()->user()->business_id),
            ],
            'date' => 'required|date',
        ]);

        $employeeId = $data['employee_id'];
        $serviceId = $data['service_id'];
        $date = $data['date'];

        $slots = $this->appointmentService
            ->getAvailableSlots($employeeId, $serviceId, $date);

        return response()->json($slots);
    }
}
