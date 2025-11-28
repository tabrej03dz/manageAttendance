@extends('dashboard.layout.root')

@section('content')
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Icons -->
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

                <!-- Generated Message -->
                <div class="text-center mb-4">
                    <p id="attendanceMessage" class="text-red-600 font-medium whitespace-pre-line">
                        {{ session('message') }}
                    </p>
                </div>

                <!-- Voice Play Button -->
                <div class="mb-6">
                    <button type="button"
                            id="speakMessageBtn"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-600 text-white text-sm font-semibold shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <i class="fas fa-volume-up"></i>
                        Listen Again
                    </button>
                    <p class="mt-1 text-xs text-gray-500 text-center">
                        Message will autoplay. Click to listen again.
                    </p>
                </div>

                <!-- Reason Form -->
                <div class="w-full max-w-xs">
                    <form
                        action="{{ route('attendance.user.note', ['record' => $record->id, 'type' => $type]) }}"
                        method="post"
                        class="mt-3">
                        @csrf

                        <textarea class="form-control mb-2 w-full border border-gray-300 rounded-lg p-2 text-sm"
                                  name="note"
                                  cols="30"
                                  rows="3"
                                  placeholder="Write a note / reason here...">{{ old('note') }}</textarea>

                        <div class="d-grid">
                            <button type="submit" id="upload"
                                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-3 md:py-3 md:px-4 rounded-full w-full mb-3 flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i>Submit
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <!-- Text to Speech with Autoplay -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('speakMessageBtn');
            const msgEl = document.getElementById('attendanceMessage');

            if (!btn || !msgEl) return;

            // Check support
            if (!('speechSynthesis' in window)) {
                btn.disabled = true;
                btn.classList.add('opacity-60', 'cursor-not-allowed');
                btn.innerHTML = '<i class="fas fa-volume-mute mr-2"></i> Voice Not Supported';
                return;
            }

            let currentUtterance = null;

            // Speak Function
            function speakMessage() {
                const text = msgEl.innerText.trim();
                if (!text) return;

                // Stop if already speaking
                if (window.speechSynthesis.speaking) {
                    window.speechSynthesis.cancel();
                }

                const utterance = new SpeechSynthesisUtterance(text);
                currentUtterance = utterance;

                // Voice style
                utterance.lang = 'hi-IN';  // Hindi style voice
                utterance.rate = 0.92;
                utterance.pitch = 1;

                window.speechSynthesis.speak(utterance);
            }

            // Button Click → Play Again
            btn.addEventListener('click', speakMessage);

            // AUTOPLAY after load
            setTimeout(() => {
                speakMessage();
            }, 400);  // Small delay to load page properly
        });
    </script>

@endsection
