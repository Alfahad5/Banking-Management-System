<?php
include '../db_connection.php';

$email = 'alfahad.infoseek@gmail.com';
$name = 'Admin';
$password = 1;
// $password = password_hash('1', PASSWORD_BCRYPT);

// Insert into admin table
$stmt = $conn->prepare("INSERT INTO `admin`(`name`, `email`, `password`) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $password);

if ($stmt->execute()) {
    echo "Admin account created successfully.";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
