<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;

class BusinessController extends Controller
{
    public function store(Request $request)
    {
        $business = Business::create([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        auth()->user()->update([
            'business_id' => $business->id
        ]);
        return redirect()->back()->with('success', 'Business creat');
    }
}