@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Kategori'" />

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
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Daftar Kategori</h3>
            <a href="{{ route('categories.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Kategori
            </a>
        </div>

        {{-- Mobile: card-list (< md) --}}
        <div class="divide-y divide-gray-100 dark:divide-gray-800 md:hidden">
            @forelse($categories as $category)
                @php
                    $isIncome  = $category->type === 'income';
                    $rowBg     = $isIncome ? 'bg-success-50 dark:bg-success-500/10' : 'bg-error-50 dark:bg-error-500/10';
                    $border    = $isIncome ? 'border-l-4 border-success-500' : 'border-l-4 border-error-500';
                    $typeLabel = $isIncome ? 'Pemasukan' : 'Pengeluaran';
                @endphp
                <div class="grid grid-cols-[1fr_auto] items-center gap-3 px-4 py-3 {{ $rowBg }} {{ $border }}">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="inline-block h-2.5 w-2.5 shrink-0 rounded-full" style="background-color: {{ $category->color }}"></span>
                            <p class="truncate text-sm font-semibold text-gray-800 dark:text-white/90">{{ $category->name }}</p>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 ml-[18px]">
                            {{ $typeLabel }}{{ $category->is_default ? ' · Default' : '' }}
                        </p>
                    </div>
                    <div class="shrink-0 flex items-center gap-1">
                        @if(!$category->is_default)
                            <a href="{{ route('categories.edit', $category) }}" class="rounded-lg p-1 text-gray-500 hover:bg-gray-200/60 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700/60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('categories.destroy', $category) }}" x-data
                                @submit.prevent="if(confirm('Yakin ingin menghapus kategori ini?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded-lg p-1 text-gray-500 hover:bg-error-100/60 hover:text-error-600 dark:text-gray-400 dark:hover:bg-error-500/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-5 py-12 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada kategori.</p>
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
                        <th class="px-5 py-3 text-center text-sm font-medium text-gray-500 dark:text-gray-400">Default</th>
                        <th class="px-5 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block h-3 w-3 rounded-full" style="background-color: {{ $category->color }}"></span>
                                    <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ $category->type === 'income' ? 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' : 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400' }}">
                                    {{ $category->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($category->is_default)
                                    <span class="inline-flex rounded-full bg-blue-light-50 px-2.5 py-0.5 text-xs font-medium text-blue-light-700 dark:bg-blue-light-500/10 dark:text-blue-light-400">Default</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(!$category->is_default)
                                        <a href="{{ route('categories.edit', $category) }}" class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}" x-data
                                            @submit.prevent="if(confirm('Yakin ingin menghapus kategori ini?')) $el.submit()">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="rounded-lg p-1.5 text-gray-500 hover:bg-error-50 hover:text-error-600 dark:text-gray-400 dark:hover:bg-error-500/10">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada kategori.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
