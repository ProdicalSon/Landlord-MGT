<?php
// admin/minimal.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Minimal Test</h1>";

// Test database connection
require_once __DIR__ . '/config/database.php';
$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    echo "<p style='color:green'>✓ Database connection successful</p>";
} else {
    echo "<p style='color:red'>✗ Database connection failed</p>";
}

// Test including a model
echo "<p>Testing model includes...</p>";
require_once __DIR__ . '/models/AdminModel.php';
echo "<p>✓ AdminModel loaded</p>";

require_once __DIR__ . '/models/UserModel.php';
echo "<p>✓ UserModel loaded</p>";

require_once __DIR__ . '/models/PropertyModel.php';
echo "<p>✓ PropertyModel loaded</p>";

require_once __DIR__ . '/models/PaymentModel.php';
echo "<p>✓ PaymentModel loaded</p>";

// Test creating instances
$adminModel = new AdminModel();
echo "<p>✓ AdminModel instantiated</p>";

$userModel = new UserModel();
echo "<p>✓ UserModel instantiated</p>";

$propertyModel = new PropertyModel();
echo "<p>✓ PropertyModel instantiated</p>";

$paymentModel = new PaymentModel();
echo "<p>✓ PaymentModel instantiated</p>";

// Test a simple query
try {
    $stats = $adminModel->getDashboardStats();
    echo "<p>✓ getDashboardStats() executed</p>";
    echo "<pre>Stats: " . print_r($stats, true) . "</pre>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Testing includes/auth.php</h2>";
require_once __DIR__ . '/includes/auth.php';
echo "<p>✓ auth.php loaded</p>";
echo "<p>Session admin_id: " . ($_SESSION['admin_id'] ?? 'not set') . "</p>";