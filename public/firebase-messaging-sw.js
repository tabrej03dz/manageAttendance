importScripts(
    'https://www.gstatic.com/firebasejs/13.0.0/firebase-app-compat.js'
);

importScripts(
    'https://www.gstatic.com/firebasejs/13.0.0/firebase-messaging-compat.js'
);

firebase.initializeApp({
    apiKey: 'YOUR_FIREBASE_API_KEY',
    authDomain: 'https://accounts.google.com/o/oauth2/auth',
    projectId: 'attendanceapp-master-7b02c',
    storageBucket: 'YOUR_PROJECT.firebasestorage.app',
    messagingSenderId: 'YOUR_MESSAGING_SENDER_ID',
    appId: 'YOUR_FIREBASE_APP_ID'
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log(
        '[firebase-messaging-sw.js] Background message:',
        payload
    );

    /*
     * Backend already sends "notification" payload.
     *
     * FCM normally displays notification automatically.
     * Therefore we don't call showNotification here,
     * otherwise duplicate notifications can appear.
     */
});

self.addEventListener('notificationclick', (event) => {

    event.notification.close();

    const data = event.notification.data || {};

    const conversationId =
        data.conversation_id ||
        event.notification?.data?.FCM_MSG?.data?.conversation_id;

    if (!conversationId) {
        return;
    }

    const url = `/chat/${conversationId}`;

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then((windowClients) => {

            for (const client of windowClients) {

                if ('focus' in client) {
                    client.navigate(url);

                    return client.focus();
                }
            }

            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});