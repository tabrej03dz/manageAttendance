@extends('dashboard.layout.root')

@section('content')
    <!-- Add Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Add Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* Tailwind-based custom class for input styling */
        .input-field {
            @apply w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:border-blue-400 transition;
        }
    </style>




    <div class="pt-2 pb-16">
        <div class="min-h-screen bg-red-50 flex flex-col mx-2 shadow-2xl rounded-lg">
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                    <img id="imagePreview" />
                    <div id="cameraIcon" class="text-red-500">
                        <i class="fas fa-camera fa-3x"></i>
                    </div>
                </div>

                <!-- Status Info -->


                <!-- Action Buttons -->
                <div class="w-full max-w-xs">
                    <button id="snap"
                            class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 md:py-3 md:px-4 rounded-full w-full mb-3 flex items-center justify-center">
                        <i class="fas fa-camera mr-2"></i>
                        Capture
                    </button>
                    <form
                        action="{{ route('visit.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        id="uploadForm"
                        class="max-w-md mx-auto mt-5 p-6 bg-white shadow-lg rounded-lg"
                    >
                        @csrf
                        <!-- Hidden Fields for Image, Latitude, Longitude, and Distance -->
                        <div class="hidden">
                            <input type="file" id="capturedImage" name="image" class="form-control">
                            <input type="text" name="latitude" id="latitude" placeholder="Latitude" class="input-field">
                            <input type="text" name="longitude" id="longitude" placeholder="Longitude" class="input-field">
                            <input type="text" name="distance" id="distance" class="input-field">
                        </div>

                        <!-- Visible Form Fields -->
                        <div class="mb-4">
                            <input
                                type="text"
                                name="address"
                                id="address"
                                placeholder="Address"
                                class="input-field"
                            >
                        </div>
                        <div class="mb-4">
                            <input
                                type="number"
                                name="expense"
                                id="expense"
                                placeholder="Expense"
                                class="input-field"
                            >
                        </div>
                        <div class="mb-4">
                            <input
                                type="file"
                                name="expense_attachment"
                                id="expense_attachment"
                                class="input-field"
                            >
                        </div>
                        <div class="mb-4">
                            <input
                                type="text"
                                name="description"
                                id="description"
                                placeholder="Description"
                                class="input-field"
                            >
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6">
                            <button
                                type="submit"
                                id="upload"
                                class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-lg flex items-center justify-center"
                            >
                                <i class="fas fa-check mr-2"></i>Submit
                            </button>
                        </div>
                    </form>



                    {{--                <button id="submitButton" --}}
                    {{--                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 md:py-3 md:px-4 rounded-full w-full mb-3 flex items-center justify-center" --}}
                    {{--                    onclick="submitImage()"> --}}
                    {{--                    <i class="fas fa-check mr-2"></i> Submit --}}
                    {{--                </button> --}}
                </div>
            </main>
        </div>
    </div>

    <script>
        // Get video stream from the camera
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

        // Capture the image on button click
        document.getElementById('snap').addEventListener('click', function() {
            var canvas = document.getElementById('canvas');
            var video = document.getElementById('video');
            var imagePreview = document.getElementById('imagePreview');
            var context = canvas.getContext('2d');

            // Set canvas dimensions equal to video dimensions
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            // Save the mirrored image
            context.save();
            context.scale(-1, 1); // Flip horizontally (mirror effect)
            context.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
            context.restore();

            // Get the data URL of the mirrored image from the canvas
            var mirroredDataURL = canvas.toDataURL('image/png');

            // Now display the normal image
            // Reset the canvas for normal capture
            context.clearRect(0, 0, canvas.width, canvas.height);
            context.drawImage(video, 0, 0, canvas.width, canvas.height); // Normal orientation
            var normalDataURL = canvas.toDataURL('image/png');

            // Hide the video element and show the image preview
            video.classList.add('hidden');
            imagePreview.classList.remove('hidden');
            imagePreview.src = normalDataURL; // Set the preview to normal orientation

            // Convert the captured mirrored image data URL to a file for submission
            fetch(mirroredDataURL)
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
        // Ensure image is captured before form submission
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
                    console.log(position);
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    let latitude = position.coords.latitude;
                    let longitude = position.coords.longitude;
                    let distance = haversineDistance(userOffice.latitude, userOffice.longitude, latitude,
                        longitude);
                    document.getElementById('distance').value = distance;
                }, function(error) {
                    alert.error("Error getting coordinates: ", error);
                });
            } else {
                alert.error("Geolocation is not supported by this browser.");
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
@endsection
