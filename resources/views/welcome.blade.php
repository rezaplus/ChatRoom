<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        #messages {
            min-height: 400px;
            max-height: 400px;
            overflow-y: auto;
        }

        .message {
            margin-bottom: 1rem;
        }
    </style>
</head>

<body data-bs-theme="dark">
    <div class="container">
        <h1 class="my-4">Chat Room</h1>

        <!-- Login Form -->
        <div id="login-form" class="mb-4">
            <h2>Login</h2>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" placeholder="Enter your email" value="rezahajizade22@gmail.com">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" placeholder="Enter your password" value="somethinghard">
            </div>
            <button class="btn btn-primary" onclick="login()">Login</button>
        </div>

        <!-- Chat Interface -->
        <div id="chat-interface" style="display: none;">
            <div id="messages" class="border p-3 mb-3"></div>
            <div class="input-group">
                <input type="text" id="message-input" class="form-control" placeholder="Type your message...">
                <button class="btn btn-primary" onclick="sendMessage()">Send</button>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- Pusher -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        let pusher;
        let channel;
        let authToken;
        let userId;

        function generateColor(userId = 0) {
            // Generate a hash from the userId
            const hash = Array.from(userId.toString())
                .reduce((hash, char) => {
                    hash = ((hash << 5) - hash) + char.charCodeAt(0);
                    return hash & hash;
                }, 0);

            // Normalize hash to a positive number
            const normalizedHash = Math.abs(hash);

            // Define a base color range
            const hueStep = 30; // Degree step for hue to ensure distinct colors
            const baseHue = (normalizedHash % 12) * hueStep; // Ensure hue is within 0-360
            const lightness = 50 + (normalizedHash % 30); // Lightness range between 50-80%
            const saturation = 50 + (normalizedHash % 30); // Saturation range between 50-80%

            // Generate color using HSL
            const color = `hsl(${baseHue}, ${saturation}%, ${lightness}%)`;
            return color;
        }


        function login() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!email || !password) {
                alert('Please enter both email and password');
                return;
            }

            fetch('{{ route("auth.login") }}', {  // Use route helper for the login endpoint
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.access_token) {
                    authToken = data.access_token;
                    userId = data.user.id;
                    initializeChat();
                } else {
                    alert('Invalid login credentials');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function initializeChat() {
            // Fetch chat room details and messages
            fetch('/api/chat-rooms/1', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${authToken}`
                }
            })
            .then(response => response.json())
            .then(data => {
                const chatRoom = data.chat_room;
                const messages = data.messages;

                // Display chat room name and description if needed
                document.querySelector('h1').textContent = `Chat Room: ${chatRoom.name}`;
                

                // wait for a sec to foreace the messages
                setTimeout(() => {
                    messages.forEach(displayMessage);
                }, 500);


                // Initialize Pusher
                Pusher.logToConsole = true;
                pusher = new Pusher('bfdfed52e2f375448e6c', {
                    cluster: 'eu',
                    authEndpoint: 'broadcasting/auth',
                    auth: {
                        headers: {
                            'X-CSRF-Token': "{{ csrf_token() }}",
                            'Authorization': `Bearer ${authToken}`
                        }
                    }
                });

                channel = pusher.subscribe('private-chat-room.1'); 
                channel.bind('MessageSent', function(data) {
                    displayMessage(data.message);
                });

                document.getElementById('login-form').style.display = 'none';
                document.getElementById('chat-interface').style.display = 'block';
            })
            .catch(error => console.error('Error:', error));
        }

        function sendMessage() {
            const messageInput = document.getElementById('message-input');
            const content = messageInput.value.trim();
            if (!content) return;

            const socketId = pusher.connection.socket_id;

            fetch('{{ route("messages.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}`,
                    'X-Socket-ID': socketId,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    chat_room_id: 1,  // Adjust as needed
                    content: content
                })
            })
            .then(response => response.json())
            .then(data => {
                messageInput.value = '';
                if (data.id) {
                    displayMessage(data);
                } else {
                    alert(data.message || 'Failed to send message');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                displayMessage('Message sending failed. Please try again.', 'fail');
            });
        }

        function displayMessage(message) {
            const messageID = message.id;
            const userId = message.user_id;
            const userName = message.user_name;
            const content = message.content;
            const messagesDiv = document.getElementById('messages');
            const messageElement = document.createElement('div');
            const routeUrl = "{{ route('messages.delete', ':id') }}".replace(':id', messageID);
            messageElement.classList.add('message');
            messageElement.style.color = generateColor(userId);
            messageElement.id = `message-${messageID}`;
            messageElement.innerHTML = `
                <strong>${userName}:</strong> ${content}
                <small>
                    <a href="${routeUrl}" onclick="event.preventDefault(); deleteMessage(${messageID})">Delete</a>
                </small>
                `;

            messagesDiv.appendChild(messageElement);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }


        function deleteMessage(messageID) {
            fetch(`/api/messages/${messageID}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${authToken}`
                }
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                if (status === 200) {
                    const messageElement = document.getElementById(`message-${messageID}`);
                    if (messageElement) {
                        messageElement.remove();
                    }
                } else {
                    const errorMessage = body.message || 'Failed to delete message';
                    alert(errorMessage);
                }
            })
            .catch(error => {
                alert('An error occurred while trying to delete the message.');
            });
        }



        // send a message when the user presses the Enter key
        document.getElementById('message-input').addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        });

    </script>
</body>

</html>