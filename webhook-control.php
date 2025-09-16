<?php
// webhook_control.php

// Pasta de logs
$logDir = __DIR__ . '/logs/control/';
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

// Nome do arquivo baseado na data/hora
$logFileName = $logDir . 'control_' . date('Y-m-d_H-i-s') . '.log';

// Captura os headers
if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }
}
$headers = getallheaders();

// Captura o corpo cru
$input = file_get_contents('php://input');

// Monta o conteúdo do log
$logContent  = "=== HEADERS ===\n" . print_r($headers, true);
$logContent .= "\n=== BODY ===\n" . $input . "\n";

// Salva no arquivo
file_put_contents($logFileName, $logContent);

// Resposta para o serviço de webhook
http_response_code(200);
echo "Webhook de controle recebido e salvo com sucesso.";
