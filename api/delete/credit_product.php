<?php
require_once '../../config/database.php';
require_once '../../models/CreditPackage.php';

// Garante que a sessão seja iniciada para usar $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/database.php';
require_once '../../models/CreditPackage.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Para lidar com o method spoofing (DELETE via POST)
    $method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

    if ($method === 'DELETE') {
        // Obter o ID do produto dos dados POST
        $id = $_POST['id'] ?? null;

        if (!$id) {
            $_SESSION['status_type'] = 'error';
            $_SESSION['status_message'] = 'ID do produto não fornecido.';
            header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-credito');
            exit();
        }

        $creditPackage = new CreditPackage();
        
        // Tenta excluir o produto
        if ($creditPackage->delete($id)) {
            $_SESSION['status_type'] = 'success';
            $_SESSION['status_message'] = 'Produto de crédito excluído com sucesso!';
            header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-credito');
            exit();
        } else {
            $_SESSION['status_type'] = 'error';
            $_SESSION['status_message'] = 'Erro ao excluir o produto de crédito.';
            header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-credito');
            exit();
        }
    } else {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Método não permitido.';
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-credito');
        exit();
    }
} else {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Método não permitido.';
    header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-credito');
    exit();
}
?>