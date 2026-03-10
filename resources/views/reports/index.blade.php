@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Laporan Keuangan'" />

    <!-- Month Filter -->
    <div class="mb-6">
        <form method="GET" class="flex items-center gap-3">
            <input type="month" name="month" value="{{ $month }}"
                class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            <button type="submit" class="h-10 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white hover:bg-brand-600 transition">Tampilkan</button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 md:gap-6 mb-6">
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
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Saldo Bersih</p>
                    <h3 class="mt-1 text-2xl font-bold {{ $netBalance >= 0 ? 'text-brand-600 dark:text-brand-400' : 'text-error-600 dark:text-error-400' }}">{{ formatRupiah($netBalance) }}</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/10">
                    <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Breakdown -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Rekap per Kategori</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800">
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Kategori</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Tipe</th>
                        <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Total</th>
                        <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">%</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = $totalIncome + $totalExpense; @endphp
                    @forelse($categoryData as $cat)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block h-3 w-3 rounded-full" style="background-color: {{ $cat['color'] }}"></span>
                                    <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $cat['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ $cat['type'] === 'income' ? 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' : 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400' }}">
                                    {{ $cat['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right text-sm font-medium {{ $cat['type'] === 'income' ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400' }}">
                                {{ formatRupiah($cat['total']) }}
                            </td>
                            <td class="px-5 py-4 text-right text-sm text-gray-600 dark:text-gray-400">
                                {{ $grandTotal > 0 ? number_format(($cat['total'] / $grandTotal) * 100, 1) : 0 }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada data transaksi untuk bulan ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
