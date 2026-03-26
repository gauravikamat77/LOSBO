<?php
include("../config/database.php");
include("../config/session_check.php");

// Get provider ID
$provider_user_id = $_GET['provider_id'] ?? 0;
if (!$provider_user_id) {
    die("No provider specified.");
}

// Fetch provider details
$stmt = $conn->prepare("
    SELECT 
        u.id AS user_id,
        u.name,
        u.email,
        u.phone,
        u.profile_image,
        p.id AS provider_id,
        p.service_type
    FROM providers p
    JOIN users u ON p.user_id = u.id
    WHERE p.id = ? AND u.role = 'provider'
");
$stmt->bind_param("i", $provider_user_id);
$stmt->execute();
$result = $stmt->get_result();
$provider = $result->fetch_assoc();

if (!$provider) {
    die("Provider not found.");
}

// Profile image fallback
$profile_img = !empty($provider['profile_image']) ? $provider['profile_image'] : 'default.png';

// Ratings
$rating_stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM ratings WHERE provider_id=?");
$rating_stmt->bind_param("i", $provider['provider_id']);
$rating_stmt->execute();
$rating_result = $rating_stmt->get_result();
$rating_data = $rating_result->fetch_assoc();

$avg_rating = $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : "Not Rated";
$total_reviews = $rating_data['total_reviews'] ?? 0;
?>

<?php include("../customer/navbar.php"); ?>
<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper" style="flex-direction: column; align-items: center; padding-top: 80px;">

    <div class="glass-card profile-main-card" style="max-width: 500px; width: 100%; text-align: center;">

        <!-- Profile -->
        <div class="profile-header">
            <img src="../uploads/profiles/<?php echo htmlspecialchars($profile_img); ?>" 
                 class="profile-pic-large"
                 style="width:120px; height:120px; border-radius:50%; object-fit:cover; border:2px solid var(--accent-blue);">

            <h2 style="margin-top:15px;"><?php echo htmlspecialchars($provider['name']); ?></h2>

            <p style="color:var(--accent-blue); font-size:0.85rem; text-transform:uppercase;">
                Service Provider
            </p>

            <!-- Rating -->
            <div style="margin-top:10px;">
                <?php
                if(is_numeric($avg_rating)){
                    for($i=0;$i<floor($avg_rating);$i++){ echo '<span style="color:#FFD700;">★</span>'; }
                    echo " ($avg_rating / 5)";
                } else {
                    echo $avg_rating;
                }
                ?>
            </div>
        </div>

        <hr style="margin:20px 0;">

        <!-- Details -->
        <div style="text-align:left;">
            <p><b>Email:</b> <?php echo htmlspecialchars($provider['email']); ?></p>
            <p><b>Phone:</b> <?php echo htmlspecialchars($provider['phone']); ?></p>
            <p><b>Service:</b> <?php echo htmlspecialchars($provider['service_type']); ?></p>
        </div>

        <!-- 🔥 BIG CTA BUTTON -->
        <a href="book_appointment.php?provider_id=<?php echo $provider['provider_id']; ?>" 
           class="book-btn">
           🚀 Book Appointment
        </a>

    </div>
</div>

<!-- ⭐ REVIEWS SECTION -->
<div class="page-wrapper" style="flex-direction: column; align-items: center; padding-top:20px;">
    <div class="glass-card" style="max-width:500px; width:100%;">
        <h3>Reviews (<?php echo $total_reviews; ?>)</h3>

        <?php
        $reviews_stmt = $conn->prepare("SELECT rating, review FROM ratings WHERE provider_id=?");
        $reviews_stmt->bind_param("i", $provider['provider_id']);
        $reviews_stmt->execute();
        $reviews_res = $reviews_stmt->get_result();

        while($rev = $reviews_res->fetch_assoc()):
        ?>
            <p>
                <?php echo str_repeat("⭐", $rev['rating']); ?>  
                <?php echo htmlspecialchars($rev['review']); ?>
            </p>
        <?php endwhile; ?>

    </div>
</div>

<!-- 🔥 BUTTON STYLE -->
<style>
.book-btn{
    display:block;
    width:100%;
    padding:16px;
    font-size:1.2rem;
    font-weight:bold;
    text-align:center;
    color:white;
    background: linear-gradient(135deg, #4db8ff, #0066ff);
    border-radius:12px;
    text-decoration:none;
    margin-top:25px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 102, 255, 0.4);
}

.book-btn:hover{
    transform: scale(1.05);
    box-shadow: 0 10px 30px rgba(0, 102, 255, 0.7);
    background: linear-gradient(135deg, #3399ff, #0052cc);
}
</style>