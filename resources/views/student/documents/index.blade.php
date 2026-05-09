@extends('layouts.dashboard')

@section('content')

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-black text-brand-text">My Documents</h1>
        <p class="text-sm text-brand-sub mt-0.5">Upload and manage your required academic documents.</p>
    </div>
    {{-- Overall Progress Chip --}}
    <div class="flex items-center gap-2 px-4 py-2 rounded-2xl border
        {{ $totalPending === 0 ? 'bg-status-successs border-status-success/20' : 'bg-status-warnings border-status-warning/20' }}">
        <span class="material-symbols-outlined text-[18px] {{ $totalPending === 0 ? 'text-status-success' : 'text-status-warning' }}">
            {{ $totalPending === 0 ? 'check_circle' : 'pending_actions' }}
        </span>
        <span class="text-sm font-bold {{ $totalPending === 0 ? 'text-status-success' : 'text-status-warning' }}">
            {{ $totalUploaded }}/{{ $totalRequired }} Uploaded
        </span>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div id="flash-success"
         class="flex items-center gap-3 px-4 py-3.5 rounded-xl bg-status-successs border border-status-success/30 mb-5 text-status-success text-sm font-medium shadow-card">
        <span class="material-symbols-outlined text-[20px] flex-shrink-0">check_circle</span>
        <span>{{ session('success') }}</span>
        <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-status-success/60 hover:text-status-success">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>
@endif

@if($errors->has('error'))
    <div id="flash-error"
         class="flex items-center gap-3 px-4 py-3.5 rounded-xl bg-status-dangers border border-status-danger/30 mb-5 text-status-danger text-sm font-medium shadow-card">
        <span class="material-symbols-outlined text-[20px] flex-shrink-0">error</span>
        <span>{{ $errors->first('error') }}</span>
        <button onclick="document.getElementById('flash-error').remove()" class="ml-auto text-status-danger/60 hover:text-status-danger">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>
@endif

{{-- Overall Progress Bar --}}
<div class="card mb-6">
    <div class="flex items-center justify-between mb-3">
        <div>
            <p class="text-sm font-semibold text-brand-text">Document Completion</p>
            <p class="text-xs text-brand-sub mt-0.5">
                @if($totalPending === 0)
                    All required documents submitted ✓
                @else
                    {{ $totalPending }} document{{ $totalPending > 1 ? 's' : '' }} still required
                @endif
            </p>
        </div>
        <span class="text-3xl font-black {{ $totalPending === 0 ? 'text-status-success' : 'text-brand-accent' }}">
            {{ $progressPct }}%
        </span>
    </div>
    <div class="w-full h-3 rounded-full bg-brand-muted overflow-hidden">
        <div class="h-full rounded-full transition-all duration-700
            {{ $totalPending === 0 ? 'bg-status-success' : 'bg-gradient-to-r from-brand-accent to-sky-400' }}"
             style="width: {{ $progressPct }}%"></div>
    </div>
    <div class="flex items-center gap-4 mt-3 text-xs text-brand-sub">
        <span class="flex items-center gap-1">
            <span class="w-2 h-2 rounded-full bg-status-success inline-block"></span>
            {{ $totalUploaded }} Uploaded
        </span>
        <span class="flex items-center gap-1">
            <span class="w-2 h-2 rounded-full bg-status-danger inline-block"></span>
            {{ $totalPending }} Pending
        </span>
    </div>
</div>

