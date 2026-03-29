<?php
include("../config/database.php");

if (!isset($_GET['booking_id'])) {
    echo json_encode(["error" => "No booking id"]);
    exit;
}

$booking_id = $_GET['booking_id'];

$stmt = $conn->prepare("SELECT status FROM bookings WHERE id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($row);
?>