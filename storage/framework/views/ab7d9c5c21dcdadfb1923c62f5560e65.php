<?php $__env->startSection('content'); ?>

<style>
.md-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);z-index:200;display:flex;align-items:center;justify-content:center;padding:16px}
.md-box{background:#fff;border-radius:20px;width:100%;max-width:600px;box-shadow:0 20px 60px rgba(0,0,0,.18);overflow:hidden;animation:mdIn .25s ease;max-height:95vh;display:flex;flex-direction:column}
@keyframes mdIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.md-head{padding:20px 24px 16px;border-bottom:1px solid #E4E9F0;display:flex;align-items:center;justify-content:space-between;flex-shrink:0}
.md-title{font-size:17px;font-weight:800;color:#1A202C;margin:0}
.md-body{padding:24px;overflow-y:auto}
.md-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.f-label{display:block;font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px}
.f-input{width:100%;padding:10px 14px;border:1.5px solid #E4E9F0;border-radius:12px;font-size:13px;font-weight:500;color:#1A202C;transition:border-color .2s,box-shadow .2s;outline:none;box-sizing:border-box}
.f-input:focus{border-color:#0EA5E9;box-shadow:0 0 0 3px rgba(14,165,233,.12)}
.f-error{font-size:11px;color:#EF4444;margin-top:4px;display:none}
.btn-icon-edit{width:30px;height:30px;border-radius:8px;border:1.5px solid #E0F2FE;background:#F0F9FF;color:#0284C7;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:all .2s;text-decoration:none}
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
            <span style="color:#0EA5E9;font-weight:700">Teachers</span>
        </div>
        <h1 style="font-size:26px;font-weight:800;color:#1A202C;margin:0">Teachers</h1>
        <p style="font-size:13px;color:#64748B;margin:2px 0 0">Complete faculty registry — manage profiles and login access.</p>
    </div>
    <button onclick="openAdd()" class="btn-primary" style="display:flex;align-items:center;gap:6px">
        <span class="material-symbols-outlined" style="font-size:18px">person_add</span> Add Teacher
    </button>
</div>


<div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:16px;align-items:center">
    <select id="filter-dept" class="f-input" style="width:auto;min-width:220px" onchange="if(dt) dt.ajax.reload()">
        <option value="">All Departments</option>
        <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($d->id); ?>"><?php echo e($d->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>


<div class="card">
    <div style="overflow-x:auto">
        <table id="teachers-table" class="w-full" style="width:100%">
            <thead>
                <tr style="border-bottom:2px solid #F0F4F8">
                    <th class="dt-th">#</th>
                    <th class="dt-th">Teacher Name</th>
                    <th class="dt-th">Employee Code</th>
                    <th class="dt-th">Department</th>
                    <th class="dt-th">Email Address</th>
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
            <h3 class="md-title" id="modal-title">New Teacher Profile</h3>
            <button onclick="closeModal()" style="border:none;background:none;cursor:pointer;font-size:22px;color:#64748B;line-height:1">×</button>
        </div>
        <div class="md-body">
            <form onsubmit="submitForm(event)">
                <input type="hidden" id="rec-id">
                
                <div class="md-grid" style="margin-bottom:14px">
                    <div>
                        <label class="f-label">Full Name *</label>
                        <input type="text" id="f-name" class="f-input" placeholder="e.g. Dr. Ramesh Kumar">
                        <p class="f-error" id="err-name"></p>
                    </div>
                    <div>
                        <label class="f-label">Email Address *</label>
                        <input type="email" id="f-email" class="f-input" placeholder="e.g. ramesh@college.com">
                        <p class="f-error" id="err-email"></p>
                    </div>
                </div>

                <div class="md-grid" style="margin-bottom:14px">
                    <div>
                        <label class="f-label">Department *</label>
                        <select id="f-dept" class="f-input">
                            <option value="">— Select Dept —</option>
                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($d->id); ?>"><?php echo e($d->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <p class="f-error" id="err-department-id"></p>
                    </div>
                    <div>
                        <label class="f-label">Employee Code *</label>
                        <input type="text" id="f-code" class="f-input" placeholder="e.g. TCH001" style="text-transform:uppercase">
                        <p class="f-error" id="err-employee-code"></p>
                    </div>
                </div>

                <div class="md-grid" style="margin-bottom:14px">
                    <div>
                        <label class="f-label">Gender *</label>
                        <select id="f-gender" class="f-input">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="f-label">Phone Number</label>
                        <input type="text" id="f-phone" class="f-input" placeholder="e.g. 9876543210">
                        <p class="f-error" id="err-phone-number"></p>
                    </div>
                </div>

                <div class="md-grid" style="margin-bottom:14px">
                    <div>
                        <label class="f-label">Experience (Years)</label>
                        <input type="number" id="f-exp" class="f-input" min="0" placeholder="e.g. 5">
                        <p class="f-error" id="err-experience-years"></p>
                    </div>
                    <div>
                        <label class="f-label">Joining Date</label>
                        <input type="date" id="f-joining" class="f-input" value="<?php echo e(date('Y-m-d')); ?>">
                        <p class="f-error" id="err-joining-date"></p>
                    </div>
                </div>

                <div style="margin-bottom:14px">
                    <label class="f-label">Qualification</label>
                    <input type="text" id="f-qual" class="f-input" placeholder="e.g. M.Tech, PhD">
                    <p class="f-error" id="err-qualification"></p>
                </div>

                <div style="margin-bottom:18px">
                    <label class="f-label">Employment Status *</label>
                    <select id="f-status" class="f-input">
                        <option value="active">Active</option>
                        <option value="on_leave">On Leave</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:10px">
                    <button type="button" onclick="closeModal()" style="padding:10px 22px;border-radius:12px;border:1.5px solid #E4E9F0;background:#F5F7FA;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                    <button type="submit" class="btn-primary" style="padding:10px 28px">Save Teacher</button>
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
            <p style="font-size:13px;color:#64748B;margin:0 0 20px">Remove <strong id="del-name"></strong>? This will also delete their login account.</p>
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

dt = $('#teachers-table').DataTable({
    processing: true, serverSide: true,
    ajax: {
        url: '<?php echo e(route("writer.teachers.datatable")); ?>',
        data: d => { d.department_id = $('#filter-dept').val(); }
    },
    columns: [
        { data: 'DT_RowIndex', name: 'id', searchable: false, width: '40px' },
        { data: 'name',        name: 'name' },
        { data: 'employee_code', name: 'employee_code', width: '90px' },
        { data: 'dept_name',   name: 'dept_name', orderable: false },
        { data: 'email',       name: 'email' },
        { data: 'status_badge', name: 'status', orderable: false, searchable: false, width: '90px' },
        { data: 'action',      name: 'action', orderable: false, searchable: false, width: '90px', className: 'text-center' },
    ],
    pageLength: 15,
    language: { search: '', searchPlaceholder: 'Search teachers...', processing: '<span style="color:#0EA5E9">Loading...</span>' },
    dom: '<"dt-toolbar"f>t<"dt-bottombar"ip>',
});

function openAdd() {
    document.getElementById('modal-title').textContent = 'New Teacher Profile';
    document.getElementById('rec-id').value = '';
    ['f-name','f-email','f-dept','f-code','f-phone','f-qual','f-exp'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('f-gender').value = 'male';
    document.getElementById('f-status').value = 'active';
    document.getElementById('f-joining').value = new Date().toISOString().split('T')[0];
    clearErrors();
    document.getElementById('modal').style.display = 'flex';
}

async function editTeacher(id) {
    const res = await fetch(`<?php echo e(url('writer/teachers')); ?>/${id}`, { headers: { 'Accept': 'application/json' } });
    const d = await res.json();
    document.getElementById('modal-title').textContent = 'Update Teacher Profile';
    document.getElementById('rec-id').value = d.id;
    document.getElementById('f-name').value = d.user.name;
    document.getElementById('f-email').value = d.user.email;
    document.getElementById('f-dept').value = d.department_id;
    document.getElementById('f-code').value = d.employee_code;
    document.getElementById('f-gender').value = d.gender;
    document.getElementById('f-phone').value = d.phone_number ?? '';
    document.getElementById('f-qual').value = d.qualification ?? '';
    document.getElementById('f-exp').value = d.experience_years;
    document.getElementById('f-joining').value = d.joining_date.split('T')[0];
    document.getElementById('f-status').value = d.status;
    clearErrors();
    document.getElementById('modal').style.display = 'flex';
}

function closeModal() { document.getElementById('modal').style.display = 'none'; }

async function submitForm(e) {
    e.preventDefault(); 
    clearErrors();
    const id = document.getElementById('rec-id').value;
    const url = id ? `<?php echo e(url('writer/teachers')); ?>/${id}` : '<?php echo e(route("writer.teachers.store")); ?>';
    const method = id ? 'PUT' : 'POST';
    
    const res = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
        body: JSON.stringify({
            name:          document.getElementById('f-name').value,
            email:         document.getElementById('f-email').value,
            department_id: document.getElementById('f-dept').value,
            employee_code: document.getElementById('f-code').value,
            gender:        document.getElementById('f-gender').value,
            phone_number:  document.getElementById('f-phone').value,
            qualification: document.getElementById('f-qual').value,
            experience_years: document.getElementById('f-exp').value,
            joining_date:  document.getElementById('f-joining').value,
            status:        document.getElementById('f-status').value,
        })
    });

    const data = await res.json();
    if (!res.ok) {
        if (data.errors) Object.entries(data.errors).forEach(([k,v]) => showError('err-'+k.replaceAll('_','-'), v[0]));
        else showToast(data.message || 'Error occurred', 'error');
        return;
    }

    showToast(data.message, 'success');
    closeModal();
    if (dt) dt.ajax.reload(null, false); else location.reload();
}

function deleteTeacher(id, name) { deleteId=id; document.getElementById('del-name').textContent=name; document.getElementById('del-modal').style.display='flex'; }
function closeDelModal() { document.getElementById('del-modal').style.display='none'; deleteId=null; }
async function confirmDelete() {
    const res = await fetch(`<?php echo e(url('writer/teachers')); ?>/${deleteId}`, { 
        method:'DELETE', 
        headers:{'X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>', 'Accept':'application/json'} 
    });
    const data = await res.json();
    if (res.ok) {
        showToast(data.message,'success');
        if (dt) dt.ajax.reload(null, false); else location.reload();
    } else {
        showToast(data.message||'Delete failed.','error');
    }
    closeDelModal();
}

async function toggleHod(id, name, isHod) {
    const action = isHod ? 'remove' : 'assign';
    if (!confirm(`Are you sure you want to ${action} the HOD role for ${name}?`)) return;
    
    const res = await fetch(`<?php echo e(url('writer/teachers')); ?>/${id}/toggle-hod`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (res.ok) {
        showToast(data.message, 'success');
        if (dt) dt.ajax.reload(null, false);
    } else {
        showToast(data.message || 'Action failed.', 'error');
    }
}

function clearErrors(){ document.querySelectorAll('.f-error').forEach(e=>{e.style.display='none';e.textContent='';}); }
function showError(id,msg){ const el=document.getElementById(id); if(el){el.textContent=msg;el.style.display='block';} }
function showToast(msg,type='success'){ const t=document.getElementById('toast'); t.textContent=msg; t.className=`toast-msg ${type} show`; setTimeout(()=>t.classList.remove('show'),3000); }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/writer/teachers/index.blade.php ENDPATH**/ ?>