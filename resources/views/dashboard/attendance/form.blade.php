@extends('dashboard.layout.root')

@section('title', $formType === 'check_in' ? 'Check In' : 'Check Out')

@push('styles')
    <style>
        .attendance-page {
            font-family: 'Inter', sans-serif;
            color: #0f172a;
        }

        body.attendance-focus-mode .main-header,
        body.attendance-focus-mode .content-header,
        body.attendance-focus-mode .top-header,
        body.attendance-focus-mode .page-header,
        body.attendance-focus-mode .app-topbar {
            display: none !important;
        }

        body.attendance-focus-mode .content-wrapper,
        body.attendance-focus-mode .content,
        body.attendance-focus-mode .container-fluid {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        .attendance-card {
            overflow: hidden;
            border: 1px solid #dbe3ee;
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.14);
        }

        .camera-section {
            background:
                radial-gradient(
                    circle at top right,
                    rgba(99, 102, 241, 0.12),
                    transparent 35%
                ),
                #ffffff;
        }

        .camera-shell {
            position: relative;
            width: 245px;
            height: 245px;
            margin: 0 auto;
            overflow: hidden;
            border: 8px solid #ffffff;
            border-radius: 999px;
            background: #e2e8f0;
            box-shadow:
                0 0 0 10px rgba(79, 70, 229, 0.15),
                0 20px 42px rgba(15, 23, 42, 0.22);
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
            display: block;
            transform: scaleX(-1);
        }

        .camera-shell img {
            display: none;
        }

        .camera-shell.fallback-active video {
            display: none;
        }

        .camera-shell.fallback-active img {
            display: block;
        }

        .camera-placeholder {
            position: absolute;
            inset: 0;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #64748b;
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        }

        .camera-shell.camera-ready .camera-placeholder,
        .camera-shell.fallback-active .camera-placeholder {
            display: none;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 8px 13px;
            border-radius: 999px;
            font-size: 12px;
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

        .attendance-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            width: 100%;
            min-height: 50px;
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

        .attendance-button:hover {
            transform: translateY(-1px);
        }

        .attendance-button:disabled {
            cursor: not-allowed;
            opacity: 0.55;
            transform: none;
            box-shadow: none;
        }

        .capture-button {
            background: linear-gradient(135deg, #0f766e, #14b8a6);
            color: #ffffff !important;
            box-shadow: 0 12px 25px rgba(13, 148, 136, 0.24);
        }

        .submit-button {
            background: linear-gradient(135deg, #4338ca, #6366f1);
            color: #ffffff !important;
            box-shadow: 0 12px 25px rgba(79, 70, 229, 0.24);
        }

        .reset-button {
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #334155 !important;
        }

        .location-box {
            border: 1px solid #c7d2fe;
            border-radius: 18px;
            background: #eef2ff;
        }

        .location-box.location-success {
            border-color: #a7f3d0;
            background: #ecfdf5;
        }

        .location-box.location-error {
            border-color: #fecaca;
            background: #fff1f2;
        }

        .warning-box,
        .validation-box {
            border: 1px solid #fda4af;
            border-radius: 16px;
            background: #fff1f2;
            color: #9f1239;
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
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .info-value {
            margin-top: 5px;
            color: #0f172a;
            font-size: 17px;
            font-weight: 900;
        }

        @media (max-width: 767px) {
            body.attendance-focus-mode .attendance-page {
                margin-right: -7px;
                margin-left: -7px;
            }

            .attendance-card {
                border-top-left-radius: 0;
                border-top-right-radius: 0;
            }
        }

        @media (max-width: 640px) {
            .camera-shell {
                width: 205px;
                height: 205px;
            }

            .camera-section,
            .attendance-detail-section {
                padding: 20px !important;
            }
        }
    </style>
@endpush

@section('content')
    <script>
        document.body.classList.add('attendance-focus-mode');
    </script>

    <div class="attendance-page space-y-5 pb-10">

        {{-- Location Warning --}}
        <div
            id="locationWarning"
            class="warning-box hidden p-4"
        >
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-rose-600">
                        <i class="fas fa-location-dot"></i>
                    </div>

                    <div>
                        <h3 class="text-sm font-extrabold">
                            Location is turned off or unavailable
                        </h3>

                        <p class="mt-1 text-sm font-medium leading-6">
                            Please turn on location for accurate attendance verification.
                        </p>

                        @if(Route::has('setting.instruction'))
                            <a
                                href="{{ route('setting.instruction') }}"
                                class="mt-2 inline-flex items-center gap-1 text-sm font-extrabold text-rose-700 underline"
                            >
                                <i class="fas fa-circle-info"></i>
                                Location Instructions
                            </a>
                        @endif
                    </div>
                </div>

                <button
                    type="button"
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-rose-600"
                    onclick="document.getElementById('locationWarning').classList.add('hidden')"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="validation-box p-4">
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

        {{-- Success Message --}}
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-700">
                <i class="fas fa-circle-check mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <section class="attendance-card">
            <div class="grid grid-cols-1 lg:grid-cols-5">

                {{-- Camera Section --}}
                <div class="camera-section border-b border-slate-200 p-6 sm:p-8 lg:col-span-3 lg:border-b-0 lg:border-r">
                    <div class="text-center">
                        <p class="text-xs font-extrabold uppercase tracking-widest text-indigo-600">
                            Camera Verification
                        </p>

                        <h1 class="mt-2 text-2xl font-extrabold text-slate-900 sm:text-3xl">
                            {{ $formType === 'check_in'
                                ? 'Mark Your Check In'
                                : 'Mark Your Check Out'
                            }}
                        </h1>
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
                            id="fallbackPreview"
                            alt="Attendance verification image"
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
                            class="status-badge status-waiting"
                        >
                            <i class="fas fa-spinner fa-spin"></i>
                            Starting camera
                        </span>
                    </div>

                    <div class="mx-auto mt-6 max-w-md space-y-3">
                        <button
                            type="button"
                            id="captureButton"
                            class="attendance-button capture-button"
                        >
                            <i class="fas fa-camera"></i>

                            <span id="captureButtonText">
                                Capture Photo
                            </span>
                        </button>

                        <button
                            type="submit"
                            id="submitButton"
                            form="attendanceForm"
                            class="attendance-button submit-button"
                            disabled
                        >
                            <i class="fas fa-check-circle"></i>

                            <span>
                                Submit
                                {{ $formType === 'check_in'
                                    ? 'Check In'
                                    : 'Check Out'
                                }}
                            </span>
                        </button>

                        <p class="text-center text-xs font-semibold leading-5 text-slate-500">
                            If the camera is unavailable, clicking Capture will
                            generate a fallback verification image.
                        </p>
                    </div>
                </div>

                {{-- Attendance Details --}}
                <div class="attendance-detail-section p-6 sm:p-8 lg:col-span-2">
                    <p class="text-xs font-extrabold uppercase tracking-widest text-indigo-600">
                        {{ $formType === 'check_in' ? 'Check In' : 'Check Out' }}
                    </p>

                    <h2 class="mt-2 text-2xl font-extrabold text-slate-900">
                        Attendance Details
                    </h2>

                    <p class="mt-2 text-sm font-medium leading-6 text-slate-500">
                        Attendance can still be submitted if the camera or
                        location is unavailable.
                    </p>

                    <div
                        id="locationBox"
                        class="location-box mt-5 p-4"
                    >
                        <div class="flex items-start gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-indigo-700">
                                <i
                                    id="locationIcon"
                                    class="fas fa-location-dot"
                                ></i>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-extrabold uppercase tracking-wider text-indigo-700">
                                    Location Status
                                </p>

                                <p
                                    id="locationStatus"
                                    class="mt-1 text-sm font-bold text-slate-700"
                                >
                                    Detecting location...
                                </p>

                                <p
                                    id="distanceText"
                                    class="mt-1 text-xs font-semibold leading-5 text-slate-500"
                                ></p>

                                <button
                                    type="button"
                                    id="retryLocationButton"
                                    class="mt-3 inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-xs font-extrabold text-indigo-700 shadow-sm"
                                >
                                    <i class="fas fa-location-crosshairs"></i>
                                    Retry Location
                                </button>
                            </div>
                        </div>
                    </div>

                    <form
                        id="attendanceForm"
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

                            <input
                                type="hidden"
                                id="locationAvailable"
                                name="location_available"
                                value="0"
                            >

                            <input
                                type="hidden"
                                id="cameraAvailable"
                                name="camera_available"
                                value="0"
                            >

                            <input
                                type="hidden"
                                id="fallbackImageUsed"
                                name="fallback_image_used"
                                value="0"
                            >
                        </div>
                    </form>

                    <a
                        href="{{ route('attendance.form', ['form_type' => $formType]) }}"
                        class="attendance-button reset-button mt-4"
                    >
                        <i class="fas fa-rotate-left"></i>
                        Reset Complete Form
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
                                Location
                            </p>

                            <p
                                id="locationShortStatus"
                                class="info-value"
                            >
                                Checking
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
            const video =
                document.getElementById('video');

            const canvas =
                document.getElementById('canvas');

            const fallbackPreview =
                document.getElementById('fallbackPreview');

            const cameraShell =
                document.getElementById('cameraShell');

            const cameraStatus =
                document.getElementById('cameraStatus');

            const captureButton =
                document.getElementById('captureButton');

            const captureButtonText =
                document.getElementById('captureButtonText');

            const submitButton =
                document.getElementById('submitButton');

            const attendanceForm =
                document.getElementById('attendanceForm');

            const capturedImageInput =
                document.getElementById('capturedImage');

            const latitudeInput =
                document.getElementById('latitude');

            const longitudeInput =
                document.getElementById('longitude');

            const distanceInput =
                document.getElementById('distance');

            const locationAvailableInput =
                document.getElementById('locationAvailable');

            const cameraAvailableInput =
                document.getElementById('cameraAvailable');

            const fallbackImageUsedInput =
                document.getElementById('fallbackImageUsed');

            const locationWarning =
                document.getElementById('locationWarning');

            const locationBox =
                document.getElementById('locationBox');

            const locationStatus =
                document.getElementById('locationStatus');

            const locationShortStatus =
                document.getElementById('locationShortStatus');

            const distanceText =
                document.getElementById('distanceText');

            const locationIcon =
                document.getElementById('locationIcon');

            const retryLocationButton =
                document.getElementById('retryLocationButton');

            const userOffice =
                @json(auth()->user()->office);

            const employeeName =
                @json(($user ?? auth()->user())->name ?? 'Employee');

            const attendanceType =
                @json($formType === 'check_in' ? 'CHECK IN' : 'CHECK OUT');

            let cameraStream = null;
            let cameraAvailable = false;
            let imageCaptured = false;
            let locationReady = false;
            let isSubmitting = false;

            /*
            |--------------------------------------------------------------------------
            | Camera Status
            |--------------------------------------------------------------------------
            */

            function setCameraStatus(message, type = 'waiting') {
                cameraStatus.className =
                    'status-badge';

                let icon =
                    'fas fa-spinner fa-spin';

                if (type === 'ready') {
                    cameraStatus.classList.add(
                        'status-ready'
                    );

                    icon =
                        'fas fa-circle-check';
                } else if (type === 'error') {
                    cameraStatus.classList.add(
                        'status-error'
                    );

                    icon =
                        'fas fa-triangle-exclamation';
                } else {
                    cameraStatus.classList.add(
                        'status-waiting'
                    );
                }

                cameraStatus.innerHTML =
                    `<i class="${icon}"></i><span>${message}</span>`;
            }

            function updateSubmitButton() {
                submitButton.disabled =
                    !imageCaptured || isSubmitting;
            }

            /*
            |--------------------------------------------------------------------------
            | Camera
            |--------------------------------------------------------------------------
            */

            async function startCamera() {
                try {
                    if (
                        !navigator.mediaDevices ||
                        !navigator.mediaDevices.getUserMedia
                    ) {
                        throw new Error(
                            'Camera is not supported.'
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

                    video.srcObject =
                        cameraStream;

                    await video.play();

                    cameraAvailable = true;
                    cameraAvailableInput.value = '1';

                    cameraShell.classList.add(
                        'camera-ready'
                    );

                    cameraShell.classList.remove(
                        'fallback-active'
                    );

                    setCameraStatus(
                        'Camera ready',
                        'ready'
                    );
                } catch (error) {
                    console.error(
                        'Camera unavailable:',
                        error
                    );

                    cameraAvailable = false;
                    cameraAvailableInput.value = '0';

                    setCameraStatus(
                        'Camera unavailable - fallback capture available',
                        'error'
                    );

                    captureButtonText.textContent =
                        'Capture Image';
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

            /*
            |--------------------------------------------------------------------------
            | Image Capture
            |--------------------------------------------------------------------------
            */

            function saveBlobAsInput(
                blob,
                filename,
                fallbackUsed
            ) {
                const imageFile =
                    new File(
                        [blob],
                        filename,
                        {
                            type: 'image/jpeg'
                        }
                    );

                const dataTransfer =
                    new DataTransfer();

                dataTransfer.items.add(
                    imageFile
                );

                capturedImageInput.files =
                    dataTransfer.files;

                imageCaptured = true;

                fallbackImageUsedInput.value =
                    fallbackUsed ? '1' : '0';

                captureButtonText.textContent =
                    fallbackUsed
                        ? 'Capture Again'
                        : 'Capture Again';

                updateSubmitButton();
            }

            function captureCameraPhoto() {
                if (
                    !video.videoWidth ||
                    !video.videoHeight
                ) {
                    createFallbackImage();

                    return;
                }

                canvas.width =
                    video.videoWidth;

                canvas.height =
                    video.videoHeight;

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
                            createFallbackImage();

                            return;
                        }

                        saveBlobAsInput(
                            blob,
                            'attendance-camera-photo.jpg',
                            false
                        );

                        setCameraStatus(
                            'Photo captured successfully',
                            'ready'
                        );
                    },
                    'image/jpeg',
                    0.90
                );
            }

            function createFallbackImage() {
                const fallbackCanvas =
                    document.createElement('canvas');

                fallbackCanvas.width = 900;
                fallbackCanvas.height = 900;

                const context =
                    fallbackCanvas.getContext('2d');

                const gradient =
                    context.createLinearGradient(
                        0,
                        0,
                        900,
                        900
                    );

                gradient.addColorStop(
                    0,
                    '#0f172a'
                );

                gradient.addColorStop(
                    0.55,
                    '#312e81'
                );

                gradient.addColorStop(
                    1,
                    '#0f766e'
                );

                context.fillStyle =
                    gradient;

                context.fillRect(
                    0,
                    0,
                    900,
                    900
                );

                context.fillStyle =
                    'rgba(255,255,255,0.12)';

                context.beginPath();

                context.arc(
                    720,
                    150,
                    210,
                    0,
                    Math.PI * 2
                );

                context.fill();

                context.textAlign =
                    'center';

                context.fillStyle =
                    '#ffffff';

                context.font =
                    'bold 56px Arial';

                context.fillText(
                    attendanceType,
                    450,
                    250
                );

                context.font =
                    'bold 42px Arial';

                context.fillText(
                    employeeName,
                    450,
                    350
                );

                context.font =
                    '30px Arial';

                const currentDate =
                    new Date();

                context.fillText(
                    currentDate.toLocaleDateString(
                        'en-IN',
                        {
                            weekday: 'long',
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric'
                        }
                    ),
                    450,
                    440
                );

                context.fillText(
                    currentDate.toLocaleTimeString(
                        'en-IN',
                        {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: true
                        }
                    ),
                    450,
                    500
                );

                context.font =
                    'bold 28px Arial';

                context.fillStyle =
                    '#99f6e4';

                context.fillText(
                    'CAMERA UNAVAILABLE',
                    450,
                    610
                );

                context.font =
                    '24px Arial';

                context.fillStyle =
                    '#e2e8f0';

                context.fillText(
                    'System-generated attendance verification',
                    450,
                    665
                );

                fallbackCanvas.toBlob(
                    function (blob) {
                        if (!blob) {
                            alert(
                                'The verification image could not be created.'
                            );

                            return;
                        }

                        saveBlobAsInput(
                            blob,
                            'attendance-fallback-image.jpg',
                            true
                        );

                        fallbackPreview.src =
                            URL.createObjectURL(blob);

                        cameraShell.classList.add(
                            'fallback-active'
                        );

                        setCameraStatus(
                            'Fallback verification image created',
                            'ready'
                        );
                    },
                    'image/jpeg',
                    0.90
                );
            }

            captureButton.addEventListener(
                'click',
                function () {
                    if (
                        cameraAvailable &&
                        video.videoWidth &&
                        video.videoHeight
                    ) {
                        captureCameraPhoto();
                    } else {
                        createFallbackImage();
                    }
                }
            );

            /*
            |--------------------------------------------------------------------------
            | Distance Calculation
            |--------------------------------------------------------------------------
            */

            function degreesToRadians(degrees) {
                return degrees *
                    (Math.PI / 180);
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
                        Math.sqrt(1 - calculation)
                    );

                return earthRadius *
                    angularDistance;
            }

            /*
            |--------------------------------------------------------------------------
            | Location
            |--------------------------------------------------------------------------
            */

            function clearLocationValues() {
                latitudeInput.value = '';
                longitudeInput.value = '';
                distanceInput.value = '';
                locationAvailableInput.value = '0';
                locationReady = false;
            }

            function locationFailed(message) {
                clearLocationValues();

                locationBox.classList.remove(
                    'location-success'
                );

                locationBox.classList.add(
                    'location-error'
                );

                locationStatus.textContent =
                    message;

                locationStatus.classList.remove(
                    'text-emerald-700'
                );

                locationStatus.classList.add(
                    'text-rose-700'
                );

                locationShortStatus.textContent =
                    'Unavailable';

                locationShortStatus.classList.remove(
                    'text-emerald-700'
                );

                locationShortStatus.classList.add(
                    'text-rose-700'
                );

                locationIcon.className =
                    'fas fa-location-dot';

                distanceText.textContent = '';

                locationWarning.classList.remove(
                    'hidden'
                );
            }

            function locationSuccess(
                latitude,
                longitude
            ) {
                latitudeInput.value =
                    latitude;

                longitudeInput.value =
                    longitude;

                locationAvailableInput.value =
                    '1';

                locationReady = true;

                locationBox.classList.remove(
                    'location-error'
                );

                locationBox.classList.add(
                    'location-success'
                );

                locationStatus.textContent =
                    'Location detected successfully';

                locationStatus.classList.remove(
                    'text-rose-700'
                );

                locationStatus.classList.add(
                    'text-emerald-700'
                );

                locationShortStatus.textContent =
                    'Available';

                locationShortStatus.classList.remove(
                    'text-rose-700'
                );

                locationShortStatus.classList.add(
                    'text-emerald-700'
                );

                locationIcon.className =
                    'fas fa-circle-check';

                locationWarning.classList.add(
                    'hidden'
                );

                const officeLatitude =
                    parseFloat(
                        userOffice?.latitude
                    );

                const officeLongitude =
                    parseFloat(
                        userOffice?.longitude
                    );

                if (
                    !Number.isNaN(officeLatitude) &&
                    !Number.isNaN(officeLongitude)
                ) {
                    const officeDistance =
                        calculateDistance(
                            officeLatitude,
                            officeLongitude,
                            latitude,
                            longitude
                        );

                    distanceInput.value =
                        officeDistance.toFixed(2);

                    distanceText.textContent =
                        `Office distance: ${Math.round(
                            officeDistance
                        )} metres`;
                } else {
                    distanceInput.value = '';

                    distanceText.textContent =
                        'Office location is not configured.';
                }
            }

            function detectLocation() {
                clearLocationValues();

                locationStatus.textContent =
                    'Detecting location...';

                locationShortStatus.textContent =
                    'Checking';

                locationIcon.className =
                    'fas fa-spinner fa-spin';

                if (!navigator.geolocation) {
                    locationFailed(
                        'Location is not supported by this browser.'
                    );

                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        locationSuccess(
                            position.coords.latitude,
                            position.coords.longitude
                        );
                    },

                    function (error) {
                        console.error(
                            'Location error:',
                            error
                        );

                        let message =
                            'Location is currently unavailable.';

                        if (error.code === 1) {
                            message =
                                'Location permission was denied.';
                        } else if (error.code === 2) {
                            message =
                                'Your current location could not be determined.';
                        } else if (error.code === 3) {
                            message =
                                'The location request timed out.';
                        }

                        locationFailed(message);
                    },

                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            }

            retryLocationButton.addEventListener(
                'click',
                detectLocation
            );

            /*
            |--------------------------------------------------------------------------
            | Attendance Form
            |--------------------------------------------------------------------------
            */

            attendanceForm.addEventListener(
                'submit',
                function (event) {
                    if (isSubmitting) {
                        event.preventDefault();

                        return;
                    }

                    if (!imageCaptured) {
                        event.preventDefault();

                        createFallbackImage();

                        alert(
                            'A verification image has been created. Please press Submit again.'
                        );

                        return;
                    }

                    isSubmitting = true;

                    submitButton.disabled = true;

                    submitButton.innerHTML =
                        '<i class="fas fa-spinner fa-spin"></i><span>Submitting Attendance...</span>';
                }
            );

            /*
            |--------------------------------------------------------------------------
            | Current Time
            |--------------------------------------------------------------------------
            */

            function updateCurrentTime() {
                const currentTime =
                    new Date().toLocaleTimeString(
                        'en-IN',
                        {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: true
                        }
                    );

                document
                    .querySelectorAll(
                        '.current-time'
                    )
                    .forEach(function (element) {
                        element.textContent =
                            currentTime;
                    });
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