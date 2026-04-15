<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    // LIST + FORM
    public function index()
    {
        $businessId = Auth::user()->business_id;

        $employees = Employee::where('business_id', $businessId)
            ->with('services')
            ->get();

        $services = Service::where('business_id', $businessId)->get();

        return view('employees.index', compact('employees', 'services'));
    }

    // STORE
    public function store(Request $request)
    {
        $businessId = Auth::user()->business_id;

        $employee = Employee::create([
            'business_id' => $businessId,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'status' => $request->status ?? 'active',
        ]);

        // atașare servicii
        if ($request->has('services')) {
            $employee->services()->sync($request->services);
        }

        return redirect()->back()->with('success', 'Profesionist adăugat');
    }

    // DELETE
    public function destroy($id)
    {
        $employee = Employee::where('business_id', auth()->user()->business_id)
            ->findOrFail($id);
        $employee->delete();

        return redirect()->back()->with('success', 'Șters');
    }

    public function show($id)
    {
    $employee = Employee::where('business_id', auth()->user()->business_id)
        ->findOrFail($id);

        $workingHours = \App\Models\EmployeeWorkingHour::where('employee_id', $id)->get();

return view('employees.show', compact('employee', 'workingHours'));
    }
public function updateWorkingHours(Request $request, $id)

{
    $employee = Employee::where('business_id', auth()->user()->business_id)
        ->findOrFail($id);

    // Ștergem programul vechi
    DB::table('employee_working_hours')
        ->where('employee_id', $employee->id)
        ->delete();

    $days = $request->input('days', []);

  foreach ($days as $day => $data) {

    if (!empty($data['active']) && !empty($data['start']) && !empty($data['end'])) {
        DB::table('employee_working_hours')->insert([
            'employee_id' => $employee->id,
            'business_id' => auth()->user()->business_id,
            'day_of_week' => $day,
            'start_time' => $data['start'],
            'end_time' => $data['end'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }


    }

    return redirect()->back()->with('success', 'Program salvat cu succes');
}

}
