<?php
// add_property_handler.php
session_start();
header('Content-Type: application/json');

// Log everything for debugging
error_log("=== Add Property Handler Started ===");
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));
error_log("GET data: " . print_r($_GET, true));
error_log("Session: " . print_r($_SESSION, true));

// Check if user is logged in
if (!isset($_SESSION['landlord_id'])) {
    error_log("ERROR: No landlord_id in session");
    echo json_encode([
        'success' => false, 
        'message' => 'Please login as a landlord to add properties'
    ]);
    exit;
}

$landlord_id = $_SESSION['landlord_id'];
error_log("Landlord ID: " . $landlord_id);

// Handle POST request (adding property)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Processing POST request - Adding property");
    
    // Include the model
    require_once __DIR__ . '/models/LandlordPropertyModel.php';
    
    $propertyModel = new LandlordPropertyModel();
    
    // Get form data (try different possible field names)
    $title = trim($_POST['property-name'] ?? $_POST['title'] ?? '');
    $type = $_POST['property-type'] ?? $_POST['type'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $status = $_POST['status'] ?? 'available';
    $description = trim($_POST['description'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $neighborhood = trim($_POST['neighborhood'] ?? '');
    $zip = trim($_POST['zip'] ?? $_POST['zip-code'] ?? '');
    $bedrooms = $_POST['bedrooms'] ?? '1';
    $bathrooms = $_POST['bathrooms'] ?? '1';
    $area = floatval($_POST['area'] ?? 0);
    $year_built = isset($_POST['year-built']) && !empty($_POST['year-built']) ? intval($_POST['year-built']) : null;
    
    // Get amenities
    $amenities = [];
    if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
        $amenities = $_POST['amenities'];
    }
    
    error_log("Processed data - Title: $title, Type: $type, Price: $price");
    
    // Validate required fields
    $errors = [];
    if (empty($title)) $errors[] = 'Property title is required';
    if (empty($type)) $errors[] = 'Property type is required';
    if ($price <= 0) $errors[] = 'Valid monthly rent is required';
    if (empty($description)) $errors[] = 'Property description is required';
    if (empty($address)) $errors[] = 'Street address is required';
    if (empty($city)) $errors[] = 'City is required';
    if (empty($neighborhood)) $errors[] = 'Neighborhood is required';
    if ($area <= 0) $errors[] = 'Valid area is required';
    
    if (!empty($errors)) {
        error_log("Validation errors: " . implode(', ', $errors));
        echo json_encode(['success' => false, 'message' => implode('<br>', $errors)]);
        exit;
    }
    
    // Prepare data for database
    $propertyData = [
        'title' => $title,
        'type' => $type,
        'price' => $price,
        'status' => $status,
        'description' => $description,
        'address' => $address,
        'city' => $city,
        'neighborhood' => $neighborhood,
        'zip' => $zip,
        'bedrooms' => $bedrooms == '5+' ? 5 : intval($bedrooms),
        'bathrooms' => $bathrooms == '5+' ? 5 : floatval($bathrooms),
        'area' => $area,
        'year_built' => $year_built,
        'amenities' => $amenities,
        'featured' => 0
    ];
    
    // Add property to database
    $result = $propertyModel->addProperty($propertyData, $landlord_id);
    error_log("addProperty result: " . print_r($result, true));
    
    echo json_encode($result);
    exit;
}

// Handle GET request (fetching properties)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_properties') {
    error_log("Processing GET request - Fetching properties");
    
    require_once __DIR__ . '/models/LandlordPropertyModel.php';
    $propertyModel = new LandlordPropertyModel();
    
    $properties = $propertyModel->getLandlordProperties($landlord_id);
    $stats = $propertyModel->getPropertyStats($landlord_id);
    
    echo json_encode([
        'success' => true,
        'properties' => $properties,
        'stats' => $stats
    ]);
    exit;
}

// If we get here, it's an invalid request
error_log("ERROR: Invalid request - No matching condition");
echo json_encode([
    'success' => false,
    'message' => 'Invalid request. Method: ' . $_SERVER['REQUEST_METHOD']
]);
exit;
?>