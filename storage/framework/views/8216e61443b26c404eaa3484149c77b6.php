<?php $__env->startSection('content'); ?>




<div class="relative rounded-2xl p-8 mb-6 overflow-hidden shadow-[0_4px_20px_rgba(124,58,237,0.3)] border border-indigo-500/10" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 50%, #6d28d9 100%);">
    
    <div class="absolute right-0 top-0 h-full opacity-10 pointer-events-none">
        <svg viewBox="0 0 200 200" fill="white" class="h-full">
            <circle cx="160" cy="40" r="110"/>
            <circle cx="100" cy="150" r="40" fill="none" stroke="white" stroke-width="4"/>
        </svg>
    </div>
    
    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold tracking-wider uppercase bg-white/15 border border-white/20 text-white shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                    Department Overview · AI Analytics
                </span>
            </div>
            <h1 class="text-2xl lg:text-3xl font-black text-white tracking-tight leading-tight mb-2">
                AI Attendance Risk Prediction
            </h1>
            <p class="text-white/80 text-sm max-w-xl font-medium">
                Department-wide academic risk monitoring powered by Llama-3.2 AI intelligence.
            </p>
        </div>

        
        <div class="flex gap-4">
            <div class="px-5 py-3.5 rounded-xl text-center bg-white/10 backdrop-blur-md border border-white/20 shadow-sm min-w-[110px]">
                <p class="text-[9px] font-extrabold text-white/70 uppercase tracking-widest mb-0.5">Analysed</p>
                <p class="text-2xl font-black text-white leading-none mb-0.5"><?php echo e($summary['analysed']); ?>/<?php echo e($summary['total_students']); ?></p>
                <p class="text-[9px] text-white/50 font-bold">students</p>
            </div>
            <div class="px-5 py-3.5 rounded-xl text-center bg-white/10 backdrop-blur-md border border-white/20 shadow-sm min-w-[110px]">
                <p class="text-[9px] font-extrabold text-white/70 uppercase tracking-widest mb-0.5">Avg. Attendance</p>
                <p class="text-2xl font-black text-white leading-none mb-0.5"><?php echo e(number_format($summary['avg_attendance'], 1)); ?>%</p>
                <p class="text-[9px] text-white/50 font-bold">department</p>
            </div>
        </div>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <?php
        $kpis = [
            [
                'label' => 'Total Students',  
                'value' => $summary['total_students'],  
                'icon' => 'groups',            
                'glow' => 'hover:shadow-[0_0_25px_rgba(139,92,246,0.15)] hover:border-purple-500/30',
                'bg' => 'bg-purple-500/10',
                'icon_color' => 'text-purple-400',
                'accent' => 'border-purple-500/20'
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
            <div class="w-12 h-12 rounded-xl <?php echo e($kpi['bg']); ?> flex items-center justify-center flex-shrink-0">
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


<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    
    <div class="rounded-2xl bg-white border border-slate-100 p-6 shadow-[0_4px_12px_rgba(0,0,0,0.02)] flex flex-col items-center justify-center">
        <h3 class="text-sm font-extrabold text-slate-800 self-start mb-6 uppercase tracking-wider">Risk Distribution Ratio</h3>
        <?php
            $total = max(1, $summary['high'] + $summary['medium'] + $summary['low']);
            $highPct   = round(($summary['high']   / $total) * 100);
            $medPct    = round(($summary['medium'] / $total) * 100);
            $lowPct    = round(($summary['low']    / $total) * 100);
        ?>
        
        <div class="relative w-36 h-36 mb-6">
            <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90">
                <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#f1f5f9" stroke-width="4"/>
                <circle cx="18" cy="18" r="15.9155" fill="none"
                        stroke="#10b981" stroke-width="4"
                        stroke-dasharray="<?php echo e($lowPct); ?> <?php echo e(100 - $lowPct); ?>"
                        stroke-dashoffset="0"/>
                <circle cx="18" cy="18" r="15.9155" fill="none"
                        stroke="#f59e0b" stroke-width="4"
                        stroke-dasharray="<?php echo e($medPct); ?> <?php echo e(100 - $medPct); ?>"
                        stroke-dashoffset="<?php echo e(100 - $lowPct); ?>"/>
                <circle cx="18" cy="18" r="15.9155" fill="none"
                        stroke="#ef4444" stroke-width="4"
                        stroke-dasharray="<?php echo e($highPct); ?> <?php echo e(100 - $highPct); ?>"
                        stroke-dashoffset="<?php echo e(100 - $lowPct - $medPct); ?>"/>
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center leading-none">
                <span class="text-2xl font-black text-slate-800"><?php echo e($summary['analysed']); ?></span>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-1">Classified</span>
            </div>
        </div>

        <div class="space-y-3 w-full border-t border-slate-50 pt-5">
            <?php $__currentLoopData = [
                ['🚨 High Risk Threshold', $highPct, 'bg-red-500', $summary['high']], 
                ['⚠️ Medium Risk Threshold', $medPct, 'bg-yellow-500', $summary['medium']], 
                ['✅ Low Risk Threshold', $lowPct, 'bg-emerald-500', $summary['low']]
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $pct, $color, $count]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center justify-between text-xs font-semibold text-slate-600">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full <?php echo e($color); ?>"></span>
                    <span><?php echo e($label); ?></span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="text-slate-800 font-extrabold"><?php echo e($count); ?></span>
                    <span class="text-[10px] text-slate-400">(<?php echo e($pct); ?>%)</span>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="lg:col-span-2 rounded-2xl bg-white border border-slate-100 p-6 shadow-[0_4px_12px_rgba(0,0,0,0.02)]">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Attendance Distribution Clusters</h3>
            <button id="btn-predict-dept"
                    class="flex items-center gap-2 px-4.5 py-2 rounded-xl text-xs font-bold text-white transition-all
                           bg-purple-600 hover:bg-purple-500 shadow-[0_4px_10px_rgba(124,58,237,0.2)] hover:scale-[1.02]">
                <span class="material-symbols-outlined text-[16px]">auto_awesome</span>
                Analyse Department
            </button>
        </div>

        
        <div id="dept-progress" class="hidden mb-5 animate-fade-in">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-purple-600 text-[18px] animate-spin">refresh</span>
                <p class="text-xs text-slate-500 font-bold" id="dept-progress-txt">Processing student attendance matrices...</p>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                <div id="dept-fill" class="h-1.5 rounded-full bg-gradient-to-r from-purple-500 to-indigo-500 transition-all duration-300" style="width:0%"></div>
            </div>
        </div>

        
        <div class="space-y-4 pt-1">
            <?php
                $ranges = [
                    ['label' => 'Excellent Level (≥ 90% attendance)',  'count' => $students->where('att_pct', '>=', 90)->count(),               'color' => 'bg-gradient-to-r from-emerald-500 to-teal-500'],
                    ['label' => 'Satisfactory Level (75–89% attendance)', 'count' => $students->whereBetween('att_pct', [75, 89.99])->count(),     'color' => 'bg-gradient-to-r from-emerald-400 to-green-400'],
                    ['label' => 'Warning Level (60–74% attendance)',      'count' => $students->whereBetween('att_pct', [60, 74.99])->count(),     'color' => 'bg-gradient-to-r from-yellow-500 to-amber-500'],
                    ['label' => 'Critical Risk Level (< 60% attendance)', 'count' => $students->where('att_pct', '<', 60)->count(),                'color' => 'bg-gradient-to-r from-red-500 to-rose-500'],
                ];
                $maxCount = max(1, $ranges[0]['count'], $ranges[1]['count'], $ranges[2]['count'], $ranges[3]['count']);
            ?>
            <?php $__currentLoopData = $ranges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $range): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs font-bold text-slate-500"><?php echo e($range['label']); ?></span>
                    <span class="text-xs font-black text-slate-700"><?php echo e($range['count']); ?> Students</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="<?php echo e($range['color']); ?> h-2 rounded-full transition-all duration-700 animate-pulse-slow"
                         style="width:<?php echo e(round(($range['count'] / max(1, $students->count())) * 100)); ?>%"></div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>


<div class="rounded-2xl bg-white border border-slate-100 p-5 mb-8 shadow-[0_4px_12px_rgba(0,0,0,0.02)]">
    <div class="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center justify-between">
        <form method="GET" action="<?php echo e(route('hod.risk.index')); ?>" class="flex gap-2 flex-1 max-w-md">
            <div class="relative flex-1 group">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[18px] group-focus-within:text-purple-500 transition-colors">search</span>
                <input type="text" name="search" value="<?php echo e($search); ?>"
                       placeholder="Search student..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 transition-all">
            </div>
            <input type="hidden" name="risk" value="<?php echo e($riskFilter); ?>">
            <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-semibold transition-all">Go</button>
        </form>

        <div class="flex items-center gap-1.5 p-1 bg-slate-50 border border-slate-100 rounded-xl overflow-x-auto max-w-full">
            <?php $__currentLoopData = [
                ['all', 'All Active', 'text-slate-600 hover:bg-slate-100'], 
                ['high', '🚨 High Risk', 'text-red-600 hover:bg-red-50/50'], 
                ['medium', '⚠️ Medium Risk', 'text-yellow-600 hover:bg-yellow-50/50'], 
                ['low', '✅ Low Risk', 'text-emerald-600 hover:bg-emerald-50/50']
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$val, $label, $cls]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('hod.risk.index', ['risk' => $val, 'search' => $search])); ?>"
               class="px-4 py-2 rounded-lg text-xs font-bold whitespace-nowrap transition-all duration-200
                      <?php echo e($riskFilter === $val 
                         ? 'bg-white text-purple-600 shadow-[0_2px_8px_rgba(0,0,0,0.04)] border border-slate-100' 
                         : 'text-slate-500 ' . $cls); ?>">
                <?php echo e($label); ?>

            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>


