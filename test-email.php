<?php
require 'includes/mailer.php';
$mailer = new Mailer();
$mailer->sendOrderNotification('TEST123', 'brunodreams222@gmail.com');
echo "Test email sent!";