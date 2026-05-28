<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManajemenAkunGriyaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query  = User::role(['admin', 'superadmin'])->latest();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->paginate(10)->withQueryString();
        return view('pages.superadmin.akun-griya.index', compact('users', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role'     => 'required|in:admin,superadmin',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);
        $user->assignRole($data['role']);

        return back()->with('success', 'Akun berhasil dibuat.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,superadmin',
        ]);

        $user->update(['name' => $data['name'], 'email' => $data['email']]);
        $user->syncRoles([$data['role']]);

        return back()->with('success', 'Akun berhasil diperbarui.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun berhasil {$status}.");
    }
}
