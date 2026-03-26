<?php
include("../config/session_check.php");
include("../config/database.php");

$provider_user_id = $_SESSION['user_id'] ?? 0;
if(!$provider_user_id){
    die("You must be logged in as a provider.");
}

// Get provider's ID
$stmt = $conn->prepare("SELECT id FROM providers WHERE user_id=?");
$stmt->bind_param("i", $provider_user_id);
$stmt->execute();
$result = $stmt->get_result();
$provider = $result->fetch_assoc();
if(!$provider){
    die("Provider profile not found.");
}
$provider_id = $provider['id'];

// Optional success message from process_request.php
$msg = $_GET['msg'] ?? '';

// Fetch all pending bookings for this provider
$stmt2 = $conn->prepare("
    SELECT b.id AS booking_id, b.date, b.time, b.address, b.description, b.photo,
           u.name AS customer_name, u.email AS customer_email, u.phone AS customer_phone
    FROM bookings b
    JOIN users u ON b.customer_id = u.id
    WHERE b.provider_id=? AND b.status='pending'
    ORDER BY b.date ASC, b.time ASC
");
$stmt2->bind_param("i", $provider_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
?>

<link rel="stylesheet" href="../assets/css/style.css">
<?php include("navbar.php"); ?>

<div class="page-wrapper" style="flex-direction: column; padding-top: 100px; max-width: 1000px; margin: auto;">

    <h1 style="margin-bottom: 20px; color: white;">Incoming Requests</h1>

    <?php if($msg): ?>
        <div class="glass-card" style="background-color: rgba(0,200,80,0.2); padding: 15px; margin-bottom: 20px; color: white;">
            <?php echo htmlspecialchars($msg); ?>
        </div>
    <?php endif; ?>

    <?php if($result2->num_rows == 0): ?>
        <div class="glass-card" style="padding: 20px; text-align: center; color: var(--text-muted);">
            No new requests at the moment.
        </div>
    <?php else: ?>
        <?php while($row = $result2->fetch_assoc()): ?>
            <div class="glass-card" style="margin-bottom: 20px; padding: 20px;">
                <h3 style="margin-bottom: 5px;"><?php echo htmlspecialchars($row['customer_name']); ?></h3>
                <p style="margin: 5px 0;"><b>Email:</b> <?php echo htmlspecialchars($row['customer_email']); ?></p>
                <p style="margin: 5px 0;"><b>Phone:</b> <?php echo htmlspecialchars($row['customer_phone']); ?></p>
                <p style="margin: 5px 0;"><b>Appointment:</b> <?php echo htmlspecialchars($row['date']); ?> at <?php echo htmlspecialchars($row['time']); ?></p>
                <p style="margin: 5px 0;"><b>Address:</b> <?php echo htmlspecialchars($row['address']); ?></p>
                <p style="margin: 5px 0;"><b>Description:</b> <?php echo htmlspecialchars($row['description']); ?></p>

                <?php if(!empty($row['photo'])): ?>
                    <img src="../uploads/bookings/<?php echo htmlspecialchars($row['photo']); ?>" 
                         alt="Job Photo" style="width: 100px; height: 100px; object-fit: cover; margin: 10px 0; border-radius: 8px;">
                <?php endif; ?>

                <div style="margin-top: 10px;">
                    <!-- Accept Form -->
                    <form method="POST" action="process_request.php" style="display:inline-block; margin-right:10px;">
                        <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                        <button type="submit" name="action" value="accept" class="btn">Accept</button>
                    </form>

                    <!-- Reject Form -->
                    <form method="POST" action="process_request.php" style="display:inline-block;">
                        <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                        <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

</div>