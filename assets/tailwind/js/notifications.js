const key = document.getElementById('key');
const notify = document.getElementById('notify');

if (key !== null) {
    const socket = new WebSocket("ws://localhost:8443");

    socket.addEventListener("open", (event) => {
        console.log("Notification opened. Event: " + event.type);
        setInterval(() => {
            socket.send(JSON.stringify({
                hash: key.dataset.hash
            }));

            socket.addEventListener("message", (event) => {
                const data = JSON.parse(event.data);
                if(data.hash === key.dataset.hash) {
                    const keys = Object.keys(data.notify);
                    console.log(keys.length);
                    if(keys.length > 0) {
                        notify.classList.remove('bg-gray-500');
                        notify.classList.add('bg-red-500');
                    } else {
                        notify.classList.remove('bg-red-500');
                        notify.classList.add('bg-gray-500');
                    }
                    //console.log(data.notify);
                }
            });
        }, 10000);
    });
}