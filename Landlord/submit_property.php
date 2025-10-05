<?php
$servername = "localhost";
$username = "root";   
$password = "";       
$dbname = "smarthunt_db";


$conn = new mysqli($servername, $username, $password, $dbname, 3306);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$conn->set_charset("utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $property_name        = $_POST['property_name'] ?? '';
    $property_type        = $_POST['property_type'] ?? '';
    $location             = $_POST['location'] ?? '';
    $number_of_rooms      = $_POST['number_of_rooms'] ?? 0;
    $price                = $_POST['price'] ?? 0;
    $property_description = $_POST['property_description'] ?? '';

   
    $amenities = isset($_POST['amenities']) ? implode(', ', $_POST['amenities']) : '';

   
    $photos = '';
    if (!empty($_FILES['property_photos']['name'][0])) {
        $photos = implode(', ', $_FILES['property_photos']['name']);
    }

    $rules_document = '';
    if (!empty($_FILES['property_rules']['name'][0])) {
        $rules_document = implode(', ', $_FILES['property_rules']['name']);
    }


    $stmt = $conn->prepare("
        INSERT INTO properties 
        (property_name, property_type, location, number_of_rooms, price, property_description, photos, rules_document, amenities) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssisssss", 
        $property_name, 
        $property_type, 
        $location, 
        $number_of_rooms, 
        $price, 
        $property_description, 
        $photos, 
        $rules_document, 
        $amenities
    );

    if ($stmt->execute()) {
        echo "<script>alert('✅ Property added successfully!'); window.location.href='your_form_page.php';</script>";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}
?>
