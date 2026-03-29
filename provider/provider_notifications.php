<?php
include("../provider/navbar.php");
$user_id = $_SESSION['user_id']; // IMPORTANT
?>

<!DOCTYPE html>
<html>
<head>
  <title>Provider Notifications</title>
</head>
<body>

<div class="page-wrapper" style="padding: 20px;">
  <div class="glass-card" style="max-width:600px; margin:auto;">
    <h2>Notifications</h2>
    <div id="notifications"></div>
  </div>
</div>

<!-- <div id="notifications"></div> -->

<script src="../assets/js/notifications.js"></script>

<script>
  var userId = <?php echo $user_id; ?>;
  const userRole = "<?php echo $_SESSION['role']; ?>"; 
  loadNotifications(userId, userRole);
</script>

</body>
</html>