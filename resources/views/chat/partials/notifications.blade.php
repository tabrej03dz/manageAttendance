{{-- Include once in dashboard.layout.root, before </body>. --}}
<div id="chatNotificationToasts" aria-live="polite" style="position:fixed;right:20px;bottom:20px;z-index:1100;display:flex;flex-direction:column;gap:10px;max-width:min(360px,calc(100vw - 40px));"></div>
<div style="position:fixed;right:20px;bottom:5px;z-index:1101;">
    <button type="button" id="enableChatBrowserNotifications" style="border:0;border-radius:6px;padding:4px 8px;font-size:11px;background:#eef2ff;color:#3730a3;">Enable desktop notifications</button>
</div>
<script src="https://www.gstatic.com/firebasejs/13.0.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/13.0.0/firebase-messaging-compat.js"></script>
<script>
const firebaseConfig = {
    apiKey: @json(config('chat_notifications.web.api_key')),
    authDomain: @json(config('chat_notifications.web.auth_domain')),
    projectId: @json(config('chat_notifications.web.project_id')),
    storageBucket: @json(config('chat_notifications.web.storage_bucket')),
    messagingSenderId: @json(config('chat_notifications.web.messaging_sender_id')),
    appId: @json(config('chat_notifications.web.app_id'))
};

const firebaseVapidKey =
    @json(config('chat_notifications.web.vapid_key'));

const registerDeviceEndpoint =
    @json(route('chat.notifications.device'));

const csrfToken =
    document.querySelector('meta[name="csrf-token"]')?.content;
(() => {
    if (window.chatNotificationWidgetStarted) return;
    window.chatNotificationWidgetStarted = true;
    const endpoint = @json(route('chat.notifications.feed'));
    const currentConversation = @json(request()->route('conversation') instanceof \App\Models\ChatConversation ? (int) request()->route('conversation')->id : null);
    const box = document.getElementById('chatNotificationToasts');
    const enable = document.getElementById('enableChatBrowserNotifications');
    let cursor = null;
    let stopped = false;
    let timer;
    let soundEnabled = false;
    let audio;
    document.addEventListener('pointerdown', () => {
        soundEnabled = true;
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext && !audio) audio = new AudioContext();
            audio?.resume().catch(() => {});
        } catch (_) {}
    }, {once:true});
    if (
        !('Notification' in window) ||
        !('serviceWorker' in navigator) ||
        !window.isSecureContext
    ) {
        enable.hidden = true;
        console.warn('Web push requires HTTPS and browser support');
    } else {

        enable.hidden = Notification.permission === 'granted';

        enable.addEventListener('click', async () => {

            const success = await registerWebPush();

            if (success) {
                enable.hidden = true;
            }
        });

        if (Notification.permission === 'granted') {

            window.addEventListener('load', () => {
                registerWebPush();
            }, { once: true });

            if (document.readyState === 'complete') {
                registerWebPush();
            }
        }
    }
    function beep() {
        if (!soundEnabled || !audio || audio.state !== 'running') return;
        const oscillator = audio.createOscillator();
        const gain = audio.createGain();
        oscillator.connect(gain); gain.connect(audio.destination);
        oscillator.frequency.value = 750;
        gain.gain.setValueAtTime(0.04, audio.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, audio.currentTime + 0.2);
        oscillator.start(); oscillator.stop(audio.currentTime + 0.2);
    }
    function toast(notice) {
        if (Number(notice.conversation_id) === currentConversation && document.hasFocus() && !document.hidden) return;
        const url = new URL(notice.url, window.location.href);
        if (url.origin !== window.location.origin) return;
        const card = document.createElement('div');
        card.style.cssText = 'background:#fff;border:1px solid #dbeafe;border-left:4px solid #2563eb;border-radius:10px;padding:12px;box-shadow:0 6px 24px #0002;';
        const close = document.createElement('button');
        close.type = 'button'; close.textContent = '×';
        close.setAttribute('aria-label', 'Dismiss notification');
        close.style.cssText = 'float:right;border:0;background:transparent;font-size:20px;';
        close.onclick = () => card.remove();
        const link = document.createElement('a');
        link.href = url.href; link.style.cssText = 'display:block;color:#1e40af;font-weight:600;';
        link.textContent = notice.title;
        const body = document.createElement('div'); body.textContent = notice.body;
        body.style.cssText = 'font-size:12px;color:#475569;margin-top:4px;';
        card.append(close, link, body); box.appendChild(card);
        while (box.children.length > 4) box.firstElementChild.remove();
        setTimeout(() => card.remove(), 12000);
        beep();
        if (document.hidden && 'Notification' in window && Notification.permission === 'granted') {
            try {
                const native = new Notification(notice.title, {body:notice.body,tag:'chat-message-' + notice.message_id});
                native.onclick = () => {window.focus();window.location.href = url.href;native.close();};
            } catch (_) {}
        }
    }
    async function poll() {
        if (stopped) return;
        try {
            const response = await fetch(endpoint + (cursor === null ? '' : '?after_id=' + cursor), {
                credentials:'same-origin', cache:'no-store',
                headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}
            });
            if ([401,403,404].includes(response.status)) {stopped=true;box.replaceChildren();return;}
            if (!response.ok) return;
            const data = await response.json();
            if (!data.success) return;
            const next = Number(data.cursor);
            if (!Number.isSafeInteger(next) || next < 0) return;
            for (const notice of data.notifications || []) toast(notice);
            cursor = next;
        } catch (_) {} finally {if(!stopped) timer=setTimeout(poll,5000);}
    }
    poll();
    window.addEventListener('pagehide', () => {stopped=true;clearTimeout(timer);});
})();


