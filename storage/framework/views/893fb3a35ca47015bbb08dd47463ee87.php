<?php $__env->startSection('content'); ?>

<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-brand-text tracking-tight">Student Fee Dues</h1>
        <p class="text-brand-sub text-sm">Overall balance status of students</p>
    </div>
</div>

<div class="card mb-8">
    <form action="<?php echo e(route('accounts.fees.dues')); ?>" method="GET" class="flex gap-4 items-end">
        <div class="flex-1 max-w-sm">
            <label class="label">Search Student</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-brand-sub text-xl">search</span>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="input pl-10" placeholder="Name, Roll No or Admission No...">
            </div>
        </div>
        <button type="submit" class="btn-primary h-[42px] px-6">Filter</button>
        <?php if(request()->filled('search')): ?>
            <a href="<?php echo e(route('accounts.fees.dues')); ?>" class="btn-secondary h-[42px] px-4 flex items-center">
                <span class="material-symbols-outlined">refresh</span>
            </a>
        <?php endif; ?>
    </form>
</div>

<div class="card p-0 overflow-hidden" x-data="{ selectedStudent: null }">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-brand-border">
                    <th class="table-head">Student</th>
                    <th class="table-head">Total Fee</th>
                    <th class="table-head">Total Paid</th>
                    <th class="table-head">Balance</th>
                    <th class="table-head">Progress</th>
                    <th class="table-head">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                <?php $__empty_1 = true; $__currentLoopData = $dues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $due): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php 
                    $balance = $due->unit_fee - $due->total_paid;
                    $percent = $due->unit_fee > 0 ? round(($due->total_paid / $due->unit_fee) * 100) : 0;
                    $studentInstallments = $due->installments;
                ?>
                <tr class="table-row-hover">
                    <td class="table-cell">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-brand-muted flex items-center justify-center text-brand-sub text-xs font-bold">
                                <?php echo e(strtoupper(substr(optional($due->student->user)->name ?? '?', 0, 1))); ?>

                            </div>
                            <div>
                                <p class="text-sm font-semibold text-brand-text"><?php echo e(optional($due->student->user)->name ?? 'N/A'); ?></p>
                                <p class="text-[10px] font-bold text-brand-accent uppercase"><?php echo e(optional($due->student->batch->course)->name); ?></p>
                                <p class="text-[10px] text-brand-sub"><?php echo e($due->student->roll_number); ?> (<?php echo e(optional($due->student->batch)->name); ?>)</p>
                            </div>
                        </div>
                    </td>
                    <td class="table-cell text-sm font-medium text-brand-sub">₹<?php echo e(number_format($due->unit_fee, 2)); ?></td>
                    <td class="table-cell text-sm font-bold text-status-success">₹<?php echo e(number_format($due->total_paid, 2)); ?></td>
                    <td class="table-cell text-sm font-bold text-status-danger">₹<?php echo e(number_format($balance, 2)); ?></td>
                    <td class="table-cell">
                        <div class="w-24">
                            <div class="flex justify-between text-[10px] font-bold text-brand-sub mb-1">
                                <span><?php echo e($percent); ?>%</span>
                            </div>
                            <div class="h-1.5 w-full bg-brand-muted rounded-full overflow-hidden">
                                <div class="h-full bg-brand-accent rounded-full transition-all duration-500" style="width: <?php echo e($percent); ?>%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="table-cell">
                        <button @click="selectedStudent = <?php echo json_encode(['name' => $due->student->user->name, 'installments' => $due->installments], 512) ?>" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-brand-muted hover:bg-brand-acents hover:text-brand-accent text-xs font-bold transition-all">
                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                            Breakdown
                        </button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-6 py-20 text-center text-brand-sub">No student dues found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <div x-show="selectedStudent" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-brand-text/40 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in duration-300" @click.away="selectedStudent = null">
            <div class="px-6 py-5 border-b border-brand-border flex justify-between items-center bg-brand-muted/30">
                <div>
                    <h3 class="text-lg font-black text-brand-text" x-text="selectedStudent?.name"></h3>
                    <p class="text-xs text-brand-sub font-semibold">Semester-wise Installment Breakdown</p>
                </div>
                <button @click="selectedStudent = null" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-brand-border transition-colors">
                    <span class="material-symbols-outlined text-brand-sub">close</span>
                </button>
            </div>
            <div class="p-6 max-h-[60vh] overflow-y-auto">
                <div class="space-y-4">
                    <template x-for="(inst, index) in selectedStudent?.installments" :key="index">
                        <div class="p-4 rounded-2xl border border-brand-border bg-brand-surface">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="text-sm font-bold text-brand-text" x-text="`Installment ${inst.installment_number}`"></h4>
                                    <p class="text-[10px] font-bold text-brand-sub uppercase tracking-widest" x-text="`Due: ${inst.due_date}`"></p>
                                </div>
                                <span :class="inst.status === 'paid' ? 'badge-success' : 'badge-danger'" class="badge text-[10px]" x-text="inst.status.toUpperCase()"></span>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="bg-brand-muted p-2 rounded-xl">
                                    <p class="text-[9px] font-bold text-brand-sub uppercase mb-0.5">Amount</p>
                                    <p class="text-sm font-extrabold text-brand-text" x-text="`₹${parseFloat(inst.installment_amount).toLocaleString()}`"></p>
                                </div>
                                <div class="bg-brand-muted p-2 rounded-xl">
                                    <p class="text-[9px] font-bold text-brand-sub uppercase mb-0.5">Paid</p>
                                    <p class="text-sm font-extrabold text-status-success" x-text="`₹${parseFloat(inst.paid_amount).toLocaleString()}`"></p>
                                </div>
                                <div class="bg-brand-muted p-2 rounded-xl">
                                    <p class="text-[9px] font-bold text-brand-sub uppercase mb-0.5">Balance</p>
                                    <p class="text-sm font-extrabold text-status-danger" x-text="`₹${(inst.installment_amount - inst.paid_amount).toLocaleString()}`"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-brand-border bg-brand-muted/30 flex justify-end">
                <button @click="selectedStudent = null" class="btn-primary px-6">Close Details</button>
            </div>
        </div>
    </div>
</div>
    <?php if($dues->hasPages()): ?>
    <div class="px-6 py-4 bg-brand-muted/30 border-t border-brand-border">
        <?php echo e($dues->links()); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/accounts/fees/dues.blade.php ENDPATH**/ ?>