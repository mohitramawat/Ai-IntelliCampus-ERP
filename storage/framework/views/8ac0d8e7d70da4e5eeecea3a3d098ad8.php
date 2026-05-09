<?php $__env->startSection('content'); ?>


<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-brand-sub text-sm font-semibold mb-1 uppercase tracking-wider">
            <span class="material-symbols-outlined text-[16px]">home</span>
            HOD <span class="material-symbols-outlined text-[14px]">chevron_right</span> <span class="text-violet-500">Department Teachers</span>
        </div>
        <h1 class="text-3xl font-black text-brand-text flex items-center gap-3">
            Department Teachers
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 border border-orange-200 text-orange-600 text-[10px] font-bold uppercase tracking-wider">
                <span class="material-symbols-outlined text-[14px]">lock</span>
                Read Only
            </span>
        </h1>
        <p class="text-sm text-brand-sub mt-1">
            <?php echo e($dept ? $dept->name . ' Department' : 'No department assigned'); ?>

        </p>
    </div>
</div>

<?php if(!$dept): ?>
<div class="card flex flex-col items-center justify-center py-20 grayscale opacity-60">
    <div class="w-24 h-24 rounded-full bg-brand-muted flex items-center justify-center mb-4">
        <span class="material-symbols-outlined text-[48px] text-brand-border">person_off</span>
    </div>
    <h3 class="text-lg font-bold text-brand-text mb-2">No Department Linked</h3>
    <p class="text-sm text-brand-sub max-w-sm text-center">Your profile is not currently associated with any department. Please contact the administrator.</p>
</div>
<?php else: ?>


<div class="bg-gradient-to-r from-violet-50 to-fuchsia-50 border border-violet-100 rounded-2xl p-4 mb-6 flex items-start gap-4">
    <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center flex-shrink-0">
        <span class="material-symbols-outlined text-violet-600 text-[22px]">info</span>
    </div>
    <div>
        <h4 class="text-sm font-bold text-violet-800 mb-1">Read-Only Access</h4>
        <p class="text-xs text-violet-600/80 font-medium">
            You are viewing all teachers in the <strong><?php echo e($dept->name); ?></strong> department. If you need to add, edit, or remove faculty members, please contact the Data Entry / Writer department.
        </p>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="p-5 border-b border-brand-border bg-brand-muted/30 flex justify-between items-center">
        <h3 class="text-base font-bold text-brand-text flex items-center gap-2">
            <span class="material-symbols-outlined text-fuchsia-500">supervisor_account</span>
            Faculty Directory
        </h3>
    </div>
    <div class="p-5">
        <div class="overflow-x-auto">
            <table id="teachers-table" class="w-full">
                <thead>
                    <tr class="border-b-2 border-brand-border/50 text-left">
                        <th class="pb-3 text-[11px] font-bold text-brand-sub uppercase tracking-wider">#</th>
                        <th class="pb-3 text-[11px] font-bold text-brand-sub uppercase tracking-wider">Teacher Profile</th>
                        <th class="pb-3 text-[11px] font-bold text-brand-sub uppercase tracking-wider">Email Contact</th>
                        <th class="pb-3 text-[11px] font-bold text-brand-sub uppercase tracking-wider">Department</th>
                        <th class="pb-3 text-[11px] font-bold text-brand-sub uppercase tracking-wider text-right">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$('#teachers-table').DataTable({
    processing: true, serverSide: true,
    ajax: '<?php echo e(route("hod.teachers.datatable")); ?>',
    columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, width: '50px', className: 'text-sm text-brand-sub font-semibold py-4 border-b border-brand-border/40' },
        { data: 'name',        name: 'name', className: 'text-sm font-bold text-brand-text py-4 border-b border-brand-border/40' },
        { data: 'email',       name: 'email', className: 'text-sm text-brand-sub font-medium py-4 border-b border-brand-border/40' },
        { data: 'dept_name',   name: 'dept_name', orderable: false, className: 'text-sm text-brand-sub py-4 border-b border-brand-border/40' },
        { data: 'status_badge',name: 'status_badge', orderable: false, searchable: false, className: 'text-right py-4 border-b border-brand-border/40' },
    ],
    order: [[1, 'asc']],
    pageLength: 20,
    language: { 
        search: '', 
        searchPlaceholder: 'Search faculty...',
        lengthMenu: '<span class="text-xs text-brand-sub font-semibold mr-2">Show:</span> _MENU_',
        info: '<span class="text-xs text-brand-sub font-medium">Showing _START_ to _END_ of _TOTAL_ teachers</span>',
    },
    dom: '<"flex flex-col sm:flex-row gap-4 items-center justify-between mb-6"<"flex items-center gap-3"lB>f>rt<"flex flex-col sm:flex-row gap-4 items-center justify-between mt-6"ip>',
    buttons: [{ extend: 'excel', className: 'px-4 py-2 rounded-xl text-xs font-bold text-brand-sub bg-brand-muted hover:bg-brand-border/50 border border-brand-border transition-colors', text: '⬇ Export' }],
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views\hod\teachers.blade.php ENDPATH**/ ?>