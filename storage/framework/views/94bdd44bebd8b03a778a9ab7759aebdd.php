<?php $__env->startSection('content'); ?>




<div class="relative rounded-2xl p-8 mb-8 overflow-hidden shadow-[0_4px_20px_rgba(124,58,237,0.3)] border border-indigo-500/10" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 50%, #6d28d9 100%);">
    
    <div class="absolute right-0 top-0 h-full opacity-10 pointer-events-none">
        <svg viewBox="0 0 200 200" fill="white" class="h-full">
            <circle cx="160" cy="40" r="110"/>
            <circle cx="100" cy="150" r="40" fill="none" stroke="white" stroke-width="4"/>
        </svg>
    </div>
    
    <div class="relative z-10 max-w-2xl">
        <div class="flex items-center gap-2 mb-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold tracking-wider uppercase bg-white/15 border border-white/20 text-white shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                AI Core Engine · Llama 3.2
            </span>
        </div>
        <h1 class="text-2xl lg:text-3xl font-black text-white tracking-tight leading-tight mb-2">
            AI Attendance Risk Prediction
        </h1>
        <p class="text-white/80 text-sm font-medium leading-relaxed">
            Harness real-time student participation metrics processed through our neural classifier to identify academic risk before it impacts grades.
        </p>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <?php
        $kpis = [
            [
                'label' => 'Total Students',  
                'value' => $summary['total'],  
                'icon' => 'group',            
                'glow' => 'hover:shadow-[0_0_25px_rgba(99,102,241,0.15)] hover:border-indigo-500/30',
                'bg' => 'bg-indigo-500/10',
                'icon_color' => 'text-indigo-400',
                'accent' => 'border-indigo-500/20'
            ],
            [
                'label' => 'High Risk Level',    
                'value' => $summary['high'],   
                'icon' => 'crisis_alert',     
                'glow' => 'hover:shadow-[0_0_25px_rgba(239,68,68,0.15)] hover:border-red-500/30',
                'bg' => 'bg-red-500/10',
                'icon_color' => 'text-red-400',
                'accent' => 'border-red-500/20'
            ],
            [
                'label' => 'Medium Risk Level',  
                'value' => $summary['medium'], 
                'icon' => 'warning',          
                'glow' => 'hover:shadow-[0_0_25px_rgba(234,179,8,0.15)] hover:border-yellow-500/30',
                'bg' => 'bg-yellow-500/10',
                'icon_color' => 'text-yellow-400',
                'accent' => 'border-yellow-500/20'
            ],
            [
                'label' => 'Low Risk Level',     
                'value' => $summary['low'],    
                'icon' => 'verified',     
                'glow' => 'hover:shadow-[0_0_25px_rgba(16,185,129,0.15)] hover:border-emerald-500/30',
                'bg' => 'bg-emerald-500/10',
                'icon_color' => 'text-emerald-400',
                'accent' => 'border-emerald-500/20'
            ],
        ];
    ?>
    <?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="relative rounded-2xl bg-white border border-slate-100 p-5 transition-all duration-300 hover:-translate-y-1.5 shadow-[0_4px_12px_rgba(0,0,0,0.02)] <?php echo e($kpi['glow']); ?>">
        
        <div class="absolute left-0 top-4 bottom-4 w-[4px] rounded-r-md <?php echo e(str_replace('text', 'bg', $kpi['icon_color'])); ?>"></div>
        
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl <?php echo e($kpi['bg']); ?> flex items-center justify-center flex-shrink-0 transition-transform duration-300 group-hover:scale-110">
                <span class="material-symbols-outlined <?php echo e($kpi['icon_color']); ?> text-[24px]"><?php echo e($kpi['icon']); ?></span>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800 tracking-tight leading-none mb-1"><?php echo e($kpi['value']); ?></div>
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider"><?php echo e($kpi['label']); ?></div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="rounded-2xl bg-white border border-slate-100 p-5 mb-8 shadow-[0_4px_12px_rgba(0,0,0,0.02)]">
    <div class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center justify-between">
        
        
        <form method="GET" action="<?php echo e(route('teacher.risk.index')); ?>" class="flex gap-2 flex-1 max-w-md">
            <div class="relative flex-1 group">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] group-focus-within:text-indigo-500 transition-colors">search</span>
                <input type="text" name="search" value="<?php echo e($search); ?>"
                       placeholder="Search student by name..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
            </div>
            <input type="hidden" name="risk" value="<?php echo e($riskFilter); ?>">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-[0_4px_10px_rgba(79,70,229,0.2)] hover:shadow-[0_4px_14px_rgba(79,70,229,0.3)] transition-all">
                Search
            </button>
        </form>

        
        <div class="flex items-center gap-1.5 p-1 bg-slate-50 border border-slate-100 rounded-xl overflow-x-auto max-w-full">
            <?php $__currentLoopData = [
                ['all', 'All Active', 'text-slate-600 hover:bg-slate-100'],
                ['high', '🚨 High Risk', 'text-red-600 hover:bg-red-50/50'],
                ['medium', '⚠️ Medium Risk', 'text-yellow-600 hover:bg-yellow-50/50'],
                ['low', '✅ Low Risk', 'text-emerald-600 hover:bg-emerald-50/50']
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$val, $label, $cls]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('teacher.risk.index', ['risk' => $val, 'search' => $search])); ?>"
                   class="px-4 py-2 rounded-lg text-xs font-bold whitespace-nowrap transition-all duration-200
                          <?php echo e($riskFilter === $val 
                             ? 'bg-white text-indigo-600 shadow-[0_2px_8px_rgba(0,0,0,0.04)] border border-slate-100' 
                             : 'text-slate-500 ' . $cls); ?>">
                    <?php echo e($label); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <button id="btn-predict-all"
                class="flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-all
                       bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 hover:from-indigo-500 hover:to-purple-500 shadow-[0_4px_12px_rgba(79,70,229,0.3)] hover:shadow-[0_4px_20px_rgba(79,70,229,0.4)] hover:scale-[1.02]">
            <span class="material-symbols-outlined text-[18px]">auto_awesome</span>
            <span>Batch Analysis</span>
        </button>
    </div>
