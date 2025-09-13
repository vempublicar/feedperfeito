<?php
require_once __DIR__ . '/../../config/session.php';
requireUserLogin();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'products' => []];

$uid_usuario = $_SESSION['user_id'] ?? null;

if (!$uid_usuario) {
    $response['message'] = 'UID do usuário não encontrado.';
    echo json_encode($response);
    exit;
}

$doc_path = __DIR__ . '/../../doc/' . $uid_usuario;
$products_list_file = $doc_path . '/produtos.json';
$product_data_path = $doc_path . '/produtos';

if (!is_dir($product_data_path)) {
    $response['success'] = true; // Retornar sucesso, mas com produtos vazios
    echo json_encode($response);
    exit;
}

$products_list = [];
if (file_exists($products_list_file)) {
    $products_list = json_decode(file_get_contents($products_list_file), true);
    if (!is_array($products_list)) {
        $products_list = [];
    }
}

$products_data = [];
foreach ($products_list as $product_id) {
    $product_file = $product_data_path . '/' . $product_id . '.json';
    if (file_exists($product_file)) {
        $product_content = file_get_contents($product_file);
        $product = json_decode($product_content, true);
        if ($product) {
            $products_data[] = $product;
        }
    }
}

$response['success'] = true;
$response['products'] = $products_data;
echo json_encode($response);
?>