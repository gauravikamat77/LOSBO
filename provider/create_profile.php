<?php

include("../config/database.php");
session_start();

$user_id = $_SESSION['user_id'];

$category = $_POST['category'];
$description = $_POST['description'];

/* If provider selected OTHER */

if($category == "other"){
    $category = $_POST['other_category'];
}

/* Save category in categories table */

$stmt = $conn->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
$stmt->bind_param("s", $category);
$stmt->execute();

/* Save provider profile */

$stmt = $conn->prepare("
INSERT INTO provider_profiles (user_id, category, description)
VALUES (?,?,?)
");

$stmt->bind_param("iss", $user_id, $category, $description);
$stmt->execute();

/* Redirect */

header("Location: dashboard.php");
exit();

?>