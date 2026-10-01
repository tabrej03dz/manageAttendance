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
<style>
.chat-status { margin: 10px 0; padding: 10px 14px; border-radius: 8px; background: #eff6ff; color: #1e40af; }
.chat-status.error { background: #fef2f2; color: #991b1b; }
.chat-read-only { padding: 15px; color: #92400e; background: #fffbeb; border-top: 1px solid #fde68a; }
.chat-monitor-label { font-size: 10px; color: #92400e; }
.chat-toolbar { display:flex; gap:6px; padding:10px 12px; border-bottom:1px solid #eee; }
.chat-filter { border:1px solid #d1d5db; border-radius:6px; padding:5px 10px; background:white; font-size:12px; cursor:pointer; }
.chat-filter.active { background:#2563eb; color:white; border-color:#2563eb; }
.chat-team-message { width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px; margin:8px 0; }
.chat-new-users select { min-width:0; font-size:13px; }
.chat-main-subtitle { overflow-wrap:anywhere; }
</style>
@php
    $canSend = $canSend ?? false;
    $labels = ['super_admin' => 'Super Admin', 'owner' => 'Owner', 'admin' => 'Admin', 'team_leader' => 'Team Leader', 'employee' => 'Employee'];
    $chatAccess = app(\App\Services\ChatAccessService::class);
@endphp
<div class="container-fluid py-3">
    @if(session('success'))
        <div class="chat-status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="chat-status error" role="alert">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif
    <div class="chat-wrapper {{ isset($conversation) ? 'chat-open' : '' }}">
        <aside class="chat-sidebar">
            <div class="chat-sidebar-header">
                <h4 class="chat-sidebar-title">Messages</h4>
                <small class="text-muted">{{ auth()->user()->name }}</small>
                @if($canCreateTeamChat ?? false)
                    <details class="mt-2">
                        <summary>Message Entire Team</summary>
                        <form action="{{ route('chat.team.create') }}" method="POST">
                            @csrf
                            <textarea class="chat-team-message" name="message" maxlength="5000" rows="3" required placeholder="Message for your reporting team..."></textarea>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Send privately to every member</button>
                            <small class="text-muted">Replies stay in each member's private chat.</small>
                        </form>
                    </details>
                @endif
            </div>
            <div class="chat-new-users">
                <form id="startChatForm" method="POST">
                    @csrf
                    <select id="newChatUser" aria-label="Start a new chat">
                        <option value="">+ Start New Chat</option>
                        @foreach($allowedUsers as $chatUser)
                            <option value="{{ route('chat.start', $chatUser->id) }}">
                                {{ $chatUser->name }} — {{ $labels[$chatAccess->role($chatUser)] ?? 'User' }}
                            </option>
                        @endforeach
                    </select>
                    @if($allowedUsers->isEmpty())<small class="text-muted">No eligible users in your hierarchy.</small>@endif
                </form>
            </div>
            <div class="chat-toolbar">
                <button type="button" class="chat-filter active" data-filter="all">All</button>
                <button type="button" class="chat-filter" data-filter="mine">My Chats</button>
                <button type="button" class="chat-filter" data-filter="monitor">Lower Users' Chats</button>
            </div>
            <div class="chat-list">
                @forelse($conversations as $item)
                    @php
                        $monitor = (bool) $item->is_monitoring;
                        $names = $item->users->pluck('name')->implode(' ↔ ');
                        $other = $item->users->first(fn ($u) => (int) $u->id !== (int) auth()->id());
                        $title = $item->type === 'team'
                            ? ($item->name ?: 'Legacy Team Chat')
                            : ($monitor ? ($names ?: 'Private Chat') : ($other?->name ?? 'Deleted User'));
                        $latest = $item->latestMessage;
                    @endphp
                    <a href="{{ route('chat.show', $item->id) }}"
                        class="chat-list-item {{ isset($conversation) && (int) $conversation->id === (int) $item->id ? 'active' : '' }}"
                        data-chat-kind="{{ $monitor ? 'monitor' : 'mine' }}">
                        <div class="chat-avatar {{ $item->type === 'team' ? 'team' : '' }}">{{ mb_substr($title, 0, 1) }}</div>
                        <div class="chat-list-content">
                            <div class="chat-list-top">
                                <div class="chat-list-name" title="{{ $title }}">{{ $title }}</div>
                                @if($latest)<div class="chat-list-time">{{ $latest->created_at->format('h:i A') }}</div>@endif
                            </div>
                            @if($monitor)<div class="chat-monitor-label">Monitoring · Read only</div>@endif
                            <div class="chat-last-message">
                                <span>{{ $latest ? \Illuminate\Support\Str::limit($latest->message ?: 'Attachment', 35) : 'No messages yet' }}</span>
                                @if(!$monitor && $item->unread_count > 0)<span class="unread-badge">{{ $item->unread_count }}</span>@endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-4 text-center text-muted">No conversations yet.</div>
                @endforelse
            </div>
        </aside>
        <main class="chat-main">
            @if(isset($conversation))
                @php
                    $other = $conversation->users->first(fn ($u) => (int) $u->id !== (int) auth()->id());
                    $chatName = $conversation->type === 'team'
                        ? ($conversation->name ?: 'Legacy Team Chat')
                        : (($isMonitoring ?? false) ? $conversation->users->pluck('name')->implode(' ↔ ') : ($other?->name ?? 'Deleted User'));
                @endphp
                <div class="chat-main-header">
                    <a class="mobile-back" href="{{ route('chat.index') }}" aria-label="Back to chats">←</a>
                    <div class="chat-avatar">{{ mb_substr($chatName ?: 'Chat', 0, 1) }}</div>
                    <div>
                        <div class="chat-main-name">{{ $chatName ?: 'Private Chat' }}</div>
                        <div class="chat-main-subtitle">{{ ($isMonitoring ?? false) ? 'Lower user conversation · Monitoring' : 'Your conversation' }}</div>
                    </div>
                </div>
                <div class="messages-container" id="messagesContainer">
                    @foreach($messages as $message)
                        @include('chat.partials.message', ['message' => $message, 'canSend' => $canSend])
                    @endforeach
                </div>
                <div class="chat-read-only" id="readOnlyNotice" @if($canSend) hidden @endif>
                    This conversation is read-only. You can send messages only in your eligible private chats.
                </div>
                @if($canSend)
                    <div class="chat-input-area" id="chatInputArea">
                        <div class="reply-preview" id="replyPreview">
                            <button type="button" class="reply-preview-close" onclick="cancelReply()" aria-label="Cancel reply">×</button>
                            <strong id="replyName"></strong><div id="replyMessage"></div>
                        </div>
                        <form action="{{ route('chat.send', $conversation->id) }}" method="POST" enctype="multipart/form-data" class="chat-form" id="chatForm">
                            @csrf
                            <input type="hidden" name="reply_to_id" id="replyToId" value="{{ old('reply_to_id') }}">
                            <input type="file" name="attachment" id="attachmentInput" style="display:none" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx">
                            <button type="button" class="attachment-button" onclick="document.getElementById('attachmentInput').click()" title="Attachment">📎</button>
                            <textarea name="message" id="messageInput" class="chat-message-input" placeholder="Type a message..." maxlength="5000" rows="1">{{ old('message') }}</textarea>
                            <button type="submit" class="send-button" title="Send message">➤</button>
                        </form>
                        <small id="selectedFile" class="text-muted"></small>
                    </div>
                @endif
            @else
                <div class="empty-chat"><div><div class="empty-chat-icon">💬</div><h4>Attendance Chat</h4><p>Select a conversation or start a new chat.</p></div></div>
            @endif
        </main>
    </div>
</div>
<script>
function scrollToBottom() {
    const box = document.getElementById('messagesContainer');
    if (box) box.scrollTop = box.scrollHeight;
}
function setReply(id, sender, text) {
    const input = document.getElementById('replyToId');
    if (!input || !window.chatCanSend) return;
    input.value = id;
    document.getElementById('replyName').textContent = sender;
    document.getElementById('replyMessage').textContent = text;
    document.getElementById('replyPreview').style.display = 'block';
    document.getElementById('messageInput').focus();
}
function replyFromButton(button) { setReply(button.dataset.id, button.dataset.name, button.dataset.message); }
function cancelReply() {
    const input = document.getElementById('replyToId');
    if (input) input.value = '';
    const preview = document.getElementById('replyPreview');
    if (preview) preview.style.display = 'none';
}
window.chatCanSend = @json($canSend);
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('newChatUser')?.addEventListener('change', function () {
        if (!this.value) return;
        const form = document.getElementById('startChatForm');
        form.action = this.value;
        form.requestSubmit();
    });
    document.querySelectorAll('.chat-filter').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.chat-filter').forEach(b => b.classList.toggle('active', b === button));
            document.querySelectorAll('[data-chat-kind]').forEach(item => {
                item.style.display = button.dataset.filter === 'all' || item.dataset.chatKind === button.dataset.filter ? '' : 'none';
            });
        });
    });
    const input = document.getElementById('messageInput');
    input?.addEventListener('input', () => {
        input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, 120) + 'px';
    });
    input?.addEventListener('keydown', event => {
        if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) {
            event.preventDefault(); document.getElementById('chatForm').requestSubmit();
        }
    });
    document.getElementById('chatForm')?.addEventListener('submit', event => {
        const file = document.getElementById('attachmentInput');
        if (!window.chatCanSend || (!input.value.trim() && !file.files.length)) {
            event.preventDefault(); return;
        }
        if (file.files[0]?.size > 10 * 1024 * 1024) {
            event.preventDefault(); document.getElementById('selectedFile').textContent = 'Maximum file size: 10 MB'; return;
        }
        event.currentTarget.querySelector('[type="submit"]').disabled = true;
    });
    document.getElementById('attachmentInput')?.addEventListener('change', function () {
        document.getElementById('selectedFile').textContent = this.files[0] ? 'Selected: ' + this.files[0].name : '';
    });
    scrollToBottom();
});
</script>
@if(isset($conversation))
<script>
(() => {
    let lastMessageId = @json($messages->last()?->id ?? 0);
    let stopped = false;
    let timer;
    const baseUrl = @json(route('chat.messages', $conversation->id));
    const box = document.getElementById('messagesContainer');
    function node(tag, className, text) {
        const element = document.createElement(tag);
        if (className) element.className = className;
        if (text !== undefined && text !== null) element.textContent = text;
        return element;
    }
    function messageNode(message) {
        const row = node('div', 'message-row ' + (message.is_mine ? 'mine' : 'other'));
        row.dataset.messageId = message.id;
        const bubble = node('div', 'message-bubble');
        row.appendChild(bubble);
        if (!message.is_mine) bubble.appendChild(node('div', 'message-sender', message.sender_name));
        if (message.reply_to) {
            const reply = node('div', 'reply-box');
            reply.appendChild(node('strong', '', message.reply_to.sender_name));
            reply.appendChild(node('span', '', message.reply_to.message || 'Attachment'));
            bubble.appendChild(reply);
        }
        if (message.message) bubble.appendChild(node('div', 'message-text', message.message));
        if (message.attachment) {
            const attachment = node('div', 'attachment-box');
            const link = node('a', 'file-link', '📎 ' + (message.attachment_name || 'Attachment'));
            const url = new URL(message.attachment, window.location.href);
            if (url.origin === window.location.origin) {
                link.href = url.href; link.target = '_blank'; link.rel = 'noopener noreferrer';
                attachment.appendChild(link); bubble.appendChild(attachment);
            }
        }
        bubble.appendChild(node('div', 'message-time', message.created_at));
        if (window.chatCanSend) {
            const button = node('button', 'reply-action', 'Reply');
            button.type = 'button';
            button.addEventListener('click', () => setReply(message.id, message.sender_name, message.message || 'Attachment'));
            bubble.appendChild(button);
        }
        return row;
    }
    async function poll() {
        if (stopped) return;
        try {
            if (document.hidden) return;
            const response = await fetch(baseUrl + '?last_message_id=' + lastMessageId, {
                credentials: 'same-origin', headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}
            });
            if ([401, 403, 404].includes(response.status)) {
                stopped = true;
                window.chatCanSend = false;
                document.getElementById('chatInputArea')?.setAttribute('hidden', '');
                const notice = document.getElementById('readOnlyNotice');
                notice.hidden = false; notice.textContent = 'Chat access changed. Reload the page.';
                box.replaceChildren();
                return;
            }
            if (!response.ok) return;
            const data = await response.json();
            if (!data.success) return;
            window.chatCanSend = !!data.can_send;
            if (!window.chatCanSend) {
                document.getElementById('chatInputArea')?.setAttribute('hidden', '');
                document.getElementById('readOnlyNotice').hidden = false;
                document.querySelectorAll('.reply-action').forEach(button => button.remove());
            }
            const nearBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 100;
            for (const message of data.messages) {
                const id = Number(message.id);
                if (!Number.isSafeInteger(id)) continue;
                if (!box.querySelector('[data-message-id="' + id + '"]')) box.appendChild(messageNode(message));
                lastMessageId = Math.max(lastMessageId, id);
            }
            if (nearBottom) scrollToBottom();
        } catch (error) { console.error('Chat polling failed', error); }
        finally { if (!stopped) timer = setTimeout(poll, 3000); }
    }
    timer = setTimeout(poll, 3000);
    window.addEventListener('pagehide', () => { stopped = true; clearTimeout(timer); });
})();
</script>
@endif
@endsection
