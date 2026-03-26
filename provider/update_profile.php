<?php
include("../config/session_check.php");
include("../config/database.php");

$user_id = $_SESSION['user_id'];

// ✅ SAFE FETCH (avoid undefined errors)
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$service = $_POST['service'] ?? '';

$gender = $_POST['gender'] ?? '';
$language = trim($_POST['language'] ?? '');
$dob = $_POST['dob'] ?? '';
$city = trim($_POST['city'] ?? '');
$state = trim($_POST['state'] ?? '');
$pincode = trim($_POST['pincode'] ?? '');

// ✅ BASIC VALIDATION
if(!$name || !$email){
    die("Name and Email are required.");
}

// ✅ HANDLE IMAGE UPLOAD
$image_name = null;

if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0){

    $target_dir = "../uploads/profiles/";
    
    if(!is_dir($target_dir)){
        mkdir($target_dir, 0755, true);
    }

    $image_name = time() . "_" . basename($_FILES['profile_image']['name']);

    if(!move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_dir . $image_name)){
        die("Failed to upload image.");
    }
}

// ✅ UPDATE USERS TABLE
if($image_name){

    $stmt = $conn->prepare("
        UPDATE users SET 
        name=?, email=?, phone=?, profile_image=?,
        gender=?, language=?, dob=?, city=?, state=?, pincode=?
        WHERE id=?
    ");

    if(!$stmt){
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "ssssssssssi",
        $name, $email, $phone, $image_name,
        $gender, $language, $dob, $city, $state, $pincode,
        $user_id
    );

} else {

    $stmt = $conn->prepare("
        UPDATE users SET 
        name=?, email=?, phone=?,
        gender=?, language=?, dob=?, city=?, state=?, pincode=?
        WHERE id=?
    ");

    if(!$stmt){
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "sssssssssi",
        $name, $email, $phone,
        $gender, $language, $dob, $city, $state, $pincode,
        $user_id
    );
}

// ✅ EXECUTE USER UPDATE
if(!$stmt->execute()){
    die("Error updating user: " . $stmt->error);
}

// ✅ UPDATE PROVIDER SERVICE (only if given)
if(!empty($service)){
    $stmt2 = $conn->prepare("UPDATE providers SET service_type=? WHERE user_id=?");

    if(!$stmt2){
        die("Prepare failed: " . $conn->error);
    }

    $stmt2->bind_param("si", $service, $user_id);

    if(!$stmt2->execute()){
        die("Error updating provider: " . $stmt2->error);
    }
}

// ✅ SUCCESS REDIRECT
header("Location: profile.php?updated=success");
exit();
?>
