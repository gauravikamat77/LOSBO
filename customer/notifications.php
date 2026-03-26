<?php
include("../config/session_check.php");
include("../config/database.php");
include("../customer/navbar.php"); 

$user_id = $_SESSION['user_id'];

// 1. Fetch notifications first to display them
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// 2. Mark as read using a prepared statement for security
$update_stmt = $conn->prepare("UPDATE notifications SET status='read' WHERE user_id=? AND status='unread'");
$update_stmt->bind_param("i", $user_id);
$update_stmt->execute();
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper" style="flex-direction: column; justify-content: flex-start; padding-top: 20px;">
    
    <div style="width: 100%; max-width: 800px; margin-bottom: 20px;">
        <h2 class="logo-title" style="text-align: left;">Notifications</h2>
        <p class="slogan" style="text-align: left;">Stay updated on your service activities</p>
    </div>

    <div style="width: 100%; max-width: 800px; display: flex; flex-direction: column; gap: 15px;">
        
        <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="glass-card" style="padding: 20px; text-align: left; position: relative; border-left: 4px solid <?php echo ($row['status'] == 'unread') ? 'var(--accent-blue)' : 'transparent'; ?>;">
                    
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <p style="color: white; margin: 0; font-size: 1rem; line-height: 1.5;">
                            <?php echo htmlspecialchars($row['message']); ?>
                        </p>
                        
                        <?php if($row['status'] == 'unread'): ?>
                            <span style="background: var(--accent-blue); width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-left: 10px;"></span>
                        <?php endif; ?>
                    </div>

                    <div style="margin-top: 10px; color: var(--text-muted); font-size: 0.8rem; display: flex; align-items: center; gap: 5px;">
                        <span>🕒</span>
                        <?php echo date("M j, g:i a", strtotime($row['created_at'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="glass-card" style="text-align: center; padding: 50px;">
                <p style="color: var(--text-muted);">No notifications yet. You're all caught up!</p>
            </div>
        <?php endif; ?>

    </div>
</div>