@php
    $isMine = (int) $message->sender_id === (int) auth()->id();

    $senderName = $message->sender?->name ?? 'User';

    $replyMessage = $message->replyTo?->message ?? '';

    $attachmentUrl = $message->attachment
        ? \Illuminate\Support\Facades\Storage::disk('public')
            ->url($message->attachment)
        : null;
@endphp

<div
    class="message-row {{ $isMine ? 'mine' : 'other' }}"
    data-message-id="{{ $message->id }}"
>
    <div class="message-bubble">

        {{-- Sender Name --}}
        @if(!$isMine)
            <div class="message-sender">
                {{ $senderName }}
            </div>
        @endif


        {{-- Reply Message --}}
        @if($message->replyTo)

            <div class="reply-box">

                <strong>
                    {{ $message->replyTo->sender?->name ?? 'User' }}
                </strong>

                @if($message->replyTo->message)

                    {{ \Illuminate\Support\Str::limit(
                        $message->replyTo->message,
                        100
                    ) }}

                @elseif($message->replyTo->attachment)

                    📎 Attachment

                @endif

            </div>

        @endif


        {{-- Normal Message --}}
        @if($message->message)

            <div class="message-text">
                {{ $message->message }}
            </div>

        @endif


        {{-- Attachment --}}
        @if($message->attachment && $attachmentUrl)

            <div class="attachment-box">

                @if(
                    $message->attachment_type &&
                    str_starts_with(
                        $message->attachment_type,
                        'image/'
                    )
                )

                    <a
                        href="{{ $attachmentUrl }}"
                        target="_blank"
                    >
                        <img
                            src="{{ $attachmentUrl }}"
                            class="attachment-image"
                            alt="Attachment"
                        >
                    </a>

                @else

                    <a
                        href="{{ $attachmentUrl }}"
                        target="_blank"
                        class="file-link"
                    >
                        📄
                        {{ $message->attachment_name ?? 'Attachment' }}
                    </a>

                @endif

            </div>

        @endif


        {{-- Time --}}
        <div class="message-time">
            {{ $message->created_at->format('h:i A') }}
        </div>


        {{-- Reply Button --}}
        <button
            type="button"
            class="reply-action"
            data-id="{{ $message->id }}"
            data-name="{{ $senderName }}"
            data-message="{{ $message->message ?: 'Attachment' }}"
            onclick="replyFromButton(this)"
        >
            Reply
        </button>

    </div>
</div>
