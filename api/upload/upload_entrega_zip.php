<?php
require_once __DIR__ . '/../../config/session.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Método de requisição inválido.';
    $_SESSION['form_response']['pedido_id'] = $purchaseId;
    header('Location: ../../admin/producao-pedidos');
    exit();
}

$userId = $_POST['user_id'] ?? null;
$purchaseId = $_POST['purchase_id'] ?? null;

if (!$userId || !$purchaseId) {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Dados incompletos. user_id e purchase_id são obrigatórios.';
    $_SESSION['form_response']['pedido_id'] = $purchaseId;
    header('Location: ../../admin/producao-pedidos');
    exit();
}

if (!isset($_FILES['delivery_files']) || empty($_FILES['delivery_files']['name'][0])) {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Nenhum arquivo enviado.';
    $_SESSION['form_response']['pedido_id'] = $purchaseId;
    header('Location: ../../admin/producao-pedidos');
    exit();
}

// Caminho de destino base: uploads/entregas/$user_id/$purchase_id/
$baseUploadDir = __DIR__ . "/../../uploads/entregas/{$userId}/{$purchaseId}/";
$imageUploadDir = $baseUploadDir . "imagens/"; // Subpasta para imagens

// Cria o diretório base se não existir
if (!is_dir($baseUploadDir)) {
    if (!mkdir($baseUploadDir, 0777, true)) {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Falha ao criar diretório de destino base.';
        $_SESSION['form_response']['pedido_id'] = $purchaseId;
        header('Location: ../../admin/producao-pedidos');
        exit();
    }
}

// Cria o diretório de imagens se não existir
if (!is_dir($imageUploadDir)) {
    if (!mkdir($imageUploadDir, 0777, true)) {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Falha ao criar diretório de imagens.';
        $_SESSION['form_response']['pedido_id'] = $purchaseId;
        header('Location: ../../admin/producao-pedidos');
        exit();
    }
}

require_once __DIR__ . '/../../models/AprovacaoPedido.php';
$aprovacaoPedidoModel = new AprovacaoPedido();

$aprovacao = $aprovacaoPedidoModel->where(['pedido_id' => $purchaseId]);
$aprovacaoId = null;
$arquivosAtuais = [];

if (!empty($aprovacao)) {
    $aprovacao = $aprovacao[0];
    $aprovacaoId = $aprovacao['id'];
    if (isset($aprovacao['arquivos']) && is_string($aprovacao['arquivos'])) {
        $arquivosAtuais = json_decode($aprovacao['arquivos'], true) ?? [];
    }
    if (!is_array($arquivosAtuais)) {
        $arquivosAtuais = [];
    }
} else {
    // Se não encontrou uma aprovação, cria uma nova.
    // Isso pode ser ajustado dependendo da regra de negócio,
    // mas para upload de entrega, assume-se que uma aprovação já exista.
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Aprovação de pedido não encontrada para registro dos arquivos.';
    $_SESSION['form_response']['pedido_id'] = $purchaseId;
    header('Location: ../../admin/producao-pedidos');
    exit();
}

$uploadedFilePaths = [];
$allUploadSuccess = true;

foreach ($_FILES['delivery_files']['name'] as $key => $fileName) {
    if ($_FILES['delivery_files']['error'][$key] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['delivery_files']['tmp_name'][$key];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);

        $destinationDir = $isImage ? $imageUploadDir : $baseUploadDir;
        $destPath = $destinationDir . basename($fileName);

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $uploadedFilePaths[] = str_replace(__DIR__ . '/../..', '', $destPath);
        } else {
            $allUploadSuccess = false;
            // Logar erro de upload para arquivo específico, mas continuar tentando com os outros
            error_log("Falha ao mover o arquivo {$fileName} para {$destPath}.");
        }
    } else {
        $allUploadSuccess = false;
        error_log("Erro no upload do arquivo {$fileName}: " . $_FILES['delivery_files']['error'][$key]);
    }
}

if (!empty($uploadedFilePaths)) {
    $arquivosAtuais = array_merge($arquivosAtuais, $uploadedFilePaths);

    $dataToUpdate = [
        'arquivos' => json_encode($arquivosAtuais),
        'aprovacao' => 'Disponível' // Atualiza o status para Disponível após o upload da entrega
    ];

    $updated = $aprovacaoPedidoModel->update($aprovacaoId, $dataToUpdate);

    if ($updated) {
        $_SESSION['status_type'] = 'success';
        $_SESSION['status_message'] = 'Arquivos de entrega enviados e registrados com sucesso!';
    } else {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Arquivos enviados, mas falha ao registrar na aprovação.';
    }
} else {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Nenhum arquivo foi enviado ou houve erro em todos os uploads.';
}

$_SESSION['form_response']['pedido_id'] = $purchaseId;
header('Location: ../../admin/producao-pedidos');
exit();
?>