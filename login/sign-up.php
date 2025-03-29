<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "../db_connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Insert into requests table
    $stmt = $conn->prepare("INSERT INTO `requests`(`name`, `email`, `password`, `status`) VALUES (?, ?, ?, 'waiting')");
    $stmt->bind_param("sss", $name, $email, $password);
    $stmt->execute();

    $_SESSION['name'] = $name;
    $_SESSION['status'] = 'waiting';
    $_SESSION['email'] = $email;

    header("Location: status.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <!-- Correct Tailwind CSS Import -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">


</head>

<body class="bg-cover bg-center min-h-screen flex items-center justify-center"
    style="background-image: url('../img/bank2.jpg');">

    <div class="w-full max-w-md bg-white bg-opacity-90 p-8 rounded-lg shadow-lg backdrop-blur-md">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Sign Up</h2>

        <form id="signup-form" method="POST" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-600 mb-1">Name</label>
                <input type="text" id="name" name="name"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    required>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                <input type="email" id="email" name="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    required>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-600 mb-1">Password</label>
                <input type="password" id="password" name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    required>
            </div>

            <button type="submit"
                class="w-full py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">Sign
                Up</button>
        </form>

        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">Already have an account? <a href="sign-in.php"
                    class="text-blue-500 hover:underline">Sign-In</a></p>
        </div>


    </div>
</body>

</html>