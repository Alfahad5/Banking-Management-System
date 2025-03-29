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

// Fetch all transactions from the database
$transactions = $conn->query("SELECT `transaction_id`, `user_id`, `account_id`, `email`, `account_type`, `transaction_type`, `amount` FROM `transactions` WHERE 1");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
    <div class="main-content bg-gray-100 min-h-screen p-8">
        <div class="flex-1 p-4">
            <h1 class="text-2xl font-bold">Transaction History</h1>
            <p class="mt-4">Below is the list of all transactions made:</p>
        </div>

        <!-- Transactions Table -->
        <div class="overflow-x-auto bg-white p-6 rounded-lg shadow-md">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-blue-200">
                        <th class="border border-gray-300 px-4 py-2">Transaction ID</th>
                        <th class="border border-gray-300 px-4 py-2">User ID</th>
                        <th class="border border-gray-300 px-4 py-2">Account ID</th>
                        <th class="border border-gray-300 px-4 py-2">Email</th>
                        <th class="border border-gray-300 px-4 py-2">Account Type</th>
                        <th class="border border-gray-300 px-4 py-2">Transaction Type</th>
                        <th class="border border-gray-300 px-4 py-2">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($transaction = $transactions->fetch_assoc()): ?>
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">
                                <?= htmlspecialchars($transaction['transaction_id']) ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($transaction['user_id']) ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($transaction['account_id']) ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($transaction['email']) ?></td>
                            <td class="border border-gray-300 px-4 py-2">
                                <?= htmlspecialchars($transaction['account_type']) ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <?= htmlspecialchars($transaction['transaction_type']) ?>
                            </td>
                            <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($transaction['amount']) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>