<?php if($students->isEmpty()): ?>
<div class="rounded-2xl bg-white border border-slate-100 flex flex-col items-center justify-center py-20 text-center shadow-[0_4px_12px_rgba(0,0,0,0.02)]">
    <span class="material-symbols-outlined text-[40px] text-purple-300 mb-3 animate-pulse">person_search</span>
    <h3 class="text-base font-bold text-slate-800 mb-1">No student vectors found</h3>
    <a href="<?php echo e(route('hod.risk.index')); ?>" class="text-xs font-bold text-purple-600 mt-2 hover:underline">Clear current filters</a>
</div>
<?php else: ?>
<div class="rounded-2xl bg-white border border-slate-100 overflow-hidden shadow-[0_4px_12px_rgba(0,0,0,0.02)]">
    <div class="p-5 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
        <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">
            Student Prediction Matrix
            <span class="ml-2 text-xs font-bold text-slate-400">(<?php echo e($students->count()); ?> Studs)</span>
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/20">
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest px-6 py-4">Student</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest px-6 py-4">Batch / Course</th>
                    <th class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest px-6 py-4">Attendance Stats</th>
                    <th class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest px-6 py-4">AI Risk Classification</th>
                    <th class="text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest px-6 py-4">AI Diagnostic Remark</th>
                    <th class="text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest px-6 py-4">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50" id="hod-table-body">
                <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $pred    = $student->latestRiskPrediction;
                    $riskLvl = $pred?->risk_level ?? 'unanalysed';
                    
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
                    
                    $barColor = match($riskLvl) { 
                        'high' => 'bg-gradient-to-r from-red-500 to-rose-500', 
                        'medium' => 'bg-gradient-to-r from-yellow-500 to-amber-500', 
                        'low' => 'bg-gradient-to-r from-emerald-500 to-teal-500', 
                        default => 'bg-slate-300' 
                    };
                ?>
                <tr class="hover:bg-slate-50/50 transition-colors" id="row-<?php echo e($student->id); ?>">
                    
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0
                                <?php echo e(match($riskLvl) { 'high' => 'bg-red-50 text-red-500', 'medium' => 'bg-yellow-50 text-yellow-500', 'low' => 'bg-emerald-50 text-emerald-500', default => 'bg-slate-50 text-slate-500' }); ?>" id="row-avatar-<?php echo e($student->id); ?>">
                                <?php echo e(strtoupper(substr($student->user?->name ?? 'S', 0, 2))); ?>

                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate leading-none mb-1.5"><?php echo e($student->user?->name ?? '—'); ?></p>
                                <p class="text-[10px] font-semibold text-slate-400 truncate"><?php echo e($student->enrollment_number ?? ''); ?></p>
                            </div>
                        </div>
                    </td>

                    
                    <td class="px-6 py-4">
                        <p class="text-xs font-bold text-slate-700 leading-none mb-1.5"><?php echo e($student->batch?->course?->name ?? '—'); ?></p>
                        <p class="text-[10px] font-semibold text-slate-400"><?php echo e($student->batch?->name ?? ''); ?> · Sem <?php echo e($student->current_unit); ?></p>
                    </td>

                    
                    <td class="px-6 py-4 text-center">
                        <div class="flex flex-col items-center gap-1.5">
                            <span class="text-xs font-black text-slate-700" id="row-pct-<?php echo e($student->id); ?>">
                                <?php echo e($student->att_pct); ?>%
                            </span>
                            <div class="w-20 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="<?php echo e($barColor); ?> h-1.5 rounded-full transition-all duration-500" id="row-bar-<?php echo e($student->id); ?>" style="width:<?php echo e(min(100, $student->att_pct)); ?>%"></div>
                            </div>
                            <span class="text-[9px] font-bold text-slate-400 tracking-wider uppercase"><?php echo e($student->att_present); ?>/<?php echo e($student->att_total); ?> classes</span>
                        </div>
                    </td>

                    
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-bold border <?php echo e($badgeMap[$riskLvl]); ?>"
                              id="row-badge-<?php echo e($student->id); ?>">
                            <?php echo e($labelMap[$riskLvl]); ?>

                        </span>
                    </td>

                    
                    <td class="px-6 py-4 max-w-xs">
                        <p class="text-xs font-medium text-slate-600 leading-relaxed line-clamp-2"
                           id="row-remark-<?php echo e($student->id); ?>">
                            <?php echo e($pred?->ai_remark ?? 'Prediction profile pending.'); ?>

                        </p>
                    </td>

                    
                    <td class="px-6 py-4 text-center">
                        <button class="btn-hod-predict inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold
                                       bg-purple-50 hover:bg-purple-600 border border-purple-200 text-purple-600 hover:text-white
                                       transition-all"
                                data-student-id="<?php echo e($student->id); ?>"
                                data-predict-url="<?php echo e(route('hod.risk.predict', $student->id)); ?>">
                            <span class="material-symbols-outlined text-[14px]">auto_awesome</span>
                            <span class="btn-row-label">Analyse</span>
                        </button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ─── Individual Predict (HOD) ─────────────────────────────────────────────────
