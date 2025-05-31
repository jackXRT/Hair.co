<?php
require '../includes/db.php';
require '../includes/mailer.php';

// Process new orders
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    
    // Get order data
    $customerName = $db->escape($_POST['customer_name']);
    $customerEmail = $db->escape($_POST['customer_email']);
    $items = json_encode($_POST['items']); // Array of product IDs and quantities
    $total = floatval($_POST['total']);
    
    // Save to database
    $db->query("INSERT INTO orders (customer_name, customer_email, items, total)
                VALUES ('$customerName', '$customerEmail', '$items', $total)");
    
    $orderId = $db->getLastId();
    
    // Send notifications
    $mailer = new Mailer();
    $mailer->sendOrderNotification($orderId, $customerEmail);
    $mailer->sendAdminNotification($orderId, $customerEmail);
    
    // Return success response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'order_id' => $orderId,
        'message' => 'Order processed successfully!'
    ]);
    exit();
}

// Handle invalid requests
header('HTTP/1.1 400 Bad Request');
echo 'Invalid request method';