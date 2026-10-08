@extends('dashboard.layout.root')
@section('content')
<style>
    * {
        box-sizing: border-box;
    }
    .chat-wrapper {
        height: 100%;
        min-height: 0;
        max-height: none;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 15px rgba(0,0,0,.08);
        display: flex;
    }
    /* =========================================================
       SIDEBAR
    ========================================================= */
    .chat-sidebar {
        width: 340px;
        min-width: 340px;
        min-height: 0;
        overflow: hidden;
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
        min-width: 0;
        font-size: 13px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px;
        background: white;
    }
    .chat-toolbar {
        display: flex;
        gap: 6px;
        padding: 10px 12px;
        border-bottom: 1px solid #eee;
    }
    .chat-filter {
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 5px 10px;
        background: white;
        font-size: 12px;
        cursor: pointer;
    }
    .chat-filter.active {
        background: #2563eb;
        color: white;
        border-color: #2563eb;
    }
    .chat-list {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        overscroll-behavior: contain;
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
    /* =========================================================
       MAIN CHAT
    ========================================================= */
    .chat-main {
        flex: 1;
        min-width: 0;
        min-height: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: #f4f7fa;
    }
    .chat-main-header {
        height: 70px;
        min-height: 70px;
        flex-shrink: 0;
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
        overflow-wrap: anywhere;
    }
    .messages-container {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 22px;
        overscroll-behavior: contain;
    }
    /* =========================================================
       MESSAGE
    ========================================================= */
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
        margin-top: 4px;
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
    /* =========================================================
       REPLY
    ========================================================= */
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
    /* =========================================================
       ATTACHMENTS
    ========================================================= */
    .attachment-box {
        margin-top: 7px;
        max-width: 100%;
    }
    .attachment-image {
        width: auto;
        max-width: 280px;
        max-height: 320px;
        object-fit: cover;
        border-radius: 10px;
        display: block;
        cursor: pointer;
    }
    .attachment-name {
        margin-top: 4px;
        max-width: 270px;
        font-size: 10px;
        color: #6b7280;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .file-link {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 280px;
        max-width: 100%;
        padding: 10px;
        border-radius: 10px;
        background: rgba(255,255,255,.5);
        color: #111827;
        text-decoration: none !important;
    }
    .file-link:hover {
        background: rgba(0,0,0,.06);
        color: #111827;
    }
    .file-icon {
        width: 42px;
        height: 42px;
        min-width: 42px;
        border-radius: 9px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
    }
    .file-info {
        flex: 1;
        min-width: 0;
    }
    .file-name {
        font-size: 12px;
        font-weight: 600;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .file-type {
        font-size: 9px;
        color: #6b7280;
        margin-top: 2px;
    }
    /* =========================================================
       VOICE
    ========================================================= */
    .voice-note {
        display: flex;
        align-items: center;
        gap: 9px;
        width: 310px;
        max-width: 100%;
        padding: 7px;
        background: rgba(0,0,0,.04);
        border-radius: 12px;
    }
    .voice-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 50%;
        background: #2563eb;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .voice-content {
        flex: 1;
        min-width: 0;
    }
    .voice-title {
        font-size: 10px;
        color: #6b7280;
        margin-bottom: 2px;
    }
    .voice-player {
        width: 235px;
        max-width: 100%;
        height: 36px;
    }
    /* =========================================================
       VIDEO
    ========================================================= */
    .chat-video {
        width: 300px;
        max-width: 100%;
        max-height: 350px;
        border-radius: 10px;
        background: #000;
        display: block;
    }
    /* =========================================================
       INPUT
    ========================================================= */
    .chat-input-area {
        flex: 0 0 auto;
        background: #fff;
        padding: 10px 15px;
        border-top: 1px solid #e5e7eb;
        position: sticky;
        bottom: 0;
        z-index: 20;
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
    .voice-button,
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
        font-size: 18px;
    }
    .attachment-button {
        background: #f3f4f6;
        color: #374151;
    }
    .voice-button {
        background: #f3f4f6;
        color: #374151;
    }
    .voice-button.recording {
        background: #ef4444;
        color: white;
        animation: recordingPulse 1s infinite;
    }
    @keyframes recordingPulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.08);
        }
    }
    .send-button {
        background: #2563eb;
        color: white;
    }
    .send-button:disabled {
        opacity: .5;
        cursor: not-allowed;
    }
    /* =========================================================
       SELECTED FILE / RECORDING
    ========================================================= */
    .selected-file-box {
        display: none;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        padding: 8px 10px;
        border-radius: 8px;
        background: #f3f4f6;
    }
    .selected-file-box.show {
        display: flex;
    }
    .selected-file-name {
        flex: 1;
        min-width: 0;
        font-size: 12px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .remove-file-button {
        border: 0;
        background: transparent;
        color: #dc2626;
        font-size: 18px;
        cursor: pointer;
    }
    .recording-status {
        display: none;
        align-items: center;
        gap: 7px;
        margin-bottom: 8px;
        padding: 8px 10px;
        background: #fef2f2;
        color: #991b1b;
        border-radius: 8px;
        font-size: 12px;
    }
    .recording-status.show {
        display: flex;
    }
    .recording-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: #ef4444;
        animation: recordingPulse 1s infinite;
    }
    /* =========================================================
       REPLY PREVIEW
    ========================================================= */
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
    /* =========================================================
       MISC
    ========================================================= */
    .chat-page {
        height: 100%;
        min-height: 0;
        overflow: hidden;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
    html.chat-page-lock,
    body.chat-page-lock {
        overflow: hidden !important;
        overscroll-behavior: none;
    }
    .chat-status {
        margin: 10px 0;
        padding: 10px 14px;
        border-radius: 8px;
        background: #eff6ff;
        color: #1e40af;
    }
    .chat-status.error {
        background: #fef2f2;
        color: #991b1b;
    }
    .chat-read-only {
        flex: 0 0 auto;
        padding: 15px;
        color: #92400e;
        background: #fffbeb;
        border-top: 1px solid #fde68a;
    }
    .chat-team-message {
        width: 100%;
        padding: 8px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        margin: 8px 0;
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
    @media(max-width: 768px) {
        .chat-wrapper {
            height: 100%;
            min-height: 0;
            max-height: none;
            border-radius: 0;
        }
        .chat-sidebar {
            width: 100%;
            min-width: 100%;
        }
        .chat-main {
            display: none;
            width: 100%;
            height: 100%;
            min-height: 0;
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
            max-width: 88%;
        }
        .attachment-image {
            max-width: 230px;
        }
        .voice-note {
            width: 245px;
        }
        .voice-player {
            width: 175px;
        }
        .file-link {
            width: 230px;
        }
        .chat-video {
            width: 240px;
        }
        .chat-input-area {
            padding: 8px 8px calc(8px + env(safe-area-inset-bottom));
        }
        .chat-form {
            gap: 6px;
        }
        .chat-message-input {
            min-width: 0;
            min-height: 40px;
            max-height: 96px;
            padding: 9px 12px;
        }
        .attachment-button,
        .voice-button,
        .send-button {
            width: 40px;
            height: 40px;
            min-width: 40px;
        }
    }
</style>
@php
    $canSend = $canSend ?? false;
    $labels = [
        'super_admin' => 'Super Admin',
        'owner' => 'Owner',
        'admin' => 'Admin',
        'team_leader' => 'Team Leader',
        'employee' => 'Employee',
    ];
    $chatAccess = app(\App\Services\ChatAccessService::class);
@endphp
<div class="container-fluid chat-page" id="chatPage">
    @if(session('success'))
        <div class="chat-status">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="chat-status error" role="alert">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif
    <div class="chat-wrapper {{ isset($conversation) ? 'chat-open' : '' }}">
        {{-- =====================================================
             SIDEBAR
        ====================================================== --}}
        <aside class="chat-sidebar">
            <div class="chat-sidebar-header">
                <h4 class="chat-sidebar-title">
                    Messages
                </h4>
                <small class="text-muted">
                    {{ auth()->user()->name }}
                </small>
                @if($canCreateTeamChat ?? false)
                    <details class="mt-2">
                        <summary>
                            Message Entire Team
                        </summary>
                        <form
                            action="{{ route('chat.team.create') }}"
                            method="POST"
                        >
                            @csrf
                            <textarea
                                class="chat-team-message"
                                name="message"
                                maxlength="5000"
                                rows="3"
                                required
                                placeholder="Message for your reporting team..."
                            ></textarea>
                            <button
                                type="submit"
                                class="btn btn-primary btn-sm w-100"
                            >
                                Send privately to every member
                            </button>
                            <small class="text-muted">
                                Replies stay in each member's private chat.
                            </small>
                        </form>
                    </details>
                @endif
            </div>
            {{-- NEW CHAT --}}
            <div class="chat-new-users">
                <form
                    id="startChatForm"
                    method="POST"
                >
                    @csrf
                    <select
                        id="newChatUser"
                        aria-label="Start a new chat"
                    >
                        <option value="">
                            + Start New Chat
                        </option>
                        @foreach($allowedUsers as $chatUser)
                            <option
                                value="{{ route('chat.start', $chatUser->id) }}"
                            >
                                {{ $chatUser->name }}
                                —
                                {{ $labels[$chatAccess->role($chatUser)] ?? 'User' }}
                            </option>
                        @endforeach
                    </select>
                    @if($allowedUsers->isEmpty())
                        <small class="text-muted">
                            No eligible users in your hierarchy.
                        </small>
                    @endif
                </form>
            </div>
            {{-- FILTER --}}
            <div class="chat-toolbar">
                <button
                    type="button"
                    class="chat-filter active"
                    data-filter="all"
                >
                    All
                </button>
                <button
                    type="button"
                    class="chat-filter"
                    data-filter="mine"
                >
                    My Chats
                </button>
                <button
                    type="button"
                    class="chat-filter"
                    data-filter="monitor"
                >
                    Lower Users' Chats
                </button>
            </div>
            {{-- CONVERSATION LIST --}}
            <div class="chat-list">
                @forelse($conversations as $item)
                    @php
                        $monitor = (bool) $item->is_monitoring;
                        $names = $item->users
                            ->pluck('name')
                            ->implode(' ↔ ');
                        $other = $item->users->first(
                            fn ($u) =>
                                (int) $u->id !==
                                (int) auth()->id()
                        );
                        $title =
                            $item->type === 'team'
                                ? ($item->name ?: 'Legacy Team Chat')
                                : (
                                    $monitor
                                        ? ($names ?: 'Private Chat')
                                        : ($other?->name ?? 'Deleted User')
                                );
                        $latest = $item->latestMessage;
                        $latestPreview = 'No messages yet';
                        if ($latest) {
                            if ($latest->message) {
                                $latestPreview =
                                    \Illuminate\Support\Str::limit(
                                        $latest->message,
                                        35
                                    );
                            } elseif ($latest->attachment) {
                                $latestName =
                                    $latest->attachment_name
                                    ?: basename($latest->attachment);
                                $ext = strtolower(
                                    pathinfo(
                                        $latestName,
                                        PATHINFO_EXTENSION
                                    )
                                );
                                if (
                                    in_array(
                                        $ext,
                                        ['jpg','jpeg','png','gif','webp']
                                    )
                                ) {
                                    $latestPreview = '🖼️ Photo';
                                } elseif (
                                    in_array(
                                        $ext,
                                        ['mp3','wav','ogg','m4a','aac','webm']
                                    )
                                ) {
                                    $latestPreview = '🎤 Voice message';
                                } elseif (
                                    in_array(
                                        $ext,
                                        ['mp4','mov','mkv','avi']
                                    )
                                ) {
                                    $latestPreview = '🎥 Video';
                                } else {
                                    $latestPreview =
                                        '📎 ' .
                                        \Illuminate\Support\Str::limit(
                                            $latestName,
                                            28
                                        );
                                }
                            }
                        }
                    @endphp
                    <a
                        href="{{ route('chat.show', $item->id) }}"
                        class="chat-list-item {{
                            isset($conversation) &&
                            (int) $conversation->id ===
                            (int) $item->id
                                ? 'active'
                                : ''
                        }}"
                        data-chat-kind="{{ $monitor ? 'monitor' : 'mine' }}"
                    >
                        <div
                            class="chat-avatar {{
                                $item->type === 'team'
                                    ? 'team'
                                    : ''
                            }}"
                        >
                            {{ mb_substr($title, 0, 1) }}
                        </div>
                        <div class="chat-list-content">
                            <div class="chat-list-top">
                                <div
                                    class="chat-list-name"
                                    title="{{ $title }}"
                                >
                                    {{ $title }}
                                </div>
                                @if($latest)
                                    <div class="chat-list-time">
                                        {{ $latest->created_at->format('h:i A') }}
                                    </div>
                                @endif
                            </div>
                            <div class="chat-last-message">
                                <span>
                                    {{ $latestPreview }}
                                </span>
                                @if(
                                    !$monitor &&
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
        </aside>
        {{-- =====================================================
             CHAT
        ====================================================== --}}
        <main class="chat-main">
            @if(isset($conversation))
                @php
                    $other =
                        $conversation->users->first(
                            fn ($u) =>
                                (int) $u->id !==
                                (int) auth()->id()
                        );
                    $chatName =
                        $conversation->type === 'team'
                            ? (
                                $conversation->name
                                ?: 'Legacy Team Chat'
                            )
                            : (
                                ($isMonitoring ?? false)
                                    ? $conversation->users
                                        ->pluck('name')
                                        ->implode(' ↔ ')
                                    : (
                                        $other?->name
                                        ?? 'Deleted User'
                                    )
                            );
                @endphp
                {{-- HEADER --}}
                <div class="chat-main-header">
                    <a
                        class="mobile-back"
                        href="{{ route('chat.index') }}"
                    >
                        ←
                    </a>
                    <div class="chat-avatar">
                        {{ mb_substr($chatName ?: 'Chat', 0, 1) }}
                    </div>
                    <div>
                        <div class="chat-main-name">
                            {{ $chatName ?: 'Private Chat' }}
                        </div>
                        <div class="chat-main-subtitle">
                            {{
                                ($isMonitoring ?? false)
                                    ? 'Lower user conversation · Monitoring'
                                    : 'Your conversation'
                            }}
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
                            [
                                'message' => $message,
                                'canSend' => $canSend
                            ]
                        )
                    @endforeach
                </div>
                <div
                    class="chat-read-only"
                    id="readOnlyNotice"
                    @if($canSend) hidden @endif
                >
                    This conversation is read-only.
                </div>
                {{-- =====================================================
                     MESSAGE COMPOSER
                ====================================================== --}}
                @if($canSend)
                    <div
                        class="chat-input-area"
                        id="chatInputArea"
                    >
                        {{-- REPLY --}}
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
                        {{-- RECORDING STATUS --}}
                        <div
                            class="recording-status"
                            id="recordingStatus"
                        >
                            <span class="recording-dot"></span>
                            <span>
                                Recording...
                            </span>
                            <strong id="recordingTimer">
                                00:00
                            </strong>
                            <span>
                                — press stop when finished
                            </span>
                        </div>
                        {{-- SELECTED FILE --}}
                        <div
                            class="selected-file-box"
                            id="selectedFileBox"
                        >
                            <span>
                                📎
                            </span>
                            <div
                                class="selected-file-name"
                                id="selectedFile"
                            ></div>
                            <button
                                type="button"
                                class="remove-file-button"
                                onclick="removeSelectedFile()"
                            >
                                ×
                            </button>
                        </div>
                        {{-- FORM --}}
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
                                value="{{ old('reply_to_id') }}"
                            >
                            <input
                                type="file"
                                name="attachment"
                                id="attachmentInput"
                                style="display:none"
                            >
                            {{-- ATTACHMENT --}}
                            <button
                                type="button"
                                class="attachment-button"
                                id="attachmentButton"
                                title="Attach file"
                            >
                                📎
                            </button>
                            {{-- VOICE --}}
                            <button
                                type="button"
                                class="voice-button"
                                id="voiceButton"
                                title="Record voice note"
                            >
                                🎤
                            </button>
                            {{-- TEXT --}}
                            <textarea
                                name="message"
                                id="messageInput"
                                class="chat-message-input"
                                placeholder="Type a message..."
                                maxlength="5000"
                                rows="1"
                            >{{ old('message') }}</textarea>
                            {{-- SEND --}}
                            <button
                                type="submit"
                                class="send-button"
                                title="Send message"
                            >
                                ➤
                            </button>
                        </form>
                    </div>
                @endif
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
                            Select a conversation or start a new chat.
                        </p>
                    </div>
                </div>
            @endif
        </main>
    </div>
