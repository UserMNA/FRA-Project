const video = document.getElementById("video");
const startButton = document.getElementById("start-scan");
const stopButton = document.getElementById("stop-scan");
const statusEl = document.getElementById("status");
const successImg = document.getElementById("success-img");
const wrapper = document.getElementById('video-wrapper');

let successTimerId = null;
let labeledFaceDescriptors;
let faceMatcher;
let canvas = null;
let detectionInterval = null;
let stream;

const MODEL_URL = "./models";
const LABELS_BASE = "./labels";

const STABLE_SECONDS = 3;
const PASS_THRESHOLD = 0.6;
const COOLDOWN_MS = 30_000;

let currentStableLabel = null;
let stableStart = null;
const lastSuccessByLabel = new Map();

(async function init() {
  try {
    await Promise.all([
      faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
      faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
      faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
    ]);
    console.log("Models loaded");

    labeledFaceDescriptors = await getLabeledFaceDescriptions();
    faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors);
    console.log("Face data loaded");

    statusEl.textContent = "Ready. Click Start Scan.";
    startButton.disabled = false;
    stopButton.disabled = true;
  } catch (e) {
    console.error("Init error:", e);
    statusEl.textContent = "Error loading models or labels. Check console.";
  }
})();

startButton.addEventListener("click", startWebcamAndDetection);
stopButton.addEventListener("click", stopWebcamAndDetection);

function startWebcamAndDetection() {
  navigator.mediaDevices
    .getUserMedia({ video: true, audio: false })
    .then((mediaStream) => {
      stream = mediaStream;
      video.srcObject = stream;

      // ensure we don't stack multiple listeners
      video.removeEventListener("play", onPlay);
      video.addEventListener("play", onPlay);

      startButton.disabled = true;
      stopButton.disabled = false;
      statusEl.textContent = "Camera on. Looking for faces…";
      successImg.classList.add("d-none");
    })
    .catch((error) => {
      console.error("Webcam error:", error);
      statusEl.textContent = "Webcam error. See console.";
    });
}

function onPlay() {
  if (canvas) canvas.remove();
  canvas = faceapi.createCanvasFromMedia(video);

  // make sure the canvas never blocks clicks
  canvas.style.position = 'absolute';
  canvas.style.top = '0';
  canvas.style.left = '0';
  canvas.style.pointerEvents = 'none';

  // Prefer appending to the wrapper, so it sits on top of the video only
  wrapper.append(canvas);
  // document.body.append(canvas); // (fallback)

  const displaySize = { width: video.width, height: video.height };
  faceapi.matchDimensions(canvas, displaySize);

  detectionInterval = setInterval(async () => {
    try {
      const detections = await faceapi
        .detectAllFaces(video)
        .withFaceLandmarks()
        .withFaceDescriptors();

      if (!canvas) return; // user might have stopped scan

      const resizedDetections = faceapi.resizeResults(detections, displaySize);
      canvas.getContext("2d").clearRect(0, 0, canvas.width, canvas.height);

      const ctx = canvas.getContext('2d');
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      const results = resizedDetections.map(d =>
        faceMatcher.findBestMatch(d.descriptor)
      );

      // draw
      results.forEach((result, i) => {
        const box = resizedDetections[i].detection.box;
        const drawBox = new faceapi.draw.DrawBox(box, { label: result.toString() });
        drawBox.draw(canvas);
      });

      // ---- stable logic ----
      if (results.length > 0) {
        const best = results[0];
        const label = best.label;
        const distance = best.distance;

        if (label !== "unknown" && distance < PASS_THRESHOLD) {
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
              // await postAttendance(label);  // <- re-enable when your API is ready
              resetStable();
            }
          } else {
            currentStableLabel = label;
            stableStart = now;
            statusEl.textContent = `Detected ${label}… starting timer`;
          }
        } else {
          resetStable("Unknown / low confidence. Waiting…");
        }
      } else {
        resetStable("No face detected");
      }
    } catch (err) {
      console.error("Detection loop error:", err);
      statusEl.textContent = "Detection error. See console.";
    }
  }, 100);
}


function stopWebcamAndDetection() {
  if (detectionInterval) {
    clearInterval(detectionInterval);
    detectionInterval = null;
  }
  if (canvas) {
    canvas.remove();
    canvas = null;
  }
  if (stream) {
    stream.getTracks().forEach((track) => track.stop());
    stream = null;
  }
  video.srcObject = null;

  startButton.disabled = false;
  stopButton.disabled = true;
  statusEl.textContent = "Stopped.";
  resetStable();
}

function resetStable(msg) {
  currentStableLabel = null;
  stableStart = null;
  if (msg) statusEl.textContent = msg;
}

function markAttendanceSuccess(label) {
  statusEl.textContent = `${label} hadir! (kehadiran tercatat — cooldown 30s)`;
  console.log(`${label} success!`);
  // successImg.classList.remove("d-none");
  successImg.src = "safe.jpg";
  successImg.classList.remove("d-none");

  if (successTimerId) clearTimeout(successTimerId);
  successTimerId = setTimeout(() => {
    successImg.classList.add("d-none");
  }, 3000);
}

async function postAttendance(label) {
  const [name, id] = label.split(', '); // Split "Ali, 3323"
  await fetch('/api/attendance', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      employee_id: id,
      name: name,
      label: label,
      confidence: 0.95, // placeholder
      scanned_at: new Date().toISOString()
    })
  });
}

async function getLabeledFaceDescriptions() {
  const labels = ["ali_3323", "aspian_1326", "ikmal_8056", "ikhsan_5196", "daus_4291"];
  return Promise.all(
    labels.map(async (label) => {
      const descriptions = [];
      for (let i = 1; i <= 2; i++) {
        const img = await faceapi.fetchImage(
          `${LABELS_BASE}/${encodeURIComponent(label)}/${i}.jpg`
        );
        const detections = await faceapi
          .detectSingleFace(img)
          .withFaceLandmarks()
          .withFaceDescriptor();
        if (!detections) {
          console.warn(`No face found for ${label}/${i}.jpg`);
          continue;
        }
        descriptions.push(detections.descriptor);
      }
      return new faceapi.LabeledFaceDescriptors(label, descriptions);
    })
  );
}
