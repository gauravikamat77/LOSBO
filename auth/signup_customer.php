<?php
// Floating popup messages
if(isset($_GET['error'])){
    $msg = "";
    switch($_GET['error']){
        case 1:
            $msg = "❌ Please fill in all required fields.";
            break;
        case 2:
            $msg = "❌ Passwords do not match.";
            break;
        case 3:
            $msg = "❌ Password must be at least 8 characters and include uppercase, lowercase, number & special character.";
            break;
        case 4:
            $msg = "❌ Email already exists. Please use a different email.";
            break;
        case 5:
            $msg = "❌ An error occurred while creating your account. Please try again.";
            break;
    }
    echo "
    <div id='popup-error' style='position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: rgba(255,0,0,0.9); color: white; padding: 15px 25px; border-radius: 12px; font-weight: bold; z-index: 9999; box-shadow: 0 0 20px rgba(255,0,0,0.5);'>
        {$msg}
    </div>
    <script>
        setTimeout(()=> {
            document.getElementById('popup-error').style.display = 'none';
        }, 4000);
    </script>
    ";
}

if(isset($_GET['success'])){
    $msg = "";
    switch($_GET['success']){
        case 1:
            $msg = "✅ Account created successfully!";
            break;
    }
    echo "
    <div id='popup-success' style='position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: rgba(0,128,0,0.9); color: white; padding: 15px 25px; border-radius: 12px; font-weight: bold; z-index: 9999; box-shadow: 0 0 20px rgba(0,128,0,0.5); text-align:center;'>
        {$msg} <br>
        <button onclick=\"window.location.href='login.php'\" style='margin-top:10px; padding:8px 15px; background:#00e676; border:none; color:#020c1b; font-weight:bold; border-radius:5px; cursor:pointer;'>
            Go to Login
        </button>
    </div>
    ";
}
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper">
    <div class="glass-card signup-form-card" style="max-width:500px; width:90%; padding:40px;">
        
        <h2 class="logo-title">Join LOSBO</h2>
        <p class="slogan">Create your customer account</p>

        <form action="signup_customer_process.php" method="POST" class="form-group">
            
            <input type="text" name="name" placeholder="Full name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone Number">

            <!-- NEW FIELDS -->
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <input type="text" name="language" placeholder="Preferred Language" required>

            <input type="date" name="dob" required>

            <input type="text" name="city" placeholder="City" required>
            <input type="text" name="state" placeholder="State" required>
            <input type="text" name="pincode" placeholder="Pincode" required>

            <!-- PASSWORD -->
            <div style="position: relative; margin-bottom: 10px;">
                <input type="password" id="password" name="password" placeholder="Password" required>
                
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

            <button type="submit" class="btn" style="margin-top: 15px;">
                Create Account
            </button>

            <div style="margin-top: 20px; color: rgba(255,255,255,0.6); font-size: 0.9rem;">
                Already have an account? 
                <a href="login.php" class="link-blue" style="font-weight: bold;">Login</a>
            </div>
        </form>

    </div>
</div>

<script>
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