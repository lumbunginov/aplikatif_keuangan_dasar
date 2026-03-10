@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Edit Transaksi'" />

    <div class="mx-auto max-w-2xl">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">Form Edit Transaksi</h3>

            @if($errors->any())
                <div class="mb-4 rounded-lg bg-error-50 p-4 text-sm text-error-700 dark:bg-error-500/10 dark:text-error-400">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('transactions.update', $transaction) }}"
                x-data="{
                    loading: false,
                    type: '{{ old('type', $transaction->type) }}',
                    categories: @js($categories),
                    get filteredCategories() {
                        return this.categories.filter(c => c.type === this.type);
                    }
                }" @submit="loading = true">
                @csrf @method('PUT')
                <div class="space-y-5">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipe Transaksi <span class="text-error-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="income" x-model="type"
                                    class="h-4 w-4 border-gray-300 text-success-500 focus:ring-success-500 dark:border-gray-700" />
                                <span class="text-sm text-gray-700 dark:text-gray-400">Pemasukan</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="expense" x-model="type"
                                    class="h-4 w-4 border-gray-300 text-error-500 focus:ring-error-500 dark:border-gray-700" />
                                <span class="text-sm text-gray-700 dark:text-gray-400">Pengeluaran</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jumlah <span class="text-error-500">*</span></label>
                        <input type="number" name="amount" value="{{ old('amount', $transaction->amount) }}" required min="0.01" step="0.01"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi <span class="text-error-500">*</span></label>
                        <input type="text" name="description" value="{{ old('description', $transaction->description) }}" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kategori</label>
                            <select name="category_id"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="">Pilih Kategori</option>
                                <template x-for="cat in filteredCategories" :key="cat.id">
                                    <option :value="cat.id" x-text="cat.name" :selected="cat.id == '{{ old('category_id', $transaction->category_id) }}'"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Dompet <span class="text-error-500">*</span></label>
                            <select name="wallet_id" required
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="">Pilih Dompet</option>
                                @foreach($wallets as $wallet)
                                    <option value="{{ $wallet->id }}" {{ old('wallet_id', $transaction->wallet_id) == $wallet->id ? 'selected' : '' }}>{{ $wallet->name }} ({{ formatRupiah($wallet->balance) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal <span class="text-error-500">*</span></label>
                        <input type="date" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Catatan</label>
                        <textarea name="notes" rows="3"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('notes', $transaction->notes) }}</textarea>
                    </div>
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" :disabled="loading"
                            class="bg-brand-500 hover:bg-brand-600 disabled:opacity-50 inline-flex items-center rounded-lg px-5 py-2.5 text-sm font-medium text-white transition">
                            <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Simpan
                        </button>
                        <a href="{{ route('transactions.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
