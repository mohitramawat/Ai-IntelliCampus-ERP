@extends('layouts.dashboard')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-black text-brand-text">Pending Documents</h1>
        <p class="text-sm text-brand-sub mt-0.5">Students with one or more required documents missing.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('writer.students.pending-documents') }}"
           class="btn-secondary text-xs py-2"
           title="Refresh status">
            <span class="material-symbols-outlined text-[16px]">refresh</span>
            Refresh
        </a>
        <a href="{{ route('writer.students.create') }}" class="btn-primary">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            Create Student
        </a>
    </div>
</div>


@if($students->isEmpty())
    {{-- Empty State --}}
    <div class="card flex flex-col items-center justify-center py-16 text-center">
        <div class="w-16 h-16 rounded-2xl bg-status-successs flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-status-success text-[32px]">check_circle</span>
        </div>
        <h3 class="text-lg font-bold text-brand-text mb-1">All Documents Complete!</h3>
        <p class="text-sm text-brand-sub">Every student has all required documents uploaded.</p>
    </div>
@else
    {{-- Table --}}
    <div class="table-wrap bg-brand-surface">
        <table class="w-full">
            <thead>
                <tr class="bg-brand-muted">
                    <th class="table-head">#</th>
                    <th class="table-head">Student</th>
                    <th class="table-head hidden sm:table-cell">Roll No.</th>
                    <th class="table-head hidden md:table-cell">Batch / Course</th>
                    <th class="table-head">Document Status</th>
                    <th class="table-head text-center">Progress</th>
                    <th class="table-head text-center hidden lg:table-cell">Last Activity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                    @php
                        $uploaded      = $student->documents->keyBy('document_type');
                        $missing       = $student->missing_docs ?? [];
                        $totalReq      = count($requiredDocs);
                        $missingCount  = count($missing);
                        $uploadedCount = $totalReq - $missingCount;

                        // Find the most recent upload for this student
                        $lastActivity  = $student->documents
                            ->whereIn('document_type', $requiredDocs)
                            ->sortByDesc('updated_at')
                            ->first();

                        // Has student recently uploaded (within 24h)?
                        $recentUpload  = $lastActivity && $lastActivity->updated_at->diffInHours(now()) < 24;
                    @endphp
                    <tr class="table-row-hover">
                        {{-- Index --}}
                        <td class="table-cell text-brand-sub font-medium">{{ $index + 1 }}</td>

                        {{-- Student Name --}}
                        <td class="table-cell">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-acents border border-brand-accent/30 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-brand-accent">
                                        {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-brand-text">{{ $student->user->name ?? '—' }}</p>
                                    <p class="text-xs text-brand-sub hidden sm:block">{{ $student->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Roll No --}}
                        <td class="table-cell hidden sm:table-cell">
                            <span class="badge badge-accent">{{ $student->roll_number }}</span>
                        </td>

                        {{-- Batch --}}
                        <td class="table-cell hidden md:table-cell">
                            <p class="text-sm font-medium text-brand-text">{{ $student->batch->name ?? '—' }}</p>
                            <p class="text-xs text-brand-sub">{{ $student->batch->course->name ?? '' }}</p>
                        </td>

                        {{-- Document Status per type --}}
                        <td class="table-cell">
                            {{-- Badge row --}}
                            <div class="flex flex-wrap gap-x-2 gap-y-2">
                                @foreach($requiredDocs as $doc)
                                    @php
                                        $isMissing  = in_array($doc, $missing);
                                        $docRecord  = $uploaded->get($doc);
                                        $uploadedAt = $docRecord?->updated_at;
                                    @endphp
                                    <div class="flex flex-col items-start gap-0.5">
                                        <span class="badge {{ $isMissing ? 'badge-danger' : 'badge-success' }} flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[12px]">
                                                {{ $isMissing ? 'cancel' : 'check_circle' }}
                                            </span>
                                            {{ str_replace('_', ' ', ucwords($doc, '_')) }}
                                        </span>
                                        {{-- Upload date shown inline beneath badge — no overlap --}}
                                        @if(! $isMissing && $uploadedAt)
                                            <span class="text-[10px] text-brand-sub font-medium leading-tight pl-0.5">
                                                {{ $uploadedAt->format('d M, h:i A') }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Recent upload indicator — always below badges, no overlap --}}
                            @if($recentUpload && $missingCount > 0)
                                <div class="flex items-center gap-1.5 mt-2 px-2 py-1 rounded-lg bg-status-infos border border-status-info/20 w-fit">
                                    <span class="w-1.5 h-1.5 rounded-full bg-status-info animate-pulse flex-shrink-0"></span>
                                    <span class="text-[10px] text-status-info font-semibold whitespace-nowrap">Recently uploaded</span>
                                </div>
                            @endif
                        </td>

                        {{-- Progress --}}
                        <td class="table-cell text-center">
                            <div class="flex flex-col items-center gap-1">
                                <span class="text-sm font-bold {{ $missingCount > 0 ? 'text-status-danger' : 'text-status-success' }}">
                                    {{ $uploadedCount }}/{{ $totalReq }}
                                </span>
                                <div class="w-16 h-1.5 rounded-full bg-brand-border overflow-hidden">
                                    <div class="h-full rounded-full {{ $missingCount > 0 ? 'bg-status-danger' : 'bg-status-success' }}"
                                         style="width: {{ ($uploadedCount / $totalReq) * 100 }}%"></div>
                                </div>
                                @if($missingCount === 1)
                                    <span class="text-[9px] text-status-warning font-medium">1 left</span>
                                @elseif($missingCount > 1)
                                    <span class="text-[9px] text-status-danger font-medium">{{ $missingCount }} left</span>
                                @endif
                            </div>
                        </td>

                        {{-- Last Activity --}}
                        <td class="table-cell text-center hidden lg:table-cell">
                            @if($lastActivity)
                                <div class="flex flex-col items-center gap-0.5">
                                    <span class="text-xs font-medium text-brand-text">
                                        {{ $lastActivity->updated_at->diffForHumans() }}
                                    </span>
                                    <span class="text-[10px] text-brand-sub">
                                        via {{ ucfirst($lastActivity->uploader->roles->first()?->name ?? 'portal') }}
                                    </span>
                                </div>
                            @else
                                <span class="text-xs text-brand-sub/50">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="text-xs text-brand-sub mt-3 text-right">
        Showing {{ $students->count() }} student(s) with incomplete documentation.
    </p>
@endif

@endsection
