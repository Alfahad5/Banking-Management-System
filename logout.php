<?php

// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

if (isset($_GET['logout'])) {
    session_start();
    session_destroy(); // Destroy the session
    header("Location: login/sign-in.php"); // Redirect to login page
    exit();
}