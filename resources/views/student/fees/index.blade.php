@extends('layouts.dashboard')
@section('content')

<div x-data="paymentManager()">

    {{-- Hero Banner --}}
    <div class="relative bg-gradient-to-r from-brand-accent to-sky-600 rounded-2xl p-6 sm:p-8 mb-8 overflow-hidden shadow-accent">
        <div class="absolute inset-0 opacity-10">
            <svg class="absolute right-0 bottom-0 h-full" viewBox="0 0 200 200" fill="white">
                <circle cx="160" cy="160" r="80"/><circle cx="40" cy="40" r="50"/>
            </svg>
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <p class="text-white/70 text-sm font-medium mb-1">Fee Management</p>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Fee Payments</h1>
                <p class="text-white/60 text-sm mt-1">Manage your semester fees seamlessly</p>
            </div>
            <div class="text-right hidden sm:block">
                <p class="text-white/60 text-xs font-semibold uppercase tracking-widest mb-0.5">Total Outstanding</p>
                <p class="text-3xl font-extrabold text-white">₹{{ number_format($fees->sum('unit_fee') - $fees->sum('total_paid'), 0) }}</p>
            </div>
        </div>
    </div>

    {{-- Fee Units --}}
    <div class="space-y-6">
        @foreach($fees as $fee)
        @php
            $balance = $fee->unit_fee - $fee->total_paid;
            $progress = $fee->unit_fee > 0 ? ($fee->total_paid / $fee->unit_fee) * 100 : 0;
            $nextInstallment = $fee->installments->where('status', '!=', 'paid')->first();
            $nextDueAmount = $nextInstallment ? ($nextInstallment->installment_amount - $nextInstallment->paid_amount) : 0;
        @endphp

        <div class="card p-0 overflow-hidden">

            {{-- Card Header --}}
            <div class="p-6 bg-brand-muted border-b border-brand-border flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="kpi-icon {{ $fee->status === 'paid' ? 'bg-status-successs' : 'bg-brand-acents' }}">
                        <span class="material-symbols-outlined {{ $fee->status === 'paid' ? 'text-status-success' : 'text-brand-accent' }} text-[22px]">
                            {{ $fee->status === 'paid' ? 'verified' : 'receipt_long' }}
                        </span>
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg font-bold text-brand-text">{{ $fee->unit_name }}</h3>
                            @if($fee->status === 'paid')
                                <span class="badge badge-success">Paid</span>
                            @elseif($fee->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-info">Partial</span>
                            @endif
                        </div>
                        <p class="text-sm text-brand-sub mt-0.5">Total: ₹{{ number_format($fee->unit_fee, 2) }}</p>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="w-full sm:w-64">
                    <div class="flex justify-between text-xs font-semibold mb-1.5">
                        <span class="text-status-success">Paid: ₹{{ number_format($fee->total_paid, 0) }}</span>
                        <span class="text-status-warning">Due: ₹{{ number_format($balance, 0) }}</span>
                    </div>
                    <div class="w-full h-2.5 bg-brand-border rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-brand-accent to-status-success rounded-full transition-all duration-700" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>

            @if($balance > 0)
            <div class="p-6">
                {{-- Payment Options --}}
                <h4 class="text-xs font-semibold text-brand-sub uppercase tracking-wider mb-4">Payment Options</h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- Pay Next Installment --}}
                    @if($nextInstallment)
                    <button @click="openPayment('{{ $fee->id }}', '{{ $fee->unit_name }} — {{ $nextInstallment->installment_name ?? "Installment" }}', {{ $nextDueAmount }})" 
                            class="p-5 rounded-2xl border border-brand-border bg-brand-surface hover:border-brand-accent/40 hover:shadow-card-md transition-all hover:-translate-y-0.5 text-left group">
                        <div class="flex justify-between items-center mb-3">
                            <div class="kpi-icon bg-brand-acents w-10 h-10">
                                <span class="material-symbols-outlined text-brand-accent text-[20px] group-hover:scale-110 transition-transform">calendar_clock</span>
                            </div>
                            <span class="text-xs font-semibold text-brand-sub">Due {{ $nextInstallment->due_date->format('d M') }}</span>
                        </div>
                        <h5 class="text-sm font-bold text-brand-text mb-1">Pay Next Installment</h5>
                        <p class="text-xl font-extrabold text-brand-accent">₹{{ number_format($nextDueAmount, 2) }}</p>
                    </button>
                    @endif

                    {{-- Pay Full Balance --}}
                    <button @click="openPayment('{{ $fee->id }}', '{{ $fee->unit_name }} — Full Clearance', {{ $balance }})" 
                            class="p-5 rounded-2xl border border-brand-border bg-brand-surface hover:border-status-success/40 hover:shadow-card-md transition-all hover:-translate-y-0.5 text-left group">
                        <div class="flex justify-between items-center mb-3">
                            <div class="kpi-icon bg-status-successs w-10 h-10">
                                <span class="material-symbols-outlined text-status-success text-[20px] group-hover:scale-110 transition-transform">task_alt</span>
                            </div>
                            <span class="text-xs font-semibold text-brand-sub">Clear Dues</span>
                        </div>
                        <h5 class="text-sm font-bold text-brand-text mb-1">Pay Full Balance</h5>
                        <p class="text-xl font-extrabold text-status-success">₹{{ number_format($balance, 2) }}</p>
                    </button>

                    {{-- Custom Amount --}}
                    <div class="p-5 rounded-2xl border border-brand-border bg-brand-surface">
                        <div class="flex justify-between items-center mb-3">
                            <div class="kpi-icon bg-status-warnings w-10 h-10">
                                <span class="material-symbols-outlined text-status-warning text-[20px]">tune</span>
                            </div>
                            <span class="text-xs font-semibold text-brand-sub">Custom</span>
                        </div>
                        <h5 class="text-sm font-bold text-brand-text mb-2">Partial Payment</h5>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-brand-sub font-bold text-sm">₹</span>
                                <input type="number" x-model="customAmount['{{ $fee->id }}']" min="1" max="{{ $balance }}" placeholder="Enter amount" class="input pl-7 text-sm">
                            </div>
                            <button @click="if(customAmount['{{ $fee->id }}'] > 0) openPayment('{{ $fee->id }}', '{{ $fee->unit_name }} — Partial', customAmount['{{ $fee->id }}'])" 
                                    class="btn-primary px-4">
                                Pay
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Installment Breakdown --}}
                <div class="mt-6 pt-6 border-t border-brand-border">
                    <h4 class="text-xs font-semibold text-brand-sub uppercase tracking-wider mb-4">Installment Breakdown</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($fee->installments as $ins)
                        <div class="p-3 rounded-xl bg-brand-muted border border-brand-border">
                            <p class="text-xs font-bold text-brand-text mb-0.5">{{ $ins->installment_name ?? 'Installment ' . $ins->installment_number }}</p>
                            <p class="text-[10px] text-brand-sub mb-2">Due: {{ $ins->due_date->format('d M Y') }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold {{ $ins->status === 'paid' ? 'text-status-success' : 'text-brand-text' }}">₹{{ number_format($ins->installment_amount, 0) }}</span>
                                @if($ins->status === 'paid')
                                    <span class="material-symbols-outlined text-status-success text-[16px]">check_circle</span>
                                @elseif($ins->paid_amount > 0)
                                    <span class="badge badge-accent text-[9px]">{{ round(($ins->paid_amount/$ins->installment_amount)*100) }}%</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            {{-- Fully Paid State --}}
            <div class="p-8 flex flex-col items-center text-center bg-status-successs/30">
                <div class="w-14 h-14 rounded-2xl bg-status-successs flex items-center justify-center mb-3">
                    <span class="material-symbols-outlined text-status-success text-3xl">verified</span>
                </div>
                <h4 class="text-lg font-bold text-status-success">All Clear!</h4>
                <p class="text-sm text-brand-sub mt-0.5">All fees for this unit are fully paid.</p>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Payment Modal --}}
    <div x-show="showModal" x-cloak
         class="fixed inset-0 bg-brand-text/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="bg-brand-surface rounded-2xl shadow-card-lg w-full max-w-md relative overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            {{-- === STATE 1: CHECKOUT FORM === --}}
            <div x-show="state === 'form'">
                <div class="p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="section-title">Secure Checkout</h3>
                        <button @click="closePayment()"
                                class="w-8 h-8 rounded-lg bg-brand-muted flex items-center justify-center text-brand-sub hover:bg-brand-border transition-colors">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>

                    <div class="bg-brand-muted p-5 rounded-xl border border-brand-border text-center mb-6">
                        <p class="text-xs font-semibold text-brand-sub uppercase tracking-widest mb-1" x-text="selectedName"></p>
                        <h4 class="text-3xl font-extrabold text-brand-text">₹<span x-text="selectedAmount"></span></h4>
                    </div>

                    <label class="block mb-6">
                        <span class="label">Payment Method</span>
                        <select x-model="method" class="input">
                            <option value="UPI">UPI / GPay / PhonePe</option>
                            <option value="Credit/Debit Card">Credit / Debit Card</option>
                            <option value="Net Banking">Net Banking</option>
                            <option value="Education Loan/EMI">Education Loan / EMI</option>
                        </select>
                    </label>

                    <button @click="processPayment()" class="btn-primary w-full justify-center py-3.5 text-base">
                        <span class="material-symbols-outlined text-[20px]">lock</span>
                        Pay Securely
                    </button>
                    <p class="text-center text-[10px] text-brand-sub font-semibold mt-3 uppercase tracking-wider">
                        Test Environment · No Real Money Deducted
                    </p>
                </div>
            </div>

            {{-- === STATE 2: PROCESSING ANIMATION === --}}
            <div x-show="state === 'processing'" class="p-8 py-16 text-center">
                <div class="relative w-20 h-20 mx-auto mb-6">
                    <svg class="w-full h-full animate-spin" viewBox="0 0 50 50">
                        <circle cx="25" cy="25" r="20" fill="none" stroke="#E4E9F0" stroke-width="4"></circle>
                        <circle cx="25" cy="25" r="20" fill="none" stroke="#0EA5E9" stroke-width="4"
                                stroke-dasharray="80 40" stroke-linecap="round"></circle>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-brand-text mb-1">Processing Payment…</h3>
                <p class="text-sm text-brand-sub">Please wait, do not close this window.</p>
            </div>

            {{-- === STATE 3: SUCCESS === --}}
            <div x-show="state === 'success'" class="bg-gradient-to-br from-status-success to-emerald-600 p-10 py-16 text-center text-white">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-5"
                     style="animation: successPop 0.5s ease-out">
                    <span class="material-symbols-outlined text-5xl">check_circle</span>
                </div>
                <h2 class="text-2xl font-extrabold mb-2">Payment Successful!</h2>
                <p class="text-white/80 text-sm mb-8" x-text="successMsg"></p>
                <button @click="window.location.reload()"
                        class="px-8 py-3 bg-white text-status-success rounded-xl font-bold text-sm hover:scale-105 transition-transform shadow-lg">
                    <span class="mr-1">🔄</span> View Updated Balance
                </button>
            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<style>
