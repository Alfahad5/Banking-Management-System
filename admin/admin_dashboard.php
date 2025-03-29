<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/sign-in.php");
    exit();
}

include '../db_connection.php';


// Handle accept/reject actions for requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        // Fetch request details
        $stmt = $conn->prepare("SELECT name, email, password FROM `requests` WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        // Insert into users table
        $stmt = $conn->prepare("INSERT INTO `users`(`name`, `email`, `password`) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $result['name'], $result['email'], $result['password']);
        $stmt->execute();

        // Update request status
        $stmt = $conn->prepare("UPDATE `requests` SET `status` = 'accepted' WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif ($action === 'reject') {
        // Update request status to rejected
        $stmt = $conn->prepare("UPDATE `requests` SET `status` = 'rejected' WHERE `id` = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    header("Location: admin_dashboard.php");
    exit();
}

// Fetch pending requests
$requests = $conn->query("SELECT * FROM `requests` WHERE `status` = 'waiting'");

// Fetch total number of users
$userCountResult = $conn->query("SELECT COUNT(`user_id`) AS user_count FROM `users`");
$userCount = $userCountResult->fetch_assoc()['user_count'];

// Fetch total number of accounts
$accountCountResult = $conn->query("SELECT COUNT(`account_id`) AS account_count FROM `accounts`");
$accountCount = $accountCountResult->fetch_assoc()['account_count'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        .main-content {
            width: 85%;
            float: right;
        }

        @media (max-width: 768px) {
            .main-content {
                width: 100%;
                float: none;
            }
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content bg-gray-100 min-h-screen p-6">
        <div class="overflow-x-auto max-w-5xl mx-auto p-6 bg-white rounded shadow-md">
            <h1 class="text-2xl font-bold mb-6">Admin Dashboard</h1>

            <!-- Pending Requests Table -->
            <table class="table-auto w-full border-collapse border border-gray-300 mb-6">
                <thead>
                    <tr class="bg-blue-200">
                        <th class="border border-gray-300 px-4 py-2">Name</th>
                        <th class="border border-gray-300 px-4 py-2">Email</th>
                        <th class="border border-gray-300 px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $requests->fetch_assoc()): ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="border border-gray-300 px-4 py-2">
                                <form method="POST" class="flex gap-2 justify-center ">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                    <!-- Accept Button with Check Icon -->
                                    <button name="action" value="accept"
                                        class="bg-green-500 text-white px-3 py-2 rounded-full hover:bg-green-700 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <!-- Reject Button with Cross Icon -->
                                    <button name="action" value="reject"
                                        class="bg-red-500 text-white px-3 py-2 rounded-full hover:bg-red-700 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 9a1 1 0 00-.707.293l-6 6a1 1 0 001.414 1.414L10 11.414l5.293 5.293a1 1 0 001.414-1.414l-6-6A1 1 0 0010 9zm0-8a8 8 0 100 16 8 8 0 000-16z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>

                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Total Users and Accounts -->
            <div class="mt-6">
                <h2 class="text-xl font-semibold">Statistics</h2>
                <p class="mt-2">Total number of users: <span class="font-bold"><?= $userCount ?></span></p>
                <p>Total number of accounts: <span class="font-bold"><?= $accountCount ?></span></p>
            </div>
        </div>
    </div>
</body>

</html>