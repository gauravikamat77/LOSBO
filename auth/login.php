<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper">
    <div class="glass-card login-card">
        
        <h2 class="logo-title">LOSBO</h2>
        <p class="slogan">Let's go Losbo!!!</p>

        <form action="login_process.php" method="POST" class="form-group">
            
            <input type="email" name="email" placeholder="Email" required>

            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" class="btn">Login</button>

            <div style="margin-top: 15px;">
                <a href="forgot_password.php" class="link-blue" style="font-size: 0.9rem;">Forgot Password?</a>
            </div>

            <div style="margin-top: 10px; color: rgba(255,255,255,0.6); font-size: 0.9rem;">
                No account? <a href="signup.php" class="link-blue" style="font-weight: bold;">Create Account</a>
            </div>

        </form>

    </div>
</div>
<?php
if(isset($_GET['error'])){
    $msg = "";
    if($_GET['error'] == 1){
        $msg = "❌ Incorrect password. Please try again.";
    } elseif($_GET['error'] == 2){
        $msg = "❌ User not found. Please check your email.";
    }
    echo "
    <div id='error-popup' style='position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: rgba(255,0,0,0.9); color: white; padding: 15px 25px; border-radius: 12px; font-weight: bold; z-index: 9999; box-shadow: 0 0 20px rgba(255,0,0,0.5);'>
        {$msg}
    </div>
    <script>
        setTimeout(()=> {
            document.getElementById('error-popup').style.display = 'none';
        }, 4000);
    </script>
    ";
}
?>