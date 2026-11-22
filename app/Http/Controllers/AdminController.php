<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\AuditLog;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalOperators = User::where('role', 'operator')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalCarriers = Carrier::count();
        $totalLocations = Location::count();

        $auditLogs = collect();
        if (Schema::hasTable('audit_logs')) {
            $auditLogs = AuditLog::with('user')->latest()->take(15)->get();
        }

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