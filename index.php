<!DOCTYPE html>
<html>
<head>
  <title>Chat Room</title>
  <style>
    body {
      background-color: #202020; /* Dark gray background */
      color: #FFFFFF; /* White text */
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    #chat_box {
      width: 500px;
      height: 400px;
      border: 1px solid #707070; /* Thin border */
      overflow: auto;
      background-color: #303030; /* Darker gray chat box */
      border-radius: 15px; /* Rounded corners */
      box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2); /* Subtle drop shadow */
      padding: 20px;
      box-sizing: border-box;
      position: relative;
      overflow-y: scroll;
    }

    .chat_message {
      background-color: #404040; /* Even darker gray for messages */
      padding: 10px;
      border-radius: 5px; /* Slightly rounded corners on messages */
      margin-bottom: 10px;
      color: #FFFFFF; /* White text */
      position: relative;
      opacity: 0; /* Initially hide messages */
    }

    .chat_message.slide-in {
      animation: slide-in 0.5s ease forwards;
    }

    .chat_message.slide-in:last-child {
      animation-duration: 1s; /* Adjust duration for the last message */
    }

    .chat_message .username {
      font-weight: bold;
      color: #FF4081; /* Vibrant color for usernames */
    }

    @keyframes slide-in {
      from {
        transform: translateX(50px);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
  </style>
</head>
<body>
  <div id="chat_box">
    <!-- chat_message divs will be added here by the JavaScript -->
  </div>

  <input type="text" id="message" placeholder="Enter Message">
  <button onclick="sendMessage()">Send</button>

  <script>
    let username = "User" + Math.floor(Math.random() * 1000);
    let lastMessageCount = 0;

    function createMessageElement(username, message) {
      // Create a new message element with the appropriate styling
      let messageElement = document.createElement('div');
      messageElement.classList.add('chat_message');

      // Add the username and message text
      let usernameSpan = document.createElement('span');
      usernameSpan.classList.add('username');
      usernameSpan.textContent = username + ': ';
      messageElement.appendChild(usernameSpan);

      let messageText = document.createTextNode(message);
      messageElement.appendChild(messageText);

      return messageElement;
    }

    function sendMessage() {
      let msg = document.getElementById("message").value;
      fetch("process.php", {
        method: "POST",
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `username=${username}&message=${msg}`
      }).then(response => {
        if (response.ok) {
          // Clear the input field only after the server has responded
          document.getElementById("message").value = '';
        } else {
          console.error('Error:', response.status);
        }
      }).catch((error) => {
        console.error('Error:', error);
      });
    }

    function updateChatBox(messages) {
      let chatBox = document.getElementById("chat_box");
      chatBox.innerHTML = '';

      for (let i = 0; i < messages.length; i++) {
        let message = messages[i];
        let messageElement = createMessageElement(message.username, message.text);
        chatBox.appendChild(messageElement);
      }
    }

    function checkNewMessages() {
      fetch("process.php")
        .then(response => response.json())
        .then(data => {
          let messages = data.messages;

          if (messages.length > lastMessageCount) {
            let newMessages = messages.slice(lastMessageCount);
            updateChatBox(newMessages);

            let chatMessages = document.querySelectorAll('.chat_message');

            // Apply slide-in animation to new messages
            newMessages.forEach((message, index) => {
              chatMessages[lastMessageCount + index].classList.add('slide-in');
            });

            lastMessageCount = messages.length;
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }

    setInterval(checkNewMessages, 1000);
  </script>
</body>
</html>
