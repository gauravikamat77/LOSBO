<?php
include("../config/session_check.php");
include("../config/database.php");

$provider_id = $_SESSION['user_id'];

// Fetch provider details
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
$result = $stmt->get_result();
$provider = $result->fetch_assoc();

// Fallback for profile image
$profile_img = !empty($provider['profile_image']) ? $provider['profile_image'] : 'default-pro.png';

// Fetch average rating and total reviews
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

        <div class="profile-header">
            <div class="image-wrapper">
                <img src="../uploads/profiles/<?php echo $profile_img; ?>" class="profile-pic-large">
                <div class="verified-check">✓</div>
            </div>
            <h2 style="margin-top: 15px; color: white;"><?php echo htmlspecialchars($provider['name']); ?></h2>
            <div class="service-pill"><?php echo htmlspecialchars($provider['service_type']); ?> Specialist</div>

            <!-- Average Rating -->
            <div class="average-rating" style="margin-top: 10px; display: flex; align-items: center; gap: 10px;">
                <?php
                $full_stars = floor($avg_rating);
                $half_star = ($avg_rating - $full_stars) >= 0.5 ? true : false;
                for($i=0; $i<$full_stars; $i++){ echo '<span style="color:#FFD700;font-size:1.2rem;">★</span>'; }
                if($half_star){ echo '<span style="color:#FFD700;font-size:1.2rem;">☆</span>'; }
                $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
                for($i=0; $i<$empty_stars; $i++){ echo '<span style="color:#777;font-size:1.2rem;">★</span>'; }
                ?>
                <span style="color: var(--text-muted); font-size: 0.9rem;">
                    <?php echo $avg_rating; ?> / 5 (<?php echo $total_reviews; ?> reviews)
                </span>
            </div>

            <!-- View Reviews Button -->
            <?php if($total_reviews > 0): ?>
                <button id="viewReviewsBtn" class="btn" style="margin-top: 10px; width: auto; padding: 5px 15px;">View Reviews</button>
            <?php endif; ?>

        </div>

        <!-- Reviews Section (Hidden by default) -->
        <div id="reviewsSection" style="display:none; margin-top: 20px;">
            <?php
            $reviews_stmt = $conn->prepare("SELECT rating, review, created_at FROM ratings WHERE provider_id=? ORDER BY created_at DESC");
            $reviews_stmt->bind_param("i", $provider['provider_id']);
            $reviews_stmt->execute();
            $reviews_res = $reviews_stmt->get_result();
            while($rev = $reviews_res->fetch_assoc()):
            ?>
                <div class="glass-card" style="padding:15px; margin-bottom:10px;">
                    <p>
                        <?php
                        // Display stars
                        $r_full = floor($rev['rating']);
                        $r_half = ($rev['rating'] - $r_full) >= 0.5 ? true : false;
                        for($i=0;$i<$r_full;$i++){ echo '<span style="color:#FFD700;">★</span>'; }
                        if($r_half){ echo '<span style="color:#FFD700;">☆</span>'; }
                        for($i=0;$i<5-$r_full-($r_half?1:0);$i++){ echo '<span style="color:#777;">★</span>'; }
                        ?>
                        <span style="color: var(--text-muted); font-size:0.85rem; margin-left:5px;"><?php echo $rev['rating']; ?> / 5</span>
                    </p>
                    <p style="margin-top:5px;"><?php echo htmlspecialchars($rev['review']); ?></p>
                    <p style="color: var(--text-muted); font-size:0.75rem; margin-top:5px;">Posted on: <?php echo date("F j, Y", strtotime($rev['created_at'])); ?></p>
                </div>
            <?php endwhile; ?>
        </div>

        <hr style="border:0;border-top:1px solid rgba(255,255,255,0.1);margin:30px 0;">

        <div class="profile-details-grid">
            <div class="detail-item">
                <label>Professional Email</label>
                <span><?php echo htmlspecialchars($provider['email']); ?></span>
            </div>
            
            <div class="detail-item">
                <label>Business Contact</label>
                <span><?php echo htmlspecialchars($provider['phone']); ?></span>
            </div>
        </div>

        <div style="margin-top: 40px; display: flex; flex-direction: column; gap: 10px;">
            <a href="edit_profile.php" class="btn" style="width: 100%;">Update Business Profile</a>
            <br>
            <a href="dashboard.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.8rem; opacity: 0.6; text-align: center;">Back to Dashboard</a>
        </div>
    </div>
</div>

<script>
// Toggle Reviews Section
document.getElementById('viewReviewsBtn')?.addEventListener('click', function(){
    const section = document.getElementById('reviewsSection');
    if(section.style.display === 'none'){
        section.style.display = 'block';
        this.textContent = 'Hide Reviews';
    } else {
        section.style.display = 'none';
        this.textContent = 'View Reviews';
    }
});
</script>