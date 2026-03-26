<?php
include("database.php");

function createNotification($conn, $user_id, $user_type, $type, $message, $ref_id=null){
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, user_type, type, message, reference_id)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssi", $user_id, $user_type, $type, $message, $ref_id);
    $stmt->execute();
}

function getUnreadCount($conn, $user_id, $user_type){
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM notifications WHERE user_id=? AND user_type=? AND is_read=0");
    $stmt->bind_param("is", $user_id, $user_type);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}

function getNotifications($conn, $user_id, $user_type){
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id=? AND user_type=? ORDER BY created_at DESC");
    $stmt->bind_param("is", $user_id, $user_type);
    $stmt->execute();
    return $stmt->get_result();
}

function markAllRead($conn, $user_id, $user_type){
    $stmt = $conn->prepare("UPDATE notifications SET is_read=1 WHERE user_id=? AND user_type=?");
    $stmt->bind_param("is", $user_id, $user_type);
    $stmt->execute();
}
?>