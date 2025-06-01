<?php
session_start();
require 'db.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function loginUser($username, $password) {
    $db = new Database();
    $username = $db->escape($username);
    
    $result = $db->query("SELECT * FROM users WHERE username = '$username' LIMIT 1");
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }
    }
    return false;
}