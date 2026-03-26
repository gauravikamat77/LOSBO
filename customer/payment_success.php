<?php
include("../config/session_check.php");
include("../config/database.php");

$customer_id = $_SESSION['user_id'] ?? 0;
$booking_id = $_GET['booking_id'] ?? 0;
if(!$booking_id){
    die("Invalid booking ID.");
}

// Fetch booking details
$stmt = $conn->prepare("SELECT b.*, u.name as provider_name FROM bookings b JOIN users u ON b.provider_id=u.id WHERE b.id=? AND b.customer_id=?");
$stmt->bind_param("ii", $booking_id, $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    die("Booking not found.");
}

$booking = $result->fetch_assoc();
?>

<link rel="stylesheet" href="../assets/css/style.css">
<?php include("../customer/navbar.php"); ?>

<div class="page-wrapper" style="flex-direction:column;padding-top:100px; max-width:600px; margin:auto;">

    <div class="glass-card" style="padding:30px; text-align:center;">
        <h2 style="color:#00e676;">Payment Successful!</h2>
        <p>You have successfully paid ₹<?php echo $booking['price']; ?> for your booking with <b><?php echo htmlspecialchars($booking['provider_name']); ?></b>.</p>
        <a href="history.php" class="btn" style="margin-top:15px; padding:10px 25px;">Back to History</a>
    </div>

</div>