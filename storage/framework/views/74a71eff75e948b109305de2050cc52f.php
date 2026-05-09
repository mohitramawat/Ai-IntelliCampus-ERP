

<?php $__env->startSection('content'); ?>


<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-black text-brand-text">Create New Student</h1>
        <p class="text-sm text-brand-sub mt-0.5">Fill details below. Enrollment number is auto-generated.</p>
    </div>
    <a href="<?php echo e(route('writer.students.pending-documents')); ?>" class="btn-secondary">
        <span class="material-symbols-outlined text-[18px]">folder_open</span>
        Pending Docs
    </a>
</div>


<?php if(session('success')): ?>
    <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 flex items-center gap-3">
        <span class="material-symbols-outlined text-green-600">check_circle</span>
        <p class="text-sm font-semibold text-green-700"><?php echo e(session('success')); ?></p>
    </div>
<?php endif; ?>


<?php if(session('student_created')): ?>
    <?php $cred = session('student_created'); ?>
    <div class="mb-5 rounded-2xl border-2 border-green-300 bg-green-50 overflow-hidden">
        <div class="flex items-center gap-2 bg-green-500 px-4 py-2">
            <span class="material-symbols-outlined text-white text-[20px]">check_circle</span>
            <p class="text-sm font-bold text-white">Student Created Successfully!</p>
            <span class="ml-auto text-xs text-green-100">Hand this slip to the student</span>
        </div>
        <div class="p-4 space-y-2">
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white rounded-xl p-3 border border-green-200">
                    <p class="text-[10px] font-semibold text-brand-sub uppercase tracking-wide">Name</p>
                    <p class="text-sm font-bold text-brand-text mt-0.5"><?php echo e($cred['name']); ?></p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-green-200">
                    <p class="text-[10px] font-semibold text-brand-sub uppercase tracking-wide">Email (Login ID)</p>
                    <p class="text-sm font-bold text-brand-text mt-0.5"><?php echo e($cred['email']); ?></p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-green-200">
                    <p class="text-[10px] font-semibold text-brand-sub uppercase tracking-wide">Enrollment No.</p>
                    <p class="text-sm font-bold text-brand-text mt-0.5"><?php echo e($cred['enrollment']); ?></p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-green-200">
                    <p class="text-[10px] font-semibold text-brand-sub uppercase tracking-wide">Roll Number</p>
                    <p class="text-sm font-bold text-brand-text mt-0.5"><?php echo e($cred['roll']); ?></p>
                </div>
            </div>
            <div class="bg-amber-50 border border-amber-300 rounded-xl p-3 flex items-center gap-3">
                <span class="material-symbols-outlined text-amber-500 text-[22px]">key</span>
                <div>
                    <p class="text-[10px] font-semibold text-amber-700 uppercase tracking-wide">Temporary Password</p>
                    <p class="text-base font-black text-amber-900 tracking-widest mt-0.5 font-mono"><?php echo e($cred['password']); ?></p>
                </div>
                <span class="ml-auto text-[10px] text-amber-600 text-right">
                    Student must change<br>on first login
                </span>
            </div>
            <p class="text-[10px] text-brand-sub text-center">
                ⚠ This password is shown only once. Screenshot or note it before leaving this page.
            </p>
        </div>
    </div>
<?php endif; ?>

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

