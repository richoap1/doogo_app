from flask import Flask, render_template, request, redirect, url_for, session, flash, abort, jsonify  
from flask_mail import Mail, Message  
from werkzeug.security import check_password_hash, generate_password_hash  
from itsdangerous import URLSafeTimedSerializer  
import sqlite3  
import os  
import re  
from flask_dance.contrib.google import make_google_blueprint, google  
from flask_dance.contrib.facebook import make_facebook_blueprint, facebook  
from flask_session import Session  
import logging  
from functools import wraps
from datetime import datetime
import qrcode  # Import qrcode library
import io
import base64
import locale

# Set locale to Indonesian
locale.setlocale(locale.LC_ALL, 'id_ID.UTF-8')

# Configure logging  
logging.basicConfig(level=logging.DEBUG)  

# Flask app configuration  
app = Flask(__name__, static_url_path='/static', template_folder='views')  
app.secret_key = os.environ.get('SECRET_KEY', '210402')  # Set a secret key for session management  

# Configure Flask-Mail    
app.config['MAIL_SERVER'] = 'smtp.gmail.com'    
app.config['MAIL_PORT'] = 587    
app.config['MAIL_USE_TLS'] = True    
app.config['MAIL_USERNAME'] = os.environ.get('MAIL_USERNAME', 'doogo6243@gmail.com')  # Use environment variable for email    
app.config['MAIL_PASSWORD'] = os.environ.get('MAIL_PASSWORD', 'wgrb hssy whmd xgwx')  # Use App Password here    
app.config['MAIL_DEFAULT_SENDER'] = app.config['MAIL_USERNAME']  # Default sender   
app.config['MAIL_USE_SSL'] = False   

mail = Mail(app)  
s = URLSafeTimedSerializer(app.config['SECRET_KEY'])  

# Google OAuth  
google_bp = make_google_blueprint(  
    client_id='44819535733-nppfepc7f3fmu19vmj610p9iqk271osu.apps.googleusercontent.com',  
    client_secret='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',  
    redirect_to='google_login'  
)  
app.register_blueprint(google_bp, url_prefix='/google_login')  

# Facebook OAuth  
facebook_bp = make_facebook_blueprint(  
    client_id='899953522255020',  
    client_secret='f6aa97caef24884d9054f418dbef766c',  
    redirect_to='facebook_login'  
)  
app.register_blueprint(facebook_bp, url_prefix='/facebook_login')  

# Define allowed IPs for admin access  
# ALLOWED_ADMIN_IPS = ['127.0.0.1']  # Add your admin IPs here

# Database connection function  
def get_db_connection():    
    conn = sqlite3.connect('your_database.db', timeout=10)  # Increase timeout if necessary    
    conn.row_factory = sqlite3.Row    
    return conn    

def login_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if 'user_id' not in session:
            return redirect(url_for('login'))
        return f(*args, **kwargs)
    return decorated_function

def is_valid_email(email):  
    """Check if the email format is valid."""  
    email_regex = r'^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$'  
    return re.match(email_regex, email) is not None  

def create_reviews_table():  
    conn = get_db_connection()  
    conn.execute('''  
        CREATE TABLE IF NOT EXISTS reviews (  
            id INTEGER PRIMARY KEY AUTOINCREMENT,  
            product_id INTEGER NOT NULL,  
            user_id INTEGER NOT NULL,  
            rating INTEGER NOT NULL,  
            comment TEXT,  
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
            FOREIGN KEY (product_id) REFERENCES products (id),  
            FOREIGN KEY (user_id) REFERENCES users (id)  
        )  
    ''')  
    conn.commit()  
    conn.close()  


def send_registration_email(user_email, user_name):    
    """Send a welcome email to the user after registration."""    
    msg = Message("Welcome to Our Service!", recipients=[user_email])    
    msg.body = f"Hi {user_name},\n\nThank you for registering with us! We're excited to have you on board.\n\nBest regards,\nDOOGO_ID"    
    mail.send(msg)  

# Function to send promotional email  
def send_promotional_email(user_email):  
    msg = Message("Exclusive Promotion Just for You!", recipients=[user_email])  
    msg.body = "Hello!\n\nWe have an exciting promotion just for you! Enjoy 20% off your first purchase.\n\nUse code: PROMO20 at checkout.\n\nBest regards,\nDOOGO_ID"  
    mail.send(msg)
    
def send_receipt_email(user_email, receipt_content):
    msg = Message('Your Order Receipt', recipients=[user_email])
    msg.html = receipt_content
    mail.send(msg)

# Function to create the users table  
def create_users_table():  
    conn = get_db_connection()  
    conn.execute('''  
        CREATE TABLE IF NOT EXISTS users (  
            id INTEGER PRIMARY KEY AUTOINCREMENT,  
            email TEXT NOT NULL UNIQUE,  
            password TEXT NOT NULL,  
            name TEXT NOT NULL,   
            address TEXT NOT NULL,   
            gender TEXT NOT NULL,    
            role TEXT DEFAULT 'user'    
        )  
    ''')  
    conn.commit()  
    conn.close()  

