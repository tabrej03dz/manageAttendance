@extends('dashboard.layout.root')

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
                <video id="video" autoplay></video>
                <canvas id="canvas" class="hidden"></canvas>
                <img id="imagePreview" alt="Captured Image" />
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
                <button id="snap"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 md:py-3 md:px-4 rounded-full w-full mb-3 flex items-center justify-center">
                    <i class="fas fa-check mr-2"></i> Capture
                </button>

                <form
                    action="{{ $formType == 'check_in' ? route('attendance.check_in', ['user' => $user ?? null]) : route('attendance.check_out', ['user' => $user ?? null]) }}"
                    method="POST" enctype="multipart/form-data" id="uploadForm" class="mt-3">
                    @csrf
                    <div class="mb-3 d-none">
                        <input type="file" id="capturedImage" name="image" class="form-control">
                        <input type="text" name="latitude" id="latitude" placeholder="Latitude">
                        <input type="text" name="longitude" id="longitude" placeholder="Longitude">
                        <input type="text" name="distance" id="distance">
                    </div>
                    <div class="d-grid">
                        <button type="submit" id="upload"
                            class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 md:py-3 md:px-4 rounded-full w-full mb-3 flex items-center justify-center">Submit</button>
                    </div>
                </form>

                {{--                <button id="submitButton" --}}
                {{--                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 md:py-3 md:px-4 rounded-full w-full mb-3 flex items-center justify-center" --}}
                {{--                    onclick="submitImage()"> --}}
                {{--                    <i class="fas fa-check mr-2"></i> Submit --}}
                {{--                </button> --}}
                <a href="{{ route('attendance.form', ['form_type' => $formType]) }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-3 md:py-3 md:px-4 rounded-full w-full flex items-center justify-center">
                    <i class="fas fa-redo mr-2"></i> Reset
                </a>
            </div>
        </main>
    </div>
<<<<<<< HEAD









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
=======
    <script>
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(function(stream) {
>>>>>>> 916a3f67793f87e9eb53b778d3e39080d482906e
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
                    let distance = haversineDistance(userOffice.latitude, userOffice.longitude, latitude,
                        longitude);

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

<<<<<<< HEAD
            function degreesToRadians(degrees) {
                return degrees * (Math.PI / 180);
            }


            const currentTime = document.querySelector('.current-time');
            const currentDate = document.querySelector('.current-date');

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

        </script>
=======
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
>>>>>>> 916a3f67793f87e9eb53b778d3e39080d482906e


        const currentTime = document.querySelector('.current-time');
        const currentDate = document.querySelector('.current-date');

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

<<<<<<< HEAD

    <script>
{{--        document.addEventListener('DOMContentLoaded', function() {--}}
{{--            const checkInTime = document.querySelector('.check-in-time');--}}
//             const totalHours = document.querySelector('.total-hours');

{{--            const punchCircle = document.getElementById('punchCircle');--}}
{{--            const video = document.getElementById('video');--}}
{{--            const canvas = document.getElementById('canvas');--}}
{{--            const capturedImage = document.getElementById('capturedImage');--}}
{{--            const cameraIcon = document.getElementById('cameraIcon');--}}
{{--            const captureButton = document.getElementById('captureButton');--}}
{{--            const captureButtonText = document.getElementById('captureButtonText');--}}

{{--            let isImageCaptured = false;--}}
{{--            let stream = null;--}}



{{--            function toggleCamera() {--}}
{{--                if (video.classList.contains('hidden')) {--}}
{{--                    startCamera();--}}
{{--                } else {--}}
{{--                    captureImage();--}}
{{--                }--}}
{{--            }--}}

{{--            function startCamera() {--}}
{{--                navigator.mediaDevices.getUserMedia({--}}
{{--                        video: true--}}
{{--                    })--}}
{{--                    .then(videoStream => {--}}
{{--                        stream = videoStream;--}}
{{--                        video.srcObject = stream;--}}
{{--                        video.classList.remove('hidden');--}}
{{--                        cameraIcon.classList.add('hidden');--}}
{{--                        capturedImage.classList.add('hidden');--}}
{{--                        captureButtonText.textContent = 'Capture Image';--}}
{{--                    })--}}
{{--                    .catch(error => {--}}
{{--                        console.error("Error accessing the camera: ", error);--}}
{{--                    });--}}
{{--            }--}}

{{--            function captureImage() {--}}
{{--                canvas.width = video.videoWidth;--}}
{{--                canvas.height = video.videoHeight;--}}
{{--                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);--}}
{{--                capturedImage.src = canvas.toDataURL('image/png');--}}
{{--                video.classList.add('hidden');--}}
{{--                capturedImage.classList.remove('hidden');--}}
{{--                isImageCaptured = true;--}}
{{--                checkInTime.textContent = new Date().toLocaleTimeString('en-US', {--}}
{{--                    hour: '2-digit',--}}
{{--                    minute: '2-digit'--}}
{{--                });--}}
{{--                captureButtonText.textContent = 'Retake Photo';--}}

{{--                // Stop the video stream--}}
{{--                if (stream) {--}}
{{--                    stream.getTracks().forEach(track => track.stop());--}}
{{--                }--}}
{{--            }--}}

{{--            function submitImage() {--}}
{{--                if (isImageCaptured) {--}}
{{--                    alert("Image submitted successfully!");--}}
{{--                } else {--}}
{{--                    alert("Please capture an image first.");--}}
{{--                }--}}
{{--            }--}}

{{--            function resetCapture() {--}}
{{--                video.classList.add('hidden');--}}
{{--                capturedImage.classList.add('hidden');--}}
{{--                cameraIcon.classList.remove('hidden');--}}
{{--                isImageCaptured = false;--}}
{{--                checkInTime.textContent = "--:--";--}}
{{--                totalHours.textContent = "--:--";--}}
{{--                captureButtonText.textContent = 'Start Camera';--}}

{{--                // Stop the video stream if it's active--}}
{{--                if (stream) {--}}
{{--                    stream.getTracks().forEach(track => track.stop());--}}
{{--                    stream = null;--}}
{{--                }--}}
{{--            }--}}

{{--            captureButton.addEventListener('click', toggleCamera);--}}
{{--            punchCircle.addEventListener('click', toggleCamera);--}}

{{--            window.submitImage = submitImage;--}}
{{--            window.resetCapture = resetCapture;--}}
{{--        });--}}
=======
        setInterval(updateCurrentTime, 1000);
        updateCurrentTime();
>>>>>>> 916a3f67793f87e9eb53b778d3e39080d482906e
    </script>
@endsection
