<?php
require '../includes/auth.php';
require '../includes/db.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$db = new Database();
$orders = $db->query("SELECT * FROM orders ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Management</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script>
    function updateOrderStatus(orderId, status) {
        fetch('update-order.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({order_id: orderId, status: status})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`status-${orderId}`).textContent = 
                    status.charAt(0).toUpperCase() + status.slice(1);
                document.getElementById(`status-${orderId}`).className = 
                    'status-badge ' + status;
            } else {
                alert('Update failed: ' + data.message);
            }
        });
    }
    </script>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <main class="content">
            <h1>Order Management</h1>
            
            <div class="admin-form" style="margin-bottom: 2rem">
                <h2>All Orders</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                            <td><?= htmlspecialchars($order['customer_email']) ?></td>
                            <td>$<?= number_format($order['total'], 2) ?></td>
                            <td>
                                <span id="status-<?= $order['id'] ?>" 
                                      class="status-badge <?= $order['status'] ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                            <td>
                                <select onchange="updateOrderStatus(<?= $order['id'] ?>, this.value)" 
                                        style="padding: 0.3rem">
                                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                                <a href="order-details.php?id=<?= $order['id'] ?>" class="action-link">View</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>