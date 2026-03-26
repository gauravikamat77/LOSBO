<?php

session_start();
include("../config/database.php");

$booking_id = $_POST['booking_id'];
$provider_id = $_POST['provider_id'];
$rating = $_POST['rating'];
$review = $_POST['review'];

$customer_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
"INSERT INTO ratings 
(booking_id,provider_id,customer_id,rating,review)
VALUES (?,?,?,?,?)"
);

$stmt->bind_param("iiiis",
$booking_id,
$provider_id,
$customer_id,
$rating,
$review
);

$stmt->execute();

header("Location: ../customer/history.php");

?>