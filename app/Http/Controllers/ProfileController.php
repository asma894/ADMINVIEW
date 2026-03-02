<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('pages.profile', ['user' => $request->user(), 'title' => 'Profile']);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'fname'  => ['required', 'string', 'max:100'],
            'lname'  => ['required', 'string', 'max:100'],
            'email'  => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone'  => ['nullable', 'string', 'max:30'],
            'bio'    => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $avatarPath = $user->avatar;
        if ($request->hasFile('avatar')) {
            if ($avatarPath) Storage::disk('public')->delete($avatarPath);
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update([
            'name'   => trim($validated['fname'] . ' ' . $validated['lname']),
            'email'  => $validated['email'],
            'phone'  => $validated['phone'] ?? null,
            'bio'    => $validated['bio'] ?? null,
            'avatar' => $avatarPath,
        ]);

        if ($user->wasChanged('email')) {
            $user->email_verified_at = null;
            $user->save();
        }

        return back()->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', ['password' => ['required', 'current_password']]);
        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->to('/signin');
    }
}