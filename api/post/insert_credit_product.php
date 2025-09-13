<?php
// Garante que a sessão seja iniciada para usar $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/database.php';
require_once '../../models/CreditPackage.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Para formulários HTML, os dados vêm de $_POST
    $data = $_POST;

    // Validação básica dos dados
    if (!isset($data['title']) || !isset($data['credits']) || !isset($data['price'])) {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Dados incompletos. Título, créditos e preço são obrigatórios.';
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-credito');
        exit();
    }

    $creditPackage = new CreditPackage();
    
    $productData = [
        'title' => $data['title'],
        'credits' => $data['credits'],
        'bonus_credits' => $data['bonus_credits'] ?? 0,
        'price' => $data['price'],
        'tag' => $data['tag'] ?? null,
        'link' => $data['link'] ?? null,
        'is_active' => isset($data['is_active']) && $data['is_active'] === 'on' ? 1 : 0
    ];

    if ($creditPackage->create($productData)) {
        $_SESSION['status_type'] = 'success';
        $_SESSION['status_message'] = 'Produto de crédito criado com sucesso!';
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-credito');
        exit();
    } else {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Erro ao criar o produto de crédito.';
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