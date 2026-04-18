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
    public function createAppointmentSafely(array $data)
    {
        return DB::transaction(function () use ($data) {
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

            $service = Service::findOrFail($data['service_id']);

            $start = Carbon::parse($data['date'] . ' ' . $data['start_time']);
            $end = $start->copy()->addMinutes($service->duration);

            return Appointment::create([
                'business_id' => auth()->user()->business_id,
                'employee_id' => $data['employee_id'],
                'client_id' => $data['client_id'],
                'service_id' => $data['service_id'],
                'start_time' => $start,
                'end_time' => $end,
            ]);
        });
    }

    public function isSlotAvailable(int $employeeId, string $date, string $startTime, int $serviceId): bool
    {
        $service = Service::findOrFail($serviceId);

        $start = Carbon::parse($date . ' ' . $startTime);
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
        return \App\Models\Appointment::where('business_id', auth()->user()->business_id)
            ->latest()
            ->get();
    }
}
