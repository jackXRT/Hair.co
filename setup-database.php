<?php
require 'config.php';
require 'db.php';

function createTables($db) {
    // Create orders table
    $db->query("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(100) NOT NULL,
        customer_email VARCHAR(100) NOT NULL,
        items JSON NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        status ENUM('pending','completed','cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Create users table
    $db->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin','staff') DEFAULT 'staff',
        last_login TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create products table
    $db->query("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        price DECIMAL(10,2) NOT NULL,
        stock INT NOT NULL DEFAULT 0,
        description TEXT,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Create password resets table
    $db->query("CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL,
        token VARCHAR(100) NOT NULL,
        expires DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Insert sample products if none exist
    $result = $db->query("SELECT COUNT(*) as count FROM products");
    $row = $result->fetch_assoc();
    if ($row['count'] == 0) {
        $db->query("INSERT INTO products 
            (name, slug, price, stock, description) VALUES
            ('Hair Extensions', 'hair-extensions', 99.99, 50, 'Premium quality 100% human hair'),
            ('Shampoo', 'shampoo', 24.99, 100, 'Sulfate-free formula')
        ");
    }
}

try {
    $db = new Database();
    createTables($db);
    
    // Create admin user if provided (by installer)
    if (isset($_POST['admin'])) {
        $adminEmail = 'empresso.008@gmail.com'; // Fixed admin email
        $password = $_POST['admin']['password'];
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        
        $db->query("REPLACE INTO users (email, password, role) VALUES (
            '".$db->escape($adminEmail)."',
            '".$hashed."',
            'admin'
        )");
        
        // Also create a default staff account
        $staffEmail = 'staff@empressohair.com';
        $staffPass = password_hash('StaffPass123!', PASSWORD_BCRYPT);
        $db->query("REPLACE INTO users (email, password, role) VALUES (
            '".$db->escape($staffEmail)."',
            '".$staffPass."',
            'staff'
        )");
    }
    
} catch (Exception $e) {
    die("Database setup failed: " . $e->getMessage());
}