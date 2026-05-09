

<?php $__env->startSection('content'); ?>

<?php
    $name       = $student->user->name ?? '—';
    $initial    = strtoupper(substr($name, 0, 1));
    $batchName  = $student->batch->name ?? '—';
    $courseName = $student->batch->course->name ?? '—';
    $deptName   = $student->batch->course->department->name ?? '—';
    $fmtDate    = fn($d) => $d instanceof \Carbon\Carbon ? $d->format('d M Y') : ($d ? \Carbon\Carbon::parse($d)->format('d M Y') : '—');
    $fmt        = fn($n) => '₹'.number_format($n, 2);
?>


<div class="flex items-center justify-between mb-6 gap-4 flex-wrap">
    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('admin.students.index')); ?>"
           class="w-9 h-9 rounded-xl bg-brand-muted border border-brand-border flex items-center justify-center
                  text-brand-sub hover:text-brand-text hover:border-brand-accent transition-all">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-xl font-black text-brand-text leading-tight">Student Profile</h1>
            <p class="text-xs text-brand-sub"><?php echo e($deptName); ?> · <?php echo e($courseName); ?> · <?php echo e($batchName); ?></p>
        </div>
    </div>
    <span class="badge <?php echo e($student->is_active ? 'badge-success' : 'badge-danger'); ?> text-sm py-1.5 px-3">
        <?php echo e($student->is_active ? 'Active' : 'Inactive'); ?>

    </span>
</div>


