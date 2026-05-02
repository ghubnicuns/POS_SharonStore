<?php
session_start();
require 'db_connect.php'; // Ensure this file exists in the same folder

// If they are already logged in, send them straight to the dashboard
if (isset($_SESSION['UserID'])) {
    header("Location: dashboard.php");
    exit();
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_message = 'Username and password are required.';
    } else {
        // Query the database from your ERD structure
        $stmt = $pdo->prepare("SELECT * FROM tbl_users WHERE Username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify user exists and password matches
        if ($user && password_verify($password, $user['Password'])) {
            // Success! Set session variables
            $_SESSION['UserID'] = $user['UserID'];
            $_SESSION['Username'] = $user['Username'];
            $_SESSION['Role'] = $user['Role'];

            // Log the login action to the audit trail
            $log_stmt = $pdo->prepare("INSERT INTO tbl_audit_logs (UserID, Action_Performed) VALUES (?, 'User logged in')");
            $log_stmt->execute([$user['UserID']]);

            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Sharon Store</title>
    <meta name="description" content="Sign in to Sharon Store management system to manage inventory, sales, and restocking.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --pink:       #198754;
            --pink-light: #20c997;
            --pink-dark:  #146c43;
            --purple:     #0d6efd;
            --bg:         #f0f2f5;
            --surface:    rgba(255,255,255,0.85);
            --border:     rgba(0,0,0,0.12);
            --text:       #212529;
            --muted:      #6c757d;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: auto;
            position: relative;
            padding: 20px 0;
        }

        /* Animated background orbs */
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            pointer-events: none;
            animation: floatOrb 8s ease-in-out infinite alternate;
        }
        .orb1 { width: 500px; height: 500px; background: #20c997; top: -150px; left: -150px; animation-delay: 0s; }
        .orb2 { width: 400px; height: 400px; background: #0d6efd; bottom: -120px; right: -120px; animation-delay: 2s; }
        .orb3 { width: 300px; height: 300px; background: #198754; top: 50%; left: 50%; transform: translate(-50%,-50%); animation-delay: 4s; opacity: 0.10; }

        @keyframes floatOrb {
            from { transform: scale(1) translate(0,0); }
            to   { transform: scale(1.15) translate(20px, -20px); }
        }

        /* Glass card */
        .auth-card {
            position: relative;
            z-index: 10;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 48px 44px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.12), inset 0 1px 0 rgba(255,255,255,0.9);
            animation: cardIn 0.6s cubic-bezier(0.34,1.56,0.64,1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Logo */
        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }
        .logo-icon {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, var(--pink), var(--purple));
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            box-shadow: 0 8px 24px rgba(25,135,84,0.35);
        }
        .logo-name { font-size: 22px; font-weight: 800; color: var(--text); letter-spacing: -0.5px; }
        .logo-name span { color: var(--pink-light); }

        /* Headings */
        .auth-heading { font-size: 26px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
        .auth-sub { font-size: 14px; color: var(--muted); margin-bottom: 28px; }

        /* Form */
        .form-group { margin-bottom: 18px; position: relative; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--muted); margin-bottom: 8px; letter-spacing: 0.3px; transition: color 0.2s; }
        .form-group:focus-within .form-label { color: var(--pink); }
        .input-wrap { position: relative; }
        .input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 15px; pointer-events: none; transition: all 0.2s; }
        .form-group:focus-within .input-icon { color: var(--pink); }
        .form-control {
            width: 100%;
            padding: 13px 14px 13px 40px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            color: var(--text);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }
        .form-control::placeholder { color: #adb5bd; transition: color 0.2s; }
        .form-control:focus {
            border-color: var(--pink);
            box-shadow: 0 0 0 3px rgba(25,135,84,0.18);
            background: #fff;
            transform: translateY(-1px);
        }
        .form-control:hover:not(:focus) {
            border-color: #c3e6cb;
            background: #fbfcfd;
        }
        .toggle-pw {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--muted); cursor: pointer;
            font-size: 15px; padding: 0; line-height: 1; transition: color 0.2s;
        }
        .toggle-pw:hover { color: var(--pink-light); }

        /* Field validation states */
        .form-group.has-error .form-control {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220,38,38,0.15);
        }
        .form-group.has-success .form-control {
            border-color: var(--pink-light);
            box-shadow: 0 0 0 3px rgba(32,201,151,0.15);
        }
        .form-help-text {
            font-size: 12px; color: #dc2626; margin-top: 6px; display: none; animation: slideInDown 0.2s ease-out;
        }
        .form-group.has-error .form-help-text { display: block; }

        @keyframes slideInDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Password strength indicator */
        .password-strength {
            margin-top: 10px; display: none; gap: 4px;
        }
        .strength-bar { flex: 1; height: 3px; background: #e5e7eb; border-radius: 2px; overflow: hidden; }
        .strength-fill { height: 100%; width: 0%; transition: width 0.3s ease, background-color 0.3s ease; }
        .strength-label { font-size: 11px; font-weight: 600; letter-spacing: 0.5px; }

        /* Options row */
        .options-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 22px;
        }
        .checkbox-label { display: flex; align-items: center; gap: 7px; font-size: 13px; color: var(--muted); cursor: pointer; transition: color 0.2s; user-select: none; }
        .checkbox-label input { accent-color: var(--pink); width: 15px; height: 15px; cursor: pointer; transition: transform 0.2s; }
        .checkbox-label input:checked { transform: scale(1.1); }
        .checkbox-label:hover { color: var(--text); }
        .forgot-link { font-size: 13px; color: var(--pink-light); text-decoration: none; font-weight: 500; transition: color 0.2s; }
        .forgot-link:hover { color: var(--pink); }

        /* Button */
        .btn-primary {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, var(--pink), var(--purple));
            border: none; border-radius: 12px;
            color: #fff; font-size: 15px; font-weight: 700;
            cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 24px rgba(25,135,84,0.35);
            letter-spacing: 0.3px;
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow: hidden;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: rgba(255,255,255,0.1);
            transition: left 0.4s ease;
        }
        .btn-primary:hover::before { left: 100%; }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(25,135,84,0.45); }
        .btn-primary:active { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(25,135,84,0.35); }

        /* Divider */
        .divider { display: flex; align-items: center; gap: 12px; margin: 22px 0; transition: opacity 0.2s; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); transition: background 0.2s; }
        .divider span { font-size: 12px; color: var(--muted); white-space: nowrap; }

        /* Footer link */
        .auth-footer { text-align: center; font-size: 14px; color: var(--muted); }
        .auth-footer a { color: var(--pink-light); font-weight: 600; text-decoration: none; transition: all 0.2s; position: relative; }
        .auth-footer a::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 2px; background: var(--pink-light); transition: width 0.2s; }
        .auth-footer a:hover::after { width: 100%; }
        .auth-footer a:hover { color: var(--pink); }

        /* Alert */
        .alert-error {
            background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.35);
            border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #dc2626;
            margin-bottom: 18px; display: none; align-items: center; gap: 8px;
            animation: slideDown 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .alert-error.show { display: flex; }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Loading spinner on button */
        .spinner { display: none; width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .btn-primary.loading .btn-text { display: none; }
        .btn-primary.loading .spinner { display: inline-block; }

        /* Shake animation for errors */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }
        .btn-primary:active:not(.loading) {
            animation: shake 0.3s ease-in-out;
        }
        
        .form-control:focus-visible {
            outline: 2px solid var(--pink);
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <div class="bg-orb orb1"></div>
    <div class="bg-orb orb2"></div>
    <div class="bg-orb orb3"></div>

    <div class="auth-card">
        <div class="logo-wrap">
            <div class="logo-icon">🛒</div>
            <div class="logo-name">Sharon<span>Store</span></div>
        </div>

        <h1 class="auth-heading">Welcome back 👋</h1>
        <p class="auth-sub">Sign in to your store management account</p>

        <!-- PHP Error Output via CSS class -->
        <div class="alert-error <?php echo !empty($error_message) ? 'show' : ''; ?>" id="loginError">
            <i class="fa fa-circle-exclamation"></i>
            <span id="loginErrorMsg"><?php echo htmlspecialchars($error_message); ?></span>
        </div>

        <!-- Form updated to POST to PHP -->
        <form id="loginForm" method="POST" action="">
            <div class="form-group" id="usernameGroup">
                <label class="form-label" for="loginUsername">Username</label>
                <div class="input-wrap">
                    <i class="input-icon fa fa-user"></i>
                    <!-- Added name="username" -->
                    <input type="text" name="username" id="loginUsername" class="form-control" placeholder="Enter your username" autocomplete="username" required>
                </div>
                <div class="form-help-text" id="usernameHelp"></div>
            </div>

            <div class="form-group" id="passwordGroup">
                <label class="form-label" for="loginPassword">Password</label>
                <div class="input-wrap">
                    <i class="input-icon fa fa-lock"></i>
                    <!-- Added name="password" -->
                    <input type="password" name="password" id="loginPassword" class="form-control" placeholder="Enter your password" autocomplete="current-password" required>
                    <button type="button" class="toggle-pw" id="togglePw" aria-label="Toggle password visibility">
                        <i class="fa fa-eye" id="togglePwIcon"></i>
                    </button>
                </div>
                <div class="password-strength" id="passwordStrength">
                    <div style="flex: 1; display: flex; gap: 4px;">
                        <div class="strength-bar"><div class="strength-fill" id="strengthFill1"></div></div>
                        <div class="strength-bar"><div class="strength-fill" id="strengthFill2"></div></div>
                        <div class="strength-bar"><div class="strength-fill" id="strengthFill3"></div></div>
                        <div class="strength-bar"><div class="strength-fill" id="strengthFill4"></div></div>
                    </div>
                    <span class="strength-label" id="strengthLabel"></span>
                </div>
                <div class="form-help-text" id="passwordHelp"></div>
            </div>

            <div class="options-row">
                <label class="checkbox-label">
                    <input type="checkbox" id="rememberMe" name="rememberMe"> Remember me
                </label>
                <a href="#" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="btn-primary" id="loginBtn">
                <span class="btn-text">Sign In</span>
                <span class="spinner"></span>
            </button>
        </form>

        <div class="divider"><span>Don't have an account?</span></div>

        <div class="auth-footer">
            <a href="signup.php">Create a new account →</a>
        </div>
    </div>

    <script>
        // === UI Interactions ===
        const passwordInput = document.getElementById('loginPassword');
        const passwordGroup = document.getElementById('passwordGroup');
        const passwordHelp = document.getElementById('passwordHelp');

        // Password strength indicator
        function calculatePasswordStrength(pwd) {
            let strength = 0;
            if (pwd.length >= 8) strength++;
            if (pwd.length >= 12) strength++;
            if (/[A-Z]/.test(pwd)) strength++;
            if (/[0-9]/.test(pwd)) strength++;
            if (/[^A-Za-z0-9]/.test(pwd)) strength++;
            return Math.min(strength, 4);
        }

        function updatePasswordStrength(pwd) {
            if (!pwd) {
                document.getElementById('passwordStrength').style.display = 'none';
                passwordHelp.textContent = '';
                return;
            }

            const strength = calculatePasswordStrength(pwd);
            document.getElementById('passwordStrength').style.display = 'flex';

            const fills = ['strengthFill1', 'strengthFill2', 'strengthFill3', 'strengthFill4'];
            const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
            const labels = ['Weak', 'Fair', 'Good', 'Strong'];

            fills.forEach((id, idx) => {
                const fill = document.getElementById(id);
                if (idx < strength) {
                    fill.style.width = '100%';
                    fill.style.backgroundColor = colors[idx];
                } else {
                    fill.style.width = '0%';
                }
            });

            document.getElementById('strengthLabel').textContent = labels[strength - 1] || '';
        }

        passwordInput.addEventListener('input', function() {
            updatePasswordStrength(this.value);
        });

        passwordInput.addEventListener('focus', function() {
            if (this.value) updatePasswordStrength(this.value);
        });

        // Toggle password visibility
        document.getElementById('togglePw').addEventListener('click', function(e) {
            e.preventDefault();
            const pw = document.getElementById('loginPassword');
            const icon = document.getElementById('togglePwIcon');
            if (pw.type === 'password') { 
                pw.type = 'text'; 
                icon.className = 'fa fa-eye-slash'; 
            }
            else { 
                pw.type = 'password'; 
                icon.className = 'fa fa-eye'; 
            }
        });

        // Add loading state to button when form is submitted natively
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            if(document.getElementById('loginUsername').value && document.getElementById('loginPassword').value) {
                btn.classList.add('loading');
            }
        });
    </script>
</body>
</html>