# Function to create the products table  
def create_products_table():  
    conn = get_db_connection()  
    conn.execute('''  
        CREATE TABLE IF NOT EXISTS products (  
            id INTEGER PRIMARY KEY AUTOINCREMENT,  
            title TEXT NOT NULL,  
            description TEXT,  
            price REAL NOT NULL,
            image_path TEXT,  
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  
        )  
    ''')  
    conn.commit()  
    conn.close()  
    
def drop_messages_table():  
    with get_db_connection() as conn:  
        conn.execute("DROP TABLE IF EXISTS messages") 

def create_messages_table():  
    with get_db_connection() as conn:  
        conn.execute("""  
            CREATE TABLE IF NOT EXISTS messages (  
                id INTEGER PRIMARY KEY AUTOINCREMENT,  
                email TEXT NOT NULL,  
                content TEXT NOT NULL,  
                response TEXT,  
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  
                FOREIGN KEY (email) REFERENCES users(email)  
            )  
        """)
    conn.commit()  
    conn.close()
    
def test_db_connection():  
    try:  
        with get_db_connection() as conn:  
            result = conn.execute("SELECT * FROM messages").fetchall()  
            print("Database connection successful. Messages:", result)  
    except Exception as e:  
        print("Database connection failed:", e)  

# Call this function once to test the connection  
test_db_connection()  


    # Function to create the store_products table  
def create_store_products_table():  
    conn = get_db_connection()  
    conn.execute('''  
        CREATE TABLE IF NOT EXISTS store_products (  
            store_id INTEGER NOT NULL,  
            product_id INTEGER NOT NULL,  
            PRIMARY KEY(store_id, product_id),  
            FOREIGN KEY(store_id) REFERENCES stores(id) ON DELETE CASCADE ON UPDATE CASCADE,  
            FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE  
        )  
    ''')  
    conn.commit()  
    conn.close()

# # Decorator to restrict admin access based on IP  
# def restrict_admin_access(f):  
#     def wrapper(*args, **kwargs):  
#         if request.endpoint in ['admin_chat', 'admin_login', 'admin_page']:  
#             if 'user_id' not in session or session.get('role') != 'admin':  
#                 flash("You are not authorized to access this page.")  
#                 return redirect(url_for('login'))  # Redirect to login if not authorized  
            
#             if request.remote_addr not in ALLOWED_ADMIN_IPS:  
#                 abort(403)  # Forbidden access  
#         return f(*args, **kwargs)  
#     return wrapper

def handle_cart():
    products = []
    grand_total = 0
    quantity_total = 0

    for item in session.get('cart', []):
        with get_db_connection() as conn:
            product = conn.execute("SELECT * FROM products WHERE id = ?", (item['id'],)).fetchone()
        
        quantity = int(item['quantity'])
        total = quantity * product['price']
        grand_total += total
        quantity_total += quantity

        products.append({
            'id': product['id'],
            'title': product['title'],
            'price': product['price'],
            'image_path': product['image_path'],
            'quantity': quantity,
            'total': total
        })

    grand_total_plus_shipping = grand_total + 1000  # Example shipping cost

    return products, grand_total, grand_total_plus_shipping, quantity_total

def add_stock_column():
    conn = sqlite3.connect('your_database.db')
    cursor = conn.cursor()

    # Add stock column to products table
    cursor.execute('''
    ALTER TABLE products ADD COLUMN stock INTEGER DEFAULT 0
    ''')

    conn.commit()
    conn.close()
    
def product_detail(product_id):      
    product = get_product_details(product_id)      
    print(f"Fetching product details for ID: {product_id}")  # Debugging line  
    print(f"Product details: {product}")  # Debugging line  
    if not product:      
        flash("Product not found.")      
        return redirect(url_for('products'))      

def get_product_details(product_id):    
    conn = get_db_connection()    
    product = conn.execute('''    
        SELECT p.*, s.name as store_name, s.image_path as store_logo, s.rating as store_rating    
        FROM products p    
        JOIN stores s ON p.store_id = s.id    
        WHERE p.id = ?    
    ''', (product_id,)).fetchone()    
    conn.close()    
    print(f"Product details for ID {product_id}: {product}")  # Debugging line  
    return product    

def get_reviews(product_id):    
    conn = get_db_connection()    
    reviews = conn.execute('''    
        SELECT r.*, u.name as user_name, u.image_path as user_profile_image    
        FROM reviews r    
        JOIN users u ON r.user_id = u.id    
        WHERE r.product_id = ?    
    ''', (product_id,)).fetchall()    
    conn.close()    
    print(f"Reviews for product ID {product_id}: {reviews}")  # Debugging line  
    return reviews   

    
# @app.before_request  
# @restrict_admin_access  
# def before_request():  
#     pass  # This will run before every request  

# # Helper functions for formatting  
# def format_price(price):  
#     """Format the price to include 'Rp' and use thousands separators."""  
#     return f"Rp{int(price):,}".replace(',', '.')  

# # Register helper functions in Jinja2  
# app.jinja_env.globals.update(format_price=format_price)

