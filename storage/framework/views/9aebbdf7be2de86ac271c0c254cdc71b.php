

<?php $__env->startSection('content'); ?>


<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('writer.students.index')); ?>"
           class="w-9 h-9 rounded-xl bg-brand-muted border border-brand-border flex items-center justify-center
                  text-brand-sub hover:text-brand-text hover:border-brand-accent transition-all">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-black text-brand-text">Update Student Profile</h1>
            <p class="text-sm text-brand-sub mt-0.5">Editing record for <strong><?php echo e($student->user->name); ?></strong> (<?php echo e($student->enrollment_number); ?>)</p>
        </div>
    </div>
</div>


<?php if($errors->any()): ?>
    <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200">
        <div class="flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-red-500 text-[20px]">error</span>
            <p class="text-sm font-bold text-red-600">Please fix the errors below:</p>
        </div>
        <ul class="list-disc list-inside space-y-1">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="text-xs text-red-600"><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('writer.students.update', $student->id)); ?>" enctype="multipart/form-data" class="space-y-6">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    
    <div class="card">
        <div class="flex items-center justify-between mb-5 pb-4 border-b border-brand-border">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-500 text-[20px]">manage_accounts</span>
                </div>
                <div>
                    <h2 class="text-base font-bold text-brand-text">Account Information</h2>
                    <p class="text-xs text-brand-sub">Manage identity and status</p>
                </div>
            </div>
            <div>
                <label class="label mb-1 text-right text-[10px]" for="is_active">Status</label>
                <select name="is_active" id="is_active" class="input py-1.5 min-w-[120px]">
                    <option value="1" <?php echo e(old('is_active', $student->is_active) == 1 ? 'selected' : ''); ?>>Active</option>
                    <option value="0" <?php echo e(old('is_active', $student->is_active) == 0 ? 'selected' : ''); ?>>Inactive</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="label" for="name">Full Name *</label>
                <input id="name" name="name" type="text"
                       value="<?php echo e(old('name', $student->user->name)); ?>"
                       class="input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="label" for="email">Email Address *</label>
                <input id="email" name="email" type="email"
                       value="<?php echo e(old('email', $student->user->email)); ?>"
                       class="input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
    </div>

    
    <div class="card">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-brand-border">
            <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600 text-[20px]">school</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-brand-text">Academic Details</h2>
                <p class="text-xs text-brand-sub">Verify department, course, and enrollment</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="label" for="dept_select">Department *</label>
                <select id="dept_select" class="input" onchange="filterCourses(this.value)">
                    <option value="">— Select Department —</option>
                </select>
            </div>
            <div>
                <label class="label" for="course_select">Course *</label>
                <select id="course_select" class="input" disabled onchange="filterBatches(this.value)">
                    <option value="">— Select Department First —</option>
                </select>
            </div>
            <div>
                <label class="label" for="batch_select">Batch *</label>
                <select id="batch_select" name="batch_id" class="input <?php $__errorArgs = ['batch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" disabled>
                    <option value="">— Select Course First —</option>
                </select>
                <?php $__errorArgs = ['batch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="label" for="enroll">Enrollment No.</label>
                <input id="enroll" type="text" readonly
                       value="<?php echo e($student->enrollment_number); ?>"
                       class="input bg-brand-muted text-brand-sub cursor-not-allowed">
            </div>
            <div>
                <label class="label" for="roll_number">Roll Number</label>
                <input id="roll_number" name="roll_number" type="text"
                       value="<?php echo e(old('roll_number', $student->roll_number)); ?>"
                       class="input" placeholder="e.g. MCA2401">
            </div>
            <div>
                <label class="label" for="admission_date">Admission Date *</label>
                <input id="admission_date" name="admission_date" type="date"
                       value="<?php echo e(old('admission_date', $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('Y-m-d') : '')); ?>"
                       class="input <?php $__errorArgs = ['admission_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['admission_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
    </div>

    
    <div class="card">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-brand-border">
            <div class="w-9 h-9 rounded-xl bg-sky-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-sky-500 text-[20px]">person</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-brand-text">Personal Details</h2>
                <p class="text-xs text-brand-sub">Biographic and demographic information</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="label" for="gender">Gender</label>
                <select id="gender" name="gender" class="input">
                    <option value="">— Select —</option>
                    <?php $__currentLoopData = ['male','female','other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($g); ?>" <?php echo e(old('gender', $student->gender) == $g ? 'selected' : ''); ?>><?php echo e(ucfirst($g)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label" for="date_of_birth">Date of Birth</label>
                <input id="date_of_birth" name="date_of_birth" type="date"
                       value="<?php echo e(old('date_of_birth', $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('Y-m-d') : '')); ?>"
                       class="input">
            </div>
            <div>
                <label class="label" for="category">Category</label>
                <select id="category" name="category" class="input">
                    <option value="">— Select —</option>
                    <?php $__currentLoopData = ['General','OBC','SC','ST','EWS']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat); ?>" <?php echo e(old('category', $student->category) == $cat ? 'selected' : ''); ?>><?php echo e($cat); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label" for="contact_number">Contact Number</label>
                <input id="contact_number" name="contact_number" type="text"
                       value="<?php echo e(old('contact_number', $student->contact_number)); ?>"
                       class="input" placeholder="+91 9876543210">
            </div>
        </div>
    </div>

    
    <div class="card">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-brand-border">
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-500 text-[20px]">family_restroom</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-brand-text">Guardian Details</h2>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="label" for="father_name">Father's Name</label>
                <input id="father_name" name="father_name" type="text"
                       value="<?php echo e(old('father_name', $student->father_name)); ?>"
                       class="input">
            </div>
            <div>
                <label class="label" for="mother_name">Mother's Name</label>
                <input id="mother_name" name="mother_name" type="text"
                       value="<?php echo e(old('mother_name', $student->mother_name)); ?>"
                       class="input">
            </div>
        </div>
    </div>

    
    <div class="card">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-brand-border">
            <div class="w-9 h-9 rounded-xl bg-brand-muted flex items-center justify-center">
                <span class="material-symbols-outlined text-brand-sub text-[20px]">location_on</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-brand-text">Address</h2>
            </div>
        </div>
        <textarea id="address" name="address" rows="2" class="input resize-none w-full"><?php echo e(old('address', $student->address)); ?></textarea>
    </div>

    
    <div class="card">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-brand-border">
            <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-red-500 text-[20px]">upload_file</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-brand-text">Document Management</h2>
                <p class="text-xs text-brand-sub">Upload new files to replace existing ones</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php $__currentLoopData = [
                ['key' => '10th_marksheet', 'label' => '10th Marksheet'],
                ['key' => '12th_marksheet', 'label' => '12th Marksheet'],
                ['key' => 'aadhaar',        'label' => 'Aadhaar Card'],
                ['key' => 'tc',             'label' => 'Transfer Certificate'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $exists = $student->documents->where('document_type', $doc['key'])->first(); ?>
            <div>
                <label class="label" for="<?php echo e($doc['key']); ?>">
                    <?php echo e($doc['label']); ?>

                    <?php if($exists): ?>
                        <span class="text-[10px] text-status-success font-bold ml-1 flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[12px]">check_circle</span> Already Uploaded
                        </span>
                    <?php endif; ?>
                </label>
                <input id="<?php echo e($doc['key']); ?>" name="<?php echo e($doc['key']); ?>" type="file"
                       class="block w-full text-sm text-brand-sub border border-brand-border rounded-xl px-3 py-2 bg-brand-muted">
                <?php $__errorArgs = [$doc['key']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="flex flex-col sm:flex-row gap-3 pb-10">
        <button type="submit" class="btn-primary w-full sm:w-auto justify-center px-10 py-3 text-base">
            <span class="material-symbols-outlined text-[20px]">save</span>
            Save Changes
        </button>
        <a href="<?php echo e(route('writer.students.index')); ?>" class="btn-secondary w-full sm:w-auto justify-center">
            Cancel
        </a>
    </div>

</form>


<script>
const TREE = <?php echo $departmentsJson; ?>;
const deptSel = document.getElementById('dept_select');
const courseSel = document.getElementById('course_select');
const batchSel = document.getElementById('batch_select');

TREE.forEach(dept => deptSel.add(new Option(dept.name, dept.id)));

function filterCourses(deptId, preSelectedCourseId = null) {
    courseSel.innerHTML = '<option value="">— Select Course —</option>';
    batchSel.innerHTML = '<option value="">— Select Course First —</option>';
    courseSel.disabled = true; batchSel.disabled = true;
    if (!deptId) return;
    const dept = TREE.find(d => d.id == deptId);
    if (!dept) return;
    dept.courses.forEach(c => courseSel.add(new Option(c.name, c.id)));
    courseSel.disabled = false;
    if (preSelectedCourseId) {
        courseSel.value = preSelectedCourseId;
        filterBatches(preSelectedCourseId);
    }
}

function filterBatches(courseId, preSelectedBatchId = null) {
    batchSel.innerHTML = '<option value="">— Select Batch —</option>';
    batchSel.disabled = true;
    if (!courseId) return;
    let course = null;
    for (const d of TREE) { course = d.courses.find(c => c.id == courseId); if (course) break; }
    if (!course) return;
    course.batches.forEach(b => batchSel.add(new Option(b.name, b.id)));
    batchSel.disabled = false;
    if (preSelectedBatchId) batchSel.value = preSelectedBatchId;
}

// Initial restoration
(function restore() {
    const currentDeptId  = "<?php echo e($student->batch->course->department_id); ?>";
    const currentCourseId = "<?php echo e($student->batch->course_id); ?>";
    const currentBatchId  = "<?php echo e($student->batch_id); ?>";

    if (currentDeptId) {
        deptSel.value = currentDeptId;
        filterCourses(currentDeptId, currentCourseId);
        filterBatches(currentCourseId, currentBatchId);
    }
})();
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views\writer\students\edit.blade.php ENDPATH**/ ?>