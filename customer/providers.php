<?php
include("../config/session_check.php");
include("../config/database.php");

$service = $_GET['service'] ?? '';

if(empty($service)){
    die("Service not specified.");
}

// ✅ UPDATED QUERY (Alphabetical Order)
$sql = "SELECT 
        p.id AS provider_id, 
        u.name, 
        u.profile_image, 
        p.service_type,
        IFNULL(AVG(r.rating), 0) AS rating

        FROM providers p

        JOIN users u ON p.user_id = u.id

        LEFT JOIN ratings r ON p.id = r.provider_id

        WHERE LOWER(p.service_type) = LOWER(?)

        GROUP BY p.id

        ORDER BY LOWER(u.name) ASC"; // 🔥 SORT A-Z

$stmt = $conn->prepare($sql);

if(!$stmt){
    die("Prepare failed: ".$conn->error);
}

$stmt->bind_param("s", $service);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    die("No providers found for this service.");
}
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper" style="flex-direction: column; align-items: center; padding-top: 100px; padding-bottom: 60px;">

    <!-- Title -->
    <div style="text-align: center; margin-bottom: 40px;">
        <h2 style="margin: 0; font-size: 2rem; color: white;">
            Available Providers for "<?php echo htmlspecialchars(ucwords($service)); ?>"
        </h2>
    </div>

    <!-- 🔍 SEARCH + ⭐ RATING FILTER -->
    <div style="display:flex; gap:15px; margin-bottom:30px; flex-wrap:wrap; justify-content:center;">

        <!-- Search -->
        <input type="text" id="searchInput" placeholder="Search provider..."
        onkeyup="filterProviders()"
        style="padding:10px; width:250px; border-radius:8px; border:none; outline:none;">

        <!-- Rating Filter -->
        <select id="ratingFilter" onchange="filterProviders()"
        style="padding:10px; border-radius:8px; border:none;">

            <option value="all">All Ratings</option>
            <option value="5">⭐ 5 Stars</option>
            <option value="4">⭐ 4 & above</option>
            <option value="3">⭐ 3 & above</option>
            <option value="2">⭐ 2 & above</option>

        </select>

    </div>

    <!-- Providers Grid -->
    <div class="providers-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 25px; width: 100%; max-width: 1200px;">

        <?php while($row = $result->fetch_assoc()): 
            $rating = $row['rating'];
        ?>

            <div class="glass-card provider-card provider-item"
                 data-name="<?php echo strtolower($row['name']); ?>"
                 data-rating="<?php echo $rating; ?>"
                 style="
                    background: rgba(255,255,255,0.05); 
                    border: 1px solid rgba(255,255,255,0.1); 
                    border-radius: 15px; 
                    padding: 25px; 
                    text-align: center; 
                    backdrop-filter: blur(10px);
                 ">

                <!-- Profile Image -->
                <?php $img = !empty($row['profile_image']) ? $row['profile_image'] : 'default.png'; ?>
                <div style="width: 100px; height: 100px; margin: 0 auto 15px; border-radius: 50%; overflow: hidden;">
                    <img src="../uploads/profiles/<?php echo htmlspecialchars($img); ?>" 
                         style="width:100%; height:100%; object-fit:cover;">
                </div>

                <!-- Name -->
                <h3 style="color:white;">
                    <?php echo htmlspecialchars(ucwords(strtolower($row['name']))); ?>
                </h3>

                <!-- ⭐ Rating -->
                <p style="color: gold; margin:5px 0;">
                    <?php echo str_repeat("⭐", floor($rating)); ?> 
                    (<?php echo number_format($rating,1); ?>)
                </p>

                <!-- Button -->
                <a href="provider_profile.php?provider_id=<?php echo $row['provider_id']; ?>" 
                   class="btn btn-secondary">
                   View Profile
                </a>

            </div>

        <?php endwhile; ?>

    </div>
</div>

<!-- 🔥 FILTER SCRIPT -->
<script>
function filterProviders(){

let search = document.getElementById("searchInput").value.toLowerCase();
let ratingFilter = document.getElementById("ratingFilter").value;

let cards = document.getElementsByClassName("provider-item");

for(let i = 0; i < cards.length; i++){

let name = cards[i].getAttribute("data-name");
let rating = parseFloat(cards[i].getAttribute("data-rating"));

let matchSearch = name.includes(search);
let matchRating = (ratingFilter === "all" || rating >= ratingFilter);

if(matchSearch && matchRating){
cards[i].style.display = "block";
}else{
cards[i].style.display = "none";
}

}
}
</script>
