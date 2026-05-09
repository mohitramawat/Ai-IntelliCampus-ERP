@extends('layouts.dashboard')

@section('content')

{{-- Inline styles — layout has no @stack('styles') so we embed here --}}
<style>
/* ═══════════════════════════════════════════════════
   CSS VARIABLES — derived from your tailwind config
═══════════════════════════════════════════════════ */
:root {
    --sa-bg:       #F5F7FA;
    --sa-surface:  #FFFFFF;
    --sa-border:   #E4E9F0;
    --sa-muted:    #F0F4F8;
    --sa-text:     #1A202C;
    --sa-sub:      #64748B;
    --sa-accent:   #0EA5E9;
    --sa-accentD:  #0284C7;
    --sa-accentS:  #E0F2FE;
    --sa-success:  #10B981;
    --sa-successS: #D1FAE5;
    --sa-danger:   #EF4444;
    --sa-dangerS:  #FEE2E2;
    --sa-warning:  #F59E0B;
    --sa-warningS: #FEF3C7;
    --sa-info:     #6366F1;
    --sa-infoS:    #EEF2FF;
    --sa-radius:   16px;
    --sa-radius-lg:24px;
}

/* ═══════════════  ANIMATIONS  ═══════════════ */
.sa-fade-in {
    opacity: 0;
    transform: translateY(18px);
    animation: saFade .6s cubic-bezier(.25,.46,.45,.94) var(--delay, 0s) forwards;
}
@keyframes saFade {
    to { opacity: 1; transform: translateY(0); }
}
@keyframes saPulse {
    0%,100% { transform: scale(1); opacity: 1; }
    50%     { transform: scale(2.2); opacity: 0; }
}
@keyframes saShine {
    from { transform: translateX(-100%); }
    to   { transform: translateX(200%); }
}
@keyframes saGlow {
    0%,100% { opacity: 0; }
    50%     { opacity: 1; }
}
@keyframes saRing {
    0%   { transform: scale(.8); opacity: 1; }
    100% { transform: scale(1.8); opacity: 0; }
}
@keyframes saPopIn {
    0%   { transform: scale(0) rotate(-30deg); opacity: 0; }
    60%  { transform: scale(1.15) rotate(5deg); opacity: 1; }
    100% { transform: scale(1) rotate(0); opacity: 1; }
}
@keyframes saSpin { to { transform: rotate(360deg); } }
@keyframes saSlowSpin { to { transform: rotate(360deg); } }

.sa-success-enter  { transition: all .5s cubic-bezier(.34,1.56,.64,1); }
.sa-success-start  { opacity: 0; transform: scale(.85) translateY(12px); }
.sa-success-end    { opacity: 1; transform: scale(1) translateY(0); }

/* ═══════════════  HEADER  ═══════════════ */
.sa-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 28px;
}
.sa-breadcrumb {
    display: flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 500; color: var(--sa-sub);
    margin-bottom: 6px;
}
.sa-bc-active { color: var(--sa-accent); font-weight: 700; }
.sa-title {
    font-size: clamp(24px,3.8vw,34px);
    font-weight: 800; color: var(--sa-text);
    letter-spacing: -.02em; line-height: 1.1;
    margin: 0 0 4px;
}
.sa-subtitle { font-size: 14px; color: var(--sa-sub); margin: 0; max-width: 460px; }
.sa-header-right { display: flex; align-items: center; gap: 10px; }

.sa-live-badge {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 7px 14px;
    background: var(--sa-surface);
    border: 1.5px solid var(--sa-border);
    border-radius: 99px;
    font-size: 12px; font-weight: 600; color: var(--sa-text);
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
}
.sa-live-dot {
    width: 7px; height: 7px;
    background: var(--sa-success);
    border-radius: 50%;
    position: relative;
}
.sa-live-dot::after {
    content: '';
    position: absolute; inset: -3px;
    border-radius: 50%;
    background: var(--sa-success);
    animation: saPulse 2s ease-in-out infinite;
}

