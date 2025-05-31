<?php
require '../includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../includes/password-reset.php';
    
    $reset = new PasswordReset();
    if ($reset->requestReset($_POST['email'])) {
        $message = 'Password reset link sent to your email';
    } else {
        $message = 'Error: Invalid email or system error';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container" style="grid-template-columns: 1fr">
        <main class="content">
            <h1>Forgot Password</h1>
            <?php if ($message): ?>
                <div class="alert"><?= $message ?></div>
            <?php endif; ?>
            <form method="POST" class="admin-form">
                <div class="form-group">
                    <label>Admin Email</label>
                    <input type="email" name="email" class="form-control" required 
                           value="empresso.008@gmail.com">
                </div>
                <button type="submit" class="btn btn-primary">Send Reset Link</button>
                <p style="margin-top: 1rem">
                    <a href="login.php">Back to Login</a>
                </p>
            </form>
        </main>
    </div>
</body>
</html>