<div class="relative bg-gradient-to-r from-brand-accent to-sky-400 rounded-2xl p-6 mb-6 overflow-hidden shadow-accent">
    <div class="absolute inset-0 opacity-10">
        <svg class="absolute right-0 top-0 h-full" viewBox="0 0 200 200" fill="white">
            <circle cx="180" cy="20" r="80"/><circle cx="20" cy="180" r="60"/>
        </svg>
    </div>
    <div class="relative z-10 flex items-center gap-5">
        <div class="w-16 h-16 rounded-2xl bg-white/20 border-2 border-white/30 backdrop-blur flex items-center justify-center flex-shrink-0">
            <span class="text-2xl font-black text-white"><?php echo e($initial); ?></span>
        </div>
        <div>
            <h2 class="text-xl font-black text-white"><?php echo e($name); ?></h2>
            <p class="text-white/80 text-sm"><?php echo e($student->user->email ?? '—'); ?></p>
            <div class="flex flex-wrap gap-2 mt-2">
                <?php if($student->roll_number): ?>
                <span class="bg-white/20 text-white text-xs font-semibold px-2.5 py-1 rounded-lg">
                    Roll: <?php echo e($student->roll_number); ?>

                </span>
                <?php endif; ?>
                <?php if($student->enrollment_number): ?>
                <span class="bg-white/20 text-white text-xs font-semibold px-2.5 py-1 rounded-lg">
                    Enroll: <?php echo e($student->enrollment_number); ?>

                </span>
                <?php endif; ?>
                <?php if($student->category): ?>
                <span class="bg-white/20 text-white text-xs font-semibold px-2.5 py-1 rounded-lg capitalize">
                    <?php echo e($student->category); ?>

                </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php $__currentLoopData = [
        ['label'=>'Total Fee',   'value'=>$fmt($totalCourseFee), 'icon'=>'account_balance_wallet', 'color'=>'text-brand-accent',   'bg'=>'bg-brand-acents'],
        ['label'=>'Paid',        'value'=>$fmt($totalPaid),       'icon'=>'check_circle',           'color'=>'text-status-success', 'bg'=>'bg-status-successs'],
        ['label'=>'Pending',     'value'=>$fmt($totalPending),    'icon'=>'hourglass_empty',         'color'=>'text-status-danger',  'bg'=>'bg-status-dangers'],
        ['label'=>'Fines (Due)', 'value'=>$fmt($unpaidFines),     'icon'=>'gavel',                  'color'=>'text-status-warning', 'bg'=>'bg-status-warnings'],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="kpi-card">
        <div class="kpi-icon <?php echo e($k['bg']); ?>">
            <span class="material-symbols-outlined <?php echo e($k['color']); ?> text-[22px]"><?php echo e($k['icon']); ?></span>
        </div>
        <div>
            <div class="kpi-value text-xl"><?php echo e($k['value']); ?></div>
            <div class="kpi-label"><?php echo e($k['label']); ?></div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="bg-brand-surface rounded-2xl border border-brand-border shadow-card overflow-hidden">

    
    <div class="flex border-b border-brand-border px-4 pt-1 gap-1 overflow-x-auto" id="tab-nav">
        <?php $__currentLoopData = [
            ['id'=>'profile',   'label'=>'Profile',    'icon'=>'person'],
            ['id'=>'finance',   'label'=>'Finance',    'icon'=>'payments'],
            ['id'=>'documents', 'label'=>'Documents',  'icon'=>'folder_open'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <button onclick="switchTab('<?php echo e($tab['id']); ?>')"
                id="btn-<?php echo e($tab['id']); ?>"
                class="tab-nav-btn flex items-center gap-1.5 px-4 py-3 text-sm font-semibold whitespace-nowrap
                       border-b-2 border-transparent text-brand-sub hover:text-brand-text transition-all
                       <?php echo e($loop->first ? 'tab-active' : ''); ?>">
            <span class="material-symbols-outlined text-[16px]"><?php echo e($tab['icon']); ?></span>
            <?php echo e($tab['label']); ?>

        </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div id="tab-profile" class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            
            <div class="rounded-2xl bg-gradient-to-br from-brand-accent/8 to-sky-400/5 border border-brand-accent/20 p-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-brand-accent mb-4">Academic Information</p>
                <div class="grid grid-cols-2 gap-4">
                    <?php $__currentLoopData = [
                        ['label'=>'Enrollment No.',     'value'=> $student->enrollment_number ?? '—'],
                        ['label'=>'Roll Number',         'value'=> $student->roll_number ?? '—'],
                        ['label'=>'Batch',               'value'=> $batchName],
                        ['label'=>'Course',              'value'=> $courseName],
                        ['label'=>'Course Code',         'value'=> $student->batch->course->code ?? '—'],
                        ['label'=>'Department',          'value'=> $deptName],
                        ['label'=>'Duration',            'value'=> ($student->batch->course->duration_years ?? '—').' yrs'],
                        ['label'=>'Batch Period',        'value'=> ($student->batch->start_year ?? '—').' – '.($student->batch->end_year ?? '—')],
                        ['label'=>'Admission Date',      'value'=> $fmtDate($student->admission_date)],
                        ['label'=>'Current Unit/Year',   'value'=> $student->current_unit ?? '—'],
                        ['label'=>'Academic Status',     'value'=> ucwords(str_replace('_',' ',$student->academic_status ?? '—'))],
                        ['label'=>'Status',              'value'=> $student->is_active ? 'Active' : 'Inactive'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <p class="text-[10px] text-brand-sub mb-0.5"><?php echo e($row['label']); ?></p>
                        <p class="text-sm font-semibold text-brand-text"><?php echo e($row['value']); ?></p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div class="rounded-2xl bg-brand-muted border border-brand-border p-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-brand-sub mb-4">Personal Details</p>
                <div class="grid grid-cols-2 gap-4">
                    <?php $__currentLoopData = [
                        ['label'=>'Full Name',      'value'=> $name],
                        ['label'=>'Email',           'value'=> $student->user->email ?? '—'],
                        ['label'=>'Gender',          'value'=> ucfirst($student->gender ?? '—')],
                        ['label'=>'Date of Birth',   'value'=> $fmtDate($student->date_of_birth)],
                        ['label'=>'Contact',         'value'=> $student->contact_number ?? '—'],
                        ['label'=>'Category',        'value'=> ucfirst($student->category ?? '—')],
                        ['label'=>'Father\'s Name',  'value'=> $student->father_name ?? '—'],
                        ['label'=>'Mother\'s Name',  'value'=> $student->mother_name ?? '—'],
                        ['label'=>'Enrolled On',     'value'=> $fmtDate($student->created_at)],
                        ['label'=>'Account Created', 'value'=> $fmtDate($student->user->created_at ?? null)],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <p class="text-[10px] text-brand-sub mb-0.5"><?php echo e($row['label']); ?></p>
                        <p class="text-sm font-semibold text-brand-text"><?php echo e($row['value']); ?></p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php if($student->address): ?>
                <div class="mt-4 pt-4 border-t border-brand-border">
                    <p class="text-[10px] text-brand-sub mb-0.5">Address</p>
                    <p class="text-sm font-semibold text-brand-text"><?php echo e($student->address); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div id="tab-finance" class="p-6 hidden">

        
        <div class="flex items-start gap-3 p-4 rounded-xl bg-status-warnings border border-status-warning/20 mb-5">
            <span class="material-symbols-outlined text-status-warning text-[20px] flex-shrink-0 mt-0.5">shield</span>
            <div>
                <p class="text-sm font-bold text-status-warning">Read-Only Financial Overview</p>
                <p class="text-xs text-status-warning/80 mt-0.5">No edits are permitted here. Contact the Accounts team to make changes.</p>
            </div>
        </div>

        
        <div class="bg-brand-muted border border-brand-border rounded-2xl p-5 mb-5">
            <div class="flex justify-between items-center mb-3">
                <p class="text-sm font-bold text-brand-text">Fee Collection Progress</p>
                <span class="text-xl font-black text-brand-accent"><?php echo e($collectionPct); ?>%</span>
            </div>
            <div class="w-full h-3 rounded-full bg-brand-border overflow-hidden">
                <div class="h-full rounded-full bg-gradient-to-r from-brand-accent to-sky-400 transition-all duration-700"
                     style="width: <?php echo e($collectionPct); ?>%"></div>
            </div>
            <div class="flex justify-between mt-2 text-xs text-brand-sub">
                <span>Paid: <?php echo e($fmt($totalPaid)); ?></span>
                <span>Remaining: <?php echo e($fmt($totalPending)); ?></span>
            </div>
        </div>

        
        <?php if($totalFines > 0): ?>
        <div class="flex items-center justify-between p-4 rounded-xl bg-status-warnings border border-status-warning/20 mb-5">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-status-warning text-[22px]">gavel</span>
                <div>
                    <p class="text-sm font-bold text-status-warning">Total Fines Levied</p>
                    <p class="text-xs text-status-warning/80"><?php echo e($fmt($unpaidFines)); ?> still unpaid</p>
                </div>
            </div>
            <span class="text-lg font-black text-status-warning"><?php echo e($fmt($totalFines)); ?></span>
        </div>
        <?php endif; ?>

        
        <?php if($student->unitFees->isEmpty()): ?>
            <div class="text-center py-10">
                <span class="material-symbols-outlined text-[40px] text-brand-sub">payments</span>
                <p class="text-sm text-brand-sub mt-2">No fee records found for this student.</p>
            </div>
        <?php else: ?>
            <p class="text-xs font-bold text-brand-sub uppercase tracking-wider mb-3">Installment Breakdown</p>
            <div class="space-y-3">
                <?php $__currentLoopData = $student->unitFees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $statusColors = [
                        'paid'    => ['bg'=>'bg-status-successs','border'=>'border-status-success/20','text'=>'text-status-success','badge'=>'badge-success'],
                        'partial' => ['bg'=>'bg-status-warnings','border'=>'border-status-warning/20','text'=>'text-status-warning','badge'=>'badge-warning'],
                        'pending' => ['bg'=>'bg-status-dangers', 'border'=>'border-status-danger/20', 'text'=>'text-status-danger', 'badge'=>'badge-danger'],
                        'unpaid'  => ['bg'=>'bg-status-dangers', 'border'=>'border-status-danger/20', 'text'=>'text-status-danger', 'badge'=>'badge-danger'],
                    ];
                    $sc = $statusColors[$uf->status] ?? $statusColors['pending'];
                ?>
                <div class="rounded-xl border border-brand-border overflow-hidden">
                    
                    <div class="flex items-center justify-between px-4 py-3 bg-brand-muted">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-brand-accent text-[18px]">receipt_long</span>
                            <span class="text-sm font-bold text-brand-text">
                                <?php echo e($uf->unit_name ?? 'Unit '.$uf->unit_number); ?>

                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-brand-sub">
                                <?php echo e($fmt($uf->total_paid)); ?> / <?php echo e($fmt($uf->unit_fee)); ?>

                            </span>
                            <span class="badge <?php echo e($sc['badge']); ?>"><?php echo e(ucfirst($uf->status)); ?></span>
                        </div>
                    </div>
                    
                    <?php $__empty_1 = true; $__currentLoopData = $uf->installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $ic  = $statusColors[$inst->status] ?? $statusColors['pending'];
                        $fineTotal = $inst->fines->sum('fine_amount');
                    ?>
                    <div class="flex items-center justify-between px-4 py-3 border-t border-brand-border/50 hover:bg-brand-muted/50 transition-colors">
                        <div>
                            <p class="text-sm font-semibold text-brand-text">Installment #<?php echo e($inst->installment_number); ?></p>
                            <p class="text-xs text-brand-sub">
                                Due: <?php echo e($inst->due_date ? $fmtDate($inst->due_date) : '—'); ?>

                            </p>
                            <?php if($fineTotal > 0): ?>
                            <p class="text-xs text-status-warning mt-0.5">
                                <span class="material-symbols-outlined text-[12px] align-middle">gavel</span>
                                Fine: <?php echo e($fmt($fineTotal)); ?>

                                <?php if($inst->fines->where('is_paid', false)->sum('fine_amount') > 0): ?>
                                    <span class="text-status-danger">(<?php echo e($fmt($inst->fines->where('is_paid', false)->sum('fine_amount'))); ?> unpaid)</span>
                                <?php endif; ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-brand-text">
                                <?php echo e($fmt($inst->paid_amount)); ?>

                                <span class="text-brand-sub font-normal text-xs">/ <?php echo e($fmt($inst->installment_amount)); ?></span>
                            </p>
                            <span class="badge <?php echo e($ic['badge']); ?> text-[10px]"><?php echo e(ucfirst($inst->status)); ?></span>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-4 py-3 border-t border-brand-border/50">
                        <p class="text-xs text-brand-sub text-center">No installments defined.</p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>

    
    <div id="tab-documents" class="p-6 hidden">

        
        <?php
            $uploadedCount = count($student->documents);
            $totalReq      = count($requiredDocs);
            $missingCount  = count($missingDocs);
            $docPct        = $totalReq > 0 ? round(($uploadedCount / $totalReq) * 100) : 0;
        ?>
        <div class="bg-brand-muted border border-brand-border rounded-2xl p-4 mb-5">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-bold text-brand-text">Document Completion</p>
                <span class="text-sm font-black <?php echo e($missingCount > 0 ? 'text-status-danger' : 'text-status-success'); ?>">
                    <?php echo e($uploadedCount); ?>/<?php echo e($totalReq); ?>

                </span>
            </div>
            <div class="w-full h-2 rounded-full bg-brand-border overflow-hidden">
                <div class="h-full rounded-full <?php echo e($missingCount > 0 ? 'bg-status-danger' : 'bg-status-success'); ?> transition-all"
                     style="width: <?php echo e($docPct); ?>%"></div>
            </div>
            <?php if($missingCount > 0): ?>
            <p class="text-xs text-status-danger mt-2"><?php echo e($missingCount); ?> document(s) still missing.</p>
            <?php else: ?>
            <p class="text-xs text-status-success mt-2">All required documents submitted.</p>
            <?php endif; ?>
        </div>

        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $requiredDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $docType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $isMissing = in_array($docType, $missingDocs);
                $docRecord = $student->documents->firstWhere('document_type', $docType);
                $label     = str_replace('_', ' ', ucwords($docType, '_'));
                $ext       = $docRecord ? strtoupper(pathinfo($docRecord->file_name, PATHINFO_EXTENSION)) : null;
                $fileSize  = $docRecord?->file_size ? round($docRecord->file_size / 1024, 1).' KB' : null;
            ?>
            <div class="rounded-2xl border p-4 <?php echo e($isMissing ? 'bg-status-dangers border-status-danger/20' : 'bg-status-successs border-status-success/20'); ?>">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                                <?php echo e($isMissing ? 'bg-status-danger/20' : 'bg-status-success/20'); ?>">
                        <span class="material-symbols-outlined text-[22px] <?php echo e($isMissing ? 'text-status-danger' : 'text-status-success'); ?>">
                            <?php echo e($isMissing ? 'cancel' : 'check_circle'); ?>

                        </span>
                    </div>
                    <span class="badge <?php echo e($isMissing ? 'badge-danger' : 'badge-success'); ?> text-xs">
                        <?php echo e($isMissing ? 'Missing' : 'Uploaded'); ?>

                    </span>
                </div>
                <p class="text-sm font-bold <?php echo e($isMissing ? 'text-status-danger' : 'text-status-success'); ?>"><?php echo e($label); ?></p>
                <?php if($docRecord): ?>
                    <p class="text-[11px] text-brand-sub mt-1 truncate"><?php echo e($docRecord->file_name); ?></p>
                    <div class="flex items-center gap-2 mt-1">
                        <?php if($ext): ?>
                            <span class="text-[10px] bg-brand-surface text-brand-sub px-2 py-0.5 rounded-md font-semibold"><?php echo e($ext); ?></span>
                        <?php endif; ?>
                        <?php if($fileSize): ?>
                            <span class="text-[10px] text-brand-sub"><?php echo e($fileSize); ?></span>
                        <?php endif; ?>
                        <?php if($docRecord->created_at): ?>
                            <span class="text-[10px] text-brand-sub"><?php echo e($fmtDate($docRecord->created_at)); ?></span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p class="text-[11px] text-status-danger/70 mt-1">Not uploaded yet.</p>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<style>
    .tab-active { color: #6366f1 !important; border-color: #6366f1 !important; }
    .tab-nav-btn:focus { outline: none; }
    .bg-brand-accent\/8 { background-color: rgb(99 102 241 / 0.08); }
</style>
<script>
function switchTab(id) {
    ['profile','finance','documents'].forEach(t => {
        document.getElementById('tab-'+t).classList.add('hidden');
        document.getElementById('btn-'+t).classList.remove('tab-active');
    });
    document.getElementById('tab-'+id).classList.remove('hidden');
    document.getElementById('btn-'+id).classList.add('tab-active');
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/admin/students/show.blade.php ENDPATH**/ ?>