<?php
include("../config/session_check.php");
include("../config/database.php");

$customer_id = $_SESSION['user_id'] ?? 0;
if(!$customer_id){
    die("You must be logged in.");
}

$booking_id = $_GET['booking_id'] ?? 0;
if(!$booking_id){
    die("Invalid booking ID.");
}

// Fetch booking to confirm it belongs to this customer
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id=? AND customer_id=?");
$stmt->bind_param("ii", $booking_id, $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    die("Booking not found.");
}

$booking = $result->fetch_assoc();

// Simulate payment process (here you could integrate PayPal, Razorpay, Stripe, etc.)
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Update booking as paid
    $stmt2 = $conn->prepare("UPDATE bookings SET payment_status='paid' WHERE id=?");
    $stmt2->bind_param("i", $booking_id);
    if($stmt2->execute()){
        header("Location: payment_success.php?booking_id=$booking_id");
        exit;
    } else {
        die("Failed to update payment: ".$conn->error);
    }
}
?>

<link rel="stylesheet" href="../assets/css/style.css">
<?php include("../customer/navbar.php"); ?>

<div class="page-wrapper" style="flex-direction:column;padding-top:100px; max-width:600px; margin:auto;">

    <div class="glass-card" style="padding:30px; text-align:center;">
        <h2>Payment for Booking #<?php echo $booking['id']; ?></h2>
        <p>Amount: ₹<?php echo $booking['price']; ?></p>
        <form method="POST">
            <button type="submit" class="btn" style="padding:10px 25px;">Pay Now</button>
        </form>
    </div>

</div>