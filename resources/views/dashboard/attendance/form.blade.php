@extends('dashboard.layout.root')

@section('content')
    <div class="container mt-5">
        <h1>Capture Image from Camera</h1>
        <video id="video" autoplay></video>
        <button id="snap">Capture</button>
        <canvas id="canvas" style="display: none;"></canvas>
        <img id="imagePreview" style="display: none; max-width: 100%; height: auto;"/>

        <form action="{{$formType == 'check_in' ? route('attendance.check_in') : route('attendance.check_out') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf
            <input type="file" id="capturedImage" name="image" disabled>
            <button type="submit" id="upload">Submit</button>
        </form>

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
                var imagePreview = document.getElementById('imagePreview');

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);

                // Convert the image to a data URL
                var dataURL = canvas.toDataURL('image/png');

                // Display the captured image and hide the video
                video.style.display = 'none';
                imagePreview.style.display = 'block';
                imagePreview.src = dataURL;

                // Convert the data URL to a Blob object
                fetch(dataURL)
                    .then(res => res.blob())
                    .then(blob => {
                        // Create a file from the Blob object
                        var file = new File([blob], 'capturedImage.png', { type: 'image/png' });

                        // Create a DataTransfer object and add the file to it
                        var dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);

                        // Set the file input's files property to the DataTransfer object's files
                        var input = document.getElementById('capturedImage');
                        input.files = dataTransfer.files;
                    });
            });

            // Add a submit event listener to the form
            document.getElementById('uploadForm').addEventListener('submit', function(e) {
                var input = document.getElementById('capturedImage');
                if (input.files.length === 0) {
                    e.preventDefault();
                    alert('Please capture an image first.');
                }
            });
        </script>
    </div>
@endsection