@app.route('/')  
def index():  
    return render_template('index.ejs')  

@app.route('/about')  
def about():  
    return render_template('about.ejs')  

@app.route('/homepage')
def homepage():
    user_id = session.get('user_id')
    if not user_id:
        return redirect(url_for('login'))

    conn = get_db_connection()
    store = conn.execute('SELECT * FROM stores WHERE owner_id = ?', (user_id,)).fetchone()
    conn.close()
    return render_template('homepage.ejs', store=store)

@app.route('/layanan')  
def layanan():  
    return render_template('layanan.ejs')  

@app.route('/products', methods=['GET', 'POST'])  
def products():  
    user_id = session.get('user_id')  
    conn = get_db_connection()  

    if request.method == 'POST':  
        product_id = request.form.get('product_id')  
        quantity = request.form.get('quantity', '1')  # Default to '1' if not specified  

        # Convert product_id and quantity to integers  
        try:  
            product_id = int(product_id)  
            quantity = int(quantity)  
        except ValueError:  
            flash("Invalid product ID or quantity.")  
            return redirect(url_for('products'))  

        # Initialize cart in session if it doesn't exist  
        if 'cart' not in session:  
            session['cart'] = {}  

    # Add product to cart  
        if product_id in session['cart']:  
            session['cart'][product_id] += quantity  # Increment quantity if already in cart  
        else:  
            session['cart'][product_id] = quantity  # Add new product to cart  

        session.modified = True  # Mark session as modified  
        flash("Product added to cart!")  # Flash message for user feedback  

        return redirect(url_for('products'))  

    # Handle GET request to display products  
    products = conn.execute("SELECT * FROM products").fetchall()  
    store = conn.execute('SELECT * FROM stores WHERE owner_id = ?', (user_id,)).fetchone()  

    # Format prices  
    formatted_products = []  
    for product in products:  
        formatted_product = dict(product)  
        formatted_product['price'] = locale.currency(product['price'], grouping=True)  
        formatted_products.append(formatted_product)  

    conn.close()  

    return render_template('products.php', products=formatted_products, store=store)  # Ensure this points to your products template  

@app.route('/product/<int:product_id>', methods=['GET', 'POST'])  
def product_detail(product_id):  
    product = get_product_details(product_id)  
    if not product:  
        flash("Product not found.")  
        return redirect(url_for('products'))  

    reviews = get_reviews(product_id)  

    # Format price  
    product['price'] = locale.currency(product['price'], grouping=True)  

    if request.method == 'POST':  
        user_id = session.get('user_id')  
        if not user_id:  
            flash("You need to be logged in to add a review.")  
            return redirect(url_for('login'))  

        conn = get_db_connection()  
        user = conn.execute('SELECT name FROM users WHERE id = ?', (user_id,)).fetchone()  
        user_name = user['name'] if user else 'Anonymous'  

        rating = request.form.get('rating')  
        comment = request.form.get('comment')  
        store = conn.execute('SELECT * FROM stores WHERE owner_id = ?', (user_id,)).fetchone()  

        # Add review to database  
        conn.execute('''  
            INSERT INTO reviews (product_id, user_id, user_name, rating, comment)  
            VALUES (?, ?, ?, ?, ?)  
        ''', (product_id, user_id, user_name, rating, comment))  
        conn.commit()  
        conn.close()  

        flash("Review added successfully!")  
        return redirect(url_for('product_detail', product_id=product_id))  

    return render_template('detail_products.php', product=product, reviews=reviews, store=store) 
@app.route('/cart')
def cart():
    user_id = session.get('user_id')  
    conn = get_db_connection()
    products, grand_total, grand_total_plus_shipping, quantity_total = handle_cart()
    store = conn.execute('SELECT * FROM stores WHERE owner_id = ?', (user_id,)).fetchone()
    return render_template('cart.php', products=products, grand_total=grand_total, grand_total_plus_shipping=grand_total_plus_shipping, quantity_total=quantity_total, store=store)

@app.route('/add_to_cart/<int:product_id>', methods=['POST'])
def add_to_cart(product_id):
    if 'cart' not in session:
        session['cart'] = []

    quantity = int(request.form.get('quantity', 1))

    # Check if the product is already in the cart
    for item in session['cart']:
        if item['id'] == product_id:
            item['quantity'] += quantity
            break
    else:
        session['cart'].append({'id': product_id, 'quantity': quantity})

    session.modified = True

    flash('Product added to cart!')
    return redirect(url_for('cart'))

@app.route('/update_cart/<int:product_id>', methods=['POST'])
def update_cart(product_id):
    cart = session.get('cart', [])
    data = request.get_json()
    quantity = data.get('quantity', 1)

    try:
        quantity = int(quantity)
    except ValueError:
        quantity = 1

    if quantity < 1:
        cart = [item for item in cart if item['id'] != product_id]
        flash('Product removed from cart!')
    else:
        for item in cart:
            if item['id'] == product_id:
                item['quantity'] = quantity
                flash('Product quantity updated!')
                break

    session['cart'] = cart
    session.modified = True
    return jsonify(success=True)

