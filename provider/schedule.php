<?php
include("../config/session_check.php");
include("../config/database.php");

// Get logged-in provider's user ID
$provider_user_id = $_SESSION['user_id'] ?? 0;
if(!$provider_user_id){
    die("You must be logged in as a provider.");
}

// Fetch provider's internal ID
$stmt = $conn->prepare("SELECT id FROM providers WHERE user_id=?");
$stmt->bind_param("i", $provider_user_id);
$stmt->execute();
$results = $stmt->get_result();
$provider_row = $results->fetch_assoc();
$provider_id = $provider_row['id'] ?? 0;

if(!$provider_id){
    die("Provider profile not found.");
}

// Optional success message
$msg = $_GET['msg'] ?? '';

// Fetch bookings for Active Schedule:
// 1️⃣ Accepted & price not sent
// 2️⃣ Accepted & price sent & payment not paid
// 3️⃣ Completed & price sent & payment not paid
$stmt2 = $conn->prepare("
    SELECT b.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone
    FROM bookings b
    JOIN users u ON b.customer_id = u.id
    WHERE b.provider_id=?
      AND (
          (b.status='accepted' AND b.price_status!='sent') OR
          (b.status='accepted' AND b.price_status='sent' AND b.payment_status IS NULL) OR
          (b.status='completed' AND b.price_status='sent' AND b.payment_status IS NULL)
      )
    ORDER BY b.date ASC, b.time ASC
");
$stmt2->bind_param("i", $provider_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
?>

<link rel="stylesheet" href="../assets/css/style.css">
<?php include("navbar.php"); ?>

<div class="page-wrapper" style="flex-direction: column; justify-content: flex-start; padding-top: 100px; max-width: 1000px; margin: auto;">

    <div style="width: 100%; margin-bottom: 30px;">
        <h2 class="logo-title">Active Schedule</h2>
        <p class="slogan">Manage your confirmed appointments and pending payments</p>
    </div>

    <?php if($msg): ?>
        <div class="glass-card" style="background-color: rgba(0,200,80,0.2); padding: 15px; margin-bottom: 20px; color: white;">
            <?php echo htmlspecialchars($msg); ?>
        </div>
    <?php endif; ?>

    <div style="width: 100%; display: flex; flex-direction: column; gap: 20px; padding: 20px;">

        <?php if($result2->num_rows > 0): ?>
            <?php while($row = $result2->fetch_assoc()): ?>
                <div class="glass-card" style="padding: 25px; display: flex; justify-content: space-between; align-items: center; border-left: 5px solid var(--accent-blue);">

                    <div style="flex: 1;">
                        <div style="margin-bottom: 10px;">
                            <span style="color: var(--accent-blue); font-size: 0.8rem; font-weight: bold; text-transform: uppercase;">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </div>
                        <h3 style="margin: 0; color: white; font-size: 1.3rem;">
                            Customer: <?php echo htmlspecialchars($row['customer_name']); ?>
                        </h3>
                        <p style="color: var(--text-muted); margin: 5px 0; font-size: 0.95rem;">
                            📅 <?php echo date("F j, Y", strtotime($row['date'])); ?> at <?php echo htmlspecialchars($row['time']); ?> | 📍 <?php echo htmlspecialchars($row['address']); ?>
                        </p>
                        <p style="color: var(--text-muted); margin: 5px 0; font-size: 0.95rem;">
                            📧 <?php echo htmlspecialchars($row['customer_email']); ?> | 📞 <?php echo htmlspecialchars($row['customer_phone']); ?>
                        </p>
                        <p style="color: var(--text-muted); margin: 5px 0; font-size: 0.95rem;">
                            <b>Description:</b> <?php echo htmlspecialchars($row['description']); ?>
                        </p>
                        <?php if(!empty($row['photo'])): ?>
                            <img src="../uploads/bookings/<?php echo htmlspecialchars($row['photo']); ?>" 
                                 alt="Job Photo" style="width: 100px; height: 100px; object-fit: cover; margin: 10px 0; border-radius: 8px;">
                        <?php endif; ?>
                        <div style="margin-top: 10px; font-weight: bold; color: #00e676;">
                            Fixed Price: ₹<?php echo number_format($row['price'] ?? 0); ?>
                        </div>
                    </div>

                    <div style="text-align: right;">
                        <?php if($row['price_status'] !== 'sent'): ?>
                            <form method="POST" action="../api/send_payment.php">
                                <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                <input type="number" name="amount" placeholder="Enter amount" required style="width: 120px; margin-bottom: 10px;">
                                <button type="submit" class="btn" style="background: #00e676; color: #000;">
                                    Send Payment Request
                                </button>
                            </form>
                        <?php elseif($row['payment_status'] !== 'paid'): ?>
                            <span style="color: #b388ff; font-weight: bold; display: block; margin-bottom: 10px;">Payment Requested</span>
                        <?php endif; ?>

                        <?php if($row['status'] !== 'completed'): ?>
                            <a href="../api/mark_complete.php?id=<?php echo $row['id']; ?>" 
                               class="btn" 
                               onclick="return confirm('Are you sure this job is complete? This will notify the customer.')"
                               style="width: auto; padding: 12px 25px; background: #00e676; color: #000; margin-top: 10px;">
                               Mark as Complete
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="glass-card" style="text-align: center; padding: 60px;">
                <p style="color: var(--text-muted);">No active accepted jobs or pending payments. Go to <a href="requests.php" class="link-blue">Requests</a> to find work!</p>
            </div>
        <?php endif; ?>

    </div>

</div>