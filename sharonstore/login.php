<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Sharon Store</title>
    <meta name="description" content="Sign in to Sharon Store management system to manage inventory, sales, and restocking.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
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
            <div class="form-group" id="usernameGroup">
                <label class="form-label" for="loginUsername">Username</label>
                <div class="input-wrap">
                    <i class="input-icon fa fa-user"></i>
                    <input type="text" id="loginUsername" class="form-control" placeholder="Enter your username" autocomplete="username" required>
                </div>
                <div class="form-help-text" id="usernameHelp"></div>
            </div>

            <div class="form-group" id="passwordGroup">
                <label class="form-label" for="loginPassword">Password</label>
                <div class="input-wrap">
                    <i class="input-icon fa fa-lock"></i>
                    <input type="password" id="loginPassword" class="form-control" placeholder="Enter your password" autocomplete="current-password" required>
                    <button type="button" class="toggle-pw" id="togglePw" aria-label="Toggle password visibility">
                        <i class="fa fa-eye" id="togglePwIcon"></i>
                    </button>
                </div>
                <div class="password-strength" id="passwordStrength">
                    <div class="progress-bars">
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

        // === Real-time validation ===
        const usernameInput = document.getElementById('loginUsername');
        const passwordInput = document.getElementById('loginPassword');
        const usernameGroup = document.getElementById('usernameGroup');
        const passwordGroup = document.getElementById('passwordGroup');
        const usernameHelp = document.getElementById('usernameHelp');
        const passwordHelp = document.getElementById('passwordHelp');
        const accounts = getAccounts();

        // Username validation
        usernameInput.addEventListener('blur', function() {
            const value = this.value.trim();
            if (!value) {
                usernameGroup.classList.remove('has-success');
                usernameGroup.classList.add('has-error');
                usernameHelp.textContent = 'Username is required';
            } else if (value.length < 3) {
                usernameGroup.classList.remove('has-success');
                usernameGroup.classList.add('has-error');
                usernameHelp.textContent = 'Username must be at least 3 characters';
            } else {
                const userExists = accounts.some(a => a.username === value);
                if (userExists) {
                    usernameGroup.classList.add('has-success');
                    usernameGroup.classList.remove('has-error');
                    usernameHelp.textContent = '';
                } else {
                    usernameGroup.classList.remove('has-success');
                    usernameGroup.classList.remove('has-error');
                    usernameHelp.textContent = '';
                }
            }
        });

        usernameInput.addEventListener('focus', function() {
            usernameGroup.classList.remove('has-error', 'has-success');
            usernameHelp.textContent = '';
        });

        usernameInput.addEventListener('input', function() {
            usernameGroup.classList.remove('has-error', 'has-success');
            usernameHelp.textContent = '';
        });

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

            if (pwd.length < 6) {
                passwordHelp.textContent = 'Minimum 6 characters required';
                passwordGroup.classList.add('has-error');
            } else {
                passwordGroup.classList.remove('has-error');
                passwordHelp.textContent = '';
            }
        }

        passwordInput.addEventListener('input', function() {
            updatePasswordStrength(this.value);
        });

        passwordInput.addEventListener('focus', function() {
            if (this.value) updatePasswordStrength(this.value);
        });

        passwordInput.addEventListener('blur', function() {
            if (!this.value) {
                passwordGroup.classList.add('has-error');
                passwordHelp.textContent = 'Password is required';
            }
        });

        // Submit on Enter key in password field
        passwordInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').dispatchEvent(new Event('submit'));
            }
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

        // Checkbox interactions
        const rememberMeCheckbox = document.getElementById('rememberMe');
        rememberMeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                localStorage.setItem('sharonstore_remember', 'true');
            } else {
                localStorage.removeItem('sharonstore_remember');
            }
        });

        // Restore "Remember me" state
        if (localStorage.getItem('sharonstore_remember')) {
            rememberMeCheckbox.checked = true;
        }

        // Login submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            document.getElementById('loginError').classList.remove('show');
            
            // Validate fields
            const username = usernameInput.value.trim();
            const password = passwordInput.value;

            if (!username) {
                usernameGroup.classList.add('has-error');
                usernameHelp.textContent = 'Username is required';
                usernameInput.focus();
                return;
            }
            if (!password) {
                passwordGroup.classList.add('has-error');
                passwordHelp.textContent = 'Password is required';
                passwordInput.focus();
                return;
            }

            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.disabled = true;

            setTimeout(() => {
                const match = accounts.find(a => a.username === username && a.password === password);

                if (match) {
                    // Save session to localStorage
                    localStorage.setItem('sharonstore_session', JSON.stringify({
                        loggedIn: true,
                        username: match.username,
                        fullName: match.fullName,
                        role: match.role
                    }));
                    // Smooth transition to dashboard
                    document.body.style.opacity = '0.9';
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 200);
                } else {
                    btn.classList.remove('loading');
                    btn.disabled = false;
                    const err = document.getElementById('loginError');
                    err.classList.add('show');
                    document.getElementById('loginErrorMsg').textContent = 'Invalid username or password. Try admin / admin123';
                    
                    // Focus back to username
                    usernameInput.focus();
                    
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
