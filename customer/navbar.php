<?php
include("../config/session_check.php");   // START SESSION + CHECK LOGIN
include("../config/database.php");

$user_id = $_SESSION['user_id'];

/* Count unread notifications */
// $stmt = $conn->prepare("SELECT COUNT(*) as total FROM notifications WHERE user_id=? AND status='unread'");
// $stmt->bind_param("i", $user_id);
// $stmt->execute();
// $result = $stmt->get_result();
// $data = $result->fetch_assoc();
// $unread = $data['total'];
?>

<link rel="stylesheet" href="../assets/css/style.css">

<nav class="navbar">
    <div class="nav-brand">
        LOSBO <span style="font-size: 0.8rem; font-weight: normal; opacity: 0.7;">Let's go!</span>
    </div>

    <div class="nav-links">
        <a href="dashboard.php">Home</a>
        <a href="about.php">About Us</a>
        <a href="categories.php">Categories</a>
        <a href="history.php">History</a>
        <a href="profile.php">Profile</a>
        <span class="notify-wrapper" style="position: relative; display: inline-block; margin-right: 15px;">
            <a href="notifications.php" style="font-size: 1.2rem; margin-right: 0;">🔔</a>
        </span> 

        <a href="../auth/logout.php" class="logout-link">Logout</a>
    </div>
</nav>

<div style="margin-top: 80px;"></div>