<?php

include("../config/database.php");
include("../config/session_check.php");

$booking_id = $_GET['id']; // ✅ use same variable everywhere

// ✅ Update booking status
$update = $conn->prepare("UPDATE bookings SET status = 'completed' WHERE id = ?");
$update->bind_param("i", $booking_id);

if (!$update->execute()) {
    die("Update failed: " . $update->error);
}

// ✅ Get provider user id (from session)
$provider_user_id = $_SESSION['user_id'];

// ✅ Get customer_id from bookings
$getCustomer = $conn->prepare("SELECT customer_id FROM bookings WHERE id = ?");
$getCustomer->bind_param("i", $booking_id);

if (!$getCustomer->execute()) {
    die("Fetch failed: " . $getCustomer->error);
}

$result = $getCustomer->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Customer not found");
}

$customer_id = $row['customer_id'];

// ✅ Message
$message = "Service completed. Please review and make payment.";

// ✅ Insert notification
$insert = $conn->prepare("INSERT INTO notifications (sender_id, receiver_id, message, booking_id) VALUES (?, ?, ?, ?)");

$insert->bind_param("iisi", $provider_user_id, $customer_id, $message, $booking_id);

if (!$insert->execute()) {
    die("Insert failed: " . $insert->error);
}

// ✅ Redirect
header("Location: ../provider/schedule.php");
exit;

?>