document.querySelectorAll('.btn-hod-predict').forEach(btn => {
    btn.addEventListener('click', function () {
        const sid   = this.dataset.studentId;
        const url   = this.dataset.predictUrl;
        const lbl   = this.querySelector('.btn-row-label');
        const icon  = this.querySelector('.material-symbols-outlined');

        icon.textContent  = 'hourglass_top';
        icon.classList.add('animate-spin');
        lbl.textContent   = 'Analysing…';
        this.disabled     = true;

        fetch(url, { method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken} })
        .then(r => r.json())
        .then(data => {
            if (data.success) updateRow(sid, data);
        })
        .finally(() => {
            icon.textContent = 'auto_awesome';
            icon.classList.remove('animate-spin');
            lbl.textContent  = 'Re-Analyse';
            this.disabled    = false;
        });
    });
});

// ─── Analyse Department (batch) ───────────────────────────────────────────────
document.getElementById('btn-predict-dept')?.addEventListener('click', function () {
    const progDiv  = document.getElementById('dept-progress');
    const fill     = document.getElementById('dept-fill');
    const txt      = document.getElementById('dept-progress-txt');
    const buttons  = document.querySelectorAll('.btn-hod-predict');
    const total    = buttons.length;
    let   done     = 0;

    if (total === 0) return;

    progDiv.classList.remove('hidden');
    this.disabled    = true;
    fill.style.width = '0%';
    txt.textContent  = `Processing student 0 of ${total}…`;

    const processNext = idx => {
        if (idx >= total) {
            fill.style.width = '100%';
            txt.textContent  = `✅ All ${total} student predictions updated!`;
            setTimeout(() => { progDiv.classList.add('hidden'); document.getElementById('btn-predict-dept').disabled = false; }, 3000);
            return;
        }
        const btn = buttons[idx];
        const sid = btn.dataset.studentId;
        const url = btn.dataset.predictUrl;

        fetch(url, { method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken} })
        .then(r => r.json())
        .then(data => { if (data.success) updateRow(sid, data); })
        .finally(() => {
            done++;
            fill.style.width = Math.round((done / total) * 100) + '%';
            txt.textContent  = `Processing student ${done} of ${total}…`;
            setTimeout(() => processNext(idx + 1), 150);
        });
    };

    processNext(0);
});

