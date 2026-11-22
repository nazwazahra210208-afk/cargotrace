<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use Illuminate\Http\Request;

class CarrierController extends Controller
{
    public function index()
    {
        $carriers = Carrier::all();
        return view('admin.carriers.index', compact('carriers'));
    }

    public function create()
    {
        return view('admin.carriers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:carriers,code|max:50',
            'contact_number' => 'nullable|string|max:50',
        ]);

        Carrier::create($request->all());

        return redirect()->route('admin.carriers.index')->with('success', 'Carrier berhasil ditambahkan.');
    }

    public function destroy(Carrier $carrier)
    {
        $carrier->delete();
        return redirect()->route('admin.carriers.index')->with('success', 'Carrier berhasil dihapus.');
    }
}