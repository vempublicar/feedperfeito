<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/User.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($email) || empty($password)) {
        $error = "Por favor, preencha todos os campos.";
    } else {
        $userModel = new User();
        $authResponse = $userModel->loginUser($email, $password);
        
        if ($authResponse && isset($authResponse['access_token'])) {
            // Login successful, get user details from public.users table
            $userProfile = $userModel->find($authResponse['user']['id']);

            if ($userProfile) {
                $sessionData = [
                    'id' => $userProfile['id'],
                    'email' => $authResponse['user']['email'],
                    'name' => $userProfile['name'],
                    'credits' => $userProfile['credits'],
                    'role' => $userProfile['role'],
                    'phone' => $userProfile['phone'],
                    'access_token' => $authResponse['access_token'],
                    'refresh_token' => $authResponse['refresh_token']
                ];
                

                // Redirect based on user role
                if ($userProfile['role'] === 'admin') {
                    setAdminSession($sessionData);
                    header('Location: ' . $_SESSION['base_url'] . '/admin');
                } else {
                    $_SESSION['dados_usuario'] = $userProfile;
                    setUserSession($sessionData);
                    header('Location: ' . $_SESSION['base_url'] . '/dashboard');
                }
                exit();
            } else {
                $error = "Perfil de usuário não encontrado.";
            }
        } elseif ($authResponse && isset($authResponse['msg'])) {
            $error = $authResponse['msg'];
        } else {
            $error = "Credenciais inválidas.";
        }
    }
    
    // If we get here, there was an error
    // Store error in session and redirect back to login
    $_SESSION['login_error'] = $error;
    // header('Location: ' . $_SESSION['base_url'] . '/login');
    exit();
} else {
    // Not a POST request, redirect to login page
    // header('Location: ' . $_SESSION['base_url'] . '/login');
    exit();
}
?>