<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminManagementController extends Controller
{
    public function index(): View
    {
        $pendingUsers = User::where('approval_status', 'pending')->latest()->get();

        $admins = User::where('role', 'superadmin')
            ->where('approval_status', 'approved')
            ->latest()
            ->paginate(15);

        return view('pages.admin-management.index', [
            'title'        => 'Admin Management',
            'pendingUsers' => $pendingUsers,
            'admins'       => $admins,
        ]);
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        $request->validate(['role' => ['required', 'in:user,superadmin']]);

        $user->update([
            'approval_status' => 'approved',
            'role'            => $request->role,
            'status'          => 'active',
            'approved_at'     => now(),
            'approved_by'     => auth()->id(),
        ]);

        AdminNotification::create([
            'triggered_by' => $user->id,
            'type'         => 'account_approved',
            'message'      => "{$user->name} was approved as " . ucfirst($request->role) . '.',
        ]);

        return back()->with('success', "{$user->name} approved successfully.");
    }

    public function reject(User $user): RedirectResponse
    {
        $name  = $user->name;
        $email = $user->email;

        // Create notification before deleting so triggered_by FK is valid
        AdminNotification::create([
            'triggered_by' => $user->id,
            'type'         => 'account_rejected',
            'message'      => "{$name} ({$email})'s sign-up request was rejected.",
        ]);

        $user->delete();

        return back()->with('success', "{$name} rejected and removed.");
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        AdminNotification::create([
            'triggered_by' => $user->id,
            'type'         => 'account_' . $newStatus,
            'message'      => "Admin {$user->name}'s account was {$newStatus}d.",
        ]);

        return back()->with('success', "Admin {$newStatus}d successfully.");
    }

    public function edit(User $user): View
    {
        return view('pages.admin-management.edit', ['title' => 'Edit Admin', 'editAdmin' => $user]);
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

        return redirect()->route('admin-management.index')->with('success', 'Admin updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account here.']);
        }
        $name = $user->name;
        $user->delete();
        return redirect()->route('admin-management.index')->with('success', "Admin \"{$name}\" deleted.");
    }
}