<?php
session_start();
require 'db_connect.php';

// Log the logout action to the audit trail if a user was logged in
if (isset($_SESSION['UserID'])) {
    try {
        $log_stmt = $pdo->prepare("INSERT INTO tbl_audit_logs (UserID, Action_Performed) VALUES (?, 'User logged out')");
        $log_stmt->execute([$_SESSION['UserID']]);
    } catch (PDOException $e) {
        // Silently continue if audit log fails during logout
    }
}

// Securely wipe all PHP session data
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logging out...</title>
</head>
<body>
    <script>
        // Clear the frontend JavaScript session data used by your app.js
        localStorage.removeItem('sharonstore_session');
        
        // Redirect back to the login page
        window.location.href = 'login.php';
    </script>
</body>
</html>