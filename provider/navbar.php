<?php

include("../config/session_check.php");
include("../config/database.php");

// Ensure session_start() is called in your session_check or at the top of the page
$user_id = $_SESSION['user_id'];

/* Count unread notifications for the provider */
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
        LOSBO <span style="font-size: 0.7rem; color: var(--accent-blue); background: rgba(77, 184, 255, 0.1); padding: 2px 6px; border-radius: 4px; margin-left: 5px;">PRO</span>
    </div>

    <div class="nav-links">
        <a href="dashboard.php">Home</a>
        <a href="about.php">About Us</a>
        <a href="requests.php">Requests</a>
        <a href="schedule.php">Schedule</a>
        <a href="history.php">History</a>
        <a href="profile.php">Profile</a>
        <span class="notify-wrapper" style="position: relative; display: inline-block; margin-right: 10px;">
            <a href="notifications.php" style="font-size: 1.1rem; margin-right: 0;">🔔</a>
            
            <?php if($unread > 0): ?>
                <span class="nav-badge"><?php echo $unread; ?></span>
            <?php endif; ?>
        </span> 

        <a href="../auth/logout.php" class="logout-link">Logout</a>
    </div>
</nav>

<div style="margin-top: 80px;"></div>