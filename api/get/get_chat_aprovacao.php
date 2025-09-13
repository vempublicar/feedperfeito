<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/AprovacaoPedido.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    exit();
}

$pedidoId = $_GET['pedido_id'] ?? null;

if (!$pedidoId) {
    echo json_encode(['success' => false, 'message' => 'ID do pedido é obrigatório.']);
    exit();
}

$aprovacaoPedidoModel = new AprovacaoPedido();
$aprovacao = $aprovacaoPedidoModel->where(['pedido_id' => $pedidoId]);

if (!empty($aprovacao)) {
    $aprovacaoPedido = $aprovacao[0];
    $conversa = [];
    if (isset($aprovacaoPedido['conversa']) && is_string($aprovacaoPedido['conversa'])) {
        $conversa = json_decode($aprovacaoPedido['conversa'], true) ?? [];
    } elseif (isset($aprovacaoPedido['conversa']) && is_array($aprovacaoPedido['conversa'])) {
        $conversa = $aprovacaoPedido['conversa'];
    }
    echo json_encode(['success' => true, 'conversa' => $conversa]);
} else {
    echo json_encode(['success' => true, 'conversa' => []]); // Retorna array vazio se não houver aprovação
}
?>