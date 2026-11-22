<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\AuditLog; // Sesuaikan dengan model audit log Abang jika ada

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalOperators = User::where('role', 'operator')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalCarriers = Carrier::count();
        $totalLocations = Location::count();
        $auditLogs = []; // Atau AuditLog::latest()->take(5)->get(); jika sudah ada tabelnya

        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalOperators', 
            'totalCustomers', 
            'totalCarriers', 
            'totalLocations', 
            'auditLogs'
        ));
    }

    public function changeLanguage($locale)
    {
        if (in_array($locale, ['id', 'en'])) {
            session(['locale' => $locale]);
        }
        return redirect()->back();
    }
}