/* ═══════════════  BUTTONS  ═══════════════ */
.sa-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px;
    border-radius: 12px;
    font-size: 13px; font-weight: 600;
    border: none; cursor: pointer;
    transition: all .2s ease;
}
.sa-btn-outline {
    background: var(--sa-surface);
    color: var(--sa-text);
    border: 1.5px solid var(--sa-border);
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
.sa-btn-outline:hover {
    border-color: var(--sa-accent);
    color: var(--sa-accent);
    box-shadow: 0 4px 12px rgba(14,165,233,.15);
    transform: translateY(-1px);
}

/* ═══════════════  STAT CARDS  ═══════════════ */
.sa-stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media(max-width:1024px) { .sa-stats-row { grid-template-columns: repeat(2, 1fr); } }
@media(max-width:600px)  { .sa-stats-row { grid-template-columns: 1fr; } }

.sa-stat-card {
    position: relative;
    background: var(--sa-surface);
    border: 1.5px solid var(--sa-border);
    border-radius: var(--sa-radius);
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    overflow: hidden;
    transition: transform .25s, box-shadow .25s, border-color .25s;
}
.sa-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(14,165,233,.1);
    border-color: var(--sa-accent);
}

.sa-stat-icon-wrap {
    width: 44px; height: 44px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.sa-stat-icon-wrap .material-symbols-outlined { font-size: 22px; }
.sa-stat-icon--accent  { background: var(--sa-accentS); color: var(--sa-accent); }
.sa-stat-icon--success { background: var(--sa-successS); color: var(--sa-success); }
.sa-stat-icon--danger  { background: var(--sa-dangerS); color: var(--sa-danger); }
.sa-stat-icon--info    { background: var(--sa-infoS); color: var(--sa-info); }

.sa-stat-body { flex: 1; min-width: 0; }
.sa-stat-label { font-size: 12px; color: var(--sa-sub); display: block; font-weight: 500; }
.sa-stat-value {
    font-size: 28px; font-weight: 800; color: var(--sa-text);
    line-height: 1.1; display: block; margin-top: 2px;
}
.sa-stat-unit {
    font-size: 20px; font-weight: 700; color: var(--sa-accent);
    align-self: flex-start; margin-top: 18px;
}
.sa-stat-sessions {
    font-size: 11px; color: var(--sa-sub); font-weight: 500;
    align-self: flex-end; margin-bottom: 4px;
}

.sa-stat-bar-track {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 4px;
    background: var(--sa-muted);
}
.sa-stat-bar {
    height: 100%;
    width: 0;
    transition: width 1.4s cubic-bezier(.4,0,.2,1);
    border-radius: 0 4px 0 0;
}
.sa-bar-accent { background: linear-gradient(90deg, var(--sa-accent), var(--sa-info)); }

/* ═══════════════  SESSION CARD  ═══════════════ */
.sa-session-card {
    position: relative;
    background: var(--sa-surface);
    border: 1.5px solid var(--sa-border);
    border-radius: var(--sa-radius-lg);
    padding: 28px;
    margin-bottom: 24px;
    overflow: hidden;
    transition: box-shadow .3s;
}
.sa-session-card:hover { box-shadow: 0 8px 28px rgba(14,165,233,.1); }

.sa-session-glow {
    position: absolute;
    top: -60px; right: -50px;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(14,165,233,.10), transparent 70%);
    border-radius: 50%; pointer-events: none;
    animation: saGlow 4s ease-in-out infinite;
}

.sa-session-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 20px;
}
.sa-session-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 14px;
    background: var(--sa-accentS);
    border: 1.5px solid rgba(14,165,233,.25);
    border-radius: 99px;
    font-size: 11px; font-weight: 700; color: var(--sa-accentD);
    text-transform: uppercase; letter-spacing: .07em;
}
.sa-session-badge-dot {
    width: 6px; height: 6px;
    background: var(--sa-accent);
    border-radius: 50%;
    position: relative;
}
.sa-session-badge-dot::after {
    content: '';
    position: absolute; inset: -3px;
    border-radius: 50%;
    background: var(--sa-accent);
    animation: saPulse 2s ease-in-out infinite;
}
.sa-session-today {
    display: flex; align-items: center; gap: 5px;
    font-size: 12px; color: var(--sa-sub); font-weight: 500;
}

