<?php
// install/index.php

// Check if already installed
if (file_exists('../installed.lock') && !isset($_GET['force'])) {
    die('System already installed. <a href="../admin/">Go to Admin</a>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create config file
        $configContent = "<?php\n"
        . "// Database Configuration\n"
        . "define('DB_HOST', '" . addslashes($_POST['db']['host']) . "');\n"
        . "define('DB_NAME', '" . addslashes($_POST['db']['name']) . "');\n"
        . "define('DB_USER', '" . addslashes($_POST['db']['user']) . "');\n"
        . "define('DB_PASS', '" . addslashes($_POST['db']['pass']) . "');\n\n"
        
        // Add email config (using your provided admin email)
        . "// Email Configuration\n"
        . "define('EMAIL_HOST', 'smtp.gmail.com');\n"
        . "define('EMAIL_USER', 'empresso.008@gmail.com');\n"
        . "define('EMAIL_PASS', '" . addslashes($_POST['email']['pass']) . "');\n"
        . "define('EMAIL_FROM', 'no-reply@empresshair.com');\n"
        . "define('ADMIN_EMAIL', 'empresso.008@gmail.com');\n\n"
        
        // Security
        . "// Security\n"
        . "define('SECRET_KEY', '" . bin2hex(random_bytes(32)) . "');\n\n"
        
        // Error reporting
        . "// Error reporting (disable in production)\n"
        . "error_reporting(E_ALL);\n"
        . "ini_set('display_errors', 1);";
        
        file_put_contents('../includes/config.php', $configContent);
        
        // Create database
        require '../includes/setup-database.php';
        
        // Create installed lock
        file_put_contents('../installed.lock', 'DO NOT DELETE - ' . date('Y-m-d H:i:s'));
        
        header('Location: ../admin/login.php');
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Empress Hair Installation</title>
    <style>
        /* ... keep your existing styles ... */
        .note { color: #666; font-size: 0.9rem; }
    </style>
</head>
<body>
    <h1>Empress Hair Installation</h1>
    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <h2>Database Settings</h2>
        <div class="form-group">
            <label>Host</label>
            <input type="text" name="db[host]" value="localhost" required>
        </div>
        <div class="form-group">
            <label>Database Name</label>
            <input type="text" name="db[name]" value="empress_hair" required>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="db[user]" value="root" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="db[pass]">
        </div>
        
        <h2>Email Configuration</h2>
        <div class="form-group">
            <label>SMTP Password</label>
            <input type="password" name="email[pass]" required>
            <p class="note">For empresso.008@gmail.com (Gmail app password)</p>
        </div>
        
        <h2>Admin Account</h2>
        <div class="form-group">
            <label>Admin Email</label>
            <input type="email" value="empresso.008@gmail.com" readonly class="form-control">
            <p class="note">This is your primary admin account</p>
        </div>
        <div class="form-group">
            <label>Admin Password</label>
            <input type="password" name="admin[password]" required minlength="8">
        </div>
        
        <button type="submit" style="padding: 0.7rem 1.5rem;">Install</button>
    </form>
</body>
</html>