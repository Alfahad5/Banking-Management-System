<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../db_connection.php'; // Include your DB connection
include 'Usidebar.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo "<p class='text-red-500'>Please log in to view transaction history.</p>";
    exit;
}

$email = $_SESSION['email'];
$alertMessage = '';
$transactions = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_type = trim($_POST['account_type']);
    $pin = trim($_POST['pin']);

    // Validate input
    if (empty($account_type) || empty($pin)) {
        $alertMessage = 'Please fill in all fields.';
    } else {
        // Check PIN in the accounts table
        $stmt = $conn->prepare("SELECT account_id, user_id, account_email, account_type, pin FROM accounts WHERE account_email = ? AND account_type = ?");
        $stmt->bind_param("ss", $email, $account_type);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $account = $result->fetch_assoc();

            // Verify the PIN
            if ($account['pin'] === $pin) {
                // Fetch transaction history
                $stmt = $conn->prepare("SELECT transaction_id, user_id, account_id, email, account_type, transaction_type, amount FROM transactions WHERE email = ? AND account_type = ?");
                $stmt->bind_param("ss", $email, $account_type);
                $stmt->execute();
                $transactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

                if (empty($transactions)) {
                    $alertMessage = 'No transactions found.';
                }
            } else {
                $alertMessage = 'Incorrect PIN. Please try again.';
            }
        } else {
            $alertMessage = 'Account not found. Please check your details.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <script>
        window.onload = function () {
            <?php if ($alertMessage): ?>
                alert('<?php echo $alertMessage; ?>');
            <?php endif; ?>
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <div class="main-content bg-gray-100 min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h1 class="text-2xl font-bold mb-4">Transaction History</h1>
            <form method="POST">
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

                <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">View
                    Transactions</button>
            </form>
        </div>
    </div>

    <?php if (!empty($transactions)): ?>
        <div class="main-content bg-gray-100  p-8">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300 table-auto">
                    <thead>
                        <tr class="bg-blue-200">
                            <th class="px-4 py-2 border-b text-left">Transaction ID</th>
                            <th class="px-4 py-2 border-b text-left">User ID</th>
                            <th class="px-4 py-2 border-b text-left">Account ID</th>
                            <th class="px-4 py-2 border-b text-left">Email</th>
                            <th class="px-4 py-2 border-b text-left">Account Type</th>
                            <th class="px-4 py-2 border-b text-left">Transaction Type</th>
                            <th class="px-4 py-2 border-b text-left">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td class="px-4 py-2 border-b"><?php echo $transaction['transaction_id']; ?></td>
                                <td class="px-4 py-2 border-b"><?php echo $transaction['user_id']; ?></td>
                                <td class="px-4 py-2 border-b"><?php echo $transaction['account_id']; ?></td>
                                <td class="px-4 py-2 border-b"><?php echo $transaction['email']; ?></td>
                                <td class="px-4 py-2 border-b"><?php echo $transaction['account_type']; ?></td>
                                <td class="px-4 py-2 border-b"><?php echo $transaction['transaction_type']; ?></td>
                                <td class="px-4 py-2 border-b"><?php echo $transaction['amount']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</body>

</html>