</div>


<div id="ai-progress-bar" class="hidden mb-8 animate-fade-in">
    <div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/40 to-purple-50/30 p-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-indigo-600 text-[18px] animate-spin">refresh</span>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-800">Neural Network Active</p>
                <p id="progress-text" class="text-xs text-slate-400">Classifying attendance vectors...</p>
            </div>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
            <div id="progress-fill" class="h-2 rounded-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 transition-all duration-300" style="width:0%"></div>
        </div>
    </div>
</div>


<?php if($students->isEmpty()): ?>
<div class="rounded-2xl bg-white border border-slate-100 flex flex-col items-center justify-center py-20 text-center shadow-[0_4px_12px_rgba(0,0,0,0.02)]">
    <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center mb-4">
        <span class="material-symbols-outlined text-indigo-400 text-[32px]">person_search</span>
    </div>
    <h3 class="text-base font-bold text-slate-800 mb-1">No student vectors found</h3>
    <p class="text-xs text-slate-400 max-w-xs leading-relaxed">No students currently match your selected filters. Ensure attendance sessions have been created.</p>
</div>
<?php else: ?>

<div id="students-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $pred     = $student->latestRiskPrediction;
        $riskLvl  = $pred?->risk_level ?? 'unanalysed';
        
        $badgeMap = [
            'high' => 'bg-red-50 text-red-600 border-red-200/50', 
            'medium' => 'bg-yellow-50 text-yellow-600 border-yellow-200/50', 
            'low' => 'bg-emerald-50 text-emerald-600 border-emerald-200/50', 
            'unanalysed' => 'bg-slate-50 text-slate-500 border-slate-200'
        ];
        
        $labelMap = [
            'high' => '🚨 High Risk', 
            'medium' => '⚠️ Medium Risk', 
            'low' => '✅ Low Risk', 
            'unanalysed' => '— Not Analysed'
        ];
        
        $glowMap  = [
            'high' => 'hover:border-red-300 hover:shadow-[0_8px_30px_rgba(239,68,68,0.08)]', 
            'medium' => 'hover:border-yellow-300 hover:shadow-[0_8px_30px_rgba(234,179,8,0.08)]', 
            'low' => 'hover:border-emerald-300 hover:shadow-[0_8px_30px_rgba(16,185,129,0.08)]', 
            'unanalysed' => 'hover:border-indigo-200'
        ];
        
        $pct      = $student->att_pct;
        $barColor = match($riskLvl) { 
            'high' => 'bg-gradient-to-r from-red-500 to-rose-500', 
            'medium' => 'bg-gradient-to-r from-yellow-500 to-amber-500', 
            'low' => 'bg-gradient-to-r from-emerald-500 to-teal-500', 
            default => 'bg-slate-300' 
        };
    ?>

    <div class="student-card relative rounded-2xl bg-white border border-slate-100 p-6 transition-all duration-300 shadow-[0_4px_12px_rgba(0,0,0,0.01)] <?php echo e($glowMap[$riskLvl]); ?>"
         data-student-id="<?php echo e($student->id); ?>"
         data-student-name="<?php echo e($student->user?->name); ?>"
         id="card-<?php echo e($student->id); ?>">

        
        <div class="absolute left-6 right-6 top-0 h-[3px] rounded-b-md
            <?php echo e(match($riskLvl) { 'high' => 'bg-red-400', 'medium' => 'bg-yellow-400', 'low' => 'bg-emerald-400', default => 'bg-slate-200' }); ?>" id="topbar-<?php echo e($student->id); ?>"></div>

        
        <div class="flex items-start justify-between mb-5 mt-1">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 font-extrabold text-sm
                    <?php echo e(match($riskLvl) { 'high' => 'bg-red-50 text-red-500', 'medium' => 'bg-yellow-50 text-yellow-500', 'low' => 'bg-emerald-50 text-emerald-500', default => 'bg-slate-50 text-slate-500' }); ?>" id="avatar-<?php echo e($student->id); ?>">
                    <?php echo e(strtoupper(substr($student->user?->name ?? 'S', 0, 2))); ?>

                </div>
                <div class="min-w-0">
                    <p class="text-sm font-extrabold text-slate-800 truncate leading-snug"><?php echo e($student->user?->name ?? '—'); ?></p>
                    <p class="text-[11px] font-semibold text-slate-400 truncate mt-0.5"><?php echo e($student->batch?->course?->code ?? ''); ?> · Sem <?php echo e($student->current_unit); ?></p>
                </div>
            </div>
            <span class="risk-badge px-2.5 py-1 rounded-lg text-[10px] font-bold border <?php echo e($badgeMap[$riskLvl]); ?> flex-shrink-0"
                  id="badge-<?php echo e($student->id); ?>">
                <?php echo e($labelMap[$riskLvl]); ?>

            </span>
        </div>

        
        <div class="mb-5 bg-slate-50/50 border border-slate-100 rounded-xl p-3.5">
            <div class="flex justify-between items-center mb-1.5">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Attendance Rate</span>
                <span class="text-xs font-black text-slate-700 att-pct-label" id="pct-<?php echo e($student->id); ?>"><?php echo e($pct); ?>%</span>
            </div>
            <div class="w-full bg-slate-200/60 rounded-full h-2 overflow-hidden">
                <div class="h-2 rounded-full att-bar transition-all duration-700 <?php echo e($barColor); ?>"
                     id="bar-<?php echo e($student->id); ?>"
                     style="width:<?php echo e(min(100, $pct)); ?>%"></div>
            </div>
            <div class="flex justify-between text-[10px] text-slate-400 font-semibold mt-2">
                <span class="flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <?php echo e($student->att_present); ?> Present
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    <?php echo e($student->att_absent); ?> Absent
                </span>
            </div>
        </div>

        
        <div class="mb-5 ai-remark-section" id="remark-<?php echo e($student->id); ?>">
            <?php if($pred): ?>
            <div class="p-3.5 rounded-xl bg-slate-50 border-l-4 border-slate-400">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">AI Classification Remark</p>
                <p class="text-xs text-slate-600 leading-relaxed font-medium"><?php echo e($pred->ai_remark); ?></p>
            </div>
            <?php else: ?>
            <div class="p-3.5 rounded-xl bg-slate-50/50 border border-dashed border-slate-200 text-center">
                <p class="text-xs text-slate-400 font-medium">Model pending analysis</p>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="mb-5 <?php echo e(($pred && $pred->suggested_action) ? '' : 'hidden'); ?>" id="action-<?php echo e($student->id); ?>">
            <?php if($pred && $pred->suggested_action): ?>
            <div class="p-3.5 rounded-xl border
                <?php echo e(match($riskLvl) { 'high' => 'bg-red-50/30 border-red-100', 'medium' => 'bg-yellow-50/30 border-yellow-100', default => 'bg-emerald-50/30 border-emerald-100' }); ?>">
                <p class="text-[10px] font-bold uppercase tracking-wider mb-1
                    <?php echo e(match($riskLvl) { 'high' => 'text-red-500', 'medium' => 'text-yellow-500', default => 'text-emerald-500' }); ?>">
                    Prescribed Intervention
                </p>
                <p class="text-xs text-slate-600 leading-relaxed font-semibold"><?php echo e($pred->suggested_action); ?></p>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <?php if($pred): ?>
            <span class="text-[10px] font-semibold text-slate-400 flex items-center gap-1">
                <span class="material-symbols-outlined text-[12px]">schedule</span>
                <?php echo e($pred->updated_at->diffForHumans()); ?>

            </span>
            <?php else: ?>
            <span class="text-[10px] font-semibold text-slate-400">Pending Evaluation</span>
            <?php endif; ?>

            <button class="btn-predict-one flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold transition-all
                           bg-indigo-50 hover:bg-indigo-600 border border-indigo-200/60 text-indigo-600 hover:text-white hover:border-transparent hover:shadow-[0_4px_10px_rgba(79,70,229,0.15)]"
                    data-student-id="<?php echo e($student->id); ?>"
                    data-predict-url="<?php echo e(route('teacher.risk.predict', $student->id)); ?>">
                <span class="material-symbols-outlined text-[14px]">auto_awesome</span>
                <span class="btn-label">Analyse</span>
            </button>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// ─── AI Risk Prediction — AJAX JS ────────────────────────────────────────────

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ─── Predict Single Student ─────────────────────────────────────────────────

