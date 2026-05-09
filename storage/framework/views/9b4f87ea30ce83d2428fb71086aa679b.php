<?php $__env->startSection('content'); ?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-brand-text tracking-tight">Fee Transactions</h1>
        <p class="text-brand-sub text-sm">Detailed log of all student payments</p>
    </div>
</div>

<div class="card mb-8">
    <form action="<?php echo e(route('accounts.fees.transactions')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="label">Search Student</label>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="input" placeholder="Name or Roll No...">
        </div>
        <div>
            <label class="label">Payment Mode</label>
            <select name="payment_method" class="input">
                <option value="">All Methods</option>
                <option value="cash" <?php echo e(request('payment_method') == 'cash' ? 'selected' : ''); ?>>Cash</option>
                <option value="online" <?php echo e(request('payment_method') == 'online' ? 'selected' : ''); ?>>Online</option>
                <option value="upi" <?php echo e(request('payment_method') == 'upi' ? 'selected' : ''); ?>>UPI</option>
            </select>
        </div>
        <div>
            <label class="label">From Date</label>
            <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="input">
        </div>
        <div class="flex gap-2">
            <div class="flex-1">
                <label class="label">To Date</label>
                <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="input">
            </div>
            <button type="submit" class="btn-primary p-3 h-[42px] mt-auto">
                <span class="material-symbols-outlined">search</span>
            </button>
            <?php if(request()->anyFilled(['search', 'payment_method', 'date_from', 'date_to'])): ?>
                <a href="<?php echo e(route('accounts.fees.transactions')); ?>" class="btn-secondary p-3 h-[42px] mt-auto">
                    <span class="material-symbols-outlined">close</span>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="card p-0 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-brand-border">
                    <th class="table-head">ID</th>
                    <th class="table-head">Student</th>
                    <th class="table-head">Installment</th>
                    <th class="table-head">Amount</th>
                    <th class="table-head">Mode</th>
                    <th class="table-head">Date</th>
                    <th class="table-head">Reference</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="table-row-hover">
                    <td class="table-cell text-xs font-bold text-brand-sub">#<?php echo e($tx->id); ?></td>
                    <td class="table-cell">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-brand-acents flex items-center justify-center text-brand-accent text-xs font-bold">
                                <?php echo e(strtoupper(substr(optional(optional(optional($tx->installment)->studentUnitFee)->student->user)->name ?? '?', 0, 1))); ?>

                            </div>
                            <div>
                                <p class="text-sm font-semibold text-brand-text"><?php echo e(optional(optional(optional($tx->installment)->studentUnitFee)->student->user)->name ?? 'N/A'); ?></p>
                                <p class="text-xs text-brand-sub"><?php echo e(optional(optional(optional($tx->installment)->studentUnitFee)->student)->roll_number ?? ''); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="table-cell text-sm text-brand-sub">
                        <?php echo e(optional($tx->installment)->installment_name ?? 'Installment'); ?>

                    </td>
                    <td class="table-cell font-bold text-brand-text">₹<?php echo e(number_format($tx->amount_paid, 2)); ?></td>
                    <td class="table-cell">
                        <span class="badge <?php echo e($tx->payment_method == 'cash' ? 'badge-warning' : 'badge-accent'); ?>">
                            <?php echo e(strtoupper($tx->payment_method)); ?>

                        </span>
                    </td>
                    <td class="table-cell text-sm text-brand-sub">
                        <?php echo e(\Carbon\Carbon::parse($tx->payment_date)->format('d M Y')); ?>

                    </td>
                    <td class="table-cell text-xs font-mono text-brand-sub">
                        <?php echo e($tx->transaction_reference ?: '--'); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="px-6 py-20 text-center text-brand-sub">No transactions found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($transactions->hasPages()): ?>
    <div class="px-6 py-4 bg-brand-muted/30 border-t border-brand-border">
        <?php echo e($transactions->links()); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/accounts/fees/transactions.blade.php ENDPATH**/ ?>