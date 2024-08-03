@extends('dashboard.layout.root')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<h1 class="flex justify-center font-serif text-blue-400 font-bold md:text-xl lg:text-2xl">Capture Image from Camera</h1>
    <div class="container lg:mt-5 mt-2 flex justify-center">
        <video id="video" autoplay class="mb-4 border-4 border-red-500 rounded-lg shadow-lg"></video>

        <canvas id="canvas" class="hidden"></canvas>
        <img id="imagePreview" class="hidden max-w-full h-auto rounded-lg shadow-lg" />


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
    <div class="flex justify-center w-full">
        <form action="{{ $formType == 'check_in' ? route('attendance.check_in') : route('attendance.check_out') }}" method="POST" enctype="multipart/form-data" id="uploadForm" class=" bg-white w-auto p-6 ">
            @csrf
            <div class="mb-4">
                <input type="file" id="capturedImage" name="image" class="border-2 border-black p-2 rounded w-full">
            </div>
        </form>
        <div class="flex justify-center gap-2 m-4">
            <button type="submit" id="upload"  class="px-4 py-2 bg-green-400 rounded-full italic text-white hover:bg-green-500">Submit</button>
            <button type="button" id="snap" class="px-4 py-2 bg-blue-400 rounded-full italic text-white hover:bg-blue-500">Capture</button>
        </div>
    </div>

@endsection
