@extends('layouts.dashboard')

@section('content')

{{-- Inline styles — layout has no @stack('styles') --}}
<style>
:root {
    --ta-bg:#F5F7FA; --ta-surface:#FFFFFF; --ta-border:#E4E9F0; --ta-muted:#F0F4F8;
    --ta-text:#1A202C; --ta-sub:#64748B;
    --ta-accent:#0EA5E9; --ta-accentD:#0284C7; --ta-accentS:#E0F2FE;
    --ta-success:#10B981; --ta-successS:#D1FAE5;
    --ta-danger:#EF4444; --ta-dangerS:#FEE2E2;
    --ta-warning:#F59E0B; --ta-warningS:#FEF3C7;
    --ta-info:#6366F1; --ta-infoS:#EEF2FF;
}

/* ══════ ANIMATIONS ══════ */
.ta-fade{opacity:0;transform:translateY(16px);animation:taFade .55s cubic-bezier(.25,.46,.45,.94) var(--d,0s) forwards}
@keyframes taFade{to{opacity:1;transform:translateY(0)}}
@keyframes taPulse{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(2.2);opacity:0}}
@keyframes taSpin{to{transform:rotate(360deg)}}
@keyframes taShine{from{transform:translateX(-100%)}to{transform:translateX(200%)}}
@keyframes taPopIn{0%{transform:scale(0) rotate(-20deg);opacity:0}60%{transform:scale(1.1) rotate(3deg);opacity:1}100%{transform:scale(1) rotate(0);opacity:1}}
@keyframes taGlow{0%,100%{opacity:0}50%{opacity:1}}

/* ══════ HEADER ══════ */
.ta-header{display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:24px}
.ta-breadcrumb{display:flex;align-items:center;gap:5px;font-size:12px;font-weight:500;color:var(--ta-sub);margin-bottom:6px}
.ta-bc-active{color:var(--ta-accent);font-weight:700}
.ta-title{font-size:clamp(24px,3.8vw,32px);font-weight:800;color:var(--ta-text);letter-spacing:-.02em;line-height:1.1;margin:0 0 4px}
.ta-subtitle{font-size:13px;color:var(--ta-sub);margin:0;max-width:420px}
.ta-header-right{display:flex;align-items:center;gap:10px}

.ta-live-badge{display:inline-flex;align-items:center;gap:7px;padding:7px 16px;background:var(--ta-successS);border:1.5px solid rgba(16,185,129,.3);border-radius:99px;font-size:11px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:.06em}
.ta-live-dot{width:7px;height:7px;background:var(--ta-success);border-radius:50%;position:relative}
.ta-live-dot::after{content:'';position:absolute;inset:-3px;border-radius:50%;background:var(--ta-success);animation:taPulse 2s ease-in-out infinite}

/* ══════ CARD ══════ */
.ta-card{background:var(--ta-surface);border:1.5px solid var(--ta-border);border-radius:20px;padding:28px;position:relative;overflow:hidden;transition:box-shadow .3s,transform .3s}
.ta-card:hover{box-shadow:0 8px 28px rgba(14,165,233,.08)}
.ta-card-glow{position:absolute;top:-50px;right:-40px;width:180px;height:180px;background:radial-gradient(circle,rgba(14,165,233,.08),transparent 70%);border-radius:50%;pointer-events:none;animation:taGlow 4s ease-in-out infinite}

/* ══════ CONFIG FORM ══════ */
.ta-config-icon{width:52px;height:52px;background:var(--ta-accentS);border-radius:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.ta-config-icon .material-symbols-outlined{font-size:26px;color:var(--ta-accent)}

.ta-form-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-top:24px}
.ta-field{display:flex;flex-direction:column;gap:6px}
.ta-label{font-size:11px;font-weight:700;color:var(--ta-sub);text-transform:uppercase;letter-spacing:.07em}
.ta-select{
    width:100%;padding:10px 14px;
    background:var(--ta-surface);
    border:1.5px solid var(--ta-border);
    border-radius:12px;
    font-size:13px;font-weight:500;color:var(--ta-text);
    transition:border-color .2s,box-shadow .2s;
    cursor:pointer;
    appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748B' viewBox='0 0 16 16'%3E%3Cpath d='M4.646 6.646a.5.5 0 0 1 .708 0L8 9.293l2.646-2.647a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 12px center;background-size:16px;
    padding-right:36px;
}
.ta-select:focus{border-color:var(--ta-accent);box-shadow:0 0 0 3px rgba(14,165,233,.12);outline:none}
.ta-select:disabled{opacity:.45;cursor:not-allowed;background-color:var(--ta-muted)}

.ta-info-bar{display:flex;align-items:flex-start;gap:10px;padding:14px 18px;background:var(--ta-muted);border-radius:14px;margin-top:24px;border:1px solid var(--ta-border)}
.ta-info-bar p{font-size:12px;color:var(--ta-sub);line-height:1.55;margin:0}

