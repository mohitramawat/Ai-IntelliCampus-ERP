<?php $__env->startSection('content'); ?>


<div class="relative bg-gradient-to-r from-sky-500 to-brand-accent rounded-2xl p-6 mb-6 overflow-hidden shadow-accent">
    <div class="absolute inset-0 opacity-10">
        <svg class="absolute right-0 bottom-0 h-full" viewBox="0 0 200 200" fill="white">
            <circle cx="160" cy="160" r="80"/><circle cx="40" cy="40" r="50"/>
        </svg>
    </div>
    <div class="relative z-10">
        <p class="text-white/80 text-sm font-medium mb-1">Good <?php echo e(now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')); ?>,</p>
        <h2 class="text-2xl font-black text-white"><?php echo e(auth()->user()->name); ?> 🎓</h2>
        <p class="text-white/70 text-sm mt-1"><?php echo e(now()->format('l, d F Y')); ?> · Student Portal</p>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php $__currentLoopData = [
        ['label'=>'Current Semester', 'value'=> $student->current_unit ?: '1', 'icon'=>'menu_book',   'color'=>'text-brand-accent',   'bg'=>'bg-brand-acents'],
        ['label'=>'Attendance %',     'value'=> $attendancePercentage . '%','icon'=>'fact_check',  'color'=>'text-status-success', 'bg'=>'bg-status-successs'],
        ['label'=>'Pending Fees',     'value'=>'₹0','icon'=>'payments',    'color'=>'text-status-warning', 'bg'=>'bg-status-warnings'],
        ['label'=>'Documents',        'value'=> $documentCount, 'icon'=>'folder_open', 'color'=>'text-status-info',    'bg'=>'bg-status-infos'],
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

    
    <div class="card lg:col-span-1">
        <h3 class="section-title mb-1">Academic Progress</h3>
        <p class="section-sub mb-4">Your semester journey</p>
        <div class="space-y-4">
            <?php $currentUnit = $student->current_unit ?: 1; ?>
            <?php $__currentLoopData = [
                ['label'=>'Semester 1','val'=>1],
                ['label'=>'Semester 2','val'=>2],
                ['label'=>'Semester 3','val'=>3],
                ['label'=>'Semester 4','val'=>4],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold
                    <?php echo e($sem['val'] < $currentUnit ? 'bg-status-success text-white' : ($sem['val'] == $currentUnit ? 'bg-brand-accent text-white ring-4 ring-brand-acents' : 'bg-brand-muted text-brand-sub border border-brand-border')); ?>">
                    <?php if($sem['val'] < $currentUnit): ?>
                        <span class="material-symbols-outlined text-[16px]">check</span>
                    <?php elseif($sem['val'] == $currentUnit): ?>
                        <span class="material-symbols-outlined text-[16px]">radio_button_checked</span>
                    <?php else: ?>
                        <span class="material-symbols-outlined text-[14px]">radio_button_unchecked</span>
                    <?php endif; ?>
                </div>
                <span class="text-sm font-medium <?php echo e($sem['val'] < $currentUnit ? 'text-brand-sub line-through' : ($sem['val'] == $currentUnit ? 'text-brand-text font-semibold' : 'text-brand-sub')); ?>">
                    <?php echo e($sem['label']); ?>

                </span>
                <?php if($sem['val'] == $currentUnit): ?>
                    <span class="badge badge-accent ml-auto">Current</span>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="card lg:col-span-2">
        <h3 class="section-title mb-4">Quick Access</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <?php $__currentLoopData = [
                ['label'=>'Attendance',      'icon'=>'fact_check',    'color'=>'text-brand-accent',   'bg'=>'bg-brand-acents',    'route'=>'student.attendance.index'],
                ['label'=>'Profile & AI',     'icon'=>'account_circle','color'=>'text-status-success', 'bg'=>'bg-status-successs',  'route'=>'student.profile.index'],
                ['label'=>'My Documents',     'icon'=>'folder_open',   'color'=>'text-status-info',     'bg'=>'bg-status-infos',    'route'=>'student.documents.index'],
                ['label'=>'Fee Details',      'icon'=>'receipt_long',  'color'=>'text-status-warning',  'bg'=>'bg-status-warnings', 'route'=>null],
                ['label'=>'Timetable',        'icon'=>'calendar_month','color'=>'text-status-success',  'bg'=>'bg-status-successs', 'route'=>null],
                ['label'=>'Change Password',  'icon'=>'lock_reset',    'color'=>'text-brand-sub',       'bg'=>'bg-brand-muted',     'route'=>'student.password.change'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($link['route']): ?>
            <a href="<?php echo e(route($link['route'])); ?>"
               class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-brand-muted border border-brand-border hover:border-brand-accent/30 hover:shadow-card-md transition-all hover:-translate-y-0.5 group">
            <?php else: ?>
            <div class="flex flex-col items-center gap-2 p-4 rounded-2xl bg-brand-muted border border-brand-border opacity-60 cursor-not-allowed group">
            <?php endif; ?>
                <div class="w-11 h-11 rounded-xl <?php echo e($link['bg']); ?> flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined <?php echo e($link['color']); ?> text-[24px]"><?php echo e($link['icon']); ?></span>
                </div>
                <span class="text-xs font-semibold text-brand-text text-center leading-tight"><?php echo e($link['label']); ?></span>
            <?php if($link['route']): ?>
            </a>
            <?php else: ?>
            </div>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views\student\dashboard.blade.php ENDPATH**/ ?>