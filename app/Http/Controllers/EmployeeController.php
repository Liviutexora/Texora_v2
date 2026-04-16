<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Service;
use App\Services\EmployeeWorkingHoursService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    protected $employeeWorkingHoursService;

    public function __construct(EmployeeWorkingHoursService $employeeWorkingHoursService)
    {
        $this->employeeWorkingHoursService = $employeeWorkingHoursService;
    }

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

    $this->employeeWorkingHoursService->update($employee, $request->days ?? []);

    return redirect()->back()->with('success', 'Program salvat cu succes');
}

}
