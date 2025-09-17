<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../config/database.php';
require_once '../../models/MultipleProduct.php';

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
    $uploadedDownloadPaths = []; // Inicializa para múltiplos downloads

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

    // Lógica para upload de múltiplos arquivos de download
    if (!empty($_FILES['download']['name'][0])) {
        $productDownloadDirName = uniqid('download_'); // Gera um nome único para a pasta de downloads
        $uploadDownloadDir = '../../doc/prontos/multiple/' . $productDownloadDirName . '/'; // Corrigido o diretório
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
            header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo');
            exit();
        }
    }

    // Validação básica dos dados do formulário
    $id = $_REQUEST['id'] ?? $data['id'] ?? null;

    if (!$id || !isset($data['name']) || !isset($data['credits'])) {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Dados incompletos. ID, nome e créditos são obrigatórios para atualização.';
        header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo');
        exit();
    }

    $multipleProduct = new MultipleProduct();
    
    // Obter imagens existentes do banco de dados para comparação
    $existingProduct = $multipleProduct->find($id);
    $imagesInDatabase = [];
    if ($existingProduct && !empty($existingProduct['images'])) {
        $imagesInDatabase = json_decode($existingProduct['images'], true);
    }

    // Obter downloads existentes do banco de dados
    $existingDownloadsInDatabase = [];
    if ($existingProduct && !empty($existingProduct['download'])) {
        $existingDownloadsInDatabase = json_decode($existingProduct['download'], true);
    }

    // Lógica para identificar e remover downloads antigos que não foram mantidos
    $existingDownloadUrlsFromForm = isset($data['existing_downloads']) ? json_decode($data['existing_downloads'], true) : [];
    if (!is_array($existingDownloadUrlsFromForm)) {
        $existingDownloadUrlsFromForm = [];
    }
    
    foreach ($existingDownloadsInDatabase as $dbDownloadUrl) {
        if (!in_array($dbDownloadUrl, $existingDownloadUrlsFromForm)) {
            $relativePath = str_replace($_SESSION['base_url'] . '/', '../../', $dbDownloadUrl);
            $filePath = '../../' . $relativePath;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            // Remover diretório pai se estiver vazio
            $dirPath = dirname($filePath);
            if (is_dir($dirPath) && count(scandir($dirPath)) == 2) {
                rmdir($dirPath);
            }
        }
    }
    
    // Combinar URLs das imagens existentes (que o usuário manteve) com as recém-uploadeadas
    $finalImageUrls = array_merge($existingImageUrlsFromForm, $uploadedImageUrls);
    $finalDownloadPaths = array_merge($existingDownloadUrlsFromForm, $uploadedDownloadPaths); // Combina downloads existentes e novos

    // Preparar os dados para atualização
    $productData = [
        'name' => $data['name'],
        'theme' => $data['theme'] ?? null,
        'category' => $data['category'] ?? null,
        'type' => $data['type'] ?? null,
        'utilization' => $data['utilization'] ?? null,
        'credits' => $data['credits'],
        'sold_quantity' => $data['sold_quantity'] ?? 0,
        'customization_types' => isset($data['customization_types']) ? json_encode($data['customization_types']) : null,
        'description' => $data['description'] ?? null,
        'page_count' => $data['page_count'] ?? 1,
        'status' => $data['status'] ?? 'active',
        'images' => json_encode($finalImageUrls),
        'download' => json_encode($finalDownloadPaths) // Salva os caminhos dos arquivos de download como JSON
    ];

    if ($multipleProduct->update($id, $productData)) {
        $_SESSION['status_type'] = 'success';
        $_SESSION['status_message'] = 'Produto Múltiplo atualizado com sucesso!';
        // header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo');
        exit();
    } else {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Erro ao atualizar o Produto Múltiplo.';
        // header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo');
        exit();
    }
} else {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Método não permitido.';
    // header('Location: ' . $_SESSION['base_url'] . '/admin/produtos-multiplo');
    exit();
}
?>