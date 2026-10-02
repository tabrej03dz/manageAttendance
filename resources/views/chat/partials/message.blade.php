@php
    /*
    |--------------------------------------------------------------------------
    | Current User Message Check
    |--------------------------------------------------------------------------
    */
    $isMine = (int) $message->sender_id === (int) auth()->id();


    /*
    |--------------------------------------------------------------------------
    | Reply Message
    |--------------------------------------------------------------------------
    */
    $reply = $message->replyTo;

    // Security:
    // Reply sirf isi conversation ke message ka hona chahiye.
    if (
        $reply &&
        (int) $reply->conversation_id !== (int) $message->conversation_id
    ) {
        $reply = null;
    }


    /*
    |--------------------------------------------------------------------------
    | Attachment Information
    |--------------------------------------------------------------------------
    */
    $attachmentUrl = null;
    $attachmentName = null;
    $extension = null;

    $isImage = false;
    $isAudio = false;
    $isVideo = false;
    $isPdf = false;


    if ($message->attachment) {

        $attachmentUrl = route(
            'chat.attachment',
            $message->id
        );

        $attachmentName =
            $message->attachment_name
            ?: basename($message->attachment);


        $extension = strtolower(
            pathinfo(
                $attachmentName,
                PATHINFO_EXTENSION
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Supported File Types
        |--------------------------------------------------------------------------
        */

        $imageExtensions = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp',
            'bmp',
        ];


        $audioExtensions = [
            'mp3',
            'wav',
            'ogg',
            'm4a',
            'aac',
            'webm',
        ];


        $videoExtensions = [
            'mp4',
            'mov',
            'mkv',
            'avi',
            'webm',
        ];


        $isImage = in_array(
            $extension,
            $imageExtensions
        );


        $isAudio = in_array(
            $extension,
            $audioExtensions
        );


        $isVideo = in_array(
            $extension,
            $videoExtensions
        );


        $isPdf = $extension === 'pdf';
    }


    /*
    |--------------------------------------------------------------------------
    | Reply Preview Text
    |--------------------------------------------------------------------------
    */
    $replyPreview = 'Message';

    if ($reply) {

        if ($reply->message) {

            $replyPreview =
                \Illuminate\Support\Str::limit(
                    $reply->message,
                    100
                );

        } elseif ($reply->attachment) {

            $replyAttachmentName =
                $reply->attachment_name
                ?: basename($reply->attachment);

            $replyExtension = strtolower(
                pathinfo(
                    $replyAttachmentName,
                    PATHINFO_EXTENSION
                )
            );


            if (
                in_array(
                    $replyExtension,
                    [
                        'mp3',
                        'wav',
                        'ogg',
                        'm4a',
                        'aac',
                        'webm',
                    ]
                )
            ) {

                $replyPreview =
                    '🎤 Voice message';

            } elseif (
                in_array(
                    $replyExtension,
                    [
                        'jpg',
                        'jpeg',
                        'png',
                        'gif',
                        'webp',
                    ]
                )
            ) {

                $replyPreview =
                    '🖼️ Photo';

            } elseif ($replyExtension === 'pdf') {

                $replyPreview =
                    '📄 PDF';

            } else {

                $replyPreview =
                    '📎 ' .
                    $replyAttachmentName;
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Current Message Reply Button Preview
    |--------------------------------------------------------------------------
    */
    if ($message->message) {

        $currentMessagePreview =
            \Illuminate\Support\Str::limit(
                $message->message,
                100
            );

    } elseif ($isAudio) {

        $currentMessagePreview =
            '🎤 Voice message';

    } elseif ($isImage) {

        $currentMessagePreview =
            '🖼️ Photo';

    } elseif ($isVideo) {

        $currentMessagePreview =
            '🎥 Video';

    } elseif ($isPdf) {

        $currentMessagePreview =
            '📄 PDF';

    } elseif ($message->attachment) {

        $currentMessagePreview =
            '📎 ' .
            ($attachmentName ?: 'Attachment');

    } else {

        $currentMessagePreview =
            'Message';
    }
@endphp


<div
    class="message-row {{ $isMine ? 'mine' : 'other' }}"
    data-message-id="{{ $message->id }}"
>

    <div class="message-bubble">


        {{-- ============================================================= --}}
        {{-- Sender Name --}}
        {{-- ============================================================= --}}

        @if(!$isMine)

            <div class="message-sender">

                {{ $message->sender?->name ?? 'Deleted User' }}

            </div>

        @endif



        {{-- ============================================================= --}}
        {{-- Reply Message Preview --}}
        {{-- ============================================================= --}}

        @if($reply)

            <div
                class="reply-box"
                data-reply-message-id="{{ $reply->id }}"
            >

                <div class="reply-sender">

                    {{ $reply->sender?->name ?? 'Deleted User' }}

                </div>


                <div class="reply-preview">

                    {{ $replyPreview }}

                </div>

            </div>

        @endif



        {{-- ============================================================= --}}
        {{-- Text Message --}}
        {{-- ============================================================= --}}

        @if($message->message)

            <div class="message-text">

                {!! nl2br(
                    e($message->message)
                ) !!}

            </div>

        @endif



        {{-- ============================================================= --}}
        {{-- Attachment --}}
        {{-- ============================================================= --}}

        @if($message->attachment)

            <div class="attachment-box">


                {{-- ===================================================== --}}
                {{-- IMAGE --}}
                {{-- ===================================================== --}}

                @if($isImage)

                    <div class="image-attachment">

                        <a
                            href="{{ $attachmentUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="chat-image-link"
                        >

                            <img
                                src="{{ $attachmentUrl }}"
                                alt="{{ $attachmentName }}"
                                class="chat-image"
                                loading="lazy"
                            >

                        </a>


                        @if($attachmentName)

                            <div class="attachment-file-name">

                                {{ $attachmentName }}

                            </div>

                        @endif

                    </div>



                {{-- ===================================================== --}}
                {{-- AUDIO / VOICE NOTE --}}
                {{-- ===================================================== --}}

                @elseif($isAudio)

                    <div class="voice-note-container">

                        <div class="voice-note-icon">
                            🎤
                        </div>


                        <div class="voice-note-content">

                            <div class="voice-note-title">

                                Voice message

                            </div>


                            <audio
                                controls
                                preload="metadata"
                                class="voice-note-player"
                            >

                                <source
                                    src="{{ $attachmentUrl }}"
                                >

                                Your browser does not support audio playback.

                            </audio>

                        </div>

                    </div>



                {{-- ===================================================== --}}
                {{-- VIDEO --}}
                {{-- ===================================================== --}}

                @elseif($isVideo)

                    <div class="video-attachment">

                        <video
                            controls
                            preload="metadata"
                            class="chat-video"
                        >

                            <source
                                src="{{ $attachmentUrl }}"
                            >

                            Your browser does not support video playback.

                        </video>


                        <div class="attachment-file-name">

                            {{ $attachmentName }}

                        </div>

                    </div>



                {{-- ===================================================== --}}
                {{-- PDF --}}
                {{-- ===================================================== --}}

                @elseif($isPdf)

                    <a
                        href="{{ $attachmentUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="document-attachment"
                    >

                        <div class="document-icon">

                            📄

                        </div>


                        <div class="document-info">

                            <div class="document-name">

                                {{ $attachmentName }}

                            </div>

                            <div class="document-type">

                                PDF Document

                            </div>

                        </div>

                    </a>



                {{-- ===================================================== --}}
                {{-- OTHER DOCUMENT / FILE --}}
                {{-- ===================================================== --}}

                @else

                    <a
                        href="{{ $attachmentUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="document-attachment"
                    >

                        <div class="document-icon">

                            📎

                        </div>


                        <div class="document-info">

                            <div class="document-name">

                                {{ $attachmentName }}

                            </div>


                            <div class="document-type">

                                {{ strtoupper($extension ?: 'FILE') }}

                            </div>

                        </div>

                    </a>

                @endif


            </div>

        @endif



        {{-- ============================================================= --}}
        {{-- Bottom Information --}}
        {{-- ============================================================= --}}

        <div class="message-bottom">


            {{-- Time --}}

            <div class="message-time">

                {{ $message->created_at->format('d M, h:i A') }}

            </div>



            {{-- Reply Button --}}

            @if($canSend ?? false)

                <button
                    type="button"
                    class="reply-action"

                    data-id="{{ $message->id }}"

                    data-name="{{ $message->sender?->name ?? 'Deleted User' }}"

                    data-message="{{ $currentMessagePreview }}"

                    onclick="replyFromButton(this)"
                >

                    Reply

                </button>

            @endif


        </div>


    </div>

</div>


<style>

    /*
    |--------------------------------------------------------------------------
    | Attachment
    |--------------------------------------------------------------------------
    */

    .attachment-box {
        margin-top: 8px;
        max-width: 100%;
    }


    /*
    |--------------------------------------------------------------------------
    | Image
    |--------------------------------------------------------------------------
    */

    .image-attachment {
        max-width: 300px;
    }

    .chat-image-link {
        display: block;
        text-decoration: none;
    }

    .chat-image {
        display: block;

        width: auto;
        max-width: 100%;
        max-height: 350px;

        object-fit: cover;

        border-radius: 12px;

        cursor: pointer;

        background: #f3f4f6;
    }

    .chat-image:hover {
        opacity: .95;
    }


    /*
    |--------------------------------------------------------------------------
    | Attachment Filename
    |--------------------------------------------------------------------------
    */

    .attachment-file-name {
        margin-top: 5px;

        font-size: 11px;

        opacity: .7;

        overflow: hidden;

        white-space: nowrap;

        text-overflow: ellipsis;
    }


    /*
    |--------------------------------------------------------------------------
    | Voice Note
    |--------------------------------------------------------------------------
    */

    .voice-note-container {

        display: flex;

        align-items: center;

        gap: 10px;

        min-width: 260px;

        max-width: 320px;

        padding: 8px;

        border-radius: 12px;

        background: rgba(
            255,
            255,
            255,
            .15
        );
    }


    .voice-note-icon {

        width: 40px;

        height: 40px;

        min-width: 40px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 50%;

        font-size: 19px;

        background: rgba(
            0,
            0,
            0,
            .08
        );
    }


    .voice-note-content {

        flex: 1;

        min-width: 0;
    }


    .voice-note-title {

        font-size: 11px;

        margin-bottom: 3px;

        opacity: .75;
    }


    .voice-note-player {

        width: 220px;

        max-width: 100%;

        height: 36px;

        display: block;
    }


    /*
    |--------------------------------------------------------------------------
    | Video
    |--------------------------------------------------------------------------
    */

    .video-attachment {

        width: 280px;

        max-width: 100%;
    }


    .chat-video {

        width: 100%;

        max-height: 350px;

        border-radius: 12px;

        background: #000;
    }


    /*
    |--------------------------------------------------------------------------
    | Document
    |--------------------------------------------------------------------------
    */

    .document-attachment {

        display: flex;

        align-items: center;

        gap: 10px;

        width: 280px;

        max-width: 100%;

        padding: 10px;

        border-radius: 12px;

        background: rgba(
            0,
            0,
            0,
            .05
        );

        text-decoration: none;

        color: inherit;
    }


    .document-attachment:hover {

        background: rgba(
            0,
            0,
            0,
            .08
        );
    }


    .document-icon {

        width: 42px;

        height: 42px;

        min-width: 42px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 10px;

        background: rgba(
            255,
            255,
            255,
            .4
        );

        font-size: 21px;
    }


    .document-info {

        min-width: 0;

        flex: 1;
    }


    .document-name {

        font-size: 13px;

        font-weight: 600;

        overflow: hidden;

        white-space: nowrap;

        text-overflow: ellipsis;
    }


    .document-type {

        margin-top: 2px;

        font-size: 10px;

        opacity: .65;
    }


    /*
    |--------------------------------------------------------------------------
    | Reply Box
    |--------------------------------------------------------------------------
    */

    .reply-box {

        margin-bottom: 7px;

        padding: 7px 9px;

        border-left: 3px solid currentColor;

        border-radius: 7px;

        background: rgba(
            0,
            0,
            0,
            .05
        );
    }


    .reply-sender {

        font-size: 11px;

        font-weight: 700;

        margin-bottom: 2px;
    }


    .reply-preview {

        font-size: 11px;

        opacity: .75;

        overflow: hidden;

        white-space: nowrap;

        text-overflow: ellipsis;
    }


    /*
    |--------------------------------------------------------------------------
    | Message Text
    |--------------------------------------------------------------------------
    */

    .message-text {

        word-break: break-word;

        white-space: normal;
    }


    /*
    |--------------------------------------------------------------------------
    | Bottom
    |--------------------------------------------------------------------------
    */

    .message-bottom {

        display: flex;

        align-items: center;

        justify-content: flex-end;

        gap: 8px;

        margin-top: 5px;
    }


    .message-time {

        font-size: 9px;

        opacity: .6;

        white-space: nowrap;
    }


    /*
    |--------------------------------------------------------------------------
    | Reply Button
    |--------------------------------------------------------------------------
    */

    .reply-action {

        border: 0;

        padding: 0;

        margin: 0;

        background: transparent;

        cursor: pointer;

        font-size: 10px;

        color: inherit;

        opacity: .7;
    }


    .reply-action:hover {

        opacity: 1;

        text-decoration: underline;
    }


    /*
    |--------------------------------------------------------------------------
    | Mobile
    |--------------------------------------------------------------------------
    */

    @media (
        max-width: 640px
    ) {

        .chat-image {

            max-width: 240px;

            max-height: 300px;
        }


        .voice-note-container {

            min-width: 220px;

            max-width: 250px;
        }


        .voice-note-player {

            width: 180px;
        }


        .document-attachment {

            width: 230px;
        }


        .video-attachment {

            width: 240px;
        }

    }

</style>