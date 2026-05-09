<?php $__env->startSection('content'); ?>

<style>
.dt-th{padding:10px 14px;font-size:11px;font-weight:700;color:#64748B;text-align:left;text-transform:uppercase;letter-spacing:.05em}
.btn-view{width:28px;height:28px;border-radius:7px;border:1.5px solid #E0F2FE;background:#F0F9FF;color:#0284C7;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;transition:all .2s}
.btn-view:hover{background:#0EA5E9;color:#fff}
.btn-del{width:28px;height:28px;border-radius:7px;border:1.5px solid #FEE2E2;background:#FFF5F5;color:#EF4444;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .2s}
.btn-del:hover{background:#EF4444;color:#fff}
.tab-btn{padding:8px 18px;border-radius:10px;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:all .2s;color:#64748B;background:#F1F5F9}
.tab-btn.active{background:#0EA5E9;color:#fff;box-shadow:0 4px 14px rgba(14,165,233,.3)}
.toast-msg{position:fixed;top:20px;right:20px;z-index:999;padding:12px 20px;border-radius:12px;font-size:13px;font-weight:600;color:#fff;box-shadow:0 6px 20px rgba(0,0,0,.15);transform:translateX(130%);transition:transform .35s cubic-bezier(.34,1.56,.64,1)}
.toast-msg.show{transform:translateX(0)}
.toast-msg.success{background:#10B981}.toast-msg.error{background:#EF4444}
.md-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);z-index:200;display:flex;align-items:center;justify-content:center;padding:16px}
.md-box{background:#fff;border-radius:20px;width:100%;max-width:380px;box-shadow:0 20px 60px rgba(0,0,0,.18);overflow:hidden;animation:mdIn .25s ease}
@keyframes mdIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.md-head{padding:18px 22px 14px;border-bottom:1px solid #E4E9F0;display:flex;align-items:center;justify-content:space-between}
</style>


<div style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:22px">
    <div>
        <div style="font-size:12px;color:#64748B;margin-bottom:4px;display:flex;gap:5px;align-items:center">
            <span class="material-symbols-outlined" style="font-size:14px">home</span>
            <span>Admin</span>
            <span class="material-symbols-outlined" style="font-size:12px">chevron_right</span>
            <span style="color:#0EA5E9;font-weight:700">Master Data</span>
        </div>
        <h1 style="font-size:26px;font-weight:800;color:#1A202C;margin:0">Master Data Overview</h1>
        <p style="font-size:13px;color:#64748B;margin:2px 0 0">View and manage all academic structure data.</p>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <?php $__currentLoopData = [
        ['label'=>'Departments', 'val'=>$stats['departments'], 'icon'=>'account_tree',  'color'=>'text-brand-accent',   'bg'=>'bg-brand-acents'],
        ['label'=>'Courses',     'val'=>$stats['courses'],     'icon'=>'school',         'color'=>'text-status-info',    'bg'=>'bg-status-infos'],
        ['label'=>'Batches',     'val'=>$stats['batches'],     'icon'=>'layers',          'color'=>'text-status-warning', 'bg'=>'bg-status-warnings'],
        ['label'=>'Subjects',    'val'=>$stats['subjects'],    'icon'=>'menu_book',      'color'=>'text-status-success', 'bg'=>'bg-status-successs'],
        ['label'=>'Fee Structs', 'val'=>$stats['fees'],        'icon'=>'payments',       'color'=>'text-status-danger',  'bg'=>'bg-status-dangers'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="kpi-card">
        <div class="kpi-icon <?php echo e($kpi['bg']); ?>">
            <span class="material-symbols-outlined <?php echo e($kpi['color']); ?> text-[22px]"><?php echo e($kpi['icon']); ?></span>
        </div>
        <div>
            <div class="kpi-value"><?php echo e($kpi['val']); ?></div>
            <div class="kpi-label"><?php echo e($kpi['label']); ?></div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap">
    <button class="tab-btn active" onclick="switchTab('depts', this)">Departments</button>
    <button class="tab-btn" onclick="switchTab('courses', this)">Courses</button>
    <button class="tab-btn" onclick="switchTab('batches', this)">Batches</button>
    <button class="tab-btn" onclick="switchTab('subjects', this)">Subjects</button>
</div>


<div id="tab-depts" class="card">
    <div style="overflow-x:auto">
        <table id="dept-table" class="w-full" style="width:100%">
            <thead>
                <tr style="border-bottom:2px solid #F0F4F8">
                    <th class="dt-th">#</th>
                    <th class="dt-th">Name</th>
                    <th class="dt-th">Code</th>
                    <th class="dt-th">Campus</th>
                    <th class="dt-th">Courses</th>
                    <th class="dt-th">Status</th>
                    <th class="dt-th text-center">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>


<div id="tab-courses" class="card" style="display:none">
    <div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:12px">
        <select id="dept-filter" class="f-input" style="width:auto;min-width:200px;padding:8px 14px;border:1.5px solid #E4E9F0;border-radius:10px;font-size:12px;font-weight:600" onchange="dtCourses.ajax.reload()">
            <option value="">All Departments</option>
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($d->id); ?>"><?php echo e($d->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div style="overflow-x:auto">
        <table id="course-table" class="w-full" style="width:100%">
            <thead>
                <tr style="border-bottom:2px solid #F0F4F8">
                    <th class="dt-th">#</th>
                    <th class="dt-th">Name</th>
                    <th class="dt-th">Code</th>
                    <th class="dt-th">Department</th>
                    <th class="dt-th">Units</th>
                    <th class="dt-th">Batches</th>
                    <th class="dt-th">Status</th>
                    <th class="dt-th text-center">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>


<div id="tab-batches" class="card" style="display:none">
    <div style="overflow-x:auto">
        <table id="batch-table" class="w-full" style="width:100%">
            <thead>
                <tr style="border-bottom:2px solid #F0F4F8">
                    <th class="dt-th">#</th>
                    <th class="dt-th">Name</th>
                    <th class="dt-th">Course</th>
                    <th class="dt-th">Year Range</th>
                    <th class="dt-th">Students</th>
                    <th class="dt-th">Status</th>
                    <th class="dt-th text-center">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>


<div id="tab-subjects" class="card" style="display:none">
    <div style="overflow-x:auto">
        <table id="subject-table" class="w-full" style="width:100%">
            <thead>
                <tr style="border-bottom:2px solid #F0F4F8">
                    <th class="dt-th">#</th>
                    <th class="dt-th">Name</th>
                    <th class="dt-th">Code</th>
                    <th class="dt-th">Course</th>
                    <th class="dt-th">Semester</th>
                    <th class="dt-th">Status</th>
                    <th class="dt-th text-center">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>


<div id="del-modal" style="display:none" class="md-overlay" onclick="if(event.target===this)closeDelModal()">
    <div class="md-box">
        <div class="md-head">
            <h3 style="font-size:16px;font-weight:800;color:#EF4444;margin:0">⚠️ Confirm Delete</h3>
            <button onclick="closeDelModal()" style="border:none;background:none;cursor:pointer;font-size:22px;color:#64748B">×</button>
        </div>
        <div style="padding:20px 22px">
            <p style="font-size:13px;color:#64748B;margin:0 0 20px">Delete <strong id="del-name"></strong>? This cannot be undone.</p>
            <div style="display:flex;gap:10px;justify-content:flex-end">
                <button onclick="closeDelModal()" style="padding:9px 20px;border-radius:10px;border:1.5px solid #E4E9F0;background:#F5F7FA;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                <button onclick="doDelete()" style="padding:9px 20px;border-radius:10px;border:none;background:#EF4444;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Delete</button>
            </div>
        </div>
    </div>
</div>

<div id="toast" class="toast-msg"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let delType = null, delId = null;

// ── DataTables ──────────────────────────────────────────────────
const dtDepts = $('#dept-table').DataTable({
    processing: true, serverSide: true,
    ajax: '<?php echo e(route("admin.master.departments.datatable")); ?>',
    columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
        { data: 'name',        name: 'name' },
        { data: 'code',        name: 'code', width: '80px' },
        { data: 'campus_name', name: 'campus_name', orderable: false },
        { data: 'courses_count', name: 'courses_count', width: '90px' },
        { data: 'status_badge',  name: 'status_badge',  orderable: false, searchable: false, width: '90px' },
        { data: 'action',        name: 'action',         orderable: false, searchable: false, width: '80px', className: 'text-center' },
    ],
    pageLength: 15, language: { search: '', searchPlaceholder: 'Search...' },
    dom: '<"flex flex-wrap gap-2 items-center justify-between mb-4"fB>rtip',
    buttons: [{ extend: 'excel', className: 'btn-secondary text-xs', text: '⬇ Export' }],
});

const dtCourses = $('#course-table').DataTable({
    processing: true, serverSide: true,
    ajax: { url: '<?php echo e(route("admin.master.courses.datatable")); ?>', data: d => { d.department_id = $('#dept-filter').val(); } },
    columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
        { data: 'name',          name: 'name' },
        { data: 'code',          name: 'code', width: '80px' },
        { data: 'dept_name',     name: 'dept_name', orderable: false },
        { data: 'unit_info',     name: 'unit_info', orderable: false, width: '100px' },
        { data: 'batches_count', name: 'batches_count', width: '80px' },
        { data: 'status_badge',  name: 'status_badge', orderable: false, searchable: false, width: '90px' },
        { data: 'action',        name: 'action',  orderable: false, searchable: false, width: '80px', className: 'text-center' },
    ],
    pageLength: 15, language: { search: '', searchPlaceholder: 'Search...' },
    dom: '<"flex flex-wrap gap-2 items-center justify-between mb-4"fB>rtip',
    buttons: [{ extend: 'excel', className: 'btn-secondary text-xs', text: '⬇ Export' }],
});

const dtBatches = $('#batch-table').DataTable({
    processing: true, serverSide: true,
    ajax: '<?php echo e(route("admin.master.batches.datatable")); ?>',
    columns: [
        { data: 'DT_RowIndex',  orderable: false, searchable: false, width: '50px' },
        { data: 'name',         name: 'name' },
        { data: 'course_name',  name: 'course_name', orderable: false },
        { data: 'year_range',   name: 'year_range',  orderable: false, width: '110px' },
        { data: 'students_count', name: 'students_count', width: '90px' },
        { data: 'status_badge',   name: 'status_badge', orderable: false, searchable: false, width: '90px' },
        { data: 'action',         name: 'action', orderable: false, searchable: false, width: '70px', className: 'text-center' },
    ],
    pageLength: 15, language: { search: '', searchPlaceholder: 'Search...' },
    dom: '<"flex flex-wrap gap-2 items-center justify-between mb-4"fB>rtip',
    buttons: [{ extend: 'excel', className: 'btn-secondary text-xs', text: '⬇ Export' }],
});

const dtSubjects = $('#subject-table').DataTable({
    processing: true, serverSide: true,
    ajax: '<?php echo e(route("admin.master.subjects.datatable")); ?>',
    columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
        { data: 'name',        name: 'name' },
        { data: 'code',        name: 'code', width: '100px' },
        { data: 'course_name', name: 'course_name', orderable: false },
        { data: 'sem_label',   name: 'semester', width: '90px' },
        { data: 'status_badge',name: 'status_badge', orderable: false, searchable: false, width: '90px' },
        { data: 'action',      name: 'action', orderable: false, searchable: false, width: '70px', className: 'text-center' },
    ],
    pageLength: 15, language: { search: '', searchPlaceholder: 'Search...' },
    dom: '<"flex flex-wrap gap-2 items-center justify-between mb-4"fB>rtip',
    buttons: [{ extend: 'excel', className: 'btn-secondary text-xs', text: '⬇ Export' }],
});

// ── Tab switching ───────────────────────────────────────────────
function switchTab(tab, btn) {
    ['depts','courses','batches','subjects'].forEach(t => {
        document.getElementById('tab-' + t).style.display = 'none';
    });
    document.getElementById('tab-' + tab).style.display = 'block';
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    
    // Adjust columns for the datatable in the active tab
    const map = { depts: dtDepts, courses: dtCourses, batches: dtBatches, subjects: dtSubjects };
    if(map[tab]) map[tab].columns.adjust().draw();
}

// ── Delete ───────────────────────────────────────────────────────
function confirmDeleteDept(id, name) { delType='department'; delId=id; document.getElementById('del-name').textContent=name; document.getElementById('del-modal').style.display='flex'; }
function confirmDelete(type, id, name) { delType=type; delId=id; document.getElementById('del-name').textContent=name; document.getElementById('del-modal').style.display='flex'; }
function closeDelModal() { document.getElementById('del-modal').style.display='none'; delType=null; delId=null; }

const delUrls = {
    department: '<?php echo e(url("admin/master/departments")); ?>',
    course:     '<?php echo e(url("admin/master/courses")); ?>',
    batch:      '<?php echo e(url("admin/master/batches")); ?>',
    subject:    '<?php echo e(url("admin/master/subjects")); ?>',
};
const dtMap = { department: dtDepts, course: dtCourses, batch: dtBatches, subject: dtSubjects };

async function doDelete() {
    const res  = await fetch(`${delUrls[delType]}/${delId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' } });
    const data = await res.json();
    res.ok ? showToast(data.message, 'success') : showToast(data.message || 'Error.', 'error');
    dtMap[delType]?.ajax.reload(null, false);
    closeDelModal();
}

function showToast(msg, type='success') {
    const t = document.getElementById('toast');
    t.textContent = msg; t.className = `toast-msg ${type} show`;
    setTimeout(() => t.classList.remove('show'), 3000);
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views\admin\master\index.blade.php ENDPATH**/ ?>