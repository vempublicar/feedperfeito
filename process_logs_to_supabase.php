<?php

require_once __DIR__ . '/config/database.php';

echo "Iniciando processamento dos logs para o Supabase...\n";

$logFiles = [
    __DIR__ . '/logs/webhook_2025-09-08_22-45-53.txt',
    __DIR__ . '/logs/webhook_2025-09-08_22-46-25.txt'
];

foreach ($logFiles as $filePath) {
    if (file_exists($filePath)) {
        echo "Processando arquivo: " . basename($filePath) . "\n";
        $fileContent = file_get_contents($filePath);

        // Extrai o JSON do corpo
        $bodyStart = strpos($fileContent, '=== BODY ===');
        if ($bodyStart !== false) {
            $jsonContent = substr($fileContent, $bodyStart + strlen('=== BODY ==='));
            $payload = json_decode(trim($jsonContent), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                // Extrai os dados relevantes do payload
                $event = $payload['event'] ?? null;
                $time = $payload['time'] ?? null;
                $resource_id = $payload['resource']['id'] ?? null;
                $customer_email = $payload['resource']['customer']['data']['email'] ?? null;
                $customer_name = $payload['resource']['customer']['data']['name'] ?? null;
                $order_number = $payload['resource']['number'] ?? null;
                $value_total = $payload['resource']['value_total'] ?? null;
                $status_alias = $payload['resource']['status']['data']['alias'] ?? null;
                $gateway_transaction_id = $payload['resource']['transactions']['data'][0]['gateway_transaction_id'] ?? null;
                $payment_method = $payload['resource']['payments'][0]['alias'] ?? null;

                $data_to_insert = [
                    'event' => $event,
                    'event_time' => $time,
                    'yampi_order_id' => $resource_id,
                    'customer_email' => $customer_email,
                    'customer_name' => $customer_name,
                    'order_number' => $order_number,
                    'total_value' => $value_total,
                    'order_status' => $status_alias,
                    'gateway_transaction_id' => $gateway_transaction_id,
                    'payment_method' => $payment_method,
                    'raw_payload' => json_encode($payload)
                ];

                try {
                    supabase_request('yampi_webhooks', 'POST', $data_to_insert);
                    echo "Dados do arquivo " . basename($filePath) . " inseridos com sucesso no Supabase.\n";
                } catch (Exception $e) {
                    echo "Erro ao inserir dados do arquivo " . basename($filePath) . " no Supabase: " . $e->getMessage() . "\n";
                }
            } else {
                echo "Erro ao decodificar JSON do arquivo " . basename($filePath) . ": " . json_last_error_msg() . "\n";
            }
        } else {
            echo "Corpo do JSON não encontrado no arquivo " . basename($filePath) . ".\n";
        }
    } else {
        echo "Arquivo não encontrado: " . basename($filePath) . "\n";
    }
}

echo "Processamento concluído.\n";

?>