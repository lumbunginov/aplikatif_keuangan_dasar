@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Edit Role: ' . ucfirst($role->name)" />

    <div class="mx-auto max-w-2xl">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
                Permissions untuk role: <span class="text-brand-500 capitalize">{{ $role->name }}</span>
            </h3>

            <form method="POST" action="{{ route('roles.update', $role) }}" x-data="{ loading: false }" @submit="loading = true">
                @csrf @method('PUT')
                <div class="space-y-6">
                    @foreach($permissions as $group => $perms)
                        <div>
                            <h4 class="mb-3 text-sm font-semibold uppercase text-gray-500 dark:text-gray-400">{{ $group }}</h4>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                @foreach($perms as $permission)
                                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                            {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" :disabled="loading"
                        class="bg-brand-500 hover:bg-brand-600 disabled:opacity-50 inline-flex items-center rounded-lg px-5 py-2.5 text-sm font-medium text-white transition">
                        <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('roles.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
