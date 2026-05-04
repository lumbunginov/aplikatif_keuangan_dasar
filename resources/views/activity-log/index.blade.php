@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Activity Log'" />

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <!-- Filters -->
        @php $logFilters = collect(['event','date_from','date_to','causer'])->filter(fn($k) => request($k))->count(); @endphp
        <div x-data="{ open: {{ $logFilters > 0 ? 'true' : 'false' }} }" class="border-b border-gray-200 dark:border-gray-800">
            <button type="button" @click="open = !open"
                class="flex w-full items-center justify-between px-5 py-3 transition hover:bg-gray-50 dark:hover:bg-white/[0.02]">
                <span class="flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                    @if($logFilters > 0)
                        <span class="flex h-4 w-4 items-center justify-center rounded-full bg-brand-500 text-xs font-bold text-white leading-none">{{ $logFilters }}</span>
                    @endif
                </span>
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-transition class="px-5 pb-5">
                <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:flex-wrap">
                    <input type="text" name="event" value="{{ request('event') }}" placeholder="Cari aktivitas..."
                        class="h-10 rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 sm:w-56" />
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="h-10 rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="h-10 rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    <button type="submit" class="h-10 rounded-lg bg-gray-100 px-4 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Terapkan</button>
                    @if(request()->hasAny(['event', 'date_from', 'date_to', 'causer']))
                        <a href="{{ route('activity-log.index') }}" class="h-10 inline-flex items-center px-3 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Mobile: card-list (< md) --}}
        <div class="divide-y divide-gray-100 dark:divide-gray-800 md:hidden">
            @forelse($activities as $activity)
                <div class="px-4 py-3" x-data="{ open: false }">
                    <div class="grid grid-cols-[1fr_auto] items-start gap-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-gray-800 dark:text-white/90">{{ $activity->description }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                {{ $activity->created_at->format('d M Y H:i') }} · {{ $activity->causer?->name ?? 'System' }}
                            </p>
                            @if($activity->subject_type)
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ class_basename($activity->subject_type) }}{{ $activity->subject_id ? ' #' . $activity->subject_id : '' }}
                                </p>
                            @endif
                        </div>
                        @if($activity->properties->isNotEmpty())
                            <div class="shrink-0">
                                <button @click="open = !open" class="text-brand-500 hover:text-brand-600 text-xs whitespace-nowrap">
                                    <span x-text="open ? 'Tutup' : 'Detail'"></span>
                                </button>
                            </div>
                        @endif
                    </div>
                    @if($activity->properties->isNotEmpty())
                        <div x-show="open" x-transition class="mt-2">
                            <pre class="text-xs bg-gray-50 dark:bg-gray-800 p-2 rounded overflow-x-auto">{{ json_encode($activity->properties->toArray(), JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>
            @empty
                <div class="px-5 py-12 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada aktivitas tercatat.</p>
                </div>
            @endforelse
        </div>

        {{-- Desktop: full table (≥ md) --}}
        <div class="hidden md:block overflow-x-auto">
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
                    {{ $activities->firstItem() }}–{{ $activities->lastItem() }} dari {{ $activities->total() }}
                </p>
                {{ $activities->links('vendor.pagination.tailwind') }}
            </div>
        </div>
        @endif
    </div>
@endsection
