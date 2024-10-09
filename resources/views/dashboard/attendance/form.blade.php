{{-- @extends('dashboard.layout.root')

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <h1 class="text-center mb-4">Capture Image from Camera</h1>

                    @if ($errors->any())
                        <ul>
                            @foreach ($errors->all() as $e)
                                <li class="text-danger">{{ $e }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <video id="video" class="w-100 border border-secondary rounded mirror" autoplay></video>
                                <canvas id="canvas" class="d-none"></canvas>
                                <img id="imagePreview" class="d-none img-fluid border border-secondary rounded mt-3" alt="Captured image" />
                            </div>

                            <div class="d-grid gap-2">
                                <button id="snap" class="btn btn-primary btn-full">Capture</button>
                            </div>

                            <form
                                action="{{ $formType == 'check_in' ? route('attendance.check_in') : route('attendance.check_out') }}"
                                method="POST" enctype="multipart/form-data" id="uploadForm" class="mt-3">
                                @csrf
                                <div class="mb-3 d-none">
                                    <input type="file" id="capturedImage" name="image" class="form-control">
                                    <input type="text" name="latitude" id="latitude" placeholder="Latitude">
                                    <input type="text" name="longitude" id="longitude" placeholder="Longitude">
                                    <input type="text" name="distance" id="distance">
                                </div>
                                <div class="d-grid">
                                    <button type="submit" id="upload" class="btn btn-success btn-full">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .mirror {
            transform: scaleX(-1);
        }

        .btn-full {
            width: 100%;
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
        }

        @media (max-width: 767.98px) {
            .btn-full {
                width: 100%;
                padding: 1rem; /* Slightly larger padding on mobile for better touch target */
                font-size: 1.125rem; /* Larger font size on mobile for better readability */
            }
        }
    </style>

    @php
        $userOffice = json_encode(auth()->user()->office);
    @endphp

    <script>
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(function(stream) {
                var video = document.getElementById('video');
                video.srcObject = stream;
                video.play();
            })
            .catch(function(err) {
                console.log("An error occurred: " + err);
            });

        document.getElementById('snap').addEventListener('click', function() {
            var canvas = document.getElementById('canvas');
            var video = document.getElementById('video');
            var imagePreview = document.getElementById('imagePreview');
            var context = canvas.getContext('2d');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            // Apply mirror effect manually
            context.save();
            context.scale(-1, 1);
            context.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
            context.restore();

            var dataURL = canvas.toDataURL('image/png');

            video.classList.add('d-none');
            imagePreview.classList.remove('d-none');
            imagePreview.src = dataURL;

            fetch(dataURL)
                .then(res => res.blob())
                .then(blob => {
                    var file = new File([blob], 'capturedImage.png', {
                        type: 'image/png'
                    });

                    var dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);

                    var input = document.getElementById('capturedImage');
                    input.files = dataTransfer.files;
                });
        });

        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            var input = document.getElementById('capturedImage');
            if (input.files.length === 0) {
                e.preventDefault();
                alert('Please capture an image first.');
            }
        });
    </script>

    <script>
        var userOffice = @json(auth()->user()->office);
        window.onload = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    // document.getElementById('latitude').value = position.coords.latitude;
                    // document.getElementById('longitude').value = position.coords.longitude;
                    let latitude = position.coords.latitude;
                    let longitude = position.coords.longitude;
                    let distance = haversineDistance(userOffice.latitude, userOffice.longitude, latitude, longitude);

                    document.getElementById('distance').value = distance;
                    // const apiKey = '41b12ec59af34ece8d9e93f4d49e76f1';
                    //
                    //
                    // let apiUrl = https://api.opencagedata.com/geocode/v1/json?key=41b12ec59af34ece8d9e93f4d49e76f1&q=${latitude},${longitude}&pretty=1;
                    // fetch(apiUrl)
                    //     .then(response => response.json())
                    //     .then(data => {
                    //         console.log(data.results[0].components);
                    //     })
                    //     .catch(error => {
                    //         console.error('Error:', error);
                    //     });


                }, function(error) {
                    console.error("Error getting coordinates: ", error);
                });
            } else {
                console.error("Geolocation is not supported by this browser.");
            }
        }
    </script>

    <script>
        function haversineDistance(latitudeFrom, longitudeFrom, latitudeTo, longitudeTo) {
            const earthRadius = 6371000; // Earth radius in meters

            // Convert latitude and longitude from degrees to radians
            const latFrom = degreesToRadians(latitudeFrom);
            const lonFrom = degreesToRadians(longitudeFrom);
            const latTo = degreesToRadians(latitudeTo);
            const lonTo = degreesToRadians(longitudeTo);

            // Haversine formula
            const latDelta = latTo - latFrom;
            const lonDelta = lonTo - lonFrom;
            const a = Math.sin(latDelta / 2) * Math.sin(latDelta / 2) +
                Math.cos(latFrom) * Math.cos(latTo) *
                Math.sin(lonDelta / 2) * Math.sin(lonDelta / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            // Distance in meters
            const distance = earthRadius * c;
            return distance;
        }

        function degreesToRadians(degrees) {
            return degrees * (Math.PI / 180);
        }
    </script>

@endsection --}}


{{-- Check In & Check Out  --}}

{{-- @extends('dashboard.layout.root')

@section('content')
    <!-- Add Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Add Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        .punch-circle {
            box-shadow: 0 0 0 10px rgba(239, 68, 68, 0.3);
            position: relative;
            overflow: hidden;
        }

        .punch-circle video,
        .punch-circle img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scaleX(-1);
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            object-fit: cover;
        }
    </style>

    <div class="min-h-screen bg-red-50 flex flex-col pb-20">
        <!-- Main Content -->
        <main class="flex-grow flex flex-col items-center justify-center p-4">
            <!-- Time Display -->
            <div class="text-center mb-8">
                <h1 class="text-sm current-date">Oct 05, 2024 - Saturday</h1>
                <h2 class="text-5xl font-bold mb-2 current-time">11:28 AM</h2>
                <p>Current Time</p>
            </div>

            <!-- Punch Circle -->
            <div class="punch-circle w-48 h-48 rounded-full bg-white mx-auto mb-8 flex items-center justify-center cursor-pointer transition-all duration-300 hover:shadow-lg"
                id="punchCircle">
                <video id="video" autoplay class="hidden"></video>
                <canvas id="canvas" class="hidden"></canvas>
                <img id="capturedImage" alt="Captured Image" class="hidden" />
                <div id="cameraIcon" class="text-red-500">
                    <i class="fas fa-camera fa-3x"></i>
                </div>
            </div>

            <!-- Status Info -->
            <div class="flex justify-around w-full mb-8">
                <div class="text-center">
                    <i class="fas fa-sign-in-alt text-red-500 mb-2 fa-2x"></i>
                    <p class="mb-0 check-in-time font-bold text-red-700">--:--</p>
                    <small>Check In</small>
                </div>
                <div class="text-center">
                    <i class="fas fa-clock text-red-500 mb-2 fa-2x"></i>
                    <p class="mb-0 total-hours font-bold text-red-700">--:--</p>
                    <small>Total Hours</small>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="w-full max-w-xs">
                <button id="captureButton"
                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-3 md:py-3 md:px-4 rounded-full w-full mb-3 flex items-center justify-center"
                    onclick="toggleCamera()">
                    <i class="fas fa-camera mr-2"></i> <span id="captureButtonText">Start Camera</span>
                </button>
                <button id="submitButton"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 md:py-3 md:px-4 rounded-full w-full mb-3 flex items-center justify-center"
                    onclick="submitImage()">
                    <i class="fas fa-check mr-2"></i> Submit
                </button>
                <button id="resetButton"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-3 md:py-3 md:px-4 rounded-full w-full flex items-center justify-center"
                    onclick="resetCapture()">
                    <i class="fas fa-redo mr-2"></i> Reset
                </button>
            </div>
        </main>
    </div>

    <!-- Mobile Navigation Bar with Hover Effects & Elevated Design -->
    <nav class="bg-gradient-to-r from-red-600 to-red-500 fixed bottom-0 left-0 right-0 shadow-2xl md:hidden rounded-t-2xl">
        <div class="flex justify-around py-4">
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">home</span>
                <span class="text-xs text-white mt-1">Home</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">list</span>
                <span class="text-xs text-white mt-1">My Request</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">folder</span>
                <span class="text-xs text-white mt-1">Records</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">access_time</span>
                <span class="text-xs text-white mt-1">Attendance</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">account_circle</span>
                <span class="text-xs text-white mt-1">Account</span>
            </a>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkInTime = document.querySelector('.check-in-time');
            const totalHours = document.querySelector('.total-hours');
            const currentTime = document.querySelector('.current-time');
            const currentDate = document.querySelector('.current-date');
            const punchCircle = document.getElementById('punchCircle');
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const capturedImage = document.getElementById('capturedImage');
            const cameraIcon = document.getElementById('cameraIcon');
            const captureButton = document.getElementById('captureButton');
            const captureButtonText = document.getElementById('captureButtonText');

            let isImageCaptured = false;
            let stream = null;

            function updateCurrentTime() {
                const now = new Date();
                currentTime.textContent = now.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                currentDate.textContent = now.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: '2-digit',
                    weekday: 'long'
                });
            }

            setInterval(updateCurrentTime, 1000);
            updateCurrentTime();

            function toggleCamera() {
                if (video.classList.contains('hidden')) {
                    startCamera();
                } else {
                    captureImage();
                }
            }

            function startCamera() {
                navigator.mediaDevices.getUserMedia({
                        video: true
                    })
                    .then(videoStream => {
                        stream = videoStream;
                        video.srcObject = stream;
                        video.classList.remove('hidden');
                        cameraIcon.classList.add('hidden');
                        capturedImage.classList.add('hidden');
                        captureButtonText.textContent = 'Capture Image';
                    })
                    .catch(error => {
                        console.error("Error accessing the camera: ", error);
                    });
            }

            function captureImage() {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                capturedImage.src = canvas.toDataURL('image/png');
                video.classList.add('hidden');
                capturedImage.classList.remove('hidden');
                isImageCaptured = true;
                checkInTime.textContent = new Date().toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                captureButtonText.textContent = 'Retake Photo';

                // Stop the video stream
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
            }

            function submitImage() {
                if (isImageCaptured) {
                    alert("Image submitted successfully!");
                } else {
                    alert("Please capture an image first.");
                }
            }

            function resetCapture() {
                video.classList.add('hidden');
                capturedImage.classList.add('hidden');
                cameraIcon.classList.remove('hidden');
                isImageCaptured = false;
                checkInTime.textContent = "--:--";
                totalHours.textContent = "--:--";
                captureButtonText.textContent = 'Start Camera';

                // Stop the video stream if it's active
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
            }

            captureButton.addEventListener('click', toggleCamera);
            punchCircle.addEventListener('click', toggleCamera);

            window.submitImage = submitImage;
            window.resetCapture = resetCapture;
        });
    </script>
