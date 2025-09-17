<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/database.php';
require_once '../../models/CarouselProduct.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $uploadedImageUrls = [];
    $uploadedDownloadPaths = [];
    
    // Lógica para upload de imagens
    if (!empty($_FILES['images_upload']['name'][0])) {
        $productDirName = uniqid(); // Gera um nome único para a pasta do produto
        $uploadDir = '../../uploads/carrossel/' . $productDirName . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Cria o diretório para o produto
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
                    $uploadedImageUrls[] = $_SESSION['base_url'] . '/uploads/carrossel/' . $productDirName . '/' . $fileName;
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
            header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-carrossel');
            exit();
        }
    }

    // Lógica para upload de múltiplos arquivos de download
    if (!empty($_FILES['download']['name'][0])) {
        $productDownloadDirName = uniqid('download_'); // Gera um nome único para a pasta de downloads
        $uploadDownloadDir = '../../doc/prontos/carrossel/' . $productDownloadDirName . '/';
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
                    $uploadedDownloadPaths[] = '/doc/prontos/carrossel/' . $productDownloadDirName . '/' . $fileName;
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
            header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-carrossel');
            exit();
        }
    }

    // Validação básica dos dados do formulário
    if (!isset($data['name']) || !isset($data['credits'])) {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Dados incompletos. Nome e créditos são obrigatórios.';
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-carrossel');
        exit();
    }

    $carouselProduct = new CarouselProduct();
    
    // Preparar os dados para criação
    $productData = [
        'name' => $data['name'],
        'theme' => $data['theme'] ?? null,
        'category' => $data['category'] ?? null,
        'type' => $data['type'] ?? null,
        'credits' => $data['credits'],
        'sold_quantity' => $data['sold_quantity'] ?? 0,
        'customization_types' => isset($data['customization_types']) ? json_encode($data['customization_types']) : null,
        'description' => $data['description'] ?? null,
        'page_count' => $data['page_count'] ?? 1,
        'status' => $data['status'] ?? 'active',
        'unique_code' => uniqid('carousel_'), // Gerar um código único
        'images' => json_encode($uploadedImageUrls), // Salva URLs das imagens como JSON
        'download' => json_encode($uploadedDownloadPaths) // Salva os caminhos dos downloads como JSON
    ];

    if ($carouselProduct->create($productData)) {
        $_SESSION['status_type'] = 'success';
        $_SESSION['status_message'] = 'Produto Carrossel criado com sucesso!';
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-carrossel');
        exit();
    } else {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Erro ao criar o Produto Carrossel.';
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-carrossel');
        exit();
    }
} else {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Método não permitido.';
    header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-carrossel');
    exit();
}
?>