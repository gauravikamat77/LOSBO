<?php
include("../config/session_check.php");
include("../config/database.php");

$user_id = $_SESSION['user_id'];

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$service = $_POST['service'];

// Handle profile image upload
$image_name = null;
if(!empty($_FILES['profile_image']['name'])){
    $target_dir = "../uploads/profiles/";
    $image_name = time() . "_" . basename($_FILES['profile_image']['name']);
    if(!move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_dir . $image_name)){
        die("Failed to upload image.");
    }
}

// 1️⃣ Update users table
if($image_name){
    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, profile_image=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $email, $phone, $image_name, $user_id);
}else{
    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
}

if(!$stmt->execute()){
    die("Error updating users: ".$stmt->error);
}

// 2️⃣ Update providers table (only service_type)
$stmt2 = $conn->prepare("UPDATE providers SET service_type=? WHERE user_id=?");
$stmt2->bind_param("si", $service, $user_id);

if(!$stmt2->execute()){
    die("Error updating providers: ".$stmt2->error);
}

header("Location: profile.php");
exit();
?>