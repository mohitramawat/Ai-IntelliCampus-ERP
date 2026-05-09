<?php $__env->startSection('content'); ?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-brand-text tracking-tight">Revenue Reports</h1>
        <p class="text-brand-sub text-sm">Collection analytics and trends</p>
    </div>
    <div class="flex bg-brand-muted p-1 rounded-xl">
        <a href="<?php echo e(route('accounts.fees.reports', ['type' => 'daily'])); ?>" class="px-4 py-2 rounded-lg text-xs font-bold transition-all <?php echo e($type == 'daily' ? 'bg-white shadow-sm text-brand-accent' : 'text-brand-sub'); ?>">Daily</a>
        <a href="<?php echo e(route('accounts.fees.reports', ['type' => 'weekly'])); ?>" class="px-4 py-2 rounded-lg text-xs font-bold transition-all <?php echo e($type == 'weekly' ? 'bg-white shadow-sm text-brand-accent' : 'text-brand-sub'); ?>">Weekly</a>
        <a href="<?php echo e(route('accounts.fees.reports', ['type' => 'monthly'])); ?>" class="px-4 py-2 rounded-lg text-xs font-bold transition-all <?php echo e($type == 'monthly' ? 'bg-white shadow-sm text-brand-accent' : 'text-brand-sub'); ?>">Monthly</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card">
        <h3 class="section-title mb-6">Collection Trend (<?php echo e(ucfirst($type)); ?>)</h3>
        
        <?php if($data->isEmpty()): ?>
            <div class="flex flex-col items-center justify-center h-64 text-brand-sub">
                <span class="material-symbols-outlined text-4xl mb-2">bar_chart_off</span>
                <p class="text-sm">No payment data found for this period.</p>
            </div>
        <?php else: ?>
            <div class="flex items-end gap-2 h-64 mb-4 px-4 border-b border-brand-border">
                <?php $max = $data->max('total') ?: 1; ?>
                <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex-1 flex flex-col items-center group relative min-w-[30px]">
                    <div class="w-full bg-brand-accent/20 hover:bg-brand-accent rounded-t-lg transition-all duration-300 relative cursor-pointer" 
                         style="height: <?php echo e(max(($row->total / $max) * 100, 5)); ?>%">
                        <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-brand-text text-white text-[10px] py-1.5 px-2 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                            ₹<?php echo e(number_format($row->total, 0)); ?>

                        </div>
                    </div>
                    <div class="mt-2 text-[10px] font-bold text-brand-sub truncate w-full text-center">
                        <?php echo e($row->label); ?>

                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3 class="section-title mb-6">Collection Summary</h3>
        <div class="space-y-4">
            <div class="p-4 rounded-2xl bg-brand-muted">
                <p class="text-[10px] font-bold text-brand-sub uppercase tracking-wider mb-1">Total Period Collection</p>
                <p class="text-2xl font-extrabold text-brand-text">₹<?php echo e(number_format($data->sum('total'), 2)); ?></p>
            </div>
            
            <div class="space-y-3 pt-4">
                <h4 class="text-xs font-bold text-brand-text mb-2">Detailed Breakdown</h4>
                <?php $__currentLoopData = $data->sortByDesc('total')->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-brand-sub font-medium"><?php echo e($row->label); ?></span>
                    <span class="text-brand-text font-bold">₹<?php echo e(number_format($row->total, 0)); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/accounts/fees/reports.blade.php ENDPATH**/ ?>