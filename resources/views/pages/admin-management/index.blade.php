@extends('layouts.app')

@section('content')

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
    class="mb-5 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-700/30 dark:bg-green-900/20 dark:text-green-400">
    <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- ══════════════════════════════════════════════════════════
     SECTION 1: PENDING SIGN-UPS (top table)
════════════════════════════════════════════════════════════ --}}
<div class="mb-8">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Pending Approvals</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                New sign-ups waiting for your review
            </p>
        </div>
        @if($pendingUsers->count() > 0)
        <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
            {{ $pendingUsers->count() }} pending
        </span>
        @endif
    </div>

    <div class="overflow-hidden rounded-2xl border border-yellow-200 dark:border-yellow-700/40 bg-white dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-yellow-100 dark:border-yellow-700/30 bg-yellow-50 dark:bg-yellow-900/10">
                        <th class="px-5 py-4 font-medium text-gray-500 dark:text-gray-400">User</th>
                        <th class="px-5 py-4 font-medium text-gray-500 dark:text-gray-400">Email</th>
                        <th class="px-5 py-4 font-medium text-gray-500 dark:text-gray-400">Signed Up</th>
                        <th class="px-5 py-4 font-medium text-gray-500 dark:text-gray-400 text-right">Approve As</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($pendingUsers as $pending)
                    <tr class="hover:bg-yellow-50/50 dark:hover:bg-yellow-900/10 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 shrink-0 overflow-hidden rounded-full border border-gray-200 dark:border-gray-600">
                                    <img src="{{ $pending->avatar_url }}" alt="{{ $pending->name }}" class="h-full w-full object-cover" />
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $pending->name }}</p>
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-1.5 py-0.5 text-[10px] font-semibold text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                        Pending
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ $pending->email }}</td>
                        <td class="px-5 py-4 text-xs text-gray-400 dark:text-gray-500">
                            {{ $pending->created_at->format('M d, Y · h:i A') }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2" x-data="{ role: 'user' }">
                                {{-- Role selector --}}
                                <select x-model="role"
                                    class="h-8 rounded-lg border border-gray-300 bg-transparent px-2 text-xs text-gray-700 focus:border-brand-300 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                    <option value="user">User</option>
                                    <option value="superadmin">Super Admin</option>
                                </select>

                                {{-- Approve --}}
                                <form method="POST" action="{{ route('admin-management.approve', $pending) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="role" :value="role" />
                                    <button type="submit"
                                        class="rounded-lg bg-green-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-600 transition-colors">
                                        Approve
                                    </button>
                                </form>

                                {{-- Reject --}}
                                <form method="POST" action="{{ route('admin-management.reject', $pending) }}"
                                    onsubmit="return confirm('Reject and delete {{ addslashes($pending->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-700/40 dark:text-red-400 dark:hover:bg-red-900/20 transition-colors">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">All caught up!</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">No pending sign-ups right now.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     SECTION 2: ADMINS CRUD TABLE
════════════════════════════════════════════════════════════ --}}
<div>
    <div class="mb-4">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Super Admins</h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage approved super administrator accounts</p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                        <th class="px-5 py-4 font-medium text-gray-500 dark:text-gray-400">Admin</th>
                        <th class="px-5 py-4 font-medium text-gray-500 dark:text-gray-400">Email</th>
                        <th class="px-5 py-4 font-medium text-gray-500 dark:text-gray-400">Phone</th>
                        <th class="px-5 py-4 font-medium text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-5 py-4 font-medium text-gray-500 dark:text-gray-400">Approved</th>
                        <th class="px-5 py-4 font-medium text-gray-500 dark:text-gray-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($admins as $admin)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors {{ $admin->id === auth()->id() ? 'bg-brand-50/30 dark:bg-brand-500/5' : '' }}">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 shrink-0 overflow-hidden rounded-full border border-gray-200 dark:border-gray-600">
                                    <img src="{{ $admin->avatar_url }}" alt="{{ $admin->name }}" class="h-full w-full object-cover" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="font-medium text-gray-800 dark:text-white">{{ $admin->name }}</p>
                                        @if($admin->id === auth()->id())
                                        <span class="rounded bg-brand-100 px-1.5 py-0.5 text-[10px] font-semibold text-brand-700 dark:bg-brand-500/20 dark:text-brand-400">You</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $admin->bio ?? 'Super Admin' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ $admin->email }}</td>
                        <td class="px-5 py-4 text-gray-500 dark:text-gray-400">{{ $admin->phone ?? '—' }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium
                                {{ $admin->status === 'active'
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                    : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $admin->status === 'active' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ ucfirst($admin->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-400 dark:text-gray-500">
                            {{ $admin->approved_at ? $admin->approved_at->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                @if($admin->id !== auth()->id())
                                {{-- Toggle --}}
                                <form method="POST" action="{{ route('admin-management.toggle-status', $admin) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors
                                        {{ $admin->status === 'active'
                                            ? 'border-orange-200 text-orange-600 hover:bg-orange-50 dark:border-orange-700/40 dark:text-orange-400'
                                            : 'border-green-200 text-green-600 hover:bg-green-50 dark:border-green-700/40 dark:text-green-400' }}">
                                        {{ $admin->status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                                @endif
                                {{-- Edit --}}
                                <a href="{{ route('admin-management.edit', $admin) }}"
                                    class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                                    Edit
                                </a>
                                @if($admin->id !== auth()->id())
                                {{-- Delete --}}
                                <form method="POST" action="{{ route('admin-management.destroy', $admin) }}"
                                    onsubmit="return confirm('Delete admin {{ addslashes($admin->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-700/40 dark:text-red-400 dark:hover:bg-red-900/20 transition-colors">
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400 dark:text-gray-500">
                            No approved super admins found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($admins->hasPages())
        <div class="border-t border-gray-100 dark:border-gray-700 px-5 py-4">
            {{ $admins->links() }}
        </div>
        @endif
    </div>
</div>

@endsection