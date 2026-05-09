<?php $__env->startSection('content'); ?>


<div class="relative bg-gradient-to-r from-brand-accent to-sky-600 rounded-2xl p-6 sm:p-8 mb-8 overflow-hidden shadow-accent">
    <div class="absolute inset-0 opacity-10">
        <svg class="absolute right-0 bottom-0 h-full" viewBox="0 0 200 200" fill="white">
            <circle cx="160" cy="160" r="80"/><circle cx="40" cy="40" r="50"/>
        </svg>
    </div>
    <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-white/70 text-sm font-medium mb-1">Administration</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Staff Management</h1>
            <p class="text-white/60 text-sm mt-1">Manage all non-teaching staff — Accountants, Writers, HODs & Admins</p>
        </div>
        <a href="<?php echo e(route('admin.staff.create')); ?>" class="btn-primary bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/20 text-white shadow-none">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            Add Staff
        </a>
    </div>
</div>


<?php if(session('success')): ?>
<div class="bg-status-successs border border-status-success/20 text-status-success px-5 py-3 rounded-xl mb-6 flex items-center gap-2 text-sm font-semibold">
    <span class="material-symbols-outlined text-[18px]">check_circle</span>
    <?php echo e(session('success')); ?>

</div>
<?php endif; ?>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <?php
        $roleCountMap = $staffMembers->groupBy(fn($s) => optional($s->user)->roles->first()?->name ?? 'unknown');
    ?>
    <?php $__currentLoopData = [
        ['label' => 'Total Staff',   'value' => $staffMembers->count(),                'icon' => 'groups',            'color' => 'text-brand-accent',   'bg' => 'bg-brand-acents'],
        ['label' => 'Admins',        'value' => $roleCountMap->get('admin', collect())->count(),    'icon' => 'admin_panel_settings','color' => 'text-status-danger',  'bg' => 'bg-status-dangers'],
        ['label' => 'Accountants',   'value' => $roleCountMap->get('accounts', collect())->count(), 'icon' => 'account_balance',     'color' => 'text-status-info',    'bg' => 'bg-status-infos'],
        ['label' => 'Writers',       'value' => $roleCountMap->get('writer', collect())->count(),   'icon' => 'edit_note',           'color' => 'text-status-warning', 'bg' => 'bg-status-warnings'],
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


<div class="card p-0 overflow-hidden">
    <div class="px-6 py-5 border-b border-brand-border flex justify-between items-center">
        <div>
            <h3 class="section-title">All Staff Members</h3>
            <p class="section-sub">Login credentials are in users table, profiles in staff_profiles</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-brand-border">
                    <th class="table-head">#</th>
                    <th class="table-head">Name & Email</th>
                    <th class="table-head">Role</th>
                    <th class="table-head">Employee Code</th>
                    <th class="table-head">Department</th>
                    <th class="table-head">Phone</th>
                    <th class="table-head">Status</th>
                    <th class="table-head text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                <?php $__empty_1 = true; $__currentLoopData = $staffMembers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="table-row-hover">
                    <td class="table-cell text-brand-sub font-medium"><?php echo e($i + 1); ?></td>
                    <td class="table-cell">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-brand-acents flex items-center justify-center text-brand-accent text-xs font-bold flex-shrink-0">
                                <?php echo e(strtoupper(substr($member->user->name ?? '?', 0, 1))); ?>

                            </div>
                            <div>
                                <p class="text-sm font-semibold text-brand-text"><?php echo e($member->user->name); ?></p>
                                <p class="text-xs text-brand-sub"><?php echo e($member->user->email); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="table-cell">
                        <?php $role = $member->user->roles->first()?->name ?? 'N/A'; ?>
                        <?php if($role === 'admin'): ?>
                            <span class="badge badge-danger">Admin</span>
                        <?php elseif($role === 'accounts'): ?>
                            <span class="badge badge-info">Accountant</span>
                        <?php elseif($role === 'writer'): ?>
                            <span class="badge badge-warning">Writer</span>
                        <?php elseif($role === 'hod'): ?>
                            <span class="badge badge-accent">HOD</span>
                        <?php else: ?>
                            <span class="badge"><?php echo e(ucfirst($role)); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="table-cell text-sm font-mono text-brand-sub"><?php echo e($member->employee_code ?? '—'); ?></td>
                    <td class="table-cell text-sm text-brand-sub"><?php echo e($member->department->name ?? '—'); ?></td>
                    <td class="table-cell text-sm text-brand-sub"><?php echo e($member->phone_number ?? '—'); ?></td>
                    <td class="table-cell">
                        <?php if($member->status === 'active'): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="table-cell text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="<?php echo e(route('admin.staff.edit', $member->id)); ?>" class="btn-secondary py-1.5 px-3 text-xs">
                                <span class="material-symbols-outlined text-[14px]">edit</span> Edit
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-2xl bg-brand-muted flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-brand-sub text-3xl">person_off</span>
                            </div>
                            <h4 class="text-lg font-bold text-brand-text mb-1">No Staff Added Yet</h4>
                            <p class="text-sm text-brand-sub mb-4">Click "Add Staff" to create your first staff member.</p>
                            <a href="<?php echo e(route('admin.staff.create')); ?>" class="btn-primary">
                                <span class="material-symbols-outlined text-[18px]">person_add</span> Add Staff
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/admin/staff/index.blade.php ENDPATH**/ ?>