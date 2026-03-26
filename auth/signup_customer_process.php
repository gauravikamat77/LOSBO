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

// 1️⃣ Required fields check
if (!$name || !$email || !$password || !$confirm) {
    header("Location: signup_customer.php?error=1"); // 1 = Missing fields
    exit();
}

// 2️⃣ Password match
if ($password !== $confirm) {
    header("Location: signup_customer.php?error=2"); // 2 = Password mismatch
    exit();
}

// 3️⃣ Strong password validation
$uppercase = preg_match('@[A-Z]@', $password);
$lowercase = preg_match('@[a-z]@', $password);
$number    = preg_match('@[0-9]@', $password);
$specialChars = preg_match('@[^\w]@', $password);

if (strlen($password) < 8 || !$uppercase || !$lowercase || !$number || !$specialChars) {
    header("Location: signup_customer.php?error=3"); // 3 = Weak password
    exit();
}

// 4️⃣ Check if email already exists
$stmt_check = $conn->prepare("SELECT id FROM users WHERE email=?");
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$res_check = $stmt_check->get_result();

if ($res_check->num_rows > 0) {
    header("Location: signup_customer.php?error=4"); // 4 = Email already exists
    exit();
}

// 5️⃣ All validations passed, now insert
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT INTO users(name,email,password,phone,role) VALUES(?,?,?,?,?)");
$role = 'customer';
$stmt->bind_param("sssss", $name, $email, $hashed_password, $phone, $role);

if ($stmt->execute()) {
    header("Location: signup_customer.php?success=1"); // 1 = Account created
    exit();
} else {
    header("Location: signup_customer.php?error=5"); // 5 = DB error
    exit();
}
?>