<?php
require_once '../../config/database.php';
require_once '../../models/CarouselProduct.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $carouselProduct = new CarouselProduct();
    $products = $carouselProduct->all();

    if ($products !== false) {
        http_response_code(200);
        echo json_encode($products);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Erro ao buscar produtos carrossel.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Método não permitido.']);
}
?>