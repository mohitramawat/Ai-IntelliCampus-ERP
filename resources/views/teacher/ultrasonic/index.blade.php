@extends('layouts.dashboard')
@section('content')

<style>
:root {
    --us-bg: #F8FAFC; 
    --us-surface: #FFFFFF; 
    --us-border: #E2E8F0; 
    --us-muted: #F1F5F9;
    --us-text: #0F172A; 
    --us-sub: #64748B;
    --us-accent: #0284C7; 
    --us-accentD: #0369A1; 
    --us-accentS: #E0F2FE;
    --us-emerald: #10B981; 
    --us-emeraldS: #D1FAE5;
}

/* ══════ ANIMATIONS ══════ */
.us-fade { opacity: 0; transform: translateY(16px); animation: usFade .5s cubic-bezier(.25,.46,.45,.94) var(--d,0s) forwards; }
@keyframes usFade { to { opacity: 1; transform: translateY(0); } }
@keyframes radarPulseLight {
    0% { width: 0; height: 0; opacity: 1; }
    100% { width: 350px; height: 350px; opacity: 0; }
}

/* ══════ CARD & LAYOUT ══════ */
.us-card {
    background: var(--us-surface);
    border: 1px solid var(--us-border);
    border-radius: 24px;
    padding: 32px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);
}
.us-header {
    display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 32px;
}
.us-title {
    font-size: clamp(26px, 4vw, 34px); font-weight: 900; color: var(--us-text); letter-spacing: -0.03em; margin: 0 0 6px;
}
.us-subtitle {
    font-size: 14px; color: var(--us-sub); margin: 0; max-width: 450px; line-height: 1.5;
}
.us-beta-badge {
    display: inline-flex; align-items: center; padding: 4px 10px; background: var(--us-accentS); border-radius: 99px;
    font-size: 10px; font-weight: 800; color: var(--us-accent); text-transform: uppercase; letter-spacing: 0.1em;
    vertical-align: middle; margin-left: 12px;
}

/* ══════ FORM GRID (1000$ Look) ══════ */
.us-form-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px;
}
.us-field { display: flex; flex-direction: column; gap: 8px; }
.us-label {
    font-size: 11px; font-weight: 700; color: var(--us-sub); text-transform: uppercase; letter-spacing: 0.08em;
}
.us-select {
    width: 100%; padding: 14px 16px; background: var(--us-surface);
    border: 1px solid var(--us-border); border-radius: 14px;
    font-size: 14px; font-weight: 600; color: var(--us-text);
    transition: all 0.2s; cursor: pointer; appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748B' viewBox='0 0 16 16'%3E%3Cpath d='M4.646 6.646a.5.5 0 0 1 .708 0L8 9.293l2.646-2.647a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 14px center; background-size: 16px; padding-right: 40px;
}
.us-select:focus {
    border-color: var(--us-accent); box-shadow: 0 0 0 4px var(--us-accentS); outline: none;
}
.us-select:disabled {
    opacity: 0.6; cursor: not-allowed; background-color: var(--us-muted); color: var(--us-sub); border-color: var(--us-border);
}

/* ══════ BUTTONS ══════ */
.us-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 10px;
    padding: 16px 36px; border: none; cursor: pointer; border-radius: 16px;
    font-size: 15px; font-weight: 800; letter-spacing: 0.03em; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.us-btn-primary {
    background: var(--us-text); color: #fff; box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2);
}
.us-btn-primary:hover {
    transform: translateY(-2px); box-shadow: 0 15px 35px -5px rgba(15, 23, 42, 0.3); background: #000;
}
.us-btn-primary:disabled { opacity: 0.5; transform: none; cursor: not-allowed; }

.us-btn-danger {
    background: #FEF2F2; color: #EF4444; border: 1px solid #FEE2E2;
}
.us-btn-danger:hover {
    background: #FEE2E2; transform: translateY(-2px);
}

