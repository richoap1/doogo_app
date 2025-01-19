<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="{{ url_for('static', filename='public/css/styles.css') }}">
        <link rel="stylesheet" href="{{ url_for('static', filename='public/css/chat.css') }}">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link rel="stylesheet" href="{{ url_for('static', filename='public/css/homepage.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <title>Doogo</title>
    </head>
<body>
    <header class="bg-custom text-white">
        <div class="container d-flex align-items-center justify-content-between py-2">
            <a href="{% if session.get('user_id') %}{{ url_for('homepage') }}{% else %}{{ url_for('index') }}{% endif %}" class="logo-link">
                <img class="logo" src="{{ url_for('static', filename='public/images/logo.png') }}" alt="Logo" />
            </a>                       
            <form action="/search" method="GET" class="search-form">
                <input class="form-control" type="search" id="search-input" name="query" placeholder="Search" aria-label="Search" required>
                <button class="btn" type="submit">Search</button>
                <div id="suggestions" class="suggestions-dropdown"></div>
            </form>                                              
            <div class="ml-3 d-flex align-items-center">
                {% if session.get('user_id') %}
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ session.get('name') }}  <!-- Display the user's name -->
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="/profile">Profile</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/logout">Logout</a>
                    </div>
                </div>
                {% else %}
                    <div class="nav-item">
                        <a class="btn btn-dark" href="/register">Sign up</a>
                    </div>
                    <div class="nav-item">
                        <a class="btn btn-dark" href="/login">Sign In</a>
                    </div>
                {% endif %}                
                <div class="language-selector ml-3 d-flex align-items-center">
                    <a class="nav-link Language-ID" href="#ID">
                        <span class="iconify" data-icon="flagpack:id" style="width: 24px; height: 24px; margin-right: 5px;"></span>
                        ID
                    </a>
                    <a class="nav-link Language-EN" href="#EN">
                        <span class="iconify" data-icon="flagpack:gb-nir" style="width: 24px; height: 24px; margin-right: 5px;"></span>
                        EN
                    </a>
                </div>
            </div>
        </div>           
    </header>
    
    <!-- Navigation Links -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mt-3">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link text-dark" href="#about-section">About Us</a></li>
                <li class="nav-item"><a class="nav-link text-dark" href="/products">Shopping</a></li>
                <li class="nav-item"><a class="nav-link text-dark" href="/bantuan">Bantuan</a></li>
                <li class="nav-item"><a class="nav-link text-dark" href="#">Blog</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">  
    <h2 class="text-center">User Massages</h2>  

    <form method="POST" class="mb-3" action="{{ url_for('admin_chat') }}">  
        <div class="form-group">  
            <label for="userSelect">Select User:</label>  
            <select class="form-control" id="userSelect" name="user_id" onchange="this.form.submit()">  
                <option value="">-- Select User --</option>  
                {% for user in users %}  
                    <option value="{{ user.id }}" {% if user.id == selected_user_id %}selected{% endif %}>{{ user.email }} ({{ user.role }})</option>  
                {% endfor %}  
            </select>  
        </div>  
    </form>  

    <table class="table" id="messagesTable">  
        <thead>  
            <tr>  
                <th>Email</th>  
                <th>Message</th>  
                <th>Response</th>  
                <th>Action</th>  
            </tr>  
        </thead>  
        <tbody id="messagesList">  
            {% for message in messages %}  
            <tr id="message-{{ message.id }}">  
                <td>{{ message.email }}</td>  
                <td>{{ message.content }}</td>  
                <td>{{ message.response }}</td>  
                <td>  
                    <form action="{{ url_for('reply_chat', message_id=message.id) }}" method="POST">  
                        <input type="text" name="response" placeholder="Type your response" required>  
                        <button type="submit" class="btn btn-success">Reply</button>  
                    </form>  
                </td>  
            </tr>  
            {% endfor %}  
        </tbody>  
    </table>  
</div>  

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.js"></script>
<script src="https://code.iconify.design/1/1.0.0/iconify.min.js"></script>
<script>
    AOS.init();

    function fetchMessages() {
        $.getJSON('/fetch_all_messages', function(data) {
            const messagesList = $('#messagesList');
            messagesList.empty();  // Clear the current messages

            // Append new messages
            data.messages.forEach(function(message) {
                messagesList.append(
                    `<tr id="message-${message.id}">
                        <td>${message.email}</td>
                        <td>${message.content}</td>
                        <td>${message.response ? message.response : 'No response yet.'}</td>
                        <td>
                            <form action="/reply_chat/${message.id}" method="POST">
                                <input type="text" name="response" placeholder="Type your response" required>
                                <button type="submit" class="btn btn-success">Reply</button>
                            </form>
                        </td>
                    </tr>`
                );
            });
        });
    }

    // Fetch messages every 5 seconds
    setInterval(fetchMessages, 5000);
</script>
</body>
</html>
