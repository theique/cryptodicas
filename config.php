<?php

$con = mysqli_connect("localhost","theique","123Monk!","principal");
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

$conn = new mysqli("localhost","theique","123Monk!","principal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

?>
