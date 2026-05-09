<?php $__env->startSection('content'); ?>

<style>
:root {
    --us-bg: #F8FAFC; 
    --us-surface: #FFFFFF; 
    --us-border: #E2E8F0; 
    --us-muted: #F1F5F9;
    --us-text: #0F172A; 
    --us-sub: #64748B;
    --us-emerald: #10B981; 
    --us-emeraldD: #059669; 
    --us-emeraldS: #D1FAE5;
}

.us-fade { opacity: 0; transform: translateY(16px); animation: usFade .5s cubic-bezier(.25,.46,.45,.94) var(--d,0s) forwards; }
@keyframes usFade { to { opacity: 1; transform: translateY(0); } }

.us-card {
    background: var(--us-surface); border: 1px solid var(--us-border); border-radius: 24px;
    padding: 40px; position: relative; overflow: hidden; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);
}
.us-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 32px; }
.us-title { font-size: clamp(26px, 4vw, 34px); font-weight: 900; color: var(--us-text); letter-spacing: -0.03em; margin: 0 0 6px; }
.us-subtitle { font-size: 14px; color: var(--us-sub); margin: 0; max-width: 450px; line-height: 1.5; }
.us-beta-badge {
    display: inline-flex; align-items: center; padding: 4px 10px; background: var(--us-emeraldS); border-radius: 99px;
    font-size: 10px; font-weight: 800; color: var(--us-emeraldD); text-transform: uppercase; letter-spacing: 0.1em;
    vertical-align: middle; margin-left: 12px;
}

.us-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 10px;
    padding: 16px 40px; border: none; cursor: pointer; border-radius: 16px;
    font-size: 15px; font-weight: 800; letter-spacing: 0.03em; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.us-btn-scan {
    background: var(--us-emerald); color: #fff; box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);
}
.us-btn-scan:hover {
    transform: translateY(-2px); box-shadow: 0 15px 35px -5px rgba(16, 185, 129, 0.4); background: var(--us-emeraldD);
}

.us-scanner-container {
    position: relative; width: 180px; height: 180px; margin: 0 auto 30px; display: flex; align-items: center; justify-content: center;
}
.us-scanner-ring {
    position: absolute; inset: 0; border-radius: 50%; border: 2px dashed var(--us-emerald); opacity: 0.2;
}
.us-scanner-ring.active {
    animation: spin 8s linear infinite; opacity: 0.5;
}
.us-scanner-core {
    width: 80px; height: 80px; background: var(--us-emeraldS); border-radius: 50%;
    display: flex; align-items: center; justify-content: center; z-index: 10;
    border: 2px solid var(--us-emerald); box-shadow: 0 0 30px rgba(16, 185, 129, 0.3); transition: all 0.3s;
}
.us-scanner-core.active { transform: scale(1.1); box-shadow: 0 0 50px rgba(16, 185, 129, 0.5); }
@keyframes spin { 100% { transform: rotate(360deg); } }

.us-fallback-input {
    width: 140px; padding: 12px; border: 2px solid var(--us-border); border-radius: 12px;
    text-align: center; font-size: 20px; font-weight: 800; letter-spacing: 0.3em;
    font-family: monospace; transition: all 0.2s; outline: none;
}
.us-fallback-input:focus { border-color: var(--us-emerald); box-shadow: 0 0 0 4px var(--us-emeraldS); }
</style>

<div class="us-header us-fade" style="--d: 0.05s">
    <div>
        <h1 class="us-title">Proximity Check-In <span class="us-beta-badge">Beta</span></h1>
        <p class="us-subtitle">Ensure you are seated inside the classroom. The system will detect high-frequency audio tokens emitted by your professor to authenticate your presence.</p>
    </div>
</div>

