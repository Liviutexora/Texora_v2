<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function update(Request $request, $id)
    {
        $service = Service::where('business_id', auth()->user()->business_id)
            ->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer',
            'price' => 'nullable|numeric',
        ]);

        $service->update($data);

        return redirect()->back()->with('success', 'Serviciu actualizat');
    }
    public function index()
    {
        $services = Service::all();

        return view('services.index', compact('services'));
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer',
            'price' => 'nullable|numeric',
        ]);

        $data['business_id'] = auth()->user()->business_id;

        Service::create($data);

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
