<?php
require 'config.php';
require 'db.php';
require 'mailer.php';

class PasswordReset {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function requestReset($email) {
        $email = $this->db->escape($email);
        $result = $this->db->query("SELECT id FROM users WHERE username = '$email'");
        
        if ($result->num_rows === 0) return false;
        
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->db->query("DELETE FROM password_resets WHERE email = '$email'");
        $this->db->query("INSERT INTO password_resets (email, token, expires) 
                          VALUES ('$email', '$token', '$expires')");
        
        $resetLink = "https://yourdomain.com/admin/reset-password.php?token=$token";
        
        // Send reset email
        $mailer = new Mailer();
        return $mailer->sendPasswordResetEmail($email, $resetLink);
    }
    
    public function resetPassword($token, $newPassword) {
        $token = $this->db->escape($token);
        $result = $this->db->query("SELECT * FROM password_resets 
                                    WHERE token = '$token' AND expires > NOW()");
        
        if ($result->num_rows === 0) return false;
        
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        
        $this->db->query("UPDATE users SET password = '$hashedPassword' 
                          WHERE username = '$email'");
        
        $this->db->query("DELETE FROM password_resets WHERE token = '$token'");
        return true;
    }
}