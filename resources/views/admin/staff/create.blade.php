@extends('layouts.dashboard')
@section('content')

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('admin.staff.index') }}" class="text-brand-sub hover:text-brand-accent transition-colors">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <h1 class="text-2xl font-extrabold text-brand-text tracking-tight">Add New Staff</h1>
        </div>
        <p class="text-sm text-brand-sub ml-7">Create login in <code class="text-xs bg-brand-muted px-1.5 py-0.5 rounded">users</code> table + profile in <code class="text-xs bg-brand-muted px-1.5 py-0.5 rounded">staff_profiles</code> table</p>
    </div>
</div>

{{-- Validation Errors --}}
@if($errors->any())
<div class="bg-status-dangers border border-status-danger/20 text-status-danger px-5 py-3 rounded-xl mb-6 text-sm font-medium">
    <ul class="list-disc list-inside space-y-0.5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.staff.store') }}" method="POST">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Login Credentials --}}
        <div class="lg:col-span-1">
            <div class="card">
                <div class="flex items-center gap-3 mb-5">
                    <div class="kpi-icon bg-status-dangers">
                        <span class="material-symbols-outlined text-status-danger text-[22px]">lock</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-brand-text">Login Credentials</h3>
                        <p class="text-xs text-brand-sub">Stored in <code class="text-[10px] bg-brand-muted px-1 py-0.5 rounded">users</code> table</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="label">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="input" placeholder="e.g. Rajesh Kumar" required>
                    </div>

                    <div>
                        <label class="label">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="input" placeholder="e.g. rajesh@intellicampus.com" required>
                    </div>

                    <div>
                        <label class="label">Password *</label>
                        <input type="text" name="password" value="{{ old('password') }}" class="input font-mono" placeholder="Set a strong password" required>
                        <p class="text-xs text-brand-sub mt-1">Share this with the staff member securely.</p>
                    </div>

                    <div>
                        <label class="label">System Role *</label>
                        <select name="role" class="input" required>
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                    {{ ucfirst($role === 'accounts' ? 'Accountant' : ($role === 'hod' ? 'HOD (Head of Dept.)' : $role)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Personal Details --}}
        <div class="lg:col-span-2">
            <div class="card">
                <div class="flex items-center gap-3 mb-5">
                    <div class="kpi-icon bg-brand-acents">
                        <span class="material-symbols-outlined text-brand-accent text-[22px]">person</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-brand-text">Personal Details</h3>
                        <p class="text-xs text-brand-sub">Stored in <code class="text-[10px] bg-brand-muted px-1 py-0.5 rounded">staff_profiles</code> table</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Employee Code</label>
                        <input type="text" name="employee_code" value="{{ old('employee_code') }}" class="input" placeholder="e.g. EMP-001">
                    </div>

                    <div>
                        <label class="label">Department</label>
                        <select name="department_id" class="input">
                            <option value="">-- Optional --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="label">Phone Number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}" class="input" placeholder="e.g. 9876543210">
                    </div>

                    <div>
                        <label class="label">Gender</label>
                        <select name="gender" class="input">
                            <option value="">-- Select --</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="label">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="input">
                    </div>

                    <div>
                        <label class="label">Joining Date</label>
                        <input type="date" name="joining_date" value="{{ old('joining_date') }}" class="input">
                    </div>

                    <div class="md:col-span-2">
                        <label class="label">Address</label>
                        <textarea name="address" class="input" rows="2" placeholder="Full residential address">{{ old('address') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="btn-primary py-3 px-6">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    Create Staff Account
                </button>
                <a href="{{ route('admin.staff.index') }}" class="btn-secondary py-3 px-6">Cancel</a>
            </div>
        </div>

    </div>
</form>

@endsection