@app.route('/remove_from_cart/<int:product_id>', methods=['POST'])
def remove_from_cart(product_id):
    cart = session.get('cart', [])
    cart = [item for item in cart if item['id'] != product_id]
    session['cart'] = cart
    session.modified = True
    flash('Product removed from cart!')
    return jsonify(success=True)

@app.route('/checkout', methods=['GET', 'POST'])    
@login_required    
def checkout():    
    products, grand_total, grand_total_plus_shipping, quantity_total = handle_cart()    

    if request.method == 'POST':    
        first_name = request.form['first_name']    
        last_name = request.form['last_name']    
        phone_number = request.form['phone_number']    
        email = request.form['email']    
        address = request.form['address']    
        city = request.form['city']    
        state = request.form['state']    
        country = request.form['country']    
        payment_type = request.form['payment_type']    

        with get_db_connection() as conn:    
            # Insert the order  
            conn.execute("INSERT INTO orders (first_name, last_name, phone_number, email, address, city, state, country, payment_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",    
                        (first_name, last_name, phone_number, email, address, city, state, country, payment_type, 'PENDING'))    
            order_id = conn.execute("SELECT last_insert_rowid()").fetchone()[0]    

            for product in products:    
                # Insert order items  
                conn.execute("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)",    
                            (order_id, product['id'], product['quantity']))    
                # Update product stock  
                conn.execute("UPDATE products SET stock = stock - ? WHERE id = ?",    
                            (product['quantity'], product['id']))    

                # Create a notification for the seller  
                seller_id = conn.execute("SELECT store_id FROM products WHERE id = ?", (product['id'],)).fetchone()['store_id']  
                conn.execute("INSERT INTO order_notifications (order_id, seller_id, message) VALUES (?, ?, ?)",   
                            (order_id, seller_id, f"Your product '{product['title']}' has been ordered."))  

            conn.commit()    

        qr_code_image = None  
        if payment_type == 'QR Code':  
            # Generate QR code  
            qr_data = f"Order ID: {order_id}\nTotal Amount: Rp{grand_total_plus_shipping}\nPayment Type: {payment_type}"  
            qr = qrcode.QRCode(  
                version=1,  
                error_correction=qrcode.constants.ERROR_CORRECT_L,  
                box_size=10,  
                border=4,  
            )  
            qr.add_data(qr_data)  
            qr.make(fit=True)  
            img = qr.make_image(fill='black', back_color='white')  

            # Save QR code to a bytes buffer  
            buffer = io.BytesIO()  
            img.save(buffer, format="PNG")  
            qr_code_image = base64.b64encode(buffer.getvalue()).decode('utf-8')  

        # Generate receipt content  
        receipt_content = render_template('receipt.ejs', first_name=first_name, last_name=last_name, order_id=order_id, transaction_date=datetime.now().strftime('%d %b %Y %H:%M'), phone_number=phone_number, products=products, grand_total_plus_shipping=grand_total_plus_shipping, address=address, city=city, state=state, country=country, payment_type=payment_type, qr_code_image=qr_code_image)  

        # Send receipt email  
        send_receipt_email(email, receipt_content)  

        session['cart'] = []    
        session.modified = True    

        flash('Order placed successfully! A receipt has been sent to your email.', 'success')    
        return redirect(url_for('products'))    

    return render_template('checkout.php', products=products, grand_total=grand_total, grand_total_plus_shipping=grand_total_plus_shipping, quantity_total=quantity_total)    

@app.route('/notifications')    
@login_required    
def notifications():    
    user_id = session.get('user_id')    
    if not user_id:    
        return redirect(url_for('login'))    

    with get_db_connection() as conn:    
        # Fetch notifications for the logged-in seller  
        notifications = conn.execute("SELECT * FROM order_notifications WHERE seller_id = ? ORDER BY created_at DESC", (user_id,)).fetchall()    

    return render_template('notifications.php', notifications=notifications)    

@app.route('/update_order_status/<int:order_id>', methods=['POST'])
@login_required
def update_order_status(order_id):
    user_id = session.get('user_id')
    if not user_id:
        return redirect(url_for('login'))

    new_status = request.form['status']  # Get the new status from the form

    with get_db_connection() as conn:
        # Check if the user is the seller of the products in the order
        products = conn.execute("SELECT product_id FROM order_items WHERE order_id = ?", (order_id,)).fetchall()
        product_ids = [product['product_id'] for product in products]

        # Check if the seller owns any of the products
        store = conn.execute("SELECT id FROM stores WHERE owner_id = ?", (user_id,)).fetchone()
        if store:
            store_id = store['id']
            product_store_ids = conn.execute("SELECT DISTINCT store_id FROM products WHERE id IN ({})".format(','.join('?' * len(product_ids))), product_ids).fetchall()
            if any(ps['store_id'] == store_id for ps in product_store_ids):
                # Update the order status if the seller owns the products
                conn.execute("UPDATE orders SET status = ? WHERE id = ?", (new_status, order_id))
                conn.commit()
                flash("Order status updated successfully!")
            else:
                flash("You are not authorized to update this order status.")
        else:
            flash("Store not found.")

    return redirect(url_for('seller_dashboard'))

