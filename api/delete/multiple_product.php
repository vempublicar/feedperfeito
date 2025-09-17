<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/database.php';
require_once '../../models/MultipleProduct.php'; // Alterado para MultipleProduct.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Para lidar com o method spoofing (DELETE via POST)
    $method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

    if ($method === 'DELETE') {
        // Obter o ID do produto dos dados POST
        $id = $_POST['id'] ?? null;

        if (!$id) {
            $_SESSION['status_type'] = 'error';
            $_SESSION['status_message'] = 'ID do produto não fornecido.';
            header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo'); // Alterado para produtos-multiplo
            exit();
        }

        $multipleProduct = new MultipleProduct(); // Alterado para MultipleProduct
        
        // Tenta excluir o produto
        if ($multipleProduct->delete($id)) { // Alterado para $multipleProduct->delete
            $_SESSION['status_type'] = 'success';
            $_SESSION['status_message'] = 'Produto Múltiplo excluído com sucesso!'; // Alterado para Produto Múltiplo
            header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo'); // Alterado para produtos-multiplo
            exit();
        } else {
            $_SESSION['status_type'] = 'error';
            $_SESSION['status_message'] = 'Erro ao excluir o Produto Múltiplo.'; // Alterado para Produto Múltiplo
            header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo'); // Alterado para produtos-multiplo
            exit();
        }
    } else {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Método não permitido.';
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo'); // Alterado para produtos-multiplo
        exit();
    }
} else {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Método não permitido.';
    header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo'); // Alterado para produtos-multiplo
    exit();
}
?>