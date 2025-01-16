<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ url_for('static', filename='public/css/styles.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Doogo - Add Product</title>
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
        <h2>Add Product</h2>
        <form action="{{ url_for('add_product') }}" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" class="form-control" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="discount">Discount</label>
                <input type="number" class="form-control" id="discount" name="discount" required>
            </div>
            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" required>
            </div>
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" class="form-control" id="image" name="image" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>