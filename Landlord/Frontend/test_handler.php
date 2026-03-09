<?php
// test_handler.php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'message' => 'Test handler working',
    'session' => $_SESSION ?? [],
    'post' => $_POST ?? [],
    'files' => $_FILES ?? []
]);