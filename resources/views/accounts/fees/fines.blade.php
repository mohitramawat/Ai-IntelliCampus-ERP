@extends('layouts.dashboard')
@section('content')

<div class="mb-8 flex flex-col md:items-start">
    <h1 class="text-2xl font-extrabold text-brand-text tracking-tight flex items-center gap-2">
        Fine Management
        <span class="badge badge-accent">Manual Override</span>
    </h1>
    <p class="text-brand-sub text-sm">Apply bulk fines to specific departments, courses, or batches.</p>
</div>

@if(session('success'))
    <div class="card bg-status-successs/20 border-status-success/30 mb-8 flex items-center gap-3 p-4">
        <span class="material-symbols-outlined text-status-success">check_circle</span>
        <p class="text-sm font-semibold text-status-success">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="card bg-status-dangers/20 border-status-danger/30 mb-8 flex items-center gap-3 p-4">
        <span class="material-symbols-outlined text-status-danger">error</span>
        <p class="text-sm font-semibold text-status-danger">{{ session('error') }}</p>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    {{-- Application Form --}}
    <div class="card h-fit">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-status-warnings flex items-center justify-center text-status-warning">
                <span class="material-symbols-outlined">gavel</span>
            </div>
            <div>
                <h3 class="section-title">Apply Group Fine</h3>
                <p class="section-sub">₹500 will be added to pending installments</p>
            </div>
        </div>

        <form action="{{ route('accounts.fines.apply') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="label">Select Department</label>
                <select name="department_id" class="input" required>
                    <option value="">-- Choose Department --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Select Course</label>
                    <select name="course_id" class="input" required>
                        <option value="">-- Choose Course --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Select Batch</label>
                    <select name="batch_id" class="input" required>
                        <option value="">-- Choose Batch --</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">Fine Amount (₹)</label>
                    <input type="number" name="fine_amount" value="500" class="input font-bold" required>
                </div>
                <div>
                    <label class="label">Deadline Date</label>
                    <input type="date" name="deadline_date" value="{{ date('Y-m-d') }}" class="input" required>
                    <p class="text-[10px] text-brand-sub mt-1">Fines will apply to installments due on or before this date.</p>
                </div>
            </div>

            <div>
                <label class="label">Fine Reason</label>
                <input type="text" name="reason" value="Late Fee Submission" class="input" required placeholder="e.g. Late Submission">
            </div>

            <div class="pt-4 border-t border-brand-border">
                <button type="submit" class="btn-primary w-full py-4 bg-status-danger hover:bg-status-danger/90 shadow-lg shadow-status-danger/20" onclick="return confirm('Are you sure you want to apply this fine to all selected students?')">
                    <span class="material-symbols-outlined text-[20px]">priority_high</span>
                    Apply Fine Now
                </button>
            </div>
        </form>
    </div>

    {{-- Rules & Logic Info --}}
    <div class="space-y-6">
        <div class="card bg-brand-muted/50 border-dashed border-2">
            <h3 class="text-sm font-bold text-brand-text mb-4">How it works?</h3>
            <ul class="space-y-3">
                <li class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-brand-accent text-[18px] mt-0.5">check_circle</span>
                    <p class="text-xs text-brand-sub leading-relaxed">System identifies all students in the selected <b>Course</b> and <b>Batch</b>.</p>
                </li>
                <li class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-brand-accent text-[18px] mt-0.5">check_circle</span>
                    <p class="text-xs text-brand-sub leading-relaxed">It checks for their <b>Active & Unpaid</b> installments.</p>
                </li>
                <li class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-brand-accent text-[18px] mt-0.5">check_circle</span>
                    <p class="text-xs text-brand-sub leading-relaxed">A record of <b>₹500</b> is added to the <code>installment_fines</code> table.</p>
                </li>
                <li class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-brand-accent text-[18px] mt-0.5">check_circle</span>
                    <p class="text-xs text-brand-sub leading-relaxed">Students will see this fine in their portal and must pay it along with the installment.</p>
                </li>
            </ul>
        </div>

        <div class="card border-status-warning/30 bg-status-warnings/5">
            <h3 class="text-sm font-bold text-status-warning mb-2">Important Disclaimer</h3>
            <p class="text-xs text-brand-sub leading-relaxed">Fines applied manually cannot be "undone" in bulk via this form. Each fine must be individually adjusted or cleared if applied by mistake. Please double-check your selection before hitting the button.</p>
        </div>
    </div>
</div>

@endsection
