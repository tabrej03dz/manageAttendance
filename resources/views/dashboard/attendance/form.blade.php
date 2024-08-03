@extends('dashboard.layout.root')

@section('content')
    <div class="container mt-5">
        <h1>Capture Image from Camera</h1>
        <video id="video" autoplay></video>
        <button id="snap">Capture</button>
        <canvas id="canvas"></canvas>
        <script>
            // Get access to the camera
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    var video = document.getElementById('video');
                    video.srcObject = stream;
                    video.play();
                })
                .catch(function(err) {
                    console.log("An error occurred: " + err);
                });

            // Capture the image when the button is clicked
            document.getElementById('snap').addEventListener('click', function() {
                var canvas = document.getElementById('canvas');
                var video = document.getElementById('video');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);
                // The image can be accessed via canvas.toDataURL() or further processed
            });
        </script>
    </div>
@endsection
