<?php
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['download']) && $_FILES['download']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['download'];

        // Validar tipo de arquivo
        $allowedTypes = [
            'application/zip',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ];
        if (!in_array($file['type'], $allowedTypes)) {
            $response['message'] = 'Tipo de arquivo não permitido.';
            echo json_encode($response);
            exit;
        }

        // Diretório de destino
        $uploadDir = __DIR__ . '/../../doc/prontos/carrossel/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Gerar nome único para o arquivo
        $fileName = uniqid() . '_' . basename($file['name']);
        $destination = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $response['success'] = true;
            $response['message'] = 'Arquivo enviado com sucesso.';
            $response['file_path'] = 'doc/prontos/carrossel/' . $fileName;
        } else {
            $response['message'] = 'Erro ao mover o arquivo.';
        }
    } else {
        $response['message'] = 'Nenhum arquivo enviado ou erro no upload.';
    }
} else {
    $response['message'] = 'Método de requisição não permitido.';
}

echo json_encode($response);
?>