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

// NEW FIELDS
$gender = $_POST['gender'] ?? '';
$language = trim($_POST['language'] ?? '');
$dob = $_POST['dob'] ?? '';
$city = trim($_POST['city'] ?? '');
$state = trim($_POST['state'] ?? '');
$pincode = trim($_POST['pincode'] ?? '');

// 1️⃣ Required fields check
if (
    !$name || !$email || !$password || !$confirm_password || !$service ||
    !$gender || !$language || !$dob || !$city || !$state || !$pincode
) {
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
        $error = "Password must be at least 8 characters and include uppercase, lowercase, number & special character.";
    }
}

// 4️⃣ Extra validations
if (!$error) {

    // Validate pincode (India 6-digit)
    if (!preg_match('/^[0-9]{6}$/', $pincode)) {
        $error = "Invalid pincode (must be 6 digits).";
    }

    // Validate phone (10-digit)
    if ($phone && !preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Invalid phone number (must be 10 digits).";
    }
}

// 5️⃣ Proceed if no errors
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

        // Begin transaction
        $conn->begin_transaction();

        try {
            // 1️⃣ Insert service into categories
            $stmt_cat = $conn->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
            $stmt_cat->bind_param("s", $service);
            $stmt_cat->execute();
            $stmt_cat->close();

            // 2️⃣ Insert into users table (UPDATED)
            $stmt_user = $conn->prepare("
                INSERT INTO users 
                (name,email,password,phone,role,gender,language,dob,city,state,pincode) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?)
            ");

            $role = 'provider';

            $stmt_user->bind_param(
                "sssssssssss",
                $name,
                $email,
                $hashed_password,
                $phone,
                $role,
                $gender,
                $language,
                $dob,
                $city,
                $state,
                $pincode
            );

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

// 6️⃣ Display popups
if ($error) {
    echo "
    <div id='popup' style='position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: rgba(255,0,0,0.9); color: white; padding: 15px 25px; border-radius: 12px; font-weight: bold; z-index: 9999;'>
        ❌ {$error}
    </div>
    <script>
        setTimeout(()=>{document.getElementById('popup').style.display='none';}, 4000);
    </script>
    ";
} elseif ($success) {
    echo "
    <div id='popup' style='position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: rgba(0,128,0,0.9); color: white; padding: 15px 25px; border-radius: 12px; font-weight: bold; z-index: 9999; text-align:center;'>
        ✅ {$success}
        <button onclick=\"window.location.href='login.php'\" style='margin-top:10px; padding:8px 15px; background:#00e676; border:none; color:#020c1b; font-weight:bold; border-radius:5px; cursor:pointer;'>
            Go to Login
        </button>
    </div>
    ";
}
?>