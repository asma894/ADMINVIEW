<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::where('role', 'user')
            ->where('approval_status', 'approved')
            ->latest()
            ->paginate(15);

        return view('pages.users.index', ['title' => 'User Management', 'users' => $users]);
    }

    public function create(): View
    {
        return view('pages.users.create', ['title' => 'Add User']);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fname'    => ['required', 'string', 'max:100'],
            'lname'    => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'phone'    => ['nullable', 'string', 'max:30'],
            'bio'      => ['nullable', 'string', 'max:255'],
        ]);

        User::create([
            'name'            => trim($validated['fname'] . ' ' . $validated['lname']),
            'email'           => $validated['email'],
            'password'        => Hash::make($validated['password']),
            'phone'           => $validated['phone'] ?? null,
            'bio'             => $validated['bio'] ?? null,
            'role'            => 'user',
            'status'          => 'active',
            'approval_status' => 'approved',
            'approved_at'     => now(),
            'approved_by'     => auth()->id(),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('pages.users.edit', ['title' => 'Edit User', 'editUser' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'fname' => ['required', 'string', 'max:100'],
            'lname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio'   => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'name'  => trim($validated['fname'] . ' ' . $validated['lname']),
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'bio'   => $validated['bio'] ?? null,
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        AdminNotification::create([
            'triggered_by' => $user->id,
            'type'         => 'account_' . $newStatus,
            'message'      => "{$user->name}'s account was {$newStatus}d by " . auth()->user()->name . '.',
        ]);

        return back()->with('success', "User {$newStatus}d successfully.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $name = $user->name;
        $user->delete();
        return redirect()->route('users.index')->with('success', "User \"{$name}\" deleted.");
    }
}