# Register route  
@app.route('/register', methods=['GET', 'POST'])    
def register():    
    if request.method == 'POST':    
        email = request.form['username']  # Email    
        password = generate_password_hash(request.form['password'])  # Password    
        name = request.form['name']  # Name    
        address = request.form['address']  # Address    
        gender = request.form['gender']  # Gender    

        try:    
            with get_db_connection() as conn:    
                # Check if the email already exists    
                cursor = conn.execute("SELECT * FROM users WHERE email = ?", (email,))    
                existing_user = cursor.fetchone()    

                if existing_user:    
                    flash("Email already exists. Please choose a different one.")    
                    return render_template('register.php')    

                # Insert user into the database    
                conn.execute("INSERT INTO users (email, password, name, address, gender) VALUES (?, ?, ?, ?, ?)",     
                            (email, password, name, address, gender))    
                conn.commit()       

            # Send registration email    
            send_registration_email(email, name)    

            flash("Registration successful! Please log in.")    
            return redirect(url_for('login'))    

        except sqlite3.OperationalError as e:    
            flash(f"An error occurred during registration: {str(e)}")    
            return render_template('register.php')    

    return render_template('register.php')    

@app.route('/login', methods=['GET', 'POST'])    
def login():    
    if request.method == 'POST':    
        email = request.form['username']  # Change from username to email    
        password = request.form['password']    

        # Check if the user exists    
        with get_db_connection() as conn:    
            user = conn.execute("SELECT * FROM users WHERE email = ?", (email,)).fetchone()    

        if user and check_password_hash(user['password'], password):    
            # Store user information in session    
            session['user_id'] = user['id']    
            session['name'] = user['name']  # Store the user's name    
            session['email'] = user['email']  # Store the user's email for later use  
            session['role'] = user['role']  # Store the user's role    

            # Fetch and store the store name if the user has a store  
            store = conn.execute('SELECT * FROM stores WHERE owner_id = ?', (user['id'],)).fetchone()  
            if store:  
                session['store_name'] = store['name']  

            flash("Login successful!")    
            return redirect(url_for('homepage'))  # Redirect to the homepage after successful login    
        else:    
            flash("Invalid email or password.")    
            return redirect(url_for('login'))    

    return render_template('login.php')  

@app.route('/forgot_password', methods=['GET', 'POST'])  
def forgot_password():  
    if request.method == 'POST':  
        email = request.form['email']  
        token = s.dumps(email, salt='password-reset-salt')  
        link = url_for('reset_password', token=token, _external=True)  
        msg = Message('Password Reset Request', sender='noreply@example.com', recipients=[email])  
        msg.body = f'Your link to reset your password is {link}'  
        mail.send(msg)  
        flash('A password reset link has been sent to your email.', 'info')  
        return redirect(url_for('login'))  
    return render_template('forgot_password.php')  

@app.route('/reset_password/<token>', methods=['GET', 'POST'])  
def reset_password(token):  
    try:  
        email = s.loads(token, salt='password-reset-salt', max_age=3600)  
    except:  
        flash('The password reset link is invalid or has expired.', 'danger')  
        return redirect(url_for('forgot_password'))  
    
    if request.method == 'POST':  
        password = request.form['password']  
        # Update the user's password in the database  
        with get_db_connection() as conn:  
            conn.execute("UPDATE users SET password = ? WHERE email = ?", (generate_password_hash(password), email))  
            conn.commit()  
        flash('Your password has been updated!', 'success')  
        return redirect(url_for('login'))  
    
    return render_template('reset_password.php', token=token)  

# Google login route  
@app.route('/google_login')  
def google_login():  
    if not google.authorized:  
        return redirect(url_for('google.login'))  
    resp = google.get('/plus/v1/people/me')  
    assert resp.ok, resp.text  
    email = resp.json()["emails"][0]["value"]  
    name = resp.json()["displayName"]  

    # Check if the user exists in the database  
    with get_db_connection() as conn:  
        user = conn.execute("SELECT * FROM users WHERE email = ?", (email,)).fetchone()  
        if not user:  
            # If the user does not exist, create a new user  
            conn.execute("INSERT INTO users (email, name) VALUES (?, ?)", (email, name))  
            conn.commit()  

    # Store user information in session  
    session['user_id'] = user['id']  
    session['name'] = name  
    session['role'] = 'user'  # Default role for social login  
    flash("Login successful!")  
    return redirect(url_for('index'))  

