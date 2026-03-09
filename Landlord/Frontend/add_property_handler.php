<?php
// add_property_handler.php
session_start();

// Check if user is logged in and is a landlord
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'landlord') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Please login as a landlord to add properties']);
    exit;
}

require_once __DIR__ . '/models/LandlordPropertyModel.php';

$landlord_id = $_SESSION['user_id'];
$propertyModel = new LandlordPropertyModel();

// Handle AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Get form data
    $title = trim($_POST['title'] ?? '');
    $type = $_POST['type'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $status = $_POST['status'] ?? 'available';
    $description = trim($_POST['description'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $neighborhood = trim($_POST['neighborhood'] ?? '');
    $zip = trim($_POST['zip'] ?? '');
    $bedrooms = $_POST['bedrooms'] ?? '1';
    $bathrooms = $_POST['bathrooms'] ?? '1';
    $area = floatval($_POST['area'] ?? 0);
    $year_built = isset($_POST['year_built']) && !empty($_POST['year_built']) ? intval($_POST['year_built']) : null;
    
    // Get amenities
    $amenities = [];
    if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
        $amenities = $_POST['amenities'];
    }
    
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
        'featured' => 0 // Default not featured
    ];
    
    // Add property to database
    $result = $propertyModel->addProperty($propertyData, $landlord_id);
    
    echo json_encode($result);
    exit;
}

// Handle GET request to fetch properties
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_properties') {
    header('Content-Type: application/json');
    
    $properties = $propertyModel->getLandlordProperties($landlord_id);
    $stats = $propertyModel->getPropertyStats($landlord_id);
    
    // Format properties for display
    foreach ($properties as &$property) {
        $property = $propertyModel->formatPropertyForDisplay($property);
    }
    
    echo json_encode([
        'success' => true,
        'properties' => $properties,
        'stats' => $stats
    ]);
    exit;
}

// Handle DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    header('Content-Type: application/json');
    
    $property_id = intval($_GET['id']);
    
    // Verify ownership
    if (!$propertyModel->verifyOwnership($property_id, $landlord_id)) {
        echo json_encode(['success' => false, 'message' => 'You do not own this property']);
        exit;
    }
    
    $result = $propertyModel->deleteProperty($property_id);
    
    echo json_encode($result);
    exit;
}
?>