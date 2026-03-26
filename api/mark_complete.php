<?php

include("../config/database.php");

$id = $_GET['id'];

$conn->query("UPDATE bookings SET status='completed' WHERE id='$id'");

header("Location: ../provider/schedule.php");

?>