<?php
require_once '../../config/database.php';
require_once '../../models/FeedProduct.php'; // Alterado para FeedProduct.php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $feedProduct = new FeedProduct(); // Alterado para FeedProduct
    $products = $feedProduct->all(); // Alterado para $feedProduct->all()

    if ($products !== false) {
        http_response_code(200);
        echo json_encode($products);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Erro ao buscar produtos feed.']); // Alterado para produtos feed
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Método não permitido.']);
}
?>