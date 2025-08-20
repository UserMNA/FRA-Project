// DOM Elements
const video = document.getElementById("video");
const startButton = document.getElementById("start-scan");
const stopButton = document.getElementById("stop-scan");
const statusEl = document.getElementById("status");
const successImg = document.getElementById("success-img");
const wrapper = document.getElementById('video-wrapper');

// State Variables
let labeledFaceDescriptors;
let faceMatcher;
let canvas = null;
let detectionInterval = null;
let stream;
let currentStableLabel = null;
let stableStart = null;

// Constants
const MODEL_URL = "./models";
const LABELS_BASE = "./labels";
const STABLE_SECONDS = 3;
const PASS_THRESHOLD = 0.6;
const COOLDOWN_MS = 30_000;
const lastSuccessByLabel = new Map();

// --- Initialization ---
(async function init() {
    try {
        await Promise.all([
            faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
        ]);
        labeledFaceDescriptors = await getLabeledFaceDescriptions();
        faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);

        statusEl.textContent = "Ready. Click Start Scan.";
        startButton.disabled = false;
        stopButton.disabled = true;
    } catch (e) {
        console.error("Init error:", e);
        statusEl.textContent = "Error loading models or labels.";
    }
})();

// --- Event Listeners ---
startButton.addEventListener("click", startWebcamAndDetection);
stopButton.addEventListener("click", stopWebcamAndDetection);
video.addEventListener("play", onPlay);

// --- Core Functions ---

async function startWebcamAndDetection() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
        video.srcObject = stream;
        startButton.disabled = true;
        stopButton.disabled = false;
        successImg.classList.add("d-none");
        statusEl.textContent = "Camera on. Looking for faces…";
    } catch (error) {
        console.error("Webcam error:", error);
        statusEl.textContent = "Webcam error. See console.";
    }
}

function stopWebcamAndDetection() {
    clearInterval(detectionInterval);
    detectionInterval = null;
    canvas?.remove();
    canvas = null;
    stream?.getTracks().forEach(track => track.stop());
    stream = null;
    video.srcObject = null;
    startButton.disabled = false;
    stopButton.disabled = true;
    statusEl.textContent = "Stopped.";
    resetStable();
}

async function onPlay() {
    if (!video.srcObject) return; // Prevent multiple canvas creations

    canvas = faceapi.createCanvasFromMedia(video);
    wrapper.append(canvas);
    Object.assign(canvas.style, { position: 'absolute', top: '0', left: '0', pointerEvents: 'none' });

    const displaySize = { width: video.width, height: video.height };
    faceapi.matchDimensions(canvas, displaySize);

    detectionInterval = setInterval(async () => {
        try {
            const detections = await faceapi.detectAllFaces(video).withFaceLandmarks().withFaceDescriptors();
            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            const ctx = canvas.getContext("2d");
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor));

            results.forEach((result, i) => {
                const box = resizedDetections[i].detection.box;
                new faceapi.draw.DrawBox(box, { label: result.toString() }).draw(canvas);
            });

            handleDetectionResult(results);
        } catch (err) {
            console.error("Detection loop error:", err);
            statusEl.textContent = "Detection error. See console.";
            stopWebcamAndDetection();
        }
    }, 100);
}

function handleDetectionResult(results) {
    if (results.length === 0 || results[0].label === "unknown" || results[0].distance >= PASS_THRESHOLD) {
        resetStable("No face detected / low confidence. Waiting…");
        return;
    }

    const { label } = results[0];
    const now = Date.now();
    const last = lastSuccessByLabel.get(label) || 0;
    const diff = now - last;

    if (diff < COOLDOWN_MS) {
        const remain = ((COOLDOWN_MS - diff) / 1000).toFixed(1);
        statusEl.textContent = `${label} on cooldown (${remain}s left)…`;
        resetStable();
        return;
    }

    if (currentStableLabel === label) {
        if (!stableStart) stableStart = now;
        const elapsed = (now - stableStart) / 1000;
        statusEl.textContent = `Detected ${label} for ${elapsed.toFixed(1)}s…`;
        if (elapsed >= STABLE_SECONDS) {
            lastSuccessByLabel.set(label, now);
            markAttendanceSuccess(label);
            resetStable();
        }
    } else {
        currentStableLabel = label;
        stableStart = now;
        statusEl.textContent = `Detected ${label}… starting timer`;
    }
}

function resetStable(msg) {
    currentStableLabel = null;
    stableStart = null;
    if (msg) statusEl.textContent = msg;
}

function markAttendanceSuccess(label) {
    statusEl.textContent = `${label} hadir! (kehadiran tercatat — cooldown 30s)`;
    successImg.src = "safe.jpg";
    successImg.classList.remove("d-none");
    successImg.classList.add("show");

    const [name, employeeId] = label.split('_');
    saveAttendance(name, employeeId);

    clearTimeout(successTimerId);
    successTimerId = setTimeout(() => {
        successImg.classList.remove("show");
        successImg.classList.add("d-none");
    }, 3000);
}

async function getLabeledFaceDescriptions() {
    const labels = [
        "Ali_9925", "Aspian_9924", "Pirdaus_9923", "Fadil_9914",
        "Ikhsan_9911", "Ikmal_9919", "Lutfi_9920"
    ];
    const descriptors = await Promise.all(
        labels.map(async (label) => {
            const img = await faceapi.fetchImage(`/labels/${label}.JPG`);
            const detections = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
            return detections ? new faceapi.LabeledFaceDescriptors(label, [detections.descriptor]) : null;
        })
    );
    return descriptors.filter(Boolean); // Filter out null values
}

async function saveAttendance(name, employeeId) {
    const payload = {
        name,
        employee_id: employeeId,
        label: `${name}_${employeeId}`,
        confidence: 1,
        scanned_at: new Date().toISOString(),
    };

    try {
        const response = await fetch('http://127.0.0.1:8000/api/attendance', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        const data = await response.json();
        console.log('Attendance submitted:', data);
    } catch (err) {
        console.error('Error submitting attendance:', err);
    }
}