<?php
include("../config/session_check.php");
include("../config/database.php");

$provider_id = $_SESSION['user_id'];

// Use your specific table (e.g., 'users' or 'providers')
$stmt = $conn->prepare("SELECT 
    p.id AS provider_id,
    p.service_type,
    u.id AS user_id,
    u.name,
    u.email,
    u.phone,
    u.profile_image
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
            <h2 style="margin: 0; color: white;">Edit Business Profile</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Manage your professional details and expertise</p>
        </div>

        <form action="update_profile.php" method="POST" enctype="multipart/form-data" class="pro-edit-form">
            
            <div class="pro-image-upload">
                <div class="preview-ring">
                    <img src="../uploads/profiles/<?php echo $current_img; ?>" id="proPreview">
                    <label for="proFileInput" class="camera-btn">📷</label>
                </div>
                <input type="file" name="profile_image" id="proFileInput" hidden accept="image/*" onchange="showPreview(this)">
                <p style="font-size: 0.7rem; color: var(--accent-blue); margin-top: 10px; font-weight: bold;">UPDATE PHOTO</p>
            </div>

            <div class="input-tile">
                <label>Business name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($provider['name']); ?>" required>
            </div>

            <div class="input-tile">
                <label>Professional Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($provider['email']); ?>" required>
            </div>

            <div class="input-tile">
                <label>Contact Number</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($provider['phone']); ?>">
            </div>

            <div class="input-tile">
                <label>Service Category</label>
                <input type="text" name="service" value="<?php echo htmlspecialchars($provider['service_type']); ?>" placeholder="e.g. Electrician, Plumber" >
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn" style="width: 100%;">Apply Changes</button>
                <a href="profile.php" style="display: block; text-align: center; margin-top: 15px; color: var(--text-muted); text-decoration: none; font-size: 0.85rem;">Discard and Exit</a>
            </div>

        </form>
    </div>
</div>

<script>
function showPreview(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('proPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>