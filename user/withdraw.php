<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../db_connection.php'; // Include your DB connection

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo "<p class='text-red-500'>Please log in to deposit funds.</p>";
    exit;
}

$email = $_SESSION['email'];
$alertMessage = ''; // Variable to hold alert messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $account_number = trim($_POST['account_id']);
    $account_type = trim($_POST['account_type']);
    $pin = trim($_POST['pin']);
    $amount = trim($_POST['amount']);

    // Validate the amount
    if (!is_numeric($amount) || $amount <= 0) {
        $alertMessage = 'Please enter a valid Withdrawal amount.';
    } else {
        // Fetch account details from the accounts table
        $stmt = $conn->prepare("SELECT account_id, user_id, account_email, phone, account_type, pin, amount FROM accounts WHERE account_id = ?");
        $stmt->bind_param("i", $account_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Check if the email, pin, and account type match
            if ($row['account_email'] === $email && $row['pin'] === $pin && $row['account_type'] === $account_type && $row['amount'] >= $amount) {
                // Update the account amount
                $new_amount = $row['amount'] - $amount;
                $update_stmt = $conn->prepare("UPDATE accounts SET amount = ? WHERE account_id = ?");
                $update_stmt->bind_param("di", $new_amount, $account_number);

                if ($update_stmt->execute()) {

                    $transaction_type = 'Withdrawal'; // Define the transaction type as 'Withdrawal'
                    $insert_transaction_stmt = $conn->prepare("INSERT INTO transactions (user_id, account_id, email, account_type, transaction_type, amount) VALUES (?, ?, ?, ?, ?, ?)");
                    $insert_transaction_stmt->bind_param("iissss", $row['user_id'], $account_number, $email, $account_type, $transaction_type, $amount);

                    if ($insert_transaction_stmt->execute()) {
                        $alertMessage = 'Transaction successful. ' . $amount . ' Withdrawn';
                    } else {
                        $alertMessage = 'Error logging transaction. Please try again.';
                    }
                } else {
                    $alertMessage = 'Error processing the transaction. Please try again.';
                }
            } else {
                $alertMessage = 'Account information does not match. Please check the details and try again.';
            }
        } else {
            $alertMessage = 'Account not found.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw</title>
    <script>
        window.onload = function () {
            <?php if ($alertMessage): ?>
                alert('<?php echo $alertMessage; ?>');
            <?php endif; ?>
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* .main-content {
            width: 85%;
            float: right;
            background-image: url('../img/depositmoney.webp');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
        } */
        .main-content {
            width: 85%;
            float: right;

        }

        @media (max-width: 768px) {
            .main-content {
                width: 100%;
                float: none;
                margin: 5px;
            }
        }
    </style>
</head>

<body>

    <?php include "Usidebar.php"; ?>

    <div class="main-content bg-gray-50 min-h-screen flex items-center justify-center">
        <div class="bg-white bg-opacity-90 p-8 rounded-lg shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold mb-6 text-center">Withdraw Funds</h1>
            <form method="POST">
                <div class="mb-4">
                    <label for="account_id" class="block text-gray-700">Account Number</label>
                    <input type="text" id="account_id" name="account_id"
                        class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>

                <div class="mb-4">
                    <label for="account_type" class="block text-gray-700">Account Type</label>
                    <select id="account_type" name="account_type" class="w-full p-2 border border-gray-300 rounded mt-1"
                        required>
                        <option value="">Select Account Type</option>
                        <option value="Current">Current</option>
                        <option value="Savings">Savings</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="pin" class="block text-gray-700">PIN</label>
                    <input type="password" id="pin" name="pin" class="w-full p-2 border border-gray-300 rounded mt-1"
                        required>
                </div>

                <div class="mb-4">
                    <label for="amount" class="block text-gray-700">Withdrawal Amount</label>
                    <input type="text" id="amount" name="amount" class="w-full p-2 border border-gray-300 rounded mt-1"
                        required>
                </div>

                <button type="submit"
                    class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Withdraw</button>
            </form>
        </div>
    </div>

</body>

</html>