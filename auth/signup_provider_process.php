<?php
session_start();
include("../config/database.php");

// Initialize popup messages
$error = '';
$success = '';

// Collect POST data safely
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$service = $_POST['service'] ?? '';
$other_service = trim($_POST['other_service'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// 1️⃣ Required fields check
if (!$name || !$email || !$password || !$confirm_password || !$service) {
    $error = "Please fill in all required fields.";
}

// 2️⃣ Password match check
elseif ($password !== $confirm_password) {
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

    // If 'Other' is chosen, use typed service
    if ($service === "Other" && !empty($other_service)) {
        $service = $other_service;
    }

    // Check if email already exists
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    if ($res_check->num_rows > 0) {
        $error = "Email already exists. Please login or use another email.";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Begin transaction to prevent partial inserts
        $conn->begin_transaction();

        try {
            // 1️⃣ Insert into categories (ignore duplicates)
            $stmt_cat = $conn->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
            $stmt_cat->bind_param("s", $service);
            $stmt_cat->execute();
            $stmt_cat->close();

            // 2️⃣ Insert into users table
            $stmt_user = $conn->prepare("INSERT INTO users (name,email,password,phone,role) VALUES (?,?,?,?, 'provider')");
            $stmt_user->bind_param("ssss", $name, $email, $hashed_password, $phone);
            $stmt_user->execute();
            $user_id = $stmt_user->insert_id;
            $stmt_user->close();

            // 3️⃣ Insert into providers table
            $stmt_provider = $conn->prepare("INSERT INTO providers (user_id, service_type) VALUES (?, ?)");
            $stmt_provider->bind_param("is", $user_id, $service);
            $stmt_provider->execute();
            $stmt_provider->close();

            // Commit transaction
            $conn->commit();
            $success = "Account created successfully! You can now login.";

        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error creating account. Please try again.";
        }
    }
}

// 5️⃣ Display popups
if ($error) {
    echo "
    <div id='popup' style='position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: rgba(255,0,0,0.9); color: white; padding: 15px 25px; border-radius: 12px; font-weight: bold; z-index: 9999; box-shadow: 0 0 20px rgba(255,0,0,0.5);'>
        ❌ {$error}
    </div>
    <script>setTimeout(()=>{document.getElementById('popup').style.display='none';}, 4000);</script>
    ";
} elseif ($success) {
    echo "
    <div id='popup' style='position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: rgba(0,128,0,0.9); color: white; padding: 15px 25px; border-radius: 12px; font-weight: bold; z-index: 9999; box-shadow: 0 0 20px rgba(0,128,0,0.5);'>
        ✅ {$success}
        <a href='login.php' class='btn' style='display:block; margin-top:10px; padding:10px; background:#00e676; color:#020c1b; text-decoration:none; border-radius:5px;'>Go to Login</a>
    </div>
    ";
}
?>