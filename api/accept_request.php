<?php

include("../config/database.php");

$id = $_GET['id'];

$stmt = $conn->prepare(
"UPDATE bookings SET status='accepted' WHERE id=?"
);

$stmt->bind_param("i",$id);
$stmt->execute();

header("Location: ../provider/requests.php");

?>