<div class="us-card us-fade" style="--d: 0.1s; text-align: center; min-height: 400px; display: flex; flex-direction: column; align-items: center; justify-content: center;">

    <?php if(!$activeSession): ?>
        <div style="width: 80px; height: 80px; background: var(--us-muted); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
            <span class="material-symbols-outlined" style="font-size: 36px; color: var(--us-sub);">mic_off</span>
        </div>
        <h2 style="font-size: 22px; font-weight: 800; color: var(--us-text); margin: 0 0 8px;">No Active Broadcast</h2>
        <p style="font-size: 14px; color: var(--us-sub); margin: 0 0 30px;">There is currently no ultrasonic attendance session active for your batch.</p>
        <button onclick="location.reload()" class="us-btn" style="background: var(--us-muted); color: var(--us-text); border: 1px solid var(--us-border);">
            <span class="material-symbols-outlined">refresh</span> Refresh Status
        </button>
    
    <?php elseif($activeSession->is_marked): ?>
        <div style="width: 100px; height: 100px; background: var(--us-emeraldS); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 24px; border: 2px solid var(--us-emerald); box-shadow: 0 0 40px rgba(16, 185, 129, 0.2);">
            <span class="material-symbols-outlined" style="font-size: 48px; color: var(--us-emeraldD);">check_circle</span>
        </div>
        <h2 style="font-size: 32px; font-weight: 900; color: var(--us-emeraldD); margin: 0 0 8px;">VERIFIED</h2>
        <p style="font-size: 15px; font-weight: 600; color: var(--us-sub); margin: 0;">Your presence for <strong><?php echo e($activeSession->subject->code); ?></strong> has been securely verified.</p>
    
    <?php else: ?>
        <div id="scanning-ui" style="width: 100%;">
            <div class="us-scanner-container">
                <div class="us-scanner-ring" id="scan-ring"></div>
                <div class="us-scanner-core" id="scan-core">
                    <span class="material-symbols-outlined" id="scan-icon" style="font-size: 36px; color: var(--us-emeraldD);">hearing</span>
                </div>
            </div>

            <h2 id="status-title" style="font-size: 24px; font-weight: 800; color: var(--us-text); margin: 0 0 8px;">Ready to Authenticate</h2>
            <p id="status-text" style="font-size: 14px; color: var(--us-sub); margin: 0 0 32px; max-width: 300px; margin-left: auto; margin-right: auto;">
                Allow microphone access when prompted to begin scanning for the classroom token.
            </p>

            <button id="btn-scan" onclick="startScanning()" class="us-btn us-btn-scan">
                START SCANNING
            </button>

            <!-- Local testing fallback -->
            <div id="fallback-ui" style="display: none; margin-top: 40px; padding-top: 30px; border-top: 1px solid var(--us-border);">
                <p style="font-size: 12px; font-weight: 700; color: var(--us-sub); text-transform: uppercase; margin-bottom: 12px;">Testing Simulator Fallback</p>
                <div style="display: flex; gap: 10px; justify-content: center; align-items: center;">
                    <input type="text" id="manual-token" placeholder="0000" maxlength="4" class="us-fallback-input">
                    <button onclick="submitAttendance(document.getElementById('manual-token').value)" class="us-btn" style="background: var(--us-text); color: white; padding: 12px 24px; border-radius: 12px;">VERIFY</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php if($activeSession && !$activeSession->is_marked): ?>
<script>
let audioCtx;
let analyser;
let dataArray;
let isScanning = false;
let detectedDigits = [];
let lastDigitTime = 0;

async function startScanning() {
    const btn = document.getElementById('btn-scan');
    const ring = document.getElementById('scan-ring');
    const core = document.getElementById('scan-core');
    const title = document.getElementById('status-title');
    const text = document.getElementById('status-text');
    const fallback = document.getElementById('fallback-ui');

    btn.style.display = 'none';

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        title.textContent = "Mic Blocked (HTTP)";
        title.style.color = "#EF4444";
        text.textContent = "Your browser blocks mic access on local IPs without HTTPS. Please use the simulator below to test.";
        document.getElementById('scan-icon').textContent = 'mic_off';
        fallback.style.display = 'block';
        return;
    }

    try {
        // Request raw audio without echo cancellation (crucial for same-device testing and high frequencies)
        const stream = await navigator.mediaDevices.getUserMedia({ 
            audio: { echoCancellation: false, noiseSuppression: false, autoGainControl: false } 
        });
        
        ring.classList.add('active');
        core.classList.add('active');
        document.getElementById('scan-icon').textContent = 'graphic_eq';
        
        title.textContent = "Listening for Token...";
        text.textContent = "Keep your phone close to the professor's device. Decoding high-frequency audio...";

        // Setup Web Audio API
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        analyser = audioCtx.createAnalyser();
        const source = audioCtx.createMediaStreamSource(stream);
        source.connect(analyser);
        
        analyser.fftSize = 2048; // Good resolution for frequency detection
        dataArray = new Uint8Array(analyser.frequencyBinCount);
        
        isScanning = true;
        detectedDigits = [];
        scanAudio();

        // Timeout fallback after 30 seconds
        setTimeout(() => {
            if (isScanning) {
                isScanning = false;
                title.textContent = "Signal Not Found";
                title.style.color = "#F59E0B";
                text.textContent = "Could not decode the full token. Ensure the teacher's tab is active and volume is up.";
                ring.classList.remove('active');
                core.classList.remove('active');
                document.getElementById('scan-icon').textContent = 'error';
                fallback.style.display = 'block';
                
                // Show a retry button
                btn.style.display = 'inline-flex';
                btn.textContent = "RETRY SCAN";
                
                // Stop mic
                stream.getTracks().forEach(t => t.stop());
                audioCtx.close();
            }
        }, 30000);

    } catch(e) {
        title.textContent = "Permission Denied";
        title.style.color = "#EF4444";
        text.textContent = "Microphone access was denied. Please use the simulator below.";
        document.getElementById('scan-icon').textContent = 'mic_off';
        fallback.style.display = 'block';
    }
}