@endsection --}}



{{-- Attendance History --}}


{{-- @extends('dashboard.layout.root')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        #calendar {
            max-width: 100%;
            margin: 0 auto;
            border-radius: 15px;
            overflow: hidden;
            border: none;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            padding: 10px;
            background-color: white;
        }

        .calendar-card {
            border-radius: 15px;
            background-color: white;
            padding: 20px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
        }

        .calendar-header h5 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #000;
            margin: 0;
        }

        .date-info {
            display: flex;
            align-items: center;
            background-color: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
            justify-content: space-between;
        }

        .badge {
            font-size: 1.5rem;
            padding: 10px;
            border-radius: 10px;
            margin-right: 15px;
        }

        .badge.bg-success {
            background-color: #4caf50;
            color: white;
        }

        .badge.bg-warning {
            background-color: #ff9800;
            color: white;
        }

        .badge.bg-danger {
            background-color: #f44336;
            color: white;
        }

        .punch-info {
            font-weight: bold;
            display: block;
            color: #666;
            font-size: 0.9rem;
        }

        .total-hours {
            font-size: 1rem;
            color: #333;
            font-weight: bold;
        }

        @media (max-width: 576px) {
            .calendar-header h5 {
                font-size: 1.4rem;
            }

            .date-info {
                padding: 10px;
            }

            .badge {
                padding: 8px;
                font-size: 1.4rem;
            }
        }
    </style>

    <div class="container mt-3">
        <div id="calendar"></div>
    </div>

    <div class="container mt-3">
        <div class="calendar-card">
            <div class="calendar-header">
                <h5>Attendance History</h5>
                <button class="btn btn-link text-secondary">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
            </div>

            <div class="date-info">
                <div class="badge bg-success text-white">06</div>
                <div>
                    <span class="punch-info">Punch In: 09:08 AM</span>
                    <span class="punch-info">Punch Out: 05:06 PM</span>
                </div>
                <div class="total-hours">Total Hours: 08:13</div>
            </div>

            <div class="date-info">
                <div class="badge bg-warning text-white">07</div>
                <div>
                    <span class="punch-info">Punch In: 09:08 AM</span>
                    <span class="punch-info">Punch Out: 05:06 PM</span>
                </div>
                <div class="total-hours">Total Hours: 08:13</div>
            </div>

            <div class="date-info">
                <div class="badge bg-danger text-white">08</div>
                <div>
                    <span class="punch-info">Punch In: 09:08 AM</span>
                    <span class="punch-info">Punch Out: 05:06 PM</span>
                </div>
                <div class="total-hours">Total Hours: 08:13</div>
            </div>

            <div class="date-info">
                <div class="badge bg-success text-white">09</div>
                <div>
                    <span class="punch-info">Punch In: 09:08 AM</span>
                    <span class="punch-info">Punch Out: 05:06 PM</span>
                </div>
                <div class="total-hours">Total Hours: 08:13</div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var holidays = [{
                    title: 'New Year\'s Day',
                    start: '2024-01-01'
                },
                {
                    title: 'Independence Day',
                    start: '2024-07-04'
                },
                {
                    title: 'Thanksgiving',
                    start: '2024-11-28'
                },
                {
                    title: 'Christmas',
                    start: '2024-12-25'
                },
            ];

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: ''
                },
                height: 'auto',
                contentHeight: 'auto',
                events: holidays,
                eventColor: '#ff5733',
            });

            calendar.render();
        });
    </script>
@endsection --}}



{{-- Add Leave Request --}}

{{-- @extends('dashboard.layout.root')

@section('content')
<!-- Add Tailwind CSS -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<!-- Add Google Material Icons CSS -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="flex flex-col h-screen bg-gray-100">
    <!-- Header -->
    <header class="py-4 px-4 text-center border-b border-gray-300">
        <h1 class="text-2xl font-bold text-gray-800">My Requests</h1>
    </header>
    

    <!-- Main Content -->
    <main class="flex-grow overflow-y-auto px-4 py-6 mb-16">
        <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center justify-center h-full">
            <span class="material-icons text-gray-400 text-6xl mb-4">
                chat_bubble_outline
            </span>
            <p class="text-gray-600 mb-6 text-center">No requests to display</p>
            <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full shadow-md transition duration-300 ease-in-out transform hover:scale-105 active:scale-95">
                + Add leave request
            </button>
        </div>
    </main>

    <!-- Mobile Navigation Bar -->
    <nav class="bg-white border-t fixed bottom-0 left-0 right-0">
        <div class="flex justify-around">
            <a href="#" class="group flex flex-col items-center py-2">
                <span class="material-icons text-gray-400 group-hover:text-red-600">home</span>
                <span class="text-xs text-gray-400 group-hover:text-red-600">Home</span>
            </a>
            <a href="#" class="group flex flex-col items-center py-2">
                <span class="material-icons text-red-600">list</span>
                <span class="text-xs text-red-600">My request</span>
            </a>
            <a href="#" class="group flex flex-col items-center py-2">
                <span class="material-icons text-gray-400 group-hover:text-red-600">schedule</span>
                <span class="text-xs text-gray-400 group-hover:text-red-600">Take a break</span>
            </a>
            <a href="#" class="group flex flex-col items-center py-2">
                <span class="material-icons text-gray-400 group-hover:text-red-600">person</span>
                <span class="text-xs text-gray-400 group-hover:text-red-600">Profile</span>
            </a>
        </div>
    </nav>
</div>

@endsection --}}

