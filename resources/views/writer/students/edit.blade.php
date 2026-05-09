@extends('layouts.dashboard')

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('writer.students.index') }}"
           class="w-9 h-9 rounded-xl bg-brand-muted border border-brand-border flex items-center justify-center
                  text-brand-sub hover:text-brand-text hover:border-brand-accent transition-all">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-black text-brand-text">Update Student Profile</h1>
            <p class="text-sm text-brand-sub mt-0.5">Editing record for <strong>{{ $student->user->name }}</strong> ({{ $student->enrollment_number }})</p>
        </div>
    </div>
</div>

{{-- Flash Errors --}}
@if($errors->any())
    <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200">
        <div class="flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-red-500 text-[20px]">error</span>
            <p class="text-sm font-bold text-red-600">Please fix the errors below:</p>
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li class="text-xs text-red-600">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('writer.students.update', $student->id) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- SECTION 1 — Account Status & Basic Info --}}
    <div class="card">
        <div class="flex items-center justify-between mb-5 pb-4 border-b border-brand-border">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-500 text-[20px]">manage_accounts</span>
                </div>
                <div>
                    <h2 class="text-base font-bold text-brand-text">Account Information</h2>
                    <p class="text-xs text-brand-sub">Manage identity and status</p>
                </div>
            </div>
            <div>
                <label class="label mb-1 text-right text-[10px]" for="is_active">Status</label>
                <select name="is_active" id="is_active" class="input py-1.5 min-w-[120px]">
                    <option value="1" {{ old('is_active', $student->is_active) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', $student->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="label" for="name">Full Name *</label>
                <input id="name" name="name" type="text"
                       value="{{ old('name', $student->user->name) }}"
                       class="input @error('name') border-red-400 @enderror">
                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="email">Email Address *</label>
                <input id="email" name="email" type="email"
                       value="{{ old('email', $student->user->email) }}"
                       class="input @error('email') border-red-400 @enderror">
                @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- SECTION 2 — Academic Details --}}
    <div class="card">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-brand-border">
            <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600 text-[20px]">school</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-brand-text">Academic Details</h2>
                <p class="text-xs text-brand-sub">Verify department, course, and enrollment</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="label" for="dept_select">Department *</label>
                <select id="dept_select" class="input" onchange="filterCourses(this.value)">
                    <option value="">— Select Department —</option>
                </select>
            </div>
            <div>
                <label class="label" for="course_select">Course *</label>
                <select id="course_select" class="input" disabled onchange="filterBatches(this.value)">
                    <option value="">— Select Department First —</option>
                </select>
            </div>
            <div>
                <label class="label" for="batch_select">Batch *</label>
                <select id="batch_select" name="batch_id" class="input @error('batch_id') border-red-400 @enderror" disabled>
                    <option value="">— Select Course First —</option>
                </select>
                @error('batch_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="label" for="enroll">Enrollment No.</label>
                <input id="enroll" type="text" readonly
                       value="{{ $student->enrollment_number }}"
                       class="input bg-brand-muted text-brand-sub cursor-not-allowed">
            </div>
            <div>
                <label class="label" for="roll_number">Roll Number</label>
                <input id="roll_number" name="roll_number" type="text"
                       value="{{ old('roll_number', $student->roll_number) }}"
                       class="input" placeholder="e.g. MCA2401">
            </div>
            <div>
                <label class="label" for="admission_date">Admission Date *</label>
                <input id="admission_date" name="admission_date" type="date"
                       value="{{ old('admission_date', $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('Y-m-d') : '') }}"
                       class="input @error('admission_date') border-red-400 @enderror">
                @error('admission_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- SECTION 3 — Personal Details --}}
    <div class="card">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-brand-border">
            <div class="w-9 h-9 rounded-xl bg-sky-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-sky-500 text-[20px]">person</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-brand-text">Personal Details</h2>
                <p class="text-xs text-brand-sub">Biographic and demographic information</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="label" for="gender">Gender</label>
                <select id="gender" name="gender" class="input">
                    <option value="">— Select —</option>
                    @foreach(['male','female','other'] as $g)
                        <option value="{{ $g }}" {{ old('gender', $student->gender) == $g ? 'selected' : '' }}>{{ ucfirst($g) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="date_of_birth">Date of Birth</label>
                <input id="date_of_birth" name="date_of_birth" type="date"
                       value="{{ old('date_of_birth', $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('Y-m-d') : '') }}"
                       class="input">
            </div>
            <div>
                <label class="label" for="category">Category</label>
                <select id="category" name="category" class="input">
                    <option value="">— Select —</option>
                    @foreach(['General','OBC','SC','ST','EWS'] as $cat)
                        <option value="{{ $cat }}" {{ old('category', $student->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label" for="contact_number">Contact Number</label>
                <input id="contact_number" name="contact_number" type="text"
                       value="{{ old('contact_number', $student->contact_number) }}"
                       class="input" placeholder="+91 9876543210">
            </div>
        </div>
    </div>

    {{-- SECTION 4 — Guardian Details --}}
    <div class="card">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-brand-border">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-500 text-[20px]">family_restroom</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-brand-text">Guardian Details</h2>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="label" for="father_name">Father's Name</label>
                <input id="father_name" name="father_name" type="text"
                       value="{{ old('father_name', $student->father_name) }}"
                       class="input">
            </div>
            <div>
                <label class="label" for="mother_name">Mother's Name</label>
                <input id="mother_name" name="mother_name" type="text"
                       value="{{ old('mother_name', $student->mother_name) }}"
                       class="input">
            </div>
        </div>
    </div>

    {{-- SECTION 5 — Address --}}
    <div class="card">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-brand-border">
            <div class="w-9 h-9 rounded-xl bg-brand-muted flex items-center justify-center">
                <span class="material-symbols-outlined text-brand-sub text-[20px]">location_on</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-brand-text">Address</h2>
            </div>
        </div>
        <textarea id="address" name="address" rows="2" class="input resize-none w-full">{{ old('address', $student->address) }}</textarea>
    </div>

    {{-- SECTION 6 — Documents (Update/Re-upload) --}}
    <div class="card">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-brand-border">
            <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-red-500 text-[20px]">upload_file</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-brand-text">Document Management</h2>
                <p class="text-xs text-brand-sub">Upload new files to replace existing ones</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach([
                ['key' => '10th_marksheet', 'label' => '10th Marksheet'],
                ['key' => '12th_marksheet', 'label' => '12th Marksheet'],
                ['key' => 'aadhaar',        'label' => 'Aadhaar Card'],
                ['key' => 'tc',             'label' => 'Transfer Certificate'],
            ] as $doc)
            @php $exists = $student->documents->where('document_type', $doc['key'])->first(); @endphp
            <div>
                <label class="label" for="{{ $doc['key'] }}">
                    {{ $doc['label'] }}
                    @if($exists)
                        <span class="text-[10px] text-status-success font-bold ml-1 flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[12px]">check_circle</span> Already Uploaded
                        </span>
                    @endif
                </label>
                <input id="{{ $doc['key'] }}" name="{{ $doc['key'] }}" type="file"
                       class="block w-full text-sm text-brand-sub border border-brand-border rounded-xl px-3 py-2 bg-brand-muted">
                @error($doc['key'])<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            @endforeach
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex flex-col sm:flex-row gap-3 pb-10">
        <button type="submit" class="btn-primary w-full sm:w-auto justify-center px-10 py-3 text-base">
            <span class="material-symbols-outlined text-[20px]">save</span>
            Save Changes
        </button>
        <a href="{{ route('writer.students.index') }}" class="btn-secondary w-full sm:w-auto justify-center">
            Cancel
        </a>
    </div>

</form>

{{-- Cascading Logic --}}
<script>
const TREE = {!! $departmentsJson !!};
const deptSel = document.getElementById('dept_select');
const courseSel = document.getElementById('course_select');
const batchSel = document.getElementById('batch_select');

TREE.forEach(dept => deptSel.add(new Option(dept.name, dept.id)));

function filterCourses(deptId, preSelectedCourseId = null) {
    courseSel.innerHTML = '<option value="">— Select Course —</option>';
    batchSel.innerHTML = '<option value="">— Select Course First —</option>';
    courseSel.disabled = true; batchSel.disabled = true;
    if (!deptId) return;
    const dept = TREE.find(d => d.id == deptId);
    if (!dept) return;
    dept.courses.forEach(c => courseSel.add(new Option(c.name, c.id)));
    courseSel.disabled = false;
    if (preSelectedCourseId) {
        courseSel.value = preSelectedCourseId;
        filterBatches(preSelectedCourseId);
    }
}

function filterBatches(courseId, preSelectedBatchId = null) {
    batchSel.innerHTML = '<option value="">— Select Batch —</option>';
    batchSel.disabled = true;
    if (!courseId) return;
    let course = null;
    for (const d of TREE) { course = d.courses.find(c => c.id == courseId); if (course) break; }
    if (!course) return;
    course.batches.forEach(b => batchSel.add(new Option(b.name, b.id)));
    batchSel.disabled = false;
    if (preSelectedBatchId) batchSel.value = preSelectedBatchId;
}

// Initial restoration
(function restore() {
    const currentDeptId  = "{{ $student->batch->course->department_id }}";
    const currentCourseId = "{{ $student->batch->course_id }}";
    const currentBatchId  = "{{ $student->batch_id }}";

    if (currentDeptId) {
        deptSel.value = currentDeptId;
        filterCourses(currentDeptId, currentCourseId);
        filterBatches(currentCourseId, currentBatchId);
    }
})();
</script>

@endsection
