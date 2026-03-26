<?php
include("../config/session_check.php");
include("../config/database.php");
include_once("../config/notification.php");

$user_id = $_SESSION['user_id'];
$user_type = 'provider';

$notifications = getNotifications($conn, $user_id, $user_type);

// mark all as read
markAllRead($conn, $user_id, $user_type);
?>

<?php include("navbar.php"); ?>

<div class="page-wrapper" style="padding: 20px;">
    <div class="glass-card" style="max-width:600px; margin:auto;">
        <h2>Notifications</h2>

        <?php if($notifications->num_rows == 0): ?>
            <p>No notifications</p>
        <?php endif; ?>

        <?php while($row = $notifications->fetch_assoc()): ?>
            <div style="padding:10px; border-bottom:1px solid #ddd;">
                <?php echo $row['message']; ?>
                <br>
                <small><?php echo date("d M Y, h:i A", strtotime($row['created_at'])); ?></small>
            </div>
        <?php endwhile; ?>
    </div>
</div>