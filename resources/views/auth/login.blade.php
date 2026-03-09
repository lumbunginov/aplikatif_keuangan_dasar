@extends('layouts.auth', ['title' => 'Login'])

@section('content')
<div class="relative z-1 flex min-h-screen">
    <!-- Form Side -->
    <div class="flex w-full flex-1 flex-col justify-center px-6 py-12 lg:w-1/2 lg:px-12">
        <div class="mx-auto w-full max-w-md">
            <div class="mb-8">
                <h1 class="text-title-sm sm:text-title-md mb-2 font-semibold text-gray-800 dark:text-white/90">
                    Sign In
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Masukkan email dan password untuk masuk
                </p>
            </div>

            @if(session('status'))
                <div class="mb-4 rounded-lg bg-success-50 p-4 text-sm text-success-700 dark:bg-success-500/10 dark:text-success-400">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 rounded-lg bg-error-50 p-4 text-sm text-error-700 dark:bg-error-500/10 dark:text-error-400">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" x-data="{ loading: false }" @submit="loading = true">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Email<span class="text-error-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@aplikatif.com" required autofocus
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Password<span class="text-error-500">*</span>
                        </label>
                        <div x-data="{ show: false }" class="relative">
                            <input :type="show ? 'text' : 'password'" name="password" placeholder="Masukkan password" required
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                            <button type="button" @click="show = !show" class="absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex cursor-pointer items-center text-sm text-gray-700 dark:text-gray-400">
                            <input type="checkbox" name="remember" class="mr-2 h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                            Ingat saya
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">
                            Lupa password?
                        </a>
                    </div>
                    <button type="submit" :disabled="loading"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 disabled:opacity-50 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                        <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span x-text="loading ? 'Memproses...' : 'Sign In'">Sign In</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Branding Side -->
    <div class="bg-brand-950 relative hidden h-screen w-1/2 items-center justify-center lg:flex dark:bg-white/5">
        <div class="flex max-w-xs flex-col items-center text-center">
            <a href="/" class="mb-4 block">
                <img src="/images/logo/auth-logo.svg" alt="Logo" />
            </a>
            <p class="text-gray-400 dark:text-white/60">
                {{ setting('app_name', 'Aplikatif Base') }} Admin Panel
            </p>
        </div>
    </div>
</div>
@endsection
