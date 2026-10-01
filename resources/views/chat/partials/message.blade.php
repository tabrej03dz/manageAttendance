@php
    $isMine = (int) $message->sender_id === (int) auth()->id();
    $reply = $message->replyTo;
    if ($reply && (int) $reply->conversation_id !== (int) $message->conversation_id) { $reply = null; }
@endphp
<div class="message-row {{ $isMine ? 'mine' : 'other' }}" data-message-id="{{ $message->id }}">
    <div class="message-bubble">
        @if(!$isMine)
            <div class="message-sender">{{ $message->sender?->name ?? 'Deleted User' }}</div>
        @endif
        @if($reply)
            <div class="reply-box">
                <strong>{{ $reply->sender?->name ?? 'Deleted User' }}</strong>
                {{ \Illuminate\Support\Str::limit($reply->message ?: 'Attachment', 100) }}
            </div>
        @endif
        @if($message->message)
            <div class="message-text">{{ $message->message }}</div>
        @endif
        @if($message->attachment)
            <div class="attachment-box">
                <a class="file-link" href="{{ route('chat.attachment', $message->id) }}" target="_blank" rel="noopener noreferrer">
                    📎 {{ $message->attachment_name ?: 'Attachment' }}
                </a>
            </div>
        @endif
        <div class="message-time">{{ $message->created_at->format('d M, h:i A') }}</div>
        @if($canSend ?? false)
            <button type="button" class="reply-action"
                data-id="{{ $message->id }}"
                data-name="{{ $message->sender?->name ?? 'Deleted User' }}"
                data-message="{{ \Illuminate\Support\Str::limit($message->message ?: 'Attachment', 100) }}"
                onclick="replyFromButton(this)">Reply</button>
        @endif
    </div>
</div>
