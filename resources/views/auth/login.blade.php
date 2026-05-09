<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>IntelliCampus ERP — Sign In</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-brand-bg text-brand-text min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        {{-- Card --}}
        <div class="bg-brand-surface rounded-3xl shadow-card-lg border border-brand-border overflow-hidden">

            {{-- Top accent bar --}}
            <div class="h-1.5 bg-gradient-to-r from-brand-accent to-sky-400"></div>

            <div class="p-8">
                {{-- Logo --}}
                <div class="flex flex-col items-center mb-8">
                    <div class="w-14 h-14 rounded-2xl bg-brand-accent flex items-center justify-center shadow-accent mb-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-black text-brand-text tracking-tight">IntelliCampus</h1>
                    <p class="text-sm text-brand-sub mt-1">Sign in to your account</p>
                </div>

                {{-- Error flash --}}
                @if ($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-status-dangers border border-status-danger/20 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-status-danger mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-status-danger font-medium">Invalid credentials. Please try again.</p>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="label">Email address</label>
                        <input id="email" name="email" type="email"
                               value="{{ old('email') }}" required autofocus autocomplete="username"
                               class="input"
                               placeholder="you@college.edu">
                        @error('email')
                            <p class="mt-1.5 text-xs text-status-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="label">Password</label>
                        <input id="password" name="password" type="password"
                               required autocomplete="current-password"
                               class="input"
                               placeholder="••••••••">
                        @error('password')
                            <p class="mt-1.5 text-xs text-status-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember" class="flex items-center gap-2 cursor-pointer">
                            <input id="remember" name="remember" type="checkbox"
                                   class="rounded border-brand-border text-brand-accent focus:ring-brand-accent/30">
                            <span class="text-sm text-brand-sub">Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-sm font-medium text-brand-accent hover:text-brand-accentd transition-colors">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center py-3 text-base mt-2">
                        Sign in to ERP
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-xs text-brand-sub mt-6">
            © {{ date('Y') }} IntelliCampus ERP. All rights reserved.
        </p>
    </div>

</body>
</html>
