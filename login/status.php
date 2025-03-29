<?php
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

// if (!isset($_SESSION['userId'])) {
//     echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
//     exit();
// }

// include '../db_connection.php';

// $userId = $_SESSION['userId']; // Assume the user ID is stored in the session
// $stmt = $conn->prepare("SELECT status FROM requests WHERE id = ?");
// $stmt->bind_param("i", $userId);
// $stmt->execute();
// $result = $stmt->get_result();

// if ($result->num_rows > 0) {
//     $row = $result->fetch_assoc();
//     echo json_encode(['status' => $row['status']]);
// } else {
//     echo json_encode(['status' => 'error', 'message' => 'Request not found']);
// }

// $stmt->close();
// $conn->close();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['email'])) {
    header("Location: sign-up.php");
    exit;
}

include "../db_connection.php";

$email = $_SESSION['email'];
$status = $_SESSION['status'];

if ($status === 'waiting') {
    // Query to check current status in the database
    $stmt = $conn->prepare("SELECT status FROM requests WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $status = $result['status'];
    $flag = true;
    $_SESSION['status'] = $status; // Update session status  
}

if ($status === 'accepted' && $flag) {

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF; //Enable verbose debug output
        $mail->isSMTP(); //Send using SMTP
        $mail->Host = 'smtp.gmail.com'; //Set the SMTP server to send through
        $mail->SMTPAuth = true; //Enable SMTP authentication
        $mail->Username = 'alfahad.infoseek@gmail.com';
        //SMTP username
        $mail->Password = 'wkbm fjrg svzy zgsf';
        //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        //Enable implicit TLS encryption
        $mail->Port = 587; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('alfahad.infoseek@gmail.com', 'Admin');
        $mail->addAddress($_SESSION['email'], $_SESSION['name']);
        //Add a recipient
        // $mail->addAddress('ellen@example.com'); //Name is optional
        $mail->addReplyTo('alfahad.infoseek@gmail.com', 'Admin');

        // optional feilds
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = 'Account approvel';
        // Generate ticket HTML
        // $ticketHTML = generateTicketHTML($ticket);

        $mail->Body = 'Congratulations.Your account has been approved. You can Sign in NOW!';
        $mail->AltBody = 'Account has been approved';

        $mail->send();
        // echo 'Message has been sent';
        // $params = http_build_query([
        //     'train_id' => $train_id,
        //     'namee' => $name,
        //     'aadhar' => $aadhar
        //     // Include other fiels as needed
        // ]);
        // exit('<script>window.location.href="ticket.php?' . $params . '";</script>');
        $_SESSION['email_sent'] = true;
        header("Location: sign-in.php");
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status</title>
    <script>
        // This function checks the status by refreshing the page every 3 seconds
        setInterval(function () {
            location.reload();  // Reloads the page every 3 seconds
        }, 5000);
    </script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-center">Account Status</h2>
        <p class="text-center mt-4">
            <?php if (isset($_SESSION['email_sent'])): ?>
                <?php if ($_SESSION['email_sent']): ?>
                    Your account has been approved, and a confirmation email has been sent to your address. <a
                        href="sign-in.php" class="text-blue-500 hover:underline">Sign in here</a>.
                <?php else: ?>
                    There was an error sending the email. Please try again later.
                <?php endif; ?>
                <?php unset($_SESSION['email_sent']); ?> <!-- Clear the session variable -->
            <?php elseif ($status === 'waiting'): ?>
                Your request is waiting for admin approval. Please check again later.
            <?php elseif ($status === 'accepted'): ?>
                Your request has been approved! <a href="sign-in.php" class="text-blue-500 hover:underline">Sign in
                    here</a>.
            <?php elseif ($status === 'rejected'): ?>
                Your request has been rejected. <a href="sign-up.php" class="text-red-500 hover:underline">Try again</a>.
            <?php endif; ?>
        </p>
    </div>
</body>

</html>