{{-- Document Cards Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($requiredDocs as $docType => $docLabel)
        @php
            $doc       = $uploaded->get($docType);
            $isUploaded = $doc !== null;
            $ext        = $isUploaded ? pathinfo($doc->file_name, PATHINFO_EXTENSION) : null;
            $isPdf      = $ext && strtolower($ext) === 'pdf';
            $inputId    = 'file_' . Str::slug($docType, '_');
        @endphp

        <div class="card flex flex-col gap-0 p-0 overflow-hidden group">

            {{-- Card top colour band --}}
            <div class="h-1.5 w-full {{ $isUploaded ? 'bg-status-success' : 'bg-status-danger' }}"></div>

            <div class="p-5 flex-1 flex flex-col">

                {{-- Header row --}}
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0
                            {{ $isUploaded ? 'bg-status-successs' : 'bg-status-dangers' }}">
                            <span class="material-symbols-outlined text-[22px]
                                {{ $isUploaded ? 'text-status-success' : 'text-status-danger' }}">
                                {{ $isUploaded ? 'task' : 'description' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-brand-text leading-tight">{{ $docLabel }}</p>
                            <p class="text-xs text-brand-sub mt-0.5">
                                {{ $isUploaded ? 'Uploaded' : 'Required · Not Uploaded' }}
                            </p>
                        </div>
                    </div>
                    <span class="badge {{ $isUploaded ? 'badge-success' : 'badge-danger' }} flex-shrink-0">
                        {{ $isUploaded ? 'Done' : 'Pending' }}
                    </span>
                </div>

                @if($isUploaded)
                    {{-- Uploaded File Info --}}
                    <div class="rounded-xl bg-brand-muted border border-brand-border p-3 mb-4 flex items-center gap-3 flex-1">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                            {{ $isPdf ? 'bg-red-100' : 'bg-blue-100' }}">
                            <span class="material-symbols-outlined text-[20px] {{ $isPdf ? 'text-red-500' : 'text-blue-500' }}">
                                {{ $isPdf ? 'picture_as_pdf' : 'image' }}
                            </span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-brand-text truncate" title="{{ $doc->file_name }}">
                                {{ $doc->file_name }}
                            </p>
                            <p class="text-[11px] text-brand-sub">
                                {{ number_format($doc->file_size / 1024, 1) }} KB ·
                                {{ strtoupper($ext) }} ·
                                {{ $doc->created_at->format('d M Y') }}
                            </p>
                        </div>
                        {{-- View button --}}
                        <a href="{{ Storage::disk('public')->url($doc->file_path) }}"
                           target="_blank"
                           class="ml-auto flex-shrink-0 w-8 h-8 rounded-lg bg-brand-surface border border-brand-border flex items-center justify-center hover:border-brand-accent hover:text-brand-accent transition-all"
                           title="View document">
                            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                        </a>
                    </div>

                    {{-- Action Buttons: Re-upload + Delete --}}
                    <div class="flex gap-2 mt-auto">
                        {{-- Re-upload --}}
                        <button type="button"
                                onclick="openUploadModal('{{ $docType }}', '{{ $docLabel }}')"
                                class="flex-1 btn-secondary text-xs py-2 justify-center">
                            <span class="material-symbols-outlined text-[15px]">upload_file</span>
                            Replace
                        </button>
                        {{-- Delete --}}
                        <form method="POST" action="{{ route('student.documents.delete') }}"
                              onsubmit="return confirm('Are you sure you want to remove this document?')">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="document_type" value="{{ $docType }}">
                            <button type="submit" class="btn-danger text-xs py-2 px-3">
                                <span class="material-symbols-outlined text-[15px]">delete</span>
                            </button>
                        </form>
                    </div>

                @else
                    {{-- Not uploaded — upload CTA --}}
                    <div class="flex-1 flex flex-col items-center justify-center py-4 text-center mb-4 rounded-xl border-2 border-dashed border-brand-border bg-brand-muted/50 gap-2">
                        <span class="material-symbols-outlined text-brand-sub text-[32px]">cloud_upload</span>
                        <p class="text-xs text-brand-sub font-medium">PDF, JPG, PNG · Max 5 MB</p>
                    </div>
                    <button type="button"
                            onclick="openUploadModal('{{ $docType }}', '{{ $docLabel }}')"
                            class="btn-primary w-full justify-center text-xs py-2.5 mt-auto">
                        <span class="material-symbols-outlined text-[16px]">upload_file</span>
                        Upload {{ $docLabel }}
                    </button>
                @endif
            </div>
        </div>
    @endforeach
</div>

{{-- Info note --}}
<div class="flex items-start gap-2.5 mt-6 p-4 rounded-xl bg-status-infos border border-status-info/20">
    <span class="material-symbols-outlined text-status-info text-[18px] flex-shrink-0 mt-0.5">info</span>
    <p class="text-xs text-status-info font-medium">
        Uploaded documents will be reviewed by the college office.
        You may re-upload if a document is rejected or needs updating.
        Allowed formats: <strong>PDF, JPG, JPEG, PNG</strong> (max 5 MB each).
    </p>
</div>


{{-- ─── Upload Modal ──────────────────────────────────────────────────── --}}
<div id="upload-modal"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-brand-text/30 backdrop-blur-sm hidden"
     onclick="if(event.target===this) closeUploadModal()">

    <div class="bg-brand-surface rounded-2xl shadow-2xl border border-brand-border w-full max-w-md transform transition-all duration-300 scale-95 opacity-0"
         id="modal-box">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 p-4 py-4 border-b border-brand-border">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-brand-acents flex items-center justify-center">
                    <span class="material-symbols-outlined text-brand-accent text-[20px]">upload_file</span>
                </div>
                <div>
                    <p class="text-sm font-bold text-brand-text" id="modal-title">Upload Document</p>
                    <p class="text-xs text-brand-sub" id="modal-subtitle">Select a file from your device</p>
                </div>
            </div>
            <button onclick="closeUploadModal()"
                    class="w-8 h-8 rounded-lg bg-brand-muted border border-brand-border flex items-center justify-center text-brand-sub hover:text-brand-text hover:border-brand-accent transition-all">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        {{-- Modal Form --}}
        <form id="upload-form"
              method="POST"
              action="{{ route('student.documents.upload') }}"
              enctype="multipart/form-data"
              class="px-6 p-4 py-5 space-y-4">
            @csrf
            <input type="hidden" name="document_type" id="modal-doc-type">

            {{-- Drop zone --}}
            <div id="drop-zone"
                 class="relative border-2 border-dashed border-brand-border rounded-xl p-6 text-center cursor-pointer
                        hover:border-brand-accent hover:bg-brand-acents/10 transition-all duration-200 group"
                 onclick="document.getElementById('modal-file-input').click()"
                 ondragover="event.preventDefault(); this.classList.add('border-brand-accent','bg-brand-acents/10')"
                 ondragleave="this.classList.remove('border-brand-accent','bg-brand-acents/10')"
                 ondrop="handleDrop(event)">

                <span class="material-symbols-outlined text-brand-sub text-[40px] group-hover:text-brand-accent transition-colors" id="drop-icon">cloud_upload</span>
                <p class="text-sm font-semibold text-brand-text mt-2" id="drop-label">Click or drag & drop file here</p>
                <p class="text-xs text-brand-sub mt-1">PDF, JPG, JPEG, PNG · Max 5 MB</p>

                <input type="file"
                       id="modal-file-input"
                       name="document"
                       accept=".pdf,.jpg,.jpeg,.png"
                       class="hidden"
                       onchange="handleFileSelect(this)">
            </div>

            {{-- File Preview --}}
            <div id="file-preview" class="hidden flex items-center gap-3 px-3 py-2.5 rounded-xl bg-brand-muted border border-brand-border">
                <span class="material-symbols-outlined text-brand-accent text-[22px]" id="preview-icon">description</span>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-brand-text truncate" id="preview-name">filename.pdf</p>
                    <p class="text-[11px] text-brand-sub" id="preview-size">0 KB</p>
                </div>
                <button type="button" onclick="clearFile()"
                        class="text-brand-sub hover:text-status-danger transition-colors flex-shrink-0">
                    <span class="material-symbols-outlined text-[18px]">cancel</span>
                </button>
            </div>

            {{-- Validation error --}}
            @if($errors->hasAny(['document', 'document_type']))
                <p class="text-xs text-status-danger flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">error</span>
                    {{ $errors->first('document') ?? $errors->first('document_type') }}
                </p>
            @endif

            {{-- Actions --}}
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeUploadModal()"
                        class="btn-secondary flex-1 justify-center text-sm">
                    Cancel
                </button>
                <button type="submit" id="upload-btn"
                        class="btn-primary flex-1 justify-center text-sm" disabled>
                    <span class="material-symbols-outlined text-[16px]">cloud_upload</span>
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Modal helpers ────────────────────────────────────────
    function openUploadModal(docType, docLabel) {
        document.getElementById('modal-doc-type').value   = docType;
        document.getElementById('modal-title').textContent = 'Upload ' + docLabel;
        document.getElementById('modal-subtitle').textContent = 'Select a file from your device';
        clearFile();

        const modal = document.getElementById('upload-modal');
        const box   = document.getElementById('modal-box');
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            box.classList.remove('scale-95','opacity-0');
            box.classList.add('scale-100','opacity-100');
        });
    }

    function closeUploadModal() {
        const modal = document.getElementById('upload-modal');
        const box   = document.getElementById('modal-box');
        box.classList.remove('scale-100','opacity-100');
        box.classList.add('scale-95','opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    // ── File handling ────────────────────────────────────────
    function handleFileSelect(input) {
        if (input.files && input.files[0]) showPreview(input.files[0]);
    }

    function handleDrop(event) {
        event.preventDefault();
        const zone = document.getElementById('drop-zone');
        zone.classList.remove('border-brand-accent','bg-brand-acents/10');
        const file = event.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('modal-file-input').files = dt.files;
            showPreview(file);
        }
    }

    function showPreview(file) {
        const isPdf     = file.type === 'application/pdf';
        const sizeKB    = (file.size / 1024).toFixed(1);
        const preview   = document.getElementById('file-preview');
        const dropZone  = document.getElementById('drop-zone');
        const btn       = document.getElementById('upload-btn');

        document.getElementById('preview-name').textContent = file.name;
        document.getElementById('preview-size').textContent = sizeKB + ' KB · ' + file.type.split('/')[1].toUpperCase();
        document.getElementById('preview-icon').textContent = isPdf ? 'picture_as_pdf' : 'image';

        preview.classList.remove('hidden');
        preview.classList.add('flex');

        // Update drop zone feedback
        document.getElementById('drop-icon').textContent  = 'check_circle';
        document.getElementById('drop-icon').classList.add('text-status-success');
        document.getElementById('drop-label').textContent = 'File selected — ready to upload';

        // Enable submit
        btn.disabled = false;
        btn.classList.remove('opacity-50','cursor-not-allowed');
    }

    function clearFile() {
        document.getElementById('modal-file-input').value = '';
        document.getElementById('file-preview').classList.add('hidden');
        document.getElementById('file-preview').classList.remove('flex');

        document.getElementById('drop-icon').textContent = 'cloud_upload';
        document.getElementById('drop-icon').className   = 'material-symbols-outlined text-brand-sub text-[40px] group-hover:text-brand-accent transition-colors';
        document.getElementById('drop-label').textContent = 'Click or drag & drop file here';

        const btn = document.getElementById('upload-btn');
        btn.disabled = true;
    }

    // Auto-open modal if there were validation errors (re-open for the failed field)
    @if($errors->has('document') || $errors->has('document_type'))
        @php
            $oldType  = old('document_type', '');
            $oldLabel = $requiredDocs[$oldType] ?? $oldType;
        @endphp
        document.addEventListener('DOMContentLoaded', () => {
            openUploadModal('{{ $oldType }}', '{{ $oldLabel }}');
        });
    @endif

    // Auto-dismiss flash after 5s
    setTimeout(() => {
        ['flash-success','flash-error'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.transition = 'opacity 0.5s', el.style.opacity = '0', setTimeout(() => el.remove(), 500);
        });
    }, 5000);
</script>
@endpush
