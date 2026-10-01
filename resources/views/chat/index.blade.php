{{-- @extends('layouts.app') --}}

@extends('dashboard.layout.root')
@section('content')

<style>
    .chat-wrapper {
        height: calc(100vh - 100px);
        min-height: 600px;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 15px rgba(0,0,0,.08);
        display: flex;
    }

    .chat-sidebar {
        width: 340px;
        min-width: 340px;
        border-right: 1px solid #e5e7eb;
        background: #fff;
        display: flex;
        flex-direction: column;
    }

    .chat-sidebar-header {
        padding: 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .chat-sidebar-title {
        font-size: 22px;
        font-weight: 700;
        margin: 0;
    }

    .chat-new-users {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
    }

    .chat-new-users select {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px;
        background: white;
    }

    .chat-list {
        flex: 1;
        overflow-y: auto;
    }

    .chat-list-item {
        display: flex;
        gap: 12px;
        padding: 14px;
        border-bottom: 1px solid #f1f1f1;
        text-decoration: none !important;
        color: #111827 !important;
        transition: .2s;
    }

    .chat-list-item:hover {
        background: #f9fafb;
    }

    .chat-list-item.active {
        background: #eef6ff;
    }

    .chat-avatar {
        width: 46px;
        height: 46px;
        min-width: 46px;
        border-radius: 50%;
        background: #2563eb;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 17px;
        text-transform: uppercase;
    }

    .chat-avatar.team {
        background: #7c3aed;
    }

    .chat-list-content {
        min-width: 0;
        flex: 1;
    }

    .chat-list-top {
        display: flex;
        justify-content: space-between;
        gap: 8px;
    }

    .chat-list-name {
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .chat-list-time {
        color: #9ca3af;
        font-size: 11px;
        white-space: nowrap;
    }

    .chat-last-message {
        color: #6b7280;
        font-size: 13px;
        margin-top: 4px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: flex;
        justify-content: space-between;
        gap: 5px;
    }

    .unread-badge {
        background: #22c55e;
        color: #fff;
        min-width: 20px;
        height: 20px;
        border-radius: 10px;
        padding: 0 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
    }

    .chat-main {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        background: #f4f7fa;
    }

    .chat-main-header {
        height: 70px;
        min-height: 70px;
        padding: 12px 20px;
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .mobile-back {
        display: none;
        border: 0;
        background: transparent;
        font-size: 23px;
        cursor: pointer;
    }

    .chat-main-name {
        font-size: 16px;
        font-weight: 700;
    }

    .chat-main-subtitle {
        font-size: 12px;
        color: #6b7280;
    }

    .messages-container {
        flex: 1;
        overflow-y: auto;
        padding: 22px;
    }

    .message-row {
        width: 100%;
        display: flex;
        margin-bottom: 10px;
    }

    .message-row.mine {
        justify-content: flex-end;
    }

    .message-row.other {
        justify-content: flex-start;
    }

    .message-bubble {
        max-width: 70%;
        padding: 9px 12px 6px;
        border-radius: 12px;
        position: relative;
        box-shadow: 0 1px 2px rgba(0,0,0,.08);
        word-break: break-word;
    }

    .message-row.mine .message-bubble {
        background: #dcf8c6;
        border-bottom-right-radius: 3px;
    }

    .message-row.other .message-bubble {
        background: #fff;
        border-bottom-left-radius: 3px;
    }

    .message-sender {
        font-size: 11px;
        font-weight: 700;
        color: #2563eb;
        margin-bottom: 3px;
    }

    .message-text {
        font-size: 14px;
        color: #111827;
        white-space: pre-wrap;
    }

    .message-time {
        text-align: right;
        color: #6b7280;
        font-size: 10px;
        margin-top: 3px;
    }

    .reply-box {
        border-left: 3px solid #2563eb;
        background: rgba(0,0,0,.04);
        border-radius: 4px;
        padding: 6px 8px;
        margin-bottom: 6px;
        font-size: 11px;
    }

    .reply-box strong {
        display: block;
        color: #2563eb;
    }

    .attachment-box {
        margin-top: 7px;
    }

    .attachment-image {
        max-width: 250px;
        max-height: 250px;
        border-radius: 8px;
        display: block;
    }

    .file-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 8px;
        border-radius: 6px;
        background: rgba(255,255,255,.5);
        color: #2563eb;
        text-decoration: none;
    }

    .chat-input-area {
        background: #fff;
        padding: 12px 15px;
        border-top: 1px solid #e5e7eb;
    }

    .reply-preview {
        display: none;
        background: #f3f4f6;
        border-left: 4px solid #2563eb;
        padding: 8px 12px;
        margin-bottom: 8px;
        border-radius: 5px;
        position: relative;
    }

    .reply-preview-close {
        position: absolute;
        right: 10px;
        top: 5px;
        border: none;
        background: transparent;
        font-size: 18px;
        cursor: pointer;
    }

    .chat-form {
        display: flex;
        align-items: flex-end;
        gap: 8px;
    }

    .chat-message-input {
        flex: 1;
        resize: none;
        min-height: 44px;
        max-height: 120px;
        border: 1px solid #d1d5db;
        border-radius: 22px;
        padding: 11px 15px;
        outline: none;
    }

    .attachment-button,
    .send-button {
        width: 44px;
        height: 44px;
        min-width: 44px;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .attachment-button {
        background: #f3f4f6;
        color: #374151;
    }

    .send-button {
        background: #2563eb;
        color: white;
    }

    .empty-chat {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6b7280;
        padding: 30px;
    }

    .empty-chat-icon {
        font-size: 60px;
        margin-bottom: 15px;
    }

    .reply-action {
        border: none;
        background: transparent;
        color: #6b7280;
        font-size: 10px;
        padding: 0;
        margin-top: 3px;
        cursor: pointer;
    }

    @media(max-width: 768px) {
        .chat-wrapper {
            height: calc(100vh - 70px);
            min-height: 500px;
            border-radius: 0;
        }

        .chat-sidebar {
            width: 100%;
            min-width: 100%;
        }

        .chat-main {
            display: none;
            width: 100%;
        }

        .chat-wrapper.chat-open .chat-sidebar {
            display: none;
        }

        .chat-wrapper.chat-open .chat-main {
            display: flex;
        }

        .mobile-back {
            display: block;
        }

        .message-bubble {
            max-width: 85%;
        }
    }
</style>

<div class="container-fluid py-3">

    <div class="chat-wrapper {{ isset($conversation) ? 'chat-open' : '' }}">

        {{-- LEFT SIDEBAR --}}
        <div class="chat-sidebar">

            <div class="chat-sidebar-header">
                <h4 class="chat-sidebar-title">
                    Messages
                </h4>


                {{-- @if(
    auth()->user()->hasRole('team_leader') ||
    auth()->user()->hasRole('manager')
) --}}

@if(auth()->user()->hasRole('Team Leader'))

    <form
        action="{{ route('chat.team.create') }}"
        method="POST"
        class="mt-2"
    >
        @csrf

        <button
            type="submit"
            class="btn btn-primary btn-sm w-100"
        >
            👥 Message Entire Team
        </button>

    </form>

@endif

    {{-- <form
        action="{{ route('chat.team.create') }}"
        method="POST"
        class="mt-2"
    >

        @csrf

        <button
            type="submit"
            class="btn btn-primary btn-sm w-100"
        >
            👥 Message Entire Team
        </button>

    </form>

@endif --}}

                <small class="text-muted">
                    {{ auth()->user()->name }}
                </small>
            </div>

            {{-- START NEW CHAT --}}
            @if(isset($allowedUsers) && $allowedUsers->count())
                <div class="chat-new-users">

                    <select id="newChatUser">
                        <option value="">
                            + Start New Chat
                        </option>

                        @foreach($allowedUsers as $chatUser)
                            <option value="{{ $chatUser->id }}">
                                {{ $chatUser->name }}
                            </option>
                        @endforeach
                    </select>

                </div>
            @endif

            <div class="chat-list">

                @forelse($conversations as $item)

                    @php
                        $otherUser = null;

                        if ($item->type === 'private') {
                            $otherUser = $item->users
                                ->firstWhere(
                                    'id',
                                    '!=',
                                    auth()->id()
                                );
                        }

                        $conversationName =
                            $item->type === 'team'
                                ? ($item->name ?? 'Team Chat')
                                : ($otherUser?->name ?? 'Private Chat');

                        $initial = mb_substr(
                            $conversationName,
                            0,
                            1
                        );

                        $latest = $item->latestMessage ?? null;
                    @endphp

                    <a
                        href="{{ route('chat.show', $item->id) }}"
                        class="chat-list-item
                        {{ isset($conversation) && $conversation->id === $item->id ? 'active' : '' }}"
                    >

                        <div class="chat-avatar {{ $item->type === 'team' ? 'team' : '' }}">
                            {{ $initial }}
                        </div>

                        <div class="chat-list-content">

                            <div class="chat-list-top">

                                <div class="chat-list-name">
                                    {{ $conversationName }}
                                </div>

                                @if($latest)
                                    <div class="chat-list-time">
                                        {{ $latest->created_at->format('h:i A') }}
                                    </div>
                                @endif

                            </div>

                            <div class="chat-last-message">

                                <span>
                                    @if($latest)

                                        @if($latest->sender_id === auth()->id())
                                            You:
                                        @endif

                                        {{ \Illuminate\Support\Str::limit(
                                            $latest->message ?: 'Attachment',
                                            35
                                        ) }}

                                    @else
                                        No messages yet
                                    @endif
                                </span>

                                @if(
                                    isset($item->unread_count) &&
                                    $item->unread_count > 0
                                )
                                    <span class="unread-badge">
                                        {{ $item->unread_count }}
                                    </span>
                                @endif

                            </div>

                        </div>

                    </a>

                @empty

                    <div class="p-4 text-center text-muted">
                        No conversations yet.
                    </div>

                @endforelse

            </div>

        </div>

        {{-- RIGHT CHAT --}}
        <div class="chat-main">

            @if(isset($conversation))

                @php

                    $otherUser = null;

                    if ($conversation->type === 'private') {
                        $otherUser = $conversation->users
                            ->firstWhere(
                                'id',
                                '!=',
                                auth()->id()
                            );
                    }

                    $chatName =
                        $conversation->type === 'team'
                            ? ($conversation->name ?? 'Team Chat')
                            : ($otherUser?->name ?? 'Private Chat');

                    $chatInitial = mb_substr(
                        $chatName,
                        0,
                        1
                    );

                @endphp

                {{-- HEADER --}}
                <div class="chat-main-header">

                    <button
                        type="button"
                        class="mobile-back"
                        onclick="mobileBack()"
                    >
                        ←
                    </button>

                    <div class="chat-avatar {{ $conversation->type === 'team' ? 'team' : '' }}">
                        {{ $chatInitial }}
                    </div>

                    <div>
                        <div class="chat-main-name">
                            {{ $chatName }}
                        </div>

                        <div class="chat-main-subtitle">

                            @if($conversation->type === 'team')

                                {{ $conversation->users->count() }}
                                members

                            @else

                                Personal Chat

                            @endif

                        </div>
                    </div>

                </div>

                {{-- MESSAGES --}}
                <div
                    class="messages-container"
                    id="messagesContainer"
                >

                    @foreach($messages as $message)

                        @include(
                            'chat.partials.message',
                            ['message' => $message]
                        )

                    @endforeach

                </div>

                {{-- INPUT --}}
                <div class="chat-input-area">

                    <div
                        class="reply-preview"
                        id="replyPreview"
                    >
                        <button
                            type="button"
                            class="reply-preview-close"
                            onclick="cancelReply()"
                        >
                            ×
                        </button>

                        <strong id="replyName"></strong>

                        <div id="replyMessage"></div>
                    </div>

                    <form
                        action="{{ route('chat.send', $conversation->id) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="chat-form"
                        id="chatForm"
                    >

                        @csrf

                        <input
                            type="hidden"
                            name="reply_to_id"
                            id="replyToId"
                        >

                        <input
                            type="file"
                            name="attachment"
                            id="attachmentInput"
                            style="display:none;"
                        >

                        <button
                            type="button"
                            class="attachment-button"
                            onclick="document.getElementById('attachmentInput').click()"
                            title="Attachment"
                        >
                            📎
                        </button>

                        <textarea
                            name="message"
                            id="messageInput"
                            class="chat-message-input"
                            placeholder="Type a message..."
                            rows="1"
                        ></textarea>

                        <button
                            type="submit"
                            class="send-button"
                        >
                            ➤
                        </button>

                    </form>

                    <small
                        id="selectedFile"
                        class="text-muted"
                    ></small>

                </div>

            @else

                <div class="empty-chat">

                    <div>
                        <div class="empty-chat-icon">
                            💬
                        </div>

                        <h4>
                            Attendance Chat
                        </h4>

                        <p>
                            Select a conversation or start
                            a new chat.
                        </p>
                    </div>

                </div>

            @endif

        </div>

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
     * START NEW PRIVATE CHAT
     */
    const newChatUser =
        document.getElementById('newChatUser');

    if (newChatUser) {

        newChatUser.addEventListener(
            'change',
            function () {

                if (!this.value) {
                    return;
                }

                let url =
                    "{{ route('chat.start', ':user') }}";

                url = url.replace(
                    ':user',
                    this.value
                );

                window.location.href = url;
            }
        );
    }


    /*
     * AUTO RESIZE TEXTAREA
     */
    const messageInput =
        document.getElementById('messageInput');

    if (messageInput) {

        messageInput.addEventListener(
            'input',
            function () {

                this.style.height = 'auto';

                this.style.height =
                    Math.min(
                        this.scrollHeight,
                        120
                    ) + 'px';
            }
        );


        /*
         * ENTER = SEND
         * SHIFT + ENTER = NEW LINE
         */
        messageInput.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Enter' &&
                    !event.shiftKey
                ) {
                    event.preventDefault();

                    document
                        .getElementById('chatForm')
                        .requestSubmit();
                }
            }
        );
    }


    /*
     * FILE NAME
     */
    const attachmentInput =
        document.getElementById('attachmentInput');

    if (attachmentInput) {

        attachmentInput.addEventListener(
            'change',
            function () {

                const selectedFile =
                    document.getElementById(
                        'selectedFile'
                    );

                if (this.files.length) {

                    selectedFile.innerText =
                        'Selected: ' +
                        this.files[0].name;

                } else {

                    selectedFile.innerText = '';

                }
            }
        );
    }


    /*
     * SCROLL BOTTOM
     */
    scrollToBottom();

});


