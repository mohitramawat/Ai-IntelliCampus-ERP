<?php $__env->startSection('content'); ?>


<div class="relative rounded-2xl p-6 mb-6 overflow-hidden shadow-[0_4px_14px_rgba(139,92,246,0.35)]" style="background: linear-gradient(to right, #8B5CF6, #A855F7);">
    <div class="absolute right-0 top-0 h-full opacity-10">
        <svg viewBox="0 0 200 200" fill="white" class="h-full"><circle cx="160" cy="40" r="90"/></svg>
    </div>
    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <p class="text-white/80 text-sm font-medium mb-1">Welcome, HOD</p>
            <h2 class="text-2xl font-black text-white"><?php echo e(auth()->user()->name); ?> 🏛️</h2>
            <p class="text-white/70 text-sm mt-1"><?php echo e(now()->format('l, d F Y')); ?> · <?php echo e($dept?->name ?? 'Department'); ?> Control</p>
        </div>
        <?php if($dept): ?>
        <div style="background:rgba(255,255,255,0.15);backdrop-filter:blur(4px);border:1px solid rgba(255,255,255,0.25);border-radius:12px;padding:10px 18px;text-align:center">
            <p style="font-size:11px;color:rgba(255,255,255,.7);font-weight:600;margin:0;text-transform:uppercase;letter-spacing:1px">Department</p>
            <p style="font-size:16px;color:#fff;font-weight:800;margin:2px 0 0"><?php echo e($dept->code ?? $dept->name); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php
        $kpis = [
            ['label'=>'Dept. Students', 'value'=>$studentCount, 'icon'=>'group',              'color'=>'text-brand-accent',   'bg'=>'bg-brand-acents'],
            ['label'=>'Dept. Teachers', 'value'=>$teacherCount, 'icon'=>'supervisor_account', 'color'=>'text-status-success', 'bg'=>'bg-status-successs'],
            ['label'=>'Active Batches', 'value'=>$batchCount,   'icon'=>'layers',             'color'=>'text-status-warning', 'bg'=>'bg-status-warnings'],
            ['label'=>'Dept. Courses',  'value'=>$dept ? $courseCount : 0, 'icon'=>'school',  'color'=>'text-status-info',    'bg'=>'bg-status-infos'],
        ];
    ?>

    <?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="kpi-card">
        <div class="kpi-icon <?php echo e($kpi['bg']); ?>">
            <span class="material-symbols-outlined <?php echo e($kpi['color']); ?> text-[22px]"><?php echo e($kpi['icon']); ?></span>
        </div>
        <div>
            <div class="kpi-value"><?php echo e(number_format($kpi['value'])); ?></div>
            <div class="kpi-label"><?php echo e($kpi['label']); ?></div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="section-title" style="display:flex;align-items:center;gap:6px">
                <span class="material-symbols-outlined text-status-success text-[18px]">supervisor_account</span>
                Department Teachers
            </h3>
            <a href="<?php echo e(route('hod.teachers.index')); ?>" class="text-xs font-semibold text-brand-accent hover:underline">View All</a>
        </div>
        
        <?php if($teachers->isEmpty()): ?>
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <span class="material-symbols-outlined text-[38px] text-brand-border mb-2">person_off</span>
                <p class="text-sm font-semibold text-brand-sub">No teachers found</p>
            </div>
        <?php else: ?>
            <div class="space-y-2">
                <?php $__currentLoopData = $teachers->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-brand-muted transition-colors">
                    <div class="w-8 h-8 rounded-full bg-status-successs flex items-center justify-center flex-shrink-0">
                        <span class="text-xs font-bold text-status-success"><?php echo e(strtoupper(substr($t->user?->name ?? 'T', 0, 1))); ?></span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-brand-text truncate"><?php echo e($t->user?->name ?? '—'); ?></p>
                        <p class="text-[11px] text-brand-sub truncate"><?php echo e($t->user?->email ?? ''); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($teachers->count() > 5): ?>
                <p class="text-xs text-brand-sub text-center pt-1">+ <?php echo e($teachers->count() - 5); ?> more teachers</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="section-title" style="display:flex;align-items:center;gap:6px">
                <span class="material-symbols-outlined text-brand-accent text-[18px]">group</span>
                Recent Students
            </h3>
            <a href="<?php echo e(route('hod.students.index')); ?>" class="text-xs font-semibold text-brand-accent hover:underline">View All</a>
        </div>
        
        <?php if($recentStudents->isEmpty()): ?>
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <span class="material-symbols-outlined text-[38px] text-brand-border mb-2">person_off</span>
                <p class="text-sm font-semibold text-brand-sub">No students found</p>
                <p class="text-xs text-brand-sub/60 mt-1">No students enrolled in department courses</p>
            </div>
        <?php else: ?>
            <div class="space-y-2">
                <?php $__currentLoopData = $recentStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-brand-muted transition-colors">
                    <div class="w-8 h-8 rounded-full bg-brand-acents flex items-center justify-center flex-shrink-0">
                        <span class="text-xs font-bold text-brand-accent"><?php echo e(strtoupper(substr($s->user?->name ?? 'S', 0, 1))); ?></span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-brand-text truncate"><?php echo e($s->user?->name ?? '—'); ?></p>
                        <p class="text-[11px] text-brand-sub truncate">
                            <?php echo e($s->batch?->course?->code ?? ''); ?> · <?php echo e($s->batch?->name ?? ''); ?>

                        </p>
                    </div>
                    <span class="text-[10px] text-brand-sub flex-shrink-0 ml-auto"><?php echo e($s->created_at->diffForHumans()); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>


