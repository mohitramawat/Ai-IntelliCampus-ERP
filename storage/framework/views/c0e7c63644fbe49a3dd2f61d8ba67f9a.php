<?php $__env->startSection('content'); ?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-brand-text tracking-tight flex items-center gap-2">
            Fee Defaulters
            <span class="badge badge-danger">High Priority</span>
        </h1>
        <p class="text-brand-sub text-sm">Students with overdue installments as of <?php echo e(now()->format('d M Y')); ?></p>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="px-6 py-4 bg-status-danger/5 border-b border-brand-border flex items-center gap-3">
        <span class="material-symbols-outlined text-status-danger">warning</span>
        <p class="text-xs font-bold text-status-danger uppercase tracking-widest">Total Overdue Installments: <?php echo e($defaulters->total()); ?></p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-brand-border">
                    <th class="table-head">Student</th>
                    <th class="table-head">Batch</th>
                    <th class="table-head">Installment</th>
                    <th class="table-head">Due Date</th>
                    <th class="table-head">Days Overdue</th>
                    <th class="table-head">Pending Amount</th>
                    <th class="table-head">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                <?php $__empty_1 = true; $__currentLoopData = $defaulters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php 
                    $overdueDays = \Carbon\Carbon::parse($installment->due_date)->diffInDays(now());
                    $pending = $installment->installment_amount - $installment->paid_amount;
                ?>
                <tr class="table-row-hover">
                    <td class="table-cell">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-status-danger/10 flex items-center justify-center text-status-danger text-xs font-bold">
                                <?php echo e(strtoupper(substr(optional($installment->studentUnitFee->student->user)->name ?? '?', 0, 1))); ?>

                            </div>
                            <div>
                                <p class="text-sm font-semibold text-brand-text"><?php echo e(optional($installment->studentUnitFee->student->user)->name ?? 'N/A'); ?></p>
                                <p class="text-xs text-brand-sub"><?php echo e($installment->studentUnitFee->student->roll_number); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="table-cell text-xs font-bold text-brand-sub">
                        <?php echo e(optional($installment->studentUnitFee->student->batch)->name); ?>

                    </td>
                    <td class="table-cell text-sm text-brand-text">
                        <?php echo e($installment->installment_name ?? 'Installment #' . $installment->installment_number); ?>

                    </td>
                    <td class="table-cell text-sm font-semibold text-brand-sub">
                        <?php echo e(\Carbon\Carbon::parse($installment->due_date)->format('d M Y')); ?>

                    </td>
                    <td class="table-cell">
                        <span class="px-2 py-1 rounded text-[10px] font-bold <?php echo e($overdueDays > 30 ? 'bg-status-danger text-white' : 'bg-status-warning/20 text-status-warning'); ?>">
                            <?php echo e($overdueDays); ?> DAYS
                        </span>
                    </td>
                    <td class="table-cell font-extrabold text-status-danger">₹<?php echo e(number_format($pending, 2)); ?></td>
                    <td class="table-cell">
                        <span class="badge badge-danger">OVERDUE</span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="px-6 py-20 text-center text-brand-sub">Great! No defaulters found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($defaulters->hasPages()): ?>
    <div class="px-6 py-4 bg-brand-muted/30 border-t border-brand-border">
        <?php echo e($defaulters->links()); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views\accounts\fees\defaulters.blade.php ENDPATH**/ ?>