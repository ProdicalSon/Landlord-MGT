<?php

$servername = "localhost";
$username = "root";   
$password = "";       
$dbname = "smarthunt_db";

$conn = new mysqli($servername, $username, $password, $dbname, port: 3306);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT * FROM properties ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Properties Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; margin: 20px; }
        .property-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        .property-header { display: flex; justify-content: space-between; align-items: center; }
        .property-header h2 { margin: 0; font-size: 22px; color: #333; }
        .property-info { margin: 10px 0; color: #555; }
        .photos { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; }
        .photos img { width: 200px; height: 150px; object-fit: cover; border-radius: 8px; }
        .amenities { margin-top: 10px; }
        .amenity { display: inline-block; background: #e9ecef; padding: 6px 12px; margin: 5px; border-radius: 20px; font-size: 14px; }
        .price { font-weight: bold; color: green; font-size: 18px; }
    </style>
</head>
<body>
    <h1><i class="fas fa-list"></i> Properties Dashboard</h1>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="property-card">
                <div class="property-header">
                    <h2><?php echo htmlspecialchars($row['property_name']); ?></h2>
                    <span class="price">Ksh <?php echo number_format($row['price']); ?></span>
                </div>
                <div class="property-info">
                    <p><b>Type:</b> <?php echo htmlspecialchars($row['property_type']); ?></p>
                    <p><b>Location:</b> <?php echo htmlspecialchars($row['location']); ?></p>
                    <p><b>Rooms:</b> <?php echo htmlspecialchars($row['number_of_rooms']); ?></p>
                    <p><b>Description:</b> <?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                </div>

                <!-- Property Photos -->
                <div class="photos">
                    <?php
                    $photos = $conn->query("SELECT photo_path FROM property_photos WHERE property_id=" . $row['property_id']);
                    while ($photo = $photos->fetch_assoc()):
                    ?>
                        <img src="<?php echo htmlspecialchars($photo['photo_path']); ?>" alt="Property Photo">
                    <?php endwhile; ?>
                </div>

                <!-- Property Amenities -->
                <div class="amenities">
                    <b>Amenities:</b><br>
                    <?php
                    $amenities = $conn->query("SELECT amenity FROM property_amenities WHERE property_id=" . $row['property_id']);
                    while ($amenity = $amenities->fetch_assoc()):
                    ?>
                        <span class="amenity"><?php echo htmlspecialchars($amenity['amenity']); ?></span>
                    <?php endwhile; ?>
                </div>

                <!-- Rules document -->
                <?php if (!empty($row['rules_document'])): ?>
                    <p><b>Rules:</b> <a href="<?php echo htmlspecialchars($row['rules_document']); ?>" target="_blank">View Document</a></p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No properties found.</p>
    <?php endif; ?>

</body>
</html>
<?php $conn->close(); ?>