function scrollToBottom()
{
    const container =
        document.getElementById(
            'messagesContainer'
        );

    if (!container) {
        return;
    }

    container.scrollTop =
        container.scrollHeight;
}


function setReply(
    id,
    senderName,
    message
) {

    document
        .getElementById('replyToId')
        .value = id;

    document
        .getElementById('replyName')
        .innerText = senderName;

    document
        .getElementById('replyMessage')
        .innerText = message;

    document
        .getElementById('replyPreview')
        .style.display = 'block';

    document
        .getElementById('messageInput')
        .focus();
}


function cancelReply()
{
    document
        .getElementById('replyToId')
        .value = '';

    document
        .getElementById('replyPreview')
        .style.display = 'none';
}


function mobileBack()
{
    document
        .querySelector('.chat-wrapper')
        .classList.remove('chat-open');
}

</script>


@if(isset($conversation))

<script>

let lastMessageId =
    {{ $messages->last()?->id ?? 0 }};

let chatPolling = null;


/*
 * Message ko safe HTML me convert karne ke liye.
 */
function escapeHtml(value)
{
    if (value === null || value === undefined) {
        return '';
    }

    const div =
        document.createElement('div');

    div.textContent = value;

    return div.innerHTML;
}


/*
 * AJAX se aaye message ka HTML.
 */
