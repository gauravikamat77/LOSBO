<?php
include("../config/database.php");
include("../config/session_check.php");

// ✅ Get booking_id (from POST or GET depending on your form)
if (isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
} elseif (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];
} else {
    die("Booking ID missing");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ✅ STEP 1: Update booking as paid
    $stmt2 = $conn->prepare("UPDATE bookings SET payment_status='paid' WHERE id=?");
    $stmt2->bind_param("i", $booking_id);

    if (!$stmt2->execute()) {
        die("Failed to update payment: " . $stmt2->error);
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

    // ✅ STEP 3: Customer ID (sender)
    $customer_id = $_SESSION['user_id'];

    // ✅ STEP 4: Message
    $message = "Payment completed for your work. Thank you!";

    // ✅ STEP 5: Insert notification
    $insert = $conn->prepare("
        INSERT INTO notifications (sender_id, receiver_id, message, booking_id) 
        VALUES (?, ?, ?, ?)
    ");

    $insert->bind_param("iisi", $customer_id, $provider_user_id, $message, $booking_id);

    if (!$insert->execute()) {
        die("Insert failed: " . $insert->error);
    }

    // ✅ Redirect after success
    header("Location: payment_success.php?booking_id=" . $booking_id);
    exit;
}

// ✅ Get booking_id
if (!isset($_GET['booking_id'])) {
    die("Booking ID missing");
}

$booking_id = $_GET['booking_id'];

// ✅ Fetch booking details
$stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->bind_param("i", $booking_id);

if (!$stmt->execute()) {
    die("Fetch failed: " . $stmt->error);
}

$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    die("Booking not found");
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