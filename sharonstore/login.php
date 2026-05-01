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
            overflow: hidden;
            position: relative;
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
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--muted); margin-bottom: 8px; letter-spacing: 0.3px; }
        .input-wrap { position: relative; }
        .input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 15px; pointer-events: none; }
        .form-control {
            width: 100%;
            padding: 13px 14px 13px 40px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            color: var(--text);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-control::placeholder { color: #adb5bd; }
        .form-control:focus {
            border-color: var(--pink);
            box-shadow: 0 0 0 3px rgba(25,135,84,0.18);
        }
        .toggle-pw {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--muted); cursor: pointer;
            font-size: 15px; padding: 0; line-height: 1; transition: color 0.2s;
        }
        .toggle-pw:hover { color: var(--pink-light); }

        /* Options row */
        .options-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 22px;
        }
        .checkbox-label { display: flex; align-items: center; gap: 7px; font-size: 13px; color: var(--muted); cursor: pointer; }
        .checkbox-label input { accent-color: var(--pink); width: 15px; height: 15px; }
        .forgot-link { font-size: 13px; color: var(--pink-light); text-decoration: none; font-weight: 500; }
        .forgot-link:hover { color: var(--pink); }

        /* Button */
        .btn-primary {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, var(--pink), var(--purple));
            border: none; border-radius: 12px;
            color: #fff; font-size: 15px; font-weight: 700;
            cursor: pointer; transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
            box-shadow: 0 8px 24px rgba(25,135,84,0.35);
            letter-spacing: 0.3px;
            font-family: 'Inter', sans-serif;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(25,135,84,0.45); }
        .btn-primary:active { transform: translateY(0); }

        /* Divider */
        .divider { display: flex; align-items: center; gap: 12px; margin: 22px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
        .divider span { font-size: 12px; color: var(--muted); white-space: nowrap; }

        /* Footer link */
        .auth-footer { text-align: center; font-size: 14px; color: var(--muted); }
        .auth-footer a { color: var(--pink-light); font-weight: 600; text-decoration: none; }
        .auth-footer a:hover { color: var(--pink); }

        /* Alert */
        .alert-error {
            background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.35);
            border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #fca5a5;
            margin-bottom: 18px; display: none; align-items: center; gap: 8px;
        }
        .alert-error.show { display: flex; }

        /* Loading spinner on button */
        .spinner { display: none; width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .btn-primary.loading .btn-text { display: none; }
        .btn-primary.loading .spinner { display: inline-block; }
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

        <div class="alert-error" id="loginError">
            <i class="fa fa-circle-exclamation"></i>
            <span id="loginErrorMsg">Invalid username or password.</span>
        </div>

        <form id="loginForm" novalidate>
            <div class="form-group">
                <label class="form-label" for="loginUsername">Username</label>
                <div class="input-wrap">
                    <i class="input-icon fa fa-user"></i>
                    <input type="text" id="loginUsername" class="form-control" placeholder="Enter your username" autocomplete="username" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="loginPassword">Password</label>
                <div class="input-wrap">
                    <i class="input-icon fa fa-lock"></i>
                    <input type="password" id="loginPassword" class="form-control" placeholder="Enter your password" autocomplete="current-password" required>
                    <button type="button" class="toggle-pw" id="togglePw" aria-label="Toggle password visibility">
                        <i class="fa fa-eye" id="togglePwIcon"></i>
                    </button>
                </div>
            </div>

            <div class="options-row">
                <label class="checkbox-label">
                    <input type="checkbox" id="rememberMe"> Remember me
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
        // --- Demo accounts (stored in localStorage via signup) ---
        const DEFAULT_ACCOUNTS = [
            { username: 'admin', password: 'admin123', role: 'Admin', fullName: 'Sharon Admin' }
        ];

        function getAccounts() {
            const stored = localStorage.getItem('sharonstore_accounts');
            const accounts = stored ? JSON.parse(stored) : [];
            // Merge defaults (avoid duplicates)
            DEFAULT_ACCOUNTS.forEach(def => {
                if (!accounts.find(a => a.username === def.username)) accounts.unshift(def);
            });
            return accounts;
        }

        // Toggle password visibility
        document.getElementById('togglePw').addEventListener('click', function() {
            const pw = document.getElementById('loginPassword');
            const icon = document.getElementById('togglePwIcon');
            if (pw.type === 'password') { pw.type = 'text'; icon.className = 'fa fa-eye-slash'; }
            else { pw.type = 'password'; icon.className = 'fa fa-eye'; }
        });

        // Login submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');

            const username = document.getElementById('loginUsername').value.trim();
            const password = document.getElementById('loginPassword').value;

            setTimeout(() => {
                const accounts = getAccounts();
                const match = accounts.find(a => a.username === username && a.password === password);

                if (match) {
                    // Save session to localStorage
                    localStorage.setItem('sharonstore_session', JSON.stringify({
                        loggedIn: true,
                        username: match.username,
                        fullName: match.fullName,
                        role: match.role
                    }));
                    window.location.href = 'dashboard.php';
                } else {
                    btn.classList.remove('loading');
                    const err = document.getElementById('loginError');
                    err.classList.add('show');
                    document.getElementById('loginErrorMsg').textContent = 'Invalid username or password. Try admin / admin123';
                    setTimeout(() => err.classList.remove('show'), 4000);
                }
            }, 900);
        });

        // Check if already logged in
        const session = localStorage.getItem('sharonstore_session');
        if (session && JSON.parse(session).loggedIn) {
            window.location.href = 'dashboard.php';
        }
    </script>
</body>
</html>
