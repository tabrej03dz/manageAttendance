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


{{-- @extends('dashboard.layout.root')

@section('content')
<div class="container py-4">
    <div class="card attendance-card">
        <div class="card-body text-center">
            <h2 class="display-4 fw-bold mb-0 current-time">09:15 AM</h2>
            <p class="text-muted current-date">Feb 01, 2024 - Thursday</p>
            
            <div class="punch-circle" id="punchCircle">
                <i class="fas fa-hand-pointer fa-3x text-danger"></i>
            </div>
            <p class="fs-5 punch-status">Punch In</p>
            
            <div class="row text-center mt-4">
                <div class="col-4">
                    <i class="fas fa-sign-in-alt text-danger mb-2"></i>
                    <p class="mb-0 punch-in-time">09:08 AM</p>
                    <small class="text-muted">Punch In</small>
                </div>
                <div class="col-4">
                    <i class="fas fa-sign-out-alt text-danger mb-2"></i>
                    <p class="mb-0 punch-out-time">--:-- --</p>
                    <small class="text-muted">Punch Out</small>
                </div>
                <div class="col-4">
                    <i class="fas fa-clock text-danger mb-2"></i>
                    <p class="mb-0 total-hours">--:--</p>
                    <small class="text-muted">Total Hours</small>
                </div>
            </div>
            
            <div class="mt-4">
                <button id="punchButton" class="btn btn-danger btn-lg w-100 punch-button">
                    <span class="arrow-container text-danger">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                    <span class="swipe-text">Swipe to Punch In</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .attendance-card {
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        background: linear-gradient(145deg, #ffffff, #f9d6d6);
        transition: transform 0.3s ease;
    }
    .attendance-card:hover {
        transform: translateY(-5px);
    }
    .punch-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(145deg, #ffe6e6, #ffffff);
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px auto;
        box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2), -5px -5px 15px #ffffff;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .punch-circle:hover {
        box-shadow: 8px 8px 20px rgba(0, 0, 0, 0.3), -8px -8px 20px #ffffff;
    }
    .punch-circle:active {
        box-shadow: inset 5px 5px 10px rgba(0, 0, 0, 0.2), inset -5px -5px 10px #ffffff;
    }
    .punch-button {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .arrow-container {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 30px;
        height: 30px;
        background-color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: transform 0.3s ease;
    }
    .punch-button.active .arrow-container {
        transform: translate(200px, -50%);
    }
    .punch-button.active .swipe-text {
        opacity: 0;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .attendance-card {
            padding: 20px;
        }
        .punch-circle {
            width: 100px;
            height: 100px;
        }
        .btn-lg {
            font-size: 1rem;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const punchButton = document.getElementById('punchButton');
    const punchCircle = document.getElementById('punchCircle');
    const punchStatus = document.querySelector('.punch-status');
    const punchInTime = document.querySelector('.punch-in-time');
    const punchOutTime = document.querySelector('.punch-out-time');
    const totalHours = document.querySelector('.total-hours');
    const currentTime = document.querySelector('.current-time');
    const currentDate = document.querySelector('.current-date');
    
    let isPunchedIn = false;
    let startTime, endTime;

    function updateCurrentTime() {
        const now = new Date();
        currentTime.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        currentDate.textContent = now.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: '2-digit', weekday: 'long' });
    }

    setInterval(updateCurrentTime, 1000);
    updateCurrentTime();

    function handlePunch() {
        const now = new Date();
        if (!isPunchedIn) {
            startTime = now;
            punchInTime.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            punchStatus.textContent = 'Punch Out';
            punchButton.querySelector('.swipe-text').textContent = 'Swipe to Punch Out';
            isPunchedIn = true;
        } else {
            endTime = now;
            punchOutTime.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
            const diff = Math.abs(endTime - startTime);
            const hours = Math.floor(diff / 3600000);
            const minutes = Math.floor((diff % 3600000) / 60000);
            totalHours.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
            punchStatus.textContent = 'Punch In';
            punchButton.querySelector('.swipe-text').textContent = 'Swipe to Punch In';
            isPunchedIn = false;
        }
    }

    let startX;
    let isDragging = false;

    punchButton.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        isDragging = true;
    });

    punchButton.addEventListener('touchmove', function(e) {
        if (!isDragging) return;
        e.preventDefault();
        let diffX = e.touches[0].clientX - startX;
        if (diffX > 50) {
            this.classList.add('active');
        } else {
            this.classList.remove('active');
        }
    });

    punchButton.addEventListener('touchend', function() {
        isDragging = false;
        if (this.classList.contains('active')) {
            handlePunch();
        }
        this.classList.remove('active');
    });

    punchCircle.addEventListener('click', handlePunch);
});
</script>
@endsection --}}

{{-- //Records --}}

@extends('dashboard.layout.root')

@section('content')
<!-- Include Bootstrap CSS and FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
    /* Calendar Styles */
    #calendar {
        max-width: 100%;
        margin: 0 auto;
        border-radius: 15px;
        overflow: hidden;
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
        font-size: 1.6rem;
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
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
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
        color: #666;
        font-size: 0.9rem;
    }

    .total-hours {
        font-size: 1rem;
        color: #333;
        font-weight: bold;
    }

    /* Hover effect for calendar events */
    .fc-event:hover {
        background-color: #ff5733 !important;
        opacity: 0.9;
    }

    /* Responsive Styles */
    @media (max-width: 576px) {
        .calendar-header h5 {
            font-size: 1.4rem;
        }

        .date-info {
            padding: 10px;
        }

        .badge {
            padding: 8px;
            font-size: 1.2rem;
        }

        .punch-info {
            font-size: 0.8rem;
        }

        .total-hours {
            font-size: 0.9rem;
        }
    }
</style>

<!-- FullCalendar Rendering -->
<div class="container pt-3">
    <div id="calendar"></div>
</div>

<!-- Attendance History Section -->
<div class="container mt-3 pb-3">
    <div class="calendar-card">
        <div class="calendar-header">
            <h5>Attendance History</h5>
            <button class="btn btn-link text-secondary">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
        </div>

        <!-- Date Info Entries -->
        <div class="date-info">
            <div class="badge bg-success">06</div>
            <div>
                <span class="punch-info">Punch In: 09:08 AM</span> 
                <span class="punch-info">Punch Out: 05:06 PM</span>
            </div>
            <div class="total-hours">Total Hours: 08:13</div>
        </div>

        <div class="date-info">
            <div class="badge bg-warning">07</div>
            <div>
                <span class="punch-info">Punch In: 09:08 AM</span> 
                <span class="punch-info">Punch Out: 05:06 PM</span>
            </div>
            <div class="total-hours">Total Hours: 08:13</div>
        </div>

        <div class="date-info">
            <div class="badge bg-danger">08</div>
            <div>
                <span class="punch-info">Punch In: 09:08 AM</span> 
                <span class="punch-info">Punch Out: 05:06 PM</span>
            </div>
            <div class="total-hours">Total Hours: 08:13</div>
        </div>

        <div class="date-info">
            <div class="badge bg-success">09</div>
            <div>
                <span class="punch-info">Punch In: 09:08 AM</span> 
                <span class="punch-info">Punch Out: 05:06 PM</span>
            </div>
            <div class="total-hours">Total Hours: 08:13</div>
        </div>
    </div>
</div>

<!-- Include jQuery, FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var holidays = [
            { title: 'New Year\'s Day', start: '2024-01-01' },
            { title: 'Independence Day', start: '2024-07-04' },
            { title: 'Thanksgiving', start: '2024-11-28' },
            { title: 'Christmas', start: '2024-12-25' },
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

@endsection










