<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\EmployeeWorkingHour;
use Carbon\Carbon;

class AppointmentService
{
    public function getAvailableSlots($employeeId, $serviceId, $date)
    {
        $service = Service::findOrFail($serviceId);
        $duration = $service->duration; // în minute

        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        $workingHours = EmployeeWorkingHour::where('employee_id', $employeeId)
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

            $overlap = Appointment::where('employee_id', $employeeId)
                ->whereDate('start_time', $date)
                ->where(function ($q) use ($slotStart, $slotEnd) {
                    $q->whereBetween('start_time', [$slotStart, $slotEnd])
                      ->orWhereBetween('end_time', [$slotStart, $slotEnd])
                      ->orWhere(function ($q2) use ($slotStart, $slotEnd) {
                          $q2->where('start_time', '<=', $slotStart)
                             ->where('end_time', '>=', $slotEnd);
                      });
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
    return \App\Models\Appointment::latest()->get();
}

}
