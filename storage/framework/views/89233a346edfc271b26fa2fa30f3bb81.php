

<?php $__env->startSection('content'); ?>


<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-black text-brand-text">Student Registry</h1>
        <p class="text-sm text-brand-sub mt-0.5">Manage student records, update profiles, and track document status.</p>
    </div>
    <a href="<?php echo e(route('writer.students.create')); ?>" class="btn-primary self-start">
        <span class="material-symbols-outlined text-[18px]">person_add</span>
        New Admission
    </a>
</div>


<div class="card mb-5 p-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div>
            <label class="label text-xs">Department</label>
            <select id="filter-department" class="input text-sm">
                <option value="">All Departments</option>
                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($d->id); ?>"><?php echo e($d->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="label text-xs">Course</label>
            <select id="filter-course" class="input text-sm">
                <option value="">All Courses</option>
                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($c->id); ?>" data-dept="<?php echo e($c->department_id); ?>"><?php echo e($c->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="label text-xs">Batch</label>
            <select id="filter-batch" class="input text-sm">
                <option value="">All Batches</option>
                <?php $__currentLoopData = $batches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($b->id); ?>" data-course="<?php echo e($b->course_id); ?>"><?php echo e($b->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="label text-xs">Status</label>
            <select id="filter-status" class="input text-sm">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>
    <div class="flex justify-end mt-3">
        <button id="btn-reset-filters" class="btn-secondary text-xs py-2">
            <span class="material-symbols-outlined text-[15px]">filter_alt_off</span>
            Reset Filters
        </button>
    </div>
</div>


<?php if(session('success')): ?>
<div class="mb-5 p-4 rounded-xl bg-status-successs border border-status-success/20 flex items-center gap-3">
    <span class="material-symbols-outlined text-status-success">check_circle</span>
    <p class="text-sm font-bold text-status-success"><?php echo e(session('success')); ?></p>
</div>
<?php endif; ?>