document.querySelectorAll('.btn-predict-one').forEach(btn => {
    btn.addEventListener('click', function () {
        const studentId  = this.dataset.studentId;
        const url        = this.dataset.predictUrl;
        const btnEl      = this;
        const labelEl    = this.querySelector('.btn-label');
        const iconEl     = this.querySelector('.material-symbols-outlined');

        // Loading state
        iconEl.textContent   = 'hourglass_top';
        iconEl.classList.add('animate-spin');
        labelEl.textContent  = 'Analysing…';
        btnEl.disabled       = true;

        fetch(url, {
            method:  'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                updateStudentCard(studentId, data);
                showToast(`✅ ${data.risk_label} — AI analysis complete`, data.risk_level);
            } else {
                showToast('❌ Prediction failed: ' + (data.message ?? 'Unknown error'), 'error');
            }
        })
        .catch(err => showToast('❌ Network error: ' + err.message, 'error'))
        .finally(() => {
            iconEl.textContent  = 'auto_awesome';
            iconEl.classList.remove('animate-spin');
            labelEl.textContent = 'Re-Analyse';
            btnEl.disabled      = false;
        });
    });
});

// ─── Predict All ─────────────────────────────────────────────────────────────

document.getElementById('btn-predict-all')?.addEventListener('click', function () {
    const bar      = document.getElementById('ai-progress-bar');
    const fill     = document.getElementById('progress-fill');
    const txt      = document.getElementById('progress-text');
    const btnEl    = this;
    const cards    = document.querySelectorAll('.student-card');
    const total    = cards.length;
    let   processed = 0;

    if (total === 0) { showToast('No students to analyse.', 'info'); return; }

    bar.classList.remove('hidden');
    btnEl.disabled = true;
    fill.style.width = '0%';
    txt.textContent  = `Processing student 0 of ${total}…`;

    const processNext = (index) => {
        if (index >= total) {
            fill.style.width = '100%';
            txt.textContent  = `✅ All ${total} students analysed!`;
            btnEl.disabled   = false;
            setTimeout(() => bar.classList.add('hidden'), 3000);
            return;
        }

        const card      = cards[index];
        const studentId = card.dataset.studentId;
        const url       = `/teacher/risk/predict/${studentId}`;

        fetch(url, {
            method:  'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) updateStudentCard(studentId, data);
        })
        .finally(() => {
            processed++;
            const pct        = Math.round((processed / total) * 100);
            fill.style.width = pct + '%';
            txt.textContent  = `Processing student ${processed} of ${total}…`;
            setTimeout(() => processNext(index + 1), 200);
        });
    };

    processNext(0);
});

