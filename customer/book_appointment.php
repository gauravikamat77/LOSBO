<?php
include("../config/session_check.php");
include("../config/database.php");


// Get provider_id from URL
$provider_id = $_GET['provider_id'] ?? 0;

if(!$provider_id){
    die("Provider not specified.");
}

// Query provider info by joining providers -> users
$stmt = $conn->prepare(
    "SELECT p.id AS provider_id, p.service_type, u.id AS user_id, u.name, u.email, u.profile_image 
     FROM providers p 
     JOIN users u ON p.user_id = u.id 
     WHERE p.id = ? AND u.role='provider'"
);

if(!$stmt){
    die("Prepare failed: ".$conn->error);
}

$stmt->bind_param("i", $provider_id);
$stmt->execute();
$result = $stmt->get_result();
$provider = $result->fetch_assoc();

if(!$provider){
    die("Provider not found.");
}

// For debugging
// var_dump($provider);
?>


<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper" style="flex-direction: column; align-items: center; padding-top: 80px;">
    <div class="glass-card" style="max-width:500px; width:100%;">
        <h2>Book Appointment with <?php echo htmlspecialchars($provider['name']); ?></h2>
        <p>Service: <?php echo htmlspecialchars($provider['service_type']); ?></p>

        <form action="book_appointment_process.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="provider_id" value="<?php echo $provider_id; ?>">
            <input type="hidden" name="service_type" value="<?php echo htmlspecialchars($provider['service_type']); ?>">

            <label>Date</label>
            <input type="date" name="appointment_date" required>

            <label>Time</label>
            <input type="time" name="appointment_time" required>

            <label>Address</label>
            <input type="text" name="address" placeholder="Enter your address" required>
            <br><br>
            <label>Description</label>
            <textarea name="description" placeholder="Describe the task (optional)"></textarea>
            <br><br>
            <label>Upload Photo (Optional)</label>
            <input type="file" name="photo" accept="image/*">

            <button class="btn" type="submit" style="margin-top: 15px;">Send Booking Request</button>
           
        </form>
        
    </div>
    <br>
    <a href="dashboard.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.8rem; opacity: 0.6; text-align: center;">Back to Dashboard</a>
</div>