<div class="card mt-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="section-title" style="display:flex;align-items:center;gap:6px">
            <span class="material-symbols-outlined text-status-warning text-[18px]">policy</span>
            Attendance Audit Log (Manual Overrides)
        </h3>
    </div>
    
    <?php if(isset($recentOverrides) && $recentOverrides->isEmpty()): ?>
        <div class="flex flex-col items-center justify-center py-6 text-center">
            <span class="material-symbols-outlined text-[38px] text-brand-border mb-2">verified_user</span>
            <p class="text-sm font-semibold text-brand-sub">No manual overrides recorded.</p>
            <p class="text-xs text-brand-sub/60 mt-1">All attendances are 100% verified by AI.</p>
        </div>
    <?php elseif(isset($recentOverrides)): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-brand-border">
                        <th class="py-2 px-3 text-xs font-semibold text-brand-sub uppercase tracking-wide">Student</th>
                        <th class="py-2 px-3 text-xs font-semibold text-brand-sub uppercase tracking-wide">Subject</th>
                        <th class="py-2 px-3 text-xs font-semibold text-brand-sub uppercase tracking-wide">Teacher</th>
                        <th class="py-2 px-3 text-xs font-semibold text-brand-sub uppercase tracking-wide">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border/50">
                    <?php $__currentLoopData = $recentOverrides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-brand-muted/50 transition-colors">
                        <td class="py-2.5 px-3">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-status-warnings flex items-center justify-center flex-shrink-0">
                                    <span class="text-[10px] font-bold text-status-warning"><?php echo e(strtoupper(substr($record->student->user->name ?? 'S', 0, 1))); ?></span>
                                </div>
                                <span class="text-sm font-semibold text-brand-text"><?php echo e($record->student->user->name ?? '—'); ?></span>
                            </div>
                        </td>
                        <td class="py-2.5 px-3 text-xs font-medium text-brand-sub"><?php echo e($record->lectureSession->subject->name ?? '—'); ?></td>
                        <td class="py-2.5 px-3 text-xs text-brand-sub"><?php echo e($record->lectureSession->teacher->user->name ?? '—'); ?></td>
                        <td class="py-2.5 px-3 text-xs text-brand-sub"><?php echo e(\Carbon\Carbon::parse($record->marked_at)->format('d M h:i A')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/hod/dashboard.blade.php ENDPATH**/ ?>