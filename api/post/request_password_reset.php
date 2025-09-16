<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $error = '';
    $success = '';

    if (empty($email)) {
        $error = "Por favor, preencha o campo de e-mail.";
    } else {
        $userModel = new User();
        $resetResponse = $userModel->requestPasswordReset($email);

        if ($resetResponse === true) {
            $success = "Se as informações estiverem corretas, um link para redefinir sua senha foi enviado para o seu e-mail.";
        } elseif (is_string($resetResponse)) {
            $error = $resetResponse; // Supabase error message
        } else {
            $error = "Erro ao solicitar a redefinição de senha. Por favor, tente novamente.";
        }
    }

    if (!empty($error)) {
        $_SESSION['reset_error'] = $error;
    } elseif (!empty($success)) {
        $_SESSION['reset_success'] = $success;
    }
    
    header('Location: ../../forgot-password.php');
    exit();
} else {
    header('Location: ../../forgot-password.php');
    exit();
}
?>