/* ══════ RADAR & ACTIVE STATE ══════ */
.us-radar-container {
    position: relative; width: 200px; height: 200px; margin: 0 auto 30px; display: flex; align-items: center; justify-content: center;
}
.us-radar-circle {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
    border-radius: 50%; border: 1.5px solid var(--us-accent);
    animation: radarPulseLight 2s infinite cubic-bezier(0.1, 0.8, 0.4, 1); pointer-events: none;
}
.us-radar-core {
    width: 80px; height: 80px; background: var(--us-accentS); border-radius: 50%;
    display: flex; align-items: center; justify-content: center; z-index: 10;
    border: 2px solid var(--us-accent); box-shadow: 0 0 30px rgba(2, 132, 199, 0.2);
}

.us-token-box {
    display: inline-flex; align-items: center; gap: 12px; background: var(--us-muted);
    border: 1px solid var(--us-border); padding: 12px 24px; border-radius: 16px; margin-bottom: 40px;
}
.us-token-label { font-size: 12px; font-weight: 700; color: var(--us-sub); text-transform: uppercase; letter-spacing: 0.1em; }
.us-token-value { font-size: 28px; font-weight: 900; color: var(--us-text); letter-spacing: 0.2em; font-family: monospace; }
</style>

<div x-data="ultrasonicTeacher()">

    <div class="us-header us-fade" style="--d: 0.05s">
        <div>
            <h1 class="us-title">
                Ultrasonic Broadcast <span class="us-beta-badge">Beta</span>
            </h1>
            <p class="us-subtitle">Transmit high-frequency acoustic tokens to authenticate student presence securely within the classroom.</p>
        </div>
    </div>

    {{-- STATE: CONFIG --}}
    <div class="us-card us-fade" style="--d: 0.1s" x-show="!isActive" x-cloak style="display: {{ $activeSession ? 'none' : 'block' }}">
        
        <div class="us-form-grid">
            <div class="us-field">
                <label class="us-label">Course</label>
                <select x-model="formData.course_id" @change="onCourseChange()" class="us-select">
                    <option value="">— Select Course —</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="us-field">
                <label class="us-label">Semester</label>
                <select x-model="formData.semester" @change="onSemesterChange()" :disabled="!formData.course_id" class="us-select">
                    <option value="">— Select Course First —</option>
                    <template x-for="sem in availableSemesters" :key="sem">
                        <option :value="sem" x-text="`Semester ${sem}`"></option>
                    </template>
                </select>
            </div>
            <div class="us-field">
                <label class="us-label">Subject</label>
                <select x-model="formData.subject_id" :disabled="!formData.semester" class="us-select">
                    <option value="">— Select Semester First —</option>
                    <template x-for="subj in filteredSubjects" :key="subj.id">
                        <option :value="subj.id" x-text="`${subj.code} - ${subj.name}`"></option>
                    </template>
                </select>
            </div>
            <div class="us-field">
                <label class="us-label">Batch</label>
                <select x-model="formData.batch_id" :disabled="!formData.course_id" class="us-select">
                    <option value="">— Select Course First —</option>
                    <template x-for="batch in filteredBatches" :key="batch.id">
                        <option :value="batch.id" x-text="batch.name"></option>
                    </template>
                </select>
            </div>
            <div class="us-field">
                <label class="us-label">Period</label>
                <select x-model="formData.period_number" class="us-select">
                    @foreach(range(1, 8) as $p)
                        <option value="{{ $p }}">Period {{ $p }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-top: 32px; display: flex; justify-content: flex-end;">
            <button @click="startBroadcast()" :disabled="!isFormValid || loading" class="us-btn us-btn-primary">
                <span class="material-symbols-outlined" style="font-size: 20px;">sensors</span>
                <span x-text="loading ? 'INITIALIZING...' : 'START BROADCAST'"></span>
            </button>
        </div>
    </div>

    {{-- STATE: ACTIVE --}}
    <div class="us-card us-fade" style="--d: 0.15s; text-align: center; padding: 60px 32px;" x-show="isActive" x-cloak style="display: {{ $activeSession ? 'block' : 'none' }}">
        
        <div class="us-radar-container">
            <div class="us-radar-circle" style="animation-delay: 0s;"></div>
            <div class="us-radar-circle" style="animation-delay: 0.5s;"></div>
            <div class="us-radar-circle" style="animation-delay: 1s;"></div>
            <div class="us-radar-core">
                <span class="material-symbols-outlined" style="font-size: 36px; color: var(--us-accent);">surround_sound</span>
            </div>
        </div>

        <h2 style="font-size: 20px; font-weight: 800; color: var(--us-text); margin: 0 0 20px;">BROADCASTING IN PROGRESS</h2>
        
        <div class="us-token-box">
            <span class="us-token-label">Audio Token:</span>
            <span class="us-token-value">{{ $activeSession ? $activeSession->ultrasonic_token : '----' }}</span>
        </div>

        <div style="display: flex; gap: 40px; justify-content: center; margin-bottom: 40px;">
            <div style="text-align: center;">
                <p style="font-size: 11px; font-weight: 700; color: var(--us-sub); text-transform: uppercase;">Verified Present</p>
                <p style="font-size: 24px; font-weight: 900; color: var(--us-emerald);" x-text="presentCount">{{ $activeSession ? $activeSession->present_count : 0 }}</p>
            </div>
            <div style="text-align: center;">
                <p style="font-size: 11px; font-weight: 700; color: var(--us-sub); text-transform: uppercase;">Total Class Size</p>
                <p style="font-size: 24px; font-weight: 900; color: var(--us-text);">{{ $activeSession ? $activeSession->total_students : 0 }}</p>
            </div>
        </div>

        <button @click="stopBroadcast()" class="us-btn us-btn-danger" style="margin-bottom: 40px;">
            <span class="material-symbols-outlined">stop_circle</span>
            TERMINATE SESSION
        </button>

        {{-- LIVE STUDENT LIST --}}
        <div style="text-align: left; max-width: 600px; margin: 0 auto; background: var(--us-muted); border-radius: 16px; padding: 20px; border: 1px solid var(--us-border);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 14px; font-weight: 800; color: var(--us-text); margin: 0; display: flex; align-items: center; gap: 8px;">
                    <span class="material-symbols-outlined" style="font-size: 18px; color: var(--us-emerald);">how_to_reg</span>
                    Live Verification Feed
                </h3>
                <span style="font-size: 11px; color: var(--us-sub); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 14px; animation: spin 2s linear infinite;">sync</span>
                    Auto-updating
                </span>
            </div>
            
            <div style="max-height: 250px; overflow-y: auto; padding-right: 8px;">
                <template x-if="markedStudents.length === 0">
                    <div style="text-align: center; padding: 30px 0; color: var(--us-sub); font-size: 13px; font-style: italic;">
                        Waiting for students to scan the token...
                    </div>
                </template>
                <template x-for="record in markedStudents" :key="record.id">
                    <div style="display: flex; justify-content: space-between; align-items: center; background: var(--us-surface); padding: 12px 16px; border-radius: 12px; border: 1px solid var(--us-border); margin-bottom: 8px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--us-emeraldS); color: var(--us-emeraldD); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px;" x-text="record.student.user.name.substring(0,2).toUpperCase()"></div>
                            <div>
                                <p style="font-size: 14px; font-weight: 700; color: var(--us-text); margin: 0;" x-text="record.student.user.name"></p>
                                <p style="font-size: 11px; color: var(--us-sub); margin: 0;" x-text="record.student.roll_number || 'No Roll Number'"></p>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 10px; font-weight: 800; color: var(--us-emerald); background: var(--us-emeraldS); padding: 3px 8px; border-radius: 99px; text-transform: uppercase;">Verified</span>
                            <p style="font-size: 10px; color: var(--us-sub); margin: 4px 0 0 0;" x-text="formatTime(record.marked_at)"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('ultrasonicTeacher', () => ({
        isActive: {{ $activeSession ? 'true' : 'false' }},
        loading: false,
        presentCount: {{ $activeSession ? $activeSession->present_count : 0 }},
        allBatches: @json($batches),
        allSubjects: @json($subjects),
        filteredBatches: [], filteredSubjects: [], availableSemesters: [],
        markedStudents: [],
        _pollInterval: null,
        
        formData: {
            course_id: '', semester: '', batch_id: '', subject_id: '', period_number: 1, teacher_lat: 0, teacher_long: 0
        },

        get isFormValid() {
            return this.formData.course_id && this.formData.semester && this.formData.batch_id && this.formData.subject_id;
        },

        init() {
            @if($activeSession)
                this.initAudio("{{ $activeSession->ultrasonic_token }}");
                // Needs user interaction to play audio in modern browsers
                document.addEventListener('click', () => {
                    if(this.audioCtx && this.audioCtx.state === 'suspended') this.audioCtx.resume();
                }, {once: true});
                
                // Start live polling
                this.startPolling();
            @endif
        },

        startPolling() {
            this.pollStudents();
            this._pollInterval = setInterval(() => this.pollStudents(), 3000); // Every 3 seconds
        },

        async pollStudents() {
            try {
                const res = await fetch('{{ url("teacher/attendance/session") }}/{{ $activeSession->id ?? 0 }}/students');
                const data = await res.json();
                this.markedStudents = data;
                this.presentCount = data.length;
            } catch(e) {}
        },

        formatTime(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            return d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');
        },

        onCourseChange() {
            const cid = this.formData.course_id;
            this.formData.semester = ''; this.formData.batch_id = ''; this.formData.subject_id = '';
            if (!cid) { this.filteredBatches = []; this.availableSemesters = []; return; }
            this.filteredBatches = this.allBatches.filter(b => b.course_id == cid);
            const batch24 = this.filteredBatches.find(b => b.name && b.name.includes('24'));
            if (batch24) this.formData.batch_id = batch24.id;
            const courseSubjects = this.allSubjects.filter(s => s.course_id == cid);
            this.availableSemesters = [...new Set(courseSubjects.map(s => parseInt(s.semester)))].sort((a, b) => a - b);
        },

        onSemesterChange() {
            const cid = this.formData.course_id; const sem = this.formData.semester;
            this.formData.subject_id = '';
            if (!cid || !sem) { this.filteredSubjects = []; return; }
            this.filteredSubjects = this.allSubjects.filter(s => s.course_id == cid && s.semester == sem);
        },

        async startBroadcast() {
            this.loading = true;
            try {
                const res = await fetch('{{ route("teacher.ultrasonic.start") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(this.formData)
                });
                const data = await res.json();
                if(data.success) { window.location.reload(); } else { alert(data.message); this.loading = false; }
            } catch(e) { alert("Error starting broadcast."); this.loading = false; }
        },

        async stopBroadcast() {
            if(!confirm("Stop ultrasonic broadcast?")) return;
            const res = await fetch('{{ route("teacher.attendance.close") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ lecture_session_id: {{ $activeSession->id ?? 0 }} })
            });
            if(res.ok) window.location.reload();
        },

        audioCtx: null,
        initAudio(token) {
            if (!this.audioCtx) this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const playDigit = (digit, index) => {
                const freq = 18000 + (parseInt(digit) * 200);
                const osc = this.audioCtx.createOscillator();
                const gain = this.audioCtx.createGain();
                osc.type = 'sine'; osc.frequency.value = freq;
                osc.connect(gain); gain.connect(this.audioCtx.destination);
                const startTime = this.audioCtx.currentTime + (index * 0.5);
                osc.start(startTime); osc.stop(startTime + 0.4);
            };
            setInterval(() => {
                for(let i=0; i<token.length; i++) playDigit(token[i], i);
            }, 3000);
        }
    }));
});
</script>
@endpush
