<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::latest()->get();
        return view('clients', compact('clients'));
    }

    public function store(Request $request)
    {
        Client::create([
    'business_id' => auth()->user()->business_id,
    'name' => $request->name,
    'phone' => $request->phone,
    'email' => $request->email,
]);
        return redirect()->back();
    }
    public function destroy($id)
{
    $client = Client::where('id', $id)
        ->where('business_id', auth()->user()->business_id)
        ->firstOrFail();

    $client->delete();

    return redirect()->back();
}
}
