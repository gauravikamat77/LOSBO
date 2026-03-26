<?php
session_start();
include("../config/database.php");

// Initialize variables
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
    $confirm = $_POST['confirm_password'] ?? '';

    // 1️⃣ Required fields check
    if (!$name || !$email || !$password || !$confirm || !$service) {
        $error = "Please fill in all required fields.";
    }
    // 2️⃣ Password match
    elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    }
    // 3️⃣ Strong password validation
    else {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $special   = preg_match('@[^\w]@', $password);

        if (strlen($password) < 8 || !$uppercase || !$lowercase || !$number || !$special) {
            $error = "Password must be at least 8 characters and include an uppercase letter, a lowercase letter, a number, and a special character.";
        }
    }

    // 4️⃣ Proceed if no errors
    if (!$error) {
        // Use typed service if "Other"
        if ($service === "Other" && !empty($other_service)) {
            $service = $other_service;
        }

        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $error = "Email already exists. Please login or use another email.";
        } else {
            // Insert service into categories table
            $stmt_cat = $conn->prepare("INSERT IGNORE INTO categories(name) VALUES(?)");
            $stmt_cat->bind_param("s", $service);
            $stmt_cat->execute();
            $stmt_cat->close();

            // Hash password
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            // Insert user
            $stmt_user = $conn->prepare("INSERT INTO users(name,email,password,phone,role) VALUES(?,?,?,?, 'provider')");
            $stmt_user->bind_param("ssss", $name, $email, $hashed, $phone);

            if ($stmt_user->execute()) {
                $user_id = $stmt_user->insert_id;
                $stmt_user->close();

                // Insert into providers table
                $stmt_provider = $conn->prepare("INSERT INTO providers(user_id,service_type) VALUES(?,?)");
                $stmt_provider->bind_param("is", $user_id, $service);
                if ($stmt_provider->execute()) {
                    $success = "Account created successfully! You can now login.";
                } else {
                    $error = "Error creating provider record. Please try again.";
                }
                $stmt_provider->close();
            } else {
                $error = "Error creating account. Please try again.";
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
        <div id="popup" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: rgba(255,0,0,0.9); color: white; padding: 15px 25px; border-radius: 12px; font-weight: bold; z-index: 9999; box-shadow: 0 0 20px rgba(255,0,0,0.5);">
            ❌ <?php echo $error; ?>
        </div>
        <script>
            setTimeout(()=> { document.getElementById('popup').style.display='none'; }, 4000);
        </script>
        <?php elseif($success): ?>
        <div id='popup-success' style='position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: rgba(0,128,0,0.9); color: white; padding: 15px 25px; border-radius: 12px; font-weight: bold; z-index: 9999; box-shadow: 0 0 20px rgba(0,128,0,0.5); text-align:center;'>
            ✅ <?php echo $success; ?>
            <a href="login.php" class="btn" style="display:block; margin-top:10px; padding:10px; background:#00e676; color:#020c1b; text-decoration:none; border-radius:5px;">Go to Login</a>
        </div>
        <script>
            setTimeout(()=> { document.getElementById('popup-success').style.display='none'; }, 4000);
        </script>
        <?php endif; ?>

        <form action="" method="POST" class="form-group" enctype="multipart/form-data">
            
            <input type="text" name="name" placeholder="Full name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone Number">

            <select name="service" id="serviceSelect" onchange="checkOtherService()" required>
                <option value="" disabled selected>Select Service</option>
                <option value="Plumber">Plumber</option>
                <option value="Electrician">Electrician</option>
                <option value="Carpenter">Carpenter</option>
                <option value="Cleaning">Cleaning</option>
                <option value="Other">Other</option>
            </select>

            <input type="text" name="other_service" id="otherServiceInput" 
                   placeholder="Enter your service" 
                   style="display:none; margin-top: 5px; border-color: var(--accent-blue);">

            <div style="position: relative; margin-bottom: 10px;">
                <input type="password" id="password" name="password" placeholder="Password" required>
                
                <!-- Password requirements popup -->
                <div id="password-popup" style="background: rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:white; padding:10px; border-radius:8px; font-size:0.8rem; margin-top:5px;">
                    <strong>Password must contain:</strong>
                    <ul style="padding-left:18px; margin:5px 0;">
                        <li id="pw-length">At least 8 characters</li>
                        <li id="pw-uppercase">1 uppercase letter (A-Z)</li>
                        <li id="pw-lowercase">1 lowercase letter (a-z)</li>
                        <li id="pw-number">1 number (0-9)</li>
                        <li id="pw-special">1 special character (!@#$%^&*)</li>
                    </ul>
                </div>
            </div>

            <input type="password" name="confirm_password" placeholder="Confirm Password" required>

            <button class="btn" type="submit" style="margin-top: 15px;">
                Create Provider Account
            </button>

            <div style="margin-top: 20px; color: rgba(255,255,255,0.6); font-size: 0.9rem;">
                Already a partner? <a href="login.php" class="link-blue" style="font-weight: bold;">Login</a>
            </div>
        </form>

    </div>
</div>

<script>
function checkOtherService(){
    let service = document.getElementById("serviceSelect").value;
    let otherInput = document.getElementById("otherServiceInput");

    if(service === "Other"){
        otherInput.style.display = "block";
        otherInput.required = true; // Make it required if visible
    } else {
        otherInput.style.display = "none";
        otherInput.required = false;
    }
}

// Real-time password strength validation
const passwordInput = document.getElementById('password');
const pwLength = document.getElementById('pw-length');
const pwUpper = document.getElementById('pw-uppercase');
const pwLower = document.getElementById('pw-lowercase');
const pwNumber = document.getElementById('pw-number');
const pwSpecial = document.getElementById('pw-special');

passwordInput.addEventListener('input', () => {
    const val = passwordInput.value;

    pwLength.style.color = val.length >= 8 ? '#00e676' : 'white';
    pwUpper.style.color = /[A-Z]/.test(val) ? '#00e676' : 'white';
    pwLower.style.color = /[a-z]/.test(val) ? '#00e676' : 'white';
    pwNumber.style.color = /[0-9]/.test(val) ? '#00e676' : 'white';
    pwSpecial.style.color = /[!@#$%^&*]/.test(val) ? '#00e676' : 'white';
});
</script>