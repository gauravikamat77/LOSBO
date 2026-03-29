<?php
include("database.php");

if (!isset($_POST['id'])) {
    exit;
}

$id = $_POST['id'];

$stmt = $conn->prepare("UPDATE notifications SET status = 1 WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo "success";
?>