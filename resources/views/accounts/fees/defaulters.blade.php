@extends('layouts.dashboard')
@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-brand-text tracking-tight flex items-center gap-2">
            Fee Defaulters
            <span class="badge badge-danger">High Priority</span>
        </h1>
        <p class="text-brand-sub text-sm">Students with overdue installments as of {{ now()->format('d M Y') }}</p>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="px-6 py-4 bg-status-danger/5 border-b border-brand-border flex items-center gap-3">
        <span class="material-symbols-outlined text-status-danger">warning</span>
        <p class="text-xs font-bold text-status-danger uppercase tracking-widest">Total Overdue Installments: {{ $defaulters->total() }}</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-brand-border">
                    <th class="table-head">Student</th>
                    <th class="table-head">Batch</th>
                    <th class="table-head">Installment</th>
                    <th class="table-head">Due Date</th>
                    <th class="table-head">Days Overdue</th>
                    <th class="table-head">Pending Amount</th>
                    <th class="table-head">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @forelse($defaulters as $installment)
                @php 
                    $overdueDays = \Carbon\Carbon::parse($installment->due_date)->diffInDays(now());
                    $pending = $installment->installment_amount - $installment->paid_amount;
                @endphp
                <tr class="table-row-hover">
                    <td class="table-cell">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-status-danger/10 flex items-center justify-center text-status-danger text-xs font-bold">
                                {{ strtoupper(substr(optional($installment->studentUnitFee->student->user)->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-brand-text">{{ optional($installment->studentUnitFee->student->user)->name ?? 'N/A' }}</p>
                                <p class="text-xs text-brand-sub">{{ $installment->studentUnitFee->student->roll_number }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="table-cell text-xs font-bold text-brand-sub">
                        {{ optional($installment->studentUnitFee->student->batch)->name }}
                    </td>
                    <td class="table-cell text-sm text-brand-text">
                        {{ $installment->installment_name ?? 'Installment #' . $installment->installment_number }}
                    </td>
                    <td class="table-cell text-sm font-semibold text-brand-sub">
                        {{ \Carbon\Carbon::parse($installment->due_date)->format('d M Y') }}
                    </td>
                    <td class="table-cell">
                        <span class="px-2 py-1 rounded text-[10px] font-bold {{ $overdueDays > 30 ? 'bg-status-danger text-white' : 'bg-status-warning/20 text-status-warning' }}">
                            {{ $overdueDays }} DAYS
                        </span>
                    </td>
                    <td class="table-cell font-extrabold text-status-danger">₹{{ number_format($pending, 2) }}</td>
                    <td class="table-cell">
                        <span class="badge badge-danger">OVERDUE</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-20 text-center text-brand-sub">Great! No defaulters found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($defaulters->hasPages())
    <div class="px-6 py-4 bg-brand-muted/30 border-t border-brand-border">
        {{ $defaulters->links() }}
    </div>
    @endif
</div>

@endsection
