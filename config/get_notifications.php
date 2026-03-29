<?php
include("../config/database.php");

header('Content-Type: application/json');

// safety check
if (!isset($_GET['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_GET['user_id'];

// ✅ use prepared statement
$query = "
SELECT n.*, u.name AS sender_name, b.status AS booking_status
FROM notifications n
JOIN users u ON n.sender_id = u.id
LEFT JOIN bookings b ON n.booking_id = b.id
WHERE n.receiver_id = ?
ORDER BY n.created_at DESC
";

$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);