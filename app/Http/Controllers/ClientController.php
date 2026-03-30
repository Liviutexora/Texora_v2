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
        Client::create($request->all());
        return redirect()->back();
    }
}
