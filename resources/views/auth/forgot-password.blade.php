@extends('layouts.auth', ['title' => 'Lupa Password'])

@section('content')
<div class="flex min-h-screen items-center justify-center px-6 py-12">
    <div class="w-full max-w-md">
        <a href="{{ route('login') }}" class="mb-6 inline-flex items-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">
            <svg class="mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Login
        </a>

        <h1 class="text-title-sm mb-2 font-semibold text-gray-800 dark:text-white/90">Lupa Password</h1>
        <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">Masukkan email Anda untuk menerima link reset password.</p>

        @if(session('status'))
            <div class="mb-4 rounded-lg bg-success-50 p-4 text-sm text-success-700 dark:bg-success-500/10 dark:text-success-400">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-lg bg-error-50 p-4 text-sm text-error-700 dark:bg-error-500/10 dark:text-error-400">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            <div class="space-y-5">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
                <button type="submit" :disabled="loading"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 disabled:opacity-50 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                    <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span x-text="loading ? 'Mengirim...' : 'Kirim Link Reset'">Kirim Link Reset</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
