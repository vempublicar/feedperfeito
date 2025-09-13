<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define o diretório de destino para o upload
$uploadDir = '../../uploads/carrossel/';

// Garante que o diretório de upload exista
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

header('Content-Type: application/json');

$uploadedFiles = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['images_upload']['name'][0])) {
        $totalFiles = count($_FILES['images_upload']['name']);

        for ($i = 0; $i < $totalFiles; $i++) {
            $fileName = basename($_FILES['images_upload']['name'][$i]);
            $targetFilePath = $uploadDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            // Permitir certos formatos de arquivo
            $allowTypes = ['jpg', 'png', 'jpeg', 'gif'];
            if (in_array($fileType, $allowTypes)) {
                // Upload do arquivo para o servidor
                if (move_uploaded_file($_FILES['images_upload']['tmp_name'][$i], $targetFilePath)) {
                    $uploadedFiles[] = $_SESSION['base_url'] . '/uploads/carrossel/' . $fileName;
                } else {
                    $errors[] = "Erro ao fazer upload de " . $fileName;
                }
            } else {
                $errors[] = "Formato de arquivo não permitido para " . $fileName;
            }
        }
    } else {
        $errors[] = "Nenhum arquivo enviado.";
    }

    if (empty($errors)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Upload de imagens realizado com sucesso.',
            'files' => $uploadedFiles
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Ocorreram erros durante o upload.',
            'errors' => $errors
        ]);
    }

} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido.']);
}
?>