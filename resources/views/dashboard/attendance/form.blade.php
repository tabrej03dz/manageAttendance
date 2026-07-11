@extends('dashboard.layout.root')

@section('title', $formType === 'check_in' ? 'Check In' : 'Check Out')

@push('styles')
    <style>
        .attendance-punch-page {
            font-family: 'Inter', sans-serif;
            color: #0f172a;
        }

        /*
        |--------------------------------------------------------------------------
        | Attendance Focus Mode
        |--------------------------------------------------------------------------
        | This page hides the normal dashboard top header, page header and extra
        | spacing so the camera starts immediately at the top of the screen.
        */

        body.attendance-focus-mode .main-header,
        body.attendance-focus-mode .content-header,
        body.attendance-focus-mode .top-header,
        body.attendance-focus-mode .page-header,
        body.attendance-focus-mode .app-topbar {
            display: none !important;
        }

        body.attendance-focus-mode .content-wrapper {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        body.attendance-focus-mode .content {
            padding-top: 0 !important;
        }

        body.attendance-focus-mode .content-wrapper > .content {
            padding-top: 0 !important;
        }

        body.attendance-focus-mode .container-fluid {
            padding-top: 0 !important;
        }

        body.attendance-focus-mode .attendance-punch-page {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        @media (max-width: 767px) {
            body.attendance-focus-mode .content-wrapper {
                min-height: 100vh !important;
            }

            body.attendance-focus-mode .attendance-punch-page {
                margin-left: -7px;
                margin-right: -7px;
            }

            body.attendance-focus-mode .attendance-main-card {
                border-top-left-radius: 0;
                border-top-right-radius: 0;
            }
        }

        .attendance-main-card {
            overflow: hidden;
            border: 1px solid #dbe3ee;
            border-radius: 22px;
            background: #ffffff;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.12);
        }

        .camera-section {
            background:
                radial-gradient(
                    circle at top right,
                    rgba(99, 102, 241, 0.10),
                    transparent 36%
                ),
                #ffffff;
        }

        .camera-shell {
            position: relative;
            width: 240px;
            height: 240px;
            margin-inline: auto;
            overflow: hidden;
            border: 8px solid #ffffff;
            border-radius: 999px;
            background: #e2e8f0;
            box-shadow:
                0 0 0 10px rgba(79, 70, 229, 0.16),
                0 18px 40px rgba(15, 23, 42, 0.22);
        }

        .camera-shell video,
        .camera-shell img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .camera-shell video {
            transform: scaleX(-1);
        }

        .camera-shell img {
            display: none;
        }

        .camera-shell.has-image img {
            display: block;
        }

        .camera-shell.has-image video,
        .camera-shell.has-image .camera-placeholder {
            display: none;
        }

        .camera-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #64748b;
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        }

        .camera-status {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 11px;
            font-weight: 800;
        }

        .status-waiting {
            background: #fff7ed;
            color: #c2410c;
        }

        .status-ready {
            background: #ecfdf5;
            color: #047857;
        }

        .status-error {
            background: #fff1f2;
            color: #be123c;
        }

        .action-button {
            min-height: 50px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            width: 100%;
            border: 0;
            border-radius: 14px;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 900;
            text-decoration: none !important;
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                opacity 0.2s ease;
        }

        .action-button:hover {
            transform: translateY(-1px);
        }

        .action-button:disabled {
            cursor: not-allowed;
            opacity: 0.55;
            transform: none;
            box-shadow: none;
        }

        .button-capture {
            background: linear-gradient(135deg, #0f766e, #14b8a6);
            color: #ffffff !important;
            box-shadow: 0 12px 24px rgba(13, 148, 136, 0.22);
        }

        .button-submit {
            background: linear-gradient(135deg, #4338ca, #6366f1);
            color: #ffffff !important;
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.22);
        }

        .button-reset {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #334155 !important;
        }

        .location-box {
            border: 1px solid #c7d2fe;
            border-radius: 16px;
            background: #eef2ff;
        }

        .info-card {
            border: 1px solid #dbe3ee;
            border-radius: 16px;
            background: #f8fafc;
            padding: 16px;
        }

        .info-label {
            color: #64748b;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .info-value {
            margin-top: 4px;
            color: #0f172a;
            font-size: 17px;
            font-weight: 900;
        }

        .bottom-info-card {
            position: relative;
            overflow: hidden;
            border: 1px solid #312e81;
            border-radius: 20px;
            background: linear-gradient(
                135deg,
                #0f172a 0%,
                #172554 55%,
                #312e81 100%
            );
            color: #ffffff;
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.20);
        }

        .bottom-info-card::after {
            content: '';
            position: absolute;
            right: -70px;
            bottom: -100px;
            width: 210px;
            height: 210px;
            border-radius: 999px;
            background: rgba(6, 182, 212, 0.18);
        }

        .permission-alert,
        .error-box {
            border: 1px solid #fecaca;
            border-radius: 16px;
            background: #fff1f2;
            color: #9f1239;
        }

        @media (max-width: 640px) {
            .attendance-main-card {
                border-radius: 18px;
            }

            .camera-shell {
                width: 205px;
                height: 205px;
            }

            .camera-section,
            .attendance-action-section {
                padding: 20px;
            }
        }
    </style>
@endpush

@section('content')
    <script>
        document.body.classList.add('attendance-focus-mode');
    </script>

    <div class="attendance-punch-page space-y-5 pb-10">

        {{-- Location permission warning --}}
        <div
            id="alert-container"
            class="permission-alert hidden p-4"
        >
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-sm font-extrabold">
                        Location Permission Required
                    </h3>

                    <p class="mt-1 text-sm font-medium">
                        Attendance submit करने के लिए browser location permission
                        enable करें।
                    </p>

                    <a
                        href="{{ route('setting.instruction') }}"
                        class="mt-2 inline-flex items-center gap-1 text-sm font-extrabold text-rose-700 underline"
                    >
                        <i class="fas fa-circle-info"></i>
                        See Instructions
                    </a>
                </div>

                <button
                    type="button"
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-rose-600"
                    onclick="document.getElementById('alert-container').classList.add('hidden')"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="error-box p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-triangle-exclamation mt-1"></i>

                    <div>
                        <h3 class="text-sm font-extrabold">
                            Please fix the following:
                        </h3>

                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm font-medium">
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Camera and submit section comes first --}}
        <section class="attendance-main-card">
            <div class="grid grid-cols-1 lg:grid-cols-5">

                {{-- Camera --}}
                <div
                    class="camera-section border-b border-slate-200 p-6 sm:p-8 lg:col-span-3 lg:border-b-0 lg:border-r"
                >
                    <div class="text-center">
                        <p class="text-xs font-extrabold uppercase tracking-widest text-indigo-600">
                            Camera Verification
                        </p>

                        <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">
                            Capture Your Photo
                        </h1>

                        <p class="mt-2 text-sm font-medium text-slate-500">
                            Face को circle के बीच रखें और Capture दबाएँ।
                        </p>
                    </div>

                    <div
                        id="cameraShell"
                        class="camera-shell mt-7"
                    >
                        <video
                            id="video"
                            autoplay
                            playsinline
                            muted
                        ></video>

                        <canvas
                            id="canvas"
                            class="hidden"
                        ></canvas>

                        <img
                            id="imagePreview"
                            alt="Captured attendance photo"
                        >

                        <div class="camera-placeholder">
                            <i class="fas fa-camera text-4xl"></i>

                            <span class="text-xs font-extrabold">
                                Starting camera...
                            </span>
                        </div>
                    </div>

                    <div class="mt-5 text-center">
                        <span
                            id="cameraStatus"
                            class="camera-status status-waiting"
                        >
                            Waiting for camera
                        </span>
                    </div>

                    <div class="mx-auto mt-6 max-w-md space-y-3">
                        <button
                            type="button"
                            id="snap"
                            class="action-button button-capture"
                            disabled
                        >
                            <i class="fas fa-camera"></i>
                            Capture Photo
                        </button>

                        <button
                            type="button"
                            id="retake"
                            class="action-button button-reset hidden"
                        >
                            <i class="fas fa-rotate"></i>
                            Retake Photo
                        </button>

                        <button
                            type="submit"
                            id="upload"
                            form="uploadForm"
                            class="action-button button-submit"
                            disabled
                        >
                            <i class="fas fa-check-circle"></i>

                            Submit
                            {{ $formType === 'check_in'
                                ? 'Check In'
                                : 'Check Out'
                            }}
                        </button>

                        <p class="text-center text-xs font-semibold text-slate-500">
                            Photo capture और location detect होने के बाद Submit button active होगा।
                        </p>
                    </div>
                </div>

                {{-- Submit and location --}}
                <div class="attendance-action-section p-6 sm:p-8 lg:col-span-2">
                    <p class="text-xs font-extrabold uppercase tracking-widest text-indigo-600">
                        {{ $formType === 'check_in' ? 'Check In' : 'Check Out' }}
                    </p>

                    <h2 class="mt-2 text-2xl font-extrabold text-slate-900">
                        Submit Attendance
                    </h2>

                    <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                        Camera section में ऊपर दिया गया Submit button photo capture
                        और location detect होने के बाद automatically active हो जाएगा।
                    </p>

                    <div class="location-box mt-5 p-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-indigo-700"
                            >
                                <i class="fas fa-location-dot"></i>
                            </div>

                            <div>
                                <p class="text-xs font-extrabold uppercase tracking-wider text-indigo-700">
                                    Location Status
                                </p>

                                <p
                                    id="locationStatus"
                                    class="mt-1 text-sm font-bold text-slate-700"
                                >
                                    Detecting your current location...
                                </p>

                                <p
                                    id="distanceText"
                                    class="mt-1 text-xs font-semibold text-slate-500"
                                ></p>
                            </div>
                        </div>
                    </div>

                    <form
                        id="uploadForm"
                        class="mt-5"
                        action="{{ $formType === 'check_in'
                            ? route('attendance.check_in', ['user' => $user ?? null])
                            : route('attendance.check_out', ['user' => $user ?? null]) }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >
                        @csrf

                        <div class="hidden">
                            <input
                                type="file"
                                id="capturedImage"
                                name="image"
                                accept="image/png,image/jpeg"
                            >

                            <input
                                type="text"
                                id="latitude"
                                name="latitude"
                            >

                            <input
                                type="text"
                                id="longitude"
                                name="longitude"
                            >

                            <input
                                type="text"
                                id="distance"
                                name="distance"
                            >
                        </div>

                    </form>

                    <a
                        href="{{ route('attendance.form', ['form_type' => $formType]) }}"
                        class="action-button button-reset mt-3"
                    >
                        <i class="fas fa-rotate-left"></i>
                        Reset
                    </a>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <div class="info-card">
                            <p class="info-label">
                                Current Time
                            </p>

                            <p class="info-value current-time">
                                {{ now()->format('h:i A') }}
                            </p>
                        </div>

                        <div class="info-card">
                            <p class="info-label">
                                Total Hours
                            </p>

                            <p class="info-value total-hours">
                                --:--
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const imagePreview = document.getElementById('imagePreview');
            const cameraShell = document.getElementById('cameraShell');
            const cameraStatus = document.getElementById('cameraStatus');
            const snapButton = document.getElementById('snap');
            const retakeButton = document.getElementById('retake');
            const uploadButton = document.getElementById('upload');
            const uploadForm = document.getElementById('uploadForm');
            const capturedImageInput = document.getElementById('capturedImage');
            const locationStatus = document.getElementById('locationStatus');
            const distanceText = document.getElementById('distanceText');
            const alertContainer = document.getElementById('alert-container');

            const userOffice = @json(auth()->user()->office);

            let cameraStream = null;
            let imageCaptured = false;
            let locationReady = false;

            function setCameraStatus(message, type = 'waiting') {
                cameraStatus.textContent = message;
                cameraStatus.className = 'camera-status';

                if (type === 'ready') {
                    cameraStatus.classList.add('status-ready');
                } else if (type === 'error') {
                    cameraStatus.classList.add('status-error');
                } else {
                    cameraStatus.classList.add('status-waiting');
                }
            }

            function updateSubmitButton() {
                uploadButton.disabled = !(imageCaptured && locationReady);
            }

            async function startCamera() {
                try {
                    if (
                        !navigator.mediaDevices ||
                        !navigator.mediaDevices.getUserMedia
                    ) {
                        throw new Error(
                            'Camera is not supported in this browser.'
                        );
                    }

                    cameraStream =
                        await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: 'user',
                                width: {
                                    ideal: 1280
                                },
                                height: {
                                    ideal: 720
                                }
                            },
                            audio: false
                        });

                    video.srcObject = cameraStream;

                    await video.play();

                    snapButton.disabled = false;

                    setCameraStatus(
                        'Camera ready',
                        'ready'
                    );
                } catch (error) {
                    console.error(
                        'Camera error:',
                        error
                    );

                    snapButton.disabled = true;

                    setCameraStatus(
                        'Camera permission denied or unavailable',
                        'error'
                    );
                }
            }

            function stopCamera() {
                if (!cameraStream) {
                    return;
                }

                cameraStream
                    .getTracks()
                    .forEach(function (track) {
                        track.stop();
                    });

                cameraStream = null;
            }

            snapButton.addEventListener('click', function () {
                if (
                    !video.videoWidth ||
                    !video.videoHeight
                ) {
                    alert(
                        'Camera is still loading. Please wait a moment.'
                    );

                    return;
                }

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                const context =
                    canvas.getContext('2d');

                context.save();

                context.translate(
                    canvas.width,
                    0
                );

                context.scale(
                    -1,
                    1
                );

                context.drawImage(
                    video,
                    0,
                    0,
                    canvas.width,
                    canvas.height
                );

                context.restore();

                canvas.toBlob(
                    function (blob) {
                        if (!blob) {
                            alert(
                                'Unable to capture photo. Please try again.'
                            );

                            return;
                        }

                        const imageFile = new File(
                            [blob],
                            'attendance-photo.png',
                            {
                                type: 'image/png'
                            }
                        );

                        const dataTransfer =
                            new DataTransfer();

                        dataTransfer.items.add(
                            imageFile
                        );

                        capturedImageInput.files =
                            dataTransfer.files;

                        imagePreview.src =
                            URL.createObjectURL(blob);

                        cameraShell.classList.add(
                            'has-image'
                        );

                        imageCaptured = true;

                        snapButton.classList.add(
                            'hidden'
                        );

                        retakeButton.classList.remove(
                            'hidden'
                        );

                        setCameraStatus(
                            'Photo captured successfully',
                            'ready'
                        );

                        updateSubmitButton();

                        stopCamera();
                    },
                    'image/png',
                    0.92
                );
            });

            retakeButton.addEventListener(
                'click',
                async function () {
                    imageCaptured = false;

                    capturedImageInput.value = '';

                    imagePreview.removeAttribute(
                        'src'
                    );

                    cameraShell.classList.remove(
                        'has-image'
                    );

                    retakeButton.classList.add(
                        'hidden'
                    );

                    snapButton.classList.remove(
                        'hidden'
                    );

                    snapButton.disabled = true;

                    setCameraStatus(
                        'Restarting camera...',
                        'waiting'
                    );

                    updateSubmitButton();

                    await startCamera();
                }
            );

            function degreesToRadians(degrees) {
                return degrees * (Math.PI / 180);
            }

            function calculateDistance(
                latitudeFrom,
                longitudeFrom,
                latitudeTo,
                longitudeTo
            ) {
                const earthRadius = 6371000;

                const fromLatitude =
                    degreesToRadians(latitudeFrom);

                const fromLongitude =
                    degreesToRadians(longitudeFrom);

                const toLatitude =
                    degreesToRadians(latitudeTo);

                const toLongitude =
                    degreesToRadians(longitudeTo);

                const latitudeDifference =
                    toLatitude - fromLatitude;

                const longitudeDifference =
                    toLongitude - fromLongitude;

                const calculation =
                    Math.sin(
                        latitudeDifference / 2
                    ) ** 2 +
                    Math.cos(fromLatitude) *
                    Math.cos(toLatitude) *
                    Math.sin(
                        longitudeDifference / 2
                    ) ** 2;

                const angularDistance =
                    2 *
                    Math.atan2(
                        Math.sqrt(calculation),
                        Math.sqrt(
                            1 - calculation
                        )
                    );

                return earthRadius * angularDistance;
            }

            function detectLocation() {
                if (!navigator.geolocation) {
                    locationStatus.textContent =
                        'Geolocation is not supported by this browser.';

                    locationStatus.classList.add(
                        'text-rose-700'
                    );

                    alertContainer.classList.remove(
                        'hidden'
                    );

                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        const latitude =
                            position.coords.latitude;

                        const longitude =
                            position.coords.longitude;

                        document.getElementById(
                            'latitude'
                        ).value = latitude;

                        document.getElementById(
                            'longitude'
                        ).value = longitude;

                        let officeDistance = 0;

                        if (
                            userOffice &&
                            userOffice.latitude !== null &&
                            userOffice.longitude !== null
                        ) {
                            officeDistance =
                                calculateDistance(
                                    parseFloat(
                                        userOffice.latitude
                                    ),
                                    parseFloat(
                                        userOffice.longitude
                                    ),
                                    latitude,
                                    longitude
                                );
                        }

                        document.getElementById(
                            'distance'
                        ).value =
                            officeDistance.toFixed(2);

                        locationReady = true;

                        locationStatus.textContent =
                            'Location detected successfully';

                        locationStatus.classList.remove(
                            'text-rose-700'
                        );

                        locationStatus.classList.add(
                            'text-emerald-700'
                        );

                        distanceText.textContent =
                            userOffice
                                ? `Office distance: ${Math.round(
                                      officeDistance
                                  )} metres`
                                : 'Office location is not configured.';

                        updateSubmitButton();
                    },
                    function (error) {
                        console.error(
                            'Location error:',
                            error
                        );

                        locationReady = false;

                        locationStatus.textContent =
                            'Location permission denied or location unavailable.';

                        locationStatus.classList.add(
                            'text-rose-700'
                        );

                        distanceText.textContent =
                            'Enable location permission and reload this page.';

                        alertContainer.classList.remove(
                            'hidden'
                        );

                        updateSubmitButton();
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 0
                    }
                );
            }

            uploadForm.addEventListener(
                'submit',
                function (event) {
                    if (!imageCaptured) {
                        event.preventDefault();

                        alert(
                            'Please capture your photo first.'
                        );

                        return;
                    }

                    if (!locationReady) {
                        event.preventDefault();

                        alert(
                            'Please allow location access before submitting attendance.'
                        );

                        return;
                    }

                    uploadButton.disabled = true;

                    uploadButton.innerHTML =
                        '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                }
            );

            const currentTimeElements =
                document.querySelectorAll(
                    '.current-time'
                );

            const currentDateElements =
                document.querySelectorAll(
                    '.current-date'
                );

            function updateCurrentTime() {
                const currentDateTime =
                    new Date();

                const currentTime =
                    currentDateTime.toLocaleTimeString(
                        'en-IN',
                        {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: true
                        }
                    );

                const currentDate =
                    currentDateTime.toLocaleDateString(
                        'en-IN',
                        {
                            year: 'numeric',
                            month: 'short',
                            day: '2-digit',
                            weekday: 'long'
                        }
                    );

                currentTimeElements.forEach(
                    function (element) {
                        element.textContent =
                            currentTime;
                    }
                );

                currentDateElements.forEach(
                    function (element) {
                        element.textContent =
                            currentDate;
                    }
                );
            }

            updateCurrentTime();

            setInterval(
                updateCurrentTime,
                1000
            );

            startCamera();

            detectLocation();

            window.addEventListener(
                'beforeunload',
                function () {
                    stopCamera();

                    document.body.classList.remove(
                        'attendance-focus-mode'
                    );
                }
            );
        });
    </script>
@endpush