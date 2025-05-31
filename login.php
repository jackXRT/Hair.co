<?php
require '../includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (loginUser($_POST['username'], $_POST['password'])) {
        header('Location: index.php');
        exit();
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container" style="grid-template-columns: 1fr">
        <main class="content">
            <h1>Admin Login</h1>
            <?php if (isset($error)): ?>
                <div style="color: red; margin: 1rem 0"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" class="admin-form">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
                
                <!-- ADDED PASSWORD RESET LINK -->
                <p style="margin-top: 1rem">
                    <a href="forgot-password.php">Forgot Password?</a>
                </p>
                
            </form>
        </main>
    </div>
</body>
</html>