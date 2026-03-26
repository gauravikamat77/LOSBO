<?php
include("../config/session_check.php");
?>

<link rel="stylesheet" href="../assets/css/style.css">


<div class="page-wrapper" style="flex-direction: column; align-items: center; padding-top: 120px;">

    <div class="glass-card" style="max-width: 500px; text-align: center; padding: 40px;">
        
        <div style="font-size: 60px; color: #ff4d4d; margin-bottom: 20px;">
            ✖
        </div>

        <h2 style="margin-bottom: 10px;">Payment Request Failed</h2>

        <p style="color: var(--text-muted); margin-bottom: 25px;">
            Something went wrong while sending the payment request.
            Please try again.
        </p>

        <a href="../provider/schedule.php" class="btn">Back to Schedule</a>

    </div>

</div>