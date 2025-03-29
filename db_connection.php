<?php
$conn = mysqli_connect("localhost", "root", "", "bank");

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}