<?php
require_once __DIR__ . '/../../config/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    exit();
}

$userId = $_POST['user_id'] ?? null;
$purchaseId = $_POST['purchase_id'] ?? null;

if (!$userId || !$purchaseId) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos. user_id e purchase_id são obrigatórios.']);
    exit();
}

if (!isset($_FILES['zip_file']) || $_FILES['zip_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Erro no upload do arquivo.']);
    exit();
}

$file = $_FILES['zip_file'];
$fileName = basename($file['name']);
$fileTmpPath = $file['tmp_name'];
$fileSize = $file['size'];
$fileType = $file['type'];
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Validação básica do arquivo
if ($fileExtension !== 'zip') {
    echo json_encode(['success' => false, 'message' => 'Apenas arquivos ZIP são permitidos.']);
    exit();
}

// Caminho de destino: uploads/entregas/$user_id/$purchase_id/
$uploadDir = __DIR__ . "/../../uploads/entregas/{$userId}/{$purchaseId}/";

// Cria o diretório se não existir
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) { // Permissão 0777 para fins de teste, ajustar em produção
        echo json_encode(['success' => false, 'message' => 'Falha ao criar diretório de destino.']);
        exit();
    }
}

$destPath = $uploadDir . $fileName;

if (move_uploaded_file($fileTmpPath, $destPath)) {
    // Agora, insere o caminho do arquivo na tabela aprovacoes_pedidos
    require_once __DIR__ . '/../../models/AprovacaoPedido.php';
    $aprovacaoPedidoModel = new AprovacaoPedido();

    // Tenta encontrar a aprovação pelo pedido_id
    $aprovacao = $aprovacaoPedidoModel->where(['pedido_id' => $purchaseId]);

    if (!empty($aprovacao)) {
        $aprovacao = $aprovacao[0];
        $aprovacaoId = $aprovacao['id'];

        $arquivosAtuais = [];
        if (isset($aprovacao['arquivos']) && is_string($aprovacao['arquivos'])) {
            $arquivosAtuais = json_decode($aprovacao['arquivos'], true) ?? [];
        }
        if (!is_array($arquivosAtuais)) {
            $arquivosAtuais = [];
        }

        // Adiciona o novo caminho do arquivo à lista
        $arquivosAtuais[] = str_replace(__DIR__ . '/../..', '', $destPath); // Salva o caminho relativo

        $dataToUpdate = [
            'arquivos' => json_encode($arquivosAtuais),
            'aprovacao' => 'Disponível' // Opcional: Atualiza o status para Disponível após o upload da entrega
        ];

        $updated = $aprovacaoPedidoModel->update($aprovacaoId, $dataToUpdate);

        if ($updated) {
            echo json_encode(['success' => true, 'message' => 'Arquivo ZIP enviado e registrado com sucesso!', 'path' => str_replace(__DIR__ . '/../..', '', $destPath)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Arquivo ZIP enviado, mas falha ao registrar na aprovação.']);
        }
    } else {
        // Se não encontrou aprovacaoPedido, pode ser que ainda não exista.
        // Neste caso, você pode criar um novo ou apenas informar que a aprovação não foi encontrada.
        // Por simplicidade, vamos apenas informar que não foi encontrada.
        echo json_encode(['success' => false, 'message' => 'Arquivo ZIP enviado, mas aprovação de pedido não encontrada para registro.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Falha ao mover o arquivo enviado.']);
}
?>