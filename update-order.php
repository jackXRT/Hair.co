<?php
require '../includes/auth.php';
require '../includes/db.php';

if (!isAdmin()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

// Get input data
$data = json_decode(file_get_contents('php://input'), true);
$orderId = intval($data['order_id'] ?? 0);
$status = $data['status'] ?? '';

// Validate status
$allowedStatuses = ['pending', 'completed', 'cancelled'];
if (!in_array($status, $allowedStatuses)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

// Update database
$db = new Database();
$db->query("UPDATE orders SET status = '$status' WHERE id = $orderId");

if ($db->connection->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}