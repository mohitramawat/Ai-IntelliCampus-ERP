@extends('layouts.dashboard')
@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-brand-text tracking-tight">Revenue Reports</h1>
        <p class="text-brand-sub text-sm">Collection analytics and trends</p>
    </div>
    <div class="flex bg-brand-muted p-1 rounded-xl">
        <a href="{{ route('accounts.fees.reports', ['type' => 'daily']) }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $type == 'daily' ? 'bg-white shadow-sm text-brand-accent' : 'text-brand-sub' }}">Daily</a>
        <a href="{{ route('accounts.fees.reports', ['type' => 'weekly']) }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $type == 'weekly' ? 'bg-white shadow-sm text-brand-accent' : 'text-brand-sub' }}">Weekly</a>
        <a href="{{ route('accounts.fees.reports', ['type' => 'monthly']) }}" class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $type == 'monthly' ? 'bg-white shadow-sm text-brand-accent' : 'text-brand-sub' }}">Monthly</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card">
        <h3 class="section-title mb-6">Collection Trend ({{ ucfirst($type) }})</h3>
        
        @if($data->isEmpty())
            <div class="flex flex-col items-center justify-center h-64 text-brand-sub">
                <span class="material-symbols-outlined text-4xl mb-2">bar_chart_off</span>
                <p class="text-sm">No payment data found for this period.</p>
            </div>
        @else
            <div class="flex items-end gap-2 h-64 mb-4 px-4 border-b border-brand-border">
                @php $max = $data->max('total') ?: 1; @endphp
                @foreach($data as $row)
                <div class="flex-1 flex flex-col items-center group relative min-w-[30px]">
                    <div class="w-full bg-brand-accent/20 hover:bg-brand-accent rounded-t-lg transition-all duration-300 relative cursor-pointer" 
                         style="height: {{ max(($row->total / $max) * 100, 5) }}%">
                        <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-brand-text text-white text-[10px] py-1.5 px-2 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                            ₹{{ number_format($row->total, 0) }}
                        </div>
                    </div>
                    <div class="mt-2 text-[10px] font-bold text-brand-sub truncate w-full text-center">
                        {{ $row->label }}
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="card">
        <h3 class="section-title mb-6">Collection Summary</h3>
        <div class="space-y-4">
            <div class="p-4 rounded-2xl bg-brand-muted">
                <p class="text-[10px] font-bold text-brand-sub uppercase tracking-wider mb-1">Total Period Collection</p>
                <p class="text-2xl font-extrabold text-brand-text">₹{{ number_format($data->sum('total'), 2) }}</p>
            </div>
            
            <div class="space-y-3 pt-4">
                <h4 class="text-xs font-bold text-brand-text mb-2">Detailed Breakdown</h4>
                @foreach($data->sortByDesc('total')->take(5) as $row)
                <div class="flex justify-between items-center text-sm">
                    <span class="text-brand-sub font-medium">{{ $row->label }}</span>
                    <span class="text-brand-text font-bold">₹{{ number_format($row->total, 0) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
