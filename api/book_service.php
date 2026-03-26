<?php

session_start();
include("../config/database.php");

/* Get customer id from session */

$customer_id = $_SESSION['user_id'];

/* Get form data */

$provider_id = $_POST['provider_id'];
$date = $_POST['date'];
$time = $_POST['time'];
$description = $_POST['description'];
$address = $_POST['address'];

/* Optional photo upload */

$photo = NULL;

if(isset($_FILES['photo']) && $_FILES['photo']['name'] != ""){

$photo = time() . "_" . $_FILES['photo']['name'];

move_uploaded_file(
$_FILES['photo']['tmp_name'],
"../uploads/".$photo
);

}

/* Default booking values */

$status = "pending";
$price_status = "waiting";
$payment_status = "pending";

/* Insert booking request */

$stmt = $conn->prepare(
"INSERT INTO bookings
(customer_id,provider_id,date,time,description,address,photo,status,price_status,payment_status)
VALUES (?,?,?,?,?,?,?,?,?,?)"
);

$stmt->bind_param(
"iissssssss",
$customer_id,
$provider_id,
$date,
$time,
$description,
$address,
$photo,
$status,
$price_status,
$payment_status
);

$stmt->execute();

/* -------------------------
GET PROVIDER USER ID
-------------------------- */

$stmt3 = $conn->prepare("
SELECT user_id FROM providers WHERE id=?
");

$stmt3->bind_param("i",$provider_id);
$stmt3->execute();
$res = $stmt3->get_result();
$row = $res->fetch_assoc();

$provider_user_id = $row['user_id'];

/* -------------------------
CREATE NOTIFICATION
-------------------------- */

$message = "You have received a new booking request.";

$stmt2 = $conn->prepare(
"INSERT INTO notifications (user_id,message)
VALUES (?,?)"
);

$stmt2->bind_param("is",$provider_user_id,$message);
$stmt2->execute();

/* Redirect customer */

header("Location: ../customer/history.php");
exit();

?>