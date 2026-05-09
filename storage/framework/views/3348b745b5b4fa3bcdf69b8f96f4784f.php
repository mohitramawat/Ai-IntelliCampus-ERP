<?php $__env->startSection('content'); ?>

<style>
.md-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);z-index:200;display:flex;align-items:center;justify-content:center;padding:16px}
.md-box{background:#fff;border-radius:20px;width:100%;max-width:520px;box-shadow:0 20px 60px rgba(0,0,0,.18);overflow:hidden;animation:mdIn .25s ease}
@keyframes mdIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.md-head{padding:20px 24px 16px;border-bottom:1px solid #E4E9F0;display:flex;align-items:center;justify-content:space-between}
.md-title{font-size:17px;font-weight:800;color:#1A202C;margin:0}
.md-body{padding:24px}
.md-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.f-group{margin-bottom:0}
.f-label{display:block;font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px}
.f-input{width:100%;padding:10px 14px;border:1.5px solid #E4E9F0;border-radius:12px;font-size:13px;font-weight:500;color:#1A202C;transition:border-color .2s,box-shadow .2s;outline:none;box-sizing:border-box}
.f-input:focus{border-color:#0EA5E9;box-shadow:0 0 0 3px rgba(14,165,233,.12)}
.f-error{font-size:11px;color:#EF4444;margin-top:4px;display:none}
.f-toggle{display:flex;align-items:center;gap:10px;cursor:pointer}
.f-toggle input{width:38px;height:20px;appearance:none;background:#E4E9F0;border-radius:99px;cursor:pointer;transition:background .2s;position:relative;flex-shrink:0}
.f-toggle input:checked{background:#10B981}
.f-toggle input::after{content:'';position:absolute;top:3px;left:3px;width:14px;height:14px;background:#fff;border-radius:50%;transition:left .2s;box-shadow:0 1px 4px rgba(0,0,0,.2)}
.f-toggle input:checked::after{left:21px}
.btn-icon-edit{width:30px;height:30px;border-radius:8px;border:1.5px solid #E0F2FE;background:#F0F9FF;color:#0284C7;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .2s}
.btn-icon-edit:hover{background:#0EA5E9;color:#fff;border-color:#0EA5E9}
.btn-icon-del{width:30px;height:30px;border-radius:8px;border:1.5px solid #FEE2E2;background:#FFF5F5;color:#EF4444;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .2s}
.btn-icon-del:hover{background:#EF4444;color:#fff;border-color:#EF4444}
.toast-msg{position:fixed;top:20px;right:20px;z-index:999;padding:12px 20px;border-radius:12px;font-size:13px;font-weight:600;color:#fff;box-shadow:0 6px 20px rgba(0,0,0,.15);transform:translateX(130%);transition:transform .35s cubic-bezier(.34,1.56,.64,1)}
.toast-msg.show{transform:translateX(0)}
.toast-msg.success{background:#10B981}.toast-msg.error{background:#EF4444}
.filter-bar{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:16px;align-items:center}
.f-select{padding:8px 14px;border:1.5px solid #E4E9F0;border-radius:10px;font-size:12px;font-weight:600;color:#1A202C;background:#fff;outline:none;cursor:pointer}
.f-select:focus{border-color:#0EA5E9}
</style>


<div style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:22px">
    <div>
        <div style="font-size:12px;color:#64748B;margin-bottom:4px;display:flex;gap:5px;align-items:center">
            <span class="material-symbols-outlined" style="font-size:14px">home</span>
            <span>Writer</span>
            <span class="material-symbols-outlined" style="font-size:12px">chevron_right</span>
            <span style="color:#0EA5E9;font-weight:700">Courses</span>
        </div>
        <h1 style="font-size:26px;font-weight:800;color:#1A202C;margin:0">Courses</h1>
        <p style="font-size:13px;color:#64748B;margin:2px 0 0">Manage academic courses — set duration, unit type, and department.</p>
    </div>
    <button onclick="openAdd()" class="btn-primary" style="display:flex;align-items:center;gap:6px">
        <span class="material-symbols-outlined" style="font-size:18px">add</span> Add Course
    </button>
</div>


<div class="filter-bar">
    <select id="filter-dept" class="f-select" onchange="if(dt) dt.ajax.reload()">
        <option value="">All Departments</option>
        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($d->id); ?>"><?php echo e($d->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>


<div class="card">
    <div style="overflow-x:auto">
        <table id="courses-table" class="w-full" style="width:100%">
            <thead>
                <tr style="border-bottom:2px solid #F0F4F8">
                    <th class="dt-th">#</th>
                    <th class="dt-th">Course Name</th>
                    <th class="dt-th">Code</th>
                    <th class="dt-th">Department</th>
                    <th class="dt-th">Duration / Units</th>
                    <th class="dt-th">Status</th>
                    <th class="dt-th text-center">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>


<div id="modal" style="display:none" class="md-overlay" onclick="if(event.target===this)closeModal()">
    <div class="md-box">
        <div class="md-head">
            <h3 class="md-title" id="modal-title">Add Course</h3>
            <button onclick="closeModal()" style="border:none;background:none;cursor:pointer;font-size:22px;color:#64748B;line-height:1">×</button>
        </div>
        <div class="md-body">
            <form onsubmit="submitForm(event)">
                <input type="hidden" id="rec-id">
                <div style="margin-bottom:14px">
                    <label class="f-label">Department *</label>
                    <select id="f-dept" class="f-input">
                        <option value="">— Select Department —</option>
                        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($d->id); ?>"><?php echo e($d->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p class="f-error" id="err-department-id"></p>
                </div>
                <div class="md-grid" style="margin-bottom:14px">
                    <div class="f-group">
                        <label class="f-label">Course Name *</label>
                        <input type="text" id="f-name" class="f-input" placeholder="e.g. Master of Computer Applications">
                        <p class="f-error" id="err-name"></p>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Course Code *</label>
                        <input type="text" id="f-code" class="f-input" placeholder="e.g. MCA" style="text-transform:uppercase">
                        <p class="f-error" id="err-code"></p>
                    </div>
                </div>
                <div class="md-grid" style="margin-bottom:14px">
                    <div class="f-group">
                        <label class="f-label">Duration (Years) *</label>
                        <input type="number" id="f-duration" class="f-input" min="1" max="6" placeholder="e.g. 2">
                        <p class="f-error" id="err-duration-years"></p>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Unit Type *</label>
                        <select id="f-unit-type" class="f-input" onchange="unitTypeChanged()">
                            <option value="semester">Semester</option>
                            <option value="year">Year</option>
                        </select>
                    </div>
                </div>
                <div style="margin-bottom:14px">
                    <label class="f-label">Total Units (<span id="unit-label">Semesters</span>) *</label>
                    <input type="number" id="f-total-units" class="f-input" min="1" max="12" placeholder="e.g. 4">
                    <p class="f-error" id="err-total-units"></p>
                </div>
                <div style="margin-bottom:14px">
                    <label class="f-label">Description</label>
                    <textarea id="f-desc" class="f-input" rows="2" placeholder="Optional course description"></textarea>
                </div>
                <div style="margin-bottom:18px">
                    <label class="f-toggle">
                        <input type="checkbox" id="f-active" checked>
                        <span style="font-size:13px;font-weight:600;color:#1A202C">Active</span>
                    </label>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end">
                    <button type="button" onclick="closeModal()" style="padding:10px 22px;border-radius:12px;border:1.5px solid #E4E9F0;background:#F5F7FA;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                    <button type="submit" class="btn-primary" style="padding:10px 28px">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="del-modal" style="display:none" class="md-overlay" onclick="if(event.target===this)closeDelModal()">
    <div class="md-box" style="max-width:380px">
        <div class="md-head">
            <h3 class="md-title" style="color:#EF4444">⚠️ Confirm Delete</h3>
            <button onclick="closeDelModal()" style="border:none;background:none;cursor:pointer;font-size:22px;color:#64748B">×</button>
        </div>
        <div class="md-body">
            <p style="font-size:13px;color:#64748B;margin:0 0 20px">Delete <strong id="del-name"></strong>? This cannot be undone.</p>
            <div style="display:flex;gap:10px;justify-content:flex-end">
                <button onclick="closeDelModal()" style="padding:10px 22px;border-radius:12px;border:1.5px solid #E4E9F0;background:#F5F7FA;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                <button onclick="confirmDelete()" style="padding:10px 22px;border-radius:12px;border:none;background:#EF4444;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Delete</button>
            </div>
        </div>
    </div>
</div>

<div id="toast" class="toast-msg"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
let deleteId = null;
let dt;

dt = $('#courses-table').DataTable({
    processing: true, serverSide: true,
    ajax: {
        url: '<?php echo e(route("writer.master.courses.datatable")); ?>',
        data: d => { d.department_id = $('#filter-dept').val(); }
    },
    columns: [
        { data: 'DT_RowIndex',     name: 'id',              searchable: false, width: '50px' },
        { data: 'name',            name: 'name' },
        { data: 'code',            name: 'code',             width: '80px' },
        { data: 'department_name', name: 'department_name',  orderable: false },
        { data: 'unit_label',      name: 'unit_label',      orderable: false },
        { data: 'status_badge',    name: 'status_badge',    orderable: false, searchable: false, width: '90px' },
        { data: 'action',          name: 'action',           orderable: false, searchable: false, width: '90px', className: 'text-center' },
    ],
    pageLength: 15,
    language: { search: '', searchPlaceholder: 'Search courses...' },
    dom: '<"dt-toolbar"f>t<"dt-bottombar"ip>',
});

function unitTypeChanged() {
    const v = document.getElementById('f-unit-type').value;
    document.getElementById('unit-label').textContent = v === 'semester' ? 'Semesters' : 'Years';
}

function openAdd() {
    document.getElementById('modal-title').textContent = 'Add Course';
    document.getElementById('rec-id').value = '';
    ['f-dept','f-name','f-code','f-desc'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('f-duration').value = '';
    document.getElementById('f-total-units').value = '';
    document.getElementById('f-unit-type').value = 'semester';
    document.getElementById('f-active').checked = true;
    unitTypeChanged();
    clearErrors();
    document.getElementById('modal').style.display = 'flex';
}

async function editCourse(id) {
    const res = await fetch(`<?php echo e(url('writer/master/courses')); ?>/${id}`);
    const d   = await res.json();
    document.getElementById('modal-title').textContent = 'Edit Course';
    document.getElementById('rec-id').value          = d.id;
    document.getElementById('f-dept').value          = d.department_id;
    document.getElementById('f-name').value          = d.name;
    document.getElementById('f-code').value          = d.code;
    document.getElementById('f-duration').value      = d.duration_years;
    document.getElementById('f-unit-type').value     = d.unit_type;
    document.getElementById('f-total-units').value   = d.total_units;
    document.getElementById('f-desc').value          = d.description ?? '';
    document.getElementById('f-active').checked      = d.is_active;
    unitTypeChanged();
    clearErrors();
    document.getElementById('modal').style.display = 'flex';
}

function closeModal() { document.getElementById('modal').style.display = 'none'; }

async function submitForm(e) {
    e.preventDefault(); clearErrors();
    const id = document.getElementById('rec-id').value;
    const url    = id ? `<?php echo e(url('writer/master/courses')); ?>/${id}` : '<?php echo e(route("writer.master.courses.store")); ?>';
    const method = id ? 'PUT' : 'POST';
    const res = await fetch(url, {
        method, headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>' },
        body: JSON.stringify({
            department_id:  document.getElementById('f-dept').value,
            name:           document.getElementById('f-name').value,
            code:           document.getElementById('f-code').value,
            duration_years: document.getElementById('f-duration').value,
            unit_type:      document.getElementById('f-unit-type').value,
            total_units:    document.getElementById('f-total-units').value,
            description:    document.getElementById('f-desc').value,
            is_active:      document.getElementById('f-active').checked,
        })
    });
    const data = await res.json();
    if (!res.ok) {
        if (data.errors) Object.entries(data.errors).forEach(([k,v]) => showError('err-'+k.replaceAll('_','-'), v[0]));
        else showToast(data.message || 'Error.', 'error');
        return;
    }
    showToast(data.message, 'success'); closeModal(); if(dt) dt.ajax.reload(null, false); else location.reload();
}

function deleteCourse(id, name) { deleteId=id; document.getElementById('del-name').textContent=name; document.getElementById('del-modal').style.display='flex'; }
function closeDelModal() { document.getElementById('del-modal').style.display='none'; deleteId=null; }
async function confirmDelete() {
    const res = await fetch(`<?php echo e(url('writer/master/courses')); ?>/${deleteId}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'} });
    const data = await res.json();
    res.ok ? showToast(data.message,'success') : showToast(data.message||'Delete failed.','error');
    if(dt) dt.ajax.reload(null,false); else location.reload(); closeDelModal();
}
function clearErrors() { document.querySelectorAll('.f-error').forEach(e=>{e.style.display='none';e.textContent='';}); }
function showError(id,msg){ const el=document.getElementById(id); if(el){el.textContent=msg;el.style.display='block';} }
function showToast(msg,type='success'){ const t=document.getElementById('toast'); t.textContent=msg; t.className=`toast-msg ${type} show`; setTimeout(()=>t.classList.remove('show'),3000); }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views\writer\master\courses\index.blade.php ENDPATH**/ ?>