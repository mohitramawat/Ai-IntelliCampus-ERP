<?php $__env->startSection('content'); ?>


<div class="relative bg-gradient-to-r from-emerald-500 to-teal-400 rounded-2xl p-6 mb-6 overflow-hidden shadow-[0_4px_14px_rgba(16,185,129,0.35)]">
    <div class="absolute right-0 top-0 h-full opacity-10">
        <svg viewBox="0 0 200 200" fill="white" class="h-full"><circle cx="160" cy="40" r="90"/></svg>
    </div>
    <div class="relative z-10">
        <p class="text-white/80 text-sm font-medium mb-1">Welcome,</p>
        <h2 class="text-2xl font-black text-white"><?php echo e(auth()->user()->name); ?> 👨‍🏫</h2>
        <p class="text-white/70 text-sm mt-1"><?php echo e(now()->format('l, d F Y')); ?> · Teacher Portal</p>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php $__currentLoopData = [
        ['label'=>'My Classes Today', 'value'=>$classesToday,   'icon'=>'class',       'color'=>'text-brand-accent',   'bg'=>'bg-brand-acents'],
        ['label'=>'Sessions This Week','value'=>$classesThisWeek,'icon'=>'schedule',    'color'=>'text-status-success', 'bg'=>'bg-status-successs'],
        ['label'=>'Total Classes Taught','value'=>$totalClasses, 'icon'=>'history_edu', 'color'=>'text-status-warning', 'bg'=>'bg-status-warnings'],
        ['label'=>'Subjects Assigned','value'=>count($subjects), 'icon'=>'book',        'color'=>'text-status-info',    'bg'=>'bg-status-infos'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="kpi-card">
        <div class="kpi-icon <?php echo e($kpi['bg']); ?>">
            <span class="material-symbols-outlined <?php echo e($kpi['color']); ?> text-[22px]"><?php echo e($kpi['icon']); ?></span>
        </div>
        <div>
            <div class="kpi-value"><?php echo e($kpi['value']); ?></div>
            <div class="kpi-label"><?php echo e($kpi['label']); ?></div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    
    <div class="card">
        <h3 class="section-title mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <?php $__currentLoopData = [
                ['label'=>'Start Attendance Session','icon'=>'play_circle',   'color'=>'text-status-success', 'url'=>route('teacher.attendance.index')],
                ['label'=>'View My Sessions',        'icon'=>'history',       'color'=>'text-brand-accent', 'url'=>'#'],
                ['label'=>'My Schedule',             'icon'=>'calendar_month','color'=>'text-status-warning', 'url'=>'#'],
                ['label'=>'My Profile',              'icon'=>'manage_accounts','color'=>'text-status-info', 'url'=>'#'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($a['url']); ?>" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-brand-muted transition-colors group text-left">
                <div class="w-9 h-9 rounded-xl bg-brand-muted flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined <?php echo e($a['color']); ?> text-[20px]"><?php echo e($a['icon']); ?></span>
                </div>
                <span class="text-sm font-semibold text-brand-text"><?php echo e($a['label']); ?></span>
                <span class="material-symbols-outlined text-brand-sub text-[16px] ml-auto">chevron_right</span>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="lg:col-span-2 space-y-4">
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="section-title">My Recent Classes</h3>
                <span class="badge badge-accent">History</span>
            </div>

            <?php if($todaySessions->isEmpty() && $yesterdaySessions->isEmpty()): ?>
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <span class="material-symbols-outlined text-[40px] text-brand-border mb-2">event_busy</span>
                    <p class="text-sm font-medium text-brand-sub">No recent classes found</p>
                    <p class="text-xs text-brand-sub/60 mt-1">Your conducted attendance sessions will appear here.</p>
                </div>
            <?php else: ?>
                <div class="space-y-6">
                    
                    <?php if($todaySessions->isNotEmpty()): ?>
                    <div>
                        <h4 class="text-xs font-bold text-brand-sub uppercase tracking-wider mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-brand-accent"></span> Today
                        </h4>
                        <div class="space-y-3">
                            <?php $__currentLoopData = $todaySessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-4 rounded-xl border border-brand-border bg-brand-surface hover:bg-brand-muted transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <h5 class="text-sm font-bold text-brand-text"><?php echo e($session->subject->name); ?></h5>
                                    <span class="text-xs font-semibold text-brand-sub"><?php echo e(\Carbon\Carbon::parse($session->start_time)->format('h:i A')); ?></span>
                                </div>
                                <div class="flex flex-wrap gap-2 text-xs text-brand-sub mt-2">
                                    <span class="flex items-center gap-1 bg-brand-acents text-brand-accent px-2 py-1 rounded-md font-medium">
                                        <span class="material-symbols-outlined text-[14px]">school</span>
                                        <?php echo e($session->subject->course->name ?? 'N/A'); ?>

                                    </span>
                                    <span class="flex items-center gap-1 bg-brand-muted px-2 py-1 rounded-md">
                                        <span class="material-symbols-outlined text-[14px]">layers</span>
                                        Sem <?php echo e($session->subject->semester); ?>

                                    </span>
                                    <span class="flex items-center gap-1 bg-brand-muted px-2 py-1 rounded-md">
                                        <span class="material-symbols-outlined text-[14px]">group</span>
                                        <?php echo e($session->batch->name ?? 'N/A'); ?>

                                    </span>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    
                    <?php if($yesterdaySessions->isNotEmpty()): ?>
                    <div>
                        <h4 class="text-xs font-bold text-brand-sub uppercase tracking-wider mb-3 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-status-info"></span> Yesterday
                        </h4>
                        <div class="space-y-3">
                            <?php $__currentLoopData = $yesterdaySessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-4 rounded-xl border border-brand-border bg-brand-surface opacity-80 hover:opacity-100 transition-opacity">
                                <div class="flex justify-between items-start mb-2">
                                    <h5 class="text-sm font-bold text-brand-text"><?php echo e($session->subject->name); ?></h5>
                                    <span class="text-xs font-semibold text-brand-sub"><?php echo e(\Carbon\Carbon::parse($session->start_time)->format('h:i A')); ?></span>
                                </div>
                                <div class="flex flex-wrap gap-2 text-xs text-brand-sub mt-2">
                                    <span class="flex items-center gap-1 bg-brand-infos text-status-info px-2 py-1 rounded-md font-medium">
                                        <span class="material-symbols-outlined text-[14px]">school</span>
                                        <?php echo e($session->subject->course->name ?? 'N/A'); ?>

                                    </span>
                                    <span class="flex items-center gap-1 bg-brand-muted px-2 py-1 rounded-md">
                                        <span class="material-symbols-outlined text-[14px]">layers</span>
                                        Sem <?php echo e($session->subject->semester); ?>

                                    </span>
                                    <span class="flex items-center gap-1 bg-brand-muted px-2 py-1 rounded-md">
                                        <span class="material-symbols-outlined text-[14px]">group</span>
                                        <?php echo e($session->batch->name ?? 'N/A'); ?>

                                    </span>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-flat flex items-center gap-4 bg-brand-acents border-brand-accent/20">
            <span class="material-symbols-outlined text-brand-accent text-[28px]">info</span>
            <div>
                <p class="text-sm font-semibold text-brand-text">HOD Note</p>
                <p class="text-xs text-brand-sub mt-0.5">Teachers with HOD role manage department data via the HOD panel.</p>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/teacher/dashboard.blade.php ENDPATH**/ ?>