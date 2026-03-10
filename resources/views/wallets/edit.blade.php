@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Edit Dompet'" />

    <div class="mx-auto max-w-2xl">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">Form Edit Dompet</h3>

            @if($errors->any())
                <div class="mb-4 rounded-lg bg-error-50 p-4 text-sm text-error-700 dark:bg-error-500/10 dark:text-error-400">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('wallets.update', $wallet) }}" x-data="{ loading: false }" @submit="loading = true">
                @csrf @method('PUT')
                <div class="space-y-5">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Dompet <span class="text-error-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $wallet->name) }}" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipe <span class="text-error-500">*</span></label>
                        <select name="type" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="cash" {{ old('type', $wallet->type) === 'cash' ? 'selected' : '' }}>Tunai</option>
                            <option value="bank" {{ old('type', $wallet->type) === 'bank' ? 'selected' : '' }}>Bank</option>
                            <option value="ewallet" {{ old('type', $wallet->type) === 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                            <option value="other" {{ old('type', $wallet->type) === 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Saldo Saat Ini</label>
                        <input type="text" value="{{ formatRupiah($wallet->balance) }}" disabled
                            class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400" />
                        <p class="mt-1 text-xs text-gray-400">Saldo diperbarui otomatis melalui transaksi.</p>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi</label>
                        <input type="text" name="description" value="{{ old('description', $wallet->description) }}"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $wallet->is_active) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700" />
                        <label for="is_active" class="text-sm text-gray-700 dark:text-gray-400">Aktif</label>
                    </div>
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" :disabled="loading"
                            class="bg-brand-500 hover:bg-brand-600 disabled:opacity-50 inline-flex items-center rounded-lg px-5 py-2.5 text-sm font-medium text-white transition">
                            <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Simpan
                        </button>
                        <a href="{{ route('wallets.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
