<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/database.php';
require_once '../../models/FeedProduct.php'; // Alterado para FeedProduct.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Para lidar com o method spoofing (PUT via POST)
    $method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

    if ($method === 'PUT') {
        parse_str(file_get_contents("php://input"), $put_data);
        $data = array_merge($_POST, $put_data); // Combina POST e PUT data
    } else {
        $data = $_POST;
    }

    $uploadedImageUrls = []; // URLs das imagens recém-uploadeadas
    $existingImageUrlsFromForm = $_POST['existing_images'] ?? []; // URLs das imagens existentes que o usuário manteve no formulário

    // Lógica para upload de imagens
    if (!empty($_FILES['images_upload']['name'][0])) {
        $uploadDir = '../../uploads/feed/'; // Alterado para uploads/feed/
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
                    $uploadedImageUrls[] = $_SESSION['base_url'] . '/uploads/feed/' . $fileName; // Alterado para uploads/feed/
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
            header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-feed'); // Alterado para produtos-feed
            exit();
        }
    }

    // Validação básica dos dados do formulário
    // O ID pode vir do $_REQUEST (GET ou POST) ou do $data
    $id = $_REQUEST['id'] ?? $data['id'] ?? null;

    if (!$id || !isset($data['name']) || !isset($data['credits'])) {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Dados incompletos. ID, nome e créditos são obrigatórios para atualização.';
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-feed'); // Alterado para produtos-feed
        exit();
    }

    $feedProduct = new FeedProduct(); // Alterado para FeedProduct
    
    // Obter imagens existentes do banco de dados para comparação
    $existingProduct = $feedProduct->find($id); // Alterado para $feedProduct->find
    $imagesInDatabase = [];
    if ($existingProduct && !empty($existingProduct['images'])) {
        $imagesInDatabase = json_decode($existingProduct['images'], true);
    }

    // Identificar e remover imagens que foram excluídas pelo usuário
    foreach ($imagesInDatabase as $dbImage) {
        if (!in_array($dbImage, $existingImageUrlsFromForm)) {
            // A imagem foi removida pelo usuário, então a removemos do servidor
            $filePath = str_replace($_SESSION['base_url'] . '/', '../../', $dbImage);
            if (file_exists($filePath)) {
                unlink($filePath); // Exclui o arquivo físico
            }
        }
    }
    
    // Combinar URLs das imagens existentes (que o usuário manteve) com as recém-uploadeadas
    $finalImageUrls = array_merge($existingImageUrlsFromForm, $uploadedImageUrls);

    // Preparar os dados para atualização
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
        'images' => json_encode($finalImageUrls) // Salva URLs das imagens como JSON
    ];

    if ($feedProduct->update($id, $productData)) { // Alterado para $feedProduct->update
        $_SESSION['status_type'] = 'success';
        $_SESSION['status_message'] = 'Produto Feed atualizado com sucesso!'; // Alterado para Produto Feed
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-feed'); // Alterado para produtos-feed
        exit();
    } else {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Erro ao atualizar o Produto Feed.'; // Alterado para Produto Feed
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-feed'); // Alterado para produtos-feed
        exit();
    }
} else {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Método não permitido.';
    header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-feed'); // Alterado para produtos-feed
    exit();
}
?>