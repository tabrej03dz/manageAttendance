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
        window.onload = function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                }, function(error) {
                    console.error("Error getting coordinates: ", error);
                });
            } else {
                console.error("Geolocation is not supported by this browser.");
            }
        }
    </script>
@endsection --}}


@extends('dashboard.layout.root')

@section('content')

<div class="container">
    <div class="card" style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border: none; border-radius: 20px; margin: 20px; padding: 40px 20px; text-align: center; background-color: white;"> 

        <!-- Time Display -->
        <div style="font-size: 48px; font-weight: bold;">09:15 AM</div>
        <p style="color: gray;">Feb 01, 2024 - Thursday</p>

        <!-- Punch In Button -->
        <div style="border-radius: 50%; width: 150px; height: 150px; margin: 20px auto; background-color: #f1f1f1; display: flex; justify-content: center; align-items: center;">
            <i class="fas fa-hand-pointer" style="font-size: 40px; color: #dc3545;"></i>
        </div>
        <p>Punch In</p>

        <!-- Log Time Section -->
        <div class="row text-center" style="margin-top: 20px;">
            <div class="col">
                <i class="fas fa-clock" style="font-size: 24px; color: #dc3545;"></i>
                <p class="mb-0">09:08 AM</p>
                <small>Punch In</small>
            </div>
            <div class="col">
                <i class="fas fa-clock" style="font-size: 24px; color: #dc3545;"></i>
                <p class="mb-0">06:05 PM</p>
                <small>Punch Out</small>
            </div>
            <div class="col">
                <i class="fas fa-clock" style="font-size: 24px; color: #dc3545;"></i>
                <p class="mb-0">08:13</p>
                <small>Total Hours</small>
            </div>
        </div>

        <div class="text-center" style="margin: 20px;">
            <div class="btn" id="punchButton" style="background-color: #dc3545; color: white; padding: 15px 30px; display: inline-flex; align-items: center; border-radius: 30px; position: relative; overflow: hidden;">
                <!-- Arrow inside a perfect circle -->
                <span id="arrowContainer" class="arrow-container" style="display: inline-flex; justify-content: center; align-items: center; background-color: white; color: #dc3545; width: 50px; height: 50px; border-radius: 50%; margin-right: 15px; position: relative;">
                    <i id="arrowIcon" class="fas fa-chevron-right" style="font-size: 20px;"></i>
                </span>
                Swipe right to Punch in
            </div>
        </div>
        
    </div>
</div>

<style>
    .slide {
        animation: slide 0.5s forwards; /* Slide on click */
    }
    
    @keyframes slide {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(10px); /* Adjust this value for slide distance */
        }
    }
</style>

<script>
    document.getElementById('punchButton').addEventListener('click', function() {
        const arrowContainer = document.getElementById('arrowContainer');
        arrowContainer.classList.add('slide');

        // Optional: Remove the class after animation ends to allow re-clicking
        arrowContainer.addEventListener('animationend', function() {
            arrowContainer.classList.remove('slide');
            arrowContainer.style.transform = 'translateX(0)'; // Reset position
        }, { once: true });
    });
</script>

@endsection


{{-- @extends('dashboard.layout.root')

@section('content')
<!-- Include Bootstrap CSS if not already included in your layout -->
<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

<div class="d-flex flex-column justify-content-center align-items-center vh-100">
    <h1 class="text-uppercase letter-spacing">Slide to Unlock</h1>
    <input type="range" value="0" class="form-range pullee" id="slider" min="0" max="150" />
</div>

<script>
    const inputRange = document.getElementById('slider');
    const maxValue = 150; // Higher value for smoother dragging
    const speed = 12; // Animation speed
    let currValue, rafID;

    // Listen for unlock start
    inputRange.addEventListener('mousedown', unlockStartHandler, false);
    inputRange.addEventListener('touchstart', unlockStartHandler, false);
    inputRange.addEventListener('mouseup', unlockEndHandler, false);
    inputRange.addEventListener('touchend', unlockEndHandler, false);

    function unlockStartHandler() {
        window.cancelAnimationFrame(rafID);
        currValue = +this.value; // Set to desired value
    }

    function unlockEndHandler() {
        currValue = +this.value; // Store current value
        if (currValue >= maxValue) {
            successHandler();
        } else {
            rafID = window.requestAnimationFrame(animateHandler);
        }
    }

    function animateHandler() {
        inputRange.value = currValue; // Update input range
        if (currValue > -1) {
            rafID = window.requestAnimationFrame(animateHandler); // Continue animation
        }
        currValue -= speed; // Decrement value
    }

    function successHandler() {
        alert('Unlocked');
        inputRange.value = 0; // Reset input range
    }
</script>

<style>
    .pullee {
        -webkit-appearance: none; /* Remove default styling */
        appearance: none;
    }

    .pullee::-webkit-slider-runnable-track {
        height: 1rem;
        background: #DDE0E3;
    }

    .pullee::-moz-range-track {
        height: 1rem;
        background: #DDE0E3;
    }

    .pullee::-ms-track {
        height: 1rem;
        background: #DDE0E3;
    }

    .pullee::-webkit-slider-thumb {
        -webkit-appearance: none; /* Remove default styling */
        appearance: none;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background: #5990DD;
        cursor: grabbing; /* Change cursor on hover */
    }

    .pullee::-moz-range-thumb {
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background: #5990DD;
        cursor: grabbing; /* Change cursor on hover */
    }

    .pullee::-ms-thumb {
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background: #5990DD;
        cursor: grabbing; /* Change cursor on hover */
    }

    .letter-spacing {
        letter-spacing: 1.25px;
    }
</style>
@endsection --}}


