<link rel="stylesheet" href="../assets/css/style.css">

<div class="page-wrapper" style="flex-direction: column; align-items: center; padding-top: 80px; padding-bottom: 80px;">

    <div class="glass-card login-card" style="max-width: 450px; width: 100%; padding: 40px; border-top: 4px solid var(--accent-blue);">
        
        <h2 class="logo-title" style="text-align:center; margin-bottom:10px;">Reset Access</h2>
        <p class="slogan" style="text-align:center; color: var(--text-muted); font-size:0.9rem; margin-bottom:30px;">
            Enter your registered email to receive a reset link
        </p>

        <form action="send_reset_link.php" method="POST" class="form-group" style="display:flex; flex-direction:column; gap:15px;">

            <div style="display:flex; flex-direction:column; text-align:left;">
                <label style="color: var(--text-muted); font-size: 0.9rem; padding-left: 5px; margin-bottom:5px;">
                    Registered Email Address
                </label>
                <input type="email" name="email" placeholder="e.g. name@example.com" required
                       style="padding:12px; border-radius:10px; border:1px solid var(--glass-border); background: rgba(255,255,255,0.05); color:white;">
            </div>

            <button type="submit" class="btn" style="width:100%; padding:12px; margin-top:5px;">
                Send Reset Link
            </button>

            <div style="margin-top: 25px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px; text-align:center;">
                <a href="login.php" class="link-blue" style="font-size: 0.9rem; color: var(--accent-blue); text-decoration:none;">
                    ← Back to Login
                </a>
            </div>
            
        </form>

    </div>

</div>