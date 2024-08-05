let socket = new WebSocket("ws://localhost:8443");

socket.addEventListener("open", (event) => {
    socket.send(JSON.stringify({
        message: event.data,
        email: 'alexandershtyher@gmail.com'
    }));
});
socket.addEventListener("message", (event) => {
    console.log(event.data);
});