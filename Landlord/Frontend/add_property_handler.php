<?php
// add_property_handler.php
session_start();
header('Content-Type: application/json');

// Error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['landlord_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login as a landlord to add properties']);
    exit;
}

$landlord_id = $_SESSION['landlord_id'];

// Handle POST request (adding property)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Include the model
    require_once __DIR__ . '/models/LandlordPropertyModel.php';
    
    $propertyModel = new LandlordPropertyModel();
    
    // Get form data
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
        'featured' => 0
    ];
    
    // Add property to database
    $result = $propertyModel->addProperty($propertyData, $landlord_id);
    
    if ($result['success']) {
        $property_id = $result['property_id'];
        
        // Handle image uploads
        if (!empty($_FILES['property_photos']['name'][0])) {
            $uploadResult = uploadPropertyImages($property_id, $_FILES['property_photos']);
            if (!$uploadResult['success']) {
                // Images failed to upload but property was created
                $result['message'] .= ' But images failed to upload: ' . $uploadResult['message'];
            } else {
                $result['message'] .= ' And ' . $uploadResult['count'] . ' images uploaded.';
            }
        }
        
        // Handle rules/document uploads
        if (!empty($_FILES['property_rules']['name'][0])) {
            $rulesResult = uploadPropertyRules($property_id, $_FILES['property_rules']);
            // You can store rules in a separate table if needed
        }
    }
    
    echo json_encode($result);
    exit;
}

/**
 * Upload property images
 */
function uploadPropertyImages($property_id, $files) {
    $uploadDir = __DIR__ . '/uploads/properties/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $uploadedFiles = [];
    $errors = [];
    $firstImage = true;
    
    require_once __DIR__ . '/models/LandlordPropertyModel.php';
    $propertyModel = new LandlordPropertyModel();
    
    // Process each file
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $tmp_name = $files['tmp_name'][$i];
            $originalName = $files['name'][$i];
            
            // Generate unique filename
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $filename = 'property_' . $property_id . '_' . time() . '_' . $i . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            $fileType = mime_content_type($tmp_name);
            
            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = "$originalName is not an allowed image type";
                continue;
            }
            
            // Validate file size (max 5MB)
            if ($files['size'][$i] > 5 * 1024 * 1024) {
                $errors[] = "$originalName is too large (max 5MB)";
                continue;
            }
            
            // Move uploaded file
            if (move_uploaded_file($tmp_name, $filepath)) {
                // Save to database
                $dbPath = 'uploads/properties/' . $filename;
                $isPrimary = $firstImage ? 1 : 0;
                
                if ($propertyModel->savePropertyImage($property_id, $dbPath, $isPrimary)) {
                    $uploadedFiles[] = $dbPath;
                    $firstImage = false;
                } else {
                    $errors[] = "Failed to save $originalName to database";
                }
            } else {
                $errors[] = "Failed to move $originalName";
            }
        } else if ($files['error'][$i] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = "Error uploading file: " . $files['name'][$i];
        }
    }
    
    return [
        'success' => empty($errors),
        'count' => count($uploadedFiles),
        'files' => $uploadedFiles,
        'message' => empty($errors) ? 'All images uploaded' : implode(', ', $errors)
    ];
}

/**
 * Upload property rules/documents
 */
function uploadPropertyRules($property_id, $files) {
    $uploadDir = __DIR__ . '/uploads/rules/';
    
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $uploadedFiles = [];
    $errors = [];
    
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $tmp_name = $files['tmp_name'][$i];
            $originalName = $files['name'][$i];
            
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $filename = 'rules_' . $property_id . '_' . time() . '_' . $i . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
            $fileType = mime_content_type($tmp_name);
            
            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = "$originalName is not an allowed document type";
                continue;
            }
            
            if (move_uploaded_file($tmp_name, $filepath)) {
                $uploadedFiles[] = 'uploads/rules/' . $filename;
                // You can save to a property_rules table here if needed
            } else {
                $errors[] = "Failed to move $originalName";
            }
        }
    }
    
    return [
        'success' => empty($errors),
        'count' => count($uploadedFiles),
        'files' => $uploadedFiles
    ];
}

// Handle GET request (fetching properties)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_properties') {
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

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit;
?>