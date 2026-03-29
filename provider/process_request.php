<?php
include("../config/session_check.php");
include("../config/database.php");

$provider_user_id = $_SESSION['user_id'] ?? 0;
if(!$provider_user_id){
    die("You must be logged in as a provider.");
}

// Get provider's ID from providers table
$stmt = $conn->prepare("SELECT id FROM providers WHERE user_id=?");
$stmt->bind_param("i", $provider_user_id);
$stmt->execute();
$result = $stmt->get_result();
$provider = $result->fetch_assoc();
if(!$provider){
    die("Provider profile not found.");
}
$provider_id = $provider['id'];

// Get POST data
$booking_id = $_POST['booking_id'] ?? 0;
$action = $_POST['action'] ?? '';

if(!$booking_id || !in_array($action, ['accept','reject'])){
    die("Invalid request.");
}

// Verify booking belongs to this provider
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id=? AND provider_id=?");
$stmt->bind_param("ii", $booking_id, $provider_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
if(!$booking){
    die("Booking not found or not yours.");
}

// Update booking status
$new_status = ($action === 'accept') ? 'accepted' : 'rejected';
$stmt = $conn->prepare("UPDATE bookings SET status=? WHERE id=?");
$stmt->bind_param("si", $new_status, $booking_id);
$stmt->execute();

// get customer_id first
$res = mysqli_query($conn, "SELECT customer_id FROM bookings WHERE id='$booking_id'");
$row = mysqli_fetch_assoc($res);
$customer_id = $row['customer_id'];





// Redirect
if($action === 'accept'){
    $message = 'Your booking was accepted';
    mysqli_query($conn, "INSERT INTO notifications (sender_id, receiver_id, message, booking_id) VALUES ('$provider_user_id', '$customer_id', '$message', '$booking_id')");
    header("Location: schedule.php");
}else{
    $message = 'Your booking was rejected';
    mysqli_query($conn, "INSERT INTO notifications (sender_id, receiver_id, message, booking_id) VALUES ('$provider_user_id', '$customer_id', '$message', '$booking_id')");
    header("Location: requests.php?msg=Booking rejected successfully");
}
exit();
?>