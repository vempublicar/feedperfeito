<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/database.php';
require_once '../../models/Purchase.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Usuário não autenticado. Por favor, faça login.';
    header('Location: ../../login.php');
    exit();
}

$downloadFilePath = $_POST['file_path'] ?? null;
$purchaseId = $_POST['purchase_id'] ?? null;

if (!$downloadFilePath || !$purchaseId) {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Parâmetros de download incompletos.';
    header('Location: ../../dashboard/pedidos');
    exit();
}

$purchaseModel = new Purchase();
$purchase = $purchaseModel->find($purchaseId);

if (!$purchase || $purchase['user_id'] !== $_SESSION['user_id']) {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Compra não encontrada ou você não tem permissão para acessá-la.';
    header('Location: ../../dashboard/pedidos');
    exit();
}

$fullPath = '../../' . $downloadFilePath;

if (!file_exists($fullPath)) {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'O arquivo de download não existe no servidor.';
    header('Location: ../../dashboard/pedidos');
    exit();
}

// Atualizar o status da compra para 'Entregue'
$purchaseModel->update($purchaseId, ['status' => 'Entregue', 'download' => $downloadFilePath]);

// Forçar o download do arquivo
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($fullPath));
flush(); // Libera o output buffer
readfile($fullPath);
exit;
?>