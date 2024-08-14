const refreshInterval = 12000;
let interval;

const key = document.getElementById('key');
const notify = document.getElementById('notify');
const push = document.getElementById('push-notification');

if (key !== null || push !== null) {
    if(key.dataset.hash) {
        const client = new WebSocket("ws://localhost:8443");
        const persist = () => {
            client.onopen = () => {
                sendMessage(client, key.dataset.hash);
                interval = setInterval(() => {
                    sendMessage(client, key.dataset.hash);
                    client.onmessage = (event) => {
                        const data = JSON.parse(event.data);

                        if (push !== null && (data.hash === key.dataset.email || data.hash === push.dataset.email)) {
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

                            if (keys.length === 0) {
                                push.classList.add('sr-only');
                            }

                            if (keys.length > 0) {
                                body.innerText = data.notify.message.length > 25 ? data.notify.message.substr(0, 25) + '...' : data.notify.message;
                                from.innerText = data.notify.from;
                                const date = new Date(data.notify.createdAt.date);
                                created.innerText = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
                                url.href = data.notify.url;
                                push.classList.remove('sr-only');
                                push.classList.add('animate__animated', 'animate__bounceInUp', 'animate__delay-2s');
                            }
                        }
                    };
                }, refreshInterval);
            };
        }
        client.onclose = function () {
            setInterval(() => {
                persist();
                console.log('Reconnecting...');
            }, 8000);
            clearInterval(interval);
        };

        client.onerror = function (error) {
            console.error('Socket encountered error: ', error, 'Closing socket');
            client.close();
        };
        persist();
    }
}

const sendMessage = (client, key) => {
    client.send(JSON.stringify({
        hash: key
    }));
};