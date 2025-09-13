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
$pedidoId = $input['pedido_id'] ?? null;
$uniqueCode = $input['unique_code'] ?? null;
$message = $input['message'] ?? null;
$sender = $input['sender'] ?? 'Usuário Desconhecido';
 
if (!$message) {
    echo json_encode(['success' => false, 'message' => 'A mensagem é obrigatória.']);
    exit();
}
 
$aprovacaoPedidoModel = new AprovacaoPedido();
$aprovacao = null;
 
// Tenta encontrar a aprovação pelo aprovacao_id
if ($aprovacaoId) {
    $foundAprovacao = $aprovacaoPedidoModel->where(['id' => $aprovacaoId]);
    if (!empty($foundAprovacao)) {
        $aprovacao = $foundAprovacao[0];
    }
}
 
// Se não encontrou pelo aprovacao_id, tenta encontrar pelo pedido_id
if (!$aprovacao && $pedidoId) {
    $foundAprovacao = $aprovacaoPedidoModel->where(['pedido_id' => $pedidoId]);
    if (!empty($foundAprovacao)) {
        $aprovacao = $foundAprovacao[0];
        $aprovacaoId = $aprovacao['id']; // Atualiza aprovacaoId se encontrado
    }
}
 
// Se ainda não encontrou, cria um novo AprovacaoPedido
if (!$aprovacao && $pedidoId && $uniqueCode) {
    // Aqui você precisaria de mais dados para criar um AprovacaoPedido completo,
    // como uid_usuario_pedido, bonus, imagens, etc.
    // Por simplicidade, vou criar com dados mínimos e status inicial.
    // O ideal seria buscar esses dados da tabela 'purchases' ou de outro lugar.
    $newAprovacaoData = [
        'uid_usuario_pedido' => $_SESSION['user_id'] ?? 'desconhecido', // Assumindo que o user_id está na sessão
        'unique_code' => $uniqueCode,
        'bonus' => 0,
        'imagens' => '[]', // JSON vazio
        'aprovacao' => 'Pendente', // Status inicial
        'num_revisao' => 0,
        'conversa' => '[]', // JSON vazio
        'pedido_id' => $pedidoId, // Atribui o pedido_id
    ];
    $aprovacaoId = $aprovacaoPedidoModel->createAprovacao($newAprovacaoData);
    if ($aprovacaoId) {
        $aprovacao = $aprovacaoPedidoModel->where(['id' => $aprovacaoId])[0];
    }
}
 
if (!$aprovacao) {
    echo json_encode(['success' => false, 'message' => 'Não foi possível encontrar ou criar uma aprovação para este pedido.']);
    exit();
}
 
$novaMensagem = [
    'sender' => $sender,
    'message' => $message,
    'timestamp' => date('Y-m-d H:i:s')
];
 
$updated = $aprovacaoPedidoModel->updateConversa($aprovacaoId, $novaMensagem);

if ($updated) {
    echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Falha ao enviar mensagem.']);
}
?>