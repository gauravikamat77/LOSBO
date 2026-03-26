<?php
include("../config/session_check.php");
include("../config/database.php");
include("navbar.php"); 


$user_id = $_SESSION['user_id'] ?? 0;

// ✅ FETCH ALL FIELDS
$sql = "SELECT 
    id, name, email, phone, profile_image,
    gender, language, dob, city, state, pincode
FROM users WHERE id = ?";

$stmt = $conn->prepare($sql);

if(!$stmt){
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

// ✅ FIXED IMAGE BUG
$current_img = !empty($customer['profile_image']) ? $customer['profile_image'] : 'default.png';
?>

<?php include("navbar.php"); ?>
<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper" style="flex-direction: column; align-items: center; padding-top: 50px; padding-bottom: 50px;">

<div class="glass-card" style="max-width: 550px; width: 95%; padding: 40px;">
        
<div style="text-align: center; margin-bottom: 30px;">
<h2>Edit Profile</h2>
<p style="color: var(--text-muted); font-size: 0.9rem;">Update your personal information</p>
</div>

<form action="update_profile.php" method="POST" enctype="multipart/form-data" class="edit-form">

<!-- IMAGE -->
<div class="image-upload-section">
<div class="preview-container">
<img src="../uploads/profiles/<?php echo $current_img; ?>" id="imgPreview">
<label for="fileInput" class="upload-badge">📷</label>
</div>
<input type="file" name="profile_image" id="fileInput" hidden onchange="previewImage(this)">
</div>

<br>

<!-- BASIC -->
<div class="input-group">
<label>Full name</label>
<input type="text" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
</div>
<br>

<div class="input-group">
<label>Email</label>
<input type="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
</div>
<br>

<div class="input-group">
<label>Phone</label>
<input type="text" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>">
</div>
<br>

<!-- ✅ NEW FIELDS -->
<div class="input-group">
<label>Gender</label>
<select name="gender">
<option value="">Select</option>
<option value="Male" <?php if($customer['gender']=="Male") echo "selected"; ?>>Male</option>
<option value="Female" <?php if($customer['gender']=="Female") echo "selected"; ?>>Female</option>
<option value="Other" <?php if($customer['gender']=="Other") echo "selected"; ?>>Other</option>
</select>
</div>
<br>

<div class="input-group">
<label>Language</label>
<input type="text" name="language" value="<?php echo htmlspecialchars($customer['language']); ?>">
</div>
<br>

<div class="input-group">
<label>Date of Birth</label>
<input type="date" name="dob" value="<?php echo $customer['dob']; ?>">
</div>
<br>

<div class="input-group">
<label>City</label>
<input type="text" name="city" value="<?php echo htmlspecialchars($customer['city']); ?>">
</div>
<br>

<div class="input-group">
<label>State</label>
<input type="text" name="state" value="<?php echo htmlspecialchars($customer['state']); ?>">
</div>
<br>

<div class="input-group">
<label>Pincode</label>
<input type="text" name="pincode" value="<?php echo htmlspecialchars($customer['pincode']); ?>">
</div>
<br>

<!-- BUTTON -->
<div style="margin-top: 25px;">
<button type="submit" class="btn" style="width: 100%;">Save Profile</button>

<a href="profile.php" 
style="display:block; text-align:center; margin-top:15px; color: var(--text-muted); text-decoration:none;">
Cancel
</a>
</div>

</form>
</div>
</div>

<script>
function previewImage(input){
if(input.files && input.files[0]){
let reader = new FileReader();
reader.onload = function(e){
document.getElementById('imgPreview').src = e.target.result;
}
reader.readAsDataURL(input.files[0]);
}
}
</script>
