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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

$email = $_SESSION['email'];
$accountType = '';
$otpSent = false;
$otpVerified = false;
$otp = '';
$newPinMessage = '';
$otpMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle account selection and sending OTP
    if (isset($_POST['account_type']) && !isset($_POST['otp'])) {
        $accountType = $_POST['account_type'];

        // Generate a random 4-digit OTP
        $otp = rand(1000, 9999);

        // Store OTP in session
        $_SESSION['otp'] = $otp;

        // Send OTP to user's email
        try {
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'alfahad.infoseek@gmail.com'; // Use your email
            $mail->Password = 'wkbm fjrg svzy zgsf'; // Use your SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('alfahad.infoseek@gmail.com', 'Admin');
            $mail->addAddress($email);
            $mail->addReplyTo('alfahad.infoseek@gmail.com', 'Admin');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = 'Your OTP to reset password is: ' . $otp;
            $mail->AltBody = 'Your OTP to reset password is: ' . $otp;

            $mail->send();
            $otpSent = true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    // Handle OTP verification
    if (isset($_POST['otp']) && !isset($_POST['new_pin'])) {
        if ($_POST['otp'] == $_SESSION['otp']) {  // Check OTP from session
            $otpVerified = true;
            unset($_SESSION['otp']);  // Unset OTP after successful verification
        } else {
            $otpMessage = 'Invalid OTP. Please try again.';
        }
    }

    // Handle updating PIN
    if (isset($_POST['new_pin']) && $otpVerified) {
        $newPin = $_POST['new_pin'];

        if (!empty($newPin)) {
            // Prepare SQL query to update the PIN in the accounts table
            $stmt = $conn->prepare("UPDATE accounts SET pin = ? WHERE account_email = ? AND account_type = ?");

            // Check if the statement was prepared successfully
            if ($stmt === false) {
                $newPinMessage = "Error preparing SQL statement: " . $conn->error;
            } else {
                // Bind parameters to the statement
                $stmt->bind_param("sss", $newPin, $email, $accountType);

                // Check if the statement executed successfully
                if ($stmt->execute()) {
                    $newPinMessage = 'PIN updated successfully!';
                } else {
                    $newPinMessage = 'Failed to update PIN. MySQL Error: ' . $stmt->error;
                }

                // Close the statement after execution
                $stmt->close();
            }
        } else {
            $newPinMessage = 'Please enter a new PIN.';
        }
    }

}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .main-content {
            width: 85%;
            float: right;
        }
    </style>
</head>

<body>
    <div class="main-content">
        <div class="bg-gray-100 min-h-screen p-8">
            <h1 class="text-3xl font-bold mb-6">Forget Password</h1>

            <?php if (!$otpSent): ?>
                <!-- Account Type Selection Form -->
                <form id="account-selection-form" method="POST" class="bg-white p-6 rounded-lg shadow-md max-w-lg">
                    <div class="mb-4">
                        <label for="account_type" class="block text-gray-700">Select Account Type</label>
                        <select id="account_type" name="account_type" class="w-full p-2 border border-gray-300 rounded mt-1"
                            required>
                            <option value="">Select Account Type</option>
                            <option value="Current">Current</option>
                            <option value="Savings">Savings</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Get
                        OTP</button>
                </form>
            <?php endif; ?>

            <?php if ($otpSent && !$otpVerified): ?>
                <!-- OTP Verification Form -->
                <form id="otp-form" method="POST" class="bg-white p-6 rounded-lg shadow-md max-w-lg mt-6">
                    <div class="mb-4">
                        <label for="otp" class="block text-gray-700">Enter OTP</label>
                        <input type="text" id="otp" name="otp" class="w-full p-2 border border-gray-300 rounded mt-1"
                            required>
                    </div>
                    <button type="submit" class="w-full bg-green-500 text-white p-2 rounded hover:bg-green-600">Verify
                        OTP</button>
                </form>
            <?php endif; ?>

            <?php if ($otpVerified): ?>
                <!-- New PIN Form -->
                <form id="new-pin-form" method="POST" class="bg-white p-6 rounded-lg shadow-md max-w-lg mt-6">
                    <div class="mb-4">
                        <label for="new_pin" class="block text-gray-700">Enter New PIN</label>
                        <input type="password" id="new_pin" name="new_pin"
                            class="w-full p-2 border border-gray-300 rounded mt-1" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Update
                        PIN</button>
                </form>
            <?php endif; ?>

            <?php if ($newPinMessage): ?>
                <p class="mt-6 text-xl font-semibold text-green-600"><?php echo htmlspecialchars($newPinMessage); ?></p>
            <?php endif; ?>


            <?php if (isset($otpMessage)): ?>
                <p class="mt-6 text-xl font-semibold text-red-600"><?php echo htmlspecialchars($otpMessage); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

<!-- with debugging -->
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

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';

$email = $_SESSION['email'];
$accountType = '';
$otpSent = false;
$otpVerified = false;
$otp = '';
$newPinMessage = '';
$otpMessage = ''; // To show invalid OTP error message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle account selection and sending OTP
    if (isset($_POST['account_type']) && !isset($_POST['otp'])) {
        $accountType = $_POST['account_type'];

        // Generate a random 4-digit OTP
        $otp = rand(1000, 9999);

        // Store OTP in session
        $_SESSION['otp'] = $otp;

        // Send OTP to user's email
        try {
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'alfahad.infoseek@gmail.com'; // Use your email
            $mail->Password = 'wkbm fjrg svzy zgsf'; // Use your SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('alfahad.infoseek@gmail.com', 'Admin');
            $mail->addAddress($email);
            $mail->addReplyTo('alfahad.infoseek@gmail.com', 'Admin');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = 'Your OTP to reset password is: ' . $otp;
            $mail->AltBody = 'Your OTP to reset password is: ' . $otp;

            $mail->send();
            $otpSent = true;

            // Redirect to prevent resubmitting the form on reload
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    // Handle OTP verification
    if (isset($_POST['otp']) && !isset($_POST['new_pin'])) {
        if ($_POST['otp'] == $_SESSION['otp']) {
            $_SESSION['otpVerified'] = true;
            // Redirect after OTP verification
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $otpMessage = 'Invalid OTP. Please try again.';
        }
    }

    // Handle updating PIN
    if (isset($_POST['new_pin']) && isset($_SESSION['otpVerified']) && $_SESSION['otpVerified']) {
        $newPin = $_POST['new_pin'];

        if (!empty($newPin)) {
            // Prepare SQL query to update the PIN in the accounts table
            $stmt = $conn->prepare("UPDATE accounts SET pin = ? WHERE account_email = ? AND account_type = ?");
            $stmt->bind_param("sss", $newPin, $email, $accountType);

            if ($stmt->execute()) {
                $newPinMessage = 'PIN updated successfully!';
                // Optionally, reset OTP verification status after successful PIN update
                unset($_SESSION['otpVerified']);
                // Redirect after successful PIN update
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $newPinMessage = 'Failed to update PIN. Please try again.';
            }
        } else {
            $newPinMessage = 'Please enter a new PIN.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .main-content {
            width: 85%;
            float: right;
        }
    </style>
</head>

<body>
    <div class="main-content">
        <div class="bg-gray-100 min-h-screen p-8">
            <h1 class="text-3xl font-bold mb-6">Forget Password</h1>

            <?php if (!$otpSent): ?>
                <!-- Account Type Selection Form -->
                <form id="account-selection-form" method="POST" class="bg-white p-6 rounded-lg shadow-md max-w-lg">
                    <div class="mb-4">
                        <label for="account_type" class="block text-gray-700">Select Account Type</label>
                        <select id="account_type" name="account_type" class="w-full p-2 border border-gray-300 rounded mt-1"
                            required>
                            <option value="">Select Account Type</option>
                            <option value="Current">Current</option>
                            <option value="Savings">Savings</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Get
                        OTP</button>
                </form>
            <?php endif; ?>

            <?php if ($otpSent && !$otpVerified): ?>
                <!-- OTP Verification Form -->
                <form id="otp-form" method="POST" class="bg-white p-6 rounded-lg shadow-md max-w-lg mt-6">
                    <div class="mb-4">
                        <label for="otp" class="block text-gray-700">Enter OTP</label>
                        <input type="text" id="otp" name="otp" class="w-full p-2 border border-gray-300 rounded mt-1"
                            required>
                    </div>
                    <button type="submit" class="w-full bg-green-500 text-white p-2 rounded hover:bg-green-600">Verify
                        OTP</button>
                </form>
            <?php endif; ?>

            <?php if ($otpVerified): ?>
                <!-- New PIN Form -->
                <form id="new-pin-form" method="POST" class="bg-white p-6 rounded-lg shadow-md max-w-lg mt-6">
                    <div class="mb-4">
                        <label for="new_pin" class="block text-gray-700">Enter New PIN</label>
                        <input type="password" id="new_pin" name="new_pin"
                            class="w-full p-2 border border-gray-300 rounded mt-1" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Update
                        PIN</button>
                </form>
            <?php endif; ?>

            <?php if ($newPinMessage): ?>
                <p class="mt-6 text-xl font-semibold text-green-600"><?php echo htmlspecialchars($newPinMessage); ?></p>
            <?php endif; ?>

            <?php if ($otpMessage): ?>
                <p class="mt-6 text-xl font-semibold text-red-600"><?php echo htmlspecialchars($otpMessage); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>