<?php
include("../config/session_check.php");
include("../config/database.php");

// Get logged-in provider's user ID
$provider_user_id = $_SESSION['user_id'] ?? 0;

if (!$provider_user_id) {
    header("Location: sendpayment_failed.php?error=login_required");
    exit();
}

// Check POST data
$booking_id = $_POST['booking_id'] ?? 0;
$amount = $_POST['amount'] ?? 0;

if (!$booking_id || !$amount) {
    header("Location: sendpayment_failed.php?error=invalid_request");
    exit();
}

// Fetch the provider's internal ID
$stmt = $conn->prepare("SELECT id FROM providers WHERE user_id=?");

if(!$stmt){
    header("Location: sendpayment_failed.php?error=db_prepare_failed");
    exit();
}

$stmt->bind_param("i", $provider_user_id);
$stmt->execute();
$result = $stmt->get_result();
$provider_row = $result->fetch_assoc();
$provider_id = $provider_row['id'] ?? 0;

if (!$provider_id) {
    header("Location: sendpayment_failed.php?error=provider_not_found");
    exit();
}

// Verify that this booking belongs to this provider
$stmt2 = $conn->prepare("SELECT * FROM bookings WHERE id=? AND provider_id=?");

if(!$stmt2){
    header("Location: sendpayment_failed.php?error=db_prepare_failed");
    exit();
}

$stmt2->bind_param("ii", $booking_id, $provider_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$booking = $result2->fetch_assoc();

if (!$booking) {
    header("Location: sendpayment_failed.php?error=booking_not_found");
    exit();
}

// Update booking with payment request
$stmt3 = $conn->prepare("UPDATE bookings SET price_status='sent', price=? WHERE id=?");

if(!$stmt3){
    header("Location: sendpayment_failed.php?error=db_prepare_failed");
    exit();
}

$stmt3->bind_param("di", $amount, $booking_id);

if (!$stmt3->execute()) {
    header("Location: sendpayment_failed.php?error=update_failed");
    exit();
}

// SUCCESS
$message = "Price has been sent for your booking";
// get customer_id first
$res = mysqli_query($conn, "SELECT customer_id FROM bookings WHERE id='$booking_id'");
$row = mysqli_fetch_assoc($res);
$customer_id = $row['customer_id'];
mysqli_query($conn, "INSERT INTO notifications (sender_id, receiver_id, message, booking_id) VALUES ('$provider_user_id', '$customer_id', '$message', '$booking_id')");

header("Location: sendpayment_success.php?booking_id=".$booking_id);
exit();
?>