<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/Purchase.php';
require_once __DIR__ . '/../../models/AprovacaoPedido.php';

header('Content-Type: application/json');

// Garante que a requisição é um POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    exit();
}

// Obtém os dados da requisição
$input = json_decode(file_get_contents('php://input'), true);
$purchaseId = $input['purchase_id'] ?? null;
$newStatus = $input['new_status'] ?? null;
$uidUsuarioPedido = $input['uid_usuario_pedido'] ?? null;
$uniqueCode = $input['unique_code'] ?? null;
$downloadPath = $input['download_path'] ?? null;

if (!$purchaseId || !$newStatus) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos. purchase_id e new_status são obrigatórios.']);
    exit();
}

$purchaseModel = new Purchase();

// Atualiza o status do pedido e o caminho de download
$dataToUpdate = ['status' => $newStatus];
if ($downloadPath !== null) {
    $dataToUpdate['download'] = $downloadPath;
}
$updated = $purchaseModel->update($purchaseId, $dataToUpdate);

if ($updated) {
    if ($newStatus === 'confirmado') {
        $aprovacaoPedidoModel = new AprovacaoPedido();
        $dataAprovacao = [
            'uid_usuario_pedido' => $uidUsuarioPedido,
            'unique_code' => $uniqueCode,
            'pedido_id' => $purchaseId,
            'aprovacao' => 'Aprovação',
            'observacoes' => 'Pedido confirmado. Em breve estrará na etapa de produção.'
        ];
        $aprovacaoCreated = $aprovacaoPedidoModel->create($dataAprovacao);
        if (!$aprovacaoCreated) {
            echo json_encode(['success' => false, 'message' => 'Falha ao criar registro de aprovação.']);
            exit();
        }
    }
    echo json_encode(['success' => true, 'message' => 'Status do pedido atualizado com sucesso!', 'new_status' => $newStatus]);
} else {
    echo json_encode(['success' => false, 'message' => 'Falha ao atualizar o status do pedido.']);
}
?>
