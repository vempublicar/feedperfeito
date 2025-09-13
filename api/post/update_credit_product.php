<?php
// Garante que a sessão seja iniciada para usar $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/database.php';
require_once '../../models/CreditPackage.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Para lidar com o method spoofing (PUT via POST)
    $method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

    // Dados vêm de $_POST para PUT via POST ou POST normal
    $data = $_POST;

    // Validação básica dos dados
    if (!isset($data['id']) || !isset($data['title']) || !isset($data['credits']) || !isset($data['price'])) {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Dados incompletos. ID, Título, créditos e preço são obrigatórios para atualização.';
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-credito');
        exit();
    }

    $id = $data['id'];
    $creditPackage = new CreditPackage();
    
    // Preparar os dados para atualização, removendo o ID e o _method
    $productData = [
        'title' => $data['title'],
        'credits' => $data['credits'],
        'bonus_credits' => $data['bonus_credits'] ?? 0,
        'price' => $data['price'],
        'tag' => $data['tag'] ?? null,
        'link' => $data['link'] ?? null,
        'is_active' => isset($data['is_active']) && $data['is_active'] === 'on' ? 1 : 0
    ];

    if ($creditPackage->update($id, $productData)) { // Passa $productData para update
        $_SESSION['status_type'] = 'success';
        $_SESSION['status_message'] = 'Produto de crédito atualizado com sucesso!';
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-credito');
        exit();
    } else {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Erro ao atualizar o produto de crédito.';
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