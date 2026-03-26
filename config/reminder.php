<?php
include("../config/database.php");
include_once("../config/notification.php");

// 🔔 TODAY reminder
$sql2 = "
SELECT * FROM tasks 
WHERE DATE(task_date) = CURDATE()
AND id NOT IN (
    SELECT reference_id FROM notifications 
    WHERE type = 'task_reminder_today'
)";

$result2 = $conn->query($sql2);

while($row = $result2->fetch_assoc()){

    // 👉 ADD IT HERE (inside loop)
    createNotification(
        $conn,
        $row['provider_id'],
        'provider',
        'task_reminder_today',
        "You have a task today",
        $row['id']
    );
}


// 🔔 TOMORROW reminder
$sql = "
SELECT * FROM tasks 
WHERE DATE(task_date) = CURDATE() + INTERVAL 1 DAY
AND id NOT IN (
    SELECT reference_id FROM notifications 
    WHERE type = 'task_reminder_tomorrow'
)";

$result = $conn->query($sql);

while($row = $result->fetch_assoc()){

    // 👉 ADD IT HERE ALSO
    createNotification(
        $conn,
        $row['provider_id'],
        'provider',
        'task_reminder_tomorrow',
        "Reminder: You have a task tomorrow",
        $row['id']
    );
}
?>