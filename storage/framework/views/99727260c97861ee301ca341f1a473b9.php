<?php $__env->startSection('content'); ?>


<div class="relative bg-gradient-to-r from-brand-accent to-sky-600 rounded-2xl p-6 sm:p-8 mb-8 overflow-hidden shadow-accent">
    <div class="absolute inset-0 opacity-10">
        <svg class="absolute right-0 bottom-0 h-full" viewBox="0 0 200 200" fill="white">
            <circle cx="160" cy="160" r="80"/><circle cx="40" cy="40" r="50"/>
        </svg>
    </div>
    <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-white/70 text-sm font-medium mb-1">Accounts Overview</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Fee Collection Dashboard</h1>
        </div>
        <div class="text-right hidden sm:block">
            <p class="text-white/60 text-xs font-semibold uppercase tracking-widest mb-0.5">Recovery Rate</p>
            <?php $recoveryRate = $expectedFees > 0 ? round(($collectedFees / $expectedFees) * 100, 1) : 0; ?>
            <p class="text-3xl font-extrabold text-white"><?php echo e($recoveryRate); ?>%</p>
        </div>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <?php $__currentLoopData = [
        ['label' => 'Total Collection',  'value' => '₹' . number_format($collectedFees, 0), 'icon' => 'payments',        'color' => 'text-status-success', 'bg' => 'bg-status-successs'],
        ['label' => 'Expected Revenue',  'value' => '₹' . number_format($expectedFees, 0),  'icon' => 'account_balance', 'color' => 'text-brand-accent',   'bg' => 'bg-brand-acents'],
        ['label' => 'Outstanding Dues',  'value' => '₹' . number_format($pendingFees, 0),   'icon' => 'pending_actions',  'color' => 'text-status-warning', 'bg' => 'bg-status-warnings'],
        ['label' => 'Total Fee Payers',  'value' => $totalStudents . ' Students',             'icon' => 'group',            'color' => 'text-status-info',    'bg' => 'bg-status-infos'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="kpi-card">
        <div class="kpi-icon <?php echo e($kpi['bg']); ?>">
            <span class="material-symbols-outlined <?php echo e($kpi['color']); ?> text-[22px]"><?php echo e($kpi['icon']); ?></span>
        </div>
        <div>
            <div class="kpi-value text-lg sm:text-2xl"><?php echo e($kpi['value']); ?></div>
            <div class="kpi-label"><?php echo e($kpi['label']); ?></div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    
    <div class="lg:col-span-2 card p-0 overflow-hidden">
        <div class="px-6 py-5 border-b border-brand-border flex justify-between items-center">
            <div>
                <h3 class="section-title">Recent Transactions</h3>
                <p class="section-sub">Live payment feed</p>
            </div>
            <span class="badge badge-accent">Last 10</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-brand-border">
                        <th class="table-head">Student</th>
                        <th class="table-head">Installment</th>
                        <th class="table-head">Amount</th>
                        <th class="table-head">Method</th>
                        <th class="table-head">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    <?php $__empty_1 = true; $__currentLoopData = $recentPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="table-row-hover">
                        <td class="table-cell">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-brand-acents flex items-center justify-center text-brand-accent text-xs font-bold flex-shrink-0">
                                    <?php echo e(strtoupper(substr(optional(optional(optional($payment->installment)->studentUnitFee)->student->user)->name ?? '?', 0, 1))); ?>

                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-brand-text"><?php echo e(optional(optional(optional($payment->installment)->studentUnitFee)->student->user)->name ?? 'N/A'); ?></p>
                                    <p class="text-xs text-brand-sub"><?php echo e(optional(optional(optional($payment->installment)->studentUnitFee)->student)->admission_number ?? ''); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="table-cell text-sm font-medium text-brand-sub">
                            <?php echo e(optional($payment->installment)->installment_name ?? 'Installment'); ?>

                        </td>
                        <td class="table-cell">
                            <span class="text-sm font-bold text-brand-text">₹<?php echo e(number_format($payment->amount_paid, 2)); ?></span>
                        </td>
                        <td class="table-cell text-xs font-medium text-brand-sub">
                            <?php echo e($payment->payment_method); ?>

                        </td>
                        <td class="table-cell">
                            <span class="badge badge-success">Success</span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-14 h-14 rounded-2xl bg-brand-muted flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-brand-sub text-2xl">receipt_long</span>
                                </div>
                                <p class="text-sm text-brand-sub font-medium">No payments recorded yet.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="card flex flex-col items-center">
        <h3 class="section-title mb-0.5 self-start">Collection Progress</h3>
        <p class="section-sub mb-6 self-start">Target vs Achievement</p>

        <div class="relative w-44 h-44 mb-6">
            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="40" fill="transparent" stroke="#E4E9F0" stroke-width="7"></circle>
                <circle cx="50" cy="50" r="40" fill="transparent" stroke="#0EA5E9" stroke-width="7"
                        stroke-dasharray="<?php echo e($recoveryRate * 2.51); ?> 251"
                        stroke-linecap="round"
                        class="transition-all duration-1000 ease-out"></circle>
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-3xl font-extrabold text-brand-text"><?php echo e($recoveryRate); ?>%</span>
                <span class="text-xs font-semibold text-brand-sub">Collected</span>
            </div>
        </div>

        <div class="w-full space-y-3">
            <div class="flex justify-between items-center p-3 rounded-xl bg-brand-muted">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-status-success"></div>
                    <span class="text-sm font-medium text-brand-sub">Collected</span>
                </div>
                <span class="text-sm font-bold text-status-success">₹<?php echo e(number_format($collectedFees, 0)); ?></span>
            </div>
            <div class="flex justify-between items-center p-3 rounded-xl bg-brand-muted">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-status-warning"></div>
                    <span class="text-sm font-medium text-brand-sub">Remaining</span>
                </div>
                <span class="text-sm font-bold text-status-warning">₹<?php echo e(number_format($pendingFees, 0)); ?></span>
            </div>
            <div class="flex justify-between items-center p-3 rounded-xl bg-brand-muted">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-brand-accent"></div>
                    <span class="text-sm font-medium text-brand-sub">Expected</span>
                </div>
                <span class="text-sm font-bold text-brand-text">₹<?php echo e(number_format($expectedFees, 0)); ?></span>
            </div>
        </div>
    </div>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/accounts/dashboard.blade.php ENDPATH**/ ?>