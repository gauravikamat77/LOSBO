<?php
include("../config/session_check.php");
include("../config/database.php");
include("../provider/navbar.php"); 

$user_id = $_SESSION['user_id'] ?? 0;
if(!$user_id){
    die("You must be logged in.");
}

// Get provider ID
$stmt_provider = $conn->prepare("SELECT id FROM providers WHERE user_id=?");
$stmt_provider->bind_param("i", $user_id);
$stmt_provider->execute();
$prov_res = $stmt_provider->get_result()->fetch_assoc();
$provider_id = $prov_res['id'] ?? 0;

if(!$provider_id){
    die("Provider profile not found.");
}

// Total earnings (completed + paid)
$total_stmt = $conn->prepare("
    SELECT SUM(price) AS total 
    FROM bookings 
    WHERE provider_id=? AND status='completed' AND payment_status='paid'
");
$total_stmt->bind_param("i", $provider_id);
$total_stmt->execute();
$total_res = $total_stmt->get_result()->fetch_assoc();
$total_earnings = $total_res['total'] ?? 0;

// Fetch completed + paid bookings
$stmt = $conn->prepare("
    SELECT * 
    FROM bookings 
    WHERE provider_id=? AND status='completed' AND payment_status='paid' 
    ORDER BY date DESC
");
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$results = $stmt->get_result();
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper" style="flex-direction: column; align-items: center; padding-top: 40px; padding-bottom: 60px;">

    <!-- Header Card -->
    <div class="glass-card" style="max-width: 900px; width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 30px; border-left: 5px solid #00e676; margin-bottom: 30px;">
        <div>
            <h2 style="margin: 0; font-size: 1.8rem;">Service History</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Your lifetime business performance</p>
        </div>
        <div style="text-align: right;">
            <p style="font-size: 0.75rem; color: #00e676; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">Total Revenue</p>
            <h1 style="margin: 0; color: white;">₹<?php echo number_format($total_earnings, 2); ?></h1>
        </div>
    </div>

    <!-- Completed Bookings Cards -->
    <div style="max-width: 900px; width: 100%; display: flex; flex-direction: column; gap: 20px;">

        <?php if($results->num_rows == 0): ?>
            <div class="glass-card" style="text-align: center; padding: 60px;">
                <p style="color: var(--text-muted);">No completed and paid services found yet.</p>
            </div>
        <?php endif; ?>

        <?php while($row = $results->fetch_assoc()): ?>
            <div class="history-card" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-radius: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                
                <!-- Left: Date -->
                <div class="card-date" style="text-align: center; min-width: 70px; padding: 10px; background: #1e1e1e; border-radius: 8px;">
                    <span style="display: block; font-size: 1.2rem; font-weight: bold;"><?php echo date("d", strtotime($row['date'])); ?></span>
                    <span style="display: block; font-size: 0.8rem;"><?php echo date("M", strtotime($row['date'])); ?></span>
                </div>

                <!-- Middle: Booking Details -->
                <div class="card-details" style="flex: 1; margin-left: 15px;">
                    <h4 style="margin: 0; color: white;">Booking #<?php echo $row['id']; ?></h4>
                    <p style="margin: 5px 0; font-size: 0.85rem; color: var(--text-muted);">
                        Customer ID: <?php echo $row['customer_id']; ?>
                    </p>
                    <p style="margin: 5px 0; font-size: 0.85rem; color: var(--text-muted);">
                        Status: <span style="color: #00e676;">Completed</span> • 
                        Payment: <span style="color: var(--accent-blue); text-transform: capitalize;"><?php echo $row['payment_status']; ?></span>
                    </p>
                </div>

                <!-- Right: Price -->
                <div class="card-price" style="text-align: right; min-width: 100px; font-weight: bold; color: #00e676; font-size: 1.1rem;">
                    + ₹<?php echo number_format($row['price'], 2); ?>
                </div>
            </div>
        <?php endwhile; ?>

    </div>
</div>