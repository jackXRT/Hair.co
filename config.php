<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'empress_hair');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

// Email Configuration - YOUR ADMIN EMAIL HERE
define('EMAIL_HOST', 'smtp.gmail.com');
define('EMAIL_USER', 'empresso.008@gmail.com');
define('EMAIL_PASS', 'your-app-password'); // Gmail app password
define('EMAIL_FROM', 'no-reply@empresshair.com');
define('ADMIN_EMAIL', 'empresso.008@gmail.com'); // Admin notifications

// Security
define('SECRET_KEY', 'generate-strong-key-here');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);