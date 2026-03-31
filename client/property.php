<?php
// property.php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include models
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/PropertyModel.php';
require_once __DIR__ . '/models/SavedPropertyModel.php';
require_once __DIR__ . '/models/NotificationModel.php';
require_once __DIR__ . '/models/UserModel.php';
require_once __DIR__ . '/models/PaymentModel.php';

// Initialize models
$propertyModel = new PropertyModel();
$savedPropertyModel = new SavedPropertyModel();
$notificationModel = new NotificationModel();
$userModel = new UserModel();
$paymentModel = new PaymentModel();

// Get property ID from URL
$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get current user ID from session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$isLoggedIn = $user_id > 0;
$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';

if ($property_id === 0) {
    header('Location: index.php');
    exit;
}

// Fetch property from database with images
$property = $propertyModel->getPropertyById($property_id);
$propertyImages = $propertyModel->getPropertyImages($property_id);

if (!$property) {
    header('Location: index.php');
    exit;
}

// Check if property is saved by user
$isSaved = $isLoggedIn ? $savedPropertyModel->isSaved($user_id, $property_id) : false;

// Get landlord details
$landlord = $userModel->getUserById($property['landlord_id']);

// Helper functions
function getPropertyImageUrl($imagePath) {
    if (empty($imagePath)) return null;
    return '/Landlord-MGT/Landlord/Frontend/' . $imagePath;
}

function getPropertyFeatures($property) {
    $features = [];
    if (!empty($property['property_type'])) $features[] = ucfirst($property['property_type']);
    if (!empty($property['bedrooms'])) $features[] = $property['bedrooms'] . ' Bedroom' . ($property['bedrooms'] > 1 ? 's' : '');
    if (!empty($property['bathrooms'])) $features[] = $property['bathrooms'] . ' Bathroom' . ($property['bathrooms'] > 1 ? 's' : '');
    if (!empty($property['sqft'])) $features[] = number_format($property['sqft']) . ' sq ft';
    
    if (!empty($property['amenities'])) {
        $amenities = json_decode($property['amenities'], true);
        if (is_array($amenities)) $features = array_merge($features, $amenities);
    }
    return array_slice($features, 0, 12);
}

function formatFullAddress($property) {
    $parts = [];
    if (!empty($property['address'])) $parts[] = $property['address'];
    if (!empty($property['neighborhood'])) $parts[] = $property['neighborhood'];
    if (!empty($property['city'])) $parts[] = $property['city'];
    return implode(', ', $parts);
}

