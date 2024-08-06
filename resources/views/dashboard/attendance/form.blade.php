@extends('dashboard.layout.root')

@section('content')
    <div class="container-fluid mt-3 mt-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <h1 class="text-center mb-4">Capture Image from Camera</h1>

                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <video id="video" class="w-100 border border-secondary rounded" autoplay></video>
                            <canvas id="canvas" class="d-none"></canvas>
                            <img id="imagePreview" class="d-none img-fluid border border-secondary rounded mt-3"
                                alt="Captured image" />
                        </div>

                        <div class="d-grid gap-2">
                            <button id="snap" class="btn btn-primary">Capture</button>
                        </div>

                        <form
                            action="{{ $formType == 'check_in' ? route('attendance.check_in') : route('attendance.check_out') }}"
                            method="POST" enctype="multipart/form-data" id="uploadForm" class="mt-3">
                            @csrf
                            <div class="mb-3 d-none">
                                <input type="file" id="capturedImage" name="image" class="form-control">
                            </div>
                            <input type="text" name="latitude" id="latitude" placeholder="Latitude">
                            <input type="text" name="longitude" id="longitude" placeholder="Longitude">
                            <div class="d-grid">
                                <button type="submit" id="upload" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);


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
        // Check if the Geolocation API is available
        if (navigator.geolocation) {
            // Get the current position
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                },
                function(error) {
                    console.error("Error getting geolocation: ", error.message);
                },

            );
        } else {
            console.error("Geolocation is not supported by this browser.");
        }
    </script>
@endsection
