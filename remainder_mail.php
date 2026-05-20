<?php

require 'files/lib/myPHPMailer/src/PHPMailer.php';
require 'files/lib/myPHPMailer/src/SMTP.php';
require 'files/lib/myPHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($sendMailTo, $sendMailToName, $mailSubject, $mailBody)
{
    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->SMTPDebug = 0;  // Set to 2 for debugging purposes
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'savestreetchildrenrwanda@gmail.com'; // Your Gmail username
        $mail->Password = 'rcmd keoa fwkg ngeb'; // Your Gmail password
        $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587; // TCP port to connect to

        // Recipients
        $mail->setFrom('savestreetchildrenrwanda@gmail.com', 'Traffic sign Detection System'); // Sender's email and name
        $mail->addAddress($sendMailTo, $sendMailToName); // Receiver's email and name

        // Content
        $mail->isHTML(true);
        $mail->Subject = $mailSubject;
        $mail->Body = '<html><body><p>' . $mailBody . '</p></body></html>';

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
$token = rand(1000, 9999);
// Sending the email
$sendMailTo = $_SESSION['user'];
$sendMailToName = $_SESSION['user'];
$mailSubject = 'Remainder';
$mailBody = "
    <p>Dear $fullname,</p>
    <p>We hope you're doing well!</p>
    <p>Here's a summary of your recent travel activity:</p>
    <h2 style='color: #2c3e50;'>Reference ID: <strong>$token</strong></h2>
    <p>You can view the full details of your trips by clicking the link below:</p>
    <p>
      <a href='http://localhost/traffic_detect/traffic_detect/history.php' style='color: #3498db; text-decoration: none;'>
        View My Travel History
      </a>
    </p>
    <p>If you have any questions or need assistance, feel free to contact our support team.</p>
    <p>Best regards,<br><strong>The Support Team</strong></p>
";

sendEmail($sendMailTo, $sendMailToName, $mailSubject, $mailBody);

?>
