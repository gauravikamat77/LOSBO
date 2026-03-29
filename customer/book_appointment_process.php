<?php
include("../config/database.php");
include("../config/session_check.php");

$customer_id = $_SESSION['user_id'];
$provider_id = $_POST['provider_id'];
$date = $_POST['appointment_date'];
$time = $_POST['appointment_time'];
$address = $_POST['address'];
$description = $_POST['description'] ?? '';
$photo_name = null;

// Handle optional photo upload
if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0){
    $upload_dir = "../uploads/bookings/";
    
    // Create folder if it doesn't exist
    if(!is_dir($upload_dir)){
        mkdir($upload_dir, 0777, true);
    }

    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $photo_name = uniqid() . "_photo." . $ext;
    $target = $upload_dir . $photo_name;

    if(!move_uploaded_file($_FILES['photo']['tmp_name'], $target)){
        die("Failed to upload photo.");
    }
}

// Insert into bookings
$stmt = $conn->prepare("INSERT INTO bookings (customer_id, provider_id, date, time, description,address, photo, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");


if(!$stmt){
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("iisssss", $customer_id, $provider_id, $date, $time, $description,$address, $photo_name);

if($stmt->execute()){
    $booking_id = mysqli_insert_id($conn);
    // Get provider's user_id
    $getUser = $conn->prepare("SELECT user_id FROM providers WHERE id = ?");
    $getUser->bind_param("i", $provider_id);
    $getUser->execute();
    $result = $getUser->get_result();
    $row = $result->fetch_assoc();

    $provider_user_id = $row['user_id'];
    $query = "INSERT INTO notifications (sender_id, receiver_id, message, booking_id) VALUES ('$customer_id', '$provider_user_id', 'New booking request', '$booking_id')";
    mysqli_query($conn, $query);
    
    header("Location: bookings_success.php");
    // Send notification to provider
    // addNotification($conn, $user_id, 'customer', "Your order was placed successfully");
    
    exit;
}else{
    die("Failed to book appointment: " . $stmt->error);
}
?>