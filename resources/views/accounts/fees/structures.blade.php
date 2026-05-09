@extends('layouts.dashboard')
@section('content')

<div class="mb-8">
    <h1 class="text-2xl font-extrabold text-brand-text tracking-tight">Fee Structures</h1>
    <p class="text-brand-sub text-sm">Official academic fee configurations (Read-only)</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($structures as $structure)
    <div class="card hover:shadow-xl transition-shadow border-t-4 border-brand-accent">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-lg font-bold text-brand-text">{{ optional($structure->course)->name }}</h3>
                <p class="text-xs text-brand-sub uppercase tracking-widest font-bold">Category: {{ $structure->category ?? 'General' }}</p>
            </div>
            <span class="material-symbols-outlined text-brand-accent">account_balance_wallet</span>
        </div>

        <div class="space-y-3 pt-4 border-t border-brand-border">
            <div class="flex justify-between text-sm">
                <span class="text-brand-sub">Effective Year</span>
                <span class="text-brand-text font-bold">{{ $structure->effective_from_year }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-brand-sub">Currency</span>
                <span class="text-brand-text font-bold">{{ $structure->currency }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-brand-sub">Status</span>
                <span class="badge {{ $structure->is_active ? 'badge-success' : 'badge-danger' }}">{{ $structure->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
            <div class="flex justify-between text-sm pt-2 border-t border-brand-border">
                <span class="text-brand-text font-bold uppercase text-xs">Total Yearly Fee</span>
                <span class="text-brand-accent font-extrabold text-lg">₹{{ number_format($structure->total_fee, 0) }}</span>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full card py-20 text-center">
        <p class="text-brand-sub">No fee structures defined yet.</p>
    </div>
    @endforelse
</div>

@endsection
