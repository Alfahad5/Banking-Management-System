<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: sign-in.php");
    exit;
}

include '../db_connection.php'; // Include your DB connection
include "Usidebar.php";

$email = $_SESSION['email'];
$balanceMessage = '';
$userName = 'User';

// Fetch user name from the database
$stmt = $conn->prepare("SELECT name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userName = $row['name'];
}

// Fetch account details (account_id, account_type) from the accounts table
$accountDetails = '';
$stmt = $conn->prepare("SELECT account_id, account_type FROM accounts WHERE account_email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$accountResult = $stmt->get_result();

if ($accountResult->num_rows > 0) {
    $accountRow = $accountResult->fetch_assoc();
    $accountDetails = "Account ID: " . htmlspecialchars($accountRow['account_id']) . " | Account Type: " . htmlspecialchars($accountRow['account_type']);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_type = trim($_POST['account_type']);
    $pin = trim($_POST['pin']);

    // Validate form inputs
    if (!empty($account_type) && !empty($pin)) {
        $stmt = $conn->prepare("SELECT amount FROM accounts WHERE account_email = ? AND account_type = ? AND pin = ?");
        $stmt->bind_param("sss", $email, $account_type, $pin);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $account = $result->fetch_assoc();
            $balanceMessage = "Your balance is ₹" . number_format($account['amount'], 2);
        } else {
            $balanceMessage = "Invalid account type or PIN. Please try again.";
        }
    } else {
        $balanceMessage = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
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
    <div class="main-content bg-gray-50 min-h-screen p-8">
        <!-- Welcome Message -->
        <h1 class="text-4xl font-bold text-blue-700 mb-8">Welcome, <?php echo htmlspecialchars($userName); ?>!</h1>

        <!-- Account Info Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
            <h2 class="text-2xl font-semibold text-gray-700">Account Information</h2>
            <p class="text-gray-600 mt-2"><?php echo $accountDetails ? $accountDetails : 'No account found.'; ?></p>
        </div>

        <!-- Check Balance Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Check Balance</h2>
            <form method="POST">
                <div class="mb-4">
                    <label for="account_type" class="block text-gray-700">Account Type</label>
                    <select id="account_type" name="account_type" class="w-full p-2 border rounded mt-1">
                        <option value="">Select Account Type</option>
                        <option value="Current">Current</option>
                        <option value="Savings">Savings</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="pin" class="block text-gray-700">PIN</label>
                    <input type="password" id="pin" name="pin" class="w-full p-2 border rounded mt-1" required>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700">Check
                    Balance</button>
            </form>

            <!-- Balance Message -->
            <?php if ($balanceMessage): ?>
                <p class="mt-6 text-lg font-semibold text-green-600"><?php echo htmlspecialchars($balanceMessage); ?></p>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>