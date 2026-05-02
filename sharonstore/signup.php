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

        .bg-orb { position: fixed; border-radius: 50%; filter: blur(80px); opacity: 0.35; pointer-events: none; animation: floatOrb 8s ease-in-out infinite alternate; }
        .orb1 { width: 500px; height: 500px; background: #20c997; top: -150px; left: -150px; animation-delay: 0s; }
        .orb2 { width: 400px; height: 400px; background: #0d6efd; bottom: -120px; right: -120px; animation-delay: 2s; }
        .orb3 { width: 300px; height: 300px; background: #198754; top: 50%; left: 50%; transform: translate(-50%,-50%); animation-delay: 4s; opacity: 0.10; }

        @keyframes floatOrb { from { transform: scale(1) translate(0,0); } to { transform: scale(1.15) translate(20px, -20px); } }

        .auth-card {
            position: relative; z-index: 10;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 48px 44px;
            width: 100%; max-width: 480px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.12), inset 0 1px 0 rgba(255,255,255,0.9);
            animation: cardIn 0.6s cubic-bezier(0.34,1.56,0.64,1) both;
        }

        @keyframes cardIn { from { opacity: 0; transform: translateY(40px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }

        .logo-wrap { display: flex; align-items: center; gap: 12px; margin-bottom: 32px; }
        .logo-icon { width: 48px; height: 48px; background: linear-gradient(135deg, var(--pink), var(--purple)); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; box-shadow: 0 8px 24px rgba(25,135,84,0.35); }
        .logo-name { font-size: 22px; font-weight: 800; color: var(--text); letter-spacing: -0.5px; }
        .logo-name span { color: var(--pink-light); }

        .auth-heading { font-size: 26px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
        .auth-sub { font-size: 14px; color: var(--muted); margin-bottom: 28px; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

        .form-group { margin-bottom: 18px; position: relative; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--muted); margin-bottom: 8px; letter-spacing: 0.3px; transition: color 0.2s; }
        .form-group:focus-within .form-label { color: var(--pink); }
        .input-wrap { position: relative; }
        .input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 15px; pointer-events: none; transition: all 0.2s; }
        .form-group:focus-within .input-icon { color: var(--pink); }
        .form-control {
            width: 100%; padding: 13px 14px 13px 40px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 12px; color: var(--text); font-size: 14px;
            font-family: 'Inter', sans-serif; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); outline: none;
        }
        .form-control::placeholder { color: #adb5bd; transition: color 0.2s; }
        .form-control:focus { border-color: var(--pink); box-shadow: 0 0 0 3px rgba(25,135,84,0.18); background: #fff; transform: translateY(-1px); }
        .form-control:hover:not(:focus) { border-color: #c3e6cb; background: #fbfcfd; }
        .form-control.valid { border-color: var(--pink); box-shadow: 0 0 0 3px rgba(25,135,84,0.15); }
        .form-control.invalid { border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220,38,38,0.15); }
        
        .form-help-text {
            font-size: 12px; color: #dc2626; margin-top: 6px; display: none; animation: slideInDown 0.2s ease-out;
        }
        .form-group.has-error .form-help-text { display: block; }

        @keyframes slideInDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .toggle-pw { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--muted); cursor: pointer; font-size: 15px; padding: 0; line-height: 1; transition: color 0.2s; }
        .toggle-pw:hover { color: var(--pink-light); }

        /* Password strength indicator */
        .password-strength {
            margin-top: 10px; display: none; gap: 4px;
        }
        .strength-bar { flex: 1; height: 3px; background: #e5e7eb; border-radius: 2px; overflow: hidden; }
        .strength-fill { height: 100%; width: 0%; transition: width 0.3s ease, background-color 0.3s ease; }
        .strength-label { font-size: 11px; font-weight: 600; letter-spacing: 0.5px; }

        /* Terms */
        .terms-group { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 20px; }
        .terms-group input { margin-top: 2px; accent-color: var(--pink); min-width: 15px; height: 15px; cursor: pointer; transition: transform 0.2s; }
        .terms-group input:checked { transform: scale(1.1); }
        .terms-text { font-size: 13px; color: var(--muted); line-height: 1.5; }
        .terms-text a { color: var(--pink-light); text-decoration: none; transition: color 0.2s; }
        .terms-text a:hover { color: var(--pink); }

        .btn-primary { 
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, var(--pink), var(--purple));
            border: none; border-radius: 12px;
            color: #fff; font-size: 15px; font-weight: 700;
            cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 24px rgba(25,135,84,0.35);
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
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

        .divider { display: flex; align-items: center; gap: 12px; margin: 22px 0; transition: opacity 0.2s; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); transition: background 0.2s; }
        .divider span { font-size: 12px; color: var(--muted); white-space: nowrap; }

        .auth-footer { text-align: center; font-size: 14px; color: var(--muted); }
        .auth-footer a { color: var(--pink-light); font-weight: 600; text-decoration: none; transition: all 0.2s; position: relative; }
        .auth-footer a::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 2px; background: var(--pink-light); transition: width 0.2s; }
        .auth-footer a:hover::after { width: 100%; }
        .auth-footer a:hover { color: var(--pink); }

        .alert-success { 
            background: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.3);
            border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #86efac;
            margin-bottom: 18px; display: none; align-items: center; gap: 8px;
            animation: slideDown 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .alert-success.show { display: flex; }
        
        .alert-error { 
            background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.35);
            border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #fca5a5;
            margin-bottom: 18px; display: none; align-items: center; gap: 8px;
            animation: slideDown 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .alert-error.show { display: flex; }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .spinner { display: none; width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .btn-primary.loading .btn-text { display: none; }
        .btn-primary.loading .spinner { display: inline-block; }

        /* Improved focus visible state for accessibility */
        .form-control:focus-visible {
            outline: 2px solid var(--pink);
            outline-offset: 2px;
        }

        @media (max-width: 480px) { 
            .form-row { grid-template-columns: 1fr; } 
            .auth-card { padding: 32px 24px; } 
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
                <div class="form-group" id="firstNameGroup">
                    <label class="form-label" for="firstName">First Name</label>
                    <div class="input-wrap">
                        <i class="input-icon fa fa-user"></i>
                        <input type="text" id="firstName" class="form-control" placeholder="Sharon" required>
                    </div>
                    <div class="form-help-text" id="firstNameHelp"></div>
                </div>
                <div class="form-group" id="lastNameGroup">
                    <label class="form-label" for="lastName">Last Name</label>
                    <div class="input-wrap">
                        <i class="input-icon fa fa-user"></i>
                        <input type="text" id="lastName" class="form-control" placeholder="Dela Cruz" required>
                    </div>
                    <div class="form-help-text" id="lastNameHelp"></div>
                </div>
            </div>

            <div class="form-group" id="usernameGroup">
                <label class="form-label" for="signupUsername">Username</label>
                <div class="input-wrap">
                    <i class="input-icon fa fa-at"></i>
                    <input type="text" id="signupUsername" class="form-control" placeholder="Choose a username" required autocomplete="off">
                </div>
                <div class="form-help-text" id="usernameHelp"></div>
            </div>

            <div class="form-group" id="emailGroup">
                <label class="form-label" for="signupEmail">Email Address</label>
                <div class="input-wrap">
                    <i class="input-icon fa fa-envelope"></i>
                    <input type="email" id="signupEmail" class="form-control" placeholder="you@email.com" required>
                </div>
                <div class="form-help-text" id="emailHelp"></div>
            </div>

            <div class="form-group" id="roleGroup">
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

            <div class="form-group" id="passwordGroup">
                <label class="form-label" for="signupPassword">Password</label>
                <div class="input-wrap">
                    <i class="input-icon fa fa-lock"></i>
                    <input type="password" id="signupPassword" class="form-control" placeholder="Create a strong password" required autocomplete="new-password">
                    <button type="button" class="toggle-pw" id="togglePwSignup" aria-label="Toggle password visibility">
                        <i class="fa fa-eye" id="togglePwIconSignup"></i>
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

            <div class="form-group" id="confirmPasswordGroup">
                <label class="form-label" for="confirmPassword">Confirm Password</label>
                <div class="input-wrap">
                    <i class="input-icon fa fa-lock"></i>
                    <input type="password" id="confirmPassword" class="form-control" placeholder="Repeat your password" required autocomplete="new-password">
                </div>
                <div class="form-help-text" id="confirmPasswordHelp"></div>
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
        function getAccounts() {
            const stored = localStorage.getItem('sharonstore_accounts');
            return stored ? JSON.parse(stored) : [];
        }

        // Get form elements
        const firstNameInput = document.getElementById('firstName');
        const lastNameInput = document.getElementById('lastName');
        const usernameInput = document.getElementById('signupUsername');
        const emailInput = document.getElementById('signupEmail');
        const roleSelect = document.getElementById('signupRole');
        const passwordInput = document.getElementById('signupPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const agreeTermsCheckbox = document.getElementById('agreeTerms');

        const firstNameGroup = document.getElementById('firstNameGroup');
        const lastNameGroup = document.getElementById('lastNameGroup');
        const usernameGroup = document.getElementById('usernameGroup');
        const emailGroup = document.getElementById('emailGroup');
        const passwordGroup = document.getElementById('passwordGroup');
        const confirmPasswordGroup = document.getElementById('confirmPasswordGroup');

        const firstNameHelp = document.getElementById('firstNameHelp');
        const lastNameHelp = document.getElementById('lastNameHelp');
        const usernameHelp = document.getElementById('usernameHelp');
        const emailHelp = document.getElementById('emailHelp');
        const passwordHelp = document.getElementById('passwordHelp');
        const confirmPasswordHelp = document.getElementById('confirmPasswordHelp');

        // Validation functions
        function validateFirstName() {
            const value = firstNameInput.value.trim();
            if (!value) {
                firstNameGroup.classList.add('has-error');
                firstNameHelp.textContent = 'First name is required';
                return false;
            }
            if (value.length < 2) {
                firstNameGroup.classList.add('has-error');
                firstNameHelp.textContent = 'First name must be at least 2 characters';
                return false;
            }
            firstNameGroup.classList.remove('has-error');
            firstNameHelp.textContent = '';
            return true;
        }

        function validateLastName() {
            const value = lastNameInput.value.trim();
            if (!value) {
                lastNameGroup.classList.add('has-error');
                lastNameHelp.textContent = 'Last name is required';
                return false;
            }
            if (value.length < 2) {
                lastNameGroup.classList.add('has-error');
                lastNameHelp.textContent = 'Last name must be at least 2 characters';
                return false;
            }
            lastNameGroup.classList.remove('has-error');
            lastNameHelp.textContent = '';
            return true;
        }

        function validateUsername() {
            const value = usernameInput.value.trim();
            if (!value) {
                usernameGroup.classList.add('has-error');
                usernameHelp.textContent = 'Username is required';
                return false;
            }
            if (value.length < 3) {
                usernameGroup.classList.add('has-error');
                usernameHelp.textContent = 'Username must be at least 3 characters';
                return false;
            }
            const accounts = getAccounts();
            if (accounts.find(a => a.username === value)) {
                usernameGroup.classList.add('has-error');
                usernameHelp.textContent = 'Username is already taken';
                return false;
            }
            usernameGroup.classList.remove('has-error');
            usernameHelp.textContent = '';
            return true;
        }

        function validateEmail() {
            const value = emailInput.value.trim();
            if (!value) {
                emailGroup.classList.add('has-error');
                emailHelp.textContent = 'Email is required';
                return false;
            }
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                emailGroup.classList.add('has-error');
                emailHelp.textContent = 'Please enter a valid email address';
                return false;
            }
            emailGroup.classList.remove('has-error');
            emailHelp.textContent = '';
            return true;
        }

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

            if (pwd.length < 6) {
                passwordHelp.textContent = 'Minimum 6 characters required';
                passwordGroup.classList.add('has-error');
            } else {
                passwordGroup.classList.remove('has-error');
                passwordHelp.textContent = '';
            }
        }

        function validateConfirmPassword() {
            const pwd = passwordInput.value;
            const confirm = confirmPasswordInput.value;
            
            if (!confirm) {
                confirmPasswordGroup.classList.add('has-error');
                confirmPasswordHelp.textContent = 'Please confirm your password';
                return false;
            }
            if (pwd !== confirm) {
                confirmPasswordGroup.classList.add('has-error');
                confirmPasswordHelp.textContent = 'Passwords do not match';
                return false;
            }
            confirmPasswordGroup.classList.remove('has-error');
            confirmPasswordHelp.textContent = '';
            return true;
        }

        // Event listeners for validation
        firstNameInput.addEventListener('blur', validateFirstName);
        firstNameInput.addEventListener('focus', () => {
            firstNameGroup.classList.remove('has-error');
            firstNameHelp.textContent = '';
        });

        lastNameInput.addEventListener('blur', validateLastName);
        lastNameInput.addEventListener('focus', () => {
            lastNameGroup.classList.remove('has-error');
            lastNameHelp.textContent = '';
        });

        usernameInput.addEventListener('blur', validateUsername);
        usernameInput.addEventListener('focus', () => {
            usernameGroup.classList.remove('has-error');
            usernameHelp.textContent = '';
        });

        emailInput.addEventListener('blur', validateEmail);
        emailInput.addEventListener('focus', () => {
            emailGroup.classList.remove('has-error');
            emailHelp.textContent = '';
        });

        passwordInput.addEventListener('input', function() {
            updatePasswordStrength(this.value);
            if (confirmPasswordInput.value) validateConfirmPassword();
        });

        passwordInput.addEventListener('blur', function() {
            if (!this.value) {
                passwordGroup.classList.add('has-error');
                passwordHelp.textContent = 'Password is required';
            }
        });

        confirmPasswordInput.addEventListener('input', validateConfirmPassword);
        confirmPasswordInput.addEventListener('blur', validateConfirmPassword);

        // Toggle password visibility
        document.getElementById('togglePwSignup').addEventListener('click', function(e) {
            e.preventDefault();
            const pw = document.getElementById('signupPassword');
            const icon = document.getElementById('togglePwIconSignup');
            if (pw.type === 'password') { 
                pw.type = 'text'; 
                icon.className = 'fa fa-eye-slash'; 
            }
            else { 
                pw.type = 'password'; 
                icon.className = 'fa fa-eye'; 
            }
        });

        // Form submit
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('signupBtn');
            const errorEl = document.getElementById('errorAlert');
            const errorMsg = document.getElementById('errorMsg');

            // Validate all fields
            if (!validateFirstName() | !validateLastName() | !validateUsername() | !validateEmail() | !validateConfirmPassword()) {
                return;
            }

            const password = passwordInput.value;
            if (password.length < 6) {
                showError('Password must be at least 6 characters.');
                return;
            }

            if (!agreeTermsCheckbox.checked) {
                showError('Please agree to the Terms of Service.');
                return;
            }

            btn.classList.add('loading');
            btn.disabled = true;

            setTimeout(() => {
                const firstName = firstNameInput.value.trim();
                const lastName = lastNameInput.value.trim();
                const username = usernameInput.value.trim();
                const email = emailInput.value.trim();
                const role = roleSelect.value;

                // Save new account
                const accounts = getAccounts();
                accounts.push({ username, password, role, fullName: `${firstName} ${lastName}`, email });
                localStorage.setItem('sharonstore_accounts', JSON.stringify(accounts));

                btn.classList.remove('loading');
                btn.disabled = false;
                errorEl.classList.remove('show');
                document.getElementById('successAlert').classList.add('show');

                // Smooth fade to login
                document.body.style.opacity = '0.9';
                setTimeout(() => { window.location.href = 'login.php'; }, 1500);
            }, 1200);
        });

        function showError(msg) {
            const el = document.getElementById('errorAlert');
            document.getElementById('errorMsg').textContent = msg;
            el.classList.add('show');
            setTimeout(() => el.classList.remove('show'), 4000);
        }

        // Redirect if already logged in
        const session = localStorage.getItem('sharonstore_session');
        if (session && JSON.parse(session).loggedIn) { 
            window.location.href = 'dashboard.php'; 
        }
    </script>
    
</body>
</html>
