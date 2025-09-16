<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['access_token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $error = '';
    $success = '';

    if (empty($token) || empty($password) || empty($confirmPassword)) {
        $error = "Por favor, preencha todos os campos.";
    } elseif ($password !== $confirmPassword) {
        $error = "As senhas não coincidem.";
    } elseif (strlen($password) < 6) {
        $error = "A senha deve ter pelo menos 6 caracteres.";
    } else {
        $userModel = new User();
        try {
            $resetResponse = $userModel->resetPassword($token, $password);
            if ($resetResponse === true) {
                $success = "Sua senha foi redefinida com sucesso. Faça login com a nova senha.";
                $_SESSION['login_success'] = $success;
                header('Location: ' . $_SESSION['base_url'] . '/login');
                exit();
            } else {
                $error = "Erro inesperado ao redefinir a senha.";
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    if (!empty($error)) {
        $_SESSION['reset_error'] = $error;
    } elseif (!empty($success)) {
        $_SESSION['reset_success'] = $success;
    }
    
    header('Location: ' . $_SESSION['base_url'] . '/reset-password');
    exit();
} else {
    header('Location: ' . $_SESSION['base_url'] . '/login');
    exit();
}
?>