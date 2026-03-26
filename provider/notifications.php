<?php
include("../config/session_check.php");
include("../config/database.php");
include("navbar.php"); 

$user_id = $_SESSION['user_id'];

// 1. Fetch notifications FIRST to see which were unread before we update them
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

<div class="page-wrapper" style="flex-direction: column; justify-content: flex-start; padding-top: 100px;">
    
    <div class="glass-card" style="width: 100%; max-width: 800px; padding: 0;">
        
        <div style="padding: 25px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <h2 style="margin: 0; font-size: 1.5rem;">Notifications</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Stay updated on your booking status</p>
        </div>

        <div class="notification-list">
            <?php if($result->num_rows == 0): ?>
                <div style="padding: 40px; text-align: center;">
                    <p style="color: var(--text-muted);">No notifications yet. You're all caught up!</p>
                </div>
            <?php endif; ?>

            <?php while($row = $result->fetch_assoc()): ?>
                <div class="notification-item <?php echo ($row['status'] == 'unread') ? 'unread' : ''; ?>" 
                     style="padding: 20px 25px; border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.3s ease; position: relative;">
                    
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <p style="margin: 0; color: #fff; font-size: 1rem; line-height: 1.4;">
                            <?php echo htmlspecialchars($row['message']); ?>
                        </p>
                        
                        <?php if($row['status'] == 'unread'): ?>
                            <span style="background: var(--accent-blue); width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-top: 6px;"></span>
                        <?php endif; ?>
                    </div>

                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 0.8rem; opacity: 0.5;">🕒</span>
                        <small style="color: var(--text-muted);">
                            <?php echo date("M j, g:i a", strtotime($row['created_at'])); ?>
                        </small>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    </div>
</div>