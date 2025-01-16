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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ url_for('static', filename='public/css/products.css') }}">
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
                <div class="nav-item">
                    <a href="/cart" class="btn btn-dark position-relative cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                        {% if session.get('cart') %}
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ session.get('cart')|length }}
                        </span>
                        {% endif %}
                    </a>
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
        <h2>Products</h2>

        <div class="product-container">
            {% if products %}
                {% for product in products %}
                    <div class="product-card">
                        <h3>{{ product.title }}</h3>
                        <p>{{ product.description }}</p>
                        <p>Harga: {{ format_price(product.price) }}</p> <!-- Format price -->
                        <p>Diskon: {{ format_discount(product.discount) }}%</p> <!-- Format discount -->
                        <p>Stock: {{ product.stock }}</p> <!-- Display stock -->
                        <img src="{{ product.image_path }}" alt="{{ product.title }}" />
                        <form action="{{ url_for('add_to_cart', product_id=product.id) }}" method="POST" onsubmit="showNotification(event)">
                            <input type="hidden" name="product_id" value="{{ product.id }}">
                            <input type="number" name="quantity" value="1" min="1" class="form-control mb-2" />
                            <button type="submit" class="btn btn-primary">Add to Cart</button>
                        </form>
                    </div>
                {% endfor %}
            {% else %}
                <p>No products found.</p>
            {% endif %}
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-custom text-white">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-menu">
                        <img src="{{ url_for('static', filename='public/images/logo.png') }}" width="200" height="200" alt="logo">
                        <div class="social-links mt-3">
                            <ul class="d-flex list-unstyled gap-2">
                                <li>
                                    <a href="#" class="btn btn-outline-light">
                                        <span class="iconify" data-icon="akar-icons:facebook-fill" style="width: 16px; height: 16px;"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="btn btn-outline-light">
                                        <span class="iconify" data-icon="akar-icons:twitter-fill" style="width: 16px; height: 16px;"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="btn btn-outline-light">
                                        <span class="iconify" data-icon="akar-icons:youtube-fill" style="width: 16px; height: 16px;"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="btn btn-outline-light">
                                        <span class="iconify" data-icon="akar-icons:instagram-fill" style="width: 16px; height: 16px;"></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="footer-menu">
                        <h5 class="widget-title">Organic</h5>
                        <ul class="menu-list list-unstyled">
                            <li class="menu-item"><a href="/about" class="nav-link">About us</a></li>
                            <li class="menu-item"><a href="#" class="nav-link">Conditions</a></li>
                            <li class="menu-item"><a href="#" class="nav-link">Our Journals</a></li>
                            <li class="menu-item"><a href="#" class="nav-link">Careers</a></li>
                            <li class="menu-item"><a href="/seller" class="nav-link">Login Seller</a></li>
                            <li class="menu-item"><a href="#" class="nav-link">Ultras Press</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="footer-menu">
                        <h5 class="widget-title">Quick Links</h5>
                        <ul class="menu-list list-unstyled">
                            <li class="menu-item"><a href="#" class="nav-link">Offers</a></li>
                            <li class="menu-item"><a href="#" class="nav-link">Discount Coupons</a></li>
                            <li class="menu-item"><a href="#" class="nav-link">Stores</a></li>
                            <li class="menu-item"><a href="#" class="nav-link">Track Order</a></li>
                            <li class="menu-item"><a href="#" class="nav-link">Shop</a></li>
                            <li class="menu-item"><a href="#" class="nav-link">Info</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="footer-menu">
                        <h5 class="widget-title">Customer Service</h5>
                        <ul class="menu-list list-unstyled">
                            <li class="menu-item"><a href="#" class="nav-link">FAQ</a></li>
                            <li class="menu-item"><a href="#" class="nav-link">Contact</a></li>
                            <li class="menu-item"><a href="#" class="nav-link">Privacy Policy</a></li>
                            <li class="menu-item"><a href="#" class="nav-link">Returns &amp; Refunds</a></li>
                            <li class="menu-item"><a href="#" class="nav-link">Cookie Guidelines</a></li>
                            <li class="menu-item"><a href="#" class="nav-link">Delivery Information</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-menu">
                        <h5 class="widget-title">Subscribe Us</h5>
                        <p>Subscribe to our newsletter to get updates about our grand offers.</p>
                        <form class="d-flex mt-3 gap-0" action="/">
                            <input class="form-control rounded-start rounded-0 bg-light" type="email" placeholder="Email Address" aria-label="Email Address">
                            <button class="btn btn-dark rounded-end rounded-0" type="submit">Subscribe</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center py-3">
            <p class="Copyright2023PtDoogoIndonesiaAllRightsReserved">Copyright @ 2023 PT. DOOGO INDONESIA. All Rights Reserved</p>
        </div>
    </footer>

    <div id="chat-popup" class="chat-popup" style="display: none;"> <!-- Initially hidden -->
        <div class="chat-header">
            <span>Customer Service</span>
            <button id="close-chat" class="close-chat">✖</button>
        </div>
        <div class="chat-body" id="chat-body">
            <!-- Messages will be dynamically added here -->
        </div>
        <div class="chat-input">
            <input type="text" id="user-input" placeholder="Type a message..." />
            <button id="send-button" class="send-button">Send</button>
        </div>
    </div>
    <button id="open-chat" class="open-chat">
        <i class="fas fa-comment-dots"></i> <!-- Font Awesome chat icon -->
    </button>

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