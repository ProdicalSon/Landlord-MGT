<?php
echo "<h2>File Path Test</h2>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Script name: " . $_SERVER['SCRIPT_NAME'] . "</p>";

echo "<h3>Checking if files exist:</h3>";
$files = [
    'propertylistings.php',
    'view-tenant.php',
    'inquiries.php',
    'payments.php',
    'profile.php',
    'logout.php'
];

echo "<ul>";
foreach ($files as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    echo "<li>$file: " . ($exists ? '✅ EXISTS' : '❌ NOT FOUND') . "</li>";
}
echo "</ul>";
?>
