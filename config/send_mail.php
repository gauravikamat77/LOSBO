<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Make sure PHPMailer is installed via Composer

function sendMail($to, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'testingtheprojects0.0@gmail.com'; // Your Gmail
        $mail->Password   = 'oxmhtoszijsryntc';                 // App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     // TLS
        $mail->Port       = 587;

        //Recipients
        $mail->setFrom('testingtheprojects0.0@gmail.com', 'LOSBO');
        $mail->addAddress($to);

        //Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Optional: debug info
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}
?>