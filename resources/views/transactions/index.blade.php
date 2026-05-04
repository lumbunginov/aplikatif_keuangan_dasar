@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Transaksi'" />

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

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Daftar Transaksi</h3>
            <a href="{{ route('transactions.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Transaksi
            </a>
        </div>

        <!-- Filters -->
        <div class="border-b border-gray-200 p-5 dark:border-gray-800">
            <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:flex-wrap">
                <select name="type" class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">Semua Tipe</option>
                    <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
                <select name="category_id" class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="wallet_id" class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">Semua Dompet</option>
                    @foreach($wallets as $w)
                        <option value="{{ $w->id }}" {{ request('wallet_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                    @endforeach
                </select>
                <input type="month" name="month" value="{{ request('month') }}"
                    class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                <button type="submit" class="h-10 rounded-lg bg-gray-100 px-4 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Filter</button>
                @if(request()->hasAny(['type', 'category_id', 'wallet_id', 'month']))
                    <a href="{{ route('transactions.index') }}" class="h-10 inline-flex items-center rounded-lg px-3 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Reset</a>
                @endif
            </form>
        </div>

        {{-- Mobile: card-list (< md) --}}
        <div class="divide-y divide-gray-100 dark:divide-gray-800 md:hidden">
            @forelse($transactions as $transaction)
                @php
                    $isIncome = $transaction->type === 'income';
                    $rowBg    = $isIncome ? 'bg-success-50 dark:bg-success-500/10' : 'bg-error-50 dark:bg-error-500/10';
                    $border   = $isIncome ? 'border-l-4 border-success-500' : 'border-l-4 border-error-500';
                    $amtColor = $isIncome ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400';
                    $prefix   = $isIncome ? '+' : '-';
                @endphp
                <div class="grid grid-cols-[1fr_auto] items-center gap-3 px-4 py-3 {{ $rowBg }} {{ $border }}">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-800 dark:text-white/90">{{ $transaction->description }}</p>
                        <p class="truncate text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            {{ $transaction->transaction_date->format('d M Y') }}{{ $transaction->category ? ' · ' . $transaction->category->name : '' }}
                        </p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-sm font-bold {{ $amtColor }}">{{ $prefix }}{{ formatRupiah($transaction->amount) }}</p>
                        <div class="flex items-center justify-end gap-1 mt-1">
                            <a href="{{ route('transactions.edit', $transaction) }}" class="rounded-lg p-1 text-gray-500 hover:bg-gray-200/60 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700/60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" x-data
                                @submit.prevent="if(confirm('Yakin ingin menghapus transaksi ini?')) $el.submit()">
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
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada transaksi.</p>
                </div>
            @endforelse
        </div>

        {{-- Desktop: full table (≥ md) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800">
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Kategori</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Dompet</th>
                        <th class="px-5 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Tipe</th>
                        <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah</th>
                        <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $transaction->transaction_date->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $transaction->description }}</span>
                                @if($transaction->notes)
                                    <p class="text-xs text-gray-400 truncate max-w-xs">{{ $transaction->notes }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($transaction->category)
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium" style="background-color: {{ $transaction->category->color }}20; color: {{ $transaction->category->color }}">
                                        <span class="h-1.5 w-1.5 rounded-full" style="background-color: {{ $transaction->category->color }}"></span>
                                        {{ $transaction->category->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $transaction->wallet->name ?? '-' }}</td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ $transaction->type === 'income' ? 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' : 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400' }}">
                                    {{ $transaction->type === 'income' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right text-sm font-medium {{ $transaction->type === 'income' ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}{{ formatRupiah($transaction->amount) }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('transactions.edit', $transaction) }}" class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" x-data
                                        @submit.prevent="if(confirm('Yakin ingin menghapus transaksi ini?')) $el.submit()">
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
                            <td colspan="7" class="px-5 py-12 text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada transaksi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-800">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan {{ $transactions->firstItem() }}-{{ $transactions->lastItem() }} dari {{ $transactions->total() }} hasil
                </p>
                {{ $transactions->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        @endif
    </div>
@endsection
