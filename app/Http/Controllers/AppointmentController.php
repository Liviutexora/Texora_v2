<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppointmentService;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function index()
    {
     $appointments = \App\Models\Appointment::latest()->get();

return view('appointments.index', compact('appointments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_name' => 'required',
            'appointment_time' => 'required|date',
            'service' => 'nullable',
            'notes' => 'nullable',
        ]);
        $data['business_id'] = auth()->user()->business_id;

        $this->appointmentService->create($data);

        return redirect()->back()->with('success', 'Programare adăugată');
    }

public function getSlots()
{
    $employeeId = request('employee_id');
    $serviceId = request('service_id');
    $date = request('date');

    $slots = app(\App\Services\AppointmentService::class)
        ->getAvailableSlots($employeeId, $serviceId, $date);

    return response()->json($slots);
}
}
