<?php
include("../config/session_check.php");
include("../config/database.php");

$provider_id = $_SESSION['user_id'];

// ✅ FETCH UPDATED FIELDS
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
$result = $stmt->get_result();
$provider = $result->fetch_assoc();

// Fallback
$profile_img = !empty($provider['profile_image']) ? $provider['profile_image'] : 'default-pro.png';

// Rating
$rating_stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM ratings WHERE provider_id=?");
$rating_stmt->bind_param("i", $provider['provider_id']);
$rating_stmt->execute();
$rating_res = $rating_stmt->get_result()->fetch_assoc();

$avg_rating = round($rating_res['avg_rating'] ?? 0, 1);
$total_reviews = $rating_res['total_reviews'] ?? 0;
?>

<?php include("navbar.php"); ?>
<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper" style="flex-direction: column; align-items: center; padding-top: 60px;">

<div class="glass-card profile-main-card" style="border-top: 3px solid var(--accent-blue);">
        
<div class="pro-badge">LOSBO PRO</div>

<!-- PROFILE HEADER -->
<div class="profile-header">
<div class="image-wrapper">
<img src="../uploads/profiles/<?php echo $profile_img; ?>" class="profile-pic-large">
<div class="verified-check">✓</div>
</div>

<h2 style="margin-top: 15px;"><?php echo htmlspecialchars($provider['name']); ?></h2>

<div class="service-pill">
<?php echo htmlspecialchars($provider['service_type']); ?> Specialist
</div>

<!-- ⭐ RATING -->
<div style="margin-top: 10px;">
<?php
$full = floor($avg_rating);
for($i=0;$i<$full;$i++){ echo '<span style="color:#FFD700;">★</span>'; }
?>
<span style="color: var(--text-muted);">
<?php echo $avg_rating; ?> / 5 (<?php echo $total_reviews; ?> reviews)
</span>
</div>

<?php if($total_reviews > 0): ?>
<button id="viewReviewsBtn" class="btn" style="margin-top: 10px;">View Reviews</button>
<?php endif; ?>

</div>

<!-- REVIEWS -->
<div id="reviewsSection" style="display:none; margin-top: 20px;">
<?php
$reviews_stmt = $conn->prepare("SELECT rating, review, created_at FROM ratings WHERE provider_id=? ORDER BY created_at DESC");
$reviews_stmt->bind_param("i", $provider['provider_id']);
$reviews_stmt->execute();
$reviews_res = $reviews_stmt->get_result();

while($rev = $reviews_res->fetch_assoc()):
?>
<div class="glass-card" style="padding:15px; margin-bottom:10px;">
<p><?php echo str_repeat("⭐", $rev['rating']); ?></p>
<p><?php echo htmlspecialchars($rev['review']); ?></p>
<p style="font-size:0.75rem;">
<?php echo date("F j, Y", strtotime($rev['created_at'])); ?>
</p>
</div>
<?php endwhile; ?>
</div>

<hr style="margin:30px 0;">

<!-- ✅ UPDATED DETAILS -->
<div class="profile-details-grid">

<div class="detail-item">
<label>Email</label>
<span><?php echo htmlspecialchars($provider['email']); ?></span>
</div>

<div class="detail-item">
<label>Phone</label>
<span><?php echo htmlspecialchars($provider['phone']); ?></span>
</div>

<div class="detail-item">
<label>Gender</label>
<span><?php echo htmlspecialchars($provider['gender']); ?></span>
</div>

<div class="detail-item">
<label>Language</label>
<span><?php echo htmlspecialchars($provider['language']); ?></span>
</div>

<div class="detail-item">
<label>Date of Birth</label>
<span><?php echo htmlspecialchars($provider['dob']); ?></span>
</div>

<div class="detail-item">
<label>Location</label>
<span>
<?php 
echo htmlspecialchars($provider['city']) . ", " .
     htmlspecialchars($provider['state']) . " - " .
     htmlspecialchars($provider['pincode']);
?>
</span>
</div>

</div>

<!-- BUTTONS -->
<div style="margin-top: 30px;">
<a href="edit_profile.php" class="btn">Update Profile</a>
<br><br>
<a href="dashboard.php" style="opacity:0.6;">Back to Dashboard</a>
</div>

</div>
</div>

<script>
// Toggle Reviews
document.getElementById('viewReviewsBtn')?.addEventListener('click', function(){
let sec = document.getElementById('reviewsSection');

if(sec.style.display === 'none'){
sec.style.display = 'block';
this.textContent = "Hide Reviews";
}else{
sec.style.display = 'none';
this.textContent = "View Reviews";
}
});
</script>
