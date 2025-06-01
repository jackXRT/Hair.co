public function sendPasswordResetEmail($email, $resetLink) {
    try {
        $this->mail->clearAddresses();
        $this->mail->addAddress($email);
        $this->mail->Subject = 'Password Reset Request';
        
        $message = file_get_contents('../templates/password-reset-email.html');
        $message = str_replace('{{reset_link}}', $resetLink, $message);
        
        $this->mail->Body = $message;
        $this->mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Password Reset Email Error: ".$this->mail->ErrorInfo);
        return false;
    }
}
// Add to existing Mailer class

private function fetchOrderDetails($orderId) {
    $db = new Database();
    $result = $db->query("SELECT * FROM orders WHERE id = $orderId");
    
    if ($result->num_rows === 0) return null;
    
    $order = $result->fetch_assoc();
    $order['order_date'] = date('F j, Y', strtotime($order['created_at']));
    
    // Decode and format items
    $items = json_decode($order['items'], true);
    $orderItems = [];
    $total = 0;
    
    foreach ($items as $item) {
        $itemTotal = $item['price'] * $item['quantity'];
        $orderItems[] = [
            'name' => $item['name'],
            'price' => number_format($item['price'], 2),
            'quantity' => $item['quantity'],
            'item_total' => number_format($itemTotal, 2)
        ];
        $total += $itemTotal;
    }
    
    $order['order_items'] = $orderItems;
    $order['order_total'] = number_format($total, 2);
    
    return $order;
}

public function sendOrderNotification($orderId, $customerEmail) {
    $order = $this->fetchOrderDetails($orderId);
    if (!$order) return false;

    try {
        $this->mail->addAddress($customerEmail);
        $this->mail->Subject = 'Your Order #'.$orderId;
        
        $template = file_get_contents('../templates/order-email.html');
        $replacements = [
            '{{customer_name}}' => $order['customer_name'],
            '{{order_id}}' => $orderId,
            '{{order_date}}' => $order['order_date'],
            '{{order_total}}' => $order['order_total'],
            '{{current_year}}' => date('Y'),
            '{{order_items}}' => ''
        ];
        
        // Build items HTML
        $itemsHtml = '';
        foreach ($order['order_items'] as $item) {
            $itemsHtml .= "<tr>
                <td>{$item['name']}</td>
                <td>\${$item['price']}</td>
                <td>{$item['quantity']}</td>
                <td>\${$item['item_total']}</td>
            </tr>";
        }
        
        $template = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );
        
        $template = str_replace('{{#order_items}}', $itemsHtml, $template);
        $template = str_replace('{{/order_items}}', '', $template);
        
        $this->mail->Body = $template;
        $this->mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Order Email Error: ".$this->mail->ErrorInfo);
        return false;
    }
}

public function sendAdminNotification($orderId, $customerEmail) {
    $order = $this->fetchOrderDetails($orderId);
    if (!$order) return false;

    try {
        $this->mail->clearAddresses();
        $this->mail->addAddress(ADMIN_EMAIL);
        $this->mail->Subject = 'New Order #'.$orderId;
        
        $template = file_get_contents('../templates/admin-notification.html');
        $replacements = [
            '{{order_id}}' => $orderId,
            '{{customer_name}}' => $order['customer_name'],
            '{{customer_email}}' => $customerEmail,
            '{{order_total}}' => $order['order_total'],
            '{{order_date}}' => $order['order_date'],
            '{{current_time}}' => date('Y-m-d H:i:s'),
            '{{order_items}}' => ''
        ];
        
        // Build items list
        $itemsList = '';
        foreach ($order['order_items'] as $item) {
            $itemsList .= "<li>{$item['quantity']} x {$item['name']} - \${$item['item_total']}</li>";
        }
        
        $template = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );
        
        $template = str_replace('{{#order_items}}', $itemsList, $template);
        $template = str_replace('{{/order_items}}', '', $template);
        
        $this->mail->Body = $template;
        $this->mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Admin Email Error: ".$this->mail->ErrorInfo);
        return false;
    }
}