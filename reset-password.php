<?php
require '../includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$token = $_GET['token'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../includes/password-reset.php';
    
    $reset = new PasswordReset();
    if ($reset->resetPassword($_POST['token'], $_POST['password'])) {
        header('Location: login.php?reset=success');
        exit();
    } else {
        $error = 'Invalid or expired token';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container" style="grid-template-columns: 1fr">
        <main class="content">
            <h1>Reset Password</h1>
            <?php if ($error): ?>
                <div style="color: var(--admin-danger); margin: 1rem 0"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" class="admin-form">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" class="form-control" required
                           minlength="8">
                </div>
                
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </form>
        </main>
    </div>
</body>
</html>