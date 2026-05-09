<?php $__env->startSection('content'); ?>

<style>
.md-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);z-index:200;display:flex;align-items:center;justify-content:center;padding:16px}
.md-box{background:#fff;border-radius:20px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.18);overflow:hidden;animation:mdIn .25s ease}
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
</style>


<div style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:22px">
    <div>
        <div style="font-size:12px;color:#64748B;margin-bottom:4px;display:flex;gap:5px;align-items:center">
            <span class="material-symbols-outlined" style="font-size:14px">home</span>
            <span>Writer</span>
            <span class="material-symbols-outlined" style="font-size:12px">chevron_right</span>
            <span style="color:#0EA5E9;font-weight:700">Batches</span>
        </div>
        <h1 style="font-size:26px;font-weight:800;color:#1A202C;margin:0">Batches</h1>
        <p style="font-size:13px;color:#64748B;margin:2px 0 0">Manage student batches — assign course, year range and status.</p>
    </div>
    <button onclick="openAdd()" class="btn-primary" style="display:flex;align-items:center;gap:6px">
        <span class="material-symbols-outlined" style="font-size:18px">add</span> Add Batch
    </button>
</div>


<div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:16px">
    <select id="filter-course" class="f-input" style="width:auto;min-width:200px" onchange="if(dt) dt.ajax.reload()">
        <option value="">All Courses</option>
        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>"><?php echo e($c->code); ?> — <?php echo e($c->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <select id="filter-status" class="f-input" style="width:auto;min-width:140px" onchange="if(dt) dt.ajax.reload()">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="completed">Completed</option>
        <option value="inactive">Inactive</option>
    </select>
</div>


<div class="card">
    <div style="overflow-x:auto">
        <table id="batches-table" class="w-full" style="width:100%">
            <thead>
                <tr style="border-bottom:2px solid #F0F4F8">
                    <th class="dt-th">#</th>
                    <th class="dt-th">Batch Name</th>
                    <th class="dt-th">Course</th>
                    <th class="dt-th">Year Range</th>
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
            <h3 class="md-title" id="modal-title">Add Batch</h3>
            <button onclick="closeModal()" style="border:none;background:none;cursor:pointer;font-size:22px;color:#64748B;line-height:1">×</button>
        </div>
        <div class="md-body">
            <form onsubmit="submitForm(event)">
                <input type="hidden" id="rec-id">
                <div style="margin-bottom:14px">
                    <label class="f-label">Course *</label>
                    <select id="f-course" class="f-input" onchange="autoBatchName()">
                        <option value="">— Select Course —</option>
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>" data-code="<?php echo e($c->code); ?>"><?php echo e($c->code); ?> — <?php echo e($c->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p class="f-error" id="err-course-id"></p>
                </div>
                <div style="margin-bottom:14px">
                    <label class="f-label">Batch Name *</label>
                    <input type="text" id="f-name" class="f-input" placeholder="e.g. MCA 2024-2026">
                    <p class="f-error" id="err-name"></p>
                </div>
                <div class="md-grid" style="margin-bottom:14px">
                    <div class="f-group">
                        <label class="f-label">Start Year *</label>
                        <input type="number" id="f-start" class="f-input" min="2000" max="2099" placeholder="e.g. 2024" oninput="autoBatchName()">
                        <p class="f-error" id="err-start-year"></p>
                    </div>
                    <div class="f-group">
                        <label class="f-label">End Year *</label>
                        <input type="number" id="f-end" class="f-input" min="2000" max="2099" placeholder="e.g. 2026" oninput="autoBatchName()">
                        <p class="f-error" id="err-end-year"></p>
                    </div>
                </div>
                <div style="margin-bottom:18px">
                    <label class="f-label">Status *</label>
                    <select id="f-status" class="f-input">
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <p class="f-error" id="err-status"></p>
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
            <p style="font-size:13px;color:#64748B;margin:0 0 20px">Delete batch <strong id="del-name"></strong>? This cannot be undone.</p>
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

dt = $('#batches-table').DataTable({
    processing: true, serverSide: true,
    ajax: {
        url: '<?php echo e(route("writer.master.batches.datatable")); ?>',
        data: d => {
            d.course_id = $('#filter-course').val();
            d.status    = $('#filter-status').val();
        }
    },
    columns: [
        { data: 'DT_RowIndex', name: 'id',          searchable: false, width: '50px' },
        { data: 'name',        name: 'name' },
        { data: 'course_name', name: 'course_name', orderable: false },
        { data: 'year_range',  name: 'year_range',  orderable: false },
        { data: 'status_badge',name: 'status_badge',orderable: false, searchable: false, width: '110px' },
        { data: 'action',      name: 'action',       orderable: false, searchable: false, width: '90px', className: 'text-center' },
    ],
    pageLength: 15,
    language: { search: '', searchPlaceholder: 'Search batches...' },
    dom: '<"dt-toolbar"f>t<"dt-bottombar"ip>',
});

// Auto-fill batch name from course code + years
function autoBatchName() {
    const sel   = document.getElementById('f-course');
    const code  = sel.options[sel.selectedIndex]?.dataset.code || '';
    const start = document.getElementById('f-start').value;
    const end   = document.getElementById('f-end').value;
    if (code && start && end) {
        document.getElementById('f-name').value = `${code} ${start}-${end}`;
    }
}

function openAdd() {
    document.getElementById('modal-title').textContent = 'Add Batch';
    document.getElementById('rec-id').value = '';
    document.getElementById('f-course').value = '';
    document.getElementById('f-name').value = '';
    document.getElementById('f-start').value = new Date().getFullYear();
    document.getElementById('f-end').value = '';
    document.getElementById('f-status').value = 'active';
    clearErrors();
    document.getElementById('modal').style.display = 'flex';
}

async function editBatch(id) {
    const res = await fetch(`<?php echo e(url('writer/master/batches')); ?>/${id}`);
    const d   = await res.json();
    document.getElementById('modal-title').textContent = 'Edit Batch';
    document.getElementById('rec-id').value    = d.id;
    document.getElementById('f-course').value  = d.course_id;
    document.getElementById('f-name').value    = d.name;
    document.getElementById('f-start').value   = d.start_year;
    document.getElementById('f-end').value     = d.end_year;
    document.getElementById('f-status').value  = d.status;
    clearErrors();
    document.getElementById('modal').style.display = 'flex';
}

function closeModal() { document.getElementById('modal').style.display = 'none'; }

async function submitForm(e) {
    e.preventDefault(); clearErrors();
    const id     = document.getElementById('rec-id').value;
    const url    = id ? `<?php echo e(url('writer/master/batches')); ?>/${id}` : '<?php echo e(route("writer.master.batches.store")); ?>';
    const method = id ? 'PUT' : 'POST';
    const res = await fetch(url, {
        method, headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify({
            course_id:  document.getElementById('f-course').value,
            name:       document.getElementById('f-name').value,
            start_year: document.getElementById('f-start').value,
            end_year:   document.getElementById('f-end').value,
            status:     document.getElementById('f-status').value,
            is_active:  document.getElementById('f-status').value === 'active',
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

function deleteBatch(id, name) { deleteId=id; document.getElementById('del-name').textContent=name; document.getElementById('del-modal').style.display='flex'; }
function closeDelModal() { document.getElementById('del-modal').style.display='none'; deleteId=null; }
async function confirmDelete() {
    const res = await fetch(`<?php echo e(url('writer/master/batches')); ?>/${deleteId}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'} });
    const data = await res.json();
    res.ok ? showToast(data.message,'success') : showToast(data.message||'Delete failed.','error');
    if(dt) dt.ajax.reload(null,false); else location.reload(); closeDelModal();
}
function clearErrors(){ document.querySelectorAll('.f-error').forEach(e=>{e.style.display='none';e.textContent='';}); }
function showError(id,msg){ const el=document.getElementById(id); if(el){el.textContent=msg;el.style.display='block';} }
function showToast(msg,type='success'){ const t=document.getElementById('toast'); t.textContent=msg; t.className=`toast-msg ${type} show`; setTimeout(()=>t.classList.remove('show'),3000); }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/writer/master/batches/index.blade.php ENDPATH**/ ?>