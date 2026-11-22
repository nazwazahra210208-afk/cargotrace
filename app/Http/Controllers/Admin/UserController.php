<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        $title = "Daftar Pengguna Sistem";

        // Jika ada filter role dari klik kartu dashboard (misal: ?role=operator atau ?role=customer)
        if ($request->has('role') && !empty($request->role)) {
            $role = $request->get('role');
            $query->where('role', $role);

            if ($role == 'operator') {
                $title = "Kelola Operator Logistik";
            } elseif ($role == 'customer') {
                $title = "Kelola Customer Aktif";
            } elseif ($role == 'admin') {
                $title = "Kelola Administrator";
            }
        }

        $users = $query->latest()->get();

        return view('admin.users.index', compact('users', 'title'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,operator,customer',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}