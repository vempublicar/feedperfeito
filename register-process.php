<?php
require_once 'config/session.php';
require_once 'models/User.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $terms = $_POST['terms'] ?? false;
    
    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "Por favor, preencha todos os campos.";
    } elseif ($password !== $confirmPassword) {
        $error = "As senhas não coincidem.";
    } elseif (strlen($password) < 6) {
        $error = "A senha deve ter pelo menos 6 caracteres.";
    } elseif (!$terms) {
        $error = "Você deve concordar com os Termos e Condições.";
    } else {
        // Check if user already exists
        $userModel = new User();
        $existingUser = $userModel->findByEmail($email);
        
        if ($existingUser) {
            $error = "Já existe uma conta com este email.";
        } else {
            // Create new user
            $result = $userModel->createUser($name, $email, $password);
            
            if ($result && isset($result[0])) {
                // Registration successful
                $success = "Conta criada com sucesso! Você pode fazer login agora.";
                $_SESSION['register_success'] = $success;
                header('Location: /login');
                exit();
            } else {
                $error = "Erro ao criar conta. Por favor, tente novamente.";
            }
        }
    }
    print_r($result);
    // If we get here, there was an error
    // Store error in session and redirect back to registration
    $_SESSION['register_error'] = $error;
    // header('Location: /register');
    exit();
} else {
    // Not a POST request, redirect to registration page
    // header('Location: /register');
    exit();
}
?>
