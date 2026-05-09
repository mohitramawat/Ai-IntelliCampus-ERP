@extends('layouts.dashboard')
@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-brand-text tracking-tight">Fee Transactions</h1>
        <p class="text-brand-sub text-sm">Detailed log of all student payments</p>
    </div>
</div>

<div class="card mb-8">
    <form action="{{ route('accounts.fees.transactions') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="label">Search Student</label>
            <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="Name or Roll No...">
        </div>
        <div>
            <label class="label">Payment Mode</label>
            <select name="payment_method" class="input">
                <option value="">All Methods</option>
                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="online" {{ request('payment_method') == 'online' ? 'selected' : '' }}>Online</option>
                <option value="upi" {{ request('payment_method') == 'upi' ? 'selected' : '' }}>UPI</option>
            </select>
        </div>
        <div>
            <label class="label">From Date</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="input">
        </div>
        <div class="flex gap-2">
            <div class="flex-1">
                <label class="label">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="input">
            </div>
            <button type="submit" class="btn-primary p-3 h-[42px] mt-auto">
                <span class="material-symbols-outlined">search</span>
            </button>
            @if(request()->anyFilled(['search', 'payment_method', 'date_from', 'date_to']))
                <a href="{{ route('accounts.fees.transactions') }}" class="btn-secondary p-3 h-[42px] mt-auto">
                    <span class="material-symbols-outlined">close</span>
                </a>
            @endif
        </div>
    </form>
</div>

<div class="card p-0 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-brand-border">
                    <th class="table-head">ID</th>
                    <th class="table-head">Student</th>
                    <th class="table-head">Installment</th>
                    <th class="table-head">Amount</th>
                    <th class="table-head">Mode</th>
                    <th class="table-head">Date</th>
                    <th class="table-head">Reference</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @forelse($transactions as $tx)
                <tr class="table-row-hover">
                    <td class="table-cell text-xs font-bold text-brand-sub">#{{ $tx->id }}</td>
                    <td class="table-cell">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-brand-acents flex items-center justify-center text-brand-accent text-xs font-bold">
                                {{ strtoupper(substr(optional(optional(optional($tx->installment)->studentUnitFee)->student->user)->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-brand-text">{{ optional(optional(optional($tx->installment)->studentUnitFee)->student->user)->name ?? 'N/A' }}</p>
                                <p class="text-xs text-brand-sub">{{ optional(optional(optional($tx->installment)->studentUnitFee)->student)->roll_number ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="table-cell text-sm text-brand-sub">
                        {{ optional($tx->installment)->installment_name ?? 'Installment' }}
                    </td>
                    <td class="table-cell font-bold text-brand-text">₹{{ number_format($tx->amount_paid, 2) }}</td>
                    <td class="table-cell">
                        <span class="badge {{ $tx->payment_method == 'cash' ? 'badge-warning' : 'badge-accent' }}">
                            {{ strtoupper($tx->payment_method) }}
                        </span>
                    </td>
                    <td class="table-cell text-sm text-brand-sub">
                        {{ \Carbon\Carbon::parse($tx->payment_date)->format('d M Y') }}
                    </td>
                    <td class="table-cell text-xs font-mono text-brand-sub">
                        {{ $tx->transaction_reference ?: '--' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-20 text-center text-brand-sub">No transactions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
    <div class="px-6 py-4 bg-brand-muted/30 border-t border-brand-border">
        {{ $transactions->links() }}
    </div>
    @endif
</div>

@endsection
