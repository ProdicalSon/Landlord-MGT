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
$password2 = $_POST['password'];

$query = "INSERT INTO users  (username,email,passwordd) VALUES('$username', '$email', '$password2')";
$result = mysqli_query($connection, $query);

?>