function getLandlordInfo($landlord, $property) {
    return [
        'name' => ($landlord['first_name'] ?? '') . ' ' . ($landlord['last_name'] ?? '') ?: 'Property Manager',
        'phone' => $landlord['phone_number'] ?? 'Not provided',
        'email' => $landlord['email'] ?? 'landlord@example.com',
        'mpesa' => $property['mpesa_number'] ?? $landlord['mpesa_number'] ?? 'Not provided',
        'joined' => isset($landlord['created_at']) ? date('Y', strtotime($landlord['created_at'])) : '2023'
    ];
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'send_rent_request') {
        if (!$isLoggedIn) {
            echo json_encode(['success' => false, 'message' => 'Please login to send a rent request']);
            exit;
        }
        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $move_date = $_POST['moveDate'] ?? '';
        $message = trim($_POST['message'] ?? '');
        
        if (empty($name) || empty($email) || empty($phone) || empty($move_date)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit;
        }
        
        $notificationMessage = "New rent request from $name for {$property['property_name']}\n";
        $notificationMessage .= "Email: $email\nPhone: $phone\nMove-in Date: $move_date\nMessage: $message";
        
        $notificationModel->create($property['landlord_id'], 'rent_request', $notificationMessage, $property_id);
        $notificationModel->create($user_id, 'rent_request_sent', "Your rent request for {$property['property_name']} has been sent", $property_id);
        
        echo json_encode(['success' => true, 'message' => 'Rent request sent successfully!']);
        exit;
    }
    
    if ($_POST['action'] === 'toggle_save') {
        if (!$isLoggedIn) {
            echo json_encode(['success' => false, 'message' => 'Please login to save properties']);
            exit;
        }
        
        $result = $savedPropertyModel->saveProperty($user_id, $property_id);
        $isSaved = $savedPropertyModel->isSaved($user_id, $property_id);
        
        echo json_encode([
            'success' => $result,
            'saved' => $isSaved,
            'message' => $isSaved ? 'Property saved' : 'Property removed'
        ]);
        exit;
    }
    
    if ($_POST['action'] === 'initiate_payment') {
        if (!$isLoggedIn) {
            echo json_encode(['success' => false, 'message' => 'Please login to make a payment']);
            exit;
        }
        
        $phone = trim($_POST['phone_number'] ?? '');
        $amount = floatval($_POST['amount'] ?? 0);
        
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($cleanPhone, 0, 1) == '0') $cleanPhone = '254' . substr($cleanPhone, 1);
        if (substr($cleanPhone, 0, 3) != '254') $cleanPhone = '254' . $cleanPhone;
        
        $paymentData = [
            'property_id' => $property_id,
            'tenant_id' => $user_id,
            'landlord_id' => $property['landlord_id'],
            'amount' => $amount,
            'phone_number' => $cleanPhone,
            'status' => 'pending'
        ];
        
        $payment_id = $paymentModel->createPayment($paymentData);
        
        if (!$payment_id) {
            echo json_encode(['success' => false, 'message' => 'Failed to create payment record']);
            exit;
        }
        
        $checkoutRequestId = 'SIM' . time() . rand(1000, 9999);
        $notificationModel->create($property['landlord_id'], 'payment_initiated', "Payment of KES " . number_format($amount) . " initiated for {$property['property_name']}", $property_id);
        
        echo json_encode([
            'success' => true,
            'message' => 'STK Push sent. Check your phone.',
            'checkout_request_id' => $checkoutRequestId,
            'payment_id' => $payment_id
        ]);
        exit;
    }
    
    if ($_POST['action'] === 'check_payment_status') {
        $payment_id = intval($_POST['payment_id'] ?? 0);
        static $attempt = 0;
        $attempt++;
        
        if ($attempt >= 3) {
            $paymentModel->updatePaymentStatus($payment_id, 'completed', 'SIM' . rand(100000, 999999));
            $payment = $paymentModel->getPaymentById($payment_id);
            if ($payment) {
                $notificationModel->create($payment['landlord_id'], 'payment_received', "Payment of KES " . number_format($payment['amount']) . " received", $payment['property_id']);
            }
            echo json_encode(['success' => true, 'status' => 'completed', 'message' => 'Payment completed!']);
        } else {
            echo json_encode(['success' => true, 'status' => 'pending', 'message' => 'Processing...']);
        }
        exit;
    }
}

