import {Base64} from "js-base64";

const refreshInterval = 12000;
let interval;

const key = document.getElementById('key');
const notify = document.getElementById('notify');
const push = document.getElementById('push-notification');

if (key !== null || push !== null) {
    const socket = new WebSocket("ws://localhost:8443");
    socket.onopen = (event) => {
        console.log("Notification opened. Event: " + event.type);
        interval = setInterval(() => {
            sendMessage(socket, key.dataset.hash);
            socket.onmessage = (event) => {
                const data = JSON.parse(event.data);

                if (data.hash === Base64.decode(key.dataset.hash)) {
                    const keys = Object.keys(data.notify);

                    if (notify !== null) {
                        if (keys.length > 0) {
                            notify.classList.remove('bg-gray-500');
                            notify.classList.add('bg-green-500');
                        } else {
                            notify.classList.remove('bg-green-500');
                            notify.classList.add('bg-gray-500');
                        }
                    }
                    const created = document.getElementById('push-notification-date');
                    const body = document.getElementById('push-notification-body');
                    const from = document.getElementById('push-notification-from');
                    const url = document.getElementById('push-notification-url');

                    if (keys.length === 0 && push !== null) {
                        push.classList.add('sr-only');
                    }

                    if (keys.length > 0 && push !== null) {
                        body.innerText = data.notify.message.length > 50 ? data.notify.message.substr(0, 50) + '...' : data.notify.message;
                        from.innerText = data.notify.from;
                        const date = new Date(data.notify.createdAt.date);
                        created.innerText = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                        url.href = data.notify.url;
                        push.classList.remove('sr-only');
                    }
                }
            };
        }, refreshInterval);
    };
    socket.onclose = function (event) {
        console.log('Socket is closed.', event);
        //clearInterval(interval);
    };

    socket.onerror = function (error) {
        console.error('Socket encountered error: ', error.message, 'Closing socket');
        socket.close();
    };

}

const isOpen = (socket) => {
    return socket.readyState === ws.OPEN;
}

const sendMessage = (socket, key) => {
    socket.send(JSON.stringify({
        hash: key
    }));
};

const error = (code) => {
    let reason;
    if (code === 1000)
        reason = "Normal closure, meaning that the purpose for which the connection was established has been fulfilled.";
    else if (code === 1001)
        reason = "An endpoint is \"going away\", such as a server going down or a browser having navigated away from a page.";
    else if (code === 1002)
        reason = "An endpoint is terminating the connection due to a protocol error";
    else if (code === 1003)
        reason = "An endpoint is terminating the connection because it has received a type of data it cannot accept (e.g., an endpoint that understands only text data MAY send this if it receives a binary message).";
    else if (code === 1004)
        reason = "Reserved. The specific meaning might be defined in the future.";
    else if (code === 1005)
        reason = "No status code was actually present.";
    else if (code === 1006)
        reason = "The connection was closed abnormally, e.g., without sending or receiving a Close control frame";
    else if (code === 1007)
        reason = "An endpoint is terminating the connection because it has received data within a message that was not consistent with the type of the message (e.g., non-UTF-8 [https://www.rfc-editor.org/rfc/rfc3629] data within a text message).";
    else if (code === 1008)
        reason = "An endpoint is terminating the connection because it has received a message that \"violates its policy\". This reason is given either if there is no other sutible reason, or if there is a need to hide specific details about the policy.";
    else if (code === 1009)
        reason = "An endpoint is terminating the connection because it has received a message that is too big for it to process.";
    else if (code === 1010)
        reason = "An endpoint (client) is terminating the connection because it has expected the server to negotiate one or more extension, but the server didn't return them in the response message of the WebSocket handshake. Specifically, the extensions that are needed are: " + event.reason;
    else if (code === 1011)
        reason = "A server is terminating the connection because it encountered an unexpected condition that prevented it from fulfilling the request.";
    else if (code === 1015)
        reason = "The connection was closed due to a failure to perform a TLS handshake (e.g., the server certificate can't be verified).";
    else
        reason = "Unknown reason";

    return reason;
}