/* ══════ BUTTONS ══════ */
.ta-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:14px 32px;border:none;cursor:pointer;border-radius:14px;font-size:14px;font-weight:700;transition:all .2s}
.ta-btn-primary{background:linear-gradient(135deg,var(--ta-accent),var(--ta-info));color:#fff;box-shadow:0 6px 20px rgba(14,165,233,.3)}
.ta-btn-primary:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(14,165,233,.4)}
.ta-btn-primary:active{transform:scale(.97)}
.ta-btn-primary:disabled{opacity:.5;cursor:not-allowed;transform:none}
.ta-btn-danger{background:linear-gradient(135deg,var(--ta-danger),#F87171);color:#fff;box-shadow:0 6px 20px rgba(239,68,68,.25)}
.ta-btn-danger:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(239,68,68,.35)}
.ta-btn-danger:disabled{opacity:.5;cursor:not-allowed;transform:none}
.ta-btn-secondary{background:var(--ta-muted);color:var(--ta-text);border:1.5px solid var(--ta-border)}
.ta-btn-secondary:hover{border-color:var(--ta-accent);color:var(--ta-accent)}

.ta-spinner{width:20px;height:20px;animation:taSpin .7s linear infinite}

/* ══════ ACTIVE SESSION ══════ */
.ta-active-grid{display:grid;grid-template-columns:1.8fr 1fr;gap:20px}
@media(max-width:900px){.ta-active-grid{grid-template-columns:1fr}}

.ta-detail-chip{
    display:flex;flex-direction:column;gap:2px;
    padding:14px 18px;background:var(--ta-muted);
    border:1px solid var(--ta-border);border-radius:14px;
}
.ta-detail-label{font-size:10px;font-weight:700;color:var(--ta-sub);text-transform:uppercase;letter-spacing:.08em}
.ta-detail-val{font-size:16px;font-weight:800;color:var(--ta-text)}

.ta-live-counter{
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    text-align:center;padding:20px 0;
}
.ta-counter-number{font-size:56px;font-weight:900;color:var(--ta-accent);line-height:1;margin-bottom:4px}
.ta-counter-label{font-size:12px;font-weight:600;color:var(--ta-sub);text-transform:uppercase;letter-spacing:.08em}
.ta-counter-total{font-size:14px;color:var(--ta-sub);font-weight:500;margin-top:8px}
.ta-progress-track{width:100%;max-width:200px;height:6px;background:var(--ta-muted);border-radius:99px;overflow:hidden;margin-top:12px}
.ta-progress-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,var(--ta-accent),var(--ta-success));transition:width .8s ease;position:relative;overflow:hidden}
.ta-progress-fill::after{content:'';position:absolute;inset:0;background:linear-gradient(90deg,transparent,rgba(255,255,255,.5),transparent);animation:taShine 2.5s infinite}

.ta-view-list-btn{
    display:inline-flex;align-items:center;gap:5px;
    margin-top:16px;padding:7px 14px;
    background:var(--ta-accentS);border:1px solid rgba(14,165,233,.2);
    border-radius:99px;font-size:11px;font-weight:700;color:var(--ta-accentD);
    cursor:pointer;transition:all .2s;
}
.ta-view-list-btn:hover{background:var(--ta-accent);color:#fff;border-color:var(--ta-accent)}

/* ══════ SUMMARY ══════ */
.ta-summary-icon{
    width:80px;height:80px;border-radius:50%;
    background:var(--ta-successS);
    display:flex;align-items:center;justify-content:center;
    margin:0 auto 20px;animation:taPopIn .5s cubic-bezier(.34,1.56,.64,1) both;
}
.ta-summary-icon .material-symbols-outlined{font-size:42px;color:var(--ta-success)}
.ta-summary-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;max-width:480px;margin:28px auto 0}
.ta-summary-stat{padding:18px;background:var(--ta-muted);border:1px solid var(--ta-border);border-radius:14px;text-align:center}
.ta-summary-num{font-size:24px;font-weight:800;line-height:1;margin-bottom:4px}
.ta-summary-lbl{font-size:10px;font-weight:700;color:var(--ta-sub);text-transform:uppercase;letter-spacing:.07em}

