<?php
$server = 'localhost';
$username = "root";
$password = "1457Debora";
$database = "landlordusers";


$connection = mysqli_connect($server, $username, $password, $database);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password2 = $_POST['passwordd'];
    $hashed_password = password_hash($password2, PASSWORD_BCRYPT);

    
    $query = "INSERT INTO landlordd (username, email, passwordd) VALUES ('$username', '$email', '$hashed_password')";
    $result = mysqli_query($connection, $query);

    if ($result) {
        echo "Registration successful!";
        header("Location: loginapproval.php");
        exit();
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($connection);
    }
}

mysqli_close($connection);
?>
