<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Face Recognition</title>

  <link rel="stylesheet" href="{{ asset('style.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="d-flex flex-column align-items-center gap-3">
    <div id="video-wrapper" class="position-relative d-inline-block" style="position: relative;">
      <video id="video" width="640" height="480" autoplay muted playsinline></video>
      <img id="success-img" src="{{ asset('safe.jpg') }}" class="d-none" alt="Success"
      style="position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; z-index: 10;">
    </div>
    <div id="status" class="mt-2 text-muted">Loading models…</div>
    
    <div class="d-flex gap-2">
      <button id="start-scan" class="btn btn-primary" type="button" disabled>Start Scan</button>
      <button id="stop-scan" class="btn btn-danger" type="button" disabled>Stop Scan</button>
    </div>
  </div>

  <script defer src="{{ asset('face-api.min.js') }}"></script>
  <script defer src="{{ asset('script.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
