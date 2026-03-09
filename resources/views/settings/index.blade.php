@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Settings'" />

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-4 rounded-lg bg-success-50 p-4 text-sm text-success-700 dark:bg-success-500/10 dark:text-success-400">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-lg bg-error-50 p-4 text-sm text-error-700 dark:bg-error-500/10 dark:text-error-400">
            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data"
        x-data="{ loading: false }" @submit="loading = true">
        @csrf @method('PUT')

        <div class="space-y-6">
            <!-- General Settings -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Pengaturan Umum</h3>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Aplikasi</label>
                        <input type="text" name="app_name" value="{{ $settings['app_name'] ?? '' }}" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Warna Utama</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="primary_color" value="{{ $settings['primary_color'] ?? '#4F46E5' }}"
                                class="h-11 w-16 cursor-pointer rounded-lg border border-gray-300 dark:border-gray-700" />
                            <input type="text" value="{{ $settings['primary_color'] ?? '#4F46E5' }}" readonly
                                class="h-11 flex-1 rounded-lg border border-gray-300 bg-gray-50 px-4 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400" />
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Timezone</label>
                        <select name="timezone" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            @foreach($timezones as $tz)
                                <option value="{{ $tz }}" {{ ($settings['timezone'] ?? 'Asia/Jakarta') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Session Lifetime (menit)</label>
                        <input type="number" name="session_lifetime" value="{{ $settings['session_lifetime'] ?? 120 }}" min="1" max="10080" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                </div>
                <div class="mt-5 flex items-center gap-6">
                    <label class="flex cursor-pointer items-center gap-3 text-sm text-gray-700 dark:text-gray-400">
                        <input type="checkbox" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                        Mode Maintenance
                    </label>
                </div>
            </div>

            <!-- Branding -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Branding</h3>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Logo Aplikasi</label>
                        @if($settings['app_logo'] ?? null)
                            <img src="{{ asset('storage/settings/' . $settings['app_logo']) }}" alt="Logo" class="mb-2 h-12" />
                        @endif
                        <input type="file" name="app_logo" accept="image/*"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Favicon</label>
                        @if($settings['app_favicon'] ?? null)
                            <img src="{{ asset('storage/settings/' . $settings['app_favicon']) }}" alt="Favicon" class="mb-2 h-8" />
                        @endif
                        <input type="file" name="app_favicon" accept="image/*,.ico"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                </div>
            </div>

            <!-- Email Settings -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Email</h3>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Pengirim</label>
                        <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? '' }}"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email Pengirim</label>
                        <input type="email" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? '' }}"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                </div>
            </div>

            <!-- PWA Settings -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">PWA (Progressive Web App)</h3>
                <div class="mb-5">
                    <label class="flex cursor-pointer items-center gap-3 text-sm text-gray-700 dark:text-gray-400">
                        <input type="checkbox" name="pwa_enabled" value="1" {{ ($settings['pwa_enabled'] ?? '1') === '1' ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                        Aktifkan PWA
                    </label>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Singkat (max 12 karakter)</label>
                        <input type="text" name="pwa_short_name" value="{{ $settings['pwa_short_name'] ?? 'Aplikatif' }}" maxlength="12"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Warna Tema PWA</label>
                        <div class="flex items-center gap-3">
                            <input type="color" name="pwa_theme_color" value="{{ $settings['pwa_theme_color'] ?? '#4F46E5' }}"
                                class="h-11 w-16 cursor-pointer rounded-lg border border-gray-300 dark:border-gray-700" />
                            <span class="text-sm text-gray-500">{{ $settings['pwa_theme_color'] ?? '#4F46E5' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" :disabled="loading"
                class="bg-brand-500 hover:bg-brand-600 disabled:opacity-50 inline-flex items-center rounded-lg px-6 py-2.5 text-sm font-medium text-white transition">
                <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                Simpan Pengaturan
            </button>
        </div>
    </form>
@endsection
