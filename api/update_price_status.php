<?php
include("../config/database.php");
include("../config/session_check.php");

$customer_id = $_SESSION['user_id'];

if (!isset($_GET['booking_id']) || !isset($_GET['action'])) {
    die("Invalid request");
}

$booking_id = $_GET['booking_id'];
$action = $_GET['action'];

// ✅ तय action
if ($action == "accept") {
    $status = "accepted";
} elseif ($action == "reject") {
    $status = "rejected";
} else {
    die("Invalid action");
}

// ✅ STEP 1: Update price_status
$update = $conn->prepare("UPDATE bookings SET price_status = ? WHERE id = ?");
$update->bind_param("si", $status, $booking_id);

if (!$update->execute()) {
    die("Update failed: " . $update->error);
}

// ✅ STEP 2: Get provider_user_id using JOIN
$getUser = $conn->prepare("
    SELECT p.user_id 
    FROM providers p
    JOIN bookings b ON b.provider_id = p.id
    WHERE b.id = ?
");

$getUser->bind_param("i", $booking_id);

if (!$getUser->execute()) {
    die("Fetch failed: " . $getUser->error);
}

$result = $getUser->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Provider not found");
}

$provider_user_id = $row['user_id'];

// ✅ STEP 3: Create message
$message = ($action == "accept") 
    ? "Customer accepted your price" 
    : "Customer rejected your price";

// ✅ STEP 4: Insert notification
$insert = $conn->prepare("
    INSERT INTO notifications (sender_id, receiver_id, message, booking_id) 
    VALUES (?, ?, ?, ?)
");

$insert->bind_param("iisi", $customer_id, $provider_user_id, $message, $booking_id);

if (!$insert->execute()) {
    die("Insert failed: " . $insert->error);
}

// ✅ Redirect
header("Location: ../customer/history.php");
exit;
?>