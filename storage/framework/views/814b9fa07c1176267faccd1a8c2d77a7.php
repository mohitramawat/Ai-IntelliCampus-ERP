<?php $__env->startSection('content'); ?>

<style>
.md-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);z-index:200;display:flex;align-items:center;justify-content:center;padding:16px}
.md-box{background:#fff;border-radius:20px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.18);overflow:hidden;animation:mdIn .25s ease}
@keyframes mdIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.md-head{padding:20px 24px 16px;border-bottom:1px solid #E4E9F0;display:flex;align-items:center;justify-content:space-between}
.md-title{font-size:17px;font-weight:800;color:#1A202C;margin:0}
.md-body{padding:24px}
.f-group{margin-bottom:18px}
.f-label{display:block;font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px}
.f-input{width:100%;padding:10px 14px;border:1.5px solid #E4E9F0;border-radius:12px;font-size:13px;font-weight:500;color:#1A202C;transition:border-color .2s,box-shadow .2s;outline:none}
.f-input:focus{border-color:#0EA5E9;box-shadow:0 0 0 3px rgba(14,165,233,.12)}
.f-error{font-size:11px;color:#EF4444;margin-top:4px;display:none}
.f-toggle{display:flex;align-items:center;gap:10px;cursor:pointer}
.f-toggle input{width:38px;height:20px;appearance:none;background:#E4E9F0;border-radius:99px;cursor:pointer;transition:background .2s;position:relative}
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
            <span style="color:#0EA5E9;font-weight:700">Departments</span>
        </div>
        <h1 style="font-size:26px;font-weight:800;color:#1A202C;margin:0">Departments</h1>
        <p style="font-size:13px;color:#64748B;margin:2px 0 0">Manage all academic departments of the campus.</p>
    </div>
    <button onclick="openAdd()" class="btn-primary" style="display:flex;align-items:center;gap:6px">
        <span class="material-symbols-outlined" style="font-size:18px">add</span>
        Add Department
    </button>
</div>


<div class="card">
    <div style="overflow-x:auto">
        <table id="depts-table" class="w-full" style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="border-bottom:2px solid #F0F4F8">
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#64748B;text-align:left;text-transform:uppercase">#</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#64748B;text-align:left;text-transform:uppercase">Name</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#64748B;text-align:left;text-transform:uppercase">Code</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#64748B;text-align:left;text-transform:uppercase">Campus</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#64748B;text-align:left;text-transform:uppercase">Status</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#64748B;text-align:center;text-transform:uppercase">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>


<div id="modal" style="display:none" class="md-overlay" onclick="if(event.target===this)closeModal()">
    <div class="md-box">
        <div class="md-head">
            <h3 class="md-title" id="modal-title">Add Department</h3>
            <button onclick="closeModal()" style="border:none;background:none;cursor:pointer;font-size:20px;color:#64748B;line-height:1">×</button>
        </div>
        <div class="md-body">
            <form id="dept-form" onsubmit="submitForm(event)">
                <input type="hidden" id="dept-id">
                <div class="f-group">
                    <label class="f-label">Department Name *</label>
                    <input type="text" id="f-name" class="f-input" placeholder="e.g. Computer Science & IT">
                    <p class="f-error" id="err-name"></p>
                </div>
                <div class="f-group">
                    <label class="f-label">Department Code *</label>
                    <input type="text" id="f-code" class="f-input" placeholder="e.g. CSIT" style="text-transform:uppercase">
                    <p class="f-error" id="err-code"></p>
                </div>
                <div class="f-group">
                    <label class="f-toggle">
                        <input type="checkbox" id="f-active" checked>
                        <span style="font-size:13px;font-weight:600;color:#1A202C">Active</span>
                    </label>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
                    <button type="button" onclick="closeModal()" style="padding:10px 22px;border-radius:12px;border:1.5px solid #E4E9F0;background:#F5F7FA;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                    <button type="submit" id="save-btn" class="btn-primary" style="padding:10px 28px">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="del-modal" style="display:none" class="md-overlay" onclick="if(event.target===this)closeDelModal()">
    <div class="md-box" style="max-width:380px">
        <div class="md-head">
            <h3 class="md-title" style="color:#EF4444">⚠️ Confirm Delete</h3>
            <button onclick="closeDelModal()" style="border:none;background:none;cursor:pointer;font-size:20px;color:#64748B">×</button>
        </div>
        <div class="md-body">
            <p style="font-size:13px;color:#64748B;margin:0 0 20px">Are you sure you want to delete <strong id="del-name"></strong>? This cannot be undone.</p>
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

dt = $('#depts-table').DataTable({
    processing: true, serverSide: true,
    ajax: '<?php echo e(route("writer.master.departments.datatable")); ?>',
    columns: [
        { data: 'DT_RowIndex', name: 'id', searchable: false, width: '50px' },
        { data: 'name',        name: 'name' },
        { data: 'code',        name: 'code', width: '100px' },
        { data: 'campus_name', name: 'campus_name', orderable: false },
        { data: 'status_badge',name: 'status_badge', orderable: false, searchable: false, width: '90px' },
        { data: 'action',      name: 'action', orderable: false, searchable: false, width: '90px', className: 'text-center' },
    ],
    pageLength: 15,
    language: { search: '', searchPlaceholder: 'Search departments...', processing: '<span style="color:#0EA5E9">Loading...</span>' },
    dom: '<"dt-toolbar"f>t<"dt-bottombar"ip>',
});

function openAdd() {
    document.getElementById('modal-title').textContent = 'Add Department';
    document.getElementById('dept-id').value = '';
    document.getElementById('f-name').value = '';
    document.getElementById('f-code').value = '';
    document.getElementById('f-active').checked = true;
    clearErrors();
    document.getElementById('modal').style.display = 'flex';
}

async function editDept(id) {
    const res = await fetch(`<?php echo e(url('writer/master/departments')); ?>/${id}`);
    const d   = await res.json();
    document.getElementById('modal-title').textContent = 'Edit Department';
    document.getElementById('dept-id').value  = d.id;
    document.getElementById('f-name').value   = d.name;
    document.getElementById('f-code').value   = d.code;
    document.getElementById('f-active').checked = d.is_active;
    clearErrors();
    document.getElementById('modal').style.display = 'flex';
}

function closeModal() { document.getElementById('modal').style.display = 'none'; }

async function submitForm(e) {
    e.preventDefault();
    clearErrors();
    
    const btn = document.getElementById('save-btn');
    const id  = document.getElementById('dept-id').value;
    const originalText = btn.innerHTML;

    try {
        btn.disabled = true;
        btn.innerHTML = '<div style="display:flex;align-items:center;gap:8px justify-content:center"><span class="material-symbols-outlined animate-spin" style="font-size:16px">sync</span> Saving...</div>';

        const method = id ? 'PUT' : 'POST';
        const url    = id ? `<?php echo e(url('writer/master/departments')); ?>/${id}` : '<?php echo e(route("writer.master.departments.store")); ?>';

        const res = await fetch(url, {
            method,
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' 
            },
            body: JSON.stringify({
                name:      document.getElementById('f-name').value,
                code:      document.getElementById('f-code').value,
                is_active: document.getElementById('f-active').checked,
            })
        });
        
        const contentType = res.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            const text = await res.text();
            console.error("Server Error Response:", text);
            throw new Error("Server returned HTML instead of JSON. Check your routes or session.");
        }

        const data = await res.json();

        if (!res.ok) {
            if (data.errors) {
                Object.entries(data.errors).forEach(([k, v]) => showError('err-' + k.replace('_','-'), v[0]));
            } else {
                showToast(data.message || 'Error occurred while saving.', 'error');
            }
            btn.disabled = false;
            btn.innerHTML = originalText;
            return;
        }

        showToast(data.message, 'success');
        closeModal();
        if (dt) dt.ajax.reload(null, false); else location.reload();
    } catch (err) {
        console.error(err);
        showToast('Something went wrong. Please try again.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

function deleteDept(id, name) {
    deleteId = id;
    document.getElementById('del-name').textContent = name;
    document.getElementById('del-modal').style.display = 'flex';
}
function closeDelModal() { document.getElementById('del-modal').style.display = 'none'; deleteId = null; }

async function confirmDelete() {
    const res  = await fetch(`<?php echo e(url('writer/master/departments')); ?>/${deleteId}`, {
        method: 'DELETE', headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
    });
    const data = await res.json();
    if (res.ok) { showToast(data.message, 'success'); if (dt) dt.ajax.reload(null, false); else location.reload(); }
    else showToast(data.message || 'Delete failed.', 'error');
    closeDelModal();
}

function clearErrors() { document.querySelectorAll('.f-error').forEach(e => { e.style.display='none'; e.textContent=''; }); }
function showError(id, msg) { const el = document.getElementById(id); if(el){ el.textContent=msg; el.style.display='block'; } }
function showToast(msg, type='success') {
    const t = document.getElementById('toast');
    t.textContent = msg; t.className = `toast-msg ${type} show`;
    setTimeout(() => t.classList.remove('show'), 3000);
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views\writer\master\departments\index.blade.php ENDPATH**/ ?>