<form method="POST" action="<?php echo e(route('writer.students.store')); ?>" enctype="multipart/form-data" class="space-y-6">
    <?php echo csrf_field(); ?>

    
    <div class="card">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-brand-border">
            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-500 text-[20px]">lock</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-brand-text">Login Credentials</h2>
                <p class="text-xs text-brand-sub">Student will use these to access the portal</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="label" for="name">Full Name *</label>
                <input id="name" name="name" type="text" value="<?php echo e(old('name')); ?>"
                       class="input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       placeholder="e.g. Mohit Sharma">
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
                <input id="email" name="email" type="email" value="<?php echo e(old('email')); ?>"
                       class="input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       placeholder="mohit@college.edu">
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
        <div class="mt-3 p-3 rounded-xl bg-brand-muted border border-brand-border flex items-center gap-2">
            <span class="material-symbols-outlined text-brand-sub text-[18px]">auto_awesome</span>
            <p class="text-xs text-brand-sub">
                Password is <strong class="text-brand-text">auto-generated</strong> in the format
                <code class="bg-white px-1.5 py-0.5 rounded text-brand-accent font-mono text-xs">COURSE@FirstNameContact</code>
                — e.g. <code class="font-mono text-xs">MCA@Mohit8107233811</code>.
                The student must change it on first login.
            </p>
        </div>
    </div>

    
    <div class="card">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-brand-border">
            <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-green-600 text-[20px]">school</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-brand-text">Academic Details</h2>
                <p class="text-xs text-brand-sub">Select department → course → batch in order</p>
            </div>
        </div>

        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">

            
            <div>
                <label class="label" for="dept_select">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-5 h-5 rounded-full bg-brand-accent text-white text-[10px] font-bold flex items-center justify-center">1</span>
                        Department *
                    </span>
                </label>
                <select id="dept_select"
                        class="input"
                        onchange="filterCourses(this.value)">
                    <option value="">— Select Department —</option>
                </select>
            </div>

            
            <div>
                <label class="label" for="course_select">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-5 h-5 rounded-full bg-brand-accent text-white text-[10px] font-bold flex items-center justify-center">2</span>
                        Course *
                    </span>
                </label>
                <select id="course_select"
                        class="input"
                        disabled
                        onchange="filterBatches(this.value)">
                    <option value="">— Select Department First —</option>
                </select>
            </div>

            
            <div>
                <label class="label" for="batch_select">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-5 h-5 rounded-full bg-brand-accent text-white text-[10px] font-bold flex items-center justify-center">3</span>
                        Batch *
                    </span>
                </label>
                <select id="batch_select"
                        name="batch_id"
                        class="input <?php $__errorArgs = ['batch_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-400 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        disabled
                        onchange="updateEnrollmentPreview()">
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
                <label class="label" for="enrollment_preview">Enrollment No.
                    <span class="text-[10px] text-brand-sub font-normal ml-1">(auto-generated)</span>
                </label>
                <div class="relative">
                    <input id="enrollment_preview" type="text" readonly
                           class="input bg-brand-muted text-brand-sub cursor-not-allowed"
                           placeholder="Select batch to preview…">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2
                                 material-symbols-outlined text-brand-sub text-[16px]">auto_awesome</span>
                </div>
                <p class="mt-1 text-[10px] text-brand-sub">Format: COURSEYEARSEQ — e.g. MCA20240001</p>
            </div>

            
            <div>
                <label class="label" for="roll_number">Roll Number
                    <span class="text-[10px] text-brand-sub font-normal ml-1">(optional — auto if blank)</span>
                </label>
                <input id="roll_number" name="roll_number" type="text" value="<?php echo e(old('roll_number')); ?>"
                       class="input" placeholder="Leave blank to auto-generate">
            </div>

            
            <div>
                <label class="label" for="admission_date">Admission Date *</label>
                <input id="admission_date" name="admission_date" type="date"
                       value="<?php echo e(old('admission_date', date('Y-m-d'))); ?>"
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
                <p class="text-xs text-brand-sub">All fields optional</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="label" for="gender">Gender</label>
                <select id="gender" name="gender" class="input">
                    <option value="">— Select —</option>
                    <option value="male"   <?php echo e(old('gender') === 'male'   ? 'selected' : ''); ?>>Male</option>
                    <option value="female" <?php echo e(old('gender') === 'female' ? 'selected' : ''); ?>>Female</option>
                    <option value="other"  <?php echo e(old('gender') === 'other'  ? 'selected' : ''); ?>>Other</option>
                </select>
            </div>
            <div>
                <label class="label" for="date_of_birth">Date of Birth</label>
                <input id="date_of_birth" name="date_of_birth" type="date"
                       value="<?php echo e(old('date_of_birth')); ?>" class="input">
            </div>
            <div>
                <label class="label" for="category">Category</label>
                <select id="category" name="category" class="input">
                    <option value="">— Select —</option>
                    <?php $__currentLoopData = ['General','OBC','SC','ST','EWS']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat); ?>" <?php echo e(old('category') === $cat ? 'selected' : ''); ?>><?php echo e($cat); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label" for="contact_number">Contact Number</label>
                <input id="contact_number" name="contact_number" type="text"
                       value="<?php echo e(old('contact_number')); ?>" class="input" placeholder="+91 9876543210">
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
                <p class="text-xs text-brand-sub">Optional parent information</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="label" for="father_name">Father's Name</label>
                <input id="father_name" name="father_name" type="text"
                       value="<?php echo e(old('father_name')); ?>" class="input" placeholder="Ramesh Sharma">
            </div>
            <div>
                <label class="label" for="mother_name">Mother's Name</label>
                <input id="mother_name" name="mother_name" type="text"
                       value="<?php echo e(old('mother_name')); ?>" class="input" placeholder="Sunita Sharma">
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
                <p class="text-xs text-brand-sub">Optional residential address</p>
            </div>
        </div>
        <textarea id="address" name="address" rows="2"
                  class="input resize-none w-full"
                  placeholder="House No, Street, City, State, PIN"><?php echo e(old('address')); ?></textarea>
    </div>

    
    <div class="card">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-brand-border">
            <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-red-500 text-[20px]">upload_file</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-brand-text">Document Upload</h2>
                <p class="text-xs text-brand-sub">PDF, JPG, PNG — max 5 MB each. All optional now, tracked as pending.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php $__currentLoopData = [
                ['key' => '10th_marksheet', 'label' => '10th Marksheet',      'req' => true],
                ['key' => '12th_marksheet', 'label' => '12th Marksheet',      'req' => true],
                ['key' => 'aadhaar',        'label' => 'Aadhaar Card',        'req' => true],
                ['key' => 'tc',             'label' => 'Transfer Certificate', 'req' => false],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div>
                <label class="label" for="<?php echo e($doc['key']); ?>">
                    <?php echo e($doc['label']); ?>

                    <?php if($doc['req']): ?>
                        <span class="text-red-500 ml-0.5">*</span>
                        <span class="text-[10px] text-brand-sub font-normal">(required for admission)</span>
                    <?php else: ?>
                        <span class="text-[10px] text-brand-sub font-normal">(optional)</span>
                    <?php endif; ?>
                </label>
                <input id="<?php echo e($doc['key']); ?>"
                       name="<?php echo e($doc['key']); ?>"
                       type="file"
                       accept=".pdf,.jpg,.jpeg,.png"
                       class="block w-full text-sm text-brand-sub border border-brand-border
                              rounded-xl px-3 py-2 bg-brand-muted cursor-pointer
                              file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-600
                              hover:file:bg-brand-accent hover:file:text-white
                              file:transition-colors file:cursor-pointer">
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
        <div class="mt-4 p-3 rounded-xl bg-blue-50 border border-blue-200 flex items-start gap-2">
            <span class="material-symbols-outlined text-blue-500 text-[18px] mt-0.5">info</span>
            <p class="text-xs text-brand-text">
                Documents marked <span class="text-red-500 font-bold">*</span> are required for admission.
                If not uploaded now, the student appears in <strong>Pending Documents</strong>.
            </p>
        </div>
    </div>

    
    <div class="flex flex-col sm:flex-row gap-3 pb-6">
        <button type="submit" class="btn-primary w-full sm:w-auto justify-center px-10 py-3 text-base">
            <span class="material-symbols-outlined text-[20px]">person_add</span>
            Create Student
        </button>
        <a href="<?php echo e(route('writer.dashboard')); ?>" class="btn-secondary w-full sm:w-auto justify-center">
            Cancel
        </a>
    </div>

</form>


<script>
// Full data tree injected from PHP
const TREE = <?php echo $departmentsJson; ?>;

const deptSel   = document.getElementById('dept_select');
const courseSel = document.getElementById('course_select');
const batchSel  = document.getElementById('batch_select');
const enrollPrev= document.getElementById('enrollment_preview');

// Populate Department dropdown on page load
TREE.forEach(dept => {
    const opt = new Option(dept.name, dept.id);
    deptSel.add(opt);
});

// ── Step 1: Department selected → populate Courses ──────────────
function filterCourses(deptId) {
    // Reset downstream
    resetSelect(courseSel, '— Select Department First —');
    resetSelect(batchSel,  '— Select Course First —');
    enrollPrev.value = '';
    courseSel.disabled = true;
    batchSel.disabled  = true;

    if (!deptId) return;

    const dept = TREE.find(d => d.id == deptId);
    if (!dept || !dept.courses.length) {
        courseSel.add(new Option('No courses available', ''));
        return;
    }

    resetSelect(courseSel, '— Select Course —');
    dept.courses.forEach(course => {
        const opt = new Option(`${course.name} (${course.code})`, course.id);
        opt.dataset.code = course.code;
        courseSel.add(opt);
    });
    courseSel.disabled = false;
}

// ── Step 2: Course selected → populate Batches ──────────────────
function filterBatches(courseId) {
    resetSelect(batchSel, '— Select Course First —');
    enrollPrev.value = '';
    batchSel.disabled = true;

    if (!courseId) return;

    // Find course across all depts
    let course = null;
    for (const dept of TREE) {
        course = dept.courses.find(c => c.id == courseId);
        if (course) break;
    }

    if (!course || !course.batches.length) {
        batchSel.add(new Option('No active batches', ''));
        batchSel.disabled = false; // Enable so user can see the 'No active batches' message
        return;
    }

    resetSelect(batchSel, '— Select Batch —');
    course.batches.forEach(batch => {
        const opt = new Option(batch.name, batch.id);
        opt.dataset.batchName = batch.name;
        batchSel.add(opt);
    });
    batchSel.disabled = false;
}

// ── Step 3: Batch selected → show enrollment preview ────────────
function updateEnrollmentPreview() {
    const batchId = batchSel.value;
    if (!batchId) { enrollPrev.value = ''; return; }

    // Find selected course code and batch year
    const courseOpt = courseSel.options[courseSel.selectedIndex];
    const code = courseOpt ? courseOpt.dataset.code : '';

    const batchName = batchSel.options[batchSel.selectedIndex].dataset.batchName || '';
    // Extract start year from batch name (e.g. "MCA 2024-2026" -> "2024")
    const yearMatch = batchName.match(/(\d{4})/);
    const year = yearMatch ? yearMatch[1] : new Date().getFullYear();

    enrollPrev.value = `${code}${year}XXXX  ← auto-assigned on save`;
}

// ── Utility: reset a select to a placeholder ────────────────────
function resetSelect(sel, placeholder) {
    sel.innerHTML = '';
    sel.add(new Option(placeholder, ''));
}

// ── Re-select old values after validation error (blade old()) ───
(function restoreOldValues() {
    const oldBatchId = "<?php echo e(old('batch_id')); ?>";
    if (!oldBatchId) return;

    // Find which dept + course has this batch
    for (const dept of TREE) {
        for (const course of dept.courses) {
            const batch = course.batches.find(b => b.id == oldBatchId);
            if (batch) {
                // Restore dept
                deptSel.value = dept.id;
                filterCourses(dept.id);

                // Restore course
                courseSel.value = course.id;
                filterBatches(course.id);

                // Restore batch
                batchSel.value = oldBatchId;
                updateEnrollmentPreview();
                return;
            }
        }
    }
})();
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/writer/students/create.blade.php ENDPATH**/ ?>