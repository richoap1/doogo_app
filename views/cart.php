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
    <link rel="stylesheet" href="{{ url_for('static', filename='public/css/cart.css') }}">
    <title>Doogo - Cart</title>
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
                        {% if store %}
                        <a class="dropdown-item" href="/seller_dashboard">Store</a>
                        {% else %}
                        <a class="dropdown-item" href="/register_vendor">Register Store</a>
                        {% endif %}
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
    </header>

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
        <div class="row">
            <!-- Section 1: Items to Checkout -->
            <div class="col-md-8">
                <h2>Items in Your Cart</h2>
                <div class="cart-items">
                    <!-- Loop through cart items -->
                    {% for item in products %}
                    <div class="cart-item d-flex justify-content-between align-items-center">
                        <div class="item-details">
                            <img src="{{ item.image_path }}" alt="{{ item.title }}" class="img-fluid" style="width: 100px;">
                            <h5>{{ item.title }}</h5>
                            <div class="d-flex align-items-center">
                                <button class="btn btn-secondary" onclick="updateQuantity({{ item.id }}, {{ item.quantity - 1 }})">-</button>
                                <input type="number" name="quantity" value="{{ item.quantity }}" min="1" class="form-control mx-2" style="width: 80px;" readonly>
                                <button class="btn btn-secondary" onclick="updateQuantity({{ item.id }}, {{ item.quantity + 1 }})">+</button>
                            </div>
                        </div>
                        <div class="item-price">
                            <p>Rp. {{ "{:,.2f}".format(item.price).replace(',', '.').replace('.', ',', 1) }}</p>
                            <p>Total: Rp. {{ "{:,.2f}".format(item.total).replace(',', '.').replace('.', ',', 1) }}</p>
                        </div>
                    </div>
                    {% endfor %}
                </div>
            </div>

            <!-- Section 3: Checkout Summary -->
                    <div class="col-md-4">
            <div class="order-summary-container">
                <h1 class="order-summary-title">Order Summary</h1>
                <div class="order-summary order-summary-custom">
                    <img src="{{ url_for('static', filename='public/images/logo.png') }}" alt="Checkout" class="img-fluid">
                    <h4>Total: Rp. {{ "{:,.2f}".format(grand_total).replace(',', '.').replace('.', ',', 1) }}</h4>
                    <h4>Total with Shipping: Rp. {{ "{:,.2f}".format(grand_total_plus_shipping).replace(',', '.').replace('.', ',', 1) }}</h4>
                    <h4>Total Quantity: {{ quantity_total }}</h4>
                    <a href="{{ url_for('checkout') }}" class="btn btn-primary btn-block">Proceed to Checkout</a>
                </div>
            </div>
        </div>

        <!-- Section 2: Recommended Products -->
        <div class="recommended-products mt-5">
            <h2>Recommended Products</h2>
            <div class="row">
                <!-- Loop through recommended products -->
                {% for product in recommended_products %}
                <div class="col-md-3">
                    <div class="recommended-product">
                        <img src="{{ product.image_url }}" alt="{{ product.name }}" class="img-fluid">
                        <h5>{{ product.name }}</h5>
                        <p>Rp. {{ "{:,.2f}".format(product.price).replace(',', '.').replace('.', ',', 1) }}</p>
                        <a href="{{ url_for('product', product_id=product.id) }}" class="btn btn-secondary btn-block">View Product</a>
                    </div>
                </div>
                {% endfor %}
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.js"></script>
    <script src="https://code.iconify.design/1/1.0.0/iconify.min.js"></script>
    <script src="{{ url_for('static', filename='js/script.js') }}"></script>
</body>
</html>