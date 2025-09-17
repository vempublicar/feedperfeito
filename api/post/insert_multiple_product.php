<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/database.php';
require_once '../../models/MultipleProduct.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $uploadedImageUrls = [];
    $uploadedDownloadPaths = [];

    // Lógica para upload de imagens
    if (!empty($_FILES['images_upload']['name'][0])) {
        $uploadDir = '../../uploads/multiple/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $totalFiles = count($_FILES['images_upload']['name']);
        $errors = [];

        for ($i = 0; $i < $totalFiles; $i++) {
            $fileName = uniqid() . '_' . basename($_FILES['images_upload']['name'][$i]); // Garante nome único
            $targetFilePath = $uploadDir . $fileName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            $allowTypes = ['jpg', 'png', 'jpeg', 'gif'];
            if (in_array($fileType, $allowTypes)) {
                if (move_uploaded_file($_FILES['images_upload']['tmp_name'][$i], $targetFilePath)) {
                    $uploadedImageUrls[] = $_SESSION['base_url'] . '/uploads/multiple/' . $fileName;
                } else {
                    $errors[] = "Erro ao fazer upload de " . $_FILES['images_upload']['name'][$i];
                }
            } else {
                $errors[] = "Formato de arquivo não permitido para " . $_FILES['images_upload']['name'][$i];
            }
        }

        if (!empty($errors)) {
            $_SESSION['status_type'] = 'error';
            $_SESSION['status_message'] = 'Erro(s) no upload de imagens: ' . implode(', ', $errors);
            header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo');
            exit();
        }

    }

    // Lógica para upload de múltiplos arquivos de download (movida para fora do bloco de imagens)
    if (!empty($_FILES['download']['name'][0])) {
        $productDownloadDirName = uniqid('download_'); // Gera um nome único para a pasta de downloads
        $uploadDownloadDir = '../../doc/prontos/multiple/' . $productDownloadDirName . '/';
        if (!is_dir($uploadDownloadDir)) {
            mkdir($uploadDownloadDir, 0777, true);
        }

        $totalDownloadFiles = count($_FILES['download']['name']);
        $downloadErrors = [];

        for ($i = 0; $i < $totalDownloadFiles; $i++) {
            $fileName = uniqid() . '_' . basename($_FILES['download']['name'][$i]);
            $targetFilePath = $uploadDownloadDir . $fileName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            $allowTypes = ['zip', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'png', 'jpeg', 'gif'];
            if (in_array($fileType, $allowTypes)) {
                if (move_uploaded_file($_FILES['download']['tmp_name'][$i], $targetFilePath)) {
                    $uploadedDownloadPaths[] = '/doc/prontos/multiple/' . $productDownloadDirName . '/' . $fileName;
                } else {
                    $downloadErrors[] = "Erro ao fazer upload de " . $_FILES['download']['name'][$i];
                }
            } else {
                $downloadErrors[] = "Formato de arquivo não permitido para " . $_FILES['download']['name'][$i];
            }
        }

        if (!empty($downloadErrors)) {
            $_SESSION['status_type'] = 'error';
            $_SESSION['status_message'] = 'Erro(s) no upload de downloads: ' . implode(', ', $downloadErrors);
            header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo'); // Corrigido o redirecionamento
            exit();
        }
    }

    // Inicializa $uploadedImageUrls se não houver imagens de upload
    if (!isset($uploadedImageUrls)) {
        $uploadedImageUrls = [];
    }
    
    // Removida a lógica de limpeza de diretório incorreta

    // Validação básica dos dados do formulário
    if (!isset($data['name']) || !isset($data['credits'])) {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Dados incompletos. Nome e créditos são obrigatórios.';
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo');
        exit();
    }

    $multipleProduct = new MultipleProduct();

    // Preparar os dados para criação
    $productData = [
        'name' => $data['name'],
        'theme' => $data['theme'] ?? null,
        'category' => $data['category'] ?? null,
        'type' => $data['type'] ?? null,
        'utilization' => $data['utilization'] ?? null, // Adicionado
        'credits' => $data['credits'],
        'sold_quantity' => $data['sold_quantity'] ?? 0,
        'customization_types' => isset($data['customization_types']) ? json_encode($data['customization_types']) : null,
        'description' => $data['description'] ?? null,
        'page_count' => $data['page_count'] ?? 1,
        'status' => $data['status'] ?? 'active',
        'unique_code' => uniqid('multiple_'), // Gerar um código único para multiple
        'images' => json_encode($uploadedImageUrls), // Salva URLs das imagens como JSON
        'download' => json_encode($uploadedDownloadPaths) // Salva os caminhos dos arquivos de download como JSON
    ];

    if ($multipleProduct->create($productData)) {
        $_SESSION['status_type'] = 'success';
        $_SESSION['status_message'] = 'Produto Múltiplo criado com sucesso!';
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo');
        exit();
    } else {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Erro ao criar o Produto Múltiplo.';
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo');
        exit();
    }
} else {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Método não permitido.';
    header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo');
    exit();
}
?>