<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/database.php';
require_once '../../models/FeedProduct.php'; // Alterado para FeedProduct.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Para lidar com o method spoofing (DELETE via POST)
    $method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

    if ($method === 'DELETE') {
        // Obter o ID do produto dos dados POST
        $id = $_POST['id'] ?? null;

        if (!$id) {
            $_SESSION['status_type'] = 'error';
            $_SESSION['status_message'] = 'ID do produto não fornecido.';
            header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-feed'); // Alterado para produtos-feed
            exit();
        }

        $feedProduct = new FeedProduct(); // Alterado para FeedProduct
        
        // Tenta excluir o produto
        if ($feedProduct->delete($id)) { // Alterado para $feedProduct->delete
            $_SESSION['status_type'] = 'success';
            $_SESSION['status_message'] = 'Produto Feed excluído com sucesso!'; // Alterado para Produto Feed
            header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-feed'); // Alterado para produtos-feed
            exit();
        } else {
            $_SESSION['status_type'] = 'error';
            $_SESSION['status_message'] = 'Erro ao excluir o Produto Feed.'; // Alterado para Produto Feed
            header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-feed'); // Alterado para produtos-feed
            exit();
        }
    } else {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Método não permitido.';
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-feed'); // Alterado para produtos-feed
        exit();
    }
} else {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Método não permitido.';
    header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-feed'); // Alterado para produtos-feed
    exit();
}
?>