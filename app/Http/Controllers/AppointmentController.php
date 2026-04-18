<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

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
            'date' => 'required|date',
            'start_time' => 'required',
            'notes' => 'nullable|string',
        ]);

        $service = \App\Models\Service::findOrFail($data['service_id']);
        // build full datetime
        $start = Carbon::parse($data['date'] . ' ' . $data['start_time']);
        // calculate end time
        $end = (clone $start)->addMinutes($service->duration);
        // override values
        $data['start_time'] = $start;
        $data['end_time'] = $end;

        $data['business_id'] = auth()->user()->business_id;



        if (strlen($data['start_time']) <= 5) {
            $data['start_time'] = \Carbon\Carbon::parse($data['date'] . ' ' . $data['start_time']);
        } else {
            $data['start_time'] = \Carbon\Carbon::parse($data['start_time']);
        }

        unset($data['end_time']);


        try {
            $this->appointmentService->createAppointmentSafely($data);
            return redirect()->back()->with('success', 'Programare creată cu succes');
        } catch (\Exception $e) {
            dd($e->getMessage());
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
            'start_time' => 'required',
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
