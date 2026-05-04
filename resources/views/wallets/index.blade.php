@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Dompet'" />

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-4 rounded-lg bg-success-50 p-4 text-sm text-success-700 dark:bg-success-500/10 dark:text-success-400">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-4 rounded-lg bg-error-50 p-4 text-sm text-error-700 dark:bg-error-500/10 dark:text-error-400">
            {{ session('error') }}
        </div>
    @endif

    <!-- Total Balance Card -->
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Saldo (Dompet Aktif)</p>
                <h3 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ formatRupiah($totalBalance) }}</h3>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/10">
                <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 7H3a2 2 0 00-2 2v9a2 2 0 002 2h18a2 2 0 002-2V9a2 2 0 00-2-2zM4 7V5a2 2 0 012-2h12a2 2 0 012 2v2"/></svg>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Daftar Dompet</h3>
            <a href="{{ route('wallets.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Dompet
            </a>
        </div>

        {{-- Mobile: card-list (< md) --}}
        <div class="divide-y divide-gray-100 dark:divide-gray-800 md:hidden">
            @forelse($wallets as $wallet)
                @php
                    $typeLabels = ['cash' => 'Tunai', 'bank' => 'Bank', 'ewallet' => 'E-Wallet', 'other' => 'Lainnya'];
                    $isActive  = $wallet->is_active;
                    $rowBg     = $isActive ? 'bg-success-50 dark:bg-success-500/10' : 'bg-error-50 dark:bg-error-500/10';
                    $border    = $isActive ? 'border-l-4 border-success-500' : 'border-l-4 border-error-500';
                    $balColor  = $wallet->balance >= 0 ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400';
                @endphp
                <div class="grid grid-cols-[1fr_auto] items-center gap-3 px-4 py-3 {{ $rowBg }} {{ $border }}">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-800 dark:text-white/90">{{ $wallet->name }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $typeLabels[$wallet->type] ?? $wallet->type }}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-sm font-bold {{ $balColor }}">{{ formatRupiah($wallet->balance) }}</p>
                        <div class="flex items-center justify-end gap-1 mt-1">
                            <a href="{{ route('wallets.edit', $wallet) }}" class="rounded-lg p-1 text-gray-500 hover:bg-gray-200/60 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700/60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('wallets.destroy', $wallet) }}" x-data
                                @submit.prevent="if(confirm('Yakin ingin menghapus dompet ini?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded-lg p-1 text-gray-500 hover:bg-error-100/60 hover:text-error-600 dark:text-gray-400 dark:hover:bg-error-500/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-12 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada dompet. Tambahkan dompet pertama Anda.</p>
                </div>
            @endforelse
        </div>

        {{-- Desktop: full table (≥ md) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800">
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Nama</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Tipe</th>
                        <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Saldo</th>
                        <th class="px-5 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wallets as $wallet)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-5 py-4">
                                <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $wallet->name }}</span>
                                @if($wallet->description)
                                    <p class="text-xs text-gray-400">{{ $wallet->description }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $typeLabels = ['cash' => 'Tunai', 'bank' => 'Bank', 'ewallet' => 'E-Wallet', 'other' => 'Lainnya'];
                                @endphp
                                <span class="inline-flex rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">
                                    {{ $typeLabels[$wallet->type] ?? $wallet->type }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right text-sm font-medium {{ $wallet->balance >= 0 ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400' }}">
                                {{ formatRupiah($wallet->balance) }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ $wallet->is_active ? 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' : 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400' }}">
                                    {{ $wallet->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('wallets.edit', $wallet) }}" class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('wallets.destroy', $wallet) }}" x-data
                                        @submit.prevent="if(confirm('Yakin ingin menghapus dompet ini?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-lg p-1.5 text-gray-500 hover:bg-error-50 hover:text-error-600 dark:text-gray-400 dark:hover:bg-error-500/10">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada dompet. Tambahkan dompet pertama Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
