<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('business_id', auth()->user()->business_id)->get();

        return view('services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer',
            'price' => 'nullable|numeric',
        ]);

        Service::create([
            'name' => $request->name,
            'duration' => $request->duration,
            'price' => $request->price,
            'business_id' => auth()->user()->business_id,
        ]);

        return redirect()->back()->with('success', 'Serviciu adăugat');
    }

    public function destroy($id)
    {
        $service = Service::where('business_id', auth()->user()->business_id)
            ->findOrFail($id);

        $service->delete();

        return redirect()->back()->with('success', 'Serviciu șters');
    }
}
