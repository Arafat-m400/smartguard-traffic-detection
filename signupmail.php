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
$sendMailTo = $username;
$sendMailToName = $fullname;
$mailSubject = 'Welcome to the Traffic Sign Detection System';

$mailBody = "
    <p>Dear $sendMailToName,</p>
    <p>Thank you for signing up for the <strong>Traffic Sign Detection System</strong>!</p>
    <p>Your account has been successfully created.</p>
    <p>You can log in and get started by clicking the link below:</p>
    <p>
      <a href='http://localhost/traffic_detect/traffic_detect/login.php' style='color: #3498db; text-decoration: none;'>
        Go to Login Page
      </a>
    </p>
    <p>If you have any questions or need help, feel free to contact our support team at <a href='mailto:savestreetchildrenrwanda@gmail.com'>annenshutiyanjye@gmail.com</a>.</p>
    <p>Best regards,<br><strong>Traffic Sign Detection Team</strong></p>
";


sendEmail($sendMailTo, $sendMailToName, $mailSubject, $mailBody);

?>
