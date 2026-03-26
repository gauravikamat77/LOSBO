<?php
session_start();
include("../config/database.php");

// Initialize popup messages
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $service = $_POST['service'] ?? '';
    $other_service = trim($_POST['other_service'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // NEW FIELDS
    $gender = $_POST['gender'] ?? '';
    $language = trim($_POST['language'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');

    // VALIDATIONS
    if (
        !$name || !$email || !$password || !$confirm_password || !$service ||
        !$gender || !$language || !$dob || !$city || !$state || !$pincode
    ) {
        $error = "Please fill in all required fields.";
    }
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    }
    else {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $special   = preg_match('@[^\w]@', $password);

        if (strlen($password) < 8 || !$uppercase || !$lowercase || !$number || !$special) {
            $error = "Weak password format.";
        }
    }

    // Extra validations
    if (!$error) {
        if (!preg_match('/^[0-9]{6}$/', $pincode)) {
            $error = "Invalid pincode.";
        }
        if ($phone && !preg_match('/^[0-9]{10}$/', $phone)) {
            $error = "Invalid phone number.";
        }
    }

    // If valid → insert
    if (!$error) {

        if ($service === "Other" && !empty($other_service)) {
            $service = $other_service;
        }

        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $error = "Email already exists.";
        } else {

            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $conn->begin_transaction();

            try {
                // Category
                $stmt_cat = $conn->prepare("INSERT IGNORE INTO categories(name) VALUES(?)");
                $stmt_cat->bind_param("s", $service);
                $stmt_cat->execute();

                // User
                $stmt_user = $conn->prepare("
                    INSERT INTO users
                    (name,email,password,phone,role,gender,language,dob,city,state,pincode)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?)
                ");

                $role = 'provider';

                $stmt_user->bind_param(
                    "sssssssssss",
                    $name,$email,$hashed_password,$phone,$role,
                    $gender,$language,$dob,$city,$state,$pincode
                );

                $stmt_user->execute();
                $user_id = $stmt_user->insert_id;

                // Provider
                $stmt_provider = $conn->prepare("INSERT INTO providers(user_id,service_type) VALUES(?,?)");
                $stmt_provider->bind_param("is", $user_id, $service);
                $stmt_provider->execute();

                $conn->commit();
                $success = "Account created successfully!";

            } catch (Exception $e) {
                $conn->rollback();
                $error = "Something went wrong.";
            }
        }
    }
}
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper">
<div class="glass-card signup-form-card">

<h2 class="logo-title">Partner with LOSBO</h2>
<p class="slogan">Grow your business today</p>

<?php if($error): ?>
<div id="popup" style="position:fixed;top:20px;left:50%;transform:translateX(-50%);background:red;color:white;padding:15px;border-radius:10px;">
❌ <?php echo $error; ?>
</div>
<?php elseif($success): ?>
<div id='popup' style='position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: rgba(0,128,0,0.9); color: white; padding: 15px 25px; border-radius: 12px; font-weight: bold; z-index: 9999; box-shadow: 0 0 20px rgba(0,128,0,0.5); text-align:center;'>
✅ <?php echo $success; ?>
<a href="login.php" style='margin-top:10px; padding:8px 15px; background:#00e676; border:none; color:#020c1b; font-weight:bold; border-radius:5px; cursor:pointer;'>Login</a>
</div>
<?php endif; ?>

<form method="POST" class="form-group">

<input type="text" name="name" placeholder="Full name" required>
<input type="email" name="email" placeholder="Email" required>
<input type="text" name="phone" placeholder="Phone">

<select name="gender" required>
<option value="">Gender</option>
<option>Male</option>
<option>Female</option>
<option>Other</option>
</select>

<input type="text" name="language" placeholder="Language" required>
<input type="date" name="dob" required>
<input type="text" name="city" placeholder="City" required>
<input type="text" name="state" placeholder="State" required>
<input type="text" name="pincode" placeholder="Pincode" required>

<select name="service" id="serviceSelect" onchange="checkOtherService()" required>
<option value="">Service</option>
<option>Plumber</option>
<option>Electrician</option>
<option>Carpenter</option>
<option>Cleaning</option>
<option value="Other">Other</option>
</select>

<input type="text" name="other_service" id="otherServiceInput" placeholder="Other service" style="display:none;">

<!-- PASSWORD -->
<input type="password" id="password" name="password" placeholder="Password" required>

<div id="password-popup">
<ul>
<li id="pw-length">At least 8 characters</li>
<li id="pw-uppercase">Uppercase letter</li>
<li id="pw-lowercase">Lowercase letter</li>
<li id="pw-number">Number</li>
<li id="pw-special">Special character</li>
</ul>
</div>

<input type="password" name="confirm_password" placeholder="Confirm Password" required>

<button class="btn">Create Account</button>

</form>
</div>
</div>

<script>
// WAIT FOR DOM
document.addEventListener("DOMContentLoaded", function(){

function checkOtherService(){
    let val = document.getElementById("serviceSelect").value;
    let input = document.getElementById("otherServiceInput");
    if(val === "Other"){
        input.style.display = "block";
        input.required = true;
    } else {
        input.style.display = "none";
        input.required = false;
    }
}
window.checkOtherService = checkOtherService;

// PASSWORD VALIDATION
const passwordInput = document.getElementById('password');

const pwLength = document.getElementById('pw-length');
const pwUpper = document.getElementById('pw-uppercase');
const pwLower = document.getElementById('pw-lowercase');
const pwNumber = document.getElementById('pw-number');
const pwSpecial = document.getElementById('pw-special');

passwordInput.addEventListener('input', function(){

    let val = this.value;

    pwLength.style.color = val.length >= 8 ? "#00e676" : "white";
    pwUpper.style.color = /[A-Z]/.test(val) ? "#00e676" : "white";
    pwLower.style.color = /[a-z]/.test(val) ? "#00e676" : "white";
    pwNumber.style.color = /[0-9]/.test(val) ? "#00e676" : "white";
    pwSpecial.style.color = /[!@#$%^&*]/.test(val) ? "#00e676" : "white";

});

});
</script>