@keyframes successPop {
    0%   { transform: scale(0); opacity: 0; }
    60%  { transform: scale(1.2); opacity: 1; }
    100% { transform: scale(1); }
}
[x-cloak] { display: none !important; }
</style>

<script>
function paymentManager() {
    return {
        showModal: false,
        state: 'form',   // 'form' | 'processing' | 'success'
        selectedId: null,
        selectedName: '',
        selectedAmount: 0,
        method: 'UPI',
        customAmount: {},
        successMsg: '',

        openPayment(id, name, amount) {
            this.selectedId   = id;
            this.selectedName = name;
            this.selectedAmount = parseFloat(amount);
            this.state     = 'form';
            this.showModal = true;
        },

        closePayment() {
            if (this.state === 'processing') return; // can't close while processing
            this.showModal = false;
        },

        async processPayment() {
            this.state = 'processing';

            try {
                const res = await fetch('{{ route("student.fees.pay.simulate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        unit_fee_id: this.selectedId,
                        amount:      this.selectedAmount,
                        method:      this.method
                    })
                });

                const data = await res.json();

                if (data.success) {
                    this.successMsg = data.message;
                    this.state = 'success';   // ← show green success screen
                } else {
                    this.state = 'form';
                    alert('❌ ' + (data.message || 'Payment Failed'));
                }
            } catch (e) {
                this.state = 'form';
                alert('Network error. Please check your connection.');
            }
        }
    }
}
</script>
@endpush