# Facebook login route  
@app.route('/facebook_login')  
def facebook_login():  
    if not facebook.authorized:  
        return redirect(url_for('facebook.login'))  
    resp = facebook.get('/me?fields=id,name,email')  
    assert resp.ok, resp.text  
    email = resp.json()["email"]  
    name = resp.json()["name"]  

    # Check if the user exists in the database  
    with get_db_connection() as conn:  
        user = conn.execute("SELECT * FROM users WHERE email = ?", (email,)).fetchone()  
        if not user:  
            # If the user does not exist, create a new user  
            conn.execute("INSERT INTO users (email, name) VALUES (?, ?)", (email, name))  
            conn.commit()  

    # Store user information in session  
    session['user_id'] = user['id']  
    session['name'] = name  
    session['role'] = 'user'  # Default role for social login  
    flash("Login successful!")  
    return redirect(url_for('index'))  

@app.route('/admin_login', methods=['GET', 'POST'])  
def admin_login():  
    if request.method == 'POST':  
        email = request.form['username']  # Change from username to email  
        password = request.form['password']  

        # Check if the admin exists  
        with get_db_connection() as conn:  
            admin = conn.execute("SELECT * FROM users WHERE email = ? AND role = 'admin'", (email,)).fetchone()  # Change from username to email  

        if admin and check_password_hash(admin['password'], password):  
            # Store admin information in session  
            session['user_id'] = admin['id']  
            session['role'] = admin['role']  # Store the admin's role  
            flash("Admin login successful!")  
            return redirect(url_for('admin_page'))  # Redirect to the admin page after successful login  
        else:  
            flash("Invalid admin email or password.")  
            return redirect(url_for('admin_login'))  

    return render_template('admin_login.php')  # Render the admin login page  

@app.route('/bantuan', methods=['GET', 'POST'])    
@login_required    
def bantuan():    
    if request.method == 'POST':    
        user_message = request.form['user_message']    
        user_email = session.get('email')  # Get email from session    
            
        if user_email is None:    
            flash("Error: User email not found in session.")    
            return redirect(url_for('bantuan'))    

        # Store user message in the database    
        with get_db_connection() as conn:    
            conn.execute("INSERT INTO messages (content, email) VALUES (?, ?)", (user_message, user_email))    
            conn.commit()    
        flash("Message sent successfully!")    
        return redirect(url_for('bantuan'))    

    # Fetch messages and responses for the logged-in user    
    with get_db_connection() as conn:    
        messages = conn.execute("SELECT * FROM messages WHERE email = ?", (session.get('email'),)).fetchall()    

    return render_template('bantuan.ejs', messages=messages)   

@app.route('/admin_page')    
@login_required    
def admin_page():    
    # Check if the user has the role of admin    
    if session.get('role') != 'admin':    
        flash("You are not authorized to access this page.")    
        return redirect(url_for('admin_login'))  # Redirect to login if not authorized    

    return render_template('admin_page.php')  # Render the admin page    

@app.route('/admin_chat', methods=['GET', 'POST'])  
def admin_chat():  
    # Check if the user is logged in and has the role of admin  
    if 'user_id' not in session or session.get('role') != 'admin':  
        flash("You are not authorized to access this page.")  
        return redirect(url_for('login'))  # Redirect to login if not authorized  

    selected_user_id = None  
    messages = []  

    if request.method == 'POST':  
        selected_user_id = request.form.get('user_id')  # Get the selected user ID from the form  

        if selected_user_id:  
            # Fetch messages for the selected user  
            with get_db_connection() as conn:  
                messages = conn.execute("SELECT * FROM messages WHERE email = (SELECT email FROM users WHERE id = ?)", (selected_user_id,)).fetchall()  

    # Fetch all users to display in the dropdown, including sellers and admins  
    with get_db_connection() as conn:  
        users = conn.execute("SELECT id, email, role FROM users WHERE role IN ('user', 'seller', 'admin')").fetchall()  # Fetch users with roles 'user', 'seller', and 'admin'  

    return render_template('admin_chat.php', messages=messages, users=users, selected_user_id=selected_user_id)  

@app.route('/reply_chat/<int:message_id>', methods=['POST'])  
def reply_chat(message_id):  
    response = request.form['response']  

    # Update the message with the admin's response  
    with get_db_connection() as conn:  
        conn.execute("UPDATE messages SET response = ? WHERE id = ?", (response, message_id))  
        conn.commit()  

    flash("Response sent successfully!")  
    return redirect(url_for('admin_chat'))  # Redirect back to the admin chat page    

@app.route('/seller', methods=['GET', 'POST'])  
def seller():  
    if 'user_id' not in session or session.get('role') not in ['seller', 'admin']:  
        flash("You are not authorized to access this page.")  
        return redirect(url_for('login'))  

    user_id = session.get('user_id')  
    conn = get_db_connection()  
    categories = conn.execute('SELECT * FROM categories').fetchall()  
    store = conn.execute('SELECT * FROM stores WHERE owner_id = ?', (user_id,)).fetchone()  

    if request.method == 'POST':  
        title = request.form['title']  
        description = request.form['description']  
        price = request.form['price']  
        stock = request.form['stock']  
        category_id = request.form['category_id']  
        image = request.files['image']  # Assuming image_path is provided  

        # Insert new product into products table  
        conn.execute('''  
            INSERT INTO products (title, description, price, stock, category_id, image_path)  
            VALUES (?, ?, ?, ?, ?, ?)  
        ''', (title, description, price, stock, category_id, image.filename))  

        # Get the last inserted product id  
        product_id = conn.execute('SELECT last_insert_rowid()').fetchone()[0]  

        # Insert into store_products table  
        conn.execute('''  
            INSERT INTO store_products (store_id, product_id)  
            VALUES (?, ?)  
        ''', (store['id'], product_id))  

        conn.commit()  
        flash("Product added successfully!")  
        return redirect(url_for('seller_dashboard'))  

    conn.close()  
    return render_template('seller.php', categories=categories, store=store)  


