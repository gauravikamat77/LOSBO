<?php
session_start();
include("../config/database.php");

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if(!$email || !$password){
    header("Location: login.php?error=2");
    exit;
}

// Check user
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $user = $result->fetch_assoc();

    if(password_verify($password, $user['password'])){
        // Login success
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        if($user['role'] == 'customer'){
            header("Location: ../customer/dashboard.php");
        } else {
            header("Location: ../provider/dashboard.php");
        }
        exit;
    } else {
        // Password incorrect
        header("Location: login.php?error=1");
        exit;
    }
} else {
    // User not found
    header("Location: login.php?error=2");
    exit;
}