</div>
<script>
    window.chatCanSend = @json($canSend);
    /*
    |--------------------------------------------------------------------------
    | Keep complete chat inside the visible viewport
    |--------------------------------------------------------------------------
    */
    function getBottomOverlayHeight() {
        let height = 0;
        const page = document.getElementById('chatPage');
        document.querySelectorAll('body *').forEach(el => {
            if (page?.contains(el)) return;
            const style = getComputedStyle(el);
            if (style.position !== 'fixed' && style.position !== 'sticky') return;
            const rect = el.getBoundingClientRect();
            if (rect.height < 30 || rect.height > 120) return;
            if (rect.bottom >= window.innerHeight - 4 && rect.top < window.innerHeight) {
                height = Math.max(height, window.innerHeight - rect.top);
            }
        });
        if (window.innerWidth <= 768) height = Math.max(height, 58);
        return height;
    }
    function fitChatToViewport() {
        const page = document.getElementById('chatPage');
        if (!page) return;
        const viewport = window.visualViewport;
        const viewportHeight = viewport ? viewport.height : window.innerHeight;
        const rect = page.getBoundingClientRect();
        const top = Math.max(rect.top, 0);
        const bottomOverlay = getBottomOverlayHeight();
        const height = Math.max(240, viewportHeight - top - bottomOverlay);
        page.style.height = height + 'px';
        page.style.maxHeight = height + 'px';
    }
    document.documentElement.classList.add('chat-page-lock');
    document.body.classList.add('chat-page-lock');
    fitChatToViewport();
    window.addEventListener('resize', fitChatToViewport);
    window.visualViewport?.addEventListener('resize', fitChatToViewport);
    window.visualViewport?.addEventListener('scroll', fitChatToViewport);
    let mediaRecorder = null;
    let recordingStream = null;
    let recordedChunks = [];
    let recordingTimerInterval = null;
    let recordingSeconds = 0;
    function scrollToBottom() {
        const box =
            document.getElementById(
                'messagesContainer'
            );
        if (box) {
            box.scrollTop =
                box.scrollHeight;
        }
    }
    /* =========================================================
       REPLY
    ========================================================= */
    function setReply(
        id,
        sender,
        text
    ) {
        if (!window.chatCanSend) {
            return;
        }
        const input =
            document.getElementById(
                'replyToId'
            );
        if (!input) {
            return;
        }
        input.value = id;
        document.getElementById(
            'replyName'
        ).textContent =
            sender || 'User';
        document.getElementById(
            'replyMessage'
        ).textContent =
            text || 'Message';
        document.getElementById(
            'replyPreview'
        ).style.display =
            'block';
        document.getElementById(
            'messageInput'
        )?.focus();
    }
    function replyFromButton(button) {
        setReply(
            button.dataset.id,
            button.dataset.name,
            button.dataset.message
        );
    }
    function cancelReply() {
        const input =
            document.getElementById(
                'replyToId'
            );
        if (input) {
            input.value = '';
        }
        const preview =
            document.getElementById(
                'replyPreview'
            );
        if (preview) {
            preview.style.display =
                'none';
        }
    }
    /* =========================================================
       FILE
    ========================================================= */
    function showSelectedFile(file) {
        const box =
            document.getElementById(
                'selectedFileBox'
            );
        const name =
            document.getElementById(
                'selectedFile'
            );
        if (!box || !name) {
            return;
        }
        if (!file) {
            box.classList.remove(
                'show'
            );
            name.textContent = '';
            return;
        }
        let size =
            file.size / 1024;
        let sizeText;
        if (size >= 1024) {
            sizeText =
                (
                    size / 1024
                ).toFixed(2) +
                ' MB';
        } else {
            sizeText =
                size.toFixed(0) +
                ' KB';
        }
        name.textContent =
            file.name +
            ' (' +
            sizeText +
            ')';
        box.classList.add(
            'show'
        );
    }
    function removeSelectedFile() {
        const input =
            document.getElementById(
                'attachmentInput'
            );
        if (input) {
            input.value = '';
        }
        showSelectedFile(null);
    }
    /* =========================================================
       RECORDING TIMER
    ========================================================= */
    function formatRecordingTime(
        seconds
    ) {
        const minutes =
            Math.floor(
                seconds / 60
            );
        const remaining =
            seconds % 60;
        return String(
            minutes
        ).padStart(2, '0') +
            ':' +
            String(
                remaining
            ).padStart(2, '0');
    }
    function startRecordingTimer() {
        recordingSeconds = 0;
        document.getElementById(
            'recordingTimer'
        ).textContent =
            '00:00';
        recordingTimerInterval =
            setInterval(
                () => {
                    recordingSeconds++;
                    document.getElementById(
                        'recordingTimer'
                    ).textContent =
                        formatRecordingTime(
                            recordingSeconds
                        );
                },
                1000
            );
    }
    function stopRecordingTimer() {
        if (
            recordingTimerInterval
        ) {
            clearInterval(
                recordingTimerInterval
            );
            recordingTimerInterval =
                null;
        }
    }
    /* =========================================================
       VOICE RECORD
    ========================================================= */
    async function toggleVoiceRecording() {
        const button =
            document.getElementById(
                'voiceButton'
            );
        const status =
            document.getElementById(
                'recordingStatus'
            );
        /*
        |--------------------------------------------------------------------------
        | Stop
        |--------------------------------------------------------------------------
        */
        if (
            mediaRecorder &&
            mediaRecorder.state ===
                'recording'
        ) {
            mediaRecorder.stop();
            button.classList.remove(
                'recording'
            );
            button.textContent =
                '🎤';
            status.classList.remove(
                'show'
            );
            stopRecordingTimer();
            return;
        }
        /*
        |--------------------------------------------------------------------------
        | Browser Support
        |--------------------------------------------------------------------------
        */
        if (
            !navigator.mediaDevices ||
            !navigator.mediaDevices
                .getUserMedia ||
            typeof MediaRecorder ===
                'undefined'
        ) {
            alert(
                'Voice recording is not supported in this browser.'
            );
            return;
        }
        try {
            /*
            |--------------------------------------------------------------------------
            | Microphone Permission
            |--------------------------------------------------------------------------
            */
            recordingStream =
                await navigator
                    .mediaDevices
                    .getUserMedia({
                        audio: true
                    });
            recordedChunks = [];
            /*
            |--------------------------------------------------------------------------
            | Choose supported format
            |--------------------------------------------------------------------------
            */
            let mimeType = '';
            const formats = [
                'audio/webm;codecs=opus',
                'audio/webm',
                'audio/ogg;codecs=opus',
                'audio/mp4'
            ];
            for (
                const format
                of formats
            ) {
                if (
                    MediaRecorder
                        .isTypeSupported(
                            format
                        )
                ) {
                    mimeType =
                        format;
                    break;
                }
            }
            const options =
                mimeType
                    ? { mimeType }
                    : undefined;
            mediaRecorder =
                new MediaRecorder(
                    recordingStream,
                    options
                );
            /*
            |--------------------------------------------------------------------------
            | Data
            |--------------------------------------------------------------------------
            */
            mediaRecorder.addEventListener(
                'dataavailable',
                event => {
                    if (
                        event.data &&
                        event.data.size > 0
                    ) {
                        recordedChunks.push(
                            event.data
                        );
                    }
                }
            );
            /*
            |--------------------------------------------------------------------------
            | Recording Finished
            |--------------------------------------------------------------------------
            */
            mediaRecorder.addEventListener(
                'stop',
                () => {
                    const finalMime =
                        mediaRecorder.mimeType ||
                        mimeType ||
                        'audio/webm';
                    const blob =
                        new Blob(
                            recordedChunks,
                            {
                                type: finalMime
                            }
                        );
                    let extension =
                        'webm';
                    if (
                        finalMime.includes(
                            'ogg'
                        )
                    ) {
                        extension =
                            'ogg';
                    } else if (
                        finalMime.includes(
                            'mp4'
                        )
                    ) {
                        extension =
                            'm4a';
                    }
                    const file =
                        new File(
                            [blob],
                            'voice-note-' +
                                Date.now() +
                                '.' +
                                extension,
                            {
                                type:
                                    finalMime
                            }
                        );
                    /*
                    |--------------------------------------------------------------------------
                    | Put recording into file input
                    |--------------------------------------------------------------------------
                    */
                    const transfer =
                        new DataTransfer();
                    transfer.items.add(
                        file
                    );
                    const attachment =
                        document.getElementById(
                            'attachmentInput'
                        );
                    attachment.files =
                        transfer.files;
                    showSelectedFile(
                        file
                    );
                    /*
                    |--------------------------------------------------------------------------
                    | Stop microphone
                    |--------------------------------------------------------------------------
                    */
                    if (
                        recordingStream
                    ) {
                        recordingStream
                            .getTracks()
                            .forEach(
                                track =>
                                    track.stop()
                            );
                    }
                    recordingStream =
                        null;
                    recordedChunks =
                        [];
                    mediaRecorder =
                        null;
                }
            );
            /*
            |--------------------------------------------------------------------------
            | Start
            |--------------------------------------------------------------------------
            */
            mediaRecorder.start(
                250
            );
            button.classList.add(
                'recording'
            );
            button.textContent =
                '⏹';
            status.classList.add(
                'show'
            );
            startRecordingTimer();
        } catch (error) {
            console.error(
                'Microphone error:',
                error
            );
            alert(
                'Microphone permission allow karein. HTTPS ya localhost par voice recording use karein.'
            );
        }
    }
    /* =========================================================
       DOM
    ========================================================= */
    document.addEventListener(
        'DOMContentLoaded',
        () => {
            /*
            |--------------------------------------------------------------------------
            | Start Chat
            |--------------------------------------------------------------------------
            */
            document.getElementById(
                'newChatUser'
            )?.addEventListener(
                'change',
                function () {
                    if (!this.value) {
                        return;
                    }
                    const form =
                        document.getElementById(
                            'startChatForm'
                        );
                    form.action =
                        this.value;
                    form.requestSubmit();
                }
            );
            /*
            |--------------------------------------------------------------------------
            | Filters
            |--------------------------------------------------------------------------
            */
            document
                .querySelectorAll(
                    '.chat-filter'
                )
                .forEach(
                    button => {
                        button.addEventListener(
                            'click',
                            () => {
                                document
                                    .querySelectorAll(
                                        '.chat-filter'
                                    )
                                    .forEach(
                                        b =>
                                            b.classList
                                                .toggle(
                                                    'active',
                                                    b === button
                                                )
                                    );
                                document
                                    .querySelectorAll(
                                        '[data-chat-kind]'
                                    )
                                    .forEach(
                                        item => {
                                            item.style.display =
                                                button.dataset.filter === 'all' ||
                                                item.dataset.chatKind ===
                                                    button.dataset.filter
                                                    ? ''
                                                    : 'none';
                                        }
                                    );
                            }
                        );
                    }
                );
            /*
            |--------------------------------------------------------------------------
            | Textarea
            |--------------------------------------------------------------------------
            */
            const input =
                document.getElementById(
                    'messageInput'
                );
            input?.addEventListener(
                'input',
                () => {
                    input.style.height =
                        'auto';
                    input.style.height =
                        Math.min(
                            input.scrollHeight,
                            120
                        ) +
                        'px';
                }
            );
            input?.addEventListener(
                'keydown',
                event => {
                    if (
                        event.key ===
                            'Enter' &&
                        !event.shiftKey &&
                        !event.isComposing
                    ) {
                        event.preventDefault();
                        document
                            .getElementById(
                                'chatForm'
                            )
                            ?.requestSubmit();
                    }
                }
            );
            /*
            |--------------------------------------------------------------------------
            | Attachment Button
            |--------------------------------------------------------------------------
            */
            document.getElementById(
                'attachmentButton'
            )?.addEventListener(
                'click',
                () => {
                    document.getElementById(
                        'attachmentInput'
                    )?.click();
                }
            );
            /*
            |--------------------------------------------------------------------------
            | File Selected
            |--------------------------------------------------------------------------
            */
            document.getElementById(
                'attachmentInput'
            )?.addEventListener(
                'change',
                function () {
                    const file =
                        this.files?.[0];
                    showSelectedFile(
                        file || null
                    );
                }
            );
            /*
            |--------------------------------------------------------------------------
            | Voice Button
            |--------------------------------------------------------------------------
            */
            document.getElementById(
                'voiceButton'
            )?.addEventListener(
                'click',
                toggleVoiceRecording
            );
            /*
            |--------------------------------------------------------------------------
            | Submit
            |--------------------------------------------------------------------------
            */
            document.getElementById(
                'chatForm'
            )?.addEventListener(
                'submit',
                event => {
                    const messageInput =
                        document.getElementById(
                            'messageInput'
                        );
                    const fileInput =
                        document.getElementById(
                            'attachmentInput'
                        );
                    /*
                    |--------------------------------------------------------------------------
                    | Stop empty message
                    |--------------------------------------------------------------------------
                    */
                    if (
                        !window.chatCanSend ||
                        (
                            !messageInput.value.trim() &&
                            !fileInput.files.length
                        )
                    ) {
                        event.preventDefault();
                        return;
                    }
                    /*
                    |--------------------------------------------------------------------------
                    | 50 MB frontend limit
                    |--------------------------------------------------------------------------
                    */
                    const file =
                        fileInput.files?.[0];
                    if (
                        file &&
                        file.size >
                            50 *
                            1024 *
                            1024
                    ) {
                        event.preventDefault();
                        alert(
                            'Maximum file size 50 MB hai.'
                        );
                        return;
                    }
                    /*
                    |--------------------------------------------------------------------------
                    | Recording still running
                    |--------------------------------------------------------------------------
                    */
                    if (
                        mediaRecorder &&
                        mediaRecorder.state ===
                            'recording'
                    ) {
                        event.preventDefault();
                        alert(
                            'Pehle voice recording stop karein, phir send karein.'
                        );
                        return;
                    }
                    const sendButton =
                        event.currentTarget
                            .querySelector(
                                '.send-button'
                            );
                    if (sendButton) {
                        sendButton.disabled =
                            true;
                    }
                }
            );
            fitChatToViewport();
            setTimeout(fitChatToViewport, 150);
            setTimeout(fitChatToViewport, 500);
            new MutationObserver(fitChatToViewport).observe(document.body, {childList:true, subtree:true});
            scrollToBottom();
            input?.addEventListener('focus', () => {
                setTimeout(() => {
                    fitChatToViewport();
                    scrollToBottom();
                }, 250);
            });
        }
    );
