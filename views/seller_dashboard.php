<!-- filepath: /C:/Users/richo/Documents/doogo-app/views/seller_dashboard.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard</title>
    <link rel="stylesheet" href="{{ url_for('static', filename='public/css/styles.css') }}">
    <link rel="stylesheet" href="{{ url_for('static', filename='public/css/seller_dashboard.css') }}">
    <link rel="stylesheet" href="{{ url_for('static', filename='public/css/chat.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ url_for('static', filename='public/css/homepage.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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

    <div class="container mt-5 modern-container">
        <h2>Seller Dashboard</h2>
        {% if store %}
        <div class="store-info mb-4">
            <img src="{{ store['image_path'] }}" alt="{{ store['name'] }}" class="img-fluid" style="max-width: 200px;">
            <h3>{{ store['name'] }}</h3>
            <p>{{ store['description'] }}</p>
            <p><strong>Location:</strong> {{ store['location'] }}</p>
            <p><strong>Operational Hours:</strong> {{ store['operational_hours'] }}</p>
        </div>
        {% else %}
        <p>You do not have a store yet. <a href="/register_vendor">Register your store</a>.</p>
        {% endif %}

        <h3>Your Products</h3>
        <div class="product-container">
            {% if products %}
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {% for product in products %}
                            <tr>
                                <td>{{ product.title }}</td>
                                <td>{{ product.description }}</td>
                                <td>{{ product.price }}</td>
                                <td>{{ product.stock }}</td>
                                <td><img src="{{ product.image_path }}" alt="{{ product.title }}" style="width: 50px; height: auto;"></td>
                                <td>
                                    <a href="{{ url_for('edit_product', product_id=product.id) }}" class="btn btn-primary">Edit</a>
                                    <form action="{{ url_for('delete_product', product_id=product.id) }}" method="POST" class="d-inline">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        {% endfor %}
                    </tbody>
                </table>
            {% else %}
                <p>No products found.</p>
            {% endif %}
        </div>
        <a href="/seller" class="btn btn-primary mb-3">Add New Product</a>

        <h3>Your Orders</h3>  
        <div class="order-container">  
            <table class="table">  
                <thead>  
                    <tr>  
                        <th>Order ID</th>  
                        <th>Customer Name</th>  
                        <th>Status</th>  
                        <th>Actions</th>  
                    </tr>  
                </thead>  
                <tbody>  
                    {% for order in orders %}  
                        <tr>  
                            <td>{{ order.id }}</td>  
                            <td>{{ order.first_name }} {{ order.last_name }}</td>  
                            <td>{{ order.status }}</td>  
                            <td>  
                                <form action="{{ url_for('update_order_status', order_id=order.id) }}" method="POST">  
                                    <select name="status">  
                                        <option value="PENDING" {% if order.status == 'PENDING' %}selected{% endif %}>Pending</option>  
                                        <option value="SHIPPED" {% if order.status == 'SHIPPED' %}selected{% endif %}>Shipped</option>  
                                        <option value="DELIVERED" {% if order.status == 'DELIVERED' %}selected{% endif %}>Delivered</option>  
                                        <option value="CANCELLED" {% if order.status == 'CANCELLED' %}selected{% endif %}>Cancelled</option>  
                                    </select>  
                                    <button type="submit" class="btn btn-primary">Update Status</button>  
                                </form>  
                            </td>  
                        </tr>  
                    {% endfor %}  
                </tbody>  
            </table>  
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.js"></script>
    <script src="https://code.iconify.design/1/1.0.0/iconify.min.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="{{ url_for('static', filename='js/chat.js') }}"></script>
    <script src="{{ url_for('static', filename='js/script.js') }}"></script>
</body>
</html>