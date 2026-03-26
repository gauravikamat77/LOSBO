<?php
include("../config/session_check.php");
include("../config/database.php");

$customer_ids = $_SESSION['user_id'];

$queryies = "SELECT 
b.*,
p.profile_image,
u.name AS provider_name
FROM bookings b
LEFT JOIN providers p ON b.provider_id = p.id
LEFT JOIN users u ON p.user_id = u.id
WHERE b.customer_id = '$customer_ids'";

$results = $conn->query($queryies);

if(!$results){
    die("SQL Error: " . $conn->error);
}
?>

<link rel="stylesheet" href="../assets/css/style.css">
<?php include("../customer/navbar.php"); ?>

<div class="page-wrapper" style="flex-direction:column;padding-top:100px;">

<div style="max-width:800px;width:100%;margin-bottom:30px;">
<h2 class="logo-title">Service History</h2>
<p class="slogan">Track your bookings and service progress</p>
</div>

<div style="max-width:800px;width:100%;display:flex;flex-direction:column;gap:20px;">

<?php if($results->num_rows > 0): ?>

<?php while($row = $results->fetch_assoc()): ?>

<?php
$status = $row['status'] ?? "pending";
$price_status = $row['price_status'] ?? "";
$payment_status = $row['payment_status'] ?? "";

$label = "Pending";
$color = "#ffcc00"; // default yellow

if($status == "accepted"){
    $label = "Accepted";
    $color = "#4db8ff"; // blue
}

if($status == "rejected"){
    $label = "Rejected";
    $color = "#ff3d00"; // red
}

if($price_status == "sent"){
    $label = "Payment Requested";
    $color = "#b388ff"; // purple
}

if($payment_status == "paid"){
    $label = "Payment Paid";
    $color = "#00e676"; // green
}

if($status == "completed"){
    $label = "Completed";
    $color = "#00e676"; // green
}

$image = $row['photo'] ?? "";

// Check if rating already exists for this booking
$rating_query = $conn->prepare("SELECT id FROM ratings WHERE booking_id=? LIMIT 1");
$rating_query->bind_param("i", $row['id']);
$rating_query->execute();
$rating_res = $rating_query->get_result();
$rating_done = $rating_res->num_rows > 0; // true if already rated
?>

<div class="glass-card" style="padding:25px;">

<div style="display:flex;gap:20px;align-items:flex-start;">

<!-- Issue Image -->
<?php if(!empty($image)): ?>
<img src="../uploads/bookings/<?php echo htmlspecialchars($image); ?>" 
style="width:100px;height:100px;border-radius:10px;object-fit:cover;">
<?php endif; ?>

<!-- Booking Info -->
<div style="flex:1;">

<div style="display:flex;justify-content:space-between;align-items:center;">

<h3 style="margin:0;">
    <a href="provider_profile.php?provider_id=<?php echo $row['provider_id']; ?>" 
       style="color:white; text-decoration:none; display:inline-block; transition: transform 0.3s, color 0.3s;"
       onmouseover="this.style.transform='scale(1.2)'; this.style.color='var(--accent-blue)';"
       onmouseout="this.style.transform='scale(1)'; this.style.color='white';">
       <?php echo htmlspecialchars($row['provider_name'] ?? "Service Provider"); ?>
    </a>
</h3>

<span style="
padding:6px 14px;
border-radius:20px;
font-size:0.8rem;
font-weight:bold;
color:<?php echo $color; ?>;
border:1px solid rgba(255,255,255,0.1);
">
<?php echo $label; ?>
</span>

</div>

<p style="color:var(--text-muted);margin-top:8px;">
📅 <?php echo date("F j, Y", strtotime($row['date'])); ?>
</p>

<?php if(!empty($row['time'])): ?>
<p style="color:var(--text-muted);">
⏰ <?php echo date("h:i A", strtotime($row['time'])); ?>
</p>
<?php endif; ?>

<p><b>Address:</b> <?php echo htmlspecialchars($row['address'] ?? "Not provided"); ?></p>
<p><b>Description:</b> <?php echo htmlspecialchars($row['description'] ?? "No description"); ?></p>

<!-- PAYMENT SECTION -->
<?php if($status == "completed" && $price_status == "sent" && $payment_status != "paid"): ?>
<p style="color:#b388ff;font-weight:600;margin-top:10px;">
Service Completed. Payment Requested: ₹<?php echo $row['price']; ?>
</p>
<a href="pay_now.php?booking_id=<?php echo $row['id']; ?>" 
class="btn"
style="margin-top:10px;width:auto;padding:10px 25px;">
Pay Now
</a>
<?php endif; ?>

<?php if($payment_status == "paid"): ?>
<p style="color:#00e676;margin-top:10px;">
✔ Payment completed successfully.
</p>
<?php endif; ?>

<!-- RATING SECTION -->
<?php if($status == "completed" && $payment_status == "paid"): ?>
<hr style="border:0;border-top:1px solid rgba(255,255,255,0.1);margin:15px 0;">

<?php if(!$rating_done): ?>
<form action="../api/add_rating.php" method="POST">
<input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
<input type="hidden" name="provider_id" value="<?php echo $row['provider_id']; ?>">

<label style="color:var(--text-muted);font-size:0.9rem;">Rate Experience</label>
<input type="number" name="rating" min="1" max="5" value="5" style="width:80px;margin-left:10px;">
<textarea name="review" placeholder="Tell us how the service was..." style="width:100%;margin-top:10px;padding:10px;background:rgba(255,255,255,0.05);border:1px solid var(--glass-border);border-radius:10px;color:white;"></textarea>
<button type="submit" class="btn" style="margin-top:10px;width:auto;padding:10px 25px;">Submit Feedback</button>
</form>
<?php else: ?>
<p style="color:#00e676;font-weight:600;">✔ You have already submitted your rating and review.</p>
<?php endif; ?>
<?php endif; ?>

</div>
</div>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="glass-card" style="padding:40px;text-align:center;">
No bookings found.
</div>

<?php endif; ?>

</div>
</div>