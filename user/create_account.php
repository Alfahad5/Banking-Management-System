<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../db_connection.php'; // Include your DB connection

// Fetch session email
if (!isset($_SESSION['email'])) {
    echo "<p class='text-red-500'>Please log in to create an account.</p>";
    exit;
}
$email = $_SESSION['email'];

$alertMessage = ''; // Variable to hold alert messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'];
    $account_type = trim($_POST['account_type']);
    $pin = trim($_POST['pin']);
    $confirm_pin = trim($_POST['confirm_pin']);
    $amt = 0;

    if ($pin !== $confirm_pin) {
        $alertMessage = 'PIN and Confirm PIN do not match.';
    } else {
        // Fetch user_id from users table
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user_id = $row['user_id'];

            // Check if the account already exists
            $check_stmt = $conn->prepare("SELECT * FROM accounts WHERE account_email = ? AND phone = ? AND account_type = ?");
            $check_stmt->bind_param("sss", $email, $phone, $account_type);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $alertMessage = 'Account already exists.';
            } else {
                // Insert new account
                $insert_stmt = $conn->prepare("INSERT INTO accounts (user_id, account_email, phone, account_type, pin, amount) VALUES (?, ?, ?, ?, ?, ?)");
                $insert_stmt->bind_param("issssi", $user_id, $email, $phone, $account_type, $pin, $amt);
                if ($insert_stmt->execute()) {
                    $alertMessage = 'Account created successfully!';
                } else {
                    $alertMessage = 'Error creating account. Please try again.';
                }
            }
        } else {
            $alertMessage = 'User not found.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <script>
        function validateForm() {
            const phone = document.getElementById('phone').value;
            const pin = document.getElementById('pin').value;
            const confirmPin = document.getElementById('confirm_pin').value;

            if (phone === '' || pin === '' || confirmPin === '') {
                alert('All fields are required!');
                return false;
            }

            if (pin !== confirmPin) {
                alert('PIN and Confirm PIN do not match!');
                return false;
            }
            return true;
        }

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

    <?php include "Usidebar.php"; ?>

    <div class="main-content bg-gray-50 min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h1 class="text-2xl font-bold mb-6 text-center">Create Account</h1>
            <form method="POST" onsubmit="return validateForm();">
                <div class="mb-4">
                    <label for="phone" class="block text-gray-700">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="w-full p-2 border border-gray-300 rounded mt-1"
                        required>
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
                    <label for="confirm_pin" class="block text-gray-700">Confirm PIN</label>
                    <input type="password" id="confirm_pin" name="confirm_pin"
                        class="w-full p-2 border border-gray-300 rounded mt-1" required>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Create
                    Account</button>
            </form>
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">Forgot Pin? <br> <a href="forget_password.php"
                        class="text-blue-500 hover:underline">Reset Pin</a></p>
            </div>
        </div>
    </div>

</body>

</html>