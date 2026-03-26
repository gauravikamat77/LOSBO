<?php

include("../config/database.php");

$id = $_GET['id'];

$conn->query("UPDATE bookings SET status='rejected' WHERE id='$id'");

header("Location: ../provider/requests.php");

?>