function scanAudio() {
    if (!isScanning) return;
    requestAnimationFrame(scanAudio);
    
    analyser.getByteFrequencyData(dataArray);
    
    let binWidth = audioCtx.sampleRate / analyser.fftSize;
    let startBin = Math.floor(17800 / binWidth);
    let endBin = Math.floor(20200 / binWidth);
    
    let maxEnergy = 0;
    let peakBin = 0;
    let sumEnergy = 0;
    let countBins = 0;
    
    // Calculate average energy (Noise Floor) and find peak
    for (let i = startBin; i <= endBin; i++) {
        let energy = dataArray[i];
        sumEnergy += energy;
        countBins++;
        if (energy > maxEnergy) {
            maxEnergy = energy;
            peakBin = i;
        }
    }
    
    let averageEnergy = sumEnergy / countBins;
    
    // SMART DETECTION (SNR):
    // Classroom noise (talking, shouting) does not produce 18kHz spikes.
    // Instead of a strict volume limit, we check if the peak is significantly louder than the background hiss.
    // A peak that is +15 above the average noise floor in this band is a valid tone!
    if (maxEnergy > 15 && maxEnergy > averageEnergy + 15) {
        let peakFreq = peakBin * binWidth;
        let digit = Math.round((peakFreq - 18000) / 200);
        
        if (digit >= 0 && digit <= 9) {
            let now = Date.now();
            
            // Accept digit if 400ms passed, OR if buffer is empty
            if (now - lastDigitTime > 400 || detectedDigits.length === 0) {
                detectedDigits.push(digit);
                lastDigitTime = now;
                
                // Sliding window: keep only the latest 4 digits
                if (detectedDigits.length > 4) {
                    detectedDigits.shift();
                }
                
                document.getElementById('status-title').textContent = `Token Decoding: ${detectedDigits.length}/4`;
                document.getElementById('status-title').style.color = "var(--us-emerald)";
                
                if (detectedDigits.length === 4) {
                    isScanning = false;
                    document.getElementById('status-title').textContent = "Authenticating Token...";
                    submitAttendance(detectedDigits.join(''));
                }
            }
        }
    }
}

async function submitAttendance(token) {
    if(!token || token.length !== 4) {
        alert("Enter 4 digit token.");
        return;
    }
    
    try {
        const res = await fetch('<?php echo e(route("student.ultrasonic.mark")); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
            body: JSON.stringify({
                lecture_session_id: <?php echo e($activeSession->id); ?>,
                student_lat: 0, student_long: 0,
                token: token
            })
        });

        if(res.ok) {
            window.location.reload();
        } else {
            // Failed? The sliding window caught a wrong combination.
            // Just clear buffer and resume scanning silently!
            detectedDigits = [];
            isScanning = true;
            document.getElementById('status-title').textContent = "Scanning Environment...";
            document.getElementById('status-title').style.color = "var(--us-text)";
            scanAudio();
        }
    } catch(e) {
        alert("Network error.");
    }
}
</script>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ramaw\Desktop\CollegeProject\IntelliCampus ERP\resources\views/student/ultrasonic/index.blade.php ENDPATH**/ ?>