function createMessageHtml(message)
{
    let rowClass =
        message.is_mine
            ? 'mine'
            : 'other';

    let senderHtml = '';

    if (!message.is_mine) {

        senderHtml =
            '<div class="message-sender">' +
                escapeHtml(message.sender_name) +
            '</div>';
    }


    let replyHtml = '';

    if (message.reply_to) {

        replyHtml =
            '<div class="reply-box">' +
                '<strong>' +
                    escapeHtml(
                        message.reply_to.sender_name
                    ) +
                '</strong>' +

                escapeHtml(
                    message.reply_to.message ?? ''
                ) +
            '</div>';
    }


    let attachmentHtml = '';

    if (message.attachment) {

        const type =
            message.attachment_type ?? '';

        if (type.startsWith('image/')) {

            attachmentHtml =
                '<div class="attachment-box">' +
                    '<a href="' +
                        escapeHtml(
                            message.attachment
                        ) +
                        '" target="_blank">' +

                        '<img src="' +
                            escapeHtml(
                                message.attachment
                            ) +
                            '" class="attachment-image">' +

                    '</a>' +
                '</div>';

        } else {

            attachmentHtml =
                '<div class="attachment-box">' +
                    '<a class="file-link" ' +
                    'target="_blank" href="' +
                        escapeHtml(
                            message.attachment
                        ) +
                    '">' +

                    '📄 ' +
                    escapeHtml(
                        message.attachment_name ??
                        'Attachment'
                    ) +

                    '</a>' +
                '</div>';
        }
    }


    let messageText =
        escapeHtml(
            message.message ?? ''
        );


    return `
        <div class="message-row ${rowClass}">

            <div class="message-bubble">

                ${senderHtml}

                ${replyHtml}

                ${
                    messageText
                        ? `<div class="message-text">${messageText}</div>`
                        : ''
                }

                ${attachmentHtml}

                <div class="message-time">
                    ${escapeHtml(message.created_at)}
                </div>

                <button
                    type="button"
                    class="reply-action"
                    data-id="${message.id}"
                    data-name="${escapeHtml(message.sender_name)}"
                    data-message="${escapeHtml(message.message ?? '')}"
                    onclick="replyFromButton(this)"
                >
                    Reply
                </button>

            </div>

        </div>
    `;
}


