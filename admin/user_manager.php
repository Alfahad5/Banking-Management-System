<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is an admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/sign-in.php");
    exit();
}

include "../db_connection.php"; // Include DB connection

// Handle delete user action
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];

    // Prepare and execute deletion query
    $stmt = $conn->prepare("DELETE FROM `users` WHERE `user_id` = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Redirect to user_manager page after deletion
    header("Location: user_manager.php");
    exit();
}

// Handle update user action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Prepare and execute update query
    $stmt = $conn->prepare("UPDATE `users` SET `name` = ?, `email` = ? WHERE `user_id` = ?");
    $stmt->bind_param("ssi", $name, $email, $user_id);
    $stmt->execute();

    // Redirect to user_manager page after update
    header("Location: user_manager.php");
    exit();
}

// Fetch all users from the database
$users = $conn->query("SELECT `user_id`, `name`, `email`, `password` FROM `users` WHERE 1");

// Check if we are editing a specific user
$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_user_id = $_GET['edit'];
    // Fetch the user to edit
    $stmt = $conn->prepare("SELECT `user_id`, `name`, `email` FROM `users` WHERE `user_id` = ?");
    $stmt->bind_param("i", $edit_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_user = $result->fetch_assoc();
}

// Now include the sidebar after the header logic
// Include the sidebar
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        .main-content {
            width: 85%;
            float: right;
        }

        @media (max-width: 768px) {
            #h {
                text-align: center;
            }

            .main-content {
                width: 100%;
                float: none;
            }
        }
    </style>
</head>

<body>
    <?php include "sidebar.php"; ?>

    <div class="main-content bg-gray-100 min-h-screen p-8">
        <div class="overflow-x-auto p-6 bg-white rounded shadow-md">
            <h1 id='h' class="text-2xl font-bold mb-4">User Manager</h1>

            <!-- Check if we are editing a user -->
            <?php if ($edit_user): ?>
                <h2 class="text-xl font-semibold mb-4">Edit User</h2>
                <form method="POST" action="user_manager.php">
                    <input type="hidden" name="user_id" value="<?= $edit_user['user_id'] ?>">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700">Name</label>
                        <input type="text" name="name" id="name" value="<?= htmlspecialchars($edit_user['name']) ?>"
                            class="border px-3 py-1 rounded w-full" required>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="<?= htmlspecialchars($edit_user['email']) ?>"
                            class="border px-3 py-1 rounded w-full" required>
                    </div>
                    <button type="submit" name="update"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update</button>
                </form>
            <?php else: ?>
                <!-- Users Table -->
                <table class="table-auto  w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-blue-200">
                            <th class="border border-gray-300 px-4 py-2">User ID</th>
                            <th class="border border-gray-300 px-4 py-2">Name</th>
                            <th class="border border-gray-300 px-4 py-2">Email</th>
                            <th class="border border-gray-300 px-4 py-2">Password</th>
                            <th class="border border-gray-300 px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <tr>
                                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($user['user_id']) ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($user['name']) ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($user['password']) ?></td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <!-- Edit and Delete buttons in a flex container to align them horizontally -->
                                    <div class="flex justify-center space-x-4">
                                        <!-- Edit Button with Icon -->
                                        <a href="user_manager.php?edit=<?= $user['user_id'] ?>"
                                            class="p-3   text-green-500 hover:bg-blue-100">
                                            <i class="fas fa-edit text-xl"></i> <!-- Font Awesome Edit Icon -->
                                        </a>
                                        <!-- Delete Button with Icon -->
                                        <a href="user_manager.php?delete=<?= $user['user_id'] ?>"
                                            class="p-3  text-red-500 hover:bg-blue-100"
                                            onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="fas fa-trash-alt text-xl"></i> <!-- Font Awesome Trash Icon -->
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>