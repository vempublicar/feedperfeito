<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/database.php';
require_once '../../models/Purchase.php';
require_once '../../models/CreditManager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $userId = $_SESSION['user_id'] ?? null;
        $purchaseId = $_POST['purchaseId'] ?? null;
        $creditsToRefund = $_POST['creditsToRefund'] ?? null;

        if (!$userId || !$purchaseId || !$creditsToRefund) {
            $_SESSION['status_type'] = 'error';
            $_SESSION['status_message'] = 'Dados obrigatórios faltando para cancelar a compra.';
            header('Location: ' . $_SESSION['base_url'] . '/dashboard/pedidos');
            exit();
        }

        $purchaseModel = new Purchase();
        $creditManager = new CreditManager();

        // 1. Atualizar o status da compra para 'canceled'
        $updateResult = $purchaseModel->update($purchaseId, ['status' => 'canceled']);

        if (!$updateResult) {
            $_SESSION['status_type'] = 'error';
            $_SESSION['status_message'] = 'Erro ao atualizar o status da compra.';
            header('Location: ' . $_SESSION['base_url'] . '/dashboard/pedidos');
            exit();
        }

        // 2. Estornar os créditos
        $newCredits = $creditManager->refundCredits($userId, $creditsToRefund, 'Estorno de compra ID: ' . $purchaseId);

        if ($newCredits !== false) {
            $_SESSION['status_type'] = 'success';
            $_SESSION['status_message'] = 'Compra cancelada e créditos estornados com sucesso!';
            $_SESSION['user_credits'] = $newCredits; // Atualiza o saldo na sessão
            header('Location: ' . $_SESSION['base_url'] . '/dashboard/pedidos');
            exit();
        } else {
            // Se o estorno falhar, a compra já foi marcada como cancelada, mas os créditos não foram devolvidos.
            // Isso requer atenção manual ou um mecanismo de re-tentativa.
            $_SESSION['status_type'] = 'warning';
            $_SESSION['status_message'] = 'Compra cancelada, mas houve um erro ao estornar os créditos. Por favor, entre em contato com o suporte.';
            header('Location: ' . $_SESSION['base_url'] . '/dashboard/pedidos');
            exit();
        }

    } catch (Exception $e) {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Erro interno do servidor: ' . $e->getMessage();
        header('Location: ' . $_SESSION['base_url'] . '/dashboard/pedidos');
        exit();
    }
} else {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Método de requisição não permitido.';
    header('Location: ' . $_SESSION['base_url'] . '/dashboard/pedidos');
    exit();
}
?>