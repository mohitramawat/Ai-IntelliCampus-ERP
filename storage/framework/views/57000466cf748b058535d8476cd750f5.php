<?php $__env->startSection('content'); ?>


<div class="relative rounded-2xl p-6 mb-6 overflow-hidden shadow-[0_4px_14px_rgba(79,70,229,0.35)]" style="background: linear-gradient(to right, #2563EB, #6366F1);">
    <div class="absolute right-0 top-0 h-full opacity-10">
        <svg viewBox="0 0 200 200" fill="white" class="h-full"><circle cx="160" cy="40" r="90"/></svg>
    </div>
    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <p class="text-white/80 text-sm font-medium mb-1">Welcome back,</p>
            <h2 class="text-2xl font-black text-white"><?php echo e(auth()->user()->name); ?> ⚙️</h2>
            <p class="text-white/70 text-sm mt-1"><?php echo e(now()->format('l, d F Y')); ?> · Admin Control Panel</p>
        </div>
        <a href="<?php echo e(route('admin.students.index')); ?>"
           class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 backdrop-blur text-white font-semibold
                  px-4 py-2.5 rounded-xl text-sm transition-all border border-white/20 self-start sm:self-auto">
            <span class="material-symbols-outlined text-[18px]">group</span>
            All Students
        </a>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="kpi-card">
        <div class="kpi-icon bg-brand-acents">
            <span class="material-symbols-outlined text-brand-accent text-[22px]">school</span>
        </div>
        <div>
            <div class="kpi-value"><?php echo e(number_format($stats['total_students'])); ?></div>
            <div class="kpi-label">Total Students</div>
            <div class="text-[11px] text-status-success mt-0.5"><?php echo e($stats['active_students']); ?> active</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon bg-status-successs">
            <span class="material-symbols-outlined text-status-success text-[22px]">person</span>
        </div>
        <div>
            <div class="kpi-value"><?php echo e(number_format($stats['total_teachers'])); ?></div>
            <div class="kpi-label">Teachers</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon bg-status-warnings">
            <span class="material-symbols-outlined text-status-warning text-[22px]">business</span>
        </div>
        <div>
            <div class="kpi-value"><?php echo e($stats['departments']); ?></div>
            <div class="kpi-label">Departments</div>
            <div class="text-[11px] text-brand-sub mt-0.5"><?php echo e($stats['total_courses']); ?> courses</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon bg-status-dangers">
            <span class="material-symbols-outlined text-status-danger text-[22px]">folder_open</span>
        </div>
        <div>
            <div class="kpi-value"><?php echo e($docPendingCount); ?></div>
            <div class="kpi-label">Pending Docs</div>
            <div class="text-[11px] text-brand-sub mt-0.5">students incomplete</div>
        </div>
    </div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

    
    <div class="card lg:col-span-2">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="section-title">Financial Overview</h3>
                <p class="section-sub">Live fee collection data</p>
            </div>
            <div class="w-9 h-9 rounded-xl bg-status-warnings flex items-center justify-center">
                <span class="material-symbols-outlined text-status-warning text-[20px]">account_balance_wallet</span>
            </div>
        </div>

        
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-5">
            <?php
                $fKpis = [
                    ['label'=>'Total Billed',    'value'=>'₹'.number_format($financial['total_fee_billed'],2), 'color'=>'text-brand-accent',   'bg'=>'bg-brand-acents'],
                    ['label'=>'Collected',        'value'=>'₹'.number_format($financial['total_collected'],2),  'color'=>'text-status-success', 'bg'=>'bg-status-successs'],
                    ['label'=>'Outstanding',      'value'=>'₹'.number_format($financial['total_pending'],2),    'color'=>'text-status-danger',  'bg'=>'bg-status-dangers'],
                ];
            ?>
            <?php $__currentLoopData = $fKpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="p-3 rounded-xl <?php echo e($fk['bg']); ?> border border-brand-border/30">
                <p class="text-[11px] font-medium text-brand-sub mb-1"><?php echo e($fk['label']); ?></p>
                <p class="text-base font-black <?php echo e($fk['color']); ?>"><?php echo e($fk['value']); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <?php
            $pct = $financial['total_fee_billed'] > 0
                ? round(($financial['total_collected'] / $financial['total_fee_billed']) * 100, 1)
                : 0;
        ?>
        <div class="mb-4">
            <div class="flex justify-between text-xs font-medium text-brand-sub mb-1.5">
                <span>Collection Rate</span>
                <span class="font-bold text-brand-text"><?php echo e($pct); ?>%</span>
            </div>
            <div class="w-full h-2.5 rounded-full bg-brand-muted overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700"
                     style="width:<?php echo e($pct); ?>%; background: linear-gradient(to right, var(--brand-accent), #38BDF8);"></div>
            </div>
        </div>

        
        <div class="flex items-center justify-between p-3 rounded-xl bg-brand-muted border border-brand-border">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-status-warning text-[18px]">gavel</span>
                <div>
                    <p class="text-xs font-semibold text-brand-text">Total Fines Levied</p>
                    <p class="text-[11px] text-brand-sub">₹<?php echo e(number_format($financial['unpaid_fines'],2)); ?> unpaid</p>
                </div>
            </div>
            <span class="text-sm font-bold text-status-warning">₹<?php echo e(number_format($financial['total_fines'],2)); ?></span>
        </div>
    </div>

    
    <div class="card">
        <h3 class="section-title mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <?php $__currentLoopData = [
                ['label'=>'All Students',       'icon'=>'group',              'color'=>'text-brand-accent',   'bg'=>'bg-brand-acents',      'route'=>'admin.students.index'],
                ['label'=>'Pending Documents',  'icon'=>'folder_open',        'color'=>'text-status-warning', 'bg'=>'bg-status-warnings',   'route'=>'writer.students.pending-documents'],
                ['label'=>'Create Student',     'icon'=>'person_add',         'color'=>'text-status-success', 'bg'=>'bg-status-successs',   'route'=>'writer.students.create'],
                ['label'=>'Attendance Report',  'icon'=>'fact_check',         'color'=>'text-status-info',    'bg'=>'bg-status-infos',      'route'=>null],
                ['label'=>'Fee Analytics',      'icon'=>'account_balance',    'color'=>'text-status-danger',  'bg'=>'bg-status-dangers',    'route'=>null],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($action['route']): ?>
            <a href="<?php echo e(route($action['route'])); ?>"
               class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-brand-muted transition-colors group">
            <?php else: ?>
            <div class="w-full flex items-center gap-3 p-3 rounded-xl opacity-50 cursor-not-allowed">
            <?php endif; ?>
                <div class="w-9 h-9 rounded-xl <?php echo e($action['bg']); ?> flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined <?php echo e($action['color']); ?> text-[20px]"><?php echo e($action['icon']); ?></span>
                </div>
                <span class="text-sm font-semibold text-brand-text"><?php echo e($action['label']); ?></span>
                <?php if($action['route']): ?>
                <span class="material-symbols-outlined text-brand-sub text-[16px] ml-auto">chevron_right</span>
                <?php endif; ?>
            <?php if($action['route']): ?>
            </a>
            <?php else: ?>
            </div>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="section-title">Batch Enrolment</h3>
                <p class="section-sub">Active batches by student count</p>
            </div>
        </div>
        <?php if($batchBreakdown->isEmpty()): ?>
            <p class="text-sm text-brand-sub text-center py-6">No active batches found.</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php $maxStudents = $batchBreakdown->max('students_count') ?: 1; ?>
                <?php $__currentLoopData = $batchBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <div>
                            <span class="text-xs font-semibold text-brand-text"><?php echo e($batch->name); ?></span>
                            <span class="text-[10px] text-brand-sub ml-1">· <?php echo e($batch->course->name ?? ''); ?></span>
                        </div>
                        <span class="text-xs font-bold text-brand-accent"><?php echo e($batch->students_count); ?></span>
                    </div>
                    <div class="w-full h-1.5 rounded-full bg-brand-muted overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700"
                             style="width: <?php echo e(($batch->students_count / $maxStudents) * 100); ?>%; background: linear-gradient(to right, var(--brand-accent), #38BDF8);"></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="section-title">Recently Enrolled</h3>
                <p class="section-sub">Last 5 students added</p>
            </div>
            <a href="<?php echo e(route('admin.students.index')); ?>" class="text-xs font-semibold text-brand-accent hover:underline">
                View All
            </a>
        </div>
        <?php if($recentStudents->isEmpty()): ?>
            <p class="text-sm text-brand-sub text-center py-6">No students found.</p>
        <?php else: ?>
        <div class="space-y-3">
            <?php $__currentLoopData = $recentStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-brand-muted transition-colors">
                <div class="w-8 h-8 rounded-full bg-brand-acents flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-brand-accent">
                        <?php echo e(strtoupper(substr($s->user->name ?? 'S', 0, 1))); ?>

                    </span>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-brand-text truncate"><?php echo e($s->user->name ?? '—'); ?></p>
                    <p class="text-[11px] text-brand-sub truncate">
                        <?php echo e($s->batch->course->name ?? ''); ?> · <?php echo e($s->batch->name ?? ''); ?>

                    </p>
                </div>
                <span class="text-[10px] text-brand-sub flex-shrink-0 ml-auto">
                    <?php echo e($s->created_at->diffForHumans()); ?>

                </span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>