<?php
include("../config/session_check.php");
include("../config/database.php");

$provider_id = $_SESSION['user_id'];

// FETCH ALL FIELDS
$stmt = $conn->prepare("SELECT 
    p.id AS provider_id,
    p.service_type,
    u.id AS user_id,
    u.name,
    u.email,
    u.phone,
    u.profile_image,
    u.gender,
    u.language,
    u.dob,
    u.city,
    u.state,
    u.pincode
FROM providers p
JOIN users u ON p.user_id = u.id
WHERE p.user_id = ?");

$stmt->bind_param("i", $provider_id);
$stmt->execute();
$provider = $stmt->get_result()->fetch_assoc();

$current_img = !empty($provider['profile_image']) ? $provider['profile_image'] : 'default-pro.png';
?>

<?php include("navbar.php"); ?>
<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper" style="flex-direction: column; align-items: center; padding-top: 40px; padding-bottom: 40px;">

<div class="glass-card" style="max-width: 550px; width: 95%; padding: 40px;">
        
<div style="text-align: center; margin-bottom: 30px;">
<h2>Edit Business Profile</h2>
</div>

<form action="update_profile.php" method="POST" enctype="multipart/form-data">

<!-- IMAGE -->
<div class="pro-image-upload">
<div class="preview-ring">
<img src="../uploads/profiles/<?php echo $current_img; ?>" id="proPreview">
<label for="proFileInput" class="camera-btn">📷</label>
</div>
<input type="file" name="profile_image" id="proFileInput" hidden onchange="showPreview(this)">
</div>

<br>

<!-- BASIC -->
<input type="text" name="name" value="<?php echo htmlspecialchars($provider['name']); ?>" placeholder="Name" required>
<br><br>

<input type="email" name="email" value="<?php echo htmlspecialchars($provider['email']); ?>" placeholder="Email" required>
<br><br>

<input type="text" name="phone" value="<?php echo htmlspecialchars($provider['phone']); ?>" placeholder="Phone">
<br><br>

<input type="text" name="service" value="<?php echo htmlspecialchars($provider['service_type']); ?>" placeholder="Service">
<br><br>

<!-- NEW FIELDS -->
<select name="gender">
<option value="">Gender</option>
<option value="Male" <?php if($provider['gender']=="Male") echo "selected"; ?>>Male</option>
<option value="Female" <?php if($provider['gender']=="Female") echo "selected"; ?>>Female</option>
<option value="Other" <?php if($provider['gender']=="Other") echo "selected"; ?>>Other</option>
</select>
<br><br>

<input type="text" name="language" value="<?php echo htmlspecialchars($provider['language']); ?>" placeholder="Language">
<br><br>

<input type="date" name="dob" value="<?php echo $provider['dob']; ?>">
<br><br>

<input type="text" name="city" value="<?php echo htmlspecialchars($provider['city']); ?>" placeholder="City">
<br><br>

<input type="text" name="state" value="<?php echo htmlspecialchars($provider['state']); ?>" placeholder="State">
<br><br>

<input type="text" name="pincode" value="<?php echo htmlspecialchars($provider['pincode']); ?>" placeholder="Pincode">
<br><br>

<!-- BUTTON -->
<button class="btn" style="margin-top:20px; width:100%;">Update Profile</button>

<br><br>

<a href="profile.php" style="display:block; text-align:center; color: var(--text-muted); text-decoration:none;">
Cancel
</a>

</form>
</div>
</div>

<script>
function showPreview(input){
if(input.files && input.files[0]){
let reader = new FileReader();
reader.onload = function(e){
document.getElementById('proPreview').src = e.target.result;
}
reader.readAsDataURL(input.files[0]);
}
}
</script>
