<?php
require_once 'includes/config.php';

try {
    // Create admin user with proper password hash
    $username = 'admin';
    $email = 'admin@example.com';
    $password = 'admin123';
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // First check if admin exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin) {
        // Update existing admin
        $stmt = $pdo->prepare("
            UPDATE users 
            SET password = ?, 
                email = ?,
                is_admin = 1,
                status = 'active'
            WHERE username = ?
        ");
        $stmt->execute([$password_hash, $email, $username]);
        echo "Admin user updated successfully!";
    } else {
        // Create new admin
        $stmt = $pdo->prepare("
            INSERT INTO users (
                username, 
                email, 
                password, 
                is_admin,
                status,
                referral_code
            ) VALUES (?, ?, ?, 1, 'active', 'ADMIN123')
        ");
        $stmt->execute([$username, $email, $password_hash]);
        echo "Admin user created successfully!";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
} 