<!DOCTYPE html>

<head>
    <title>Pusher Test</title>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('bfdfed52e2f375448e6c', {
      cluster: 'eu'
    });

var channel = pusher.subscribe('private-chat-room.1'); // note the 'private-' prefix
channel.bind('MessageSent', function(data) {
alert(JSON.stringify(data));
});
    </script>
</head>

<body>
    <h1>Pusher Test</h1>
    <p>
        Try publishing an event to channel <code>chat-room.1</code>
        with event name <code>my-event</code>.
    </p>
</body>