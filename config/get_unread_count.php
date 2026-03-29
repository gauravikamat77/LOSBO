<?php
include("database.php");

header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    echo json_encode(["count" => 0]);
    exit;
}

$user_id = $_GET['user_id'];

$stmt = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM notifications 
    WHERE receiver_id = ? AND status = 0
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode([
    "count" => $row['total']
]);
?>