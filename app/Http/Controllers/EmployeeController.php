<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $employee = Employee::where('id', $id)
    ->where('business_id', auth()->user()->business_id)
    ->firstOrFail();

        $employee->delete();

        return redirect()->back()->with('success', 'Șters');
    }
}
