@extends('dashboard.layout.root')

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
{{--                                    <input type="text" name="latitude" id="latitude" placeholder="Latitude">--}}
{{--                                    <input type="text" name="longitude" id="longitude" placeholder="Longitude">--}}
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

                    // document.getElementById('distance').value = distance;
                    // const apiKey = '41b12ec59af34ece8d9e93f4d49e76f1';
                    //
                    //
                    // let apiUrl = `https://api.opencagedata.com/geocode/v1/json?key=41b12ec59af34ece8d9e93f4d49e76f1&q=${latitude},${longitude}&pretty=1`;
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
@endsection
