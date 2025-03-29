<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "../db_connection.php";

// Initialize error message
$error = "";

// Handle login request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if fields are empty
    if (empty($email) || empty($password)) {
        $error = "Please fill in both email and password.";
    } else {
        // Check admin credentials first
        $admin_email = 'alfahad.infoseek@gmail.com';
        $admin_pass = '1';  // Update with the correct admin password if needed

        // If the login is for admin
        if ($email === $admin_email && $admin_pass === $password) {
            // Admin login successful
            $_SESSION['email'] = $admin_email;
            $_SESSION['role'] = 'admin';
            header("Location: ../admin/admin_dashboard.php");
            exit();
        }

        // Check user credentials
        $stmt = $conn->prepare("SELECT * FROM `users` WHERE `email` = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        // Verify password for user
        if ($user && password_verify($password, $user['password'])) {
            // User login successful
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = 'user';
            header("Location: ../user/user_dashboard.php");
            exit();
        }

        // If both checks fail
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-cover bg-center min-h-screen flex items-center justify-center"
    style="background-image: url('../img/bank2.jpg');">

    <div class="w-full max-w-md bg-white bg-opacity-90 p-8 rounded-lg shadow-lg backdrop-blur-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Sign In</h1>

        <!-- Display error message -->
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Sign-in Form -->
        <form action="sign-in.php" method="POST" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required
                    class="w-full px-3 py-2 border rounded shadow-sm focus:ring focus:ring-indigo-200">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-3 py-2 border rounded shadow-sm focus:ring focus:ring-indigo-200">
            </div>
            <button type="submit"
                class="w-full bg-indigo-600 text-white py-2 rounded shadow hover:bg-indigo-700 focus:outline-none">Sign
                In</button>
        </form>

        <p class="mt-4 text-center text-sm text-gray-600">
            Don't have an account? <a href="sign-up.php" class="text-indigo-600 hover:text-indigo-900">Sign Up</a>
        </p>
    </div>
</body>

</html>