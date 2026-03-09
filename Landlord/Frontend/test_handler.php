<?php
// test_handler.php
session_start();
header('Content-Type: application/json');

// Log everything
error_log("=== TEST HANDLER ===");
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);

$response = [
    'success' => true,
    'message' => 'Test handler working',
    'method' => $_SERVER['REQUEST_METHOD'],
    'post_data' => $_POST,
    'session' => isset($_SESSION['landlord_id']) ? 'logged in' : 'not logged in'
];

echo json_encode($response);
?>