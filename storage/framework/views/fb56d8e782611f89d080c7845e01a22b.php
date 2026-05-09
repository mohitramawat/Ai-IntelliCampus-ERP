

<?php $__env->startSection('content'); ?>


<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-black text-brand-text">All Students</h1>
        <p class="text-sm text-brand-sub mt-0.5">Complete student registry with search, filters, and one-click detail view.</p>
    </div>
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
    div.dataTables_wrapper div.dataTables_filter label {
        display: flex; align-items: center; gap: 8px; font-size: 12px; color: #6b7280; margin: 0;
    }
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
    table.dataTable thead th.sorting,
    table.dataTable thead th.sorting_asc,
    table.dataTable thead th.sorting_desc { padding-right: 16px !important; }

    div.dataTables_wrapper div.dt-bottombar {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 16px; border-top: 1px solid #f3f4f6; flex-wrap: wrap; gap: 8px;
    }
    div.dataTables_wrapper div.dataTables_info { font-size: 11px; color: #9ca3af; font-weight: 500; }

    div.dataTables_wrapper div.dataTables_paginate { display: flex; align-items: center; gap: 2px; }
    div.dataTables_wrapper div.dataTables_paginate span { display: flex; gap: 2px; }
    div.dataTables_wrapper div.dataTables_paginate .paginate_button {
        display: inline-flex !important; align-items: center; justify-content: center;
        min-width: 30px; height: 30px; padding: 0 8px;
        border-radius: 8px; font-size: 12px; font-weight: 600;
        color: #6b7280 !important; border: 1px solid transparent !important;
        background: transparent !important; cursor: pointer; transition: all .15s; box-shadow: none !important;
    }
    div.dataTables_wrapper div.dataTables_paginate .paginate_button:hover:not(.disabled) {
        background: #f3f4f6 !important; color: #111827 !important; border-color: #e5e7eb !important;
    }
    div.dataTables_wrapper div.dataTables_paginate .paginate_button.current,
    div.dataTables_wrapper div.dataTables_paginate .paginate_button.current:hover {
        background: #eef2ff !important; color: #6366f1 !important; border-color: #c7d2fe !important;
    }
    div.dataTables_wrapper div.dataTables_paginate .paginate_button.disabled,
    div.dataTables_wrapper div.dataTables_paginate .paginate_button.disabled:hover { opacity: .35; cursor: default; }
    div.dataTables_wrapper div.dataTables_processing { background:transparent; border:none; box-shadow:none; font-size:12px; color:#9ca3af; }
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
    const cs = document.getElementById('filter-course');
    cs.innerHTML = '<option value="">All Courses</option>';
    allCourseOptions.forEach(o => cs.appendChild(o.cloneNode(true)));
    const bs = document.getElementById('filter-batch');
    bs.innerHTML = '<option value="">All Batches</option>';
    allBatchOptions.forEach(o => bs.appendChild(o.cloneNode(true)));
    table.ajax.reload();
});

const table = $('#students-table').DataTable({
    processing: true,
    serverSide: true,
    dom: '<"dt-toolbar"lf>t<"dt-bottombar"ip>',
    ajax: {
        url: '<?php echo e(route('admin.students.datatable')); ?>',
        data: d => {
            d.department_id = document.getElementById('filter-department').value;
            d.course_id     = document.getElementById('filter-course').value;
            d.batch_id      = document.getElementById('filter-batch').value;
            d.status        = document.getElementById('filter-status').value;
        }
    },
    columns: [
        { data:'DT_RowIndex', name:'DT_RowIndex', orderable:false, searchable:false,
          className:'table-cell',
          render: d => `<span style="font-size:12px;color:#9ca3af;font-weight:500">${d}</span>` },
        { data:null, name:'name', className:'table-cell',
          render: d =>
            `<div style="display:flex;align-items:center;gap:10px">
               <div style="width:32px;height:32px;border-radius:50%;background:#eef2ff;border:1px solid #c7d2fe;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                 <span style="font-size:12px;font-weight:700;color:#6366f1">${d.name.charAt(0).toUpperCase()}</span>
               </div>
               <div>
                 <p style="font-size:13px;font-weight:600;color:#111827;margin:0;line-height:1.3">${d.name}</p>
                 <p style="font-size:11px;color:#9ca3af;margin:0">${d.email}</p>
               </div>
             </div>` },
        { data:'roll_number', name:'roll_number', className:'table-cell',
          render: d => d
            ? `<span style="background:#eef2ff;color:#6366f1;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px">${d}</span>`
            : `<span style="color:#d1d5db">—</span>` },
        { data:'course',     name:'course',     className:'table-cell', render: d => `<span style="font-size:13px;color:#111827">${d}</span>` },
        { data:'batch',      name:'batch',      className:'table-cell', render: d => `<span style="font-size:13px;color:#111827">${d}</span>` },
        { data:'department', name:'department', className:'table-cell', render: d => `<span style="font-size:12px;color:#6b7280">${d}</span>` },
        { data:'doc_status', name:'doc_status', orderable:false, className:'table-cell text-center',
          render: d => {
            const [done, total] = d.split('/').map(Number);
            const ok = done >= total;
            return `<span style="background:${ok?'#f0fdf4':'#fef2f2'};color:${ok?'#16a34a':'#ef4444'};font-size:12px;font-weight:700;padding:3px 10px;border-radius:20px">${d}</span>`;
          }},
        { data:'status_badge', name:'is_active', orderable:false, className:'table-cell text-center' },
        { data:'action',       name:'action',    orderable:false, searchable:false, className:'table-cell text-center' },
    ],
    order:      [[1,'asc']],
    pageLength: 15,
    lengthMenu: [[10,15,25,50],[10,15,25,50]],
    language: {
        processing:        '<span style="font-size:12px;color:#9ca3af">Loading…</span>',
        search:            '',
        searchPlaceholder: '🔍  Search students…',
        lengthMenu:        'Show _MENU_',
        info:              'Showing _START_–_END_ of _TOTAL_ students',
        infoEmpty:         'No students found',
        zeroRecords:       'No students match the current filters.',
        emptyTable:        'No students found.',
        paginate: { first:'«', last:'»', next:'›', previous:'‹' },
    },
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views\admin\students\index.blade.php ENDPATH**/ ?>