<?php $__env->startSection('content'); ?>


<div class="relative bg-gradient-to-r from-pink-500 to-rose-400 rounded-2xl p-6 mb-6 overflow-hidden shadow-[0_4px_14px_rgba(236,72,153,0.35)]">
    <div class="absolute right-0 top-0 h-full opacity-10">
        <svg viewBox="0 0 200 200" fill="white" class="h-full"><circle cx="160" cy="40" r="90"/></svg>
    </div>
    <div class="relative z-10">
        <p class="text-white/80 text-sm font-medium mb-1">Welcome,</p>
        <h2 class="text-2xl font-black text-white"><?php echo e(auth()->user()->name); ?> ✍️</h2>
        <p class="text-white/70 text-sm mt-1"><?php echo e(now()->format('l, d F Y')); ?> · Writer / Data Entry</p>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php $__currentLoopData = [
        ['label'=>'Total Students','value'=>$totalStudents, 'icon'=>'group',           'color'=>'text-brand-accent',  'bg'=>'bg-brand-acents'],
        ['label'=>'Teachers',      'value'=>$totalTeachers, 'icon'=>'supervisor_account','color'=>'text-status-success','bg'=>'bg-status-successs'],
        ['label'=>'Total Documents','value'=>$totalDocs,     'icon'=>'folder_open',     'color'=>'text-status-warning','bg'=>'bg-status-warnings'],
        ['label'=>'Active Batches', 'value'=>$totalBatches,  'icon'=>'layers',          'color'=>'text-status-info',   'bg'=>'bg-status-infos'],
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
        <h3 class="section-title mb-4" style="display:flex;align-items:center;gap:6px">
            <span class="material-symbols-outlined text-brand-accent text-[18px]">group</span>
            Student Management
        </h3>
        <div class="space-y-2">
            <a href="<?php echo e(route('writer.students.create')); ?>" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-brand-muted transition-colors group">
                <div class="w-9 h-9 rounded-xl bg-brand-muted flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-brand-accent text-[20px]">person_add</span>
                </div>
                <span class="text-sm font-semibold text-brand-text">Add New Student</span>
                <span class="material-symbols-outlined text-brand-sub text-[16px] ml-auto">chevron_right</span>
            </a>
            <a href="<?php echo e(route('writer.students.index')); ?>" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-brand-muted transition-colors group">
                <div class="w-9 h-9 rounded-xl bg-brand-muted flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-status-info text-[20px]">group</span>
                </div>
                <span class="text-sm font-semibold text-brand-text">All Students List</span>
                <span class="material-symbols-outlined text-brand-sub text-[16px] ml-auto">chevron_right</span>
            </a>
            <a href="<?php echo e(route('writer.students.pending-documents')); ?>" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-brand-muted transition-colors group">
                <div class="w-9 h-9 rounded-xl bg-brand-muted flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-status-warning text-[20px]">folder_open</span>
                </div>
                <span class="text-sm font-semibold text-brand-text">Pending Documents</span>
                <span class="material-symbols-outlined text-brand-sub text-[16px] ml-auto">chevron_right</span>
            </a>
        </div>
    </div>

    
    <div class="card">
        <h3 class="section-title mb-4" style="display:flex;align-items:center;gap:6px">
            <span class="material-symbols-outlined text-status-success text-[18px]">database</span>
            Master Data
        </h3>
        <div class="space-y-2">
            <a href="<?php echo e(route('writer.master.departments.index')); ?>" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-brand-muted transition-colors group">
                <div class="w-9 h-9 rounded-xl bg-status-successs flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-status-success text-[20px]">account_tree</span>
                </div>
                <span class="text-sm font-semibold text-brand-text">Departments</span>
                <span class="material-symbols-outlined text-brand-sub text-[16px] ml-auto">chevron_right</span>
            </a>
            <a href="<?php echo e(route('writer.master.courses.index')); ?>" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-brand-muted transition-colors group">
                <div class="w-9 h-9 rounded-xl bg-status-infos flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-status-info text-[20px]">school</span>
                </div>
                <span class="text-sm font-semibold text-brand-text">Courses</span>
                <span class="material-symbols-outlined text-brand-sub text-[16px] ml-auto">chevron_right</span>
            </a>
            <a href="<?php echo e(route('writer.master.batches.index')); ?>" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-brand-muted transition-colors group">
                <div class="w-9 h-9 rounded-xl bg-brand-muted flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-status-warning text-[20px]">layers</span>
                </div>
                <span class="text-sm font-semibold text-brand-text">Batches</span>
                <span class="material-symbols-outlined text-brand-sub text-[16px] ml-auto">chevron_right</span>
            </a>
            <a href="<?php echo e(route('writer.master.subjects.index')); ?>" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-brand-muted transition-colors group">
                <div class="w-9 h-9 rounded-xl bg-brand-acents flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-brand-accent text-[20px]">menu_book</span>
                </div>
                <span class="text-sm font-semibold text-brand-text">Subjects</span>
                <span class="material-symbols-outlined text-brand-sub text-[16px] ml-auto">chevron_right</span>
            </a>
            <a href="<?php echo e(route('writer.master.fees.index')); ?>" class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-brand-muted transition-colors group">
                <div class="w-9 h-9 rounded-xl" style="background:#F0FDF4;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:transform .2s" class="group-hover:scale-105">
                    <span class="material-symbols-outlined text-[20px]" style="color:#16A34A">payments</span>
                </div>
                <span class="text-sm font-semibold text-brand-text">Fee Structures</span>
                <span class="material-symbols-outlined text-brand-sub text-[16px] ml-auto">chevron_right</span>
            </a>
        </div>
    </div>

    
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="section-title">Recent Activity</h3>
            <span class="badge badge-info">Today</span>
        </div>
        <div class="flex flex-col items-center justify-center py-10 text-center">
            <span class="material-symbols-outlined text-[44px] text-brand-border mb-3">edit_note</span>
            <p class="text-sm font-semibold text-brand-sub">No entries today</p>
            <p class="text-xs text-brand-sub/60 mt-1">Your recent data entry actions will appear here</p>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views\writer\dashboard.blade.php ENDPATH**/ ?>