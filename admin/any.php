<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .head {
            width: 100%;
            height: 10%;
        }

        .side {
            width: 15%;
            height: 100%;
            float: left;
        }

        .cont {
            width: 85%;
            height: 100%;
            float: right;
        }
    </style>

</head>

<body>

    <div class="panel">
        <div class="head">
            <header class="text-gray-600 body-font">
                <div class="container mx-auto flex flex-wrap p-5 flex-col md:flex-row items-center">
                    <a class="flex title-font font-medium items-center text-gray-900 mb-4 md:mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2"
                            class="w-10 h-10 text-white p-2 bg-indigo-500 rounded-full" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                        </svg>
                        <span class="ml-3 text-xl">Tailblocks</span>
                    </a>
                    <nav class="md:ml-auto flex flex-wrap items-center text-base justify-center">
                        <a class="mr-5 hover:text-gray-900">First Link</a>
                        <a class="mr-5 hover:text-gray-900">Second Link</a>
                        <a class="mr-5 hover:text-gray-900">Third Link</a>
                        <a class="mr-5 hover:text-gray-900">Fourth Link</a>
                    </nav>
                    <button
                        class="inline-flex items-center bg-gray-100 border-0 py-1 px-3 focus:outline-none hover:bg-gray-200 rounded text-base mt-4 md:mt-0">Button
                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" class="w-4 h-4 ml-1" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </header>
        </div>

        <div class="side">
            <div class="flex h-screen flex-col justify-between border-e bg-white">
                <div class="px-4 py-6">
                    <!-- <span class="grid h-10 w-32 place-content-center rounded-lg bg-gray-100 text-xs text-gray-600">
                        Logo
                    </span> -->

                    <ul class="mt-6 space-y-1">
                        <li>
                            <!-- <a href="#"
                                class="block rounded-lg bg-gray-300 px-4 py-2 text-sm font-medium text-gray-700">
                                Banking management
                            </a> -->
                            <h2 class="block rounded-lg bg-gray-300 px-4 py-2 text-sm font-medium 
                            
                            ">Banking
                                management</h2>
                        </li>

                        <li>
                            <a href="user_dashboard.php"
                                class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                                User Dashboard
                            </a>
                        </li>

                        <li>
                            <a href="create_account.php"
                                class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                                Create Account
                            </a>
                        </li>

                        <li>
                            <a href="forget_password.php"
                                class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                                Forget Password
                            </a>
                        </li>

                        <li>
                            <a href="deposit.php"
                                class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                                Deposit
                            </a>
                        </li>

                        <li>
                            <a href="withdraw.php"
                                class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                                Withdraw
                            </a>
                        </li>

                        <li>
                            <a href="transaction_history.php"
                                class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                                Transaction History
                            </a>
                        </li>

                        <li>
                            <a href="../logout.php?logout=true"
                                class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                                Log out
                            </a>
                        </li>

                    </ul>
                </div>

                <!-- <div class="relative bottom-5 w-full px-4">
                    <a href="../login/sign-in.php"
                        class="block px-4 py-2 text-gray-200 bg-red-500 hover:bg-red-700 rounded text-center">
                        Log Out
                    </a>
                </div> -->
            </div>
        </div>

        <!-- <div class="cont ">

        </div> -->
    </div>
</body>

</html>

<!-- user dashboard -->
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
    </style>
</head>

<body>
    <div class="main-content bg-gray-100 min-h-screen p-8">
        <!-- Welcome Message -->
        <h1 class="text-3xl font-bold mb-6">Welcome, <?php echo htmlspecialchars($userName); ?>!</h1>

        <!-- Account Info Section -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-semibold">Account(s) Information</h2>
            <p class="text-gray-700 mt-2"><?php echo $accountDetails ? $accountDetails : 'No account found.'; ?></p>
        </div>

        <!-- Check Balance Section -->
        <h2 class="text-2xl font-semibold mb-4">Check Balance</h2>

        <!-- Form -->
        <form method="POST" class="bg-white p-6 rounded-lg shadow-md max-w-lg">
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

            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Check
                Balance</button>
        </form>

        <!-- Balance Message -->
        <?php if ($balanceMessage): ?>
            <p class="mt-6 text-xl font-semibold text-green-600"><?php echo htmlspecialchars($balanceMessage); ?></p>
        <?php endif; ?>
    </div>
</body>

</html>