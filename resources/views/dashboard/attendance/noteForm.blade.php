@extends('dashboard.layout.root')

@section('title', 'Attendance Note')

@push('styles')
    <style>
        .attendance-note-page {
            font-family: 'Inter', sans-serif;
            color: #0f172a;
        }

        .attendance-note-wrapper {
            min-height: calc(100vh - 100px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 12px 50px;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(99, 102, 241, 0.12),
                    transparent 30%
                ),
                radial-gradient(
                    circle at bottom left,
                    rgba(14, 165, 233, 0.10),
                    transparent 32%
                ),
                linear-gradient(
                    135deg,
                    #f8fafc 0%,
                    #eef2ff 50%,
                    #f8fafc 100%
                );
        }

        .attendance-note-card {
            position: relative;
            width: 100%;
            max-width: 720px;
            overflow: hidden;
            border: 1px solid #dbe3ee;
            border-radius: 28px;
            background: #ffffff;
            box-shadow:
                0 25px 70px rgba(15, 23, 42, 0.14),
                0 8px 24px rgba(79, 70, 229, 0.08);
        }

        .attendance-note-card::before {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            top: -150px;
            right: -100px;
            border-radius: 999px;
            background: rgba(99, 102, 241, 0.13);
        }

        .attendance-note-header {
            position: relative;
            overflow: hidden;
            padding: 30px;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(34, 211, 238, 0.18),
                    transparent 32%
                ),
                linear-gradient(
                    135deg,
                    #0f172a 0%,
                    #172554 55%,
                    #312e81 100%
                );
            color: #ffffff;
        }

        .attendance-note-header::after {
            content: '';
            position: absolute;
            width: 180px;
            height: 180px;
            left: -80px;
            bottom: -120px;
            border-radius: 999px;
            background: rgba(6, 182, 212, 0.16);
        }

        .header-icon-box {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 58px;
            height: 58px;
            border: 1px solid rgba(255, 255, 255, 0.20);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.12);
            color: #67e8f9;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }

        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 11px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.40);
            color: #c7d2fe;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .attendance-message-box {
            position: relative;
            border: 1px solid #c7d2fe;
            border-radius: 20px;
            padding: 20px;
            background:
                linear-gradient(
                    135deg,
                    #eef2ff,
                    #f8fafc
                );
        }

        .attendance-message-box::before {
            content: '\f075';
            position: absolute;
            right: 18px;
            top: 14px;
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            font-size: 30px;
            color: rgba(79, 70, 229, 0.10);
        }

        .helper-card {
            border: 1px solid #e2e8f0;
            border-radius: 15px;
            background: #f8fafc;
        }

        .voice-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 46px;
            border: 0;
            border-radius: 14px;
            padding: 11px 18px;
            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #4f46e5
                );
            color: #ffffff;
            font-size: 13px;
            font-weight: 800;
            box-shadow:
                0 12px 24px rgba(37, 99, 235, 0.22);
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                opacity 0.2s ease;
        }

        .voice-button:hover {
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow:
                0 16px 30px rgba(37, 99, 235, 0.28);
        }

        .voice-button:disabled {
            cursor: not-allowed;
            opacity: 0.55;
            transform: none;
            box-shadow: none;
        }

        .audio-wave {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            height: 16px;
        }

        .audio-wave span {
            width: 3px;
            height: 7px;
            border-radius: 999px;
            background: currentColor;
        }

        .voice-button.is-speaking .audio-wave span {
            animation:
                voiceWave 0.8s ease-in-out infinite;
        }

        .voice-button.is-speaking .audio-wave span:nth-child(2) {
            animation-delay: 0.15s;
        }

        .voice-button.is-speaking .audio-wave span:nth-child(3) {
            animation-delay: 0.30s;
        }

        .voice-button.is-speaking .audio-wave span:nth-child(4) {
            animation-delay: 0.45s;
        }

        @keyframes voiceWave {
            0%,
            100% {
                height: 6px;
            }

            50% {
                height: 16px;
            }
        }

        .note-field-wrapper {
            position: relative;
        }

        .note-field {
            width: 100%;
            min-height: 125px;
            resize: vertical;
            border: 1px solid #cbd5e1;
            border-radius: 16px;
            padding: 15px 16px 34px;
            background: #ffffff;
            color: #0f172a;
            font-size: 14px;
            line-height: 1.6;
            outline: none;
            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background 0.2s ease;
        }

        .note-field::placeholder {
            color: #94a3b8;
        }

        .note-field:focus {
            border-color: #6366f1;
            background: #ffffff;
            box-shadow:
                0 0 0 4px rgba(99, 102, 241, 0.12);
        }

        .note-counter {
            position: absolute;
            right: 13px;
            bottom: 11px;
            border-radius: 999px;
            padding: 3px 8px;
            background: #f1f5f9;
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
        }

        .submit-note-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            width: 100%;
            min-height: 52px;
            border: 0;
            border-radius: 15px;
            padding: 13px 18px;
            background:
                linear-gradient(
                    135deg,
                    #059669,
                    #10b981
                );
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
            box-shadow:
                0 13px 27px rgba(5, 150, 105, 0.24);
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                opacity 0.2s ease;
        }

        .submit-note-button:hover {
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow:
                0 17px 34px rgba(5, 150, 105, 0.30);
        }

        .submit-note-button:disabled {
            cursor: not-allowed;
            opacity: 0.65;
            transform: none;
            box-shadow: none;
        }

        .error-box {
            border: 1px solid #fecaca;
            border-radius: 16px;
            background: #fff1f2;
            color: #9f1239;
        }

        @media (max-width: 640px) {
            .attendance-note-wrapper {
                align-items: flex-start;
                padding: 8px 7px 30px;
            }

            .attendance-note-card {
                border-radius: 20px;
            }

            .attendance-note-header {
                padding: 22px 20px;
            }

            .attendance-note-content {
                padding: 20px !important;
            }

            .header-icon-box {
                width: 50px;
                height: 50px;
                border-radius: 15px;
            }

            .note-field {
                min-height: 110px;
            }

            .voice-button {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="attendance-note-page">
        <div class="attendance-note-wrapper">
            <div class="attendance-note-card">

                {{-- Header --}}
                <div class="attendance-note-header">
                    <div class="relative z-10 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="header-icon-box">
                                <i class="fas fa-clipboard-check text-2xl"></i>
                            </div>

                            <div>
                                <span class="header-badge">
                                    <i class="fas fa-user-clock"></i>
                                    Attendance Information
                                </span>

                                <h1 class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">
                                    Add Attendance Note
                                </h1>

                                <p class="mt-2 max-w-xl text-sm font-medium leading-6 text-indigo-100">
                                    Attendance से संबंधित note या reason लिखकर submit करें।
                                </p>
                            </div>
                        </div>

                        <div class="hidden text-right sm:block">
                            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-200">
                                Current Time
                            </p>

                            <p
                                id="currentTime"
                                class="mt-1 text-lg font-extrabold text-white"
                            >
                                {{ now()->format('h:i A') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="attendance-note-content space-y-5 p-6 sm:p-8">

                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="error-box p-4">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-rose-600">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>

                                <div>
                                    <h3 class="text-sm font-extrabold">
                                        Please check the following:
                                    </h3>

                                    <ul class="mt-2 list-disc space-y-1 pl-5">
                                        @foreach($errors->all() as $error)
                                            <li class="text-sm font-medium">
                                                {{ $error }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Attendance Message --}}
                    <div class="attendance-message-box">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-indigo-600 shadow-sm">
                                <i class="fas fa-comment-dots"></i>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-extrabold uppercase tracking-wider text-indigo-600">
                                    Attendance Message
                                </p>

                                <p
                                    id="attendanceMessage"
                                    class="mt-2 whitespace-pre-line text-sm font-semibold leading-7 text-slate-700 sm:text-base"
                                >
                                    {{ session('message') ?: 'Attendance note submit करने के लिए नीचे reason लिखें।' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Voice Control --}}
                    <div class="helper-card flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                                <i class="fas fa-volume-up"></i>
                            </div>

                            <div>
                                <p class="text-sm font-extrabold text-slate-800">
                                    Listen to Message
                                </p>

                                <p
                                    id="voiceHelpText"
                                    class="mt-1 text-xs font-medium text-slate-500"
                                >
                                    Voice autoplay की कोशिश होगी। जरूरत पड़ने पर Listen Again दबाएँ।
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            id="speakMessageBtn"
                            class="voice-button shrink-0"
                        >
                            <i class="fas fa-volume-up"></i>

                            <span id="voiceButtonText">
                                Listen Again
                            </span>

                            <span class="audio-wave">
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>
                    </div>

                    {{-- Note Form --}}
                    <form
                        id="attendanceNoteForm"
                        action="{{ route('attendance.user.note', [
                            'record' => $record->id,
                            'type' => $type
                        ]) }}"
                        method="POST"
                        class="space-y-4"
                    >
                        @csrf

                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <label
                                    for="note"
                                    class="text-sm font-extrabold text-slate-800"
                                >
                                    Note / Reason
                                </label>

                                <span class="text-xs font-semibold text-slate-500">
                                    Optional details
                                </span>
                            </div>

                            <div class="note-field-wrapper">
                                <textarea
                                    id="note"
                                    name="note"
                                    rows="4"
                                    maxlength="500"
                                    class="note-field"
                                    placeholder="Example: Traffic की वजह से देर हुई, गलती से जल्दी checkout हो गया, या कोई दूसरी जानकारी..."
                                >{{ old('note') }}</textarea>

                                <span
                                    id="noteCounter"
                                    class="note-counter"
                                >
                                    0 / 500
                                </span>
                            </div>
                        </div>

                        <button
                            type="submit"
                            id="submitNoteButton"
                            class="submit-note-button"
                        >
                            <i class="fas fa-check-circle"></i>

                            <span>
                                Submit Attendance Note
                            </span>
                        </button>
                    </form>

                    <div class="flex items-start gap-2 rounded-xl bg-amber-50 px-4 py-3 text-xs font-semibold leading-5 text-amber-800">
                        <i class="fas fa-info-circle mt-0.5"></i>

                        <span>
                            सही और स्पष्ट reason लिखें ताकि attendance record समझने में आसानी हो।
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const speechEngine =
                window.speechSynthesis;

            const messageElement =
                document.getElementById('attendanceMessage');

            const listenButton =
                document.getElementById('speakMessageBtn');

            const voiceButtonText =
                document.getElementById('voiceButtonText');

            const voiceHelpText =
                document.getElementById('voiceHelpText');

            const noteField =
                document.getElementById('note');

            const noteCounter =
                document.getElementById('noteCounter');

            const noteForm =
                document.getElementById('attendanceNoteForm');

            const submitButton =
                document.getElementById('submitNoteButton');

            const currentTimeElement =
                document.getElementById('currentTime');

            let selectedVoice = null;
            let activeUtterance = null;
            let isSpeaking = false;
            let isSubmitting = false;

            /*
            |--------------------------------------------------------------------------
            | Current Time
            |--------------------------------------------------------------------------
            */

            function updateCurrentTime() {
                if (!currentTimeElement) {
                    return;
                }

                currentTimeElement.textContent =
                    new Date().toLocaleTimeString(
                        'en-IN',
                        {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: true
                        }
                    );
            }

            updateCurrentTime();

            window.setInterval(
                updateCurrentTime,
                1000
            );

            /*
            |--------------------------------------------------------------------------
            | Note Counter
            |--------------------------------------------------------------------------
            */

            function updateNoteCounter() {
                if (
                    !noteField ||
                    !noteCounter
                ) {
                    return;
                }

                const currentLength =
                    noteField.value.length;

                noteCounter.textContent =
                    `${currentLength} / 500`;

                if (currentLength >= 450) {
                    noteCounter.classList.add(
                        'text-rose-600',
                        'bg-rose-50'
                    );
                } else {
                    noteCounter.classList.remove(
                        'text-rose-600',
                        'bg-rose-50'
                    );
                }
            }

            if (noteField) {
                updateNoteCounter();

                noteField.addEventListener(
                    'input',
                    updateNoteCounter
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Smart Text Conversion
            |--------------------------------------------------------------------------
            */

            function normalizeSpeechText(rawText) {
                if (!rawText) {
                    return '';
                }

                let text = rawText
                    .replace(/\r\n/g, ' ')
                    .replace(/\n+/g, '. ')
                    .replace(/\s+/g, ' ')
                    .trim();

                /*
                 * Complete early checkout sentence.
                 */
                text = text.replace(
                    /You\s+are\s+checking\s+out\s+(\d+)\s*(?:h|hr|hrs|hour|hours)\s*[,،]?\s*(\d+)\s*(?:m|min|mins|minute|minutes)\s+earlier\s+than\s+your\s+scheduled\s+time\.?/gi,
                    function (
                        match,
                        hours,
                        minutes
                    ) {
                        const hourText =
                            Number(hours) === 1
                                ? `${hours} घंटा`
                                : `${hours} घंटे`;

                        return `आप अपने निर्धारित समय से ${hourText} और ${minutes} मिनट पहले चेक आउट कर रहे हैं।`;
                    }
                );

                text = text.replace(
                    /Please\s+share\s+the\s+reason\s+for\s+early\s+check[\s-]?out\.?/gi,
                    'कृपया जल्दी चेक आउट करने का कारण बताएं।'
                );

                /*
                 * Numeric time units.
                 */
                text = text
                    .replace(
                        /(\d+(?:\.\d+)?)\s*(?:hours?|hrs?|hr|h)\b/gi,
                        function (
                            match,
                            value
                        ) {
                            return Number(value) === 1
                                ? `${value} घंटा`
                                : `${value} घंटे`;
                        }
                    )
                    .replace(
                        /(\d+(?:\.\d+)?)\s*(?:minutes?|mins?|min|m)\b/gi,
                        '$1 मिनट'
                    )
                    .replace(
                        /(\d+(?:\.\d+)?)\s*(?:seconds?|secs?|sec|s)\b/gi,
                        '$1 सेकंड'
                    );

                const replacements = [
                    [
                        /\bchecking\s+out\b/gi,
                        'चेक आउट कर रहे हैं'
                    ],
                    [
                        /\bchecking\s+in\b/gi,
                        'चेक इन कर रहे हैं'
                    ],
                    [
                        /\bchecked\s+out\b/gi,
                        'चेक आउट किया'
                    ],
                    [
                        /\bchecked\s+in\b/gi,
                        'चेक इन किया'
                    ],
                    [
                        /\bcheck[\s-]?out\b/gi,
                        'चेक आउट'
                    ],
                    [
                        /\bcheck[\s-]?in\b/gi,
                        'चेक इन'
                    ],
                    [
                        /\bscheduled\s+time\b/gi,
                        'निर्धारित समय'
                    ],
                    [
                        /\bearlier\s+than\b/gi,
                        'पहले'
                    ],
                    [
                        /\bearly\b/gi,
                        'जल्दी'
                    ],
                    [
                        /\breason\b/gi,
                        'कारण'
                    ],
                    [
                        /\battendance\b/gi,
                        'अटेंडेंस'
                    ],
                    [
                        /\boffice\b/gi,
                        'ऑफिस'
                    ],
                    [
                        /\blocation\b/gi,
                        'लोकेशन'
                    ],
                    [
                        /\bcamera\b/gi,
                        'कैमरा'
                    ],
                    [
                        /\bbreak\b/gi,
                        'ब्रेक'
                    ],
                    [
                        /\bsubmit\b/gi,
                        'सबमिट'
                    ],

                    [/\bnhi\b/gi, 'नहीं'],
                    [/\bnahi\b/gi, 'नहीं'],
                    [/\bnahin\b/gi, 'नहीं'],

                    [/\bhai\b/gi, 'है'],
                    [/\bhain\b/gi, 'हैं'],
                    [/\bhein\b/gi, 'हैं'],

                    [/\bho\b/gi, 'हो'],
                    [/\bgya\b/gi, 'गया'],
                    [/\bgaya\b/gi, 'गया'],
                    [/\bgyi\b/gi, 'गई'],
                    [/\bgayi\b/gi, 'गई'],

                    [/\bkr\b/gi, 'कर'],
                    [/\bkro\b/gi, 'करो'],
                    [/\bkaro\b/gi, 'करो'],

                    [/\bkyu\b/gi, 'क्यों'],
                    [/\bkyun\b/gi, 'क्यों'],
                    [/\bkya\b/gi, 'क्या'],

                    [/\bgalti\b/gi, 'गलती'],
                    [/\bdubara\b/gi, 'दोबारा'],
                    [/\bdobara\b/gi, 'दोबारा'],

                    [/\bjaldi\b/gi, 'जल्दी'],
                    [/\bder\b/gi, 'देर'],

                    [/\bsahi\b/gi, 'सही'],
                    [/\bthik\b/gi, 'ठीक'],
                    [/\btheek\b/gi, 'ठीक'],

                    [/\bplease\b/gi, 'कृपया'],
                    [/\bthanks\b/gi, 'धन्यवाद']
                ];

                replacements.forEach(
                    function (replacement) {
                        text = text.replace(
                            replacement[0],
                            replacement[1]
                        );
                    }
                );

                /*
                 * Numeric h पहले घंटे बन चुका है।
                 * अब अकेला h का मतलब "है" है।
                 */
                text = text
                    .replace(
                        /(^|[\s,.;:!?।])h(?=$|[\s,.;:!?।])/gi,
                        '$1है'
                    )
                    .replace(
                        /(^|[\s,.;:!?।])n(?=$|[\s,.;:!?।])/gi,
                        '$1नहीं'
                    );

                text = text
                    .replace(/\s*,\s*/g, ', ')
                    .replace(
                        /,\s*(?=\d+\s*(?:मिनट|घंटे|घंटा))/g,
                        ' और '
                    )
                    .replace(/[!?]+/g, '। ')
                    .replace(/\.{1,}/g, '। ')
                    .replace(/\s+/g, ' ')
                    .trim();

                if (
                    text &&
                    !/[।!?]$/.test(text)
                ) {
                    text += '।';
                }

                return text;
            }

            /*
            |--------------------------------------------------------------------------
            | Voice Selection
            |--------------------------------------------------------------------------
            */

            function selectBestVoice() {
                if (!speechEngine) {
                    return null;
                }

                const voices =
                    speechEngine.getVoices();

                if (!voices.length) {
                    selectedVoice = null;

                    return null;
                }

                const hindiVoices =
                    voices.filter(function (voice) {
                        return (
                            voice.lang || ''
                        )
                            .toLowerCase()
                            .startsWith('hi');
                    });

                const preferredNames = [
                    'swara',
                    'madhur',
                    'kalpana',
                    'hemant',
                    'google hindi',
                    'microsoft hindi',
                    'natural'
                ];

                for (
                    const preferredName of preferredNames
                ) {
                    const matchedVoice =
                        hindiVoices.find(
                            function (voice) {
                                return (
                                    voice.name || ''
                                )
                                    .toLowerCase()
                                    .includes(
                                        preferredName
                                    );
                            }
                        );

                    if (matchedVoice) {
                        selectedVoice =
                            matchedVoice;

                        return selectedVoice;
                    }
                }

                selectedVoice =
                    hindiVoices[0] ||
                    voices.find(
                        function (voice) {
                            return (
                                voice.lang || ''
                            )
                                .toLowerCase()
                                .includes('en-in');
                        }
                    ) ||
                    voices.find(
                        function (voice) {
                            return voice.default;
                        }
                    ) ||
                    voices[0];

                return selectedVoice;
            }

            if (speechEngine) {
                selectBestVoice();

                speechEngine.addEventListener(
                    'voiceschanged',
                    selectBestVoice
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Voice UI
            |--------------------------------------------------------------------------
            */

            function setPlayingState() {
                isSpeaking = true;

                if (listenButton) {
                    listenButton.classList.add(
                        'is-speaking'
                    );
                }

                if (voiceButtonText) {
                    voiceButtonText.textContent =
                        'Playing...';
                }

                if (voiceHelpText) {
                    voiceHelpText.textContent =
                        'Attendance message चल रहा है।';
                }
            }

            function resetPlayingState() {
                isSpeaking = false;

                if (listenButton) {
                    listenButton.classList.remove(
                        'is-speaking'
                    );

                    listenButton.disabled = false;
                }

                if (voiceButtonText) {
                    voiceButtonText.textContent =
                        'Listen Again';
                }

                if (voiceHelpText) {
                    voiceHelpText.textContent =
                        'Message सुनने के लिए Listen Again दबाएँ।';
                }
            }

            function stopSpeech() {
                if (!speechEngine) {
                    return;
                }

                speechEngine.cancel();
                speechEngine.resume();

                activeUtterance = null;
                isSpeaking = false;
            }

            /*
            |--------------------------------------------------------------------------
            | Play Voice
            |--------------------------------------------------------------------------
            */

            function playVoice() {
                if (
                    !speechEngine ||
                    !('SpeechSynthesisUtterance' in window)
                ) {
                    if (listenButton) {
                        listenButton.disabled = true;
                    }

                    if (voiceButtonText) {
                        voiceButtonText.textContent =
                            'Voice Not Supported';
                    }

                    if (voiceHelpText) {
                        voiceHelpText.textContent =
                            'इस device या browser में voice उपलब्ध नहीं है।';
                    }

                    return;
                }

                if (!messageElement) {
                    return;
                }

                const rawText =
                    messageElement.innerText.trim();

                const speechText =
                    normalizeSpeechText(rawText);

                if (!speechText) {
                    return;
                }

                stopSpeech();

                selectBestVoice();

                const utterance =
                    new SpeechSynthesisUtterance(
                        speechText
                    );

                activeUtterance =
                    utterance;

                if (selectedVoice) {
                    utterance.voice =
                        selectedVoice;

                    utterance.lang =
                        selectedVoice.lang ||
                        'hi-IN';
                } else {
                    utterance.lang =
                        'hi-IN';
                }

                utterance.rate = 0.90;
                utterance.pitch = 1;
                utterance.volume = 1;

                utterance.onstart =
                    function () {
                        setPlayingState();
                    };

                utterance.onend =
                    function () {
                        activeUtterance = null;

                        resetPlayingState();
                    };

                utterance.onerror =
                    function (event) {
                        console.error(
                            'Speech error:',
                            event.error
                        );

                        activeUtterance = null;

                        resetPlayingState();

                        if (voiceHelpText) {
                            voiceHelpText.textContent =
                                'Voice autoplay नहीं चला। Listen Again button दबाएँ।';
                        }
                    };

                speechEngine.resume();

                speechEngine.speak(
                    utterance
                );

                window.setTimeout(
                    function () {
                        if (speechEngine.paused) {
                            speechEngine.resume();
                        }
                    },
                    100
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Listen Again Button
            |--------------------------------------------------------------------------
            */

            if (listenButton) {
                if (
                    !speechEngine ||
                    !('SpeechSynthesisUtterance' in window)
                ) {
                    listenButton.disabled = true;

                    voiceButtonText.textContent =
                        'Voice Not Supported';
                } else {
                    listenButton.addEventListener(
                        'click',
                        function () {
                            if (
                                speechEngine.speaking ||
                                isSpeaking
                            ) {
                                stopSpeech();
                                resetPlayingState();

                                return;
                            }

                            playVoice();
                        }
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Autoplay Attempt
            |--------------------------------------------------------------------------
            */

            window.setTimeout(
                function () {
                    if (
                        speechEngine &&
                        'SpeechSynthesisUtterance' in window
                    ) {
                        playVoice();
                    }
                },
                700
            );

            /*
            |--------------------------------------------------------------------------
            | Form Submit
            |--------------------------------------------------------------------------
            */

            if (noteForm) {
                noteForm.addEventListener(
                    'submit',
                    function (event) {
                        if (isSubmitting) {
                            event.preventDefault();

                            return;
                        }

                        isSubmitting = true;

                        stopSpeech();

                        if (submitButton) {
                            submitButton.disabled =
                                true;

                            submitButton.innerHTML =
                                '<i class="fas fa-spinner fa-spin"></i><span>Submitting Note...</span>';
                        }
                    }
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Cleanup
            |--------------------------------------------------------------------------
            */

            window.addEventListener(
                'beforeunload',
                function () {
                    stopSpeech();

                    if (speechEngine) {
                        speechEngine.removeEventListener(
                            'voiceschanged',
                            selectBestVoice
                        );
                    }
                }
            );
        });
    </script>
@endpush