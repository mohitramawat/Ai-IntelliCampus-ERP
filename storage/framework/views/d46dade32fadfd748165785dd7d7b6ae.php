<?php $__env->startSection('content'); ?>


<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="<?php echo e(route('admin.staff.index')); ?>" class="text-brand-sub hover:text-brand-accent transition-colors">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <h1 class="text-2xl font-extrabold text-brand-text tracking-tight">Add New Staff</h1>
        </div>
        <p class="text-sm text-brand-sub ml-7">Create login in <code class="text-xs bg-brand-muted px-1.5 py-0.5 rounded">users</code> table + profile in <code class="text-xs bg-brand-muted px-1.5 py-0.5 rounded">staff_profiles</code> table</p>
    </div>
</div>


<?php if($errors->any()): ?>
<div class="bg-status-dangers border border-status-danger/20 text-status-danger px-5 py-3 rounded-xl mb-6 text-sm font-medium">
    <ul class="list-disc list-inside space-y-0.5">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>

<form action="<?php echo e(route('admin.staff.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        
        <div class="lg:col-span-1">
            <div class="card">
                <div class="flex items-center gap-3 mb-5">
                    <div class="kpi-icon bg-status-dangers">
                        <span class="material-symbols-outlined text-status-danger text-[22px]">lock</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-brand-text">Login Credentials</h3>
                        <p class="text-xs text-brand-sub">Stored in <code class="text-[10px] bg-brand-muted px-1 py-0.5 rounded">users</code> table</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="label">Full Name *</label>
                        <input type="text" name="name" value="<?php echo e(old('name')); ?>" class="input" placeholder="e.g. Rajesh Kumar" required>
                    </div>

                    <div>
                        <label class="label">Email Address *</label>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="input" placeholder="e.g. rajesh@intellicampus.com" required>
                    </div>

                    <div>
                        <label class="label">Password *</label>
                        <input type="text" name="password" value="<?php echo e(old('password')); ?>" class="input font-mono" placeholder="Set a strong password" required>
                        <p class="text-xs text-brand-sub mt-1">Share this with the staff member securely.</p>
                    </div>

                    <div>
                        <label class="label">System Role *</label>
                        <select name="role" class="input" required>
                            <option value="">-- Select Role --</option>
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($role); ?>" <?php echo e(old('role') == $role ? 'selected' : ''); ?>>
                                    <?php echo e(ucfirst($role === 'accounts' ? 'Accountant' : ($role === 'hod' ? 'HOD (Head of Dept.)' : $role))); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="lg:col-span-2">
            <div class="card">
                <div class="flex items-center gap-3 mb-5">
                    <div class="kpi-icon bg-brand-acents">
                        <span class="material-symbols-outlined text-brand-accent text-[22px]">person</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-brand-text">Personal Details</h3>
                        <p class="text-xs text-brand-sub">Stored in <code class="text-[10px] bg-brand-muted px-1 py-0.5 rounded">staff_profiles</code> table</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Employee Code</label>
                        <input type="text" name="employee_code" value="<?php echo e(old('employee_code')); ?>" class="input" placeholder="e.g. EMP-001">
                    </div>

                    <div>
                        <label class="label">Department</label>
                        <select name="department_id" class="input">
                            <option value="">-- Optional --</option>
                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($dept->id); ?>" <?php echo e(old('department_id') == $dept->id ? 'selected' : ''); ?>><?php echo e($dept->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div>
                        <label class="label">Phone Number</label>
                        <input type="text" name="phone_number" value="<?php echo e(old('phone_number')); ?>" class="input" placeholder="e.g. 9876543210">
                    </div>

                    <div>
                        <label class="label">Gender</label>
                        <select name="gender" class="input">
                            <option value="">-- Select --</option>
                            <option value="male" <?php echo e(old('gender') == 'male' ? 'selected' : ''); ?>>Male</option>
                            <option value="female" <?php echo e(old('gender') == 'female' ? 'selected' : ''); ?>>Female</option>
                            <option value="other" <?php echo e(old('gender') == 'other' ? 'selected' : ''); ?>>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="label">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="<?php echo e(old('date_of_birth')); ?>" class="input">
                    </div>

                    <div>
                        <label class="label">Joining Date</label>
                        <input type="date" name="joining_date" value="<?php echo e(old('joining_date')); ?>" class="input">
                    </div>

                    <div class="md:col-span-2">
                        <label class="label">Address</label>
                        <textarea name="address" class="input" rows="2" placeholder="Full residential address"><?php echo e(old('address')); ?></textarea>
                    </div>
                </div>
            </div>

            
            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="btn-primary py-3 px-6">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    Create Staff Account
                </button>
                <a href="<?php echo e(route('admin.staff.index')); ?>" class="btn-secondary py-3 px-6">Cancel</a>
            </div>
        </div>

    </div>
</form>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/admin/staff/create.blade.php ENDPATH**/ ?>