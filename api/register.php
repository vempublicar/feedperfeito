<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/User.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $terms = $_POST['terms'] ?? false;
    
    // Validate input
    if (empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "Por favor, preencha todos os campos.";
    } elseif ($password !== $confirmPassword) {
        $error = "As senhas não coincidem.";
    } elseif (strlen($password) < 6) {
        $error = "A senha deve ter pelo menos 6 caracteres.";
    } elseif (!$terms) {
        $error = "Você deve concordar com os Termos e Condições.";
    } else {
        $userModel = new User();
        
        // Register user with Supabase Auth
        $authResponse = $userModel->registerUser($email, $password);
        if ($authResponse && isset($authResponse['user'])) {
            $supabaseUser = $authResponse['user'];
            
            // Registration successful, set session
            // User profile in public.profiles table will be created by a Supabase trigger
            $sessionData = [
                'id' => $supabaseUser['id'],
                'email' => $supabaseUser['email'],
                'name' => $supabaseUser['email'], // Use email as name or fetch from profile table later
                'credits' => 0, // Initial credits, if applicable
                'role' => 'user', // Default role, will be set by trigger
                'access_token' => $authResponse['access_token'],
                'refresh_token' => $authResponse['refresh_token']
            ];
            setUserSession($sessionData);

            $success = "Conta criada com sucesso! Você pode fazer login agora.";
            $_SESSION['register_success'] = $success;
            header('Location: ' . $_SESSION['base_url'] . '/dashboard.php');
            exit();
        } elseif ($authResponse && isset($authResponse['msg'])) {
            $error = $authResponse['msg'];
        } else {
            $error = "Erro ao registrar. Por favor, tente novamente.";
        }
    }
    
    // If we get here, there was an error
    // Store error in session and redirect back to registration
    $_SESSION['register_error'] = $error;
    header('Location: ' . $_SESSION['base_url'] . '/register.php'); // Redirect back to register page with error
    exit();
} else {
    // Not a POST request, redirect to registration page
    header('Location: ' . $_SESSION['base_url'] . '/register.php');
    exit();
}
?>
