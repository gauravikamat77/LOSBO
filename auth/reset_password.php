<?php
session_start();
include("../config/database.php");

$token = $_GET['token'] ?? '';
if (!$token) {
    header("Location: login.php");
    exit();
}

// Check token validity
$stmt = $conn->prepare("SELECT id, reset_token, token_expiry FROM users WHERE reset_token=? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$now = date("Y-m-d H:i:s");
$is_expired = !$user || ($user['token_expiry'] < $now);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$is_expired) {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Strong password validation
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $special   = preg_match('@[^\w]@', $password);

    if (strlen($password) < 8 || !$uppercase || !$lowercase || !$number || !$special) {
        $error = "Password must be at least 8 characters and include an uppercase letter, a lowercase letter, a number, and a special character.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, token_expiry=NULL WHERE id=?");
        $stmt->bind_param("si", $hashed, $user['id']);
        $stmt->execute();
        $success = "Password updated! You can now log in.";
    }
}
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper" style="flex-direction: column; align-items: center; justify-content: center; min-height: 90vh;">

    <div class="glass-card login-card" style="max-width: 450px; width: 90%; padding: 40px;">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 40px; color: var(--accent-blue); margin-bottom: 10px;">🔒</div>
            <h2 style="margin: 0; color: white;">New Password</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">Set a strong password to secure your account</p>
        </div>

        <?php if ($is_expired): ?>
            <div class="alert alert-error">
                <p><strong>Link Expired</strong></p>
                <p style="font-size: 0.85rem;">This reset link is no longer valid. Please request a new one.</p>
                <a href="forgot_password.php" class="btn" style="margin-top: 20px; display: block;">Request New Link</a>
            </div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success">
                <p>🎉 <?php echo $success; ?></p>
                <a href="login.php" class="btn" style="margin-top: 20px; display: block; background: #00e676; color: #020c1b;">Go to Login</a>
            </div>
        <?php else: ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error" style="margin-bottom: 20px; padding: 10px; border-radius: 8px; background: rgba(255,85,85,0.1); color: #ff5555; font-size: 0.85rem; border: 1px solid rgba(255,85,85,0.2);">
                    ⚠️ <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="input-group-modern" style="position: relative;">
                    <label>New Password</label>
                    <input type="password" name="password" placeholder="••••••••" required autofocus>
                    
                    <!-- Password requirements popup -->
                    <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 10px; border-radius: 8px; font-size: 0.8rem; margin-top: 5px;">
                        <strong>Password must contain:</strong>
                        <ul style="padding-left: 18px; margin: 5px 0;">
                            <li>At least 8 characters</li>
                            <li>1 uppercase letter (A-Z)</li>
                            <li>1 lowercase letter (a-z)</li>
                            <li>1 number (0-9)</li>
                            <li>1 special character (!@#$%^&*)</li>
                        </ul>
                    </div>
                </div>

                <div class="input-group-modern" style="margin-top: 20px;">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 30px; padding: 14px;">
                    Update Password
                </button>
            </form>
        <?php endif; ?>

    </div>
</div>