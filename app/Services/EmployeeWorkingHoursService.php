<?php

namespace App\Services;

use App\Models\EmployeeWorkingHour;

class EmployeeWorkingHoursService
{
    public function update($employee, $days)
    {
        EmployeeWorkingHour::where('employee_id', $employee->id)->delete();

        foreach ($days as $dayIndex => $intervals) {
            $validIntervals = [];

            foreach ($intervals as $interval) {
                if (empty($interval['start']) || empty($interval['end'])) {
                    continue;
                }

                if ($interval['start'] >= $interval['end']) {
                    continue;
                }

                $validIntervals[] = [
                    'start' => $interval['start'],
                    'end' => $interval['end'],
                ];
            }

            usort($validIntervals, function ($a, $b) {
                return strcmp($a['start'], $b['start']);
            });

            $previousEnd = null;

            foreach ($validIntervals as $interval) {
                if ($previousEnd !== null && $interval['start'] < $previousEnd) {
                    continue;
                }

                EmployeeWorkingHour::create([
                    'employee_id' => $employee->id,
                    'business_id' => $employee->business_id,
                    'day_of_week' => $dayIndex,
                    'start_time' => $interval['start'],
                    'end_time' => $interval['end'],
                ]);

                $previousEnd = $interval['end'];
            }
        }
    }
}
