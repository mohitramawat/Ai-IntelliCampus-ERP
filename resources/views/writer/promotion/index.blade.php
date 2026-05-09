@extends('layouts.dashboard')
@section('content')

<div x-data="promotionManager()">

    {{-- Hero Banner --}}
    <div class="relative bg-gradient-to-r from-brand-accent to-sky-600 rounded-2xl p-6 sm:p-8 mb-8 overflow-hidden shadow-accent">
        <div class="absolute inset-0 opacity-10">
            <svg class="absolute right-0 bottom-0 h-full" viewBox="0 0 200 200" fill="white">
                <circle cx="160" cy="160" r="80"/><circle cx="40" cy="40" r="50"/>
            </svg>
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Academic Promotion</h1>
                <p class="text-white/70 text-sm mt-1">Promote students to the next semester/year &amp; auto-generate their new fee dues.</p>
            </div>
            <div class="flex items-center gap-2 bg-white/15 backdrop-blur-sm rounded-xl px-4 py-2.5">
                <span class="material-symbols-outlined text-white/80 text-[20px]">info</span>
                <span class="text-white/90 text-xs font-semibold">Select a batch → Pick students → Click Promote</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Left: Batch Selector --}}
        <div class="lg:col-span-3">
            <div class="card sticky top-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="kpi-icon bg-brand-acents">
                        <span class="material-symbols-outlined text-brand-accent text-[22px]">layers</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-brand-text">Select Batch</h3>
                        <p class="text-xs text-brand-sub">Step 1 of 2</p>
                    </div>
                </div>

                <select x-model="selectedBatch" @change="fetchStudents()" class="input">
                    <option value="">-- Choose Batch --</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}">{{ $batch->course->code }} — {{ $batch->name }}</option>
                    @endforeach
                </select>

                {{-- Loading Indicator --}}
                <div x-show="isLoading" x-transition class="mt-5 flex items-center justify-center gap-2 text-brand-accent text-sm font-semibold py-3">
                    <span class="material-symbols-outlined animate-spin text-lg">autorenew</span>
                    Fetching students…
                </div>

                {{-- Quick Stats --}}
                <div x-show="students.length > 0 && !isLoading" x-transition class="mt-5 space-y-3 pt-5 border-t border-brand-border">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-brand-sub font-medium">Total Students</span>
                        <span class="font-bold text-brand-text" x-text="students.length"></span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-brand-sub font-medium">Selected</span>
                        <span class="badge badge-accent font-bold" x-text="selectedStudents.length"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Students Table --}}
        <div class="lg:col-span-9">
            <div class="card p-0 overflow-hidden">

                {{-- Table Header Bar --}}
                <div class="px-6 py-5 bg-brand-muted border-b border-brand-border flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div class="flex items-center gap-3">
                        <div class="kpi-icon bg-status-infos">
                            <span class="material-symbols-outlined text-status-info text-[22px]">group</span>
                        </div>
                        <div>
                            <h3 class="section-title">Select Students</h3>
                            <p class="text-xs text-brand-sub font-medium" x-show="students.length > 0">
                                <span x-text="selectedStudents.length"></span> of <span x-text="students.length"></span> selected for promotion
                            </p>
                            <p class="text-xs text-brand-sub font-medium" x-show="students.length === 0">Step 2 — Choose batch first</p>
                        </div>
                    </div>

                    {{-- Promote Button --}}
                    <button x-show="selectedStudents.length > 0" x-transition
                            @click="promote()" 
                            class="btn-primary"
                            :class="{'opacity-50 cursor-not-allowed pointer-events-none': isPromoting}"
                            :disabled="isPromoting">
                        <span class="material-symbols-outlined text-[18px]">upgrade</span>
                        <span x-text="isPromoting ? 'Promoting…' : 'Promote & Generate Fees'"></span>
                    </button>
                </div>

                {{-- Empty State --}}
                <div x-show="students.length === 0 && !isLoading" class="py-20 flex flex-col items-center justify-center text-center px-6">
                    <div class="w-20 h-20 rounded-2xl bg-brand-muted flex items-center justify-center mb-5">
                        <span class="material-symbols-outlined text-brand-sub text-4xl">group_add</span>
                    </div>
                    <h4 class="text-lg font-bold text-brand-text mb-1">No Students Loaded</h4>
                    <p class="text-sm text-brand-sub max-w-xs">Select a batch from the dropdown on the left to view eligible students for promotion.</p>
                </div>

                {{-- Students Table --}}
                <div x-show="students.length > 0" x-cloak class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-brand-surface border-b border-brand-border">
                                <th class="table-head w-12">
                                    <input type="checkbox" 
                                           @change="toggleAll($event)" 
                                           :checked="selectedStudents.length === students.length && students.length > 0"
                                           class="rounded border-brand-border text-brand-accent focus:ring-brand-accent/30 cursor-pointer">
                                </th>
                                <th class="table-head">Student</th>
                                <th class="table-head">Enrollment</th>
                                <th class="table-head text-center">Current Unit</th>
                                <th class="table-head text-center">Promoted To</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-border">
                            <template x-for="student in students" :key="student.id">
                                <tr class="table-row-hover cursor-pointer" @click="toggleStudent(student.id)">
                                    <td class="table-cell" @click.stop>
                                        <input type="checkbox" 
                                               :value="student.id" 
                                               x-model="selectedStudents"
                                               class="rounded border-brand-border text-brand-accent focus:ring-brand-accent/30 cursor-pointer">
                                    </td>
                                    <td class="table-cell">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-brand-acents flex items-center justify-center text-brand-accent font-bold text-xs flex-shrink-0"
                                                 x-text="student.user.name.charAt(0).toUpperCase()"></div>
                                            <div>
                                                <p class="text-sm font-semibold text-brand-text" x-text="student.user.name"></p>
                                                <p class="text-xs text-brand-sub" x-text="student.user.email"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        <span class="text-sm font-mono font-medium text-brand-sub" x-text="student.enrollment_number"></span>
                                    </td>
                                    <td class="table-cell text-center">
                                        <span class="badge badge-info" x-text="'Sem ' + student.current_unit"></span>
                                    </td>
                                    <td class="table-cell text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <span class="material-symbols-outlined text-status-success text-[16px]">arrow_forward</span>
                                            <span class="badge badge-success font-bold" x-text="'Sem ' + (student.current_unit + 1)"></span>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function promotionManager() {
    return {
        selectedBatch: '',
        students: [],
        selectedStudents: [],
        isLoading: false,
        isPromoting: false,

        async fetchStudents() {
            if (!this.selectedBatch) {
                this.students = [];
                this.selectedStudents = [];
                return;
            }

            this.isLoading = true;
            this.selectedStudents = [];
            try {
                const res = await fetch(`{{ route('writer.promotion.students') }}?batch_id=${this.selectedBatch}`);
                const data = await res.json();
                this.students = data.students;
                this.selectedStudents = this.students.map(s => s.id.toString());
            } catch(e) {
                alert('Error fetching students');
            } finally {
                this.isLoading = false;
            }
        },

        toggleAll(e) {
            this.selectedStudents = e.target.checked 
                ? this.students.map(s => s.id.toString()) 
                : [];
        },

        toggleStudent(id) {
            const strId = id.toString();
            const idx = this.selectedStudents.indexOf(strId);
            idx > -1 ? this.selectedStudents.splice(idx, 1) : this.selectedStudents.push(strId);
        },

        async promote() {
            if (!confirm(`Promote ${this.selectedStudents.length} student(s) to the next semester?\n\nThis will also auto-generate their new fee installments.`)) return;

            this.isPromoting = true;
            try {
                const res = await fetch('{{ route("writer.promotion.promote") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        batch_id: this.selectedBatch,
                        student_ids: this.selectedStudents
                    })
                });
                const data = await res.json();
                if (data.success) {
                    alert('✅ ' + data.message);
                    this.fetchStudents();
                } else {
                    alert('❌ ' + (data.message || 'Promotion failed.'));
                }
            } catch (e) {
                alert("Network error. Please try again.");
            } finally {
                this.isPromoting = false;
            }
        }
    }
}
</script>
@endpush
