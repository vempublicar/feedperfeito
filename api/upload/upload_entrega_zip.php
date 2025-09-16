<?php
require_once __DIR__ . '/../../config/session.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Método de requisição inválido.';
    $_SESSION['form_response']['pedido_id'] = $purchaseId;
    header('Location: ' . $_SESSION['base_url'] . '/admin/producao-pedidos');
    exit();
}

$userId = $_POST['user_id'] ?? null;
$purchaseId = $_POST['purchase_id'] ?? null;

if (!$userId || !$purchaseId) {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Dados incompletos. user_id e purchase_id são obrigatórios.';
    $_SESSION['form_response']['pedido_id'] = $purchaseId;
    header('Location: ' . $_SESSION['base_url'] . '/admin/producao-pedidos');
    exit();
}

if (!isset($_FILES['zip_file']) || $_FILES['zip_file']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Erro no upload do arquivo.';
    $_SESSION['form_response']['pedido_id'] = $purchaseId;
    header('Location: ' . $_SESSION['base_url'] . '/admin/producao-pedidos');
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
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Apenas arquivos ZIP são permitidos.';
    $_SESSION['form_response']['pedido_id'] = $purchaseId;
    header('Location: ' . $_SESSION['base_url'] . '/admin/producao-pedidos');
    exit();
}

// Caminho de destino: uploads/entregas/$user_id/$purchase_id/
$uploadDir = __DIR__ . "/../../uploads/entregas/{$userId}/{$purchaseId}/";

// Cria o diretório se não existir
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) { // Permissão 0777 para fins de teste, ajustar em produção
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Falha ao criar diretório de destino.';
        $_SESSION['form_response']['pedido_id'] = $purchaseId;
        header('Location: ' . $_SESSION['base_url'] . '/admin/producao-pedidos');
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
            $_SESSION['status_type'] = 'success';
            $_SESSION['status_message'] = 'Arquivo ZIP enviado e registrado com sucesso!';
            $_SESSION['form_response']['pedido_id'] = $purchaseId;
            header('Location: ' . $_SESSION['base_url'] . '/admin/producao-pedidos');
            exit();
        } else {
            $_SESSION['status_type'] = 'error';
            $_SESSION['status_message'] = 'Arquivo ZIP enviado, mas falha ao registrar na aprovação.';
            $_SESSION['form_response']['pedido_id'] = $purchaseId;
            header('Location: ' . $_SESSION['base_url'] . '/admin/producao-pedidos');
            exit();
        }
    } else {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Arquivo ZIP enviado, mas aprovação de pedido não encontrada para registro.';
        $_SESSION['form_response']['pedido_id'] = $purchaseId;
        header('Location: ' . $_SESSION['base_url'] . '/admin/producao-pedidos');
        exit();
    }
} else {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Falha ao mover o arquivo enviado.';
    $_SESSION['form_response']['pedido_id'] = $purchaseId;
    header('Location: ' . $_SESSION['base_url'] . '/admin/producao-pedidos');
    exit();
}
?>