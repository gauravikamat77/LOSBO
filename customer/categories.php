<?php 
include("../customer/navbar.php");
include("../config/database.php");

/* Fetch categories */
$result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper" style="flex-direction: column; justify-content: flex-start; padding-top: 20px;">
    
    <div style="width: 100%; max-width: 1000px; margin-bottom: 40px; text-align: center;">
        <h2 class="logo-title">Find a Professional</h2>
        <p class="slogan">Trusted local experts for every task</p>
        
        <!-- 🔍 SEARCH BAR -->
        <div style="position: relative; max-width: 600px; margin: 30px auto 0;">
            <input type="text" id="serviceSearch" placeholder="Search service..." 
                   style="width: 100%; padding: 15px 50px 15px 20px; font-size: 1.1rem; border-radius:10px; border:none; outline:none;"
                   onkeyup="filterServices()">

            <span style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 1.2rem; opacity: 0.5;">🔍</span>
        </div>
    </div>

    <!-- SERVICES GRID -->
    <div class="grid" id="serviceGrid" 
    style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px,1fr)); gap:25px; width:100%; max-width:1000px;">

<?php
while($row = $result->fetch_assoc()){
$service = htmlspecialchars($row['name']);
?>

        <div class="glass-card hover-card service-item" 
             data-name="<?php echo strtolower($service); ?>">

            <h3 style="margin-bottom:10px;"><?php echo ucfirst($service); ?></h3>
            <br>

            <a class="btn" href="providers.php?service=<?php echo urlencode($service); ?>">
                View 
            </a>

        </div>

<?php } ?>

    </div>
</div>

<!-- 🔥 FIXED SEARCH SCRIPT -->
<script>
function filterServices() {

let input = document.getElementById('serviceSearch').value.toLowerCase();
let cards = document.getElementsByClassName('service-item'); // ✅ FIXED

for (let i = 0; i < cards.length; i++) {

let name = cards[i].getAttribute('data-name');

if (name.includes(input)) {
cards[i].style.display = "block";
} else {
cards[i].style.display = "none";
}

}
}
</script>
