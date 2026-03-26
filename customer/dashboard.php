<?php
include("../config/session_check.php");
include("../config/database.php");

// Get current user ID from session
$user_id = $_SESSION['user_id'] ?? 0;

if ($user_id == 0) {
    die("Please log in first.");
}

// Fetch latest user details from DB
$stmt = $conn->prepare("SELECT name, profile_image FROM users WHERE id=? AND role='customer'");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fallbacks
$current_name = $user['name'] ?? "Customer";

// ✅ FORMAT NAME PROPERLY (capitalize each word)
$formatted_name = ucwords(strtolower($current_name));

$profile_img = !empty($user['profile_image']) ? $user['profile_image'] : "default.png";
?>

<?php include("navbar.php"); ?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper" style="flex-direction: column; justify-content: flex-start; padding-top: 100px;">

    <div class="glass-card" style="width: 100%; max-width: 900px; text-align: left;">
        
        <div style="display:flex; align-items:center; gap:15px; margin-bottom:20px;">
            
            <img src="../uploads/profiles/<?php echo htmlspecialchars($profile_img); ?>" 
                 alt="Profile Picture" 
                 style="width:50px; height:50px; border-radius:50%; object-fit:cover; border: 2px solid var(--accent-blue);">
            
            <h1 style="font-size: 2rem; margin:0;">
                Welcome, 
                <span style="color: var(--accent-blue);">
                    <?php echo htmlspecialchars($formatted_name); ?>
                </span>!
            </h1>

        </div>

        <p style="color: var(--text-muted); font-size: 1.2rem;">
            What service do you need today? Explore categories to find trusted local experts.
        </p>
        
        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 30px 0;">

        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="categories.php" class="btn" style="width: auto; padding: 12px 25px;">
                Browse Categories
            </a>

            <a href="history.php" class="btn" 
               style="width: auto; padding: 12px 25px; background: rgba(255,255,255,0.1); color: white;">
                View Past Bookings
            </a>
        </div>

    </div>

</div>