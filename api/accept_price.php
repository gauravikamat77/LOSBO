<?php

include("../config/database.php");

$id = $_GET['id'];

$stmt = $conn->prepare(
"UPDATE bookings 
SET price_status='accepted', status='confirmed'
WHERE id=?"
);

$stmt->bind_param("i",$id);
$stmt->execute();

header("Location: ../customer/history.php");

?>