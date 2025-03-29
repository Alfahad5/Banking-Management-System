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

// Variable to hold success message
$success_message = "";

// Handle update account action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_amount'])) {
    $account_id = $_POST['account_id'];
    $amount = $_POST['amount'];

    // Prepare and execute update query for amount
    $stmt = $conn->prepare("UPDATE `accounts` SET `amount` = ? WHERE `account_id` = ?");

    if ($stmt === false) {
        die('SQL Error: ' . $conn->error);
    }

    $stmt->bind_param("di", $amount, $account_id);

    if ($stmt->execute()) {
        // Set success message
        $success_message = "Updated amount successfully!";
    } else {
        // If update fails, show error
        $success_message = "Error updating the amount: " . $stmt->error;
    }
}

// Fetch all accounts from the database
$accounts = $conn->query("SELECT `account_id`, `user_id`, `account_email`, `phone`, `account_type`, `pin`, `amount` FROM `accounts` WHERE 1");

// Include the sidebar after header logic
include "sidebar.php"; // Include the sidebar
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accounts</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .main-content {
            width: 85%;
            float: right;
        }

        .success-message {
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .main-content {
                width: 100%;
                float: none;
            }
        }
    </style>
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

</head>

<body>
    <div class="main-content bg-gray-100 min-h-screen p-8">
        <div class=" overflow-x-auto p-6 bg-white rounded shadow-md">
            <h1 class="text-2xl font-bold mb-4">Manage Accounts</h1>

            <!-- Success message display -->
            <?php if (!empty($success_message)): ?>
                <div class="success-message"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <!-- Accounts Table -->
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-blue-200">
                        <th class="border border-gray-300 px-4 py-2">Account ID</th>
                        <th class="border border-gray-300 px-4 py-2">User ID</th>
                        <th class="border border-gray-300 px-4 py-2">Account Email</th>
                        <th class="border border-gray-300 px-4 py-2">Phone</th>
                        <th class="border border-gray-300 px-4 py-2">Account Type</th>
                        <th class="border border-gray-300 px-4 py-2">PIN</th>
                        <th class="border border-gray-300 px-4 py-2">Amount</th>
                        <th class="border border-gray-300 px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($account = $accounts->fetch_assoc()): ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($account['account_id']) ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($account['user_id']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($account['account_email']) ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($account['phone']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($account['account_type']) ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($account['pin']) ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($account['amount']) ?></td>
                            <td class="border border-gray-300 px-4 py-2">
                                <!-- Edit button with Icon -->
                                <button
                                    class="bg-green-400 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center justify-center"
                                    onclick="toggleEditForm(<?= $account['account_id'] ?>)">
                                    <i class="fas fa-edit text-white text-lg"></i> <!-- Font Awesome Edit Icon -->
                                </button>
                            </td>

                        </tr>

                        <!-- Update form (hidden by default) -->
                        <tr id="edit-form-<?= $account['account_id'] ?>" style="display:none;">
                            <form method="POST" action="account_manager.php">
                                <td colspan="8" class="border border-gray-300 px-4 py-2">
                                    <input type="hidden" name="account_id" value="<?= $account['account_id'] ?>">
                                    <label for="amount" class="block text-gray-700">New Amount</label>
                                    <input type="number" name="amount" value="<?= htmlspecialchars($account['amount']) ?>"
                                        class="border px-3 py-1 rounded" required>
                                    <button type="submit" name="update_amount"
                                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update
                                        Amount</button>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>


    <script>
        // Function to toggle the display of the edit form for a specific account
        function toggleEditForm(accountId) {
            var form = document.getElementById('edit-form-' + accountId);
            form.style.display = form.style.display === 'none' ? 'table-row' : 'none';
        }
    </script>
</body>

</html>