.sa-session-subject {
    font-size: 22px; font-weight: 800; color: var(--sa-text);
    margin: 0 0 8px; letter-spacing: -.01em;
}
.sa-session-meta-row { display: flex; flex-wrap: wrap; gap: 8px; }
.sa-meta-tag {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px;
    background: var(--sa-muted);
    border: 1px solid var(--sa-border);
    border-radius: 99px;
    font-size: 12px; font-weight: 500; color: var(--sa-sub);
}

/* EMPTY */
.sa-empty-state {
    display: flex; flex-direction: column; align-items: center;
    text-align: center; padding: 20px 0;
}
.sa-empty-visual {
    position: relative;
    width: 80px; height: 80px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 16px;
}
.sa-empty-bg-ring {
    position: absolute; inset: 0;
    border: 3px dashed var(--sa-border);
    border-radius: 50%;
    animation: saSlowSpin 18s linear infinite;
}
.sa-empty-icon { font-size: 36px; color: var(--sa-sub); position: relative; z-index: 1; }
.sa-empty-title { font-size: 18px; font-weight: 700; color: var(--sa-text); margin: 0 0 4px; }
.sa-empty-desc { font-size: 13px; color: var(--sa-sub); margin: 0; line-height: 1.6; }

/* MARKED */
.sa-marked-state {
    display: flex; align-items: center; gap: 24px;
    padding: 8px 0;
}
.sa-marked-visual {
    position: relative; flex-shrink: 0;
    width: 72px; height: 72px;
    display: flex; align-items: center; justify-content: center;
}
.sa-marked-pulse {
    position: absolute; inset: 0;
    border-radius: 50%;
    border: 2px solid var(--sa-success);
    animation: saRing 2s ease-out infinite;
}
.sa-marked-icon-wrap {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, var(--sa-success), #34D399);
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 6px 16px rgba(16,185,129,.3);
}
.sa-marked-icon { font-size: 30px; color: #fff; }
.sa-confetti-pop { animation: saPopIn .5s cubic-bezier(.34,1.56,.64,1) both; }
.sa-marked-badge {
    display: inline-block;
    font-size: 11px; font-weight: 700; color: var(--sa-success);
    text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px;
}

/* ACTIVE (can mark) */
.sa-active-state { display: flex; flex-direction: column; gap: 24px; }
.sa-active-bottom {
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 20px; flex-wrap: wrap;
}

/* Timer */
.sa-timer-block { flex: 1; min-width: 160px; }
.sa-timer-label {
    display: flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 600; color: var(--sa-sub);
    text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px;
}
.sa-timer-value {
    font-size: 44px; font-weight: 900;
    font-variant-numeric: tabular-nums;
    background: linear-gradient(90deg, var(--sa-accent), var(--sa-info));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1; margin-bottom: 8px; letter-spacing: -.02em;
}
.sa-timer-track {
    height: 5px; background: var(--sa-muted); border-radius: 99px; overflow: hidden;
}
.sa-timer-fill {
    height: 100%; border-radius: 99px;
    background: linear-gradient(90deg, var(--sa-accent), var(--sa-info));
    transition: width 1s linear;
    position: relative; overflow: hidden;
}
.sa-timer-fill::after {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.5), transparent);
    animation: saShine 2s infinite;
}