$landlordInfo = getLandlordInfo($landlord, $property);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['property_name']); ?> - SmartHunt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; background: #f8f9fa; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        
        /* Navigation */
        .navbar { background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000; }
        .nav-container { max-width: 1200px; margin: 0 auto; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .nav-logo a { display: flex; align-items: center; gap: 10px; text-decoration: none; font-size: 22px; font-weight: 700; color: #0077b6; }
        .nav-menu { display: flex; align-items: center; gap: 20px; }
        .nav-link { text-decoration: none; color: #333; font-size: 14px; padding: 8px 12px; border-radius: 4px; transition: background 0.2s; }
        .nav-link:hover { background: #f5f5f5; }
        .nav-button { background: #0077b6; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .mobile-menu-btn { display: none; background: none; border: none; font-size: 20px; cursor: pointer; }
        
        /* Property Header */
        .property-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px; margin-bottom: 30px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .property-header h1 { font-size: 32px; margin-bottom: 10px; color: #333; }
        .save-property-btn { background: white; border: 2px solid #0077b6; color: #0077b6; padding: 12px 24px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.2s; }
        .save-property-btn.saved { background: #e74c3c; border-color: #e74c3c; color: white; }
        .property-location { color: #666; font-size: 16px; display: flex; align-items: center; gap: 8px; }
        .property-status { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 14px; font-weight: 600; margin-top: 10px; }
        .status-available { background: #d4edda; color: #155724; }
        .status-occupied { background: #fff3cd; color: #856404; }
        
        /* Gallery */
        .gallery-section { margin-bottom: 40px; }
        .main-image { width: 100%; height: 500px; border-radius: 12px; overflow: hidden; margin-bottom: 15px; cursor: pointer; background: #f5f5f5; }
        .main-image img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
        .main-image:hover img { transform: scale(1.05); }
        .thumbnail-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; }
        .thumbnail { height: 80px; border-radius: 8px; overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: all 0.2s; background: #f5f5f5; }
        .thumbnail.active { border-color: #0077b6; }
        .thumbnail img { width: 100%; height: 100%; object-fit: cover; }
        .image-counter { margin-top: 10px; font-size: 13px; color: #666; text-align: center; }
        
        /* Property Content */
        .property-content { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        .info-card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 25px; }
        .info-card h3 { font-size: 20px; margin-bottom: 15px; color: #333; display: flex; align-items: center; gap: 10px; }
        .info-card h3 i { color: #0077b6; }
        .price-section { background: linear-gradient(135deg, #0077b6 0%, #00a8e8 100%); color: white; }
        .price-section h2 { font-size: 36px; margin-bottom: 10px; }
        .property-meta { display: flex; gap: 25px; margin-top: 15px; flex-wrap: wrap; }
        .property-meta span { display: flex; align-items: center; gap: 8px; font-size: 14px; color: rgba(255,255,255,0.9); }
        .features-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .feature-item { display: flex; align-items: center; gap: 10px; padding: 8px; background: #f8f9fa; border-radius: 6px; font-size: 14px; }
        .feature-item i { color: #27ae60; }
        
        /* Landlord Info */
        .landlord-info { display: flex; gap: 20px; align-items: center; }
        .landlord-avatar { width: 80px; height: 80px; background: linear-gradient(135deg, #0077b6, #00a8e8); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; color: white; }
        .landlord-contact { margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 8px; }
        .landlord-contact p { margin: 5px 0; font-size: 14px; }
        .landlord-contact i { width: 20px; margin-right: 8px; color: #0077b6; }
        
        /* Buttons */
        .action-buttons { display: flex; gap: 15px; margin-top: 20px; }
        .btn-primary, .btn-outline { flex: 1; padding: 14px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s; }
        .btn-primary { background: #0077b6; color: white; border: none; }
        .btn-primary:hover { background: #005a8c; }
        .btn-outline { background: white; color: #0077b6; border: 2px solid #0077b6; }
        .btn-outline:hover { background: #0077b6; color: white; }
        
        /* Forms */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 500; color: #333; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: border-color 0.2s; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #0077b6; }
        .form-group small { display: block; margin-top: 5px; font-size: 12px; color: #666; }
        
        /* Modal */
        .modal { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 3000; align-items: center; justify-content: center; backdrop-filter: blur(5px); }
        .modal.active { display: flex; }
        .modal-content { background: white; border-radius: 20px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; animation: slideUp 0.3s ease; }
        .modal-header { padding: 24px 30px; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #0077b6 0%, #00a8e8 100%); border-radius: 20px 20px 0 0; }
        .modal-header h3 { font-size: 22px; font-weight: 600; color: white; display: flex; align-items: center; gap: 10px; }
        .modal-close { background: rgba(255,255,255,0.2); border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; color: white; font-size: 20px; transition: all 0.3s; }
        .modal-close:hover { transform: rotate(90deg); background: rgba(255,255,255,0.3); }
        .modal-body { padding: 30px; }
        .modal-footer { padding: 20px 30px; border-top: 1px solid #e9ecef; display: flex; justify-content: flex-end; gap: 15px; background: #f8f9fa; border-radius: 0 0 20px 20px; }
        .property-info-card { background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%); border-radius: 12px; padding: 20px; margin-bottom: 25px; border: 1px solid #e9ecef; }
        .property-info-card h4 { font-size: 16px; color: #0077b6; margin-bottom: 10px; }
        .property-info-card .property-detail { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e9ecef; }
        .payment-status { margin-top: 20px; padding: 15px; border-radius: 10px; display: none; align-items: center; gap: 10px; font-size: 14px; }
        .payment-status.success { background: #d4edda; color: #155724; display: flex; }
        .payment-status.error { background: #f8d7da; color: #721c24; display: flex; }
        .payment-status.info { background: #d1ecf1; color: #0c5460; display: flex; }
        
        /* Toast */
        .toast-notification { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%) translateY(100px); background: #333; color: white; padding: 12px 24px; border-radius: 8px; font-size: 14px; z-index: 4000; opacity: 0; transition: transform 0.3s, opacity 0.3s; }
        .toast-notification.show { transform: translateX(-50%) translateY(0); opacity: 1; }
        .toast-notification.success { background: #27ae60; }
        .toast-notification.error { background: #e74c3c; }
        
        /* Footer */
        .footer { background: #2c3e50; color: white; margin-top: 60px; text-align: center; padding: 20px; }
        
        @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @media (max-width: 768px) {
            .nav-menu { display: none; }
            .mobile-menu-btn { display: block; }
            .property-content { grid-template-columns: 1fr; }
            .main-image { height: 300px; }
            .thumbnail-grid { grid-template-columns: repeat(3, 1fr); }
            .action-buttons { flex-direction: column; }
            .property-header { flex-direction: column; }
            .save-property-btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <a href="index.php"><i class="fas fa-home"></i><span>SmartHunt</span></a>
            </div>
            <div class="nav-menu">
                <a href="index.php" class="nav-link">Browse</a>
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php" class="nav-link">My Profile</a>
                    <a href="logout.php" class="nav-link" onclick="return confirm('Logout?')">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-link">Login</a>
                    <a href="register.php" class="nav-button">Sign Up</a>
                <?php endif; ?>
            </div>
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()"><i class="fas fa-bars"></i></button>
        </div>
    </nav>

    <main class="container">
        <div class="property-header">
            <div>
                <h1><?php echo htmlspecialchars($property['property_name']); ?></h1>
                <p class="property-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars(formatFullAddress($property)); ?></p>
                <?php if (!empty($property['status'])): ?>
                <span class="property-status status-<?php echo strtolower($property['status']); ?>"><?php echo ucfirst($property['status']); ?></span>
                <?php endif; ?>
            </div>
            <button class="save-property-btn <?php echo $isSaved ? 'saved' : ''; ?>" onclick="toggleSaveProperty()" <?php echo !$isLoggedIn ? 'disabled' : ''; ?>>
                <i class="<?php echo $isSaved ? 'fas' : 'far'; ?> fa-heart"></i><span><?php echo $isSaved ? 'Saved' : 'Save Property'; ?></span>
            </button>
        </div>

        <!-- Image Gallery -->
        <div class="gallery-section">
            <div class="main-image" onclick="openLightbox()">
                <img id="mainImage" src="<?php echo !empty($propertyImages) ? getPropertyImageUrl($propertyImages[0]['image_path']) : 'assets/icons/bed.jpg'; ?>" alt="<?php echo htmlspecialchars($property['property_name']); ?>">
            </div>
            <?php if (count($propertyImages) > 1): ?>
            <div class="thumbnail-grid" id="thumbnailGrid">
                <?php foreach ($propertyImages as $index => $image): ?>
                <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeMainImage('<?php echo getPropertyImageUrl($image['image_path']); ?>', this)">
                    <img src="<?php echo getPropertyImageUrl($image['image_path']); ?>" alt="Thumbnail <?php echo $index + 1; ?>">
                </div>
                <?php endforeach; ?>
            </div>
            <div class="image-counter"><i class="fas fa-images"></i> <?php echo count($propertyImages); ?> photos</div>
            <?php endif; ?>
        </div>

        <div class="property-content">
            <div class="property-info">
                <div class="info-card price-section">
                    <h2>Ksh <?php echo number_format($property['monthly_rent'] ?? 0, 2); ?> <span class="period">/month</span></h2>
                    <div class="property-meta">
                        <?php if (!empty($property['bedrooms'])): ?><span><i class="fas fa-bed"></i> <?php echo $property['bedrooms']; ?> bed</span><?php endif; ?>
                        <?php if (!empty($property['bathrooms'])): ?><span><i class="fas fa-bath"></i> <?php echo $property['bathrooms']; ?> bath</span><?php endif; ?>
                        <?php if (!empty($property['sqft'])): ?><span><i class="fas fa-ruler-combined"></i> <?php echo number_format($property['sqft']); ?> sqft</span><?php endif; ?>
                        <?php if (!empty($property['property_type'])): ?><span><i class="fas fa-building"></i> <?php echo ucfirst($property['property_type']); ?></span><?php endif; ?>
                    </div>
                </div>

                <div class="info-card">
                    <h3><i class="fas fa-align-left"></i> Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($property['description'] ?? 'No description available.')); ?></p>
                </div>

                <div class="info-card">
                    <h3><i class="fas fa-check-circle"></i> Features & Amenities</h3>
                    <div class="features-grid">
                        <?php foreach (getPropertyFeatures($property) as $feature): ?>
                        <div class="feature-item"><i class="fas fa-check-circle"></i><span><?php echo htmlspecialchars($feature); ?></span></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div>
                <div class="info-card">
                    <h3><i class="fas fa-handshake"></i> Take Action</h3>
                    <div class="action-buttons">
                        <button class="btn-primary" onclick="openRentRequestModal()"><i class="fas fa-paper-plane"></i> Request to Rent</button>
                        <button class="btn-outline" onclick="openPaymentModal()"><i class="fas fa-mobile-alt"></i> Pay Rent</button>
                    </div>
                </div>

                <div class="info-card">
                    <h3><i class="fas fa-user-circle"></i> Property Owner</h3>
                    <div class="landlord-info">
                        <div class="landlord-avatar"><i class="fas fa-user-circle"></i></div>
                        <div class="landlord-details">
                            <h4><?php echo htmlspecialchars($landlordInfo['name']); ?></h4>
                            <div class="landlord-contact">
                                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($landlordInfo['phone']); ?></p>
                                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($landlordInfo['email']); ?></p>
                                <?php if (!empty($landlordInfo['mpesa']) && $landlordInfo['mpesa'] !== 'Not provided'): ?>
                                <p><i class="fas fa-mobile-alt"></i> M-Pesa: <?php echo htmlspecialchars($landlordInfo['mpesa']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Rent Request Modal -->
    <div id="rentRequestModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-paper-plane"></i> Request to Rent</h3>
                <button class="modal-close" onclick="closeRentRequestModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form id="rentRequestForm">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" id="name" placeholder="Enter your full name" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" id="email" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="tel" id="phone" placeholder="e.g., 0712345678" required>
                        <small>We'll use this to contact you</small>
                    </div>
                    <div class="form-group">
                        <label>Desired Move-in Date *</label>
                        <input type="date" id="moveDate" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Message to Landlord</label>
                        <textarea id="message" rows="4" placeholder="Tell the landlord about yourself..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn-outline" onclick="closeRentRequestModal()">Cancel</button>
                <button class="btn-primary" onclick="submitRentRequest()">Send Request</button>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-mobile-alt"></i> Pay Rent via M-Pesa</h3>
                <button class="modal-close" onclick="closePaymentModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="property-info-card">
                    <h4><i class="fas fa-home"></i> Property Details</h4>
                    <div class="property-detail"><span class="detail-label">Property:</span><span class="detail-value"><?php echo htmlspecialchars($property['property_name']); ?></span></div>
                    <div class="property-detail"><span class="detail-label">Rent:</span><span class="detail-value">KES <?php echo number_format($property['monthly_rent'], 2); ?></span></div>
                    <div class="property-detail"><span class="detail-label">Landlord:</span><span class="detail-value"><?php echo htmlspecialchars($landlordInfo['name']); ?></span></div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-mobile-alt"></i> M-Pesa Phone Number *</label>
                    <input type="tel" id="payment_phone" placeholder="e.g., 0712345678" required>
                    <small>You'll receive an STK push to complete payment</small>
                </div>
                <div id="payment_status" class="payment-status"></div>
            </div>
            <div class="modal-footer">
                <button class="btn-outline" onclick="closePaymentModal()">Cancel</button>
                <button class="btn-primary" id="payment_submit_btn" onclick="initiatePayment()">Pay KES <?php echo number_format($property['monthly_rent'], 2); ?></button>
            </div>
        </div>
    </div>

    <!-- Lightbox -->
    <div id="lightbox" class="modal">
        <div class="modal-content" style="max-width: 90%; background: transparent; box-shadow: none; text-align: center;">
            <button class="modal-close" style="position: absolute; top: 20px; right: 30px; background: rgba(0,0,0,0.5);" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
            <img id="lightboxImage" style="max-width: 90%; max-height: 90vh; object-fit: contain;">
        </div>
    </div>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> SmartHunt. All rights reserved.</p>
    </footer>

    <div id="toast" class="toast-notification"></div>

    <script>
        // Mobile menu
        function toggleMobileMenu() {
            document.getElementById('mobileMenu')?.classList.toggle('active');
        }

        // Toast notification
        function showToast(message, isError = false) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.style.backgroundColor = isError ? '#e74c3c' : '#333';
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        // Image gallery
        function changeMainImage(src, element) {
            document.getElementById('mainImage').src = src;
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            element.classList.add('active');
        }

        function openLightbox() {
            const img = document.getElementById('lightboxImage');
            img.src = document.getElementById('mainImage').src;
            document.getElementById('lightbox').classList.add('active');
        }

        function closeLightbox() {
            document.getElementById('lightbox').classList.remove('active');
        }

        // Save property
        function toggleSaveProperty() {
            <?php if (!$isLoggedIn): ?>
                showToast('Please login to save properties', true);
                setTimeout(() => window.location.href = 'login.php?redirect=property.php?id=<?php echo $property['id']; ?>', 1500);
                return;
            <?php endif; ?>

            const btn = document.querySelector('.save-property-btn');
            btn.disabled = true;
            
            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=toggle_save&property_id=<?php echo $property['id']; ?>'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const icon = btn.querySelector('i');
                    const text = btn.querySelector('span');
                    if (data.saved) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        btn.classList.add('saved');
                        text.textContent = 'Saved';
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        btn.classList.remove('saved');
                        text.textContent = 'Save Property';
                    }
                    showToast(data.message);
                }
            })
            .finally(() => btn.disabled = false);
        }

        // Rent Request
        function openRentRequestModal() {
            document.getElementById('rentRequestModal').classList.add('active');
        }

        function closeRentRequestModal() {
            document.getElementById('rentRequestModal').classList.remove('active');
        }

        function submitRentRequest() {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const moveDate = document.getElementById('moveDate').value;
            const message = document.getElementById('message').value.trim();
            
            if (!name || !email || !phone || !moveDate) {
                showToast('Please fill all required fields', true);
                return;
            }
            
            const phoneRegex = /^(07|01)[0-9]{8}$/;
            if (!phoneRegex.test(phone)) {
                showToast('Enter a valid Kenyan phone number', true);
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'send_rent_request');
            formData.append('name', name);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('moveDate', moveDate);
            formData.append('message', message);
            
            const submitBtn = event.target;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            submitBtn.disabled = true;
            
            fetch(window.location.href, { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                        closeRentRequestModal();
                        document.getElementById('rentRequestForm').reset();
                    } else {
                        showToast(data.message, true);
                    }
                })
                .catch(() => showToast('An error occurred', true))
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        // Payment
        let paymentCheckInterval = null;

        function openPaymentModal() {
            document.getElementById('paymentModal').classList.add('active');
            document.getElementById('payment_phone').value = '';
            document.getElementById('payment_status').style.display = 'none';
            const btn = document.getElementById('payment_submit_btn');
            btn.disabled = false;
            btn.innerHTML = 'Pay KES <?php echo number_format($property['monthly_rent'], 2); ?>';
        }

        function closePaymentModal() {
            if (paymentCheckInterval) clearInterval(paymentCheckInterval);
            document.getElementById('paymentModal').classList.remove('active');
        }

        function initiatePayment() {
            const phone = document.getElementById('payment_phone').value.trim();
            const phoneRegex = /^(07|01)[0-9]{8}$/;
            
            if (!phone) {
                showToast('Enter your M-Pesa phone number', true);
                return;
            }
            if (!phoneRegex.test(phone)) {
                showToast('Enter a valid Kenyan phone number', true);
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'initiate_payment');
            formData.append('phone_number', phone);
            formData.append('amount', <?php echo $property['monthly_rent']; ?>);
            
            const submitBtn = document.getElementById('payment_submit_btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;
            
            const statusDiv = document.getElementById('payment_status');
            statusDiv.className = 'payment-status info';
            statusDiv.innerHTML = '<i class="fas fa-clock"></i> Initiating payment...';
            statusDiv.style.display = 'flex';
            
            fetch(window.location.href, { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message);
                        statusDiv.innerHTML = '<i class="fas fa-clock"></i> Payment initiated. Waiting for confirmation...';
                        
                        let checkCount = 0;
                        paymentCheckInterval = setInterval(() => {
                            checkCount++;
                            if (checkCount > 12) {
                                clearInterval(paymentCheckInterval);
                                statusDiv.className = 'payment-status warning';
                                statusDiv.innerHTML = '<i class="fas fa-clock"></i> Still processing. You will receive an SMS.';
                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;
                                return;
                            }
                            checkPaymentStatus(data.payment_id);
                        }, 5000);
                    } else {
                        showToast(data.message, true);
                        statusDiv.className = 'payment-status error';
                        statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> ' + data.message;
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }
                })
                .catch(() => {
                    showToast('An error occurred', true);
                    statusDiv.className = 'payment-status error';
                    statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> An error occurred';
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        }

        function checkPaymentStatus(paymentId) {
            const formData = new FormData();
            formData.append('action', 'check_payment_status');
            formData.append('payment_id', paymentId);
            
            fetch(window.location.href, { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    const statusDiv = document.getElementById('payment_status');
                    const submitBtn = document.getElementById('payment_submit_btn');
                    
                    if (data.status === 'completed') {
                        clearInterval(paymentCheckInterval);
                        statusDiv.className = 'payment-status success';
                        statusDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                        setTimeout(() => {
                            closePaymentModal();
                            location.reload();
                        }, 3000);
                    } else if (data.status === 'failed') {
                        clearInterval(paymentCheckInterval);
                        statusDiv.className = 'payment-status error';
                        statusDiv.innerHTML = '<i class="fas fa-times-circle"></i> ' + data.message;
                        submitBtn.innerHTML = 'Pay Now';
                        submitBtn.disabled = false;
                    } else {
                        statusDiv.innerHTML = '<i class="fas fa-clock"></i> ' + data.message;
                    }
                });
        }

        // Close modals on outside click
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) this.classList.remove('active');
            });
        });

        // Set min date for move-in
        document.getElementById('moveDate').min = new Date().toISOString().split('T')[0];

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'));
            }
            if (e.key === 's' && !e.ctrlKey && !e.metaKey && <?php echo $isLoggedIn ? 'true' : 'false'; ?>) {
                e.preventDefault();
                toggleSaveProperty();
            }
        });
    </script>
</body>
</html>