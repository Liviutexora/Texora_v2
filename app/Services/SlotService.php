<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\EmployeeWorkingHour;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Employee;

class SlotService
{
    private int $interval = 15;
    private function buildIntervals(array $slots, int $duration): array
    {
        $intervals = [];

        foreach ($slots as $slot) {
            $start = $slot->copy();
            $end = $slot->copy()->addMinutes($duration);

            $intervals[] = [
                'start' => $start->format('H:i'),
                'end' => $end->format('H:i'),
            ];
        }

        return $intervals;
    }

    private function filterByServiceDuration(array $slots, $workingHours, int $duration, string $date): array
    {
        $filtered = [];
        foreach ($slots as $slot) {
            $slotStart = $slot;
            $slotEnd = $slot->copy()->addMinutes($duration);
            $valid = false;
            foreach ($workingHours as $workingHour) {
                $workingStart = Carbon::parse($date . ' ' . $workingHour->start_time);
                $workingEnd = Carbon::parse($date . ' ' . $workingHour->end_time);
                if ($slotStart->gte($workingStart) && $slotEnd->lte($workingEnd)) {
                    $valid = true;
                    break;
                }
            }
            if ($valid) {
                $filtered[] = $slot;
            }
        }
        return $filtered;
    }

    public function getAvailableSlots(int $employeeId, int $serviceId, string $date): array
    {
        $employee = Employee::where('id', $employeeId)
            ->where('business_id', auth()->user()->business_id)
            ->firstOrFail();
        $businessId = $employee->business_id;
        $dayOfWeek = Carbon::parse($date)->dayOfWeekIso - 1;
        $workingHours = EmployeeWorkingHour::where('employee_id', $employeeId)
            ->where('business_id', $businessId)
            ->where('day_of_week', $dayOfWeek)
            ->get();

        if ($workingHours->isEmpty()) {
            return [];
        }

        $slots = [];

        foreach ($workingHours as $workingHour) {
            $start = Carbon::parse($date . ' ' . $workingHour->start_time);
            $end = Carbon::parse($date . ' ' . $workingHour->end_time);

            $newSlots = $this->generateSlots($start, $end, $this->interval);

            foreach ($newSlots as $s) {
                $slots[] = $s;
            }
        }

        // Fetch appointments for the employee, business, and date
        $appointments = Appointment::where('employee_id', $employeeId)
            ->where('business_id', $businessId)
            ->whereDate('appointment_date', $date)
            ->get();

        // Remove overlapping slots
        $slots = $this->removeOverlappingSlots($slots, $appointments, $date);

        // Fetch service and get duration
        $service = Service::where('id', $serviceId)
            ->where('business_id', $businessId)
            ->firstOrFail();

        $serviceDuration = $service->duration;

        // Filter slots by service duration and working intervals
        $slots = $this->filterByServiceDuration($slots, $workingHours, $serviceDuration, $date);

        $slots = $this->buildIntervals($slots, $serviceDuration);

        // Apply booking strategy
        $strategy = 'full_day'; // temporary, will come from DB later

        if ($strategy === 'earliest_only' && count($slots) > 0) {
            return [$slots[0]];
        }

        if ($strategy === 'compact') {
            $compactSlots = [];

            for ($i = 0; $i < count($slots); $i++) {
                $current = $slots[$i];

                $hasNeighbor =
                    ($i > 0 && $current['start'] === $slots[$i - 1]['end']) ||
                    ($i < count($slots) - 1 && $current['end'] === $slots[$i + 1]['start']);

                if ($hasNeighbor) {
                    $compactSlots[] = $current;
                }
            }

            if (count($compactSlots) === 0 && count($slots) > 0) {
                return [$slots[0]];
            }

            return $compactSlots;
        }

        return $slots;
        }

    private function generateSlots(Carbon $start, Carbon $end, int $interval): array
    {
        $slots = [];
        $current = $start->copy();

        while ($current->lt($end)) {
            $slots[] = $current->copy();
            $current->addMinutes($interval);
        }

        return $slots;
    }

    private function removeOverlappingSlots(array $slots, $appointments, string $date): array
    {
        $filtered = [];

        foreach ($slots as $slot) {
            $slotStart = $slot->copy();
            $slotEnd = $slot->copy()->addMinutes($this->interval);
            $overlap = false;

            foreach ($appointments as $appointment) {
                $appointmentStart = Carbon::parse($date . ' ' . $appointment->start_time);
                $appointmentEnd = Carbon::parse($date . ' ' . $appointment->end_time);

                if ($slotStart->lt($appointmentEnd) && $slotEnd->gt($appointmentStart)) {
                    $overlap = true;
                    break;
                }
            }

            if (!$overlap) {
                $filtered[] = $slot;
            }
        }

        return $filtered;
    }
}
