<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up – Sharon Store</title>
    <meta name="description" content="Create your Sharon Store management account to get started.">
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
                    <select id="signupRole" class="form-control select-with-icon">
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