</script>
{{-- =============================================================
     LIVE POLLING
============================================================= --}}
@if(isset($conversation))
<script>
(() => {
    let lastMessageId =
        @json($messages->last()?->id ?? 0);
    let stopped = false;
    let timer = null;
    const baseUrl =
        @json(route('chat.messages', $conversation->id));
    const box =
        document.getElementById(
            'messagesContainer'
        );
    function node(
        tag,
        className,
        text
    ) {
        const element =
            document.createElement(
                tag
            );
        if (className) {
            element.className =
                className;
        }
        if (
            text !== undefined &&
            text !== null
        ) {
            element.textContent =
                text;
        }
        return element;
    }
    function getExtension(
        fileName
    ) {
        if (!fileName) {
            return '';
        }
        const parts =
            fileName
                .toLowerCase()
                .split('.');
        return parts.length > 1
            ? parts.pop()
            : '';
    }
    function messageNode(
        message
    ) {
        const row =
            node(
                'div',
                'message-row ' +
                    (
                        message.is_mine
                            ? 'mine'
                            : 'other'
                    )
            );
        row.dataset.messageId =
            message.id;
        const bubble =
            node(
                'div',
                'message-bubble'
            );
        row.appendChild(
            bubble
        );
        /*
        |--------------------------------------------------------------------------
        | Sender
        |--------------------------------------------------------------------------
        */
        if (!message.is_mine) {
            bubble.appendChild(
                node(
                    'div',
                    'message-sender',
                    message.sender_name ||
                        'Deleted User'
                )
            );
        }
        /*
        |--------------------------------------------------------------------------
        | Reply
        |--------------------------------------------------------------------------
        */
        if (message.reply_to) {
            const reply =
                node(
                    'div',
                    'reply-box'
                );
            reply.appendChild(
                node(
                    'strong',
                    '',
                    message.reply_to
                        .sender_name ||
                        'Deleted User'
                )
            );
            reply.appendChild(
                node(
                    'span',
                    '',
                    message.reply_to
                        .message ||
                    message.reply_to
                        .attachment_name ||
                    'Attachment'
                )
            );
            bubble.appendChild(
                reply
            );
        }
        /*
        |--------------------------------------------------------------------------
        | Message
        |--------------------------------------------------------------------------
        */
        if (message.message) {
            bubble.appendChild(
                node(
                    'div',
                    'message-text',
                    message.message
                )
            );
        }
        /*
        |--------------------------------------------------------------------------
        | Attachment
        |--------------------------------------------------------------------------
        */
        if (message.attachment) {
            const attachment =
                node(
                    'div',
                    'attachment-box'
                );
            try {
                const url =
                    new URL(
                        message.attachment,
                        window.location.href
                    );
                if (
                    url.origin ===
                    window.location.origin
                ) {
                    const fileName =
                        message.attachment_name ||
                        'Attachment';
                    const extension =
                        getExtension(
                            fileName
                        );
                    const images = [
                        'jpg',
                        'jpeg',
                        'png',
                        'gif',
                        'webp',
                        'bmp',
                        'svg'
                    ];
                    const audio = [
                        'mp3',
                        'wav',
                        'ogg',
                        'm4a',
                        'aac',
                        'webm',
                        'opus'
                    ];
                    const videos = [
                        'mp4',
                        'mov',
                        'mkv',
                        'avi',
                        'mpeg',
                        'mpg',
                        '3gp'
                    ];
                    /*
                    |--------------------------------------------------------------------------
                    | Image
                    |--------------------------------------------------------------------------
                    */
                    if (
                        images.includes(
                            extension
                        )
                    ) {
                        const link =
                            document.createElement(
                                'a'
                            );
                        link.href =
                            url.href;
                        link.target =
                            '_blank';
                        link.rel =
                            'noopener noreferrer';
                        const image =
                            document.createElement(
                                'img'
                            );
                        image.src =
                            url.href;
                        image.alt =
                            fileName;
                        image.loading =
                            'lazy';
                        image.className =
                            'attachment-image';
                        link.appendChild(
                            image
                        );
                        attachment.appendChild(
                            link
                        );
                        attachment.appendChild(
                            node(
                                'div',
                                'attachment-name',
                                fileName
                            )
                        );
                    }
                    /*
                    |--------------------------------------------------------------------------
                    | Voice / Audio
                    |--------------------------------------------------------------------------
                    */
                    else if (
                        audio.includes(
                            extension
                        )
                    ) {
                        const voice =
                            node(
                                'div',
                                'voice-note'
                            );
                        voice.appendChild(
                            node(
                                'div',
                                'voice-icon',
                                '🎤'
                            )
                        );
                        const content =
                            node(
                                'div',
                                'voice-content'
                            );
                        content.appendChild(
                            node(
                                'div',
                                'voice-title',
                                'Voice message'
                            )
                        );
                        const player =
                            document.createElement(
                                'audio'
                            );
                        player.controls =
                            true;
                        player.preload =
                            'metadata';
                        player.className =
                            'voice-player';
                        player.src =
                            url.href;
                        content.appendChild(
                            player
                        );
                        voice.appendChild(
                            content
                        );
                        attachment.appendChild(
                            voice
                        );
                    }
                    /*
                    |--------------------------------------------------------------------------
                    | Video
                    |--------------------------------------------------------------------------
                    */
                    else if (
                        videos.includes(
                            extension
                        )
                    ) {
                        const video =
                            document.createElement(
                                'video'
                            );
                        video.controls =
                            true;
                        video.preload =
                            'metadata';
                        video.className =
                            'chat-video';
                        video.src =
                            url.href;
                        attachment.appendChild(
                            video
                        );
                        attachment.appendChild(
                            node(
                                'div',
                                'attachment-name',
                                fileName
                            )
                        );
                    }
                    /*
                    |--------------------------------------------------------------------------
                    | Other File
                    |--------------------------------------------------------------------------
                    */
                    else {
                        const link =
                            document.createElement(
                                'a'
                            );
                        link.href =
                            url.href;
                        link.target =
                            '_blank';
                        link.rel =
                            'noopener noreferrer';
                        link.className =
                            'file-link';
                        const icon =
                            node(
                                'div',
                                'file-icon',
                                extension === 'pdf'
                                    ? '📄'
                                    : '📎'
                            );
                        const info =
                            node(
                                'div',
                                'file-info'
                            );
                        info.appendChild(
                            node(
                                'div',
                                'file-name',
                                fileName
                            )
                        );
                        info.appendChild(
                            node(
                                'div',
                                'file-type',
                                extension
                                    ? extension
                                        .toUpperCase()
                                    : 'FILE'
                            )
                        );
                        link.appendChild(
                            icon
                        );
                        link.appendChild(
                            info
                        );
                        attachment.appendChild(
                            link
                        );
                    }
                    bubble.appendChild(
                        attachment
                    );
                }
            } catch (error) {
                console.error(
                    'Attachment error:',
                    error
                );
            }
        }
        /*
        |--------------------------------------------------------------------------
        | Time
        |--------------------------------------------------------------------------
        */
        bubble.appendChild(
            node(
                'div',
                'message-time',
                message.created_at
            )
        );
        /*
        |--------------------------------------------------------------------------
        | Reply Button
        |--------------------------------------------------------------------------
        */
        if (window.chatCanSend) {
            const button =
                node(
                    'button',
                    'reply-action',
                    'Reply'
                );
            button.type =
                'button';
            let preview =
                message.message;
            if (!preview) {
                const extension =
                    getExtension(
                        message.attachment_name
                    );
                if (
                    [
                        'mp3',
                        'wav',
                        'ogg',
                        'm4a',
                        'aac',
                        'webm',
                        'opus'
                    ].includes(extension)
                ) {
                    preview =
                        '🎤 Voice message';
                } else {
                    preview =
                        '📎 ' +
                        (
                            message.attachment_name ||
                            'Attachment'
                        );
                }
            }
            button.addEventListener(
                'click',
                () => {
                    setReply(
                        message.id,
                        message.sender_name ||
                            'Deleted User',
                        preview
                    );
                }
            );
            bubble.appendChild(
                button
            );
        }
        return row;
    }
    async function poll() {
        if (stopped) {
            return;
        }
        try {
            if (document.hidden) {
                return;
            }
            const response =
                await fetch(
                    baseUrl +
                    '?last_message_id=' +
                    lastMessageId,
                    {
                        credentials:
                            'same-origin',
                        headers: {
                            'Accept':
                                'application/json',
                            'X-Requested-With':
                                'XMLHttpRequest'
                        }
                    }
                );
            if (
                [401,403,404]
                    .includes(
                        response.status
                    )
            ) {
                stopped = true;
                window.chatCanSend =
                    false;
                document
                    .getElementById(
                        'chatInputArea'
                    )
                    ?.setAttribute(
                        'hidden',
                        ''
                    );
                const notice =
                    document.getElementById(
                        'readOnlyNotice'
                    );
                if (notice) {
                    notice.hidden =
                        false;
                    notice.textContent =
                        'Chat access changed. Reload the page.';
                }
                return;
            }
            if (!response.ok) {
                return;
            }
            const data =
                await response.json();
            if (!data.success) {
                return;
            }
            window.chatCanSend =
                !!data.can_send;
            if (
                !window.chatCanSend
            ) {
                document
                    .getElementById(
                        'chatInputArea'
                    )
                    ?.setAttribute(
                        'hidden',
                        ''
                    );
                const notice =
                    document.getElementById(
                        'readOnlyNotice'
                    );
                if (notice) {
                    notice.hidden =
                        false;
                }
                document
                    .querySelectorAll(
                        '.reply-action'
                    )
                    .forEach(
                        button =>
                            button.remove()
                    );
            }
            const nearBottom =
                box.scrollHeight -
                box.scrollTop -
                box.clientHeight <
                100;
            for (
                const message
                of data.messages
            ) {
                const id =
                    Number(
                        message.id
                    );
                if (
                    !Number.isSafeInteger(
                        id
                    )
                ) {
                    continue;
                }
                if (
                    !box.querySelector(
                        '[data-message-id="' +
                        id +
                        '"]'
                    )
                ) {
                    box.appendChild(
                        messageNode(
                            message
                        )
                    );
                }
                lastMessageId =
                    Math.max(
                        lastMessageId,
                        id
                    );
            }
            if (nearBottom) {
                scrollToBottom();
            }
        } catch (error) {
            console.error(
                'Chat polling failed',
                error
            );
        } finally {
            if (!stopped) {
                timer =
                    setTimeout(
                        poll,
                        3000
                    );
            }
        }
    }
    timer =
        setTimeout(
            poll,
            3000
        );
    window.addEventListener(
        'pagehide',
        () => {
            stopped =
                true;
            clearTimeout(
                timer
            );
            document.documentElement.classList.remove('chat-page-lock');
            document.body.classList.remove('chat-page-lock');
            window.visualViewport?.removeEventListener('resize', fitChatToViewport);
            window.visualViewport?.removeEventListener('scroll', fitChatToViewport);
            if (
                recordingStream
            ) {
                recordingStream
                    .getTracks()
                    .forEach(
                        track =>
                            track.stop()
                    );
            }
        }
    );
})();
</script>
@endif
@include('chat.partials.message-info')
@endsection