// async function registerWebPush() {

//     if (!('serviceWorker' in navigator)) {
//         console.warn('Service Worker not supported');
//         return;
//     }

//     if (!('Notification' in window)) {
//         console.warn('Notifications not supported');
//         return;
//     }

//     if (!window.isSecureContext) {
//         console.warn('HTTPS required for push notifications');
//         return;
//     }

//     try {

//         const permission =
//             await Notification.requestPermission();

//         if (permission !== 'granted') {
//             console.warn('Notification permission not granted');
//             return;
//         }

//         const registration =
//             await navigator.serviceWorker.register(
//                 '/firebase-messaging-sw.js'
//             );

//         console.log(
//             'Firebase service worker registered',
//             registration
//         );

//         if (!firebase.apps.length) {
//             firebase.initializeApp(firebaseConfig);
//         }

//         const messaging = firebase.messaging();

//         const token = await messaging.getToken({
//             vapidKey: firebaseVapidKey,
//             serviceWorkerRegistration: registration
//         });

//         if (!token) {
//             console.warn('FCM token not available');
//             return;
//         }

//         let deviceId =
//             localStorage.getItem('chat_web_device_id');

//         if (!deviceId) {

//             deviceId =
//                 'web_' +
//                 crypto.randomUUID()
//                     .replace(/-/g, '');

//             localStorage.setItem(
//                 'chat_web_device_id',
//                 deviceId
//             );
//         }

//         const response = await fetch(
//             registerDeviceEndpoint,
//             {
//                 method: 'POST',

//                 credentials: 'same-origin',

//                 headers: {
//                     'Content-Type':
//                         'application/json',

//                     'Accept':
//                         'application/json',

//                     'X-CSRF-TOKEN':
//                         csrfToken,

//                     'X-Requested-With':
//                         'XMLHttpRequest'
//                 },

//                 body: JSON.stringify({
//                     device_id: deviceId,
//                     platform: 'web',
//                     fcm_token: token
//                 })
//             }
//         );

//         const result = await response.json();

//         if (!response.ok) {
//             console.error(
//                 'FCM device registration failed',
//                 result
//             );

//             return;
//         }

//         console.log(
//             'Web FCM device registered successfully'
//         );

//     } catch (error) {

//         console.error(
//             'Web push setup failed:',
//             error
//         );
//     }
// }


async function registerWebPush() {

    if (!('serviceWorker' in navigator)) {
        console.error('Service Worker not supported');
        return false;
    }

    if (!window.isSecureContext) {
        console.error('HTTPS required');
        return false;
    }

    if (!('Notification' in window)) {
        console.error('Notification API not supported');
        return false;
    }

    try {

        let permission = Notification.permission;

        if (permission === 'default') {
            permission = await Notification.requestPermission();
        }

        if (permission !== 'granted') {
            console.warn('Notification permission:', permission);
            return false;
        }

        if (
            !firebaseConfig.apiKey ||
            !firebaseConfig.projectId ||
            !firebaseConfig.messagingSenderId ||
            !firebaseConfig.appId ||
            !firebaseVapidKey
        ) {
            console.error('Firebase configuration incomplete');
            return false;
        }

        if (typeof firebase === 'undefined') {
            console.error('Firebase SDK not loaded');
            return false;
        }

        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }

        const registration = await navigator.serviceWorker.register(
            '/firebase-messaging-sw.js',
            { scope: '/' }
        );

        console.log('Service Worker registered');

        const messaging = firebase.messaging();

        const token = await messaging.getToken({
            vapidKey: firebaseVapidKey,
            serviceWorkerRegistration: registration
        });

        if (!token) {
            console.error('Firebase did not return an FCM token');
            return false;
        }

        console.log('FCM token generated successfully');

        let deviceId = localStorage.getItem('chat_web_device_id');

        if (!deviceId) {

            const randomId = crypto.randomUUID()
                .replace(/-/g, '');

            deviceId = 'web_' + randomId;

            localStorage.setItem(
                'chat_web_device_id',
                deviceId
            );
        }

        if (!csrfToken) {
            console.error('CSRF token missing');
            return false;
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
                fcm_token: token
            })
        });

        const result = await response.json();

        if (!response.ok || !result.success) {

            console.error(
                'Device registration failed',
                response.status,
                result
            );

            return false;
        }

        console.log(
            'Device saved successfully in chat_devices'
        );

        return true;

    } catch (error) {

        console.error('Web push registration error:', error);

        return false;
    }
}
</script>
