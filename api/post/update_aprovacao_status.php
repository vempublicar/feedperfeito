<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/AprovacaoPedido.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
$aprovacaoId = $input['aprovacao_id'] ?? null;
$newStatus = $input['new_status'] ?? null;

if (!$aprovacaoId || !$newStatus) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos. ID da aprovação e novo status são obrigatórios.']);
    exit();
}

$aprovacaoPedidoModel = new AprovacaoPedido();
$aprovacao = $aprovacaoPedidoModel->where(['id' => $aprovacaoId]);

if (empty($aprovacao)) {
    echo json_encode(['success' => false, 'message' => 'Aprovação de pedido não encontrada.']);
    exit();
}

$aprovacao = $aprovacao[0];
$dataToUpdate = ['aprovacao' => ucfirst($newStatus)];

if ($newStatus === 'revisão') {
    $dataToUpdate['data_revisao'] = date('Y-m-d H:i:s');
} elseif ($newStatus === 'disponível') {
    $dataToUpdate['data_aprovacao'] = date('Y-m-d H:i:s');
    // Lógica para adicionar bônus de créditos, se aplicável, pode ser adicionada aqui.
    // Ex: Se o status anterior era 'Aprovação' e agora é 'Disponível', e num_revisao era 0, etc.
}

$updated = $aprovacaoPedidoModel->update($aprovacaoId, $dataToUpdate);

if ($updated) {
    echo json_encode(['success' => true, 'message' => 'Status da aprovação atualizado com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Falha ao atualizar status da aprovação.']);
}
?>