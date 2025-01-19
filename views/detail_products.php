<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">  
    <link rel="stylesheet" href="{{ url_for('static', filename='public/css/detail_products.css') }}">  
    <title>Product Details</title>  
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
                <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                    {{ session.get('name') }}  <!-- Display the user's name -->  
                </a>  
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">  
                    <a class="dropdown-item" href="/profile">Profile</a>  
                    {% if store %}  
                    <a class="dropdown-item" href="/store">Store</a>  
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
    <div class="container">  
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">  
            <span class="navbar-toggler-icon"></span>  
        </button>  
        <div class="collapse navbar-collapse" id="navbarNav">  
            <ul class="navbar-nav">  
                <li class="nav-item"><a class="nav-link text-dark" href="/stores">Stores</a></li>  
                <li class="nav-item dropdown">  
                    <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                        Categories  
                    </a>  
                    <div class="dropdown-menu" aria-labelledby="categoriesDropdown">  
                        {% for category in categories %}  
                        <a class="dropdown-item" href="/category/{{ category['name'] }}">{{ category['name'] }}</a>  
                        {% endfor %}  
                    </div>  
                </li>  
            </ul>  
        </div>  
    </div>  
</nav>  

<!-- Product Details -->  
<div class="container mt-5">  
    <div class="row">  
        <!-- Main Image Area -->  
        <div class="col-md-6">  
            <div class="product-main-image">  
                <img src="{{ product['image_path'] }}" class="img-fluid" alt="{{ product['title'] }}">  
            </div>  
            <div class="product-thumbnails mt-3">  
                <div class="row">  
                    <div class="col-3">  
                        <img src="{{ product['image_path'] }}" class="img-fluid thumbnail" alt="{{ product['title'] }}">  
                    </div>  
                    <div class="col-3">  
                        <img src="{{ product['image_path'] }}" class="img-fluid thumbnail" alt="{{ product['title'] }}">  
                    </div>  
                    <div class="col-3">  
                        <img src="{{ product['image_path'] }}" class="img-fluid thumbnail" alt="{{ product['title'] }}">  
                    </div>  
                    <div class="col-3">  
                        <img src="{{ product['image_path'] }}" class="img-fluid thumbnail" alt="{{ product['title'] }}">  
                    </div>  
                </div>  
            </div>  
        </div>  
        
        <!-- Product Information -->  
        <div class="col-md-6">  
            <h2 class="product-title">{{ product['title'] }}</h2>  
            <p class="product-description">{{ product['description'] }}</p>  
            <div class="vendor-info d-flex align-items-center">  
                <img src="{{ product['store_logo'] }}" alt="{{ product['store_name'] }}" class="store-logo" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">  
                <p class="store-name">{{ product['store_name'] }}</p>  
            </div>  
            <div class="ratings mt-2">  
                <p><strong>Store Rating:</strong> <span class="text-warning">{{ product['store_rating'] }} <i class="fas fa-star"></i></span></p>  
                <p><strong>Item Rating:</strong> <span class="text-warning">{{ product['item_rating'] }} <i class="fas fa-star"></i></span></p>  
            </div>  
            <p class="estimated-time"><strong>Estimated Purchase Time:</strong> {{ product['estimated_purchase_time'] }}</p>  
            <p class="product-price"><strong>Price:</strong> {{ product['price'] }}</p>  
            <form action="/add_to_cart" method="POST" class="mt-3">  
                <input type="hidden" name="product_id" value="{{ product['id'] }}">  
                <input type="hidden" name="quantity" value="1">  
                <button type="submit" class="btn btn-success">Add to Cart</button>  
            </form>  
        </div>  
    </div>  
    
    <!-- User Reviews -->  
    <div class="row mt-5">  
        <div class="col-md-12">  
            <h3>User Reviews</h3>  
            <div class="reviews">  
                {% for review in reviews %}  
                <div class="review-item d-flex align-items-start mb-4">  
                    <img src="{{ review['user_profile_image'] }}" alt="{{ review['user_name'] }}" class="user-profile-image" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">  
                    <div class="review-content">  
                        <h5 class="user-name">{{ review['user_name'] }}</h5>  
                        <p class="review-text">{{ review['comment'] }}</p>  
                        <p class="review-rating"><strong>Rating:</strong> <span class="text-warning">{{ review['rating'] }} <i class="fas fa-star"></i></span></p>  
                    </div>  
                </div>  
                {% endfor %}  
            </div>  
            <h4 class="mt-4">Add Your Review</h4>  
            <form action="/add_review" method="POST">  
                <input type="hidden" name="product_id" value="{{ product['id'] }}">  
                <div class="form-group">  
                    <label for="user_name">Name</label>  
                    <input type="text" class="form-control" id="user_name" name="user_name" required>  
                </div>  
                <div class="form-group">  
                    <label for="rating">Rating</label>  
                    <select class="form-control" id="rating" name="rating" required>  
                        <option value="1">1</option>  
                        <option value="2">2</option>  
                        <option value="3">3</option>  
                        <option value="4">4</option>  
                        <option value="5">5</option>  
                    </select>  
                </div>  
                <div class="form-group">  
                    <label for="comment">Comment</label>  
                    <textarea class="form-control" id="comment" name="comment" rows="4" required></textarea>  
                </div>  
                <button type="submit" class="btn btn-primary">Submit Review</button>  
            </form>  
        </div>  
    </div>  
</div>  

<!-- Footer -->  
<footer class="bg-custom text-white">  
    <div class="container">  
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
  
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>  
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>  
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>  
<script src="https://code.iconify.design/1/1.0.0/iconify.min.js"></script>  
</body>  
</html>  
