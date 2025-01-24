<?php
session_start();

include("index.php");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="property-form">
        <h1>Add Property</h1>
        <form action="submit_property.php" method="post" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="property-name">Property Name</label>
                    <input type="text" id="property-name" name="property_name" placeholder="e.g., Tripple A" required>
                </div>
                <div class="form-group">
                    <label for="property-type">Property Type</label>
                    <select id="property-type" name="property_type" required>
                        <option value="Single Rooms">Single Rooms</option>
                        <option value="Bedsitters">Bedsitters</option>
                        <option value="Single Rooms & Bedsitters">Single Rooms & Bedsitters</option>
                        <option value="1B">1B</option>
                        <option value="2B">2B</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" placeholder="e.g., 123 Main Gate, Campus" required>
                </div>
                <div class="form-group">
                    <label for="description">Property Description</label>
                    <textarea id="description" name="property_description" rows="4" placeholder="Enter a brief description of the property"></textarea>
                </div>
                <div class="form-group">
                    <label for="rooms">Number of Rooms</label>
                    <input type="number" id="rooms" name="number_of_rooms" min="1" required>
                </div>
                <div class="form-group">
                    <label for="price">Price (in Ksh)</label>
                    <input type="number" id="price" name="price" placeholder="e.g., 5000" min="0" required>
                </div>
                <div class="form-group">
                    <label for="rules">Upload Property Rules</label>
                    <input type="file" id="rules" name="property_rules[]" accept=".pdf, .doc, .docx, .txt, .ppt" multiple required>
                </div>
                <div class="form-group">
                    <label for="photos">Upload Photos</label>
                    <input type="file" id="photos" name="property_photos[]" accept="image/*" multiple required>
                </div>
            </div>
            <button type="submit" class="submit-btn">Submit Property</button>
        </form>
    </div>
</body>
</html>
