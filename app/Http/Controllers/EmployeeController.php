<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Service;
use App\Services\EmployeeWorkingHoursService;
use App\Http\Requests\UpdateEmployeeWorkingHoursRequest;
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
        $employees = Employee::with('services')->get();

        $services = Service::all();

        return view('employees.index', compact('employees', 'services'));
    }

    // STORE
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'status' => 'nullable|in:active,inactive',
            'services' => 'nullable|array',
            'services.*' => [
                'exists:services,id',
                function ($attribute, $value, $fail) {
                    if (!\App\Models\Service::where('id', $value)
                        ->where('business_id', auth()->user()->business_id)
                        ->exists()) {
                        $fail('Serviciu invalid pentru acest business.');
                    }
                }
            ],
        ]);

        $data['business_id'] = auth()->user()->business_id;

        $employee = Employee::create($data);

        if (!empty($data['services'])) {
            $employee->services()->syncWithPivotValues(
                $data['services'],
                ['business_id' => auth()->user()->business_id]
            );
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


        $workingHours = $employee->workingHours;

        return view('employees.show', compact('employee', 'workingHours'));
    }
public function updateWorkingHours(UpdateEmployeeWorkingHoursRequest $request, $id)
{
    $employee = Employee::where('business_id', auth()->user()->business_id)
        ->findOrFail($id);

    $result = $this->employeeWorkingHoursService->update($employee, $request->validated()['days'] ?? []);

    if (!empty($result['has_warnings'])) {
        return redirect()->back()->with('warning', 'Unele intervale au fost ignorate deoarece sunt invalide sau se suprapun.');
    }

    return redirect()->back()->with('success', 'Programul de lucru a fost salvat cu succes.');
}

}
