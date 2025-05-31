<?php
require '../includes/auth.php';
require '../includes/db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <main class="content">
            <h1>Dashboard</h1>
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Total Orders</h3>
                    <p>152</p>
                </div>
                <!-- Add more cards -->
            </div>
        </main>
    </div>
</body>
</html>