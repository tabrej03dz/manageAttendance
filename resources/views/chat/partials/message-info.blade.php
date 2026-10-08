@if(isset($conversation))
<style>
.chat-message-info-button{border:0;background:transparent;color:inherit;font-size:11px;text-decoration:underline;cursor:pointer;padding:4px 6px}
#chatMessageInfoDialog{width:min(440px,92vw);max-height:80vh;border:0;border-radius:14px;padding:20px;box-shadow:0 12px 40px #0003;color:#172033;background:#fff}
#chatMessageInfoDialog::backdrop{background:#0006}
#chatMessageInfoDialog header{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:14px}
#chatMessageInfoDialog button{cursor:pointer}
#chatMessageInfoList{list-style:none;padding:0;margin:12px 0;max-height:55vh;overflow:auto}
#chatMessageInfoList li{padding:10px 0;border-bottom:1px solid #e5e7eb}
#chatMessageInfoList small{display:block;margin-top:4px;color:#64748b}
</style>

<dialog id="chatMessageInfoDialog" aria-labelledby="chatMessageInfoHeading">
    <header>
        <strong id="chatMessageInfoHeading">Message info</strong>
        <button type="button" id="chatMessageInfoClose" aria-label="Close">✕</button>
    </header>
    <p id="chatMessageInfoStatus" role="status" aria-live="polite"></p>
    <ul id="chatMessageInfoList"></ul>
    <button type="button" id="chatMessageInfoRefresh">Refresh</button>
    <button type="button" id="chatMessageInfoMore" hidden>Load more</button>
</dialog>

