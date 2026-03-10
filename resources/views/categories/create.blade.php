@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Tambah Kategori'" />

    <div class="mx-auto max-w-2xl">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">Form Tambah Kategori</h3>

            @if($errors->any())
                <div class="mb-4 rounded-lg bg-error-50 p-4 text-sm text-error-700 dark:bg-error-500/10 dark:text-error-400">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('categories.store') }}" x-data="{ loading: false }" @submit="loading = true">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Kategori <span class="text-error-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipe <span class="text-error-500">*</span></label>
                        <select name="type" required
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Warna <span class="text-error-500">*</span></label>
                        <input type="color" name="color" value="{{ old('color', '#6366f1') }}"
                            class="h-11 w-20 rounded-lg border border-gray-300 bg-transparent p-1 cursor-pointer dark:border-gray-700" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi</label>
                        <input type="text" name="description" value="{{ old('description') }}"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    </div>
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" :disabled="loading"
                            class="bg-brand-500 hover:bg-brand-600 disabled:opacity-50 inline-flex items-center rounded-lg px-5 py-2.5 text-sm font-medium text-white transition">
                            <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Simpan
                        </button>
                        <a href="{{ route('categories.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
