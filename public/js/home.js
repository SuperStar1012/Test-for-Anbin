
$(document).ready(function () {
    let Echo = new window.Echo({
        broadcaster: 'pusher',
        key: 'local',
        wsHost: window.location.hostname,
        wsPort: 6001,
        forceTLS: false,
        disableStats: true
    });
    Echo.channel('redis-channel')
        .listen('.RedisUpdated', (e) => {
            // Update your HTML based on the received data.
            console.log(e.data);
            // Example: If you want to update an element with ID 'content'
            document.getElementById('status').innerText = e.data.status;
        });
})