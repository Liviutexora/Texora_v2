<?php
namespace App\Services;


use App\Models\Employee;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\EmployeeWorkingHour;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    private function validateNotInPast(array $data): void
    {
        $start = $data['start_time'] instanceof Carbon
            ? $data['start_time']
            : Carbon::parse($data['date'] . ' ' . $data['start_time']);
        if ($start->isPast()) {
            throw new \Exception('Nu poți crea programări în trecut.');
        }
    }

    private function validateEmployeeService(array $data): void
    {
        $employee = \App\Models\Employee::find($data['employee_id']);
        if (!$employee) {
            throw new \Exception('Angajatul nu există.');
        }
        $hasService = $employee->services()
            ->where('services.id', $data['service_id'])
            ->exists();
        if (!$hasService) {
            throw new \Exception('Angajatul nu oferă acest serviciu.');
        }
    }

    private function validateWorkingHours(array $data): void
    {
        $employeeId = $data['employee_id'];
        $service = \App\Models\Service::findOrFail($data['service_id']);

        $start = $data['start_time'] instanceof Carbon
            ? $data['start_time']
            : Carbon::parse($data['date'] . ' ' . $data['start_time']);
        $end = $start->copy()->addMinutes($service->duration);

        $dayOfWeek = $start->dayOfWeek;
        $businessId = auth()->user()->business_id;

        $workingHours = \App\Models\EmployeeWorkingHour::where('business_id', $businessId)
            ->where('employee_id', $employeeId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$workingHours) {
            throw new \Exception('Angajatul nu lucrează în această zi.');
        }

        $workStart = \Carbon\Carbon::parse($data['date'] . ' ' . $workingHours->start_time);
        $workEnd = \Carbon\Carbon::parse($data['date'] . ' ' . $workingHours->end_time);

        if ($start < $workStart || $end > $workEnd) {
            throw new \Exception('Programarea este în afara programului de lucru.');
        }
    }
    public function createAppointmentSafely(array $data)
    {
        return DB::transaction(function () use ($data) {
            $this->validateNotInPast($data);
            $this->validateEmployeeService($data);
            $this->validateWorkingHours($data);

            if (!$data['start_time'] instanceof \Carbon\Carbon) {
                $data['start_time'] = \Carbon\Carbon::parse($data['start_time']);
            }

            $service = Service::findOrFail($data['service_id']);
            $data['end_time'] = (clone $data['start_time'])->addMinutes($service->duration);

            $businessId = auth()->user()->business_id;
            DB::table('appointments')
                ->where('business_id', $businessId)
                ->where('employee_id', $data['employee_id'])
                ->whereDate('start_time', $data['date'])
                ->lockForUpdate()
                ->get();

            $isAvailable = $this->isSlotAvailable(
                $data['employee_id'],
                $data['date'],
                $data['start_time'],
                $data['service_id']
            );

            if (!$isAvailable) {
                throw new \Exception('Slot is no longer available');
            }

            return Appointment::create([
                'business_id' => auth()->user()->business_id,
                'employee_id' => $data['employee_id'],
                'client_id' => $data['client_id'],
                'service_id' => $data['service_id'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
            ]);
        });
    }

    public function isSlotAvailable(int $employeeId, string $date, $startTime, int $serviceId): bool
    {
        $service = Service::findOrFail($serviceId);

        $start = $startTime instanceof Carbon
            ? $startTime
            : Carbon::parse($date . ' ' . $startTime);
        $end = $start->copy()->addMinutes($service->duration);

        $businessId = auth()->user()->business_id;

        $overlap = Appointment::where('business_id', $businessId)
            ->where('employee_id', $employeeId)
            ->whereDate('start_time', $date)
            ->where(function ($q) use ($start, $end) {
                $q->where('start_time', '<', $end)
                  ->where('end_time', '>', $start);
            })
            ->exists();

        return !$overlap;
    }

    public function getAvailableSlots($employeeId, $serviceId, $date)
    {
        $service = Service::findOrFail($serviceId);
        $duration = $service->duration; // în minute

        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $businessId = auth()->user()->business_id;

        $workingHours = EmployeeWorkingHour::where('business_id', $businessId)
            ->where('employee_id', $employeeId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$workingHours) {
            return [];
        }

        $start = Carbon::parse($date . ' ' . $workingHours->start_time);
        $end = Carbon::parse($date . ' ' . $workingHours->end_time);

        $slots = [];

        while ($start->copy()->addMinutes($duration) <= $end) {
            $slotStart = $start->copy();
            $slotEnd = $start->copy()->addMinutes($duration);

            $overlap = Appointment::where('business_id', $businessId)
                ->where('employee_id', $employeeId)
                ->whereDate('start_time', $date)
                ->where(function ($q) use ($slotStart, $slotEnd) {
                    $q->where('start_time', '<', $slotEnd)
                      ->where('end_time', '>', $slotStart);
                })
                ->exists();

            if (!$overlap) {
                $slots[] = $slotStart->format('H:i');
            }

            $start->addMinutes($duration);
        }

        return $slots;
    }

    public function getAll()
    {
        return \App\Models\Appointment::with(['client', 'service'])
            ->where('business_id', auth()->user()->business_id)
            ->latest()
            ->get();
    }
}
