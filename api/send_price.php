<?php

include("../config/database.php");

$booking_id = $_POST['booking_id'];
$price = $_POST['price'];

$stmt = $conn->prepare(
"UPDATE bookings 
SET price=?, price_status='sent'
WHERE id=?"
);

$stmt->bind_param("di",$price,$booking_id);
$stmt->execute();

/* Get customer ID */

$stmt2 = $conn->prepare(
"SELECT customer_id FROM bookings WHERE id=?"
);

$stmt2->bind_param("i",$booking_id);
$stmt2->execute();

$res = $stmt2->get_result();
$row = $res->fetch_assoc();

$customer = $row['customer_id'];

/* Add notification */

$message = "Your provider has sent a price quote.";

$stmt3 = $conn->prepare(
"INSERT INTO notifications (user_id,message)
VALUES (?,?)"
);

$stmt3->bind_param("is",$customer,$message);
$stmt3->execute();

header("Location: ../provider/schedule.php");

?>