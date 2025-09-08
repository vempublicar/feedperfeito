<?php
require_once 'config/database.php';
require_once 'models/AdminUser.php';

echo "Creating admin user...\n";

// Admin user credentials
$email = 'vempublicar@gmail.com';
$password = 'Luis**251251';
$name = 'Admin User';
$role = 'admin';

// Create admin user
$adminUser = new AdminUser();

// Check if admin user already exists
$existingAdmin = $adminUser->findByEmail($email);
if ($existingAdmin) {
    echo "Admin user with email $email already exists\n";
    echo "Admin user ID: {$existingAdmin['id']}\n";
    echo "You can now log in with:\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
} else {
    echo "Creating new admin user...\n";
    
    // Create new admin user with hashed password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $data = [
        'name' => $name,
        'email' => $email,
        'password' => $hashedPassword,
        'role' => $role,
        'is_active' => true,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    try {
        $result = $adminUser->create($data);
        if ($result && isset($result[0])) {
            echo "Admin user created successfully\n";
            echo "Admin user ID: {$result[0]['id']}\n";
            echo "You can now log in with:\n";
            echo "Email: $email\n";
            echo "Password: $password\n";
        } else {
            echo "Failed to create admin user\n";
            echo "Result: " . print_r($result, true) . "\n";
        }
    } catch (Exception $e) {
        echo "Error creating admin user: " . $e->getMessage() . "\n";
    }
}

echo "Admin user setup completed.\n";
?>