// ─── Update Card DOM ─────────────────────────────────────────────────────────

function updateStudentCard(studentId, data) {
    const riskLevel   = data.risk_level;
    const badgeMap    = {
        high:   'bg-red-50 text-red-600 border-red-200/50',
        medium: 'bg-yellow-50 text-yellow-600 border-yellow-200/50',
        low:    'bg-emerald-50 text-emerald-600 border-emerald-200/50',
    };
    const barColorMap = { 
        high: 'bg-gradient-to-r from-red-500 to-rose-500', 
        medium: 'bg-gradient-to-r from-yellow-500 to-amber-500', 
        low: 'bg-gradient-to-r from-emerald-500 to-teal-500' 
    };
    const actionColorMap = {
        high:   'bg-red-50/30 border-red-100',
        medium: 'bg-yellow-50/30 border-yellow-100',
        low:    'bg-emerald-50/30 border-emerald-100',
    };
    const actionLabelMap = { high: 'text-red-500', medium: 'text-yellow-500', low: 'text-emerald-500' };
    const avatarColorMap = {
        high: 'bg-red-50 text-red-505',
        medium: 'bg-yellow-50 text-yellow-505',
        low: 'bg-emerald-50 text-emerald-505'
    };
    
    // Top border accent
    const topBar = document.getElementById('topbar-' + studentId);
    if (topBar) {
        topBar.className = "absolute left-6 right-6 top-0 h-[3px] rounded-b-md " + 
            (riskLevel === 'high' ? 'bg-red-400' : (riskLevel === 'medium' ? 'bg-yellow-400' : 'bg-emerald-400'));
    }

    // Avatar color
    const avatar = document.getElementById('avatar-' + studentId);
    if (avatar) {
        avatar.className = "w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 font-extrabold text-sm " + 
            (riskLevel === 'high' ? 'bg-red-50 text-red-500' : (riskLevel === 'medium' ? 'bg-yellow-50 text-yellow-500' : 'bg-emerald-50 text-emerald-500'));
    }

    // Badge
    const badge = document.getElementById('badge-' + studentId);
    if (badge) { badge.className = `risk-badge px-2.5 py-1 rounded-lg text-[10px] font-bold border flex-shrink-0 ${badgeMap[riskLevel]}`; badge.textContent = data.risk_label; }

    // Attendance bar
    const bar = document.getElementById('bar-' + studentId);
    if (bar) { bar.className = `h-2 rounded-full att-bar transition-all duration-700 ${barColorMap[riskLevel]}`; bar.style.width = Math.min(100, data.att_pct) + '%'; }

    // Pct label
    const pct = document.getElementById('pct-' + studentId);
    if (pct) pct.textContent = data.att_pct + '%';

    // AI Remark with severity colored border
    const borderAccent = riskLevel === 'high' ? 'border-red-400' : (riskLevel === 'medium' ? 'border-yellow-400' : 'border-emerald-400');
    const remarkEl = document.getElementById('remark-' + studentId);
    if (remarkEl) {
        remarkEl.innerHTML = `
            <div class="p-3.5 rounded-xl bg-slate-50 border-l-4 ${borderAccent}">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">AI Classification Remark</p>
                <p class="text-xs text-slate-600 leading-relaxed font-medium">${data.ai_remark}</p>
            </div>`;
    }

    // Suggested Action
    const actionEl = document.getElementById('action-' + studentId);
    if (actionEl) {
        actionEl.classList.remove('hidden');
        actionEl.innerHTML = `
            <div class="p-3.5 rounded-xl border ${actionColorMap[riskLevel]}">
                <p class="text-[10px] font-bold uppercase tracking-wider mb-1 ${actionLabelMap[riskLevel]}">Prescribed Intervention</p>
                <p class="text-xs text-slate-600 leading-relaxed font-semibold">${data.suggested_action}</p>
            </div>`;
    }
}

// ─── Toast Notification ──────────────────────────────────────────────────────

function showToast(message, type = 'info') {
    const colorMap = { error: 'bg-red-600', info: 'bg-indigo-600', high: 'bg-red-600', medium: 'bg-yellow-600', low: 'bg-emerald-600' };
    const toast = document.createElement('div');
    toast.className = `fixed bottom-6 right-6 z-50 px-4 py-3 rounded-xl text-white text-sm font-semibold shadow-xl transform translate-y-2 opacity-0 transition-all duration-300 ${colorMap[type] ?? 'bg-indigo-600'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    requestAnimationFrame(() => { toast.style.transform = 'translateY(0)'; toast.style.opacity = '1'; });
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(8px)'; setTimeout(() => toast.remove(), 400); }, 4000);
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views\teacher\risk\index.blade.php ENDPATH**/ ?>