// ─── Update Table Row ─────────────────────────────────────────────────────────
function updateRow(sid, data) {
    const rl = data.risk_level;
    
    const badgeMap = { 
        high: 'bg-red-50 text-red-600 border-red-200/50', 
        medium: 'bg-yellow-50 text-yellow-600 border-yellow-200/50', 
        low: 'bg-emerald-50 text-emerald-600 border-emerald-200/50' 
    };
    
    const barMap   = { 
        high: 'bg-gradient-to-r from-red-500 to-rose-500', 
        medium: 'bg-gradient-to-r from-yellow-500 to-amber-500', 
        low: 'bg-gradient-to-r from-emerald-500 to-teal-500' 
    };

    const badge = document.getElementById('row-badge-' + sid);
    if (badge) { badge.className = `inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-bold border ${badgeMap[rl]}`; badge.textContent = data.risk_label; }

    const bar = document.getElementById('row-bar-' + sid);
    if (bar) { bar.className = `h-1.5 rounded-full ${barMap[rl]}`; bar.style.width = Math.min(100, data.att_pct) + '%'; }

    const pct = document.getElementById('row-pct-' + sid);
    if (pct) pct.textContent = data.att_pct + '%';

    const remark = document.getElementById('row-remark-' + sid);
    if (remark) remark.textContent = data.ai_remark;

    const avatar = document.getElementById('row-avatar-' + sid);
    if (avatar) {
        avatar.className = "w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black flex-shrink-0 " + 
            (rl === 'high' ? 'bg-red-50 text-red-500' : (rl === 'medium' ? 'bg-yellow-50 text-yellow-500' : 'bg-emerald-50 text-emerald-500'));
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/hod/risk/index.blade.php ENDPATH**/ ?>