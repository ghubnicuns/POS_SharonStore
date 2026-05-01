<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up – Sharon Store</title>
    <meta name="description" content="Create your Sharon Store management account to get started.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --pink:       #e91e8c;
            --pink-light: #ff5ab0;
            --pink-dark:  #b5006e;
            --purple:     #7c3aed;
            --bg:         #0f0715;
            --surface:    rgba(255,255,255,0.05);
            --border:     rgba(255,255,255,0.10);
            --text:       #f1e6ff;
            --muted:      #a78bca;
            --success:    #22c55e;
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
            padding: 24px;
        }

        .bg-orb { position: fixed; border-radius: 50%; filter: blur(80px); opacity: 0.35; pointer-events: none; animation: floatOrb 8s ease-in-out infinite alternate; }
        .orb1 { width: 500px; height: 500px; background: var(--purple); top: -150px; right: -150px; animation-delay: 0s; }
        .orb2 { width: 400px; height: 400px; background: var(--pink); bottom: -120px; left: -120px; animation-delay: 2s; }

        @keyframes floatOrb { from { transform: scale(1); } to { transform: scale(1.15) translate(15px,-15px); } }

        .auth-card {
            position: relative; z-index: 10;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 44px 44px;
            width: 100%; max-width: 500px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.1);
            animation: cardIn 0.6s cubic-bezier(0.34,1.56,0.64,1) both;
        }

        @keyframes cardIn { from { opacity: 0; transform: translateY(40px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }

        .logo-wrap { display: flex; align-items: center; gap: 12px; margin-bottom: 28px; }
        .logo-icon { width: 48px; height: 48px; background: linear-gradient(135deg, var(--pink), var(--purple)); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; box-shadow: 0 8px 24px rgba(233,30,140,0.4); }
        .logo-name { font-size: 22px; font-weight: 800; color: var(--text); letter-spacing: -0.5px; }
        .logo-name span { color: var(--pink-light); }

        .auth-heading { font-size: 24px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
        .auth-sub { font-size: 14px; color: var(--muted); margin-bottom: 28px; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--muted); margin-bottom: 7px; }
        .input-wrap { position: relative; }
        .input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 14px; pointer-events: none; }
        .form-control {
            width: 100%; padding: 12px 14px 12px 38px;
            background: rgba(255,255,255,0.07); border: 1px solid var(--border);
            border-radius: 12px; color: var(--text); font-size: 14px;
            font-family: 'Inter', sans-serif; transition: border-color 0.2s, box-shadow 0.2s; outline: none;
        }
        .form-control::placeholder { color: rgba(167,139,202,0.5); }
        .form-control:focus { border-color: var(--pink); box-shadow: 0 0 0 3px rgba(233,30,140,0.18); }
        .form-control.valid { border-color: var(--success); }
        .form-control.invalid { border-color: #ef4444; }

        .toggle-pw { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--muted); cursor: pointer; font-size: 14px; padding: 0; transition: color 0.2s; }
        .toggle-pw:hover { color: var(--pink-light); }

        /* Password strength */
        .pw-strength-bar { height: 4px; border-radius: 4px; background: rgba(255,255,255,0.1); margin-top: 8px; overflow: hidden; }
        .pw-strength-fill { height: 100%; width: 0%; border-radius: 4px; transition: width 0.3s, background 0.3s; }
        .pw-hint { font-size: 11px; color: var(--muted); margin-top: 5px; }

        /* Terms */
        .terms-group { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 20px; }
        .terms-group input { margin-top: 2px; accent-color: var(--pink); min-width: 15px; height: 15px; }
        .terms-text { font-size: 13px; color: var(--muted); line-height: 1.5; }
        .terms-text a { color: var(--pink-light); text-decoration: none; }

        .btn-primary { width: 100%; padding: 14px; background: linear-gradient(135deg, var(--pink), var(--purple)); border: none; border-radius: 12px; color: #fff; font-size: 15px; font-weight: 700; cursor: pointer; transition: transform 0.15s, box-shadow 0.15s; box-shadow: 0 8px 24px rgba(233,30,140,0.35); font-family: 'Inter', sans-serif; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(233,30,140,0.45); }
        .btn-primary:active { transform: translateY(0); }

        .divider { display: flex; align-items: center; gap: 12px; margin: 22px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
        .divider span { font-size: 12px; color: var(--muted); }

        .auth-footer { text-align: center; font-size: 14px; color: var(--muted); }
        .auth-footer a { color: var(--pink-light); font-weight: 600; text-decoration: none; }
        .auth-footer a:hover { color: var(--pink); }

        .alert-success { background: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.3); border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #86efac; margin-bottom: 18px; display: none; align-items: center; gap: 8px; }
        .alert-success.show { display: flex; }
        .alert-error { background: rgba(220,38,38,0.12); border: 1px solid rgba(220,38,38,0.3); border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #fca5a5; margin-bottom: 18px; display: none; align-items: center; gap: 8px; }
        .alert-error.show { display: flex; }

        .spinner { display: none; width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .btn-primary.loading .btn-text { display: none; }
        .btn-primary.loading .spinner { display: inline-block; }

        @media (max-width: 480px) { .form-row { grid-template-columns: 1fr; } .auth-card { padding: 32px 24px; } }
    </style>
</head>
<body>
    <div class="bg-orb orb1"></div>
    <div class="bg-orb orb2"></div>

    <div class="auth-card">
        <div class="logo-wrap">
            <div class="logo-icon">🛒</div>
            <div class="logo-name">Sharon<span>Store</span></div>
        </div>

        <h1 class="auth-heading">Create your account ✨</h1>
        <p class="auth-sub">Join Sharon Store management system today</p>

        <div class="alert-success" id="successAlert">
            <i class="fa fa-circle-check"></i>
            <span>Account created! Redirecting to login…</span>
        </div>
        <div class="alert-error" id="errorAlert">
            <i class="fa fa-circle-exclamation"></i>
            <span id="errorMsg">Something went wrong. Please try again.</span>
        </div>

        <form id="signupForm" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="firstName">First Name</label>
                    <div class="input-wrap">
                        <i class="input-icon fa fa-user"></i>
                        <input type="text" id="firstName" class="form-control" placeholder="Sharon" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="lastName">Last Name</label>
                    <div class="input-wrap">
                        <i class="input-icon fa fa-user"></i>
                        <input type="text" id="lastName" class="form-control" placeholder="Dela Cruz" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="signupUsername">Username</label>
                <div class="input-wrap">
                    <i class="input-icon fa fa-at"></i>
                    <input type="text" id="signupUsername" class="form-control" placeholder="Choose a username" required autocomplete="off">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="signupEmail">Email Address</label>
                <div class="input-wrap">
                    <i class="input-icon fa fa-envelope"></i>
                    <input type="email" id="signupEmail" class="form-control" placeholder="you@email.com" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="signupRole">Role</label>
                <div class="input-wrap">
                    <i class="input-icon fa fa-shield-halved"></i>
                    <select id="signupRole" class="form-control" style="padding-left:38px; cursor:pointer;">
                        <option value="Staff">Staff</option>
                        <option value="Manager">Manager</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="signupPassword">Password</label>
                <div class="input-wrap">
                    <i class="input-icon fa fa-lock"></i>
                    <input type="password" id="signupPassword" class="form-control" placeholder="Create a strong password" required autocomplete="new-password">
                    <button type="button" class="toggle-pw" id="togglePwSignup">
                        <i class="fa fa-eye" id="togglePwIconSignup"></i>
                    </button>
                </div>
                <div class="pw-strength-bar"><div class="pw-strength-fill" id="pwStrengthFill"></div></div>
                <div class="pw-hint" id="pwHint">Enter a password</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirmPassword">Confirm Password</label>
                <div class="input-wrap">
                    <i class="input-icon fa fa-lock"></i>
                    <input type="password" id="confirmPassword" class="form-control" placeholder="Repeat your password" required autocomplete="new-password">
                </div>
            </div>

            <div class="terms-group">
                <input type="checkbox" id="agreeTerms" required>
                <label class="terms-text" for="agreeTerms">
                    I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a> of Sharon Store.
                </label>
            </div>

            <button type="submit" class="btn-primary" id="signupBtn">
                <span class="btn-text">Create Account</span>
                <span class="spinner"></span>
            </button>
        </form>

        <div class="divider"><span>Already have an account?</span></div>
        <div class="auth-footer"><a href="login.php">← Back to Sign In</a></div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePwSignup').addEventListener('click', function() {
            const pw = document.getElementById('signupPassword');
            const icon = document.getElementById('togglePwIconSignup');
            if (pw.type === 'password') { pw.type = 'text'; icon.className = 'fa fa-eye-slash'; }
            else { pw.type = 'password'; icon.className = 'fa fa-eye'; }
        });

        // Password strength indicator
        document.getElementById('signupPassword').addEventListener('input', function() {
            const val = this.value;
            const fill = document.getElementById('pwStrengthFill');
            const hint = document.getElementById('pwHint');
            let score = 0;
            if (val.length >= 6) score++;
            if (val.length >= 10) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = [
                { pct: '0%', color: 'transparent', text: 'Enter a password' },
                { pct: '20%', color: '#ef4444', text: 'Very weak' },
                { pct: '40%', color: '#f97316', text: 'Weak' },
                { pct: '60%', color: '#eab308', text: 'Fair' },
                { pct: '80%', color: '#22c55e', text: 'Strong' },
                { pct: '100%', color: '#10b981', text: 'Very strong 🎉' }
            ];
            const lvl = val.length === 0 ? 0 : Math.min(score, 5);
            fill.style.width = levels[lvl].pct;
            fill.style.background = levels[lvl].color;
            hint.textContent = levels[lvl].text;
        });

        // Form submit
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('signupBtn');
            const errorEl = document.getElementById('errorAlert');
            const errorMsg = document.getElementById('errorMsg');

            // Validate
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const username = document.getElementById('signupUsername').value.trim();
            const email = document.getElementById('signupEmail').value.trim();
            const role = document.getElementById('signupRole').value;
            const password = document.getElementById('signupPassword').value;
            const confirm = document.getElementById('confirmPassword').value;
            const agreed = document.getElementById('agreeTerms').checked;

            if (!firstName || !lastName) { showError('Please enter your full name.'); return; }
            if (!username || username.length < 3) { showError('Username must be at least 3 characters.'); return; }
            if (!email.includes('@')) { showError('Please enter a valid email address.'); return; }
            if (password.length < 6) { showError('Password must be at least 6 characters.'); return; }
            if (password !== confirm) { showError('Passwords do not match.'); return; }
            if (!agreed) { showError('Please agree to the Terms of Service.'); return; }

            // Check duplicate username
            const accounts = getAccounts();
            if (accounts.find(a => a.username === username)) {
                showError('That username is already taken. Choose another.');
                return;
            }

            btn.classList.add('loading');

            setTimeout(() => {
                // Save new account
                accounts.push({ username, password, role, fullName: `${firstName} ${lastName}`, email });
                localStorage.setItem('sharonstore_accounts', JSON.stringify(accounts));

                btn.classList.remove('loading');
                errorEl.classList.remove('show');
                document.getElementById('successAlert').classList.add('show');

                setTimeout(() => { window.location.href = 'login.php'; }, 2000);
            }, 1200);
        });

        function showError(msg) {
            const el = document.getElementById('errorAlert');
            document.getElementById('errorMsg').textContent = msg;
            el.classList.add('show');
            setTimeout(() => el.classList.remove('show'), 4000);
        }

        function getAccounts() {
            const stored = localStorage.getItem('sharonstore_accounts');
            return stored ? JSON.parse(stored) : [];
        }

        // Redirect if already logged in
        const session = localStorage.getItem('sharonstore_session');
        if (session && JSON.parse(session).loggedIn) { window.location.href = 'dashboard.php'; }
    </script>
</body>
</html>
