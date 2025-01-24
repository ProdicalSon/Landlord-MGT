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
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    $query = "SELECT * FROM landlordd WHERE email = '$email'";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        
        if (password_verify($password, $user['passwordd'])) {
            
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            
            header("Location: index.php");
            exit();
        } else {
            echo "Invalid email or password!";
        }
    } else {
        echo "Invalid email or password!";
    }
}

mysqli_close($connection);
?>
