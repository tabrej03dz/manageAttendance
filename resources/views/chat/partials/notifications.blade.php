{{--
    Chat notifications: dashboard modal -> native browser permission click -> Firebase FCM + notification feed.
    Include this partial ONCE in dashboard.layout.root before </body>.
    Delete the old notificationPermissionModal and its associated script.
    Requires a meta csrf-token, named chat notification routes, Firebase configuration,
    and public/firebase-messaging-sw.js.
--}}


@if(request()->routeIs('home'))
<style>
  .chat-permission-overlay{position:fixed;inset:0;z-index:99999;background:rgba(6,12,30,.72);backdrop-filter:blur(5px);display:none;align-items:center;justify-content:center;padding:16px}
  .chat-permission-overlay.is-visible{display:flex}
  .chat-permission-dialog{position:relative;background:#fff;color:#17213a;border-radius:22px;max-width:430px;width:100%;padding:36px 30px 30px;text-align:center;box-shadow:0 25px 90px rgba(0,0,0,.28);font-family:inherit}
  .chat-permission-symbol{width:76px;height:76px;margin:0 auto 19px;border-radius:23px;display:grid;place-items:center;background:linear-gradient(145deg,#eef2ff,#e0e7ff);color:#4f46e5}
  .chat-permission-heading{font-size:23px;font-weight:800;line-height:1.25;margin:0 0 12px;color:#111827}
  .chat-permission-description{font-size:14px;color:#64748b;line-height:1.7;margin:0 0 24px}
  .chat-permission-allow{width:100%;border:0;border-radius:12px;padding:14px 20px;font-size:15px;font-weight:700;background:#4f46e5;color:#fff;cursor:pointer;box-shadow:0 5px 14px rgba(79,70,229,.22)}
  .chat-permission-allow:hover{background:#4338ca}
  .chat-permission-allow:disabled{opacity:.65;cursor:wait}
  .chat-permission-later{display:block;border:0;background:none;color:#64748b;cursor:pointer;font-size:14px;margin:18px auto 0;padding:5px 12px}
  .chat-permission-message{font-size:12px;color:#b45309;line-height:1.5;margin:15px 0 0}
  @media(max-width:480px){.chat-permission-dialog{padding:30px 20px 25px;border-radius:18px}.chat-permission-heading{font-size:21px}}
</style>
<div class="chat-permission-overlay" id="chatPermissionOverlay" role="presentation">
  <div class="chat-permission-dialog" role="dialog" aria-modal="true" aria-labelledby="chatPermissionTitle" aria-describedby="chatPermissionDescription">
    <div class="chat-permission-symbol" aria-hidden="true">
      <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>
    </div>
    <h2 class="chat-permission-heading" id="chatPermissionTitle">Never Miss an Update!</h2>
    <p class="chat-permission-description" id="chatPermissionDescription">Team ke important messages, attendance alerts aur updates turant paane ke liye browser notifications allow karein.</p>
    <button type="button" class="chat-permission-allow" id="chatPermissionAllow">🔔 Allow Notifications</button>
    <button type="button" class="chat-permission-later" id="chatPermissionLater">Maybe Later</button>
    <p class="chat-permission-message" id="chatPermissionMessage" role="status" hidden></p>
  </div>
</div>
@endif

<div id="chatNotificationToasts" aria-live="polite"
     style="position:fixed;right:20px;bottom:20px;z-index:1100;display:flex;flex-direction:column;gap:10px;max-width:min(360px,calc(100vw - 40px));"></div>

<div id="chatNotificationsControl"
     style="position:fixed;right:20px;bottom:6px;z-index:1101;display:none;max-width:min(340px,calc(100vw - 40px));">
    <button type="button" id="enableChatBrowserNotifications"
            style="border:1px solid #c7d2fe;border-radius:8px;padding:7px 12px;font-size:12px;font-weight:600;background:#eef2ff;color:#3730a3;cursor:pointer;">
        🔔 Enable Notifications
    </button>
    <p id="chatNotificationPermissionHelp" role="status" hidden
       style="background:white;border:1px solid #e2e8f0;border-radius:8px;margin:6px 0 0;padding:9px;font-size:12px;color:#92400e;box-shadow:0 4px 12px #0001;"></p>
</div>

<script src="https://www.gstatic.com/firebasejs/13.0.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/13.0.0/firebase-messaging-compat.js"></script>
<script>
(() => {
    'use strict';
    if (window.chatNotificationWidgetStarted) return;
    window.chatNotificationWidgetStarted = true;

    const firebaseConfig = {
        apiKey: @json(config('chat_notifications.web.api_key')),
        authDomain: @json(config('chat_notifications.web.auth_domain')),
        projectId: @json(config('chat_notifications.web.project_id')),
        storageBucket: @json(config('chat_notifications.web.storage_bucket')),
        messagingSenderId: @json(config('chat_notifications.web.messaging_sender_id')),
        appId: @json(config('chat_notifications.web.app_id'))
    };
    const firebaseVapidKey = @json(config('chat_notifications.web.vapid_key'));
    const registerDeviceEndpoint = @json(route('chat.notifications.device'));
    const feedEndpoint = @json(route('chat.notifications.feed'));
    const currentConversation = @json(request()->route('conversation') instanceof \App\Models\ChatConversation ? (int) request()->route('conversation')->id : null);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const permissionOverlay = document.getElementById('chatPermissionOverlay');
    const permissionAllow = document.getElementById('chatPermissionAllow');
    const permissionLater = document.getElementById('chatPermissionLater');
    const permissionMessage = document.getElementById('chatPermissionMessage');
    const box = document.getElementById('chatNotificationToasts');
    const control = document.getElementById('chatNotificationsControl');
    const enableButton = document.getElementById('enableChatBrowserNotifications');
    const help = document.getElementById('chatNotificationPermissionHelp');

    const isSupported = ('Notification' in window) &&
        ('serviceWorker' in navigator) && window.isSecureContext;

    let cursor = null;
    let stopped = false;
    let pollTimer = null;
    let soundEnabled = false;
    let audio = null;
    let registrationPromise = null;

    function closePermissionModal() {
        permissionOverlay?.classList.remove('is-visible');
    }
    function showPermissionModal() {
        if (!permissionOverlay || !isSupported || Notification.permission === 'granted') return;
        if (sessionStorage.getItem('chat_notification_later') === '1') return;
        permissionOverlay.classList.add('is-visible');
    }
    function modalMessage(message) {
        if (!permissionMessage) return;
        permissionMessage.textContent = message;
        permissionMessage.hidden = !message;
    }

    function showHelp(message) {
        help.textContent = message;
        help.hidden = false;
    }

    function clearHelp() {
        help.hidden = true;
        help.textContent = '';
    }

    function updatePermissionUI() {
        if (!isSupported) {
            control.style.display = 'block';
            enableButton.disabled = true;
            enableButton.textContent = 'Notifications unavailable';
            showHelp('Browser notifications ke liye HTTPS aur supported browser zaroori hai.');
            return;
        }
        if (Notification.permission === 'granted') {
            control.style.display = 'none';
            clearHelp();
        } else {
            control.style.display = 'block';
            enableButton.disabled = false;
            enableButton.textContent = Notification.permission === 'denied'
                ? '🔔 Notifications blocked'
                : '🔔 Enable Notifications';
            if (Notification.permission === 'denied') {
                showHelp('Browser ne notifications block ki hain. Address bar ke site controls (lock/tune icon) → Site settings → Notifications → Allow karein, phir page refresh karein.');
            } else {
                clearHelp();
            }
        }
    }

    // IMPORTANT: Browser native permission prompt MUST be invoked directly
    // from a user click, BEFORE any awaited network/service-worker work.
    async function onEnableClick() {
        if (!isSupported) return;
        if (Notification.permission === 'denied') {
            modalMessage('Notifications pehle se Block hain. Address bar ke Site controls → Site settings → Notifications → Allow karein, phir refresh karein.');
            updatePermissionUI();
            return;
        }

        let permission = Notification.permission;
        if (permission === 'default') {
            try {
                permission = await Notification.requestPermission();
            } catch (error) {
                console.error('Native notification permission request failed:', error);
                showHelp('Browser permission request nahi dikha paaya. Site settings mein notification permission check karein.');
                modalMessage('Browser permission request nahi dikha paaya. Site Settings check karein.');
                return;
            }
        }

        updatePermissionUI();
        if (permission !== 'granted') {
            modalMessage(permission === 'denied'
                ? 'Browser ne permission Block kar di. Site settings se notification Allow karein.'
                : 'Edge ke address bar mein notification permission chip aaye to uspar click karke Allow karein.');
            return;
        }
        closePermissionModal();
        await registerWebPush();
    }

    async function registerWebPush() {
        if (!isSupported || Notification.permission !== 'granted') return false;
        if (registrationPromise) return registrationPromise;

        registrationPromise = (async () => {
            try {
                if (!firebaseConfig.apiKey || !firebaseConfig.projectId ||
                    !firebaseConfig.messagingSenderId || !firebaseConfig.appId || !firebaseVapidKey) {
                    throw new Error('Firebase web configuration / VAPID key missing');
                }
                if (typeof firebase === 'undefined' || !firebase.messaging) {
                    throw new Error('Firebase messaging SDK not loaded');
                }
                if (!csrfToken) throw new Error('CSRF token missing in page meta');

                if (!firebase.apps.length) firebase.initializeApp(firebaseConfig);
                const serviceWorkerRegistration = await navigator.serviceWorker.register(
                    '/firebase-messaging-sw.js', {scope: '/'}
                );
                const messaging = firebase.messaging();
                const fcmToken = await messaging.getToken({
                    vapidKey: firebaseVapidKey,
                    serviceWorkerRegistration
                });
                if (!fcmToken) throw new Error('Firebase returned empty FCM token');

                let deviceId = localStorage.getItem('chat_web_device_id');
                if (!deviceId) {
                    const uuid = window.crypto?.randomUUID?.() ||
                        ('fallback-' + Date.now() + '-' + Math.random().toString(36).slice(2));
                    deviceId = 'web_' + uuid.replace(/-/g, '');
                    localStorage.setItem('chat_web_device_id', deviceId);
                }

                const response = await fetch(registerDeviceEndpoint, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        device_id: deviceId,
                        platform: 'web',
                        fcm_token: fcmToken
                    })
                });
                const result = await response.json().catch(() => ({}));
                if (!response.ok || !result.success) {
                    throw new Error('Device registration failed: HTTP ' + response.status + ' ' + JSON.stringify(result));
                }
                console.info('Web push notifications registered successfully');
                return true;
            } catch (error) {
                console.error('Web push setup failed:', error);
                showHelp('Permission mil gayi, lekin Firebase device register nahi hua. Console aur Firebase configuration check karein.');
                control.style.display = 'block';
                enableButton.textContent = 'Retry notification setup';
                return false;
            }
        })();

        try { return await registrationPromise; }
        finally { registrationPromise = null; }
    }

    enableButton.addEventListener('click', onEnableClick);
    // No await or asynchronous work BEFORE requestPermission: preserve click activation.
    permissionAllow?.addEventListener('click', async () => {
        permissionAllow.disabled = true;
        modalMessage('');
        try { await onEnableClick(); }
        finally { permissionAllow.disabled = false; }
    });
    permissionLater?.addEventListener('click', () => {
        sessionStorage.setItem('chat_notification_later', '1');
        closePermissionModal();
    });
    // Dashboard par ek baar, load ke baad custom prompt. Browser native prompt only on click.
    if (permissionOverlay) {
        const initializeModal = () => setTimeout(showPermissionModal, 900);
        if (document.readyState === 'complete') initializeModal();
        else window.addEventListener('load', initializeModal, {once: true});
    }
    updatePermissionUI();

    // Already granted? Register silently, without asking permission again.
    if (isSupported && Notification.permission === 'granted') {
        registerWebPush();
    }
    // If user changes browser site settings while tab is open, sync status.
    window.addEventListener('focus', () => {
        const wasHidden = control.style.display === 'none';
        updatePermissionUI();
        if (isSupported && Notification.permission === 'granted') closePermissionModal();
        if (!wasHidden && isSupported && Notification.permission === 'granted') registerWebPush();
    });

    document.addEventListener('pointerdown', () => {
        soundEnabled = true;
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext && !audio) audio = new AudioContext();
            audio?.resume().catch(() => {});
        } catch (_) {}
    }, {once: true});

    function beep() {
        if (!soundEnabled || !audio || audio.state !== 'running') return;
        try {
            const oscillator = audio.createOscillator();
            const gain = audio.createGain();
            oscillator.connect(gain);
            gain.connect(audio.destination);
            oscillator.frequency.value = 750;
            gain.gain.setValueAtTime(0.04, audio.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audio.currentTime + 0.2);
            oscillator.start();
            oscillator.stop(audio.currentTime + 0.2);
        } catch (_) {}
    }

    function toast(notice) {
        if (!notice || !notice.url) return;
        if (Number(notice.conversation_id) === currentConversation &&
            document.hasFocus() && !document.hidden) return;
        let url;
        try { url = new URL(notice.url, window.location.href); }
        catch (_) { return; }
        if (url.origin !== window.location.origin) return;

        const card = document.createElement('div');
        card.style.cssText = 'background:#fff;border:1px solid #dbeafe;border-left:4px solid #2563eb;border-radius:10px;padding:12px;box-shadow:0 6px 24px #0002;';
        const close = document.createElement('button');
        close.type = 'button';
        close.textContent = '×';
        close.setAttribute('aria-label', 'Dismiss notification');
        close.style.cssText = 'float:right;border:0;background:transparent;font-size:20px;cursor:pointer;';
        close.onclick = () => card.remove();

        const link = document.createElement('a');
        link.href = url.href;
        link.style.cssText = 'display:block;color:#1e40af;font-weight:600;';
        link.textContent = notice.title || 'New chat message';

        const body = document.createElement('div');
        body.textContent = notice.body || '';
        body.style.cssText = 'font-size:12px;color:#475569;margin-top:4px;';
        card.append(close, link, body);
        box.appendChild(card);
        while (box.children.length > 4) box.firstElementChild.remove();
        setTimeout(() => card.remove(), 12000);
        beep();

        if (document.hidden && isSupported && Notification.permission === 'granted') {
            try {
                const nativeNotification = new Notification(link.textContent, {
                    body: body.textContent,
                    tag: 'chat-message-' + notice.message_id
                });
                nativeNotification.onclick = () => {
                    window.focus();
                    window.location.href = url.href;
                    nativeNotification.close();
                };
            } catch (error) { console.warn('Native notification failed:', error); }
        }
    }

    async function poll() {
        if (stopped) return;
        try {
            const url = feedEndpoint + (cursor === null
                ? ''
                : (feedEndpoint.includes('?') ? '&' : '?') + 'after_id=' + encodeURIComponent(cursor));
            const response = await fetch(url, {
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if ([401, 403, 404].includes(response.status)) {
                stopped = true;
                box.replaceChildren();
                return;
            }
            if (!response.ok) return;
            const data = await response.json();
            if (!data.success) return;
            const next = Number(data.cursor);
            if (!Number.isSafeInteger(next) || next < 0) return;
            for (const notice of data.notifications || []) toast(notice);
            cursor = next;
        } catch (error) {
            console.warn('Chat notification feed poll failed:', error);
        } finally {
            if (!stopped) pollTimer = setTimeout(poll, 5000);
        }
    }

    poll();
    window.addEventListener('pagehide', () => {
        stopped = true;
        clearTimeout(pollTimer);
    });
})();
</script>
