<?php 

$servername = "localhost";
$username = "adex";
$password = "adexganteng";
$db ="indonesia_wilaya";

// Create connection
$conn = new mysqli($servername, $username, $password,$db);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>

