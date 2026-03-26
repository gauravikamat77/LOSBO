<?php
session_start();
include("../config/database.php"); // Database connection

// PHPMailer manual includes
require_once __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get email from form
$email = $_POST['email'] ?? '';
if (!$email) {
    die("Email is required.");
}

// Check if user exists
$stmt = $conn->prepare("SELECT id, name FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Email not found.");
}
$user = $result->fetch_assoc();

// Generate reset token and expiry
$token = bin2hex(random_bytes(50));
$expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

// Save token in database
$stmt = $conn->prepare("UPDATE users SET reset_token=?, token_expiry=? WHERE email=?");
$stmt->bind_param("sss", $token, $expiry, $email);
$stmt->execute();

// Prepare reset link
$reset_link = "http://127.0.0.1/losbo/auth/reset_password.php?token=" . $token;

// Send email
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'testingtheprojects0.0@gmail.com'; // Your Gmail
    $mail->Password   = 'oxmhtoszijsryntc';               // App password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('testingtheprojects0.0@gmail.com', 'LOSBO');
    $mail->addAddress($email, $user['name']);

    $mail->isHTML(true);
    $mail->Subject = 'LOSBO Password Reset';
    $mail->Body    = "
        <h2>Password Reset Request</h2>
        <p>Hi {$user['name']},</p>
        <p>Click the button below to reset your password:</p>
        <a href='$reset_link' style='padding:10px 20px;background:#00e676;color:white;text-decoration:none;border-radius:5px;'>Reset Password</a>
        <p>This link will expire in 1 hour.</p>
    ";

    $mail->send();

    // Redirect to reset link sent page
    header("Location: reset_link_sent.php");
    exit;

} catch (Exception $e) {
    die("Mailer Error: " . $mail->ErrorInfo);
}