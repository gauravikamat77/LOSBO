<?php
include("../config/session_check.php");
?>

<link rel="stylesheet" href="../assets/css/style.css">

<?php include("navbar.php"); ?>

<div class="page-wrapper" style="flex-direction: column; align-items: center; padding-top: 100px;">

    <div class="glass-card" style="text-align: center; max-width: 500px; padding: 30px;">
        <h2 style="font-size: 2rem; color: var(--accent-blue); margin-bottom: 15px;">Booking Confirmed!</h2>
        <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 25px;">
            Your appointment has been successfully requested. The provider will review your request and notify you once accepted.
        </p>

        <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
            <a href="dashboard.php" class="btn" style="padding: 12px 25px;">Back to Home</a>
            <a href="history.php" class="btn" style="padding: 12px 25px; background: rgba(255,255,255,0.1); color: white;">View My Bookings</a>
        </div>
    </div>

</div>