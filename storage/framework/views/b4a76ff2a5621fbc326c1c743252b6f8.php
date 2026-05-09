<?php $__env->startSection('content'); ?>

<div class="mb-8">
    <h1 class="text-2xl font-extrabold text-brand-text tracking-tight">Fee Structures</h1>
    <p class="text-brand-sub text-sm">Official academic fee configurations (Read-only)</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php $__empty_1 = true; $__currentLoopData = $structures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $structure): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card hover:shadow-xl transition-shadow border-t-4 border-brand-accent">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-lg font-bold text-brand-text"><?php echo e(optional($structure->course)->name); ?></h3>
                <p class="text-xs text-brand-sub uppercase tracking-widest font-bold">Category: <?php echo e($structure->category ?? 'General'); ?></p>
            </div>
            <span class="material-symbols-outlined text-brand-accent">account_balance_wallet</span>
        </div>

        <div class="space-y-3 pt-4 border-t border-brand-border">
            <div class="flex justify-between text-sm">
                <span class="text-brand-sub">Effective Year</span>
                <span class="text-brand-text font-bold"><?php echo e($structure->effective_from_year); ?></span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-brand-sub">Currency</span>
                <span class="text-brand-text font-bold"><?php echo e($structure->currency); ?></span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-brand-sub">Status</span>
                <span class="badge <?php echo e($structure->is_active ? 'badge-success' : 'badge-danger'); ?>"><?php echo e($structure->is_active ? 'Active' : 'Inactive'); ?></span>
            </div>
            <div class="flex justify-between text-sm pt-2 border-t border-brand-border">
                <span class="text-brand-text font-bold uppercase text-xs">Total Yearly Fee</span>
                <span class="text-brand-accent font-extrabold text-lg">₹<?php echo e(number_format($structure->total_fee, 0)); ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-span-full card py-20 text-center">
        <p class="text-brand-sub">No fee structures defined yet.</p>
    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views\accounts\fees\structures.blade.php ENDPATH**/ ?>