<div class="bg-brand-surface rounded-2xl border border-brand-border shadow-card overflow-hidden">
    <table id="students-table" class="w-full" style="width:100%">
        <thead>
            <tr class="bg-brand-muted">
                <th class="table-head">#</th>
                <th class="table-head">Student</th>
                <th class="table-head">Roll No.</th>
                <th class="table-head">Course</th>
                <th class="table-head">Batch</th>
                <th class="table-head">Department</th>
                <th class="table-head text-center">Docs</th>
                <th class="table-head text-center">Status</th>
                <th class="table-head text-center">Action</th>
            </tr>
        </thead>
    </table>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<style>
    div.dataTables_wrapper { padding: 0; font-family: inherit; }
    div.dataTables_wrapper div.dt-toolbar {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; border-bottom: 1px solid #f3f4f6; gap: 12px; flex-wrap: wrap;
    }
    div.dataTables_wrapper div.dataTables_length { display: flex; align-items: center; gap: 6px; }
    div.dataTables_wrapper div.dataTables_length label {
        display: flex; align-items: center; gap: 6px;
        font-size: 12px; font-weight: 500; color: #6b7280; margin: 0; white-space: nowrap;
    }
    div.dataTables_wrapper div.dataTables_length select {
        appearance: none; -webkit-appearance: none;
        background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px;
        padding: 5px 28px 5px 10px; font-size: 12px; font-weight: 600; color: #111827;
        cursor: pointer; outline: none; min-width: 60px; transition: border-color .15s, box-shadow .15s;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 8px center;
    }
    div.dataTables_wrapper div.dataTables_length select:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }

    div.dataTables_wrapper div.dataTables_filter { display: flex; align-items: center; }
    div.dataTables_wrapper div.dataTables_filter label { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #6b7280; margin: 0; }
    div.dataTables_wrapper div.dataTables_filter input {
        background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px;
        padding: 7px 12px; font-size: 13px; color: #111827; outline: none;
        width: 210px; transition: border-color .15s, box-shadow .15s;
    }
    div.dataTables_wrapper div.dataTables_filter input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
    div.dataTables_wrapper div.dataTables_filter input::placeholder { color: #9ca3af; }

    #students-table { border-collapse: collapse; width: 100% !important; }
    #students-table thead th { border-bottom: 1px solid #e5e7eb; }
    #students-table tbody td { border-bottom: 1px solid #f9fafb; }
    table.dataTable thead th { background-image: none !important; }
    table.dataTable thead th.sorting::after      { content: ' ↕'; font-size: 10px; opacity: .3; }
    table.dataTable thead th.sorting_asc::after  { content: ' ↑'; font-size: 10px; opacity: .7; color: #6366f1; }
    table.dataTable thead th.sorting_desc::after { content: ' ↓'; font-size: 10px; opacity: .7; color: #6366f1; }

    div.dataTables_wrapper div.dt-bottombar {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 16px; border-top: 1px solid #f3f4f6; flex-wrap: wrap; gap: 8px;
    }
    div.dataTables_wrapper div.dataTables_info { font-size: 11px; color: #9ca3af; font-weight: 500; }
    div.dataTables_wrapper div.dataTables_paginate { display: flex; align-items: center; gap: 2px; }
    div.dataTables_wrapper div.dataTables_paginate .paginate_button {
        display: inline-flex !important; align-items: center; justify-content: center;
        min-width: 30px; height: 30px; padding: 0 8px; border-radius: 8px; font-size: 12px; font-weight: 600;
        color: #6b7280 !important; border: 1px solid transparent !important; background: transparent !important;
        cursor: pointer; transition: all .15s;
    }
    div.dataTables_wrapper div.dataTables_paginate .paginate_button.current {
        background: #eef2ff !important; color: #6366f1 !important; border-color: #c7d2fe !important;
    }
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
const allCourseOptions = Array.from(document.querySelectorAll('#filter-course option[data-dept]'));
const allBatchOptions  = Array.from(document.querySelectorAll('#filter-batch option[data-course]'));

document.getElementById('filter-department').addEventListener('change', function () {
    const deptId = this.value;
    const cs = document.getElementById('filter-course');
    cs.innerHTML = '<option value="">All Courses</option>';
    allCourseOptions.filter(o => !deptId || o.dataset.dept === deptId).forEach(o => cs.appendChild(o.cloneNode(true)));
    cs.value = '';
    cs.dispatchEvent(new Event('change'));
    table.ajax.reload();
});
document.getElementById('filter-course').addEventListener('change', function () {
    const cid = this.value;
    const bs = document.getElementById('filter-batch');
    bs.innerHTML = '<option value="">All Batches</option>';
    allBatchOptions.filter(o => !cid || o.dataset.course === cid).forEach(o => bs.appendChild(o.cloneNode(true)));
    bs.value = '';
    table.ajax.reload();
});
document.getElementById('filter-batch').addEventListener('change',  () => table.ajax.reload());
document.getElementById('filter-status').addEventListener('change', () => table.ajax.reload());
document.getElementById('btn-reset-filters').addEventListener('click', () => {
    ['filter-department','filter-course','filter-batch','filter-status'].forEach(id => document.getElementById(id).value = '');
    table.ajax.reload();
});

const table = $('#students-table').DataTable({
    processing: true,
    serverSide: true,
    dom: '<"dt-toolbar"lf>t<"dt-bottombar"ip>',
    ajax: {
        url: '<?php echo e(route('writer.students.datatable')); ?>',
        data: d => {
            d.department_id = document.getElementById('filter-department').value;
            d.course_id     = document.getElementById('filter-course').value;
            d.batch_id      = document.getElementById('filter-batch').value;
            d.status        = document.getElementById('filter-status').value;
        }
    },
    columns: [
        { data:'DT_RowIndex', name:'DT_RowIndex', orderable:false, searchable:false, className:'table-cell text-center' },
        { data:null, name:'name', className:'table-cell',
          render: d => `
            <div class="flex items-center gap-2.5">
               <div class="w-8 h-8 rounded-full bg-brand-acents border border-brand-accent/20 flex items-center justify-center flex-shrink-0">
                 <span class="text-xs font-black text-brand-accent">${d.user_name.charAt(0).toUpperCase()}</span>
               </div>
               <div>
                 <p class="text-[13px] font-bold text-brand-text leading-tight">${d.user_name}</p>
                 <p class="text-[10px] text-brand-sub">${d.user_email}</p>
               </div>
            </div>`
        },
        { data:'roll_number', name:'roll_number', className:'table-cell',
          render: d => d ? `<span class="badge badge-primary text-[10px]">${d}</span>` : `<span class="text-brand-sub">—</span>`
        },
        { data:'course_name',     name:'course',     className:'table-cell text-[12px] text-brand-text' },
        { data:'batch_name',      name:'batch',      className:'table-cell text-[12px] text-brand-text' },
        { data:'department_name', name:'department', className:'table-cell text-[11px] text-brand-sub' },
        { data:'doc_status',      name:'doc_status', orderable:false, className:'table-cell text-center',
          render: d => {
            const ok = d.split('/')[0] === d.split('/')[1];
            return `<span class="badge ${ok?'badge-success':'badge-danger'} text-[10px]">${d}</span>`;
          }
        },
        { data:'status_badge', name:'is_active', orderable:false, className:'table-cell text-center' },
        { data:'action',       name:'action',    orderable:false, searchable:false, className:'table-cell text-center' },
    ],
    order:      [[1,'asc']],
    pageLength: 15,
    language: {
        search: '',
        searchPlaceholder: 'Search registry...',
        lengthMenu: 'Show _MENU_',
        info: 'Showing _START_ to _END_ of _TOTAL_ records',
        paginate: { next: '›', previous: '‹' }
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/writer/students/index.blade.php ENDPATH**/ ?>