{{-- @extends('dashboard.layout.root')

@section('content')
    <!-- Add Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Add Flatpickr CSS -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    
    <div class="pt-10">
        <!-- Card Container with border -->
        <div class="w-full max-w-screen-lg mx-auto p-6 bg-white rounded-lg shadow-lg border border-red-500">
            <div class="mb-6 text-center">
                <h2 class="text-2xl font-semibold text-gray-900">New Leave</h2>
            </div>

            <!-- Date From Field -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-1" for="date-from">From</label>
                <div class="flex items-center border border-gray-300 rounded-md overflow-hidden">
                    <span class="flex items-center justify-center w-10 h-10 bg-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 000 2v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-2V4a1 1 0 100-2h-2V1a1 1 0 10-2 0v1H6zm2 0h4v1H8V2zM4 7h12v10H4V7zm2 3a1 1 0 100 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input type="text" id="date-from" class="w-full p-2 focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Select date">
                </div>
            </div>

            <!-- Date To Field -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-1" for="date-to">To</label>
                <div class="flex items-center border border-gray-300 rounded-md overflow-hidden">
                    <span class="flex items-center justify-center w-10 h-10 bg-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 000 2v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-2V4a1 1 0 100-2h-2V1a1 1 0 10-2 0v1H6zm2 0h4v1H8V2zM4 7h12v10H4V7zm2 3a1 1 0 100 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <input type="text" id="date-to" class="w-full p-2 focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Select date">
                </div>
            </div>

            <!-- Reason Field -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-1" for="reason">Reason for applying</label>
                <textarea id="reason" class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" rows="4" placeholder="Enter your reason here"></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between">
                <button class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400 hover:bg-gray-400">Cancel</button>
                <button class="bg-red-600 text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 hover:bg-red-500">Send for approval</button>
            </div>
        </div>
    </div>

    <!-- Add Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize Flatpickr on the date inputs
        flatpickr("#date-from", {
            dateFormat: "Y-m-d",
            defaultDate: "today"
        });

        flatpickr("#date-to", {
            dateFormat: "Y-m-d",
            defaultDate: "today"
        });
    </script>
@endsection --}}

{{-- For User My request --}}

{{-- @extends('dashboard.layout.root')

@section('content')
    <!-- Add Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Add Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="pt-10 px-6">
        <!-- Page Title -->
        <div class="text-center mb-4">
            <h2 class="text-lg font-semibold text-gray-900">My Requests</h2>
        </div>

        <!-- Leave Request Cards -->
        <div class="space-y-3">
            <!-- Pending Request -->
            <div class="bg-white border rounded-lg p-3 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Suffering from cold</h3>
                        <div class="flex items-center space-x-1 text-xs text-gray-500">
                            <!-- Calendar Icon -->
                            <span class="material-icons text-gray-600">
                                event
                            </span>
                            <p>Leave from: <span class="font-semibold">29 Jan - 05 Feb</span></p>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Requested on 19 Apr, 5:30pm</p>
                    </div>
                    <div class="flex items-center space-x-1">
                        <span class="px-2 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-600 rounded-full">Pending</span>
                    </div>
                </div>
            </div>

            <!-- Approved Request -->
            <div class="bg-white border rounded-lg p-3 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Tour</h3>
                        <div class="flex items-center space-x-1 text-xs text-gray-500">
                            <!-- Calendar Icon -->
                            <span class="material-icons text-gray-600">
                                event
                            </span>
                            <p>Leave from: <span class="font-semibold">29 Jan - 05 Feb</span></p>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Requested on 19 Apr, 5:30pm</p>
                    </div>
                    <div class="flex items-center space-x-1">
                        <span class="px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-600 rounded-full">Approved</span>
                    </div>
                </div>
            </div>

            <!-- Rejected Request -->
            <div class="bg-white border rounded-lg p-3 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Going for trip</h3>
                        <div class="flex items-center space-x-1 text-xs text-gray-500">
                            <!-- Calendar Icon -->
                            <span class="material-icons text-gray-600">
                                event
                            </span>
                            <p>Leave from: <span class="font-semibold">29 Jan - 05 Feb</span></p>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Requested on 19 Apr, 5:30pm</p>
                    </div>
                    <div class="flex items-center space-x-1">
                        <span class="px-2 py-0.5 text-xs font-semibold bg-red-100 text-red-600 rounded-full">Rejected</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add New Leave Request Button -->
        <div class="mt-5">
            <button class="w-full bg-red-600 text-white text-center py-3 rounded-lg shadow-md hover:bg-red-500">
                + Add leave request
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Bar -->
    <nav class="bg-white border-t fixed bottom-0 left-0 right-0">
        <div class="flex justify-around">
            <a href="#" class="group flex flex-col items-center py-2">
                <span class="material-icons text-gray-400 group-hover:text-red-600">home</span>
                <span class="text-xs text-gray-400 group-hover:text-red-600">Home</span>
            </a>
            <a href="#" class="group flex flex-col items-center py-2">
                <span class="material-icons text-red-600">list</span>
                <span class="text-xs text-red-600">My request</span>
            </a>
            <a href="#" class="group flex flex-col items-center py-2">
                <span class="material-icons text-gray-400 group-hover:text-red-600">schedule</span>
                <span class="text-xs text-gray-400 group-hover:text-red-600">Take a break</span>
            </a>
            <a href="#" class="group flex flex-col items-center py-2">
                <span class="material-icons text-gray-400 group-hover:text-red-600">person</span>
                <span class="text-xs text-gray-400 group-hover:text-red-600">Profile</span>
            </a>
        </div>
    </nav>
@endsection --}}


{{-- Admin Manage Approval --}}
{{-- @extends('dashboard.layout.root')

@section('content')
    <!-- Add Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Add Google Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="min-h-screen bg-gray-100 pb-20">
        <div class="pt-10 px-6 lg:max-h-screen lg:overflow-y-auto lg:px-8">
            <!-- Page Title -->
            <div class="text-center mb-4">
                <!-- Apply larger text size on mobile and normal size for larger screens -->
                <h2 class="text-3xl sm:text-2xl font-bold text-gray-900">Manage Approvals</h2>
            </div>

            <!-- Tab Navigation -->
            <div class="flex justify-around border-b mb-6">
                <a href="#" id="pending-tab"
                    class="py-2 px-4 text-red-600 border-b-2 border-red-600 hover:text-red-600 text-lg sm:text-base"
                    onclick="showTab('pending')">Pending</a>
                <a href="#" id="confirmed-tab"
                    class="py-2 px-4 text-gray-600 border-b-2 border-red-600 hover:text-red-600 text-lg sm:text-base"
                    onclick="showTab('confirmed')">Confirmed</a>
                <a href="#" id="rejected-tab"
                    class="py-2 px-4 text-gray-600 border-b-2 border-red-600 hover:text-red-600 text-lg sm:text-base"
                    onclick="showTab('rejected')">Rejected</a>
            </div>

            <!-- Leave Request Cards -->
            <div id="pending" class="space-y-4">
                <!-- Pending Request -->
                <div
                    class="bg-white border rounded-lg p-4 shadow-sm lg:flex lg:justify-between lg:items-center lg:space-x-6">
                    <div class="flex flex-col lg:flex-row lg:items-center">
                        <div class="flex flex-col space-y-2 lg:space-x-4">
                            <!-- Larger text size on mobile -->
                            <h3 class="text-lg sm:text-sm font-medium text-gray-900">Suffering from cold</h3>
                            <div class="flex items-center space-x-1 text-sm sm:text-xs text-gray-500">
                                <span class="material-icons text-gray-600">event</span>
                                <p>Leave from: <span class="font-semibold">29 Jan - 05 Feb</span></p>
                            </div>
                            <div class="flex items-center space-x-1 text-sm sm:text-xs text-gray-500">
                                <img class="h-6 w-6 rounded-full" src="https://via.placeholder.com/150" alt="User Image">
                                <p class="text-xs text-gray-400">UI/UX Designer: <span class="font-semibold">Sunny
                                        Mehta</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Cancel and Confirm Buttons -->
                    <div class="flex justify-start mt-4 space-x-4 lg:mt-0 lg:flex-row-reverse">
                        <button
                            class="bg-gray-200 text-gray-700 font-semibold px-4 py-2 rounded-lg hover:bg-gray-300">Cancel</button>
                        <button
                            class="bg-red-600 text-white font-semibold px-4 py-2 rounded-lg hover:bg-red-500">Confirm</button>
                    </div>
                </div>
            </div>

            <div id="confirmed" class="space-y-4 hidden">
                <!-- Approved Request -->
                <div
                    class="bg-white border rounded-lg p-4 shadow-sm lg:flex lg:justify-between lg:items-center lg:space-x-6">
                    <div class="flex flex-col lg:flex-row lg:items-center">
                        <div class="flex flex-col space-y-2 lg:space-x-4">
                            <!-- Larger text size on mobile -->
                            <h3 class="text-lg sm:text-sm font-medium text-gray-900">Going for a trip</h3>
                            <div class="flex items-center space-x-1 text-sm sm:text-xs text-gray-500">
                                <span class="material-icons text-gray-600">event</span>
                                <p>Leave from: <span class="font-semibold">29 Jan - 05 Feb</span></p>
                            </div>
                            <div class="flex items-center space-x-1 text-sm sm:text-xs text-gray-500">
                                <img class="h-6 w-6 rounded-full" src="https://via.placeholder.com/150" alt="User Image">
                                <p class="text-xs text-gray-400">UI/UX Designer: <span class="font-semibold">Sunny
                                        Mehta</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="flex mt-2">
                        <span
                            class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-600 rounded-full shadow-md transition duration-200 ease-in-out hover:shadow-lg">
                            Approved
                        </span>
                    </div>
                </div>
            </div>

            <div id="rejected" class="space-y-4 hidden">
                <!-- Rejected Request -->
                <div
                    class="bg-white border rounded-lg p-4 shadow-sm lg:flex lg:justify-between lg:items-center lg:space-x-6">
                    <div class="flex flex-col lg:flex-row lg:items-center">
                        <div class="flex flex-col space-y-2 lg:space-x-4">
                            <!-- Larger text size on mobile -->
                            <h3 class="text-lg sm:text-sm font-medium text-gray-900">Tour</h3>
                            <div class="flex items-center space-x-1 text-sm sm:text-xs text-gray-500">
                                <span class="material-icons text-gray-600">event</span>
                                <p>Leave from: <span class="font-semibold">29 Jan - 05 Feb</span></p>
                            </div>
                            <div class="flex items-center space-x-1 text-sm sm:text-xs text-gray-500">
                                <img class="h-6 w-6 rounded-full" src="https://via.placeholder.com/150" alt="User Image">
                                <p class="text-xs text-gray-400">UI/UX Designer: <span class="font-semibold">Sunny
                                        Mehta</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="flex mt-2">
                        <span
                            class="px-3 py-1 text-xs font-semibold bg-red-100 text-red-600 rounded-full shadow-md transition duration-200 ease-in-out hover:shadow-lg">
                            Rejected
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Bar -->
    <nav class="bg-red-600 fixed bottom-0 left-0 right-0 shadow-md lg:hidden">
        <div class="flex justify-around py-2">
            <a href="#" class="group flex flex-col items-center">
                <span
                    class="material-icons text-white transition duration-300 ease-in-out group-hover:scale-110">home</span>
                <span
                    class="text-sm sm:text-xs text-white transition duration-300 ease-in-out group-hover:underline">Home</span>
            </a>
            <a href="#" class="group flex flex-col items-center">
                <span
                    class="material-icons text-white transition duration-300 ease-in-out group-hover:scale-110">list</span>
                <span class="text-sm sm:text-xs text-white transition duration-300 ease-in-out group-hover:underline">My
                    Request</span>
            </a>
            <a href="#" class="group flex flex-col items-center">
                <span
                    class="material-icons text-white transition duration-300 ease-in-out group-hover:scale-110">person</span>
                <span
                    class="text-sm sm:text-xs text-white transition duration-300 ease-in-out group-hover:underline">Profile</span>
            </a>
        </div>
    </nav>


    <script>
        function showTab(tab) {
            // Hide all sections
            document.getElementById('pending').classList.add('hidden');
            document.getElementById('confirmed').classList.add('hidden');
            document.getElementById('rejected').classList.add('hidden');

            // Remove active classes from tabs
            document.getElementById('pending-tab').classList.remove('border-red-600', 'text-red-600');
            document.getElementById('confirmed-tab').classList.remove('border-red-600', 'text-red-600');
            document.getElementById('rejected-tab').classList.remove('border-red-600', 'text-red-600');

            // Show the selected tab section and add active classes
            document.getElementById(tab).classList.remove('hidden');
            document.getElementById(tab + '-tab').classList.add('border-red-600', 'text-red-600');
        }

        // Initialize to show the Pending tab by default
        document.addEventListener('DOMContentLoaded', () => {
            showTab('pending');
        });
    </script>
@endsection --}}


