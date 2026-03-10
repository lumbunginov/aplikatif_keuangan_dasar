@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Dashboard'" />

    <!-- Month Filter -->
    <div class="mb-6">
        <form method="GET" class="flex items-center gap-3">
            <input type="month" name="month" value="{{ $month }}"
                class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            <button type="submit" class="h-10 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600 transition">Tampilkan</button>
        </form>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 md:gap-6">
        <!-- Total Pemasukan -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Pemasukan</p>
                    <h3 class="mt-1 text-2xl font-bold text-success-600 dark:text-success-400">{{ formatRupiah($totalIncome) }}</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-success-50 dark:bg-success-500/10">
                    <svg class="w-6 h-6 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </div>
            </div>
        </div>

        <!-- Total Pengeluaran -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Pengeluaran</p>
                    <h3 class="mt-1 text-2xl font-bold text-error-600 dark:text-error-400">{{ formatRupiah($totalExpense) }}</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-error-50 dark:bg-error-500/10">
                    <svg class="w-6 h-6 text-error-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </div>
            </div>
        </div>

        <!-- Saldo Bersih -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Saldo Bersih</p>
                    <h3 class="mt-1 text-2xl font-bold {{ $netBalance >= 0 ? 'text-blue-light-600 dark:text-blue-light-400' : 'text-error-600 dark:text-error-400' }}">{{ formatRupiah($netBalance) }}</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-light-50 dark:bg-blue-light-500/10">
                    <svg class="w-6 h-6 text-blue-light-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>

        <!-- Total Saldo Wallet -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Saldo Dompet</p>
                    <h3 class="mt-1 text-2xl font-bold text-brand-600 dark:text-brand-400">{{ formatRupiah($totalWalletBalance) }}</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/10">
                    <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 7H3a2 2 0 00-2 2v9a2 2 0 002 2h18a2 2 0 002-2V9a2 2 0 00-2-2zM4 7V5a2 2 0 012-2h12a2 2 0 012 2v2"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="mt-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Transaksi Terbaru</h3>
                <a href="{{ route('transactions.index') }}" class="text-sm text-brand-500 hover:text-brand-600">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                @if($recentTransactions->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="mx-auto mb-3 w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada transaksi. Mulai catat keuangan Anda!</p>
                        <a href="{{ route('transactions.create') }}" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Transaksi
                        </a>
                    </div>
                @else
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Deskripsi</th>
                                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Kategori</th>
                                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Dompet</th>
                                <th class="px-5 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Tipe</th>
                                <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $transaction)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $transaction->transaction_date->format('d M Y') }}</td>
                                    <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">{{ $transaction->description }}</td>
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