/* CTA button */
.sa-btn-cta {
    position: relative;
    padding: 16px 32px;
    background: linear-gradient(135deg, var(--sa-accent), var(--sa-info));
    color: #fff;
    border: none; cursor: pointer;
    border-radius: 16px;
    font-size: 15px; font-weight: 700;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(14,165,233,.35);
    transition: transform .2s, box-shadow .2s;
    flex-shrink: 0;
}
.sa-btn-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(14,165,233,.45);
}
.sa-btn-cta:active { transform: scale(.97); }
.sa-btn-cta:disabled { opacity: .6; cursor: not-allowed; transform: none; }
.sa-btn-bg {
    position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,.15), transparent);
    opacity: 0;
    transition: opacity .2s;
}
.sa-btn-cta:hover .sa-btn-bg { opacity: 1; }
.sa-btn-content {
    position: relative; z-index: 1;
    display: flex; align-items: center; gap: 8px;
}
.sa-spinner { width: 20px; height: 20px; animation: saSpin .8s linear infinite; }

/* ═══════════════  SUBJECT SECTION  ═══════════════ */
.sa-section { margin-bottom: 16px; }
.sa-section-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 18px;
}
.sa-section-title { font-size: 18px; font-weight: 700; color: var(--sa-text); margin: 0; }
.sa-section-sub { font-size: 12px; color: var(--sa-sub); margin: 2px 0 0; }
.sa-pill {
    padding: 5px 14px;
    background: var(--sa-accentS);
    border: 1px solid rgba(14,165,233,.2);
    border-radius: 99px;
    font-size: 12px; font-weight: 700; color: var(--sa-accentD);
}

.sa-subj-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 14px;
}
.sa-subj-card {
    background: var(--sa-surface);
    border: 1.5px solid var(--sa-border);
    border-radius: var(--sa-radius);
    padding: 18px;
    transition: transform .25s, box-shadow .25s, border-color .25s;
}
.sa-subj-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(14,165,233,.1);
    border-color: rgba(14,165,233,.3);
}

