<?php

$id = $_GET['id'];

?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">

<div class="glass-card">

<h2>Send Price Quote</h2>

<form action="../api/send_price.php" method="POST">

<input type="hidden" name="booking_id" value="<?php echo $id; ?>">

<input type="number" name="price" placeholder="Enter Amount">

<button class="btn">Send Price</button>

</form>

</div>

</div>