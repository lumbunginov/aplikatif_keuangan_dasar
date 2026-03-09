@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Role Management'" />

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-4 rounded-lg bg-success-50 p-4 text-sm text-success-700 dark:bg-success-500/10 dark:text-success-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 md:gap-6">
        @foreach($roles as $role)
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 capitalize">{{ $role->name }}</h3>
                    <span class="inline-flex rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">
                        {{ $role->users_count }} user{{ $role->users_count != 1 ? 's' : '' }}
                    </span>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $role->permissions->count() }} permission{{ $role->permissions->count() != 1 ? 's' : '' }}
                    </p>
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($role->permissions->take(5) as $perm)
                            <span class="inline-flex rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ $perm->name }}</span>
                        @endforeach
                        @if($role->permissions->count() > 5)
                            <span class="inline-flex rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-400">+{{ $role->permissions->count() - 5 }} lainnya</span>
                        @endif
                    </div>
                </div>
                @can('edit roles')
                <a href="{{ route('roles.edit', $role) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Permissions
                </a>
                @endcan
            </div>
        @endforeach
    </div>
@endsection
