<?php
session_start();
include("../config/database.php");

// Make sure user is logged in
$user_id = $_SESSION['user_id'] ?? 0;

if($user_id == 0){
    die("Please log in first.");
}

// Fetch the user from DB
$sql = "SELECT id, name, email, phone, profile_image FROM users WHERE id = ? AND role='customer'";
$stmt = $conn->prepare($sql);

if(!$stmt){
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$usered = $result->fetch_assoc();

if(!$usered){
    die("User not found.");
}

// Fallback profile image
$profile_img = !empty($usered['profile_image']) ? $usered['profile_image'] : 'default.png';
?>

<?php include("navbar.php"); ?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper" style="flex-direction: column; align-items: center; padding-top: 60px;">
    <div class="glass-card profile-main-card">
        <div class="profile-header">
            <div class="image-container">
                <img src="../uploads/profiles/<?php echo $profile_img; ?>" alt="Profile Picture" class="profile-pic-large">
            </div>
            <h2><?php echo htmlspecialchars($usered['name']); ?></h2>
        </div>

        <hr style="margin: 20px 0;">

        <div class="profile-details-grid">
            <div class="detail-item">
                <label>Email Address:</label>
                <span><?php echo htmlspecialchars($usered['email']); ?></span>
            </div>

            <div class="detail-item">
                <label>Phone Number:</label>
                <span><?php echo htmlspecialchars($usered['phone']); ?></span>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <a href="edit_profile.php" class="btn">Edit Profile</a>
        </div>
    </div>
</div>