<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Pengaturan Bahasa (Default: id / Indonesia)
        $locale = $request->get('lang', session('locale', 'id'));
        session(['locale' => $locale]);

        $user = Auth::user();

        // Jika user yang login adalah Customer
        if ($user && $user->role === 'customer') {
            $totalShipments = Shipment::where('user_id', $user->id)->count();
            $inTransitCount = Shipment::where('user_id', $user->id)->where('status', 'In Transit')->count();
            $deliveredCount = Shipment::where('user_id', $user->id)->where('status', 'Delivered')->count();
            
            // Query riwayat kiriman milik customer dengan dukungan filter status
            $query = Shipment::where('user_id', $user->id);
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            $myShipments = $query->latest()->get();

            return view('customer.dashboard', compact(
                'totalShipments',
                'inTransitCount',
                'deliveredCount',
                'myShipments',
                'locale'
            ));
        }

        // Jika user adalah Operator atau Admin
        $totalShipments = Shipment::count();
        $inTransitCount = Shipment::where('status', 'In Transit')->count();
        $deliveredCount = Shipment::where('status', 'Delivered')->count();
        
        $query = Shipment::query();
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        $myShipments = $query->latest()->get();

        return view('operator.dashboard', compact(
            'totalShipments',
            'inTransitCount',
            'deliveredCount',
            'myShipments',
            'locale'
        ));
    }
}