/* ══════ MODAL ══════ */
.ta-modal-overlay{position:fixed;inset:0;z-index:150;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.4);backdrop-filter:blur(4px)}
.ta-modal{background:var(--ta-surface);border:1.5px solid var(--ta-border);border-radius:20px;width:100%;max-width:520px;box-shadow:0 20px 50px rgba(0,0,0,.15);overflow:hidden;animation:taFade .3s ease forwards}
.ta-modal-head{padding:20px 24px;border-bottom:1px solid var(--ta-border);display:flex;align-items:center;justify-content:space-between}
.ta-modal-title{font-size:16px;font-weight:700;color:var(--ta-text);margin:0}
.ta-modal-sub{font-size:11px;color:var(--ta-sub);margin:2px 0 0}
.ta-modal-close{width:32px;height:32px;border-radius:8px;border:none;background:var(--ta-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s}
.ta-modal-close:hover{background:var(--ta-dangerS)}
.ta-modal-body{max-height:400px;overflow-y:auto;padding:16px 24px}
.ta-student-row{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-radius:12px;border:1px solid var(--ta-border);margin-bottom:8px;transition:background .2s}
.ta-student-row:hover{background:var(--ta-muted)}
.ta-student-avatar{width:36px;height:36px;border-radius:50%;background:var(--ta-accentS);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:var(--ta-accentD);flex-shrink:0}
.ta-student-name{font-size:13px;font-weight:600;color:var(--ta-text)}
.ta-student-roll{font-size:10px;color:var(--ta-sub)}

/* ══════ TOAST ══════ */
.ta-toast-wrap{position:fixed;top:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none}
.ta-toast{display:flex;align-items:center;gap:12px;padding:14px 20px;background:var(--ta-surface);border:1.5px solid var(--ta-border);border-radius:14px;box-shadow:0 8px 28px rgba(0,0,0,.12);font-size:13px;font-weight:500;color:var(--ta-text);transform:translateX(130%);transition:transform .4s cubic-bezier(.34,1.56,.64,1);pointer-events:auto;max-width:380px}
.ta-toast.ta-toast-show{transform:translateX(0)}
.ta-toast--success{border-left:4px solid var(--ta-success)}
.ta-toast--error{border-left:4px solid var(--ta-danger)}

/* Print */
@media print{.ta-card{box-shadow:none!important;border:1px solid #ccc!important}.ta-fade{opacity:1!important;animation:none!important;transform:none!important}}
[x-cloak]{display:none!important}
</style>

<div x-data="teacherAttendance()" @timer-expired.window="autoCloseSession()">

    {{-- ── HEADER ── --}}
    <div class="ta-header ta-fade" style="--d:.05s">
        <div>
            <div class="ta-breadcrumb">
                <span class="material-symbols-outlined" style="font-size:15px">school</span>
                <span>Dashboard</span>
                <span class="material-symbols-outlined" style="font-size:13px">chevron_right</span>
                <span class="ta-bc-active">Attendance</span>
            </div>
            <h1 class="ta-title">Attendance Control</h1>
            <p class="ta-subtitle">Configure and manage live lecture attendance sessions for your classes.</p>
        </div>
        <div class="ta-header-right" x-show="sessionState === 'active'" x-cloak>
            <div class="ta-live-badge">
                <span class="ta-live-dot"></span>
                Session Live
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         STATE: CONFIG (Start new session)
    ══════════════════════════════════════════ --}}
    <div class="ta-card ta-fade" style="--d:.12s"
         x-show="sessionState === 'config'"
         x-transition
         style="display:{{ $activeSession ? 'none' : 'block' }}">
        <div class="ta-card-glow"></div>

        <div style="display:flex;align-items:center;gap:16px;margin-bottom:4px;position:relative;z-index:1">
            <div class="ta-config-icon">
                <span class="material-symbols-outlined">tune</span>
            </div>
            <div>
                <h3 style="font-size:18px;font-weight:800;color:var(--ta-text);margin:0">Start New Session</h3>
                <p style="font-size:12px;color:var(--ta-sub);margin:2px 0 0">Select course, semester, subject and batch to open attendance window.</p>
            </div>
        </div>

        <div class="ta-form-grid">
            {{-- 1. Course --}}
            <div class="ta-field">
                <label class="ta-label">Course</label>
                <select x-model="formData.course_id" @change="onCourseChange()" class="ta-select">
                    <option value="">— Select Course —</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 2. Semester --}}
            <div class="ta-field">
                <label class="ta-label">Semester</label>
                <select x-model="formData.semester" @change="onSemesterChange()" :disabled="!formData.course_id" class="ta-select">
                    <option value="">— Select Course First —</option>
                    <template x-for="sem in availableSemesters" :key="sem">
                        <option :value="sem" x-text="`Semester ${sem}`"></option>
                    </template>
                </select>
            </div>

            {{-- 3. Subject (filtered by semester) --}}
            <div class="ta-field">
                <label class="ta-label">Subject</label>
                <select x-model="formData.subject_id" :disabled="!formData.semester" class="ta-select">
                    <option value="">— Select Semester First —</option>
                    <template x-for="subj in filteredSubjects" :key="subj.id">
                        <option :value="subj.id" x-text="`${subj.name} (${subj.code})`"></option>
                    </template>
                </select>
            </div>

            {{-- 4. Batch (auto-selects '24') --}}
            <div class="ta-field">
                <label class="ta-label">Batch</label>
                <select x-model="formData.batch_id" :disabled="!formData.course_id" class="ta-select">
                    <option value="">— Select Course First —</option>
                    <template x-for="batch in filteredBatches" :key="batch.id">
                        <option :value="batch.id" x-text="batch.name"></option>
                    </template>
                </select>
            </div>

            {{-- 5. Period --}}
            <div class="ta-field">
                <label class="ta-label">Period</label>
                <select x-model="formData.period_number" class="ta-select">
                    @foreach(range(1, 8) as $p)
                        <option value="{{ $p }}">Period {{ $p }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Info + CTA --}}
        <div class="ta-info-bar">
            <span class="material-symbols-outlined" style="font-size:18px;color:var(--ta-sub);flex-shrink:0;margin-top:1px">info</span>
            <p>GPS coordinates will be captured at start. Students must be within 15 metres of your location. Window stays open for <strong>5 minutes</strong> and auto-closes when time is up.</p>
        </div>

        <div style="margin-top:24px;display:flex;justify-content:flex-end">
            <button @click="startSession()"
                    :disabled="!isFormValid || loading"
                    class="ta-btn ta-btn-primary">
                <template x-if="!loading">
                    <span style="display:flex;align-items:center;gap:8px">
                        <span class="material-symbols-outlined">play_circle</span>
                        START SESSION
                    </span>
                </template>
                <template x-if="loading">
                    <span style="display:flex;align-items:center;gap:8px">
                        <svg class="ta-spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="32" stroke-dashoffset="8"/></svg>
                        INITIALIZING...
                    </span>
                </template>
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         STATE: ACTIVE SESSION
    ══════════════════════════════════════════ --}}
    <div x-show="sessionState === 'active'" x-cloak class="ta-active-grid ta-fade" style="--d:.15s; display:{{ $activeSession ? 'grid' : 'none' }}">

        {{-- Details Panel --}}
        <div class="ta-card">
            <div class="ta-card-glow"></div>
            <div style="position:relative;z-index:1">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
                    <span class="ta-live-dot"></span>
                    <span style="font-size:11px;font-weight:700;color:var(--ta-accentD);text-transform:uppercase;letter-spacing:.07em">Happening Now</span>
                </div>

                <h2 style="font-size:26px;font-weight:800;color:var(--ta-text);margin:0 0 20px;letter-spacing:-.01em" x-text="activeData.subject_name"></h2>

                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px">
                    <div class="ta-detail-chip">
                        <span class="ta-detail-label">Batch</span>
                        <span class="ta-detail-val" x-text="activeData.batch_name"></span>
                    </div>
                    <div class="ta-detail-chip">
                        <span class="ta-detail-label">Period</span>
                        <span class="ta-detail-val">#<span x-text="activeData.period"></span></span>
                    </div>
                    <div class="ta-detail-chip">
                        <span class="ta-detail-label">Started At</span>
                        <span class="ta-detail-val" x-text="activeData.start_time"></span>
                    </div>
                    <div class="ta-detail-chip"
                         x-data="timer({{ $activeSession ? \Carbon\Carbon::parse($activeSession->start_time)->addMinutes($activeSession->attendance_window_minutes)->timestamp : 0 }})">
                        <span class="ta-detail-label">Remaining</span>
                        <span class="ta-detail-val" style="color:var(--ta-accent)" x-text="displayTime">00:00</span>
                    </div>
                </div>

                <div style="margin-top:28px;display:flex;flex-wrap:wrap;align-items:center;gap:14px">
                    <button @click="openAiCamera()" class="ta-btn" style="background:#8B5CF6;color:#FFF;border:none">
                        <span style="display:flex;align-items:center;gap:8px">
                            <span class="material-symbols-outlined">center_focus_strong</span>
                            AI CAMERA
                        </span>
                    </button>
                    <button @click="fetchSessionStudents(); showManualModal = true" class="ta-btn ta-btn-secondary">
                        <span style="display:flex;align-items:center;gap:8px">
                            <span class="material-symbols-outlined">person_add</span>
                            MANUAL OVERRIDE
                        </span>
                    </button>
                    <button @click="closeSession()" :disabled="loading" class="ta-btn ta-btn-danger">
                        <template x-if="!loading">
                            <span style="display:flex;align-items:center;gap:8px">
                                <span class="material-symbols-outlined">stop_circle</span>
                                CLOSE SESSION
                            </span>
                        </template>
                        <template x-if="loading">
                            <svg class="ta-spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="32" stroke-dashoffset="8"/></svg>
                        </template>
                    </button>
                    <p style="font-size:12px;color:var(--ta-sub);font-style:italic;margin:0">Closing will auto-mark remaining students as absent.</p>
                </div>
            </div>
        </div>

        {{-- Counter Panel --}}
        <div class="ta-card">
            <div class="ta-live-counter">
                <span class="ta-counter-number" x-text="activeData.present">0</span>
                <span class="ta-counter-label">Present</span>
                <p class="ta-counter-total">out of <strong x-text="activeData.total">0</strong> students</p>

                <div class="ta-progress-track">
                    <div class="ta-progress-fill" :style="`width:${activeData.total > 0 ? (activeData.present / activeData.total * 100) : 0}%`"></div>
                </div>

                <button @click="fetchSessionStudents()" class="ta-view-list-btn">
                    <span class="material-symbols-outlined" style="font-size:15px">visibility</span>
                    View Marked List
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         STATE: SUMMARY (after closing)
    ══════════════════════════════════════════ --}}
    <div x-show="sessionState === 'summary'" x-cloak class="ta-card ta-fade" style="--d:.1s;text-align:center;padding:48px 28px">
        <div class="ta-summary-icon">
            <span class="material-symbols-outlined">verified</span>
        </div>
        <h2 style="font-size:26px;font-weight:800;color:var(--ta-text);margin:0 0 6px">Attendance Recorded</h2>
        <p style="font-size:14px;color:var(--ta-sub);margin:0">Session closed and processed successfully.</p>

        <div class="ta-summary-grid">
            <div class="ta-summary-stat">
                <span class="ta-summary-num" style="color:var(--ta-success)" x-text="summaryData.present">0</span>
                <span class="ta-summary-lbl">Present</span>
            </div>
            <div class="ta-summary-stat">
                <span class="ta-summary-num" style="color:var(--ta-danger)" x-text="summaryData.absent">0</span>
                <span class="ta-summary-lbl">Absent</span>
            </div>
            <div class="ta-summary-stat">
                <span class="ta-summary-num" style="color:var(--ta-accent)" x-text="formData.period_number">0</span>
                <span class="ta-summary-lbl">Period</span>
            </div>
        </div>

        <button @click="resetToConfig()" class="ta-btn ta-btn-secondary" style="margin-top:32px">
            <span class="material-symbols-outlined" style="font-size:18px">home</span>
            Back to Dashboard
        </button>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL: Marked Students
    ══════════════════════════════════════════ --}}
    <div x-show="showModal" x-cloak class="ta-modal-overlay" @click.self="showModal = false">
        <div class="ta-modal" @click.stop>
            <div class="ta-modal-head">
                <div>
                    <h3 class="ta-modal-title">Marked Students</h3>
                    <p class="ta-modal-sub">Live list of present students</p>
                </div>
                <button @click="showModal = false" class="ta-modal-close">
                    <span class="material-symbols-outlined" style="font-size:18px;color:var(--ta-sub)">close</span>
                </button>
            </div>
            <div class="ta-modal-body">
                <template x-if="markedStudents.length === 0">
                    <p style="text-align:center;color:var(--ta-sub);padding:32px 0;font-size:13px;font-style:italic">No students marked yet.</p>
                </template>
                <template x-for="record in markedStudents" :key="record.id">
                    <div class="ta-student-row">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="ta-student-avatar" x-text="record.student.user.name.substring(0,2).toUpperCase()"></div>
                            <div>
                                <p class="ta-student-name" x-text="record.student.user.name"></p>
                                <p class="ta-student-roll" x-text="record.student.roll_number || 'No Roll'"></p>
                            </div>
                        </div>
                        <div style="text-align:right">
                            <p style="font-size:10px;font-weight:700;color:var(--ta-success);text-transform:uppercase">Present</p>
                            <p style="font-size:10px;color:var(--ta-sub)" x-text="formatTime(record.marked_at)"></p>
                            <div style="margin-top:4px">
                                <template x-if="record.marked_by_method === 'ai'">
                                    <span style="font-size:9px;background:rgba(139,92,246,0.1);color:#8B5CF6;padding:2px 6px;border-radius:4px;font-weight:600">AI Verified</span>
                                </template>
                                <template x-if="record.marked_by_method === 'teacher_manual'">
                                    <span style="font-size:9px;background:rgba(239,68,68,0.1);color:#EF4444;padding:2px 6px;border-radius:4px;font-weight:600">Manual</span>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL: Manual Override (Unmarked Students)
    ══════════════════════════════════════════ --}}
    <div x-show="showManualModal" x-cloak class="ta-modal-overlay" @click.self="showManualModal = false">
        <div class="ta-modal" @click.stop style="max-width:500px">
            <div class="ta-modal-head">
                <div>
                    <h3 class="ta-modal-title">Manual Override</h3>
                    <p class="ta-modal-sub">Mark students missed by AI scanner</p>
                </div>
                <button @click="showManualModal = false" class="ta-modal-close">
                    <span class="material-symbols-outlined" style="font-size:18px;color:var(--ta-sub)">close</span>
                </button>
            </div>
            <div class="ta-modal-body">
                <template x-if="unmarkedStudents.length === 0">
                    <p style="text-align:center;color:var(--ta-success);padding:32px 0;font-size:13px;font-style:italic">Awesome! All students are marked present.</p>
                </template>
                <template x-for="student in unmarkedStudents" :key="student.id">
                    <div class="ta-student-row" style="display:flex; justify-content:space-between; align-items:center;">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="ta-student-avatar" style="background:var(--ta-danger);color:#fff" x-text="student.user.name.substring(0,2).toUpperCase()"></div>
                            <div>
                                <p class="ta-student-name" x-text="student.user.name"></p>
                                <p class="ta-student-roll" x-text="student.roll_number || 'No Roll'"></p>
                            </div>
                        </div>
                        <div>
                            <button @click="submitManualAttendance(student.id)" class="ta-btn ta-btn-primary" style="padding:6px 12px; font-size:11px">
                                Mark Present
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL: AI Camera Scanner
    ══════════════════════════════════════════ --}}
    <div x-show="showAiModal" x-cloak class="ta-modal-overlay" @click.self="showAiModal = false">
        <div class="ta-modal" @click.stop style="max-width:480px">
            <div class="ta-modal-head">
                <div>
                    <h3 class="ta-modal-title">AI Camera Verification</h3>
                    <p class="ta-modal-sub">Take class photos to mark bulk attendance</p>
                </div>
                <button @click="showAiModal = false" class="ta-modal-close" :disabled="aiProcessing">
                    <span class="material-symbols-outlined" style="font-size:18px;color:var(--ta-sub)">close</span>
                </button>
            </div>
            
            <div class="ta-modal-body" style="text-align:center">
                <div x-show="!aiProcessing && aiMatchedIds.length === 0">
                    <div style="margin:20px 0">
                        <span class="material-symbols-outlined" style="font-size:48px;color:var(--ta-sub)">photo_camera</span>
                        <p style="font-size:13px;color:var(--ta-sub);margin-top:8px">Take 3-4 photos from different angles of the classroom. The AI will automatically detect faces and verify them against enrolled student biometrics.</p>
                    </div>
                    <button @click="$refs.camInput.click()" class="ta-btn ta-btn-primary" style="width:100%;justify-content:center">
                        <span class="material-symbols-outlined" style="font-size:18px">camera_alt</span>
                        Open Camera
                    </button>
                </div>

                <div x-show="aiProcessing" style="padding:40px 0">
                    <svg class="ta-spinner" viewBox="0 0 24 24" style="width:40px;height:40px;color:var(--ta-accent);margin:0 auto"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="32" stroke-dashoffset="8"/></svg>
                    <p style="font-size:14px;font-weight:700;color:var(--ta-text);margin-top:16px" x-text="aiStatusText">Processing images...</p>
                    <p style="font-size:12px;color:var(--ta-sub);margin-top:4px">Please wait, AI is scanning faces...</p>
                </div>

                <div x-show="!aiProcessing && aiMatchedIds.length > 0">
                    <div style="margin:20px 0">
                        <span class="material-symbols-outlined" style="font-size:48px;color:var(--ta-success)">check_circle</span>
                        <h4 style="font-size:18px;font-weight:700;color:var(--ta-text);margin:8px 0 4px">Verification Complete</h4>
                        <p style="font-size:14px;color:var(--ta-sub)">Found <strong style="color:var(--ta-text)" x-text="aiTotalFacesFound"></strong> faces. <strong style="color:var(--ta-success)" x-text="aiMatchedIds.length"></strong> successfully matched with enrolled students.</p>
                    </div>

                    <div style="display:flex;gap:12px;margin-top:24px">
                        <button @click="$refs.camInput.click()" class="ta-btn ta-btn-secondary" style="flex:1;justify-content:center">
                            Add More Photos
                        </button>
                        <button @click="submitAiBulkAttendance()" class="ta-btn ta-btn-primary" style="flex:1;justify-content:center" :disabled="aiSubmitting">
                            <template x-if="!aiSubmitting">
                                <span>Mark <span x-text="aiMatchedIds.length"></span> Present</span>
                            </template>
                            <template x-if="aiSubmitting">
                                <span>Submitting...</span>
                            </template>
                        </button>
                    </div>
                </div>
                
                {{-- Invisible reference to input --}}
                <input type="file" accept="image/*" capture="environment" x-ref="camInput" multiple @change="processAiImages" style="display:none">
            </div>
        </div>
    </div>

