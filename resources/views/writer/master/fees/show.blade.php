@extends('layouts.dashboard')
@section('content')

<style>
.unit-card{background:#FAFBFF;border:1.5px solid #E4E9F0;border-radius:14px;padding:14px 16px;margin-bottom:10px;display:grid;grid-template-columns:70px 1fr 140px 36px;gap:10px;align-items:center}
.unit-card input{width:100%;padding:8px 10px;border:1.5px solid #E4E9F0;border-radius:8px;font-size:13px;font-weight:600;background:#fff;outline:none;box-sizing:border-box}
.unit-card input:focus{border-color:#0EA5E9;box-shadow:0 0 0 3px rgba(14,165,233,.12)}
.unit-del-btn{width:32px;height:32px;border-radius:8px;border:none;background:#FEE2E2;color:#EF4444;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:18px;line-height:1;transition:all .2s}
.unit-del-btn:hover{background:#EF4444;color:#fff}
.hdr-grid{display:grid;grid-template-columns:70px 1fr 140px 36px;gap:10px;padding:0 4px;margin-bottom:8px}
.hdr-grid span{font-size:10px;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.06em}
.info-chip{background:#EFF6FF;color:#1E40AF;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:5px}
.total-box{background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border-radius:14px;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;margin:16px 0}
.toast-msg{position:fixed;top:20px;right:20px;z-index:999;padding:12px 20px;border-radius:12px;font-size:13px;font-weight:600;color:#fff;box-shadow:0 6px 20px rgba(0,0,0,.15);transform:translateX(130%);transition:transform .35s cubic-bezier(.34,1.56,.64,1)}
.toast-msg.show{transform:translateX(0)}
.toast-msg.success{background:#10B981}.toast-msg.error{background:#EF4444}
</style>

{{-- BREADCRUMB + HEADER --}}
<div style="margin-bottom:22px">
    <div style="font-size:12px;color:#64748B;margin-bottom:6px;display:flex;gap:5px;align-items:center">
        <a href="{{ route('writer.master.fees.index') }}" style="color:#64748B;text-decoration:none">Fee Structures</a>
        <span class="material-symbols-outlined" style="font-size:12px">chevron_right</span>
        <span style="color:#0EA5E9;font-weight:700">Edit</span>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-size:24px;font-weight:800;color:#1A202C;margin:0">
                {{ $feeStructure->course->code }} — Fee Structure {{ $feeStructure->effective_from_year }}
            </h1>
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:6px">
                <span class="info-chip">
                    <span class="material-symbols-outlined" style="font-size:14px">school</span>
                    {{ $feeStructure->course->name }}
                </span>
                <span class="info-chip" style="background:#F0FDF4;color:#166534">
                    <span class="material-symbols-outlined" style="font-size:14px">payments</span>
                    {{ $feeStructure->currency ?? 'INR' }}
                </span>
                @if($feeStructure->is_active)
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-danger">Inactive</span>
                @endif
            </div>
        </div>
        <a href="{{ route('writer.master.fees.index') }}" style="display:flex;align-items:center;gap:5px;font-size:13px;font-weight:600;color:#64748B;text-decoration:none">
            <span class="material-symbols-outlined" style="font-size:16px">arrow_back</span> Back to List
        </a>
    </div>
</div>

{{-- FORM CARD --}}
<div class="card" style="max-width:780px">
    <form id="edit-form" onsubmit="saveChanges(event)">

        {{-- Header section --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:22px">
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Effective Year *</label>
                <input type="number" id="f-year" name="effective_from_year"
                    value="{{ $feeStructure->effective_from_year }}"
                    min="2000" max="2099"
                    style="width:100%;padding:10px 14px;border:1.5px solid #E4E9F0;border-radius:12px;font-size:13px;font-weight:600;outline:none;box-sizing:border-box">
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Total Fee (auto)</label>
                <input type="number" id="f-total" readonly
                    value="{{ $feeStructure->total_fee }}"
                    style="width:100%;padding:10px 14px;border:1.5px solid #E4E9F0;border-radius:12px;font-size:13px;font-weight:600;background:#F8FAFC;cursor:not-allowed;box-sizing:border-box">
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Currency</label>
                <select id="f-currency" name="currency"
                    style="width:100%;padding:10px 14px;border:1.5px solid #E4E9F0;border-radius:12px;font-size:13px;font-weight:600;outline:none">
                    <option value="INR" {{ ($feeStructure->currency ?? 'INR') === 'INR' ? 'selected' : '' }}>INR</option>
                    <option value="USD" {{ ($feeStructure->currency ?? '') === 'USD' ? 'selected' : '' }}>USD</option>
                </select>
            </div>
        </div>

        {{-- Active toggle --}}
        <div style="margin-bottom:22px;display:flex;align-items:center;gap:12px">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer">
                <input type="checkbox" id="f-active" {{ $feeStructure->is_active ? 'checked' : '' }}
                    style="width:38px;height:20px;appearance:none;background:#E4E9F0;border-radius:99px;cursor:pointer;transition:background .2s;position:relative;flex-shrink:0;outline:none"
                    onchange="this.style.background=this.checked?'#10B981':'#E4E9F0'">
                <span style="font-size:13px;font-weight:700;color:#1A202C">Active</span>
            </label>
            <span style="font-size:12px;color:#94A3B8">Inactive structures won't be shown to students</span>
        </div>
        @php $isActive = $feeStructure->is_active; @endphp
        <script>
            document.getElementById('f-active').style.background = '{{ $isActive ? "#10B981" : "#E4E9F0" }}';
        </script>

        {{-- Unit Fees --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <h3 style="font-size:14px;font-weight:800;color:#1A202C;margin:0">
                <span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;margin-right:4px">table_rows</span>
                Semester / Unit Fees
            </h3>
            <button type="button" onclick="addRow()" style="font-size:11px;font-weight:700;color:#0EA5E9;background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:4px">
                <span class="material-symbols-outlined" style="font-size:14px">add_circle</span> Add Row
            </button>
        </div>

        <div class="hdr-grid">
            <span>Unit #</span><span>Label</span><span>Fee (₹)</span><span></span>
        </div>

        <div id="unit-rows">
            @foreach($feeStructure->unitFees->sortBy('unit_number') as $uf)
            <div class="unit-card" id="row-{{ $uf->id }}">
                <input type="number" min="1" value="{{ $uf->unit_number }}" class="u-num" oninput="recalc()">
                <input type="text"   value="{{ $uf->unit_name }}"   class="u-lbl" placeholder="e.g. Semester 1">
                <input type="number" min="0" step="100" value="{{ $uf->unit_fee }}" class="u-fee" oninput="recalc()">
                <button type="button" class="unit-del-btn" onclick="removeRow('row-{{ $uf->id }}')">×</button>
            </div>
            @endforeach
        </div>

        <div class="total-box">
            <span style="font-size:14px;font-weight:700;color:#1E40AF">Total Fee (Sum of All Units)</span>
            <span id="total-display" style="font-size:22px;font-weight:800;color:#1D4ED8">
                ₹{{ number_format($feeStructure->total_fee, 0) }}
            </span>
        </div>

        {{-- Submit --}}
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
            <a href="{{ route('writer.master.fees.index') }}"
                style="padding:10px 22px;border-radius:12px;border:1.5px solid #E4E9F0;background:#F5F7FA;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;color:#1A202C">
                Cancel
            </a>
            <button type="submit" class="btn-primary" style="padding:10px 28px">
                <span class="material-symbols-outlined" style="font-size:16px;vertical-align:middle;margin-right:4px">save</span>
                Save Changes
            </button>
        </div>

    </form>
</div>

<div id="toast" class="toast-msg"></div>

@endsection

@push('scripts')
<script>
let rowCnt = 1000; // high to avoid collision with real IDs

function addRow(num = '', label = '', fee = '') {
    rowCnt++;
    const id  = 'newrow-' + rowCnt;
    const div = document.createElement('div');
    div.className = 'unit-card'; div.id = id;
    div.innerHTML = `
        <input type="number" min="1" value="${num}" class="u-num" oninput="recalc()">
        <input type="text" value="${label}" placeholder="e.g. Semester 1" class="u-lbl">
        <input type="number" min="0" step="100" value="${fee}" class="u-fee" oninput="recalc()">
        <button type="button" class="unit-del-btn" onclick="removeRow('${id}')">×</button>
    `;
    document.getElementById('unit-rows').appendChild(div);
    recalc();
}

function removeRow(id) {
    document.getElementById(id)?.remove();
    recalc();
}

function recalc() {
    const sum = [...document.querySelectorAll('.u-fee')]
        .reduce((a, i) => a + (parseFloat(i.value) || 0), 0);
    document.getElementById('f-total').value = sum;
    document.getElementById('total-display').textContent = '₹' + sum.toLocaleString('en-IN');
}

// Run once on load
recalc();

async function saveChanges(e) {
    e.preventDefault();

    const rows = [...document.querySelectorAll('#unit-rows .unit-card')];
    const units = rows.map((r, i) => ({
        unit_number: parseInt(r.querySelector('.u-num').value) || (i + 1),
        unit_name:   r.querySelector('.u-lbl').value.trim(),
        unit_fee:    parseFloat(r.querySelector('.u-fee').value) || 0,
    }));

    if (!units.length) {
        showToast('Add at least one unit fee row.', 'error');
        return;
    }

    const body = {
        effective_from_year: document.getElementById('f-year').value,
        total_fee:           document.getElementById('f-total').value,
        currency:            document.getElementById('f-currency').value,
        is_active:           document.getElementById('f-active').checked,
        units,
    };

    const res  = await fetch('{{ route("writer.master.fees.update", $feeStructure->id) }}', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(body)
    });
    const data = await res.json();

    if (res.ok) {
        showToast(data.message, 'success');
        setTimeout(() => window.location.href = '{{ route("writer.master.fees.index") }}', 1200);
    } else {
        showToast(data.message || 'Update failed.', 'error');
    }
}

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg; t.className = `toast-msg ${type} show`;
    setTimeout(() => t.classList.remove('show'), 3000);
}
</script>
@endpush