<script>
(() => {
    const box = document.getElementById('messagesContainer');
    if (!box) return;

    const seenUrl = @json(route('chat.messages.seen', $conversation->id));
    const infoTemplate = @json(route('chat.messages.info', 0));
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content
        || @json(csrf_token());

    const dialog = document.getElementById('chatMessageInfoDialog');
    const status = document.getElementById('chatMessageInfoStatus');
    const list = document.getElementById('chatMessageInfoList');
    const more = document.getElementById('chatMessageInfoMore');

    const submitted = new Set();
    const pending = new Set();
    const timers = new Map();

    let sending = false;
    let stopped = false;
    let selectedId = null;
    let infoPage = 1;
    let infoVersion = 0;
    let flushTimer;

    const active = () =>
        !document.hidden && document.hasFocus() && !dialog.open;

    function visible(row) {
        if (!active() || !row.isConnected) return false;

        const r = row.getBoundingClientRect();
        const b = box.getBoundingClientRect();
        const height = Math.min(r.bottom, b.bottom, innerHeight)
            - Math.max(r.top, b.top, 0);

        return r.width > 0 && r.height > 0
            && height >= Math.min(40, r.height);
    }

    function queue(row) {
        const id = Number(row.dataset.messageId);

        if (
            !Number.isSafeInteger(id) || id <= 0
            || submitted.has(id) || pending.has(id) || timers.has(id)
            || row.classList.contains('mine') || !visible(row)
        ) return;

        timers.set(id, setTimeout(() => {
            timers.delete(id);
            if (visible(row)) {
                pending.add(id);
                scheduleFlush();
            }
        }, 600));
    }

    function scheduleFlush() {
        clearTimeout(flushTimer);
        flushTimer = setTimeout(flush, 150);
    }

    async function flush() {
        if (sending || stopped || !active() || !pending.size) return;

        const ids = [...pending].filter(id => {
            const row = box.querySelector(
                '[data-message-id="' + id + '"]'
            );

            if (!row || !visible(row)) {
                pending.delete(id);
                return false;
            }
            return true;
        }).slice(0, 200);

        if (!ids.length) return;

        sending = true;
        try {
            const response = await fetch(seenUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({message_ids: ids})
            });

            if ([401,403,404,419].includes(response.status)) {
                stopped = true;
                return;
            }

            const payload = response.ok ? await response.json() : null;

            if (payload?.success) {
                ids.forEach(id => {
                    submitted.add(id);
                    pending.delete(id);
                });
            }
        } catch (error) {
            console.error('Chat seen acknowledgement failed', error);
        } finally {
            sending = false;
            if (!stopped && pending.size) {
                flushTimer = setTimeout(flush, 3000);
            }
        }
    }

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                queue(entry.target);
            } else {
                const id = Number(entry.target.dataset.messageId);
                clearTimeout(timers.get(id));
                timers.delete(id);
            }
        });
    }, {root: box, threshold: [0,0.1,0.5,1]});

    function decorate(row) {
        if (row.dataset.seenObserver) return;
        row.dataset.seenObserver = '1';

        if (row.classList.contains('mine')) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'chat-message-info-button';
            button.textContent = 'Message info';
            button.dataset.infoId = row.dataset.messageId;

            (
                row.querySelector('.message-bottom')
                || row.querySelector('.message-bubble')
            )?.appendChild(button);
        } else {
            observer.observe(row);
            queue(row);
        }
    }

    function scan() {
        box.querySelectorAll('.message-row[data-message-id]')
            .forEach(row => {
                decorate(row);
                queue(row);
            });

        if (pending.size) scheduleFlush();
    }

    const changes = new MutationObserver(scan);
    changes.observe(box, {childList: true, subtree: true});

    box.addEventListener('scroll', scan, {passive: true});
    window.addEventListener('focus', scan);
    document.addEventListener('visibilitychange', scan);
    dialog.addEventListener('close', scan);
    scan();

    const date = value => new Date(value).toLocaleString(undefined, {
        dateStyle: 'medium',
        timeStyle: 'medium'
    });

    async function loadInfo(id, page = 1) {
        const version = ++infoVersion;
        if (page === 1) list.replaceChildren();

        status.textContent = 'Loading…';
        more.hidden = true;

        try {
            const url = infoTemplate.replace(
                '/0/info', '/' + id + '/info'
            ) + '?page=' + page;

            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: {'Accept': 'application/json'}
            });

            if (!response.ok) {
                throw new Error(
                    'Could not load message info (' + response.status + ').'
                );
            }

            const payload = await response.json();
            if (version !== infoVersion) return;

            const data = payload.data;

            status.textContent = data.seen_count
                ? 'Seen by ' + data.seen_count + ' user(s)'
                : 'No one has seen this message yet.';

            data.seen_by.forEach(read => {
                const item = document.createElement('li');
                const name = document.createElement('strong');
                const first = document.createElement('small');

                name.textContent = read.name + ' (ID ' + read.user_id + ')';
                first.textContent = 'First seen: ' + date(read.first_seen_at);
                item.append(name, first);

                if (read.last_seen_at !== read.first_seen_at) {
                    const last = document.createElement('small');
                    last.textContent = 'Last seen: ' + date(read.last_seen_at);
                    item.append(last);
                }

                list.appendChild(item);
            });

            infoPage = page;
            more.hidden = page >= data.pagination.last_page;
        } catch (error) {
            if (version === infoVersion) status.textContent = error.message;
        }
    }

    box.addEventListener('click', event => {
        const button = event.target.closest('[data-info-id]');
        if (!button) return;

        selectedId = Number(button.dataset.infoId);
        if (!dialog.open) dialog.showModal();
        loadInfo(selectedId);
    });

    document.getElementById('chatMessageInfoClose')
        .addEventListener('click', () => dialog.close());

    document.getElementById('chatMessageInfoRefresh')
        .addEventListener('click', () => {
            if (selectedId) loadInfo(selectedId);
        });

    more.addEventListener('click', () => {
        if (selectedId) loadInfo(selectedId, infoPage + 1);
    });

    window.addEventListener('pagehide', () => {
        stopped = true;
        clearTimeout(flushTimer);
        timers.forEach(clearTimeout);
        observer.disconnect();
        changes.disconnect();
    });
})();
</script>
@endif