.sa-subj-top { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
.sa-subj-icon {
    width: 40px; height: 40px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.sa-subj-info { flex: 1; min-width: 0; }
.sa-subj-name { font-size: 14px; font-weight: 700; color: var(--sa-text); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.sa-subj-sessions { font-size: 11px; color: var(--sa-sub); }
.sa-subj-right { text-align: right; flex-shrink: 0; }
.sa-subj-pct { font-size: 22px; font-weight: 800; line-height: 1; display: block; }
.sa-subj-tag {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: 10px; font-weight: 700; padding: 2px 8px;
    border-radius: 99px; margin-top: 3px;
}

.sa-subj-bar-track {
    height: 6px; background: var(--sa-muted); border-radius: 99px; overflow: hidden; margin-bottom: 10px;
}
.sa-subj-bar {
    height: 100%; border-radius: 99px;
    width: 0;
    transition: width 1.2s cubic-bezier(.4,0,.2,1);
    position: relative; overflow: hidden;
}
.sa-bar-shine {
    position: absolute; inset: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.55), transparent);
    transform: translateX(-100%);
    animation: saShine 2.5s infinite;
}

.sa-subj-note {
    display: flex; align-items: center; gap: 4px;
    font-size: 12px; color: var(--sa-sub);
}

/* ═══════════════  TOAST  ═══════════════ */
.sa-toast-container {
    position: fixed; bottom: 24px; right: 24px;
    z-index: 9999; display: flex; flex-direction: column; gap: 10px;
    pointer-events: none;
}
.sa-toast {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 20px;
    background: var(--sa-surface);
    border: 1.5px solid var(--sa-border);
    border-radius: 14px;
    box-shadow: 0 8px 28px rgba(0,0,0,.12);
    font-size: 13px; font-weight: 500; color: var(--sa-text);
    transform: translateX(130%);
    transition: transform .4s cubic-bezier(.34,1.56,.64,1);
    pointer-events: auto;
    max-width: 380px;
}
.sa-toast.sa-toast-show { transform: translateX(0); }
.sa-toast--success { border-left: 4px solid var(--sa-success); }
.sa-toast--error   { border-left: 4px solid var(--sa-danger); }

/* Print */
@media print {
    .sa-session-glow, .sa-live-badge, .sa-live-dot::after { display: none !important; }
    .sa-stat-card, .sa-session-card, .sa-subj-card { box-shadow: none !important; border: 1px solid #ccc !important; }
    .sa-fade-in { opacity: 1 !important; animation: none !important; transform: none !important; }
}

[x-cloak] { display: none !important; }
</style>

{{-- ════════════════════════════════════════════════════════ --}}
{{--  PAGE CONTENT                                           --}}
{{-- ════════════════════════════════════════════════════════ --}}

<div class="relative" x-data="studentAttendance()" x-init="initPage()">

    {{-- PAGE HEADER --}}
    <div class="sa-header sa-fade-in" style="--delay:.05s">
        <div class="sa-header-left">
            <div class="sa-breadcrumb">
                <span class="material-symbols-outlined" style="font-size:15px">school</span>
                <span>Dashboard</span>
                <span class="material-symbols-outlined" style="font-size:13px">chevron_right</span>
                <span class="sa-bc-active">Attendance</span>
            </div>
            <h1 class="sa-title">My Attendance</h1>
            <p class="sa-subtitle">Monitor your academic consistency and mark your presence with a single tap.</p>
        </div>
        <div class="sa-header-right">
            <div class="sa-live-badge">
                <span class="sa-live-dot"></span>
                Live Sync
            </div>
            <button onclick="window.print()" class="sa-btn sa-btn-outline print:hidden">
                <span class="material-symbols-outlined" style="font-size:17px">download</span>
                Export
            </button>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="sa-stats-row">
        {{-- Overall % --}}
        <div class="sa-stat-card sa-stat-hero sa-fade-in" style="--delay:.1s">
            <div class="sa-stat-icon-wrap sa-stat-icon--accent">
                <span class="material-symbols-outlined">monitoring</span>
            </div>
            <div class="sa-stat-body">
                <span class="sa-stat-label">Overall Score</span>
                <span class="sa-stat-value sa-counter" data-target="{{ $summary['overall']['percentage'] }}">0</span>
            </div>
            <span class="sa-stat-unit">%</span>
            <div class="sa-stat-bar-track">
                <div class="sa-stat-bar sa-bar-accent" data-width="{{ $summary['overall']['percentage'] }}"></div>
            </div>
        </div>

        {{-- Present --}}
        <div class="sa-stat-card sa-fade-in" style="--delay:.18s">
            <div class="sa-stat-icon-wrap sa-stat-icon--success">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
            <div class="sa-stat-body">
                <span class="sa-stat-label">Present</span>
                <span class="sa-stat-value sa-counter" data-target="{{ $summary['overall']['present'] }}">0</span>
            </div>
            <span class="sa-stat-sessions">sessions</span>
        </div>

        {{-- Absent --}}
        <div class="sa-stat-card sa-fade-in" style="--delay:.26s">
            <div class="sa-stat-icon-wrap sa-stat-icon--danger">
                <span class="material-symbols-outlined">cancel</span>
            </div>
            <div class="sa-stat-body">
                <span class="sa-stat-label">Absent</span>
                <span class="sa-stat-value sa-counter" data-target="{{ $summary['overall']['absent'] }}">0</span>
            </div>
            <span class="sa-stat-sessions">sessions</span>
        </div>

        {{-- Total --}}
        <div class="sa-stat-card sa-fade-in" style="--delay:.34s">
            <div class="sa-stat-icon-wrap sa-stat-icon--info">
                <span class="material-symbols-outlined">event_note</span>
            </div>
            <div class="sa-stat-body">
                <span class="sa-stat-label">Total</span>
                <span class="sa-stat-value sa-counter" data-target="{{ $summary['overall']['total'] }}">0</span>
            </div>
            <span class="sa-stat-sessions">sessions</span>
        </div>
    </div>

    {{-- ACTIVE SESSION --}}
    <div class="sa-session-card sa-fade-in" style="--delay:.4s">
        <div class="sa-session-glow"></div>

        <div class="sa-session-head">
            <div class="sa-session-badge">
                <span class="sa-session-badge-dot"></span>
                Live Session
            </div>
            <div class="sa-session-today">
                <span class="material-symbols-outlined" style="font-size:16px">calendar_today</span>
                {{ now()->format('D, d M Y') }}
            </div>
        </div>

        @if(!$activeSession)
            {{-- No Active Session --}}
            <div class="sa-empty-state sa-fade-in" style="--delay:.5s">
                <div class="sa-empty-visual">
                    <div class="sa-empty-bg-ring"></div>
                    <span class="material-symbols-outlined sa-empty-icon">event_available</span>
                </div>
                <h3 class="sa-empty-title">No Active Session</h3>
                <p class="sa-empty-desc">Your next class session will appear here.<br>Sit tight — you're all caught up!</p>
            </div>

        @elseif($activeSession->is_marked)
            {{-- Already Marked --}}
            <div class="sa-marked-state sa-fade-in" style="--delay:.5s">
                <div class="sa-marked-visual">
                    <div class="sa-marked-pulse"></div>
                    <div class="sa-marked-icon-wrap">
                        <span class="material-symbols-outlined sa-marked-icon">verified</span>
                    </div>
                </div>
                <div class="sa-marked-info">
                    <span class="sa-marked-badge">✓ Attendance Recorded</span>
                    <h3 class="sa-session-subject">{{ $activeSession->subject->name }}</h3>
                    <div class="sa-session-meta-row">
                        <div class="sa-meta-tag">
                            <span class="material-symbols-outlined" style="font-size:15px">person</span>
                            {{ $activeSession->teacher->name }}
                        </div>
                        <div class="sa-meta-tag">
                            <span class="material-symbols-outlined" style="font-size:15px">schedule</span>
                            Period {{ $activeSession->period_number }}
                        </div>
                    </div>
                </div>
            </div>

        @else
            {{-- Can Mark --}}
            <div x-show="!marked" class="sa-active-state">
                <div>
                    <h3 class="sa-session-subject">{{ $activeSession->subject->name }}</h3>
                    <div class="sa-session-meta-row">
                        <div class="sa-meta-tag">
                            <span class="material-symbols-outlined" style="font-size:15px">person</span>
                            {{ $activeSession->teacher->name }}
                        </div>
                        <div class="sa-meta-tag">
                            <span class="material-symbols-outlined" style="font-size:15px">schedule</span>
                            Period {{ $activeSession->period_number }}
                        </div>
                    </div>
                </div>

                <div class="sa-active-bottom">
                    <div class="sa-timer-block"
                         x-data="timer({{ \Carbon\Carbon::parse($activeSession->start_time)->addMinutes($activeSession->attendance_window_minutes)->timestamp }})">
                        <p class="sa-timer-label">
                            <span class="material-symbols-outlined" style="font-size:14px">timer</span>
                            Window Closes In
                        </p>
                        <div class="sa-timer-value" x-text="displayTime">00:00</div>
                        <div class="sa-timer-track">
                            <div class="sa-timer-fill" :style="`width:${timerPct}%`"></div>
                        </div>
                    </div>

                    <div style="background: rgba(14, 165, 233, 0.1); border: 1px dashed var(--sa-accent); border-radius: var(--sa-radius); padding: 16px; text-align: center; margin-top: 16px; width: 100%;">
                        <span class="material-symbols-outlined" style="font-size: 28px; color: var(--sa-accent); margin-bottom: 8px;">camera_front</span>
                        <p style="font-size: 13px; font-weight: 600; color: var(--sa-text); margin: 0;">AI Camera Verification</p>
                        <p style="font-size: 11px; color: var(--sa-sub); margin: 4px 0 0;">Please look at the teacher's camera. Attendance will be marked automatically.</p>
                    </div>

                    {{-- 
                    <button @click="markAttendance({{ $activeSession->id }})"
                            :disabled="loading"
                            class="sa-btn-cta"
                            :class="loading && 'sa-btn-loading'">
                        <span class="sa-btn-bg"></span>
                        <span class="sa-btn-content" x-show="!loading">
                            <span class="material-symbols-outlined">fingerprint</span>
                            Mark My Attendance
                        </span>
                        <span class="sa-btn-content" x-show="loading" x-cloak>
                            <svg class="sa-spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="32" stroke-dashoffset="8"/></svg>
                            Verifying…
                        </span>
                    </button> 
                    --}}
                </div>
            </div>

            {{-- Success State --}}
            <div x-show="marked" x-cloak
                 x-transition:enter="sa-success-enter"
                 x-transition:enter-start="sa-success-start"
                 x-transition:enter-end="sa-success-end"
                 class="sa-marked-state">
                <div class="sa-marked-visual">
                    <div class="sa-marked-pulse"></div>
                    <div class="sa-marked-icon-wrap sa-confetti-pop">
                        <span class="material-symbols-outlined sa-marked-icon">verified</span>
                    </div>
                </div>
                <div class="sa-marked-info">
                    <span class="sa-marked-badge">✓ Successfully Verified</span>
                    <h3 class="sa-session-subject">{{ $activeSession->subject->name }}</h3>
                    <p class="sa-empty-desc" style="margin-top:4px">Your attendance has been recorded with GPS verification.</p>
                </div>
            </div>
        @endif
    </div>

    {{-- SUBJECT BREAKDOWN --}}
    <div class="sa-section sa-fade-in" style="--delay:.5s">
        <div class="sa-section-head">
            <div>
                <h2 class="sa-section-title">Subject Breakdown</h2>
                <p class="sa-section-sub">Attendance per subject — real-time data</p>
            </div>
            <div class="sa-pill">{{ count($summary['subjects']) }} Subjects</div>
        </div>

        <div class="sa-subj-grid">
            @foreach($summary['subjects'] as $idx => $subject)
            @php
                $pct = $subject['percentage'];
                if($pct >= 85)     { $accent = '#10B981'; $accentBg = '#D1FAE5'; $tag = 'Excellent'; $icon = 'emoji_events'; }
                elseif($pct >= 75) { $accent = '#0EA5E9'; $accentBg = '#E0F2FE'; $tag = 'Good';      $icon = 'thumb_up'; }
                elseif($pct >= 60) { $accent = '#F59E0B'; $accentBg = '#FEF3C7'; $tag = 'Average';   $icon = 'warning'; }
                else               { $accent = '#EF4444'; $accentBg = '#FEE2E2'; $tag = 'At Risk';   $icon = 'error'; }
            @endphp
            <div class="sa-subj-card sa-fade-in" style="--delay:{{ .55 + $idx * .07 }}s">
                <div class="sa-subj-top">
                    <div class="sa-subj-icon" style="background:{{ $accentBg }}">
                        <span class="material-symbols-outlined" style="font-size:20px;color:{{ $accent }}">menu_book</span>
                    </div>
                    <div class="sa-subj-info">
                        <h4 class="sa-subj-name">{{ $subject['subject'] }}</h4>
                        <span class="sa-subj-sessions">{{ $subject['present'] }} / {{ $subject['total'] }} sessions</span>
                    </div>
                    <div class="sa-subj-right">
                        <span class="sa-subj-pct" style="color:{{ $accent }}">{{ $pct }}%</span>
                        <span class="sa-subj-tag" style="background:{{ $accentBg }};color:{{ $accent }}">
                            <span class="material-symbols-outlined" style="font-size:12px">{{ $icon }}</span>
                            {{ $tag }}
                        </span>
                    </div>
                </div>
                <div class="sa-subj-bar-track">
                    <div class="sa-subj-bar" style="background:{{ $accent }}" data-width="{{ $pct }}">
                        <div class="sa-bar-shine"></div>
                    </div>
                </div>
                @if($subject['total'] - $subject['present'] > 0)
                    <div class="sa-subj-note">
                        <span class="material-symbols-outlined" style="font-size:13px;color:#EF4444">info</span>
                        {{ $subject['total'] - $subject['present'] }} absent
                        @if($pct < 75)
                            — <strong style="color:#EF4444">Need {{ max(0, ceil(0.75 * $subject['total'] - $subject['present'])) }} more to reach 75%</strong>
                        @endif
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- Toast --}}
<div id="toast-container" class="sa-toast-container"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {

    Alpine.data('studentAttendance', () => ({
        loading: false,
        marked: false,

        initPage() {
            this.$nextTick(() => {
                // Animated counters
                document.querySelectorAll('.sa-counter').forEach(el => {
                    const target = parseInt(el.dataset.target, 10);
                    const duration = 1200;
                    const start = performance.now();
                    const step = (now) => {
                        const t = Math.min((now - start) / duration, 1);
                        const ease = 1 - Math.pow(1 - t, 3);
                        el.textContent = Math.round(ease * target);
                        if (t < 1) requestAnimationFrame(step);
                    };
                    setTimeout(() => requestAnimationFrame(step), 400);
                });

                // Animate bars
                document.querySelectorAll('.sa-stat-bar, .sa-subj-bar').forEach((bar, i) => {
                    const w = bar.dataset.width;
                    setTimeout(() => { bar.style.width = w + '%'; }, 500 + i * 80);
                });
            });
        },

        markAttendance(sessionId) {
            this.loading = true;
            if (!navigator.geolocation) {
                this.showToast('Geolocation is not supported by your browser', 'error');
                this.loading = false;
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => this.submitAttendance(sessionId, pos.coords.latitude, pos.coords.longitude),
                () => {
                    this.showToast('GPS access is required to mark attendance', 'error');
                    this.loading = false;
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        },

        async submitAttendance(sessionId, lat, lng) {
            try {
                const res = await fetch('{{ route("student.attendance.mark") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        lecture_session_id: sessionId,
                        student_lat: lat,
                        student_long: lng
                    })
                });
                const data = await res.json();

                if (data.success) {
                    this.marked = true;
                    this.showToast('Attendance marked successfully! 🎉', 'success');
                    setTimeout(() => window.location.reload(), 2500);
                } else {
                    this.showToast(data.message || 'Failed to mark attendance', 'error');
                }
            } catch (e) {
                this.showToast('Network error. Please try again.', 'error');
            } finally {
                this.loading = false;
            }
        },

        showToast(message, type = 'success') {
            const c = document.getElementById('toast-container');
            const el = document.createElement('div');
            el.className = `sa-toast sa-toast--${type}`;

            const iconName = type === 'success' ? 'check_circle' : 'error';
            const iconColor = type === 'success' ? 'var(--sa-success)' : 'var(--sa-danger)';

            el.innerHTML = `
                <span class="material-symbols-outlined" style="font-size:20px;color:${iconColor}">${iconName}</span>
                <p style="margin:0;flex:1">${message}</p>
            `;
            c.appendChild(el);

            requestAnimationFrame(() => requestAnimationFrame(() => el.classList.add('sa-toast-show')));
            setTimeout(() => {
                el.classList.remove('sa-toast-show');
                setTimeout(() => el.remove(), 400);
            }, 4000);
        }
    }));

    Alpine.data('timer', (expiry) => ({
        expiry: expiry,
        displayTime: '00:00',
        timerPct: 100,
        totalWindow: null,

        init() {
            this.totalWindow = expiry - Math.floor(Date.now() / 1000);
            if (this.totalWindow <= 0) this.totalWindow = 1;
            this.tick();
            setInterval(() => this.tick(), 1000);
        },

        tick() {
            const now = Math.floor(Date.now() / 1000);
            const diff = this.expiry - now;

            if (diff <= 0) {
                this.displayTime = '00:00';
                this.timerPct = 0;
                return;
            }
            const m = Math.floor(diff / 60);
            const s = diff % 60;
            this.displayTime = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
            this.timerPct = Math.max(0, Math.min(100, (diff / this.totalWindow) * 100));
        }
    }));
});
</script>
@endpush