@app.route('/seller_dashboard')      
def seller_dashboard():      
    user_id = session.get('user_id')      
    if not user_id:      
        return redirect(url_for('login'))      

    conn = get_db_connection()      
    # Get the store associated with the logged-in user    
    store = conn.execute('SELECT * FROM stores WHERE owner_id = ?', (user_id,)).fetchone()      

    if store:      
        # Fetch only the products that belong to the seller's store    
        products = conn.execute('''      
            SELECT *      
            FROM products      
            WHERE store_id = ?      
        ''', (store['id'],)).fetchall()      

        # Fetch orders related to the seller's products  
        orders = conn.execute('''    
            SELECT o.*, SUM(oi.quantity * p.price) AS total_amount  
            FROM orders o  
            JOIN order_items oi ON o.id = oi.order_id  
            JOIN products p ON oi.product_id = p.id  
            WHERE p.store_id = ?  
            GROUP BY o.id  
        ''', (store['id'],)).fetchall()  
    else:      
        products = []    
        orders = []  

    conn.close()      

    # Format prices      
    formatted_products = []      
    for product in products:      
        formatted_product = dict(product)      
        formatted_product['price'] = locale.currency(product['price'], grouping=True)      
        formatted_products.append(formatted_product)      

    return render_template('seller_dashboard.php', store=store, products=formatted_products, orders=orders)  


@app.route('/register_vendor', methods=['GET', 'POST'])
def register_vendor():
    if request.method == 'POST':
        user_id = session.get('user_id')
        store_name = request.form['store_name']
        description = request.form['description']
        location = request.form['location']
        operational_hours = request.form['operational_hours']
        image = request.files['image']
        
        # Save the image file
        image_path = os.path.join('static/uploads', image.filename)
        image.save(image_path)
        
        conn = get_db_connection()
        conn.execute('''
            INSERT INTO stores (owner_id, name, description, location, operational_hours, image_path)
            VALUES (?, ?, ?, ?, ?, ?)
        ''', (user_id, store_name, description, location, operational_hours, image_path))
        
        # Update the user's role to 'seller'
        conn.execute('UPDATE users SET role = ? WHERE id = ?', ('seller', user_id))
        conn.commit()
        conn.close()
        
        session['store_name'] = store_name
        session['role'] = 'seller'  # Update the session role to 'seller'
        flash('Store registered successfully!')
        return redirect(url_for('seller_dashboard'))
    
    return render_template('register_vendor.php')

@app.route('/manage_products')
def manage_products():
    with get_db_connection() as conn:
        products = conn.execute('SELECT * FROM products').fetchall()
    return render_template('manage_products.php', products=products)

@app.route('/edit_product/<int:product_id>')
def edit_product(product_id):
    with get_db_connection() as conn:
        product = conn.execute('SELECT * FROM products WHERE id = ?', (product_id,)).fetchone()
    return render_template('edit_product.php', product=product)

@app.route('/add_product', methods=['POST'])  
def add_product():  
    title = request.form['title']  
    description = request.form['description']  
    price = request.form['price']  
    stock = request.form['stock']  
    category_id = request.form['category']  # Capture the category ID  

    # Validate image upload  
    if 'image' not in request.files:  
        flash("No image uploaded.")  
        return redirect(url_for('seller'))  

    image = request.files['image']  
    if image.filename == '':  
        flash("No selected file.")  
        return redirect(url_for('seller'))  

    # Save the uploaded image  
    image_path = os.path.join('static/public/images', image.filename)  
    image.save(image_path)  

    # Get the store ID for the logged-in seller  
    user_id = session.get('user_id')  # Get the logged-in user's ID  
    with get_db_connection() as conn:  
        store = conn.execute("SELECT id FROM stores WHERE owner_id = ?", (user_id,)).fetchone()  
        if store:  
            store_id = store['id']  # Get the store ID  
        else:  
            flash("Store not found. Please register your store first.")  
            return redirect(url_for('seller'))  

    # Insert product into the database  
    with get_db_connection() as conn:  
        conn.execute("INSERT INTO products (title, description, price, stock, category_id, store_id, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)",  
                    (title, description, price, stock, category_id, store_id, image_path))  # Include store_id  
        conn.commit()  

    flash("Product added successfully!")  
    return redirect(url_for('products'))  # Redirect to the products page after adding  


