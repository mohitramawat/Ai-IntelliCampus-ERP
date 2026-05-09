<?php $__env->startSection('content'); ?>

<style>
    :root {
        --pf-bg: #F8FAFC; 
        --pf-surface: #FFFFFF; 
        --pf-border: #E2E8F0; 
        --pf-accent: #6366F1; /* Indigo */
        --pf-accent-soft: #EEF2FF;
        --pf-text: #0F172A; 
        --pf-sub: #64748B;
        --pf-success: #10B981;
    }

    .pf-card {
        background: var(--pf-surface);
        border: 1px solid var(--pf-border);
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);
        max-width: 600px;
        margin: 0 auto;
        text-align: center;
    }

    .pf-avatar-wrapper {
        position: relative;
        width: 160px;
        height: 160px;
        margin: 0 auto 32px;
    }

    .pf-avatar {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 0 0 4px var(--pf-accent-soft);
        background: var(--pf-bg);
    }

    .pf-btn-upload {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: var(--pf-accent);
        color: white;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 3px solid #fff;
        transition: all 0.3s;
    }
    .pf-btn-upload:hover { transform: scale(1.1); background: #4F46E5; }

    .pf-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 24px;
    }
    .status-verified { background: #D1FAE5; color: #065F46; }
    .status-pending { background: #FEF3C7; color: #92400E; }

    #ai-loader {
        display: none;
        margin-top: 20px;
    }
    .spinner {
        width: 24px;
        height: 24px;
        border: 3px solid var(--pf-accent-soft);
        border-top-color: var(--pf-accent);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        display: inline-block;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="py-12 px-6">
    <div class="pf-card" x-data="profileManager()">
        <h1 class="text-3xl font-black text-slate-900 mb-2">Biometric Enrollment</h1>
        <p class="text-slate-500 mb-8">Upload a clear front-facing selfie to enable AI-powered attendance verification.</p>

        <div class="pf-avatar-wrapper">
            <img :src="previewUrl || '<?php echo e($student->profile_picture ? asset('storage/'.$student->profile_picture) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&size=160&background=EEF2FF&color=6366F1'); ?>'" 
                 class="pf-avatar" id="profile-img">
            <label for="upload-input" class="pf-btn-upload">
                <span class="material-symbols-outlined" style="font-size: 20px;">photo_camera</span>
            </label>
            <input type="file" id="upload-input" @change="handleFileUpload" class="hidden" accept="image/*">
        </div>

        <template x-if="hasDescriptor">
            <div class="pf-status-badge status-verified">
                <span class="material-symbols-outlined" style="font-size: 16px;">verified</span>
                AI Face Data Synced
            </div>
        </template>
        <template x-if="!hasDescriptor">
            <div class="pf-status-badge status-pending">
                <span class="material-symbols-outlined" style="font-size: 16px;">error</span>
                Face Scan Required
            </div>
        </template>

        <div id="ai-loader" :style="{ display: loading ? 'block' : 'none' }">
            <div class="flex flex-col items-center gap-3">
                <div class="spinner"></div>
                <p class="text-sm font-bold text-slate-600" x-text="loadingText"></p>
            </div>
        </div>

        <div x-show="errorMsg" class="mt-4 p-4 bg-red-50 text-red-600 rounded-xl text-sm font-bold" x-text="errorMsg" x-cloak></div>
        
        <div x-show="successMsg" class="mt-4 p-4 bg-emerald-50 text-emerald-600 rounded-xl text-sm font-bold" x-text="successMsg" x-cloak></div>

        <div class="mt-8 pt-8 border-t border-slate-100 text-left">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Enrollment Instructions</h3>
            <ul class="space-y-3">
                <li class="flex items-start gap-3 text-sm text-slate-600">
                    <span class="material-symbols-outlined text-indigo-500" style="font-size: 18px;">face</span>
                    <span>Face must be clearly visible and centered.</span>
                </li>
                <li class="flex items-start gap-3 text-sm text-slate-600">
                    <span class="material-symbols-outlined text-indigo-500" style="font-size: 18px;">light_mode</span>
                    <span>Avoid heavy shadows or backlit environments.</span>
                </li>
                <li class="flex items-start gap-3 text-sm text-slate-600">
                    <span class="material-symbols-outlined text-indigo-500" style="font-size: 18px;">no_photography</span>
                    <span>Do not wear hats, sunglasses, or masks.</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('profileManager', () => ({
        previewUrl: null,
        loading: false,
        loadingText: 'Initializing AI...',
        errorMsg: '',
        successMsg: '',
        hasDescriptor: <?php echo e($student->face_descriptor ? 'true' : 'false'); ?>,
        modelsLoaded: false,

        async init() {
            try {
                this.loading = true;
                this.loadingText = "Loading AI Models...";
                // Using a public model repository for convenience
                const MODEL_URL = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights';
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
                ]);
                this.modelsLoaded = true;
                this.loading = false;
            } catch (e) {
                this.errorMsg = "Failed to load AI models. Please check your connection.";
                this.loading = false;
            }
        },

        async handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.errorMsg = '';
            this.successMsg = '';
            this.loading = true;
            this.loadingText = "Analyzing Facial Features...";

            // Show preview
            this.previewUrl = URL.createObjectURL(file);

            // Wait for image to load
            const img = await faceapi.bufferToImage(file);
            
            // AI Detection
            const detection = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions())
                                          .withFaceLandmarks()
                                          .withFaceDescriptor();

            if (!detection) {
                this.errorMsg = "Face not detected! Please use a clearer photo with good lighting.";
                this.loading = false;
                return;
            }

            // Successfully detected. Now upload both image and descriptor.
            this.loadingText = "Saving to Secure Vault...";
            const formData = new FormData();
            formData.append('profile_picture', file);
            formData.append('face_descriptor', JSON.stringify(Array.from(detection.descriptor)));
            formData.append('_token', '<?php echo e(csrf_token()); ?>');

            try {
                const res = await fetch('<?php echo e(route("student.profile.update")); ?>', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: formData
                });
                const data = await res.json();
                
                if (data.success) {
                    this.successMsg = data.message;
                    this.hasDescriptor = true;
                    // Update image to the permanent storage URL
                    this.previewUrl = data.path;
                } else {
                    this.errorMsg = data.message || "Validation failed. Image might be too large.";
                }
            } catch (e) {
                this.errorMsg = "Server error. Please try again.";
            } finally {
                this.loading = false;
            }
        }
    }));
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/student/profile/index.blade.php ENDPATH**/ ?>