<?php
session_start();
include("../config/database.php");

// Check login
$user_id = $_SESSION['user_id'] ?? 0;

if($user_id == 0){
    die("Please log in first.");
}

// ✅ FETCH ALL NEW FIELDS
$sql = "SELECT 
    id, name, email, phone, profile_image,
    gender, language, dob, city, state, pincode
    FROM users 
    WHERE id = ? AND role='customer'";

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

// Fallback image
$profile_img = !empty($usered['profile_image']) ? $usered['profile_image'] : 'default.png';
?>

<?php include("navbar.php"); ?>
<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper" style="flex-direction: column; align-items: center; padding-top: 60px;">

<div class="glass-card profile-main-card" style="max-width:600px; width:95%; padding:40px;">

<!-- HEADER -->
<div class="profile-header" style="text-align:center;">
    <div class="image-container">
        <img src="../uploads/profiles/<?php echo $profile_img; ?>" 
             class="profile-pic-large"
             style="width:120px; height:120px; border-radius:50%; object-fit:cover;">
    </div>

    <h2 style="margin-top:15px;">
        <?php echo htmlspecialchars($usered['name']); ?>
    </h2>
</div>

<hr style="margin: 25px 0;">

<!-- DETAILS -->
<div style="display:flex; flex-direction:column; gap:15px;">

<div class="detail-item">
<label>Email</label>
<span><?php echo htmlspecialchars($usered['email']); ?></span>
</div>

<div class="detail-item">
<label>Phone</label>
<span><?php echo htmlspecialchars($usered['phone']); ?></span>
</div>

<div class="detail-item">
<label>Gender</label>
<span><?php echo htmlspecialchars($usered['gender']); ?></span>
</div>

<div class="detail-item">
<label>Language</label>
<span><?php echo htmlspecialchars($usered['language']); ?></span>
</div>

<div class="detail-item">
<label>Date of Birth</label>
<span><?php echo htmlspecialchars($usered['dob']); ?></span>
</div>

<div class="detail-item">
<label>Address</label>
<span>
<?php 
echo htmlspecialchars($usered['city']) . ", " .
     htmlspecialchars($usered['state']) . " - " .
     htmlspecialchars($usered['pincode']);
?>
</span>
</div>

</div>

<!-- BUTTON -->
<div style="margin-top:30px;">
<a href="edit_profile.php" class="btn" style="width:100%;">Edit Profile</a>
</div>

</div>
</div>
