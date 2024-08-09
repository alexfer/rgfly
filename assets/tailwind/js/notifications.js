import {Base64} from "js-base64";

const key = document.getElementById('key');
const notify = document.getElementById('notify');

const isOpen = (ws) => {
    return ws.readyState === ws.OPEN;
}

if (key !== null) {
    const socket = new WebSocket("ws://localhost:8443");

    socket.addEventListener("open", (event) => {
        console.log("Notification opened. Event: " + event.type);
        setInterval(() => {
            if (!isOpen(socket)) return;
            socket.send(JSON.stringify({
                hash: key.dataset.hash
            }));

            socket.addEventListener("message", (event) => {
                const data = JSON.parse(event.data);

                if (data.hash === Base64.decode(key.dataset.hash)) {
                    const keys = Object.keys(data.notify);

                    if (keys.length > 0) {
                        notify.classList.remove('bg-gray-500');
                        notify.classList.add('bg-green-500');
                    } else {
                        notify.classList.remove('bg-green-500');
                        notify.classList.add('bg-gray-500');
                    }
                    const created = document.getElementById('notify-date');
                    const body = document.getElementById('notify-body');
                    const url = document.getElementById('notify-url');

                    if (keys.length > 0) {
                        body.innerText = data.notify.message;
                        const date = new Date(data.notify.createdAt.date);
                        created.innerText = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                        url.href = data.notify.url;
                        notify.classList.remove('sr-only');
                    }
                }
            });
        }, 10000);
    });
}