<?php


$server = 'localhost';
$username = "root";
$password = "1457Debora";
$database = "landlordusers";


$connection = mysqli_connect($server,$username,$password,$database);

if($connection){
  echo "connection successful";
}else{
  echo "failed";
}

$username = $_POST['username'];
$email = $_POST['email'];
$password2 = $_POST['passwordd'];
$hashed_password = password_hash($password2, PASSWORD_BCRYPT);

$query = "INSERT INTO landlordd  (username,email,passwordd) VALUES('$username', '$email', '$password2')";
$result = mysqli_query($connection, $query);


if ($result) {
    echo "Registration successful!";
} else {
    echo "Error: " . $query . "<br>" . mysqli_error($connection);
}


mysqli_close($connection);

?>