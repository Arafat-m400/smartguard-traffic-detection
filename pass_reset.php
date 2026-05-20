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

// Sending the email
$sendMailTo = $email;
$sendMailToName = $email;
$mailSubject = 'Password reset Secured Token';
$mailBody = "
    <p>Dear user,</p>
    <p>You have requested to reset your password. Please use the following token:</p>
    <h2 style='color: #2c3e50;'>$token</h2>
    <p>Click the link below to reset your password:</p>
    <p><a href='http://localhost/trafficSignDetection/reset.php' style='color: #3498db;'>Reset your password</a></p>
    <p><strong>Note:</strong> Do not share your token with anyone. This is for your privacy and account security.</p>
    <p>If you did not request this, please ignore this message.</p>
    <p>Best regards,<br>Support Team</p>
";

sendEmail($sendMailTo, $sendMailToName, $mailSubject, $mailBody);

?>
