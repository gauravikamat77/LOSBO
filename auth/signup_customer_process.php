<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../config/database.php");

// Get POST data safely
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

// NEW FIELDS
$gender = $_POST['gender'] ?? '';
$language = trim($_POST['language'] ?? '');
$dob = $_POST['dob'] ?? '';
$city = trim($_POST['city'] ?? '');
$state = trim($_POST['state'] ?? '');
$pincode = trim($_POST['pincode'] ?? '');

// 1️⃣ Required fields check
if (
    !$name || !$email || !$password || !$confirm ||
    !$gender || !$language || !$dob || !$city || !$state || !$pincode
) {
    header("Location: signup_customer.php?error=1");
    exit();
}

// 2️⃣ Password match
if ($password !== $confirm) {
    header("Location: signup_customer.php?error=2");
    exit();
}

// 3️⃣ Strong password validation
$uppercase = preg_match('@[A-Z]@', $password);
$lowercase = preg_match('@[a-z]@', $password);
$number    = preg_match('@[0-9]@', $password);
$specialChars = preg_match('@[^\w]@', $password);

if (strlen($password) < 8 || !$uppercase || !$lowercase || !$number || !$specialChars) {
    header("Location: signup_customer.php?error=3");
    exit();
}

// 4️⃣ Validate pincode (Indian 6-digit)
if (!preg_match('/^[0-9]{6}$/', $pincode)) {
    header("Location: signup_customer.php?error=1");
    exit();
}

// 5️⃣ Validate phone (basic 10-digit)
if ($phone && !preg_match('/^[0-9]{10}$/', $phone)) {
    header("Location: signup_customer.php?error=1");
    exit();
}

// 6️⃣ Check if email already exists
$stmt_check = $conn->prepare("SELECT id FROM users WHERE email=?");
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$res_check = $stmt_check->get_result();

if ($res_check->num_rows > 0) {
    header("Location: signup_customer.php?error=4");
    exit();
}

// 7️⃣ Insert into database
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$role = 'customer';

$stmt = $conn->prepare("
    INSERT INTO users 
    (name, email, password, phone, role, gender, language, dob, city, state, pincode) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
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

if ($stmt->execute()) {
    header("Location: signup_customer.php?success=1");
    exit();
} else {
    header("Location: signup_customer.php?error=5");
    exit();
}
?>