</div>

{{-- Toast --}}
<div id="toast-container" class="ta-toast-wrap"></div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {

    Alpine.data('teacherAttendance', () => ({
        sessionState: '{{ $activeSession ? "active" : "config" }}',
        loading: false,

        // Raw data from server
        allBatches: @json($batches),
        allSubjects: @json($subjects),

        // Computed / filtered
        filteredBatches: [],
        filteredSubjects: [],
        availableSemesters: [],

        // Modal + live polling
        showModal: false,
        showManualModal: false,
        markedStudents: [],
        enrolledList: [],
        unmarkedStudents: [],
        _pollInterval: null,

        // AI Scanner Properties
        showAiModal: false,
        aiProcessing: false,
        aiSubmitting: false,
        aiStatusText: '',
        aiTotalFacesFound: 0,
        aiMatchedIds: [],
        enrolledStudents: [],
        aiModelsLoaded: false,

        init() {
            // Start live polling if session is already active
            if (this.sessionState === 'active' && this.activeData.id) {
                this.startPolling();
            }
        },

        startPolling() {
            // Poll immediately, then every 5 seconds
            this.pollStudents();
            this._pollInterval = setInterval(() => this.pollStudents(), 5000);
        },

        stopPolling() {
            if (this._pollInterval) {
                clearInterval(this._pollInterval);
                this._pollInterval = null;
            }
        },

        formData: {
            course_id: '',
            semester: '',
            batch_id: '',
            subject_id: '',
            period_number: 1,
            teacher_lat: null,
            teacher_long: null
        },

        activeData: {
            id: '{{ $activeSession?->id ?? "" }}',
            subject_name: '{{ $activeSession?->subject?->name ?? "" }}',
            batch_name: '{{ $activeSession?->batch?->name ?? "" }}',
            period: '{{ $activeSession?->period_number ?? "" }}',
            start_time: '{{ $activeSession ? \Carbon\Carbon::parse($activeSession->start_time)->format("H:i") : "" }}',
            present: {{ $activeSession?->present_count ?? 0 }},
            total: {{ $activeSession?->total_students ?? 0 }}
        },

        summaryData: { present: 0, absent: 0 },

        get isFormValid() {
            return this.formData.course_id && this.formData.batch_id && this.formData.subject_id && this.formData.semester;
        },

        // ── Course changed → populate semesters + batches, auto-select batch "24" ──
        onCourseChange() {
            const cid = this.formData.course_id;
            this.formData.semester = '';
            this.formData.batch_id = '';
            this.formData.subject_id = '';
            this.filteredSubjects = [];

            if (!cid) {
                this.filteredBatches = [];
                this.availableSemesters = [];
                return;
            }

            // Filter batches for this course
            this.filteredBatches = this.allBatches.filter(b => b.course_id == cid);

            // Auto-select batch containing "24" in name
            const batch24 = this.filteredBatches.find(b =>
                b.name && b.name.includes('24')
            );
            if (batch24) {
                this.formData.batch_id = batch24.id;
            }

            // Get unique semesters from subjects for this course
            const courseSubjects = this.allSubjects.filter(s => s.course_id == cid);
            const sems = [...new Set(courseSubjects.map(s => parseInt(s.semester)))].sort((a, b) => a - b);
            this.availableSemesters = sems;
        },

        // ── Semester changed → filter subjects by course + semester ──
        onSemesterChange() {
            const cid = this.formData.course_id;
            const sem = this.formData.semester;
            this.formData.subject_id = '';

            if (!cid || !sem) {
                this.filteredSubjects = [];
                return;
            }

            this.filteredSubjects = this.allSubjects.filter(
                s => s.course_id == cid && s.semester == sem
            );
        },

        // ── Silent poll (runs every 5s, no toast on error) ──
        async pollStudents() {
            if (!this.activeData.id || this.sessionState !== 'active') return;
            try {
                const res = await fetch(`{{ url('teacher/attendance/session') }}/${this.activeData.id}/students`);
                const data = await res.json();
                this.markedStudents = data.marked;
                this.enrolledList = data.enrolled;
                this.unmarkedStudents = this.enrolledList.filter(e => !this.markedStudents.some(m => m.student_id === e.id));
                this.activeData.present = this.markedStudents.length;
            } catch (e) {
                // Silent fail — next poll will retry
            }
        },

        // ── Opens modal + fetches latest ──
        async fetchSessionStudents() {
            if (!this.activeData.id) return;
            this.showModal = true;
            await this.pollStudents();
        },

        formatTime(dateStr) {
            if (!dateStr) return '--:--';
            const d = new Date(dateStr);
            return d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');
        },

        // ── Start session (GPS → POST) ──
        startSession() {
            this.loading = true;
            if (!navigator.geolocation) {
                this.showToast('Browser does not support geolocation.', 'error');
                this.loading = false;
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.formData.teacher_lat = pos.coords.latitude;
                    this.formData.teacher_long = pos.coords.longitude;
                    this.submitStart();
                },
                () => {
                    this.showToast('GPS Permission required to start session.', 'error');
                    this.loading = false;
                },
                { enableHighAccuracy: true }
            );
        },

        async submitStart() {
            try {
                const res = await fetch('{{ route("teacher.attendance.start") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(this.formData)
                });
                const data = await res.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    this.showToast(data.message || 'Failed to start session.', 'error');
                }
            } catch (e) {
                this.showToast('Network error.', 'error');
            } finally {
                this.loading = false;
            }
        },

        // ── Close session ──
        async closeSession() {
            this.loading = true;
            try {
                const res = await fetch('{{ route("teacher.attendance.close") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ lecture_session_id: this.activeData.id })
                });
                const data = await res.json();
                if (data.success) {
                    this.stopPolling();
                    this.summaryData.present = this.activeData.present;
                    this.summaryData.absent = this.activeData.total - this.activeData.present;
                    this.sessionState = 'summary';
                } else {
                    this.showToast(data.message, 'error');
                }
            } catch (e) {
                this.showToast('Network error.', 'error');
            } finally {
                this.loading = false;
            }
        },

        resetToConfig() {
            this.sessionState = 'config';
        },

        showToast(message, type = 'success') {
            const c = document.getElementById('toast-container');
            const el = document.createElement('div');
            el.className = `ta-toast ta-toast--${type}`;
            const iconName = type === 'success' ? 'check_circle' : 'error';
            const iconColor = type === 'success' ? 'var(--ta-success)' : 'var(--ta-danger)';
            el.innerHTML = `<span class="material-symbols-outlined" style="font-size:20px;color:${iconColor}">${iconName}</span><p style="margin:0;flex:1">${message}</p>`;
            c.appendChild(el);
            requestAnimationFrame(() => requestAnimationFrame(() => el.classList.add('ta-toast-show')));
            setTimeout(() => { el.classList.remove('ta-toast-show'); setTimeout(() => el.remove(), 400); }, 3500);
        },

        // ── AI Camera Methods ──
        async openAiCamera() {
            this.showAiModal = true;
            this.aiMatchedIds = [];
            this.aiTotalFacesFound = 0;
            if (!this.enrolledStudents.length) {
                try {
                    const res = await fetch(`{{ url('teacher/attendance/session') }}/${this.activeData.id}/biometrics`);
                    const data = await res.json();
                    this.enrolledStudents = data.map(s => ({
                        id: s.id,
                        descriptor: s.face_descriptor ? JSON.parse(s.face_descriptor) : null
                    })).filter(s => s.descriptor !== null);
                } catch(e) {
                    this.showToast('Failed to fetch biometric data.', 'error');
                }
            }
        },

        async processAiImages(event) {
            const files = event.target.files;
            if (!files || files.length === 0) return;
            
            this.aiProcessing = true;
            this.aiStatusText = 'Loading AI Models...';

            if (!this.aiModelsLoaded) {
                const MODEL_URL = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights';
                await Promise.all([
                    faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
                ]);
                this.aiModelsLoaded = true;
            }

            let newMatches = new Set();
            let facesFound = 0;

            // PERFORMANCE OPTIMIZATION: Build a FaceMatcher once for all images.
            // This handles matching much faster via optimized internal vectors.
            let faceMatcher = null;
            if (this.enrolledStudents.length > 0) {
                this.aiStatusText = 'Compiling Neural Data...';
                const labeledDescriptors = this.enrolledStudents.map(student => {
                    const descArray = new Float32Array(Object.values(student.descriptor));
                    return new faceapi.LabeledFaceDescriptors(student.id.toString(), [descArray]);
                });
                faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.42);
            }

            for (let i = 0; i < files.length; i++) {
                this.aiStatusText = `Processing image ${i+1} of ${files.length}...`;
                
                const img = await this.readImage(files[i]);
                const detections = await faceapi.detectAllFaces(img).withFaceLandmarks().withFaceDescriptors();
                facesFound += detections.length;

                if (faceMatcher) {
                    detections.forEach(det => {
                        const bestMatch = faceMatcher.findBestMatch(det.descriptor);
                        if (bestMatch.label !== 'unknown' && bestMatch.distance <= 0.42) {
                            newMatches.add(parseInt(bestMatch.label));
                        }
                    });
                }
            }

            this.aiTotalFacesFound += facesFound;
            const currentMatchedIds = new Set(this.aiMatchedIds);
            newMatches.forEach(id => currentMatchedIds.add(id));
            this.aiMatchedIds = Array.from(currentMatchedIds);
            
            if (newMatches.size === 0) {
                alert("No students matched in this photo! Please try again with a clearer picture or ensure the students are enrolled in this class.");
            }
            
            this.aiProcessing = false;
            event.target.value = '';
        },

        readImage(file) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => resolve(img);
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        },

        async submitAiBulkAttendance() {
            if (this.aiMatchedIds.length === 0) return;
            this.aiSubmitting = true;
            try {
                const res = await fetch(`{{ url('teacher/attendance/session') }}/${this.activeData.id}/mark-bulk`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ student_ids: this.aiMatchedIds })
                });
                const data = await res.json();
                
                if (data.success) {
                    this.showToast(`Successfully marked ${data.marked_count} students present! 🎉`, 'success');
                    this.showAiModal = false;
                    this.pollStudents(); 
                } else {
                    this.showToast(data.message || 'Error marking attendance.', 'error');
                }
            } catch(e) {
                this.showToast('Network error.', 'error');
            } finally {
                this.aiSubmitting = false;
            }
        },

        async submitManualAttendance(studentId) {
            try {
                const res = await fetch(`{{ url('teacher/attendance/session') }}/${this.activeData.id}/mark-manual`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ student_id: studentId })
                });
                const data = await res.json();
                
                if (data.success) {
                    this.showToast('Student manually marked present!', 'success');
                    this.pollStudents(); // Reload the lists
                } else {
                    this.showToast(data.message || 'Error marking attendance.', 'error');
                }
            } catch(e) {
                this.showToast('Network error.', 'error');
            }
        },

        // Auto-close when timer expires
        autoCloseSession() {
            if (this.sessionState !== 'active' || this.loading) return;
            this.showToast('⏱ Time\'s up! Auto-closing session...', 'success');
            setTimeout(() => this.closeSession(), 1500);
        }
    }));

    Alpine.data('timer', (expiry) => ({
        expiry, displayTime: '00:00', expired: false, _interval: null,
        init() {
            if (!this.expiry) return;
            this.tick();
            this._interval = setInterval(() => this.tick(), 1000);
        },
        tick() {
            const diff = this.expiry - Math.floor(Date.now() / 1000);
            if (diff <= 0) {
                this.displayTime = '00:00';
                if (!this.expired) {
                    this.expired = true;
                    clearInterval(this._interval);
                    // Auto-close the session
                    window.dispatchEvent(new CustomEvent('timer-expired'));
                }
                return;
            }
            this.displayTime = `${Math.floor(diff/60).toString().padStart(2,'0')}:${(diff%60).toString().padStart(2,'0')}`;
        }
    }));
});
</script>
@endpush