@app.route('/update_product/<int:product_id>', methods=['POST'])
def update_product(product_id):
    title = request.form['title']
    description = request.form['description']
    price = request.form['price']
    stock = request.form['stock']

    # Validate image upload
    image_path = None
    if 'image' in request.files and request.files['image'].filename != '':
        image = request.files['image']
        image_path = os.path.join(app.config['UPLOAD_FOLDER'], image.filename)
        image.save(image_path)

    # Update product in the database
    with get_db_connection() as conn:
        if image_path:
            conn.execute("UPDATE products SET title = ?, description = ?, price = ?, stock = ?, image_path = ? WHERE id = ?",
                        (title, description, price, stock, image_path, product_id))
        else:
            conn.execute("UPDATE products SET title = ?, description = ?, price = ?, stock = ? WHERE id = ?",
                        (title, description, price, stock, product_id))
        conn.commit()

    flash("Product updated successfully!")
    return redirect(url_for('manage_products'))  # Redirect to the manage products page after updating

@app.route('/delete_product/<int:product_id>', methods=['POST'])
def delete_product(product_id):
    with get_db_connection() as conn:
        conn.execute("DELETE FROM products WHERE id = ?", (product_id,))
        conn.commit()

    flash("Product deleted successfully!")
    return redirect(url_for('manage_products'))  # Redirect to the manage products page after deleting

@app.route('/logout')  
def logout():  
    # Check if the user is logged in  
    if 'user_id' in session:  
        # Store the role of the user who is logging out  
        user_role = session.get('role')  
        
        # Clear the session data for the user who is logging out  
        session.clear()  # This will clear all session data  
        
        flash("You have been logged out.")  
        
        # Redirect based on user role  
        if user_role == 'admin':  
            return redirect(url_for('admin_login'))  # Redirect admin to login page  
        else:  
            return redirect(url_for('index'))  # Redirect regular user to home page  
    else:  
        flash("You are not logged in.")  
        return redirect(url_for('index'))  # Redirect to home page if not logged in  

@app.route('/fetch_messages')  
def fetch_messages():  
    if 'user_id' not in session:  
        return {'messages': []}  # Return an empty list if not logged in  

    with get_db_connection() as conn:  
        messages = conn.execute("SELECT * FROM messages WHERE email = ?", (session.get('email'),)).fetchall()  # Use email instead of user_id  

    # Convert messages to a list of dictionaries  
    messages_list = [{'id': message['id'], 'content': message['content'], 'response': message['response']} for message in messages]  
    return {'messages': messages_list}  

@app.route('/fetch_all_messages')  
def fetch_all_messages():  
    with get_db_connection() as conn:  
        messages = conn.execute("SELECT * FROM messages").fetchall()  

    # Convert messages to a list of dictionaries  
    messages_list = [{'id': message['id'], 'email': message['email'], 'content': message['content'], 'response': message['response']} for message in messages]  # Use email instead of user_id  
    return {'messages': messages_list}  

@app.route('/profile')
@login_required
def profile():
    user_id = session.get('user_id')
    if not user_id:
        return redirect(url_for('login'))

    conn = get_db_connection()
    user = conn.execute('SELECT * FROM users WHERE id = ?', (user_id,)).fetchone()
    
    orders = conn.execute('''
        SELECT o.id AS order_id, o.status, oi.quantity, p.title AS product_title, (oi.quantity * p.price) AS total_price
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.email = ?
    ''', (user['email'],)).fetchall()
    
    formatted_orders = []
    for order in orders:
        formatted_order = dict(order)
        formatted_order['total_price'] = locale.currency(order['total_price'], grouping=True)
        formatted_orders.append(formatted_order)
        
    conn.close()

    return render_template('profile.php', user=user, orders=formatted_orders)

@app.route('/search', methods=['GET'])  
def search():  
    query = request.args.get('query')  # Get the search query from the URL  
    results = []  

    if query:  
        # Connect to the database  
        with get_db_connection() as conn:  
            # Search for products (you can modify this to search other tables)  
            results = conn.execute("SELECT * FROM products WHERE title LIKE ? OR description LIKE ?",   
                                ('%' + query + '%', '%' + query + '%')).fetchall()  

    return render_template('search_results.ejs', query=query, results=results)  

@app.route('/suggestions', methods=['GET'])  
def suggestions():  
    query = request.args.get('query', '')  
    results = []  

    if query:  
        with get_db_connection() as conn:  
            # Search for products matching the query  
            results = conn.execute("SELECT title FROM products WHERE title LIKE ?", ('%' + query + '%',)).fetchall()  

    # Convert results to a list of dictionaries  
    suggestions = [{'title': row['title']} for row in results]  
    return jsonify(suggestions)  # Return suggestions as JSON  

if __name__ == '__main__':  
    create_users_table()  # Create the users table when the app starts  
    create_products_table()  # Create the products table when the app starts  
    create_messages_table()
    create_reviews_table()  # Create the reviews table when the app starts  
    try:  
        app.run(host='0.0.0.0', port=5000, debug=True)  # Run on localhost  
    except KeyboardInterrupt:  
        logging.info("Server stopped by user.")  
    except Exception as e:  
        logging.error(f"An error occurred: {e}")  
