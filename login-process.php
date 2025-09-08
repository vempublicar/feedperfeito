<?php
require_once 'config/session.php';
require_once 'models/User.php';
require_once 'models/AdminUser.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $userType = $_POST['user_type'] ?? 'user'; // 'user' or 'admin'
    
    // Validate input
    if (empty($email) || empty($password)) {
        $error = "Por favor, preencha todos os campos.";
    } else {
        if ($userType === 'admin') {
            // Admin login
            $adminUser = new AdminUser();
            $admin = $adminUser->authenticate($email, $password);
            
            if ($admin) {
                // Login successful
                setAdminSession($admin);
                header('Location: /admin');
                exit();
            } else {
                $error = "Credenciais inválidas para administrador.";
            }
        } else {
            // Regular user login
            $userModel = new User();
            $user = $userModel->authenticate($email, $password);
            
            if ($user) {
                // Login successful
                setUserSession($user);
                header('Location: /dashboard');
                exit();
            } else {
                $error = "Credenciais inválidas.";
            }
        }
    }
    
    // If we get here, there was an error
    // Store error in session and redirect back to login
    $_SESSION['login_error'] = $error;
    header('Location: /feedperfeito/login');
    exit();
} else {
    // Not a POST request, redirect to login page
    header('Location: /feedperfeito/login');
    exit();
}
?>