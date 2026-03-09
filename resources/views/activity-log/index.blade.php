@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Activity Log'" />

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <!-- Filters -->
        <div class="border-b border-gray-200 p-5 dark:border-gray-800">
            <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <input type="text" name="event" value="{{ request('event') }}" placeholder="Cari aktivitas..."
                    class="h-10 rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 sm:w-56" />
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="h-10 rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="h-10 rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                <button type="submit" class="h-10 rounded-lg bg-gray-100 px-4 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Filter</button>
                @if(request()->hasAny(['event', 'date_from', 'date_to', 'causer']))
                    <a href="{{ route('activity-log.index') }}" class="h-10 inline-flex items-center px-3 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800">
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Waktu</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">User</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Subject</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ $activity->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $activity->causer?->name ?? 'System' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $activity->description }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $activity->subject_type ? class_basename($activity->subject_type) : '-' }}
                                {{ $activity->subject_id ? '#' . $activity->subject_id : '' }}
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                @if($activity->properties->isNotEmpty())
                                    <div x-data="{ open: false }">
                                        <button @click="open = !open" class="text-brand-500 hover:text-brand-600 text-xs">
                                            <span x-text="open ? 'Sembunyikan' : 'Lihat Detail'"></span>
                                        </button>
                                        <div x-show="open" x-transition class="mt-1">
                                            <pre class="text-xs bg-gray-50 dark:bg-gray-800 p-2 rounded overflow-x-auto">{{ json_encode($activity->properties->toArray(), JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas tercatat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($activities->hasPages())
        <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-800">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan {{ $activities->firstItem() }}-{{ $activities->lastItem() }} dari {{ $activities->total() }} hasil
                </p>
                {{ $activities->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        @endif
    </div>
@endsection
