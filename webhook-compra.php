<?php
// webhook-compra.php

// Diretório onde vai salvar os logs (ajuste conforme seu servidor)
$logDir = __DIR__ . '/logs';

// Cria a pasta se não existir
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

// Nome único para cada requisição (ex: webhook_2025-09-08_15-30-22.txt)
$filename = $logDir . '/webhook_' . date('Y-m-d_H-i-s') . '.txt';

// Captura os headers
$headers = getallheaders();

// Captura o body cru
$body = file_get_contents("php://input");

// Monta o conteúdo para salvar
$content = "=== HEADERS ===\n" . print_r($headers, true) . "\n";
$content .= "=== BODY ===\n" . $body . "\n";

// Salva no arquivo
file_put_contents($filename, $content);

// Retorna 200 OK para Yampi
http_response_code(200);
echo "Webhook recebido";