{{-- Profile Page --}}
{{-- @extends('dashboard.layout.root')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <div class="pb-24">
        <div class="bg-gray-100 min-h-screen">
            <div class="container mx-auto px-4 py-8">
                <!-- Full width on web, max width on mobile -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full md:max-w-md mx-auto">
                    <div class="p-6">
                        <div class="flex flex-col items-center">
                            <img src="https://via.placeholder.com/100" alt="Profile Avatar"
                                class="w-24 h-24 rounded-full mb-4">
                            <p class="text-gray-600 text-sm mb-4">Employee ID: 123456</p>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between border-b py-2">
                                <div class="flex items-center space-x-2">
                                    <span class="material-icons text-gray-600">person</span>
                                    <span class="text-gray-800 font-medium">John Doe</span>
                                </div>
                                <button>
                                    <span class="material-icons">edit</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between border-b py-2">
                                <div class="flex items-center space-x-2">
                                    <span class="material-icons text-gray-600">work</span>
                                    <span class="text-gray-800">Software Engineer</span>
                                </div>
                                <button>
                                    <span class="material-icons">edit</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between border-b py-2">
                                <div class="flex items-center space-x-2">
                                    <span class="material-icons text-gray-600">email</span>
                                    <span class="text-gray-800">john.doe@example.com</span>
                                </div>
                                <button>
                                    <span class="material-icons">edit</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between border-b py-2">
                                <div class="flex items-center space-x-2">
                                    <span class="material-icons text-gray-600">phone</span>
                                    <span class="text-gray-800">+91 1234567873</span>
                                </div>
                                <button>
                                    <span class="material-icons">edit</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between border-b py-2">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xl font-bold text-gray-600">₹</span>
                                    <span class="text-gray-800">75,000 per month</span>
                                </div>
                                <button>
                                    <span class="material-icons">edit</span>
                                </button>
                            </div>
                        </div>

                        <button
                            class="mt-6 w-full bg-red-600 text-white py-2 rounded-lg shadow hover:bg-red-700 transition duration-300">
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-100">
            <div class="container mx-auto px-4 py-8">
                <!-- Full width on web, max width on mobile -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden w-full md:max-w-md mx-auto">
                    <div class="p-6">
                        <!-- Change Password Section -->
                        <h2 class="text-lg font-semibold text-gray-700 mb-4">Change Password</h2>

                        <!-- Current Password Field -->
                        <div class="mb-4">
                            <label for="current-password" class="block text-gray-700 font-semibold mb-2">Current
                                Password</label>
                            <input type="password" id="current-password" name="current-password"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                placeholder="Enter your current password">
                        </div>

                        <!-- New Password Field -->
                        <div class="mb-4">
                            <label for="new-password" class="block text-gray-700 font-semibold mb-2">New Password</label>
                            <input type="password" id="new-password" name="new-password"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                placeholder="Enter your new password">
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="mb-4">
                            <label for="confirm-password" class="block text-gray-700 font-semibold mb-2">Confirm New
                                Password</label>
                            <input type="password" id="confirm-password" name="confirm-password"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent"
                                placeholder="Confirm your new password">
                        </div>

                        <!-- Divider -->
                        <div class="flex items-center justify-between border-b py-2"></div>

                        <!-- Update Password Button -->
                        <button
                            class="mt-6 w-full bg-red-600 text-white py-2 rounded-lg shadow hover:bg-red-700 transition duration-300">
                            Update Password
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <button
            class="mt-6 w-3/4 mx-auto flex justify-center items-center bg-red-600 text-white py-2 rounded-lg shadow hover:bg-red-700 transition duration-300">
            <!-- Icon on the left -->
            <span class="material-icons text-white text-lg mr-2">exit_to_app</span>
            LogOut
        </button>

    </div>

    <!-- Mobile Navigation Bar with Hover Effects -->
    <nav class="bg-red-600 fixed bottom-0 left-0 right-0 shadow-md md:hidden">
        <div class="flex justify-around py-2">
            <a href="#" class="group flex flex-col items-center">
                <span class="material-icons text-white transition-colors duration-300 group-hover:text-red-200">home</span>
                <span class="text-xs text-white transition-colors duration-300 group-hover:text-red-200">Home</span>
            </a>
            <a href="#" class="group flex flex-col items-center">
                <span class="material-icons text-white transition-colors duration-300 group-hover:text-red-200">list</span>
                <span class="text-xs text-white transition-colors duration-300 group-hover:text-red-200">My Request</span>
            </a>
            <a href="#" class="group flex flex-col items-center">
                <span
                    class="material-icons text-white transition-colors duration-300 group-hover:text-red-200">folder</span>
                <span class="text-xs text-white transition-colors duration-300 group-hover:text-red-200">Records</span>
            </a>
            <a href="#" class="group flex flex-col items-center">
                <span
                    class="material-icons text-white transition-colors duration-300 group-hover:text-red-200">access_time</span>
                <span class="text-xs text-white transition-colors duration-300 group-hover:text-red-200">Attendance</span>
            </a>
            <a href="#" class="group flex flex-col items-center">
                <span
                    class="material-icons text-white transition-colors duration-300 group-hover:text-red-200">person</span>
                <span class="text-xs text-white transition-colors duration-300 group-hover:text-red-200">Profile</span>
            </a>
        </div>
    </nav>
@endsection --}}


