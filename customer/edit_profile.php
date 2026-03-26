<?php
include("../config/session_check.php");
include("../config/database.php");

$user_id = $_SESSION['user_id'] ?? 0;

// Correct SQL (make sure table and columns exist!)
$sql = "SELECT id, name, email, phone, profile_image FROM users WHERE id = ?";

$stmt = $conn->prepare($sql);

if(!$stmt){
    die("SQL Error: " . $conn->error);
}

// Bind param now
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$customer = $result->fetch_assoc();
?>

<?php include("navbar.php"); ?>
<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper" style="flex-direction: column; align-items: center; padding-top: 50px; padding-bottom: 50px;">

    <div class="glass-card" style="max-width: 550px; width: 95%; padding: 40px;">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="margin: 0; color: white;">Edit Profile Here</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Update your personal information and presence</p>
        </div>

        <form action="update_profile.php" method="POST" enctype="multipart/form-data" class="edit-form">
            
            <div class="image-upload-section">
                <div class="preview-container">
                    <img src="../uploads/profiles/<?php echo $current_img; ?>" id="imgPreview">
                    <label for="fileInput" class="upload-badge">📷</label>
                </div>
                <input type="file" name="profile_image" id="fileInput" hidden accept="image/*" onchange="previewImage(this)">
                <p style="font-size: 0.75rem; color: var(--accent-blue); margin-top: 10px;">Click the camera icon to change photo</p>
            </div>

            <div class="input-group">
                <label>Full name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
            </div>

            <div class="input-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>">
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn" style="width: 100%;">Save Professional Profile</button>
                <a href="profile.php" style="display: block; text-align: center; margin-top: 15px; color: var(--text-muted); text-decoration: none; font-size: 0.85rem;">Cancel and Go Back</a>
            </div>

        </form>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imgPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>