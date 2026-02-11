<?php
// Sample properties data - in real app, this would come from database
$properties = [
    [
        'id' => 1,
        'title' => 'Modern Studio Near Campus',
        'price' => 850,
        'address' => '123 University Ave, Boston, MA',
        'beds' => 1,
        'baths' => 1,
        'sqft' => 550,
        'type' => 'Apartment',
        'image' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=300&fit=crop',
        'featured' => true
    ],
    [
        'id' => 2,
        'title' => 'Spacious 2BR House',
        'price' => 1200,
        'address' => '456 Maple St, Chicago, IL',
        'beds' => 2,
        'baths' => 1,
        'sqft' => 950,
        'type' => 'House',
        'image' => 'https://images.unsplash.com/photo-1518780664697-55e3ad937233?w=400&h=300&fit=crop',
        'featured' => false
    ],
    [
        'id' => 3,
        'title' => 'Downtown Luxury Loft',
        'price' => 1600,
        'address' => '789 Downtown Blvd, NYC',
        'beds' => 1,
        'baths' => 1,
        'sqft' => 750,
        'type' => 'Loft',
        'image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=400&h=300&fit=crop',
        'featured' => true
    ],
    [
        'id' => 4,
        'title' => 'Family Home with Garden',
        'price' => 2200,
        'address' => '101 Oak Lane, Austin, TX',
        'beds' => 3,
        'baths' => 2,
        'sqft' => 1450,
        'type' => 'House',
        'image' => 'https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=400&h=300&fit=crop',
        'featured' => false
    ],
    [
        'id' => 5,
        'title' => 'Student Apartment Complex',
        'price' => 750,
        'address' => '202 College Rd, Berkeley, CA',
        'beds' => 1,
        'baths' => 1,
        'sqft' => 500,
        'type' => 'Apartment',
        'image' => 'https://images.unsplash.com/photo-1558036117-15e82a2c9a9a?w=400&h=300&fit=crop',
        'featured' => false
    ],
    [
        'id' => 6,
        'title' => 'Modern Townhouse',
        'price' => 1800,
        'address' => '303 Modern Ave, Seattle, WA',
        'beds' => 2,
        'baths' => 2.5,
        'sqft' => 1200,
        'type' => 'Townhouse',
        'image' => 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=400&h=300&fit=crop',
        'featured' => true
    ],
    [
        'id' => 7,
        'title' => 'Cozy Studio Apartment',
        'price' => 650,
        'address' => '404 Pine St, Portland, OR',
        'beds' => 1,
        'baths' => 1,
        'sqft' => 450,
        'type' => 'Apartment',
        'image' => 'https://images.unsplash.com/photo-1499916078039-922301b0eb9b?w=400&h=300&fit=crop',
        'featured' => false
    ],
    [
        'id' => 8,
        'title' => 'Luxury City View Condo',
        'price' => 2800,
        'address' => '505 Skyline Blvd, Miami, FL',
        'beds' => 2,
        'baths' => 2,
        'sqft' => 1100,
        'type' => 'Condo',
        'image' => 'https://images.unsplash.com/photo-1448630360428-65456885c650?w=400&h=300&fit=crop',
        'featured' => true
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartHunt - Find Your Perfect Home</title>
    <link rel="stylesheet" href="./styles/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php">
                    <i class="fas fa-home"></i>
                    <span>SmartHunt</span>
                </a>
            </div>
            
            <div class="nav-menu">
                <a href="index.php" class="nav-link active"><i class="fas fa-search"></i> <span>Browse</span></a>
                <a href="#" class="nav-link"><i class="fas fa-heart"></i> <span>Saved</span></a>
                <a href="#" class="nav-link"><i class="fas fa-bell"></i> <span>Alerts</span></a>
                <a href="#" class="nav-link"><i class="fas fa-user"></i> <span>Account</span></a>
                <button class="nav-button">List Your Property</button>
            </div>
            
            <button class="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Search Bar -->
    <div class="search-container">
        <div class="search-box">
            <div class="search-input">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Enter city, neighborhood, or ZIP code">
            </div>
            <div class="search-filters">
                <select>
                    <option value="">Any Price</option>
                    <option value="500">$500+</option>
                    <option value="1000">$1000+</option>
                    <option value="1500">$1500+</option>
                </select>
                <select>
                    <option value="">Any Beds</option>
                    <option value="1">1+ Bed</option>
                    <option value="2">2+ Beds</option>
                    <option value="3">3+ Beds</option>
                </select>
                <select>
                    <option value="">Any Type</option>
                    <option value="apartment">Apartment</option>
                    <option value="house">House</option>
                    <option value="condo">Condo</option>
                </select>
                <button class="search-button">Search</button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container">
        <div class="page-header">
            <h1>Available Rentals Near You</h1>
            <p class="small-text">Showing <?php echo count($properties); ?> properties</p>
        </div>

        <div class="properties-grid">
            <?php foreach ($properties as $property): ?>
            <a href="property.php?id=<?php echo $property['id']; ?>" class="property-card-link">
                <div class="property-card <?php echo $property['featured'] ? 'featured' : ''; ?>">
                    <?php if ($property['featured']): ?>
                    <div class="featured-badge">FEATURED</div>
                    <?php endif; ?>
                    
                    <div class="property-image">
                        <img src="<?php echo $property['image']; ?>" 
                             alt="<?php echo htmlspecialchars($property['title']); ?>"
                             onerror="this.src='https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=300&fit=crop'">
                        <button class="save-btn" onclick="event.preventDefault(); event.stopPropagation(); toggleSave(this);">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                    
                    <div class="property-details">
                        <div class="property-price">
                            <span class="price">$<?php echo number_format($property['price']); ?></span>
                            <span class="period">/month</span>
                        </div>
                        
                        <h3 class="property-title">
                            <?php echo htmlspecialchars($property['title']); ?>
                        </h3>
                        
                        <p class="property-address">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($property['address']); ?>
                        </p>
                        
                        <div class="property-features">
                            <span><i class="fas fa-bed"></i> <?php echo $property['beds']; ?> bed</span>
                            <span><i class="fas fa-bath"></i> <?php echo $property['baths']; ?> bath</span>
                            <span><i class="fas fa-ruler-combined"></i> <?php echo number_format($property['sqft']); ?> sqft</span>
                        </div>
                        
                        <div class="property-type">
                            <?php echo $property['type']; ?>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>SmartHunt</h4>
                <p class="small-text">Find your perfect home quickly and easily.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#">Browse Rentals</a></li>
                    <li><a href="#">How it Works</a></li>
                    <li><a href="#">For Landlords</a></li>
                    <li><a href="#">Safety Tips</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Support</h4>
                <ul>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Newsletter</h4>
                <p class="small-text">Get the latest rental listings.</p>
                <div class="newsletter-form">
                    <input type="email" placeholder="Your email">
                    <button>Subscribe</button>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p class="small-text">&copy; <?php echo date('Y'); ?> SmartHunt. All rights reserved.</p>
        </div>
    </footer>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay">
        <div class="mobile-menu-content">
            <button class="close-menu"><i class="fas fa-times"></i></button>
            <a href="index.php" class="mobile-nav-link active">Browse</a>
            <a href="#" class="mobile-nav-link">Saved Properties</a>
            <a href="#" class="mobile-nav-link">Alerts</a>
            <a href="#" class="mobile-nav-link">My Account</a>
            <a href="#" class="mobile-nav-link">List Property</a>
            <a href="#" class="mobile-nav-link">Help Center</a>
        </div>
    </div>

    <style>
        /* Toast Notification */
        .toast-notification {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background-color: #333;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 14px;
            z-index: 4000;
            opacity: 0;
            transition: transform 0.3s, opacity 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .toast-notification.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
    </style>

    <script>
        // Mobile menu functionality
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            document.querySelector('.mobile-menu-overlay').classList.add('active');
        });
        
        document.querySelector('.close-menu').addEventListener('click', function() {
            document.querySelector('.mobile-menu-overlay').classList.remove('active');
        });
        
        // Close menu when clicking outside
        document.querySelector('.mobile-menu-overlay').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
        
        // Toast notification function
        function showToast(message) {
            // Remove existing toast
            const existingToast = document.querySelector('.toast-notification');
            if (existingToast) {
                existingToast.remove();
            }
            
            // Create new toast
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.textContent = message;
            document.body.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);
            
            // Remove after 3 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }
        
        // Save button functionality
        function toggleSave(btn) {
            const icon = btn.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                btn.style.color = '#e74c3c';
                // Show toast notification
                showToast('Property saved to favorites');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                btn.style.color = '#7f8c8d';
                showToast('Property removed from favorites');
            }
        }
        
        // Search form submission
        document.querySelector('.search-button').addEventListener('click', function() {
            const searchInput = document.querySelector('.search-input input').value;
            if (searchInput.trim()) {
                showToast(`Searching for: ${searchInput}`);
            }
        });
        
        // Newsletter form submission
        document.querySelector('.newsletter-form button').addEventListener('click', function(e) {
            e.preventDefault();
            const emailInput = document.querySelector('.newsletter-form input');
            if (emailInput.value) {
                showToast('Subscribed to newsletter!');
                emailInput.value = '';
            }
        });
        
        // Initialize images with error handling
        document.addEventListener('DOMContentLoaded', function() {
            // Check if any images failed to load
            const images = document.querySelectorAll('.property-image img');
            const fallbackImage = 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=300&fit=crop';
            
            images.forEach(img => {
                // Add error handler if not already present
                if (!img.hasAttribute('data-error-handled')) {
                    img.setAttribute('data-error-handled', 'true');
                    img.addEventListener('error', function() {
                        this.src = fallbackImage;
                    });
                }
            });
        });
    </script>
</body>
</html>