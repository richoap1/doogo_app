<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ url_for('static', filename='public/css/styles.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Doogo - Checkout</title>
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
            </div>
        </div>
    </header>

    <div class="container mt-5">
        <h2>Checkout</h2>
        <form action="{{ url_for('checkout') }}" method="POST">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Nomor HP</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="address">Alamat</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="form-group">
                <label for="city">Kota</label>
                <input type="text" class="form-control" id="city" name="city" required>
            </div>
            <div class="form-group">
                <label for="state">Wilayah</label>
                <input type="text" class="form-control" id="state" name="state" required>
            </div>
            <div class="form-group">
                <label for="country">Negara</label>
                <input type="text" class="form-control" id="country" name="country" required>
            </div>
            <div class="form-group">
                <label for="payment_type">Payment Type</label>
                <select class="form-control" id="payment_type" name="payment_type" required>
                    <option value="Credit Card">Credit Card</option>
                    <option value="QR Code">Qris</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Place Order</button>
        </form>

        <h3 class="mt-5">Order Summary</h3>
        <div class="order-summary">
            <h4>Total: Rp. {{ "{:,.2f}".format(grand_total).replace(',', '.').replace('.', ',', 1) }}</h4>
            <h4>Total with Shipping: Rp. {{ "{:,.2f}".format(grand_total_plus_shipping).replace(',', '.').replace('.', ',', 1) }}</h4>
            <h4>Total Quantity: {{ quantity_total }}</h4>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>