{{-- Records Page  --}}

{{-- @extends('dashboard.layout.root')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <!-- Employee Selection -->
        <div class="mb-4">
            <label for="employee-select" class="block mb-1 text-sm font-medium text-gray-700">Select Employee:</label>
            <select id="employee-select"
                class="border-gray-300 rounded-md shadow-sm p-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select an employee</option>
                <option value="1">John Doe</option>
                <option value="2">Jane Smith</option>
                <option value="3">Bob Johnson</option>
                <!-- Add more employees as needed -->
            </select>
        </div>

        <!-- Date Filter Section -->
        <div class="md:hidden space-y-4">
            <div class="space-y-4">
                <div class="flex flex-col w-full">
                    <label for="from-date-mobile" class="mb-1 text-sm font-medium text-gray-700">From:</label>
                    <input type="text" id="from-date-mobile"
                        class="border-gray-300 rounded-md shadow-sm p-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Select From Date">
                </div>
                <div class="flex flex-col w-full">
                    <label for="to-date-mobile" class="mb-1 text-sm font-medium text-gray-700">To:</label>
                    <input type="text" id="to-date-mobile"
                        class="border-gray-300 rounded-md shadow-sm p-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Select To Date">
                </div>
            </div>
            <div class="flex flex-col space-y-2">
                <button
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300 ease-in-out w-full">Filter</button>
                <button
                    class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition duration-300 ease-in-out w-full">Clear</button>
            </div>
        </div>

        <!-- Web View (enhanced) -->
        <div class="hidden md:block">
            <div class="flex items-end space-x-4">
                <div class="flex-grow flex space-x-4">
                    <div class="flex-1">
                        <label for="from-date-web" class="block mb-1 text-sm font-medium text-gray-700">From:</label>
                        <input type="text" id="from-date-web"
                            class="border-gray-300 rounded-md shadow-sm p-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Select From Date">
                    </div>
                    <div class="flex-1">
                        <label for="to-date-web" class="block mb-1 text-sm font-medium text-gray-700">To:</label>
                        <input type="text" id="to-date-web"
                            class="border-gray-300 rounded-md shadow-sm p-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Select To Date">
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-300 ease-in-out">Filter</button>
                    <button
                        class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition duration-300 ease-in-out">Clear</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Records Section -->
    <div class="bg-gray-100 min-h-screen pt-10 pb-28">
        <div class="container mx-auto px-6">
            <div class="bg-white rounded-xl shadow-xl overflow-hidden w-full md:max-w-4xl mx-auto">
                <div class="p-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Attendance Records</h2>

                    <!-- Attendance Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg shadow-sm">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-in Time</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Late</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-in Image</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-out Time</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-out Image</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Working Hours</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Day Type</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-in Distance</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-out Distance</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Sample Data Row -->
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 text-sm text-gray-700">October 1, 2024</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">09:00 AM</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">No</td>
                                    <td class="px-4 py-4 text-sm text-gray-700"><img src="checkin_image_url" alt="Check-in"
                                            class="w-10 h-10 rounded-full"></td>
                                    <td class="px-4 py-4 text-sm text-gray-700">05:00 PM</td>
                                    <td class="px-4 py-4 text-sm text-gray-700"><img src="checkout_image_url"
                                            alt="Check-out" class="w-10 h-10 rounded-full"></td>
                                    <td class="px-4 py-4 text-sm text-gray-700">8 hours</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">Full Day</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">200 m</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">250 m</td>
                                </tr>

                                <!-- Another Sample Data Row -->
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 text-sm text-gray-700">October 2, 2024</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">09:30 AM</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">Yes</td>
                                    <td class="px-4 py-4 text-sm text-gray-700"><img src="checkin_image_url" alt="Check-in"
                                            class="w-10 h-10 rounded-full"></td>
                                    <td class="px-4 py-4 text-sm text-gray-700">05:30 PM</td>
                                    <td class="px-4 py-4 text-sm text-gray-700"><img src="checkout_image_url"
                                            alt="Check-out" class="w-10 h-10 rounded-full"></td>
                                    <td class="px-4 py-4 text-sm text-gray-700">8 hours</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">Half Day</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">300 m</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">280 m</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center mt-4">
                        <span class="text-sm text-gray-600">Showing 1-2 of 10</span>
                        <div class="flex space-x-2">
                            <button
                                class="bg-gray-200 p-2 rounded-md hover:bg-gray-300 transition duration-300">Previous</button>
                            <button
                                class="bg-gray-200 p-2 rounded-md hover:bg-gray-300 transition duration-300">Next</button>
                        </div>
                    </div>

                    <!-- Summary Information -->
                    <div class="mt-6 bg-red-50 p-4 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold text-gray-800">Summary Information</h3>
                        <div class="mt-4 space-y-2">
                            <div class="flex justify-between text-sm font-medium text-gray-700">
                                <span>Office Days:</span>
                                <span class="font-bold text-gray-800">27</span>
                            </div>
                            <div class="flex justify-between text-sm font-medium text-gray-700">
                                <span>Working Days:</span>
                                <span class="font-bold text-gray-800">0</span>
                            </div>
                            <div class="flex justify-between text-sm font-medium text-gray-700">
                                <span>Leaves:</span>
                                <span class="font-bold text-gray-800">0</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Bar with Hover Effects & Elevated Design -->
    <nav class="bg-gradient-to-r from-red-600 to-red-500 fixed bottom-0 left-0 right-0 shadow-2xl md:hidden rounded-t-2xl">
        <div class="flex justify-around py-4">
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">home</span>
                <span class="text-xs text-white mt-1">Home</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">list</span>
                <span class="text-xs text-white mt-1">My Request</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">folder</span>
                <span class="text-xs text-white mt-1">Records</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">access_time</span>
                <span class="text-xs text-white mt-1">Attendance</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">account_circle</span>
                <span class="text-xs text-white mt-1">Account</span>
            </a>
            <a href="#" id="see-more"
                class="group flex flex-col items-center transform hover:scale-105 transition duration-300 cursor-pointer">
                <span class="material-icons text-white text-2xl">more_horiz</span>
                <span class="text-xs text-white mt-1">See More</span>
            </a>
        </div>
        <!-- Additional Options with Icons and Border -->
        <div id="more-options" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <div
                class="flex border-2 border-white justify-around bg-gradient-to-r from-red-600 to-red-500 shadow-md rounded-t-2xl py-2 mt-2">
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">logout</span>
                    <span class="text-xs">Leave</span>
                </a>
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">work</span>
                    <span class="text-xs">Office</span>
                </a>
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">manage_accounts</span>
                    <span class="text-xs">Manage Office</span>
                </a>
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">people</span>
                    <span class="text-xs">Employees</span>
                </a>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.getElementById('see-more').addEventListener('click', function() {
            const moreOptions = document.getElementById('more-options');
            const isOpen = moreOptions.style.maxHeight; // Check current state

            if (!isOpen || isOpen === '0px') {
                moreOptions.style.maxHeight = moreOptions.scrollHeight + 'px'; // Set to full height
            } else {
                moreOptions.style.maxHeight = '0'; // Collapse
            }
        });
        // Initialize datepickers
        flatpickr("#from-date-mobile", {
            dateFormat: "Y-m-d",
        });
        flatpickr("#to-date-mobile", {
            dateFormat: "Y-m-d",
        });
        flatpickr("#from-date-web", {
            dateFormat: "Y-m-d",
        });
        flatpickr("#to-date-web", {
            dateFormat: "Y-m-d",
        });
    </script>
@endsection --}}




{{-- Attandance Records --}}

