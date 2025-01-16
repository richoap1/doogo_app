document.addEventListener('DOMContentLoaded', () => {
    // Example: Handle a button click
    const button = document.getElementById('myButton');
    if (button) {
        button.addEventListener('click', () => {
            alert('Button was clicked!');
        });
    }

    // Example: Handle form submission
    const form = document.getElementById('myForm');
    if (form) {
        form.addEventListener('submit', (event) => {
            event.preventDefault(); // Prevent the default form submission
            const formData = new FormData(form);
            // Process form data here
            console.log('Form submitted:', Object.fromEntries(formData));
            alert('Form submitted successfully!');
        });
    }

    // Chat popup functionality
    const chatPopup = document.getElementById('chat-popup');
    const openChat = document.getElementById('open-chat');
    const closeChat = document.getElementById('close-chat');

    if (openChat) {
        openChat.addEventListener('click', () => {
            chatPopup.style.display = 'block';
        });
    }

    if (closeChat) {
        closeChat.addEventListener('click', () => {
            chatPopup.style.display = 'none';
        });
    }

    // Example: Handle star rating hover effect
    const ratings = document.querySelectorAll('.rating');

    ratings.forEach(rating => {
        const stars = rating.querySelectorAll('.star');
        const currentRating = parseInt(rating.getAttribute('data-rating'));

        // Highlight stars based on the current rating
        stars.forEach((star, index) => {
            if (index < currentRating) {
                star.style.display = 'inline'; // Show filled stars
            } else {
                star.style.display = 'none'; // Hide empty stars
            }
        });
    });

    // Smooth scrolling to sections
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

document.getElementById('search-input').addEventListener('input', function() {
    const query = this.value;

    if (query.length > 0) {
        fetch(`/suggestions?query=${query}`)
            .then(response => response.json())
            .then(data => {
                const suggestions = document.getElementById('suggestions');
                suggestions.innerHTML = ''; // Clear previous suggestions
                if (data.length > 0) {
                    suggestions.style.display = 'block'; // Show suggestions
                    data.forEach(product => {
                        const item = document.createElement('div');
                        item.className = 'suggestion-item';
                        item.textContent = product.title; // Display product title
                        item.onclick = function() {
                            document.getElementById('search-input').value = product.title; // Set input value
                            suggestions.style.display = 'none'; // Hide suggestions
                        };
                        suggestions.appendChild(item);
                    });
                } else {
                    suggestions.style.display = 'none'; // Hide if no suggestions
                }
            });
    } else {
        document.getElementById('suggestions').style.display = 'none'; // Hide if input is empty
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Dropdown toggle
    var dropdownToggle = document.querySelector('[data-bs-toggle="dropdown"]');
    if (dropdownToggle) {
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default link behavior
            var dropdownMenu = dropdownToggle.nextElementSibling;
            dropdownMenu.classList.toggle('show');
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.matches('[data-bs-toggle="dropdown"]')) {
            var dropdowns = [].slice.call(document.querySelectorAll('.dropdown-menu'));
            dropdowns.forEach(function(dropdown) {
                dropdown.classList.remove('show');
            });
        }
    });
});

function updateQuantity(productId, quantity) {
    if (quantity < 1) {
        removeFromCart(productId);
        return;
    }
    $.ajax({
        url: "/update_cart/" + productId,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ quantity: quantity }),
        success: function(response) {
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error updating cart:', error);
        }
    });
}

function removeFromCart(productId) {
    $.ajax({
        url: "/remove_from_cart/" + productId,
        type: 'POST',
        success: function(response) {
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error removing from cart:', error);
        }
    });
}

function populateEditForm(id, title, description, price, discount, stock, imagePath) {
    document.getElementById('edit-product-id').value = id;
    document.getElementById('edit-title').value = title;
    document.getElementById('edit-description').value = description;
    document.getElementById('edit-price').value = price;
    document.getElementById('edit-discount').value = discount;
    document.getElementById('edit-stock').value = stock;
    document.getElementById('edit-product-form').action = '/update_product/' + id;
}