function replyFromButton(button)
{
    setReply(
        button.dataset.id,
        button.dataset.name,
        button.dataset.message
    );
}


/*
 * NEW MESSAGE CHECK
 */
async function checkNewMessages()
{
    try {

        const url =
            "{{ route('chat.messages', $conversation->id) }}"
            + "?last_message_id="
            + lastMessageId;

        const response =
            await fetch(
                url,
                {
                    headers: {
                        'X-Requested-With':
                            'XMLHttpRequest'
                    }
                }
            );

        if (!response.ok) {
            return;
        }

        const data =
            await response.json();

        if (
            !data.success ||
            !data.messages.length
        ) {
            return;
        }

        const container =
            document.getElementById(
                'messagesContainer'
            );

        data.messages.forEach(
            function (message) {

                /*
                 * Same message duplicate na ho.
                 */
                if (
                    document.querySelector(
                        '[data-message-id="' +
                        message.id +
                        '"]'
                    )
                ) {
                    return;
                }

                const holder =
                    document.createElement('div');

                holder.innerHTML =
                    createMessageHtml(message);

                const element =
                    holder.firstElementChild;

                element.setAttribute(
                    'data-message-id',
                    message.id
                );

                container.appendChild(
                    element
                );

                lastMessageId =
                    Math.max(
                        lastMessageId,
                        message.id
                    );
            }
        );

        scrollToBottom();

    } catch (error) {

        console.error(
            'Chat polling error:',
            error
        );
    }
}


/*
 * Har 3 seconds me new message check.
 */
chatPolling =
    setInterval(
        checkNewMessages,
        3000
    );

</script>

@endif

@endsection
