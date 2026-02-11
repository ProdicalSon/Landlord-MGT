<?php
// In a real application, you would fetch property details from database
$property_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Sample property data - in real app, fetch from database based on $property_id
$property = [
    'id' => $property_id,
    'title' => 'Modern Studio Near Campus',
    'price' => 850,
    'address' => '123 University Ave, Boston, MA 02115',
    'beds' => 1,
    'baths' => 1,
    'sqft' => 550,
    'type' => 'Apartment',
    'description' => 'Beautiful modern studio apartment located just 5 minutes walk from campus. Recently renovated with new appliances, hardwood floors, and plenty of natural light. Perfect for students or young professionals.',
    'features' => ['Fully furnished', 'Utilities included', 'High-speed internet', 'Laundry in building', 'Pet-friendly', 'Security system'],
    'images' => [
        'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1558036117-15e82a2c9a9a?w=800&h=600&fit=crop',
        'https://images.unsplash.com/photo-1560184897-67f4a3f9a7fa?w=800&h=600&fit=crop'
    ],
    'landlord' => [
        'name' => 'John Smith',
        'phone' => '(555) 123-4567',
        'email' => 'john.smith@example.com',
        'rating' => 4.8,
        'properties' => 12
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['title']); ?> - SmartHunt</title>
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
                <a href="index.php" class="nav-link"><i class="fas fa-search"></i> <span>Browse</span></a>
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

    <!-- Property Detail -->
    <main class="container property-detail-container">
        <div class="property-header">
            <h1><?php echo htmlspecialchars($property['title']); ?></h1>
            <p class="property-location">
                <i class="fas fa-map-marker-alt"></i>
                <?php echo htmlspecialchars($property['address']); ?>
            </p>
        </div>

        <div class="property-content">
            <div class="property-gallery">
                <div class="main-image">
                    <img src="<?php echo $property['images'][0]; ?>" alt="Main property image">
                </div>
                <div class="image-thumbnails">
                    <?php foreach ($property['images'] as $index => $image): ?>
                    <img src="<?php echo $image; ?>" alt="Property image <?php echo $index + 1; ?>">
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="property-info">
                <div class="price-section">
                    <h2>$<?php echo number_format($property['price']); ?> <span class="period">/month</span></h2>
                    <div class="property-meta">
                        <span><i class="fas fa-bed"></i> <?php echo $property['beds']; ?> bed</span>
                        <span><i class="fas fa-bath"></i> <?php echo $property['baths']; ?> bath</span>
                        <span><i class="fas fa-ruler-combined"></i> <?php echo number_format($property['sqft']); ?> sqft</span>
                        <span><i class="fas fa-building"></i> <?php echo $property['type']; ?></span>
                    </div>
                </div>

                <div class="description-section">
                    <h3>Description</h3>
                    <p><?php echo htmlspecialchars($property['description']); ?></p>
                </div>

                <div class="features-section">
                    <h3>Features & Amenities</h3>
                    <div class="features-grid">
                        <?php foreach ($property['features'] as $feature): ?>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo htmlspecialchars($feature); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="landlord-section">
                    <h3>Property Owner</h3>
                    <div class="landlord-info">
                        <div class="landlord-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="landlord-details">
                            <h4><?php echo htmlspecialchars($property['landlord']['name']); ?></h4>
                            <div class="landlord-rating">
                                <div class="stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= floor($property['landlord']['rating']) ? 'filled' : ''; ?>"></i>
                                    <?php endfor; ?>
                                    <span>(<?php echo $property['landlord']['rating']; ?>)</span>
                                </div>
                                <p class="small-text"><?php echo $property['landlord']['properties']; ?> properties listed</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rent Request Form -->
                <div class="rent-request-section">
                    <h3>Request to Rent</h3>
                    <form id="rentRequestForm" class="rent-form">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="moveDate">Desired Move-in Date</label>
                            <input type="date" id="moveDate" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message to Landlord</label>
                            <textarea id="message" rows="4" placeholder="Tell the landlord about yourself and why you're interested..."></textarea>
                        </div>
                        
                        <button type="submit" class="rent-request-btn">
                            <i class="fas fa-paper-plane"></i> Send Rent Request
                        </button>
                        <p class="small-text form-note">
                            Your request will be sent directly to <?php echo htmlspecialchars($property['landlord']['name']); ?>.
                            We'll also send you a copy via email.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h4>SmartHunt</h4>
                <p class="small-text">Find your perfect home quickly and easily.</p>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Browse Rentals</a></li>
                    <li><a href="#">Safety Tips</a></li>
                    <li><a href="#">Contact Support</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Legal</h4>
                <ul>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
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
            <a href="index.php" class="mobile-nav-link">Browse</a>
            <a href="#" class="mobile-nav-link">Saved Properties</a>
            <a href="#" class="mobile-nav-link">My Account</a>
            <a href="#" class="mobile-nav-link">Help Center</a>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>Request Sent Successfully!</h3>
            <p class="small-text">Your rent request has been sent to <?php echo htmlspecialchars($property['landlord']['name']); ?>.</p>
            <p class="small-text">You'll receive a confirmation email shortly.</p>
            <button class="modal-close-btn" onclick="closeModal()">Continue Browsing</button>
        </div>
    </div>

    <script>
        // Mobile menu functionality
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            document.querySelector('.mobile-menu-overlay').classList.add('active');
        });
        
        document.querySelector('.close-menu').addEventListener('click', function() {
            document.querySelector('.mobile-menu-overlay').classList.remove('active');
        });
        
        document.querySelector('.mobile-menu-overlay').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
        
        // Rent request form submission
        document.getElementById('rentRequestForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // In a real app, you would send this data to your server
            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                moveDate: document.getElementById('moveDate').value,
                message: document.getElementById('message').value,
                propertyId: <?php echo $property['id']; ?>,
                landlordEmail: '<?php echo $property['landlord']['email']; ?>'
            };
            
            // Simulate form submission
            console.log('Rent request submitted:', formData);
            
            // Show success modal
            document.getElementById('successModal').style.display = 'flex';
            
            // Reset form
            this.reset();
        });
        
        // Close modal function
        function closeModal() {
            document.getElementById('successModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('successModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };
        
        // Image thumbnail click handler
        document.querySelectorAll('.image-thumbnails img').forEach(thumb => {
            thumb.addEventListener('click', function() {
                const mainImg = document.querySelector('.main-image img');
                mainImg.src = this.src;
            });
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
    </script>
</body>
</html>