{{-- @extends('dashboard.layout.root')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Include Flatpickr CSS for the date picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <!-- Mobile View -->
        <div class="md:hidden space-y-4">
            <div class="space-y-4">
                <div class="flex flex-col w-full">
                    <label for="from-date-mobile" class="mb-1 text-sm font-medium text-gray-700">From:</label>
                    <input type="text" id="from-date-mobile"
                        class="border-gray-300 rounded-md shadow-sm p-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Select From Date">
                </div>
                <div class="flex flex-col w-full">
                    <label for="to-date-mobile" class="mb-1 text-sm font-medium text-gray-700">To:</label>
                    <input type="text" id="to-date-mobile"
                        class="border-gray-300 rounded-md shadow-sm p-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Select To Date">
                </div>
            </div>
            <div class="flex flex-col space-y-2">
                <button
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300 ease-in-out w-full">Filter</button>
                <button
                    class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition duration-300 ease-in-out w-full">Clear</button>
            </div>
        </div>

        <!-- Web View (enhanced) -->
        <div class="hidden md:block">
            <div class="flex items-end space-x-4">
                <div class="flex-grow flex space-x-4">
                    <div class="flex-1">
                        <label for="from-date-web" class="block mb-1 text-sm font-medium text-gray-700">From:</label>
                        <input type="text" id="from-date-web"
                            class="border-gray-300 rounded-md shadow-sm p-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Select From Date">
                    </div>
                    <div class="flex-1">
                        <label for="to-date-web" class="block mb-1 text-sm font-medium text-gray-700">To:</label>
                        <input type="text" id="to-date-web"
                            class="border-gray-300 rounded-md shadow-sm p-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Select To Date">
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-300 ease-in-out">Filter</button>
                    <button
                        class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition duration-300 ease-in-out">Clear</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Attendance Records Section -->
    <div class="bg-gray-100 min-h-screen py-10">
        <div class="container mx-auto px-6">
            <!-- Full width on web, max width on mobile -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden w-full md:max-w-4xl mx-auto">
                <div class="p-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Attendance Records</h2>

                    <!-- Attendance Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg shadow-sm">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-in Time
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-out Time
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Late
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-in Image
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-out Image
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Working Hours
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Day Type
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-in Distance
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                        Check-out Distance
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Sample Data Row -->
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        1
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        October 5, 2024
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        09:00 AM
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        06:00 PM
                                    </td>
                                    <td class="px-4 py-4">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            No
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <img src="https://via.placeholder.com/50" alt="Check-in Image"
                                            class="w-12 h-12 object-cover rounded-lg shadow-sm">
                                    </td>
                                    <td class="px-4 py-4">
                                        <img src="https://via.placeholder.com/50" alt="Check-out Image"
                                            class="w-12 h-12 object-cover rounded-lg shadow-sm">
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        9 hours
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        Weekday
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        1.5 km
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        1.8 km
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination (if required) -->
                    <div class="mt-6 flex justify-between">
                        <button class="text-sm text-red-600 hover:text-red-800 flex items-center">
                            <span class="material-icons text-red-600 mr-2">chevron_left</span>Previous
                        </button>
                        <button class="text-sm text-red-600 hover:text-red-800 flex items-center">
                            Next<span class="material-icons text-red-600 ml-2">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Bar with Hover Effects & Elevated Design -->
    <nav class="bg-gradient-to-r from-red-600 to-red-500 fixed bottom-0 left-0 right-0 shadow-2xl md:hidden rounded-t-2xl">
        <div class="flex justify-around py-4">
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">home</span>
                <span class="text-xs text-white mt-1">Home</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">list</span>
                <span class="text-xs text-white mt-1">My Request</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">folder</span>
                <span class="text-xs text-white mt-1">Records</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">access_time</span>
                <span class="text-xs text-white mt-1">Attendance</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">account_circle</span>
                <span class="text-xs text-white mt-1">Account</span>
            </a>
        </div>
    </nav>

    <!-- Initialize Flatpickr for date inputs -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#from-date-mobile, #to-date-mobile, #from-date-web, #to-date-web", {
                dateFormat: "Y-m-d",
                allowInput: true,
                altInput: true,
                altFormat: "F j, Y",
                showMonths: 1
            });
        });
    </script>
@endsection --}}

{{-- Adim Employee Page --}}

{{-- @extends('dashboard.layout.root')

@section('content')
    <style>
        #more-options {
            max-height: 0;
            overflow: hidden;
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Employees</h1>

            <button
                class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                Create
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">#</th>
                        <th class="py-3 px-6 text-left">Name</th>
                        <th class="py-3 px-6 text-left">Email</th>
                        <th class="py-3 px-6 text-left">Phone</th>
                        <th class="py-3 px-6 text-left">Image</th>
                        <th class="py-3 px-6 text-left">Office</th>
                        <th class="py-3 px-6 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">1</td>
                        <td class="py-3 px-6 text-left">Alice Johnson</td>
                        <td class="py-3 px-6 text-left">alice.johnson@example.com</td>
                        <td class="py-3 px-6 text-left">+1 234 567 8901</td>
                        <td class="py-3 px-6 text-left">
                            <img src="https://via.placeholder.com/50" alt="Alice Johnson" class="rounded-full w-12 h-12">
                        </td>
                        <td class="py-3 px-6 text-left">New York</td>
                        <td class="py-3 px-6 text-left flex space-x-2">
                            <button title="Edit"
                                class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                                <span class="material-icons">edit</span>
                            </button>
                            <button title="Delete"
                                class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                <span class="material-icons">delete</span>
                            </button>
                            <button title="Profile"
                                class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                <span class="material-icons">account_circle</span>
                            </button>
                        </td>



                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">2</td>
                        <td class="py-3 px-6 text-left">Bob Smith</td>
                        <td class="py-3 px-6 text-left">bob.smith@example.com</td>
                        <td class="py-3 px-6 text-left">+1 987 654 3210</td>
                        <td class="py-3 px-6 text-left">
                            <img src="https://via.placeholder.com/50" alt="Bob Smith" class="rounded-full w-12 h-12">
                        </td>
                        <td class="py-3 px-6 text-left">Los Angeles</td>
                        <td class="py-3 px-6 text-left flex space-x-2">
                            <button title="Edit"
                                class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                                <span class="material-icons">edit</span>
                            </button>
                            <button title="Delete"
                                class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                <span class="material-icons">delete</span>
                            </button>
                            <button title="Profile"
                                class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                <span class="material-icons">account_circle</span>
                            </button>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">3</td>
                        <td class="py-3 px-6 text-left">Charlie Brown</td>
                        <td class="py-3 px-6 text-left">charlie.brown@example.com</td>
                        <td class="py-3 px-6 text-left">+1 555 555 5555</td>
                        <td class="py-3 px-6 text-left">
                            <img src="https://via.placeholder.com/50" alt="Charlie Brown" class="rounded-full w-12 h-12">
                        </td>
                        <td class="py-3 px-6 text-left">Chicago</td>
                        <td class="py-3 px-6 text-left flex space-x-2">
                            <button title="Edit"
                                class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                                <span class="material-icons">edit</span>
                            </button>
                            <button title="Delete"
                                class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                <span class="material-icons">delete</span>
                            </button>
                            <button title="Profile"
                                class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                <span class="material-icons">account_circle</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Navigation Bar with Hover Effects & Elevated Design -->
    <nav class="bg-gradient-to-r from-red-600 to-red-500 fixed bottom-0 left-0 right-0 shadow-2xl md:hidden rounded-t-2xl">
        <div class="flex justify-around py-4">
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">home</span>
                <span class="text-xs text-white mt-1">Home</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">list</span>
                <span class="text-xs text-white mt-1">My Request</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">folder</span>
                <span class="text-xs text-white mt-1">Records</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">access_time</span>
                <span class="text-xs text-white mt-1">Attendance</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">account_circle</span>
                <span class="text-xs text-white mt-1">Account</span>
            </a>
            <a href="#" id="see-more"
                class="group flex flex-col items-center transform hover:scale-105 transition duration-300 cursor-pointer">
                <span class="material-icons text-white text-2xl">more_horiz</span>
                <span class="text-xs text-white mt-1">See More</span>
            </a>
        </div>
        <!-- Additional Options with Icons and Border -->
        <div id="more-options" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <div
                class="flex border-2 border-white justify-around bg-gradient-to-r from-red-600 to-red-500 shadow-md rounded-t-2xl py-2 mt-2">
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">logout</span>
                    <span class="text-xs">Leave</span>
                </a>
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">work</span>
                    <span class="text-xs">Office</span>
                </a>
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">manage_accounts</span>
                    <span class="text-xs">Manage Office</span>
                </a>
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">people</span>
                    <span class="text-xs">Employees</span>
                </a>
            </div>
        </div>
    </nav>

    <script>
        document.getElementById('see-more').addEventListener('click', function() {
            const moreOptions = document.getElementById('more-options');
            const isOpen = moreOptions.style.maxHeight; // Check current state

            if (!isOpen || isOpen === '0px') {
                moreOptions.style.maxHeight = moreOptions.scrollHeight + 'px'; // Set to full height
            } else {
                moreOptions.style.maxHeight = '0'; // Collapse
            }
        });
    </script>
@endsection --}}


{{-- offices --}}

{{-- @extends('dashboard.layout.root')

@section('content')
    <style>
        #more-options {
            max-height: 0;
            overflow: hidden;
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Offices</h1>

            <button
                class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                Create Office
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">#</th>
                        <th class="py-3 px-6 text-left">Name</th>
                        <th class="py-3 px-6 text-left">Latitude</th>
                        <th class="py-3 px-6 text-left">Longitude</th>
                        <th class="py-3 px-6 text-left">Radius</th>
                        <th class="py-3 px-6 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">1</td>
                        <td class="py-3 px-6 text-left">New York Office</td>
                        <td class="py-3 px-6 text-left">40.7128</td>
                        <td class="py-3 px-6 text-left">-74.0060</td>
                        <td class="py-3 px-6 text-left">50 km</td>
                        <td class="py-3 px-6 text-left flex space-x-2">
                            <button title="Edit"
                                class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                                <span class="material-icons">edit</span>
                            </button>
                            <button title="Delete"
                                class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                <span class="material-icons">delete</span>
                            </button>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">2</td>
                        <td class="py-3 px-6 text-left">Los Angeles Office</td>
                        <td class="py-3 px-6 text-left">34.0522</td>
                        <td class="py-3 px-6 text-left">-118.2437</td>
                        <td class="py-3 px-6 text-left">40 km</td>
                        <td class="py-3 px-6 text-left flex space-x-2">
                            <button title="Edit"
                                class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                                <span class="material-icons">edit</span>
                            </button>
                            <button title="Delete"
                                class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                <span class="material-icons">delete</span>
                            </button>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">3</td>
                        <td class="py-3 px-6 text-left">Chicago Office</td>
                        <td class="py-3 px-6 text-left">41.8781</td>
                        <td class="py-3 px-6 text-left">-87.6298</td>
                        <td class="py-3 px-6 text-left">30 km</td>
                        <td class="py-3 px-6 text-left flex space-x-2">
                            <button title="Edit"
                                class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                                <span class="material-icons">edit</span>
                            </button>
                            <button title="Delete"
                                class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                <span class="material-icons">delete</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Navigation Bar with Hover Effects & Elevated Design -->
    <nav class="bg-gradient-to-r from-red-600 to-red-500 fixed bottom-0 left-0 right-0 shadow-2xl md:hidden rounded-t-2xl">
        <div class="flex justify-around py-4">
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">home</span>
                <span class="text-xs text-white mt-1">Home</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">list</span>
                <span class="text-xs text-white mt-1">My Request</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">folder</span>
                <span class="text-xs text-white mt-1">Records</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">access_time</span>
                <span class="text-xs text-white mt-1">Attendance</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">account_circle</span>
                <span class="text-xs text-white mt-1">Account</span>
            </a>
            <a href="#" id="see-more"
                class="group flex flex-col items-center transform hover:scale-105 transition duration-300 cursor-pointer">
                <span class="material-icons text-white text-2xl">more_horiz</span>
                <span class="text-xs text-white mt-1">See More</span>
            </a>
        </div>
        <!-- Additional Options with Icons and Border -->
        <div id="more-options" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <div
                class="flex border-2 border-white justify-around bg-gradient-to-r from-red-600 to-red-500 shadow-md rounded-t-2xl py-2 mt-2">
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">logout</span>
                    <span class="text-xs">Leave</span>
                </a>
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">work</span>
                    <span class="text-xs">Office</span>
                </a>
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">manage_accounts</span>
                    <span class="text-xs">Manage Office</span>
                </a>
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">people</span>
                    <span class="text-xs">Employees</span>
                </a>
            </div>
        </div>
    </nav>

    <script>
        document.getElementById('see-more').addEventListener('click', function() {
            const moreOptions = document.getElementById('more-options');
            const isOpen = moreOptions.style.maxHeight; // Check current state

            if (!isOpen || isOpen === '0px') {
                moreOptions.style.maxHeight = moreOptions.scrollHeight + 'px'; // Set to full height
            } else {
                moreOptions.style.maxHeight = '0'; // Collapse
            }
        });
    </script>
@endsection --}}

{{-- Manage offs Page --}}

{{-- @extends('dashboard.layout.root')

@section('content')
    <style>
        #more-options {
            max-height: 0;
            overflow: hidden;
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Manage Offs</h1>

            <button
                class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                Create Off
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">#</th>
                        <th class="py-3 px-6 text-left">Title</th>
                        <th class="py-3 px-6 text-left">Date</th>
                        <th class="py-3 px-6 text-left">Description</th>
                        <th class="py-3 px-6 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">1</td>
                        <td class="py-3 px-6 text-left">Christmas Holiday</td>
                        <td class="py-3 px-6 text-left">25-Dec-2024</td>
                        <td class="py-3 px-6 text-left">Annual Christmas holiday for all employees</td>
                        <td class="py-3 px-6 text-left flex space-x-2">
                            <button title="Edit"
                                class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                                <span class="material-icons">edit</span>
                            </button>
                            <button title="Delete"
                                class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                <span class="material-icons">delete</span>
                            </button>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">2</td>
                        <td class="py-3 px-6 text-left">New Year Holiday</td>
                        <td class="py-3 px-6 text-left">01-Jan-2025</td>
                        <td class="py-3 px-6 text-left">New Year's Day holiday for all employees</td>
                        <td class="py-3 px-6 text-left flex space-x-2">
                            <button title="Edit"
                                class="bg-blue-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                                <span class="material-icons">edit</span>
                            </button>
                            <button title="Delete"
                                class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                <span class="material-icons">delete</span>
                            </button>
                        </td>
                    </tr>
                    <!-- Additional rows can go here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Navigation Bar with Hover Effects & Elevated Design -->
    <nav class="bg-gradient-to-r from-red-600 to-red-500 fixed bottom-0 left-0 right-0 shadow-2xl md:hidden rounded-t-2xl">
        <div class="flex justify-around py-4">
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">home</span>
                <span class="text-xs text-white mt-1">Home</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">list</span>
                <span class="text-xs text-white mt-1">My Request</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">folder</span>
                <span class="text-xs text-white mt-1">Records</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">access_time</span>
                <span class="text-xs text-white mt-1">Attendance</span>
            </a>
            <a href="#" class="group flex flex-col items-center transform hover:scale-105 transition duration-300">
                <span class="material-icons text-white text-2xl">account_circle</span>
                <span class="text-xs text-white mt-1">Account</span>
            </a>
            <a href="#" id="see-more"
                class="group flex flex-col items-center transform hover:scale-105 transition duration-300 cursor-pointer">
                <span class="material-icons text-white text-2xl">more_horiz</span>
                <span class="text-xs text-white mt-1">See More</span>
            </a>
        </div>
        <!-- Additional Options with Icons and Border -->
        <div id="more-options" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <div
                class="flex border-2 border-white justify-around bg-gradient-to-r from-red-600 to-red-500 shadow-md rounded-t-2xl py-2 mt-2">
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">logout</span>
                    <span class="text-xs">Leave</span>
                </a>
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">work</span>
                    <span class="text-xs">Office</span>
                </a>
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">manage_accounts</span>
                    <span class="text-xs">Manage Office</span>
                </a>
                <a href="#" class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2">
                    <span class="material-icons text-white text-lg">people</span>
                    <span class="text-xs">Employees</span>
                </a>
            </div>
        </div>
    </nav>

    <script>
        document.getElementById('see-more').addEventListener('click', function() {
            const moreOptions = document.getElementById('more-options');
            const isOpen = moreOptions.style.maxHeight; // Check current state

            if (!isOpen || isOpen === '0px') {
                moreOptions.style.maxHeight = moreOptions.scrollHeight + 'px'; // Set to full height
            } else {
                moreOptions.style.maxHeight = '0'; // Collapse
            }
        });
    </script>
@endsection --}}


{{-- Admin Approval for leave --}}

{{-- @extends('dashboard.layout.root')

@section('content')
    <style>
        #more-options {
            max-height: 0;
            overflow: hidden;
        }

        .perspective-500 {
            perspective: 500px;
        }
    </style>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <div class="bg-gray-100 p-4 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Leave List</h1>

            <button
                class="bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                Create Leave
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">#</th>
                        <th class="py-3 px-6 text-left">Employee</th>
                        <th class="py-3 px-6 text-left">Start Date</th>
                        <th class="py-3 px-6 text-left">End Date</th>
                        <th class="py-3 px-6 text-left">Leave Type</th>
                        <th class="py-3 px-6 text-left">Reason</th>
                        <th class="py-3 px-6 text-left">Status</th>
                        <th class="py-3 px-6 text-left">Response By</th>
                        <th class="py-3 px-6 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">1</td>
                        <td class="py-3 px-6 text-left">John Doe</td>
                        <td class="py-3 px-6 text-left">01-Oct-2024</td>
                        <td class="py-3 px-6 text-left">03-Oct-2024</td>
                        <td class="py-3 px-6 text-left">Sick Leave</td>
                        <td class="py-3 px-6 text-left">Fever and cold</td>
                        <td class="py-3 px-6 text-left">Pending</td>
                        <td class="py-3 px-6 text-left">HR Manager</td>
                        <td class="py-3 px-6 text-left flex space-x-2">
                            <button title="Approve"
                                class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                <span class="material-icons">check_circle</span>
                            </button>
                            <button title="Reject"
                                class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                <span class="material-icons">cancel</span>
                            </button>
                        </td>
                    </tr>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">2</td>
                        <td class="py-3 px-6 text-left">Jane Smith</td>
                        <td class="py-3 px-6 text-left">15-Oct-2024</td>
                        <td class="py-3 px-6 text-left">20-Oct-2024</td>
                        <td class="py-3 px-6 text-left">Vacation</td>
                        <td class="py-3 px-6 text-left">Family trip</td>
                        <td class="py-3 px-6 text-left">Approved</td>
                        <td class="py-3 px-6 text-left">HR Manager</td>
                        <td class="py-3 px-6 text-left flex space-x-2">
                            <button title="Approve"
                                class="bg-green-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-50">
                                <span class="material-icons">check_circle</span>
                            </button>
                            <button title="Reject"
                                class="bg-red-500 text-white font-semibold p-2 rounded-lg shadow-md hover:bg-red-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-50">
                                <span class="material-icons">cancel</span>
                            </button>
                        </td>
                    </tr>
                    <!-- Additional rows can go here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Navigation Bar with 3D Hover Effects & Elevated Design -->
    <nav class="bg-gradient-to-r from-red-600 to-red-500 fixed bottom-0 left-0 right-0 shadow-2xl md:hidden rounded-t-2xl">
        <div class="flex justify-around py-4 perspective-500">
            <!-- Perspective applied to the parent container -->
            <a href="#"
                class="group flex flex-col items-center transform transition duration-300 hover:translateZ(30px) hover:scale-110">
                <span
                    class="material-icons text-white text-2xl transform transition duration-300 group-hover:scale-125">home</span>
                <span class="text-xs text-white mt-1 transform transition duration-300 group-hover:scale-125">Home</span>
            </a>
            <a href="#"
                class="group flex flex-col items-center transform transition duration-300 hover:translateZ(30px) hover:scale-110">
                <span
                    class="material-icons text-white text-2xl transform transition duration-300 group-hover:scale-125">list</span>
                <span class="text-xs text-white mt-1 transform transition duration-300 group-hover:scale-125">My
                    Request</span>
            </a>
            <a href="#"
                class="group flex flex-col items-center transform transition duration-300 hover:translateZ(30px) hover:scale-110">
                <span
                    class="material-icons text-white text-2xl transform transition duration-300 group-hover:scale-125">folder</span>
                <span class="text-xs text-white mt-1 transform transition duration-300 group-hover:scale-125">Records</span>
            </a>
            <a href="#"
                class="group flex flex-col items-center transform transition duration-300 hover:translateZ(30px) hover:scale-110">
                <span
                    class="material-icons text-white text-2xl transform transition duration-300 group-hover:scale-125">access_time</span>
                <span
                    class="text-xs text-white mt-1 transform transition duration-300 group-hover:scale-125">Attendance</span>
            </a>
            <a href="#"
                class="group flex flex-col items-center transform transition duration-300 hover:translateZ(30px) hover:scale-110">
                <span
                    class="material-icons text-white text-2xl transform transition duration-300 group-hover:scale-125">account_circle</span>
                <span class="text-xs text-white mt-1 transform transition duration-300 group-hover:scale-125">Account</span>
            </a>
            <a href="#" id="see-more"
                class="group flex flex-col items-center transform transition duration-300 hover:translateZ(30px) hover:scale-110 cursor-pointer">
                <span
                    class="material-icons text-white text-2xl transform transition duration-300 group-hover:scale-125">more_horiz</span>
                <span class="text-xs text-white mt-1 transform transition duration-300 group-hover:scale-125">See
                    More</span>
            </a>
        </div>

        <!-- Additional Options with Icons and Border -->
        <div id="more-options" class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <div
                class="flex border-2 border-white justify-around bg-gradient-to-r from-red-600 to-red-500 shadow-md rounded-t-2xl py-2 mt-2">
                <a href="#"
                    class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2 transform hover:translateZ(30px)">
                    <span class="material-icons text-white text-lg">logout</span>
                    <span class="text-xs">Leave</span>
                </a>
                <a href="#"
                    class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2 transform hover:translateZ(30px)">
                    <span class="material-icons text-white text-lg">work</span>
                    <span class="text-xs">Office</span>
                </a>
                <a href="#"
                    class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2 transform hover:translateZ(30px)">
                    <span class="material-icons text-white text-lg">manage_accounts</span>
                    <span class="text-xs">Manage Offs</span>
                </a>
                <a href="#"
                    class="flex flex-col items-center text-white hover:bg-red-600 px-2 py-2 transform hover:translateZ(30px)">
                    <span class="material-icons text-white text-lg">people</span>
                    <span class="text-xs">Employees</span>
                </a>
            </div>
        </div>
    </nav>

    <script>
        document.getElementById('see-more').addEventListener('click', function() {
            const moreOptions = document.getElementById('more-options');
            const isOpen = moreOptions.style.maxHeight; // Check current state

            if (!isOpen || isOpen === '0px') {
                moreOptions.style.maxHeight = moreOptions.scrollHeight + 'px'; // Set to full height
            } else {
                moreOptions.style.maxHeight = '0'; // Collapse
            }
        });
    </script>
@endsection --}}


{{-- Main Page --}}

{{-- @extends('dashboard.layout.root')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-red-700 via-red-800 to-red-900 p-4">
    <!-- 3D Card Effect -->
    <div class="bg-red-800 rounded-2xl shadow-lg p-8 max-w-md w-full transform transition-transform duration-700 hover:scale-105 hover:shadow-3xl perspective hover:rotate-x-3d">
        <!-- Logo with 3D Hover Effect -->
        <div class="flex justify-center mb-6 transform transition-all duration-500 hover:rotate-3d hover:translate-y-3d">
            <img src="" alt="App Logo" class="h-32 w-32 rounded-full shadow-lg transition-transform duration-300 transform hover:scale-110 hover:rotate-3d">
        </div>
        <!-- 3D Text Heading -->
        <h1 class="text-3xl font-extrabold text-white text-center mb-4 leading-tight transform transition-transform duration-500 hover:rotate-y-3d">Welcome to Real Victory Groups App</h1>
        <p class="text-gray-300 text-center mb-6 text-lg leading-relaxed transform transition-transform duration-500 hover:rotate-y-3d">Your gateway to managing attendance and leaves seamlessly.</p>
        
        <div class="flex flex-col space-y-4">
            <!-- 3D Button Effect -->
            <a href="/get-started" class="bg-gradient-to-r from-white to-red-500 text-red-700 hover:text-red-500 px-6 py-3 rounded-md shadow-lg transition-all duration-500 transform hover:scale-110 hover:shadow-3xl hover:rotate-3d hover:translate-z-3d font-semibold text-center">
                Get Started
            </a>
            <a href="/about" class="text-white text-center underline transition-all duration-500 hover:text-gray-200 hover:rotate-3d">Learn More</a>
        </div>
        
        <div class="mt-8 text-center transform transition-transform duration-500 hover:rotate-x-3d hover:translate-z-3d">
            <p class="text-gray-400 text-sm">© 2024 Real Victory Groups. All rights reserved.</p>
        </div>
    </div>
</div>

<style>
    /* Custom 3D effect styles */
    .shadow-3xl {
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
    }
    .perspective {
        perspective: 1000px;
    }
    .hover\:rotate-x-3d:hover {
        transform: rotateX(10deg);
    }
    .hover\:rotate-y-3d:hover {
        transform: rotateY(10deg);
    }
    .hover\:rotate-3d:hover {
        transform: rotate3d(1, 1, 0, 10deg);
    }
    .hover\:translate-z-3d:hover {
        transform: translateZ(20px);
    }
    .hover\:translate-y-3d:hover {
        transform: translateY(-10px);
    }
</style>
@endsection --}}
