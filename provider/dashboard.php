<?php
include("../config/session_check.php");
include("../config/database.php");

// Get logged-in provider's user ID
$provider_user_id = $_SESSION['user_id'] ?? 0;
if(!$provider_user_id){
    die("You must be logged in as a provider.");
}

// 1️⃣ Fetch provider's name from users table
$stmt_name = $conn->prepare("SELECT name FROM users WHERE id=?");
$stmt_name->bind_param("i", $provider_user_id);
$stmt_name->execute();
$result_name = $stmt_name->get_result();
$user_row = $result_name->fetch_assoc();
$provider_name = $user_row['name'] ?? "Provider";

// 2️⃣ Fetch provider's internal ID from providers table
$stmt_provider = $conn->prepare("SELECT id FROM providers WHERE user_id=?");
$stmt_provider->bind_param("i", $provider_user_id);
$stmt_provider->execute();
$result_provider = $stmt_provider->get_result();
$provider_row = $result_provider->fetch_assoc();
$provider_id = $provider_row['id'] ?? 0;

// 3️⃣ Count new requests (pending bookings)
$stmt_requests = $conn->prepare("SELECT COUNT(*) AS total_requests FROM bookings WHERE provider_id=? AND status='pending'");
$stmt_requests->bind_param("i", $provider_id);
$stmt_requests->execute();
$result_requests = $stmt_requests->get_result();
$requests_row = $result_requests->fetch_assoc();
$new_requests = $requests_row['total_requests'] ?? 0;

// 4️⃣ Count today's jobs (accepted, not completed, scheduled for today)
$today = date("Y-m-d");
$stmt_today = $conn->prepare("SELECT COUNT(*) AS total_today FROM bookings WHERE provider_id=? AND status='accepted' AND date=?");
$stmt_today->bind_param("is", $provider_id, $today);
$stmt_today->execute();
$result_today = $stmt_today->get_result();
$today_row = $result_today->fetch_assoc();
$todays_jobs = $today_row['total_today'] ?? 0;

// 5️⃣ Calculate monthly revenue (only completed & paid jobs in current month)
$current_month = date("m");
$current_year = date("Y");
$stmt_revenue = $conn->prepare("
    SELECT SUM(price) AS monthly_revenue 
    FROM bookings 
    WHERE provider_id=? 
    AND status='completed' 
    AND payment_status='paid'
    AND MONTH(date)=? 
    AND YEAR(date)=?
");
$stmt_revenue->bind_param("iii", $provider_id, $current_month, $current_year);
$stmt_revenue->execute();
$result_revenue = $stmt_revenue->get_result();
$revenue_row = $result_revenue->fetch_assoc();
$monthly_revenue = $revenue_row['monthly_revenue'] ?? 0;
?>

<link rel="stylesheet" href="../assets/css/style.css">
<?php include("navbar.php"); ?>

<div class="page-wrapper" style="flex-direction: column; justify-content: flex-start; padding-top: 100px; max-width: 1000px; margin: auto;">

    <!-- Greeting Card -->
    <div class="glass-card" style="width: 100%; max-width: 1000px; text-align: left; margin-bottom: 25px;">
        <h1 style="font-size: 2.2rem; margin-bottom: 10px;">
            Hello, <span style="color: var(--accent-blue);"><?php echo htmlspecialchars($provider_name); ?></span>
        </h1>
        <p style="color: var(--text-muted); font-size: 1.1rem;">Manage your services, track appointments, and grow your business.</p>
    </div>

    <!-- Stats Cards -->
    <div style="display: flex; gap: 20px; width: 100%; max-width: 1000px; flex-wrap: wrap;">

        <div class="glass-card" style="flex: 1; min-width: 200px; padding: 25px; text-align: center;">
            <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 10px;">New Requests</div>
            <div style="font-size: 2rem; font-weight: 800; color: var(--accent-blue);"><?php echo $new_requests; ?></div>
        </div>

        <div class="glass-card" style="flex: 1; min-width: 200px; padding: 25px; text-align: center;">
            <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 10px;">Today's Jobs</div>
            <div style="font-size: 2rem; font-weight: 800; color: #ffcc00;"><?php echo $todays_jobs; ?></div>
        </div>

        <div class="glass-card" style="flex: 1; min-width: 200px; padding: 25px; text-align: center;">
            <div style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 10px;">Earnings (Monthly)</div>
            <div style="font-size: 2rem; font-weight: 800; color: #00e676;">₹<?php echo number_format($monthly_revenue); ?></div>
        </div>

    </div>

    <!-- Actions -->
    <div style="margin-top: 30px; width: 100%; max-width: 1000px; display: flex; gap: 15px;">
        <a href="requests.php" class="btn" style="width: auto; padding: 12px 30px;">Check New Requests</a>
        <a href="schedule.php" class="btn" style="width: auto; padding: 12px 30px; background: rgba(255,255,255,0.05); color: white; border: 1px solid var(--glass-border);">View Full Schedule</a>
    </div>

</div>