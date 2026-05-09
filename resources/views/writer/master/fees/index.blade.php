@extends('layouts.dashboard')
@section('content')

<style>
.md-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);z-index:200;display:flex;align-items:center;justify-content:center;padding:16px}
.md-box{background:#fff;border-radius:20px;width:100%;max-width:600px;box-shadow:0 20px 60px rgba(0,0,0,.18);overflow:hidden;animation:mdIn .25s ease;max-height:90vh;display:flex;flex-direction:column}
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
/* Unit fee rows */
.unit-row{display:grid;grid-template-columns:80px 1fr 130px 36px;gap:8px;align-items:center;margin-bottom:10px;padding:10px 12px;background:#F8FAFC;border:1.5px solid #E4E9F0;border-radius:12px}
.unit-row input{border:1.5px solid #E4E9F0;border-radius:8px;padding:7px 10px;font-size:12px;font-weight:600;background:#fff;outline:none;width:100%;box-sizing:border-box}
.unit-row input:focus{border-color:#0EA5E9}
.unit-del-btn{width:28px;height:28px;border-radius:8px;border:none;background:#FEE2E2;color:#EF4444;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px;line-height:1}
.unit-del-btn:hover{background:#EF4444;color:#fff}
.unit-header{display:grid;grid-template-columns:80px 1fr 130px 36px;gap:8px;padding:0 12px;margin-bottom:6px}
.unit-header span{font-size:10px;font-weight:700;color:#94A3B8;text-transform:uppercase}
.total-calc{background:linear-gradient(135deg,#EFF6FF,#E0F2FE);border-radius:12px;padding:12px 16px;margin-top:12px;display:flex;justify-content:space-between;align-items:center}
</style>

{{-- HEADER --}}
<div style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:22px">
    <div>
        <div style="font-size:12px;color:#64748B;margin-bottom:4px;display:flex;gap:5px;align-items:center">
            <span class="material-symbols-outlined" style="font-size:14px">home</span>
            <span>Writer</span>
            <span class="material-symbols-outlined" style="font-size:12px">chevron_right</span>
            <span style="color:#0EA5E9;font-weight:700">Fee Structures</span>
        </div>
        <h1 style="font-size:26px;font-weight:800;color:#1A202C;margin:0">Fee Structures</h1>
        <p style="font-size:13px;color:#64748B;margin:2px 0 0">Define course-wise semester/year fee breakdowns.</p>
    </div>
    <button onclick="openAdd()" class="btn-primary" style="display:flex;align-items:center;gap:6px">
        <span class="material-symbols-outlined" style="font-size:18px">add</span> New Fee Structure
    </button>
</div>

{{-- FILTER --}}
<div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:16px">
    <select id="filter-course" class="f-input" style="width:auto;min-width:220px" onchange="if(dt) dt.ajax.reload()">
        <option value="">All Courses</option>
        @foreach($courses as $c)
            <option value="{{ $c->id }}">{{ $c->code }} — {{ $c->name }}</option>
        @endforeach
    </select>
</div>

{{-- TABLE --}}
<div class="card">
    <div style="overflow-x:auto">
        <table id="fees-table" class="w-full" style="width:100%">
            <thead>
                <tr style="border-bottom:2px solid #F0F4F8">
                    <th class="dt-th">#</th>
                    <th class="dt-th">Course</th>
                    <th class="dt-th">Year</th>
                    <th class="dt-th">Total Fee</th>
                    <th class="dt-th">Units</th>
                    <th class="dt-th">Status</th>
                    <th class="dt-th text-center">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

{{-- CREATE MODAL (full-featured) --}}
<div id="modal" style="display:none" class="md-overlay" onclick="if(event.target===this)closeModal()">
    <div class="md-box">
        <div class="md-head">
            <h3 class="md-title">🏦 New Fee Structure</h3>
            <button onclick="closeModal()" style="border:none;background:none;cursor:pointer;font-size:22px;color:#64748B;line-height:1">×</button>
        </div>
        <div class="md-body">
            <form onsubmit="submitForm(event)" id="fee-form">

                {{-- Header info --}}
                <div class="md-grid" style="margin-bottom:14px">
                    <div>
                        <label class="f-label">Course *</label>
                        <select id="f-course" class="f-input" onchange="courseSelected()">
                            <option value="">— Select Course —</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}"
                                    data-units="{{ $c->total_units }}"
                                    data-type="{{ $c->unit_type }}">
                                    {{ $c->code }} — {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="f-error" id="err-course-id"></p>
                    </div>
                    <div>
                        <label class="f-label">Effective From Year *</label>
                        <input type="number" id="f-year" class="f-input" min="2000" max="2099" value="{{ date('Y') }}">
                        <p class="f-error" id="err-effective-from-year"></p>
                    </div>
                </div>

                <div class="md-grid" style="margin-bottom:18px">
                    <div>
                        <label class="f-label">Total Fee (₹) *</label>
                        <input type="number" id="f-total" class="f-input" min="0" placeholder="Auto-calculated" readonly
                            style="background:#F8FAFC;cursor:not-allowed">
                    </div>
                    <div>
                        <label class="f-label">Currency</label>
                        <select id="f-currency" class="f-input">
                            <option value="INR" selected>INR — Indian Rupee</option>
                            <option value="USD">USD — US Dollar</option>
                        </select>
                    </div>
                </div>

                {{-- Unit Fees --}}
                <div style="margin-bottom:8px;display:flex;align-items:center;justify-content:space-between">
                    <label class="f-label" style="margin:0">Semester / Unit Fees *</label>
                    <button type="button" onclick="addUnitRow()" style="font-size:11px;font-weight:700;color:#0EA5E9;background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:4px">
                        <span class="material-symbols-outlined" style="font-size:14px">add_circle</span> Add Row
                    </button>
                </div>

                <div id="no-course-msg" style="padding:20px;text-align:center;color:#94A3B8;font-size:12px;background:#F8FAFC;border-radius:12px;margin-bottom:12px">
                    ← Select a course first to auto-generate semester rows
                </div>

                <div id="unit-area" style="display:none">
                    <div class="unit-header">
                        <span>Unit #</span><span>Label</span><span>Fee (₹)</span><span></span>
                    </div>
                    <div id="unit-rows"></div>
                </div>

                <div class="total-calc">
                    <span style="font-size:13px;font-weight:700;color:#1E40AF">Total (Sum of Units)</span>
                    <span id="sum-display" style="font-size:18px;font-weight:800;color:#1E40AF">₹0</span>
                </div>

                <p class="f-error" id="err-units" style="margin-top:8px"></p>

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px">
                    <button type="button" onclick="closeModal()" style="padding:10px 22px;border-radius:12px;border:1.5px solid #E4E9F0;background:#F5F7FA;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                    <button type="submit" class="btn-primary" style="padding:10px 28px">💾 Save Structure</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- DELETE MODAL --}}
<div id="del-modal" style="display:none" class="md-overlay" onclick="if(event.target===this)closeDelModal()">
    <div class="md-box" style="max-width:380px">
        <div class="md-head">
            <h3 class="md-title" style="color:#EF4444">⚠️ Confirm Delete</h3>
            <button onclick="closeDelModal()" style="border:none;background:none;cursor:pointer;font-size:22px;color:#64748B">×</button>
        </div>
        <div class="md-body">
            <p style="font-size:13px;color:#64748B;margin:0 0 20px">Delete fee structure for <strong id="del-name"></strong>?<br>All unit fee records will also be removed.</p>
            <div style="display:flex;gap:10px;justify-content:flex-end">
                <button onclick="closeDelModal()" style="padding:10px 22px;border-radius:12px;border:1.5px solid #E4E9F0;background:#F5F7FA;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                <button onclick="confirmDelete()" style="padding:10px 22px;border-radius:12px;border:none;background:#EF4444;color:#fff;font-size:13px;font-weight:700;cursor:pointer">Delete</button>
            </div>
        </div>
    </div>
</div>

<div id="toast" class="toast-msg"></div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
let deleteId = null;
let dt;

dt = $('#fees-table').DataTable({
    processing: true, serverSide: true,
    ajax: {
        url: '{{ route("writer.master.fees.datatable") }}',
        data: d => { d.course_id = $('#filter-course').val(); }
    },
    columns: [
        { data: 'DT_RowIndex',    name: 'id',           searchable: false, width: '50px' },
        { data: 'course_name',    name: 'course_name',  orderable: false },
        { data: 'effective_from_year', name: 'effective_from_year', width: '80px' },
        { data: 'total_fee_fmt',  name: 'total_fee',    width: '120px' },
        { data: 'unit_count',     name: 'unit_count',   orderable: false, width: '80px' },
        { data: 'status_badge',   name: 'status_badge', orderable: false, searchable: false, width: '90px' },
        { data: 'action',         name: 'action',        orderable: false, searchable: false, width: '90px', className: 'text-center' },
    ],
    pageLength: 15,
    language: { search: '', searchPlaceholder: 'Search...', processing: '<span style="color:#0EA5E9">Loading...</span>' },
    dom: '<"dt-toolbar"f>t<"dt-bottombar"ip>',
});

// ── Course selected → auto-generate unit rows ──────────────────
function courseSelected() {
    const sel   = document.getElementById('f-course');
    const opt   = sel.options[sel.selectedIndex];
    const units = parseInt(opt?.dataset.units) || 0;
    const type  = opt?.dataset.type || 'semester';

    if (!units) {
        document.getElementById('no-course-msg').style.display = 'block';
        document.getElementById('unit-area').style.display = 'none';
        document.getElementById('unit-rows').innerHTML = '';
        recalcTotal();
        return;
    }

    document.getElementById('no-course-msg').style.display = 'none';
    document.getElementById('unit-area').style.display = 'block';

    const container = document.getElementById('unit-rows');
    container.innerHTML = '';
    for (let i = 1; i <= units; i++) {
        const label = type === 'semester' ? `Semester ${i}` : `Year ${i}`;
        addUnitRow(i, label, '');
    }
    recalcTotal();
}

let rowIdx = 0;

function addUnitRow(num = '', label = '', fee = '') {
    rowIdx++;
    const id = 'row-' + rowIdx;
    const div = document.createElement('div');
    div.className = 'unit-row';
    div.id = id;
    div.innerHTML = `
        <input type="number" min="1" value="${num}" placeholder="#" onchange="recalcTotal()" class="unit-num">
        <input type="text"   value="${label}" placeholder="e.g. Semester 1" class="unit-lbl">
        <input type="number" min="0" step="100" value="${fee}" placeholder="e.g. 20000" oninput="recalcTotal()" class="unit-fee-val">
        <button type="button" class="unit-del-btn" onclick="removeRow('${id}')">×</button>
    `;
    document.getElementById('unit-rows').appendChild(div);
}

function removeRow(id) {
    document.getElementById(id)?.remove();
    recalcTotal();
}

function recalcTotal() {
    const vals = [...document.querySelectorAll('.unit-fee-val')].map(i => parseFloat(i.value) || 0);
    const sum  = vals.reduce((a, b) => a + b, 0);
    document.getElementById('f-total').value      = sum;
    document.getElementById('sum-display').textContent = '₹' + sum.toLocaleString('en-IN');
}

function openAdd() {
    document.getElementById('f-course').value   = '';
    document.getElementById('f-year').value     = new Date().getFullYear();
    document.getElementById('f-currency').value = 'INR';
    document.getElementById('unit-rows').innerHTML = '';
    document.getElementById('no-course-msg').style.display = 'block';
    document.getElementById('unit-area').style.display = 'none';
    document.getElementById('f-total').value = '';
    document.getElementById('sum-display').textContent = '₹0';
    clearErrors();
    document.getElementById('modal').style.display = 'flex';
}

function closeModal() { document.getElementById('modal').style.display = 'none'; }

// ── Submit ─────────────────────────────────────────────────────
async function submitForm(e) {
    e.preventDefault(); clearErrors();

    const rows   = [...document.querySelectorAll('.unit-row')];
    const units  = rows.map((r, i) => ({
        unit_number: parseInt(r.querySelector('.unit-num').value) || (i + 1),
        unit_name:   r.querySelector('.unit-lbl').value.trim(),
        unit_fee:    parseFloat(r.querySelector('.unit-fee-val').value) || 0,
    }));

    if (!units.length) {
        showError('err-units', 'Please add at least one unit fee row.');
        return;
    }

    const body = {
        course_id:           document.getElementById('f-course').value,
        effective_from_year: document.getElementById('f-year').value,
        total_fee:           document.getElementById('f-total').value,
        currency:            document.getElementById('f-currency').value,
        is_active:           true,
        units,
    };

    const res  = await fetch('{{ route("writer.master.fees.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(body)
    });
    const data = await res.json();

    if (!res.ok) {
        if (data.errors) Object.entries(data.errors).forEach(([k, v]) => showError('err-' + k.replaceAll('_','-').replaceAll('.','-'), v[0]));
        else showToast(data.message || 'Error.', 'error');
        return;
    }
    showToast(data.message, 'success');
    closeModal();
    if(dt) dt.ajax.reload(null, false); else location.reload();
}

// ── Delete ─────────────────────────────────────────────────────
function deleteFee(id, name) {
    deleteId = id;
    document.getElementById('del-name').textContent = name;
    document.getElementById('del-modal').style.display = 'flex';
}
function closeDelModal() { document.getElementById('del-modal').style.display = 'none'; deleteId = null; }
async function confirmDelete() {
    const res  = await fetch(`{{ url('writer/master/fees') }}/${deleteId}`, {
        method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    const data = await res.json();
    res.ok ? showToast(data.message, 'success') : showToast(data.message || 'Error.', 'error');
    if(dt) dt.ajax.reload(null, false); else location.reload();
    closeDelModal();
}

function clearErrors() { document.querySelectorAll('.f-error').forEach(e => { e.style.display='none'; e.textContent=''; }); }
function showError(id, msg) { const el = document.getElementById(id); if (el) { el.textContent = msg; el.style.display = 'block'; } }
function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg; t.className = `toast-msg ${type} show`;
    setTimeout(() => t.classList.remove('show'), 3000);
}
</script>
@endpush
