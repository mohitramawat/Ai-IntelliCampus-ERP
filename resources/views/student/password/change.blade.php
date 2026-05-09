@extends('layouts.dashboard')

@section('content')

<div class="max-w-lg mx-auto">

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl bg-brand-accent/10 border border-brand-accent/20
                    flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-brand-accent text-[32px]">lock_reset</span>
        </div>
        <h1 class="text-2xl font-black text-brand-text">Change Your Password</h1>
        <p class="text-sm text-brand-sub mt-1">
            @if(auth()->user()->must_change_password)
                Your account has a temporary password. Set a new secure password to continue.
            @else
                Update your password at any time to keep your account secure.
            @endif
        </p>
    </div>

    {{-- Forced-change warning banner --}}
    @if(auth()->user()->must_change_password)
        <div class="mb-5 p-4 rounded-xl bg-amber-50 border border-amber-300 flex items-start gap-3">
            <span class="material-symbols-outlined text-amber-500 text-[22px] mt-0.5">warning</span>
            <div>
                <p class="text-sm font-bold text-amber-800">Action Required — Temporary Password</p>
                <p class="text-xs text-amber-700 mt-0.5">
                    You cannot access the student portal until you set a new personal password.
                </p>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-700">
            {{ session('warning') }}
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 p-3 rounded-xl bg-green-50 border border-green-200 flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600 text-[18px]">check_circle</span>
            <p class="text-sm font-semibold text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200">
            <ul class="space-y-1">
                @foreach($errors->all() as $err)
                    <li class="text-xs text-red-600 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">error</span>{{ $err }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('student.password.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="label" for="current_password">Current / Temporary Password *</label>
                <div class="relative">
                    <input id="current_password" name="current_password" type="password"
                           autocomplete="current-password"
                           class="input pr-11 @error('current_password') border-red-400 @enderror"
                           placeholder="Enter the password you received">
                    <button type="button" onclick="toggleVis('current_password','ico_curr')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-brand-sub hover:text-brand-text transition-colors">
                        <span id="ico_curr" class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
                @error('current_password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="label" for="new_password">New Password *</label>
                <div class="relative">
                    <input id="new_password" name="new_password" type="password"
                           autocomplete="new-password"
                           class="input pr-11 @error('new_password') border-red-400 @enderror"
                           placeholder="Min. 8 characters"
                           oninput="checkStrength(this.value)">
                    <button type="button" onclick="toggleVis('new_password','ico_new')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-brand-sub hover:text-brand-text transition-colors">
                        <span id="ico_new" class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
                <div class="mt-2 flex items-center gap-2">
                    <div class="flex-1 h-1.5 rounded-full bg-brand-border overflow-hidden">
                        <div id="strength_bar" class="h-full rounded-full transition-all duration-300 w-0 bg-red-400"></div>
                    </div>
                    <span id="strength_label" class="text-[10px] text-brand-sub w-16 text-right"></span>
                </div>
                @error('new_password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="label" for="new_password_confirmation">Confirm New Password *</label>
                <div class="relative">
                    <input id="new_password_confirmation" name="new_password_confirmation" type="password"
                           autocomplete="new-password"
                           class="input pr-11"
                           placeholder="Repeat new password">
                    <button type="button" onclick="toggleVis('new_password_confirmation','ico_conf')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-brand-sub hover:text-brand-text transition-colors">
                        <span id="ico_conf" class="material-symbols-outlined text-[20px]">visibility</span>
                    </button>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-brand-muted border border-brand-border space-y-1.5">
                <p class="text-xs font-semibold text-brand-text">Password requirements:</p>
                <p class="text-xs text-brand-sub flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[14px] text-green-500">check_circle</span>
                    At least 8 characters
                </p>
                <p class="text-xs text-brand-sub flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[14px] text-green-500">check_circle</span>
                    Different from your temporary password
                </p>
                <p class="text-xs text-brand-sub flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[14px] text-green-500">check_circle</span>
                    Both fields must match
                </p>
            </div>

            <button type="submit" class="btn-primary w-full justify-center py-3">
                <span class="material-symbols-outlined text-[20px]">lock_reset</span>
                Set New Password
            </button>

            @if(!auth()->user()->must_change_password)
                <a href="{{ route('student.dashboard') }}" class="btn-secondary w-full justify-center mt-2">
                    Cancel
                </a>
            @endif
        </form>
    </div>
</div>

<script>
function toggleVis(inputId, iconId) {
    const el = document.getElementById(inputId);
    const ic = document.getElementById(iconId);
    el.type = el.type === 'password' ? 'text' : 'password';
    ic.textContent = el.type === 'password' ? 'visibility' : 'visibility_off';
}

function checkStrength(val) {
    const bar   = document.getElementById('strength_bar');
    const label = document.getElementById('strength_label');
    if (!val) { bar.style.width = '0%'; label.textContent = ''; return; }
    let score = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const map = [
        ['20%',  'bg-red-400',    'Weak'],
        ['45%',  'bg-orange-400', 'Fair'],
        ['70%',  'bg-yellow-400', 'Good'],
        ['100%', 'bg-green-500',  'Strong ✓'],
    ];
    const [w, cls, txt] = map[score - 1] ?? ['10%', 'bg-red-300', 'Too short'];
    bar.style.width = w;
    bar.className   = `h-full rounded-full transition-all duration-300 ${cls}`;
    label.textContent = txt;
}
</script>

@endsection
