<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php'; // Para supabase_request
require_once __DIR__ . '/../put/update_user_credits.php'; // Para update_user_credits

// Definir os diretórios
$logDir = __DIR__ . '/../../logs/';
$concluidoDir = $logDir . 'concluido/';

// Verificar se o diretório 'concluido' existe, senão criar
if (!is_dir($concluidoDir)) {
    mkdir($concluidoDir, 0777, true);
}

// Função para extrair o corpo JSON de um arquivo de log
function extract_json_body($fileContent) {
    $parts = explode('=== BODY ===', $fileContent);
    if (count($parts) > 1) {
        return json_decode(trim($parts[1]), true);
    }
    return null;
}

// Lógica de processamento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $packIdFromForm = $_POST['pack_id'] ?? null; // ID do pacote do formulário (pode ser usado para filtrar)
    $processedCount = 0;
    $errors = [];
    $processedFiles = [];

    // Obter todos os arquivos .txt no diretório de logs
    $logFiles = glob($logDir . '*.txt');

    foreach ($logFiles as $filePath) {
        $fileContent = file_get_contents($filePath);
        $payload = extract_json_body($fileContent);

        if ($payload === null) {
            $errors[] = "Erro ao decodificar JSON do arquivo: " . basename($filePath);
            continue;
        }

        $event = $payload['event'] ?? null;
        $customer_email = $payload['resource']['customer']['data']['email'] ?? null;
        $order_number = $payload['resource']['number'] ?? null;

        // Se o e-mail do cliente não for encontrado, pular este log
        if (empty($customer_email)) {
            $errors[] = "E-mail do cliente não encontrado no arquivo: " . basename($filePath);
            continue;
        }

        // Determinar o novo nome do arquivo
        $newFileName = strtoupper(str_replace(['.', '@'], ['-', '-'], $customer_email));
        
        if ($event === 'order.created') {
            $newFileName .= '-PEDIDO.txt';
        } elseif ($event === 'order.paid') {
            $newFileName .= '-CONFIRMACAO.txt';
        } else {
            $newFileName .= '-DESCONHECIDO.txt'; // Para eventos não mapeados
        }
        
        $destinationPath = $concluidoDir . $newFileName;
        
        // Evitar processar o mesmo arquivo se já foi movido
        // Isso é importante se houver múltiplos arquivos com o mesmo e-mail e evento
        if (file_exists($destinationPath)) {
            // Se o arquivo já existe no destino, apenas move o log atual para evitar sobrescrever
            // ou adiciona ao erro se quiser registrar que já foi processado
            error_log("Arquivo já processado existente no destino: " . $destinationPath . ". Movendo o duplicado.");
            $destinationPath = $concluidoDir . uniqid() . '_' . $newFileName; // Adiciona um ID único para evitar sobrescrever
        }
        
        // Salvar os dados no Supabase (lógica adaptada de webhook-compra.php)
        $time = $payload['time'] ?? null;
        $resource_id = $payload['resource']['id'] ?? null;
        $customer_name = $payload['resource']['customer']['data']['name'] ?? null;
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
            // Verificar se o webhook já foi inserido para evitar duplicatas
            $existingWebhooks = supabase_request('yampi_webhooks?yampi_order_id=eq.' . $resource_id . '&event=eq.' . urlencode($event), 'GET');
            if (empty($existingWebhooks)) {
                supabase_request('yampi_webhooks', 'POST', $data_to_insert);
            } else {
                // Se já existe, apenas atualiza (se necessário) ou ignora
                // Para este caso, vamos considerar que já está processado
                error_log("Webhook de evento '{$event}' para o pedido {$resource_id} já existe. Ignorando inserção.");
            }

            // Processar produtos e atualizar créditos/marcar como entregue apenas para 'order.paid'
            if ($event === 'order.paid') {
                if (isset($payload['resource']['items']['data']) && is_array($payload['resource']['items']['data'])) {
                    $customer_id = $payload['resource']['customer']['data']['id'] ?? null;
                    if ($customer_id && $customer_email && $resource_id) {
                        foreach ($payload['resource']['items']['data'] as $item) {
                            $product_data = [
                                'customer_id' => $customer_id,
                                'customer_email' => $customer_email,
                                'order_id' => $resource_id,
                                'product_id' => $item['product_id'] ?? null,
                                'product_name' => $item['sku']['data']['title'] ?? null,
                                'sku' => $item['sku']['data']['sku'] ?? null,
                                'quantity' => $item['quantity'] ?? 1,
                                'price' => $item['price'] ?? 0.00,
                                'is_active' => true,
                                'is_delivered' => false // Inicialmente como não entregue
                            ];

                            // Verifica se o produto já foi inserido para este pedido
                            $existingProducts = supabase_request('user_products?order_id=eq.' . $resource_id . '&product_id=eq.' . $product_data['product_id'], 'GET');
                            if (empty($existingProducts)) {
                                supabase_request('user_products', 'POST', $product_data);
                            } else {
                                error_log("Produto '{$product_data['product_name']}' para o pedido {$resource_id} já existe. Ignorando inserção.");
                            }

                            // Se for um produto de crédito, atualiza os créditos do usuário e marca como entregue
                            if (isset($product_data['sku']) && in_array($product_data['sku'], ['2VTUFBAXC'])) { // Adicione outros SKUs de crédito aqui
                                if (update_user_credits($customer_email, $product_data['sku'], $product_data['quantity'])) {
                                    // Marcar como entregue na tabela user_products
                                    supabase_request('user_products?order_id=eq.' . $resource_id . '&product_id=eq.' . $product_data['product_id'], 'PATCH', ['is_delivered' => false]);
                                }
                            }
                        }
                    }
                }
            }
            
            // Mover o arquivo para o diretório de concluídos
            if (rename($filePath, $destinationPath)) {
                $processedCount++;
                $processedFiles[] = $destinationPath; // Adiciona ao array para evitar duplicatas de processamento
            } else {
                $errors[] = "Falha ao mover o arquivo: " . basename($filePath);
                error_log("Falha ao mover o arquivo de log: " . $filePath . " para " . $destinationPath);
            }

        } catch (Exception $e) {
            $errors[] = "Erro ao salvar no Supabase ou processar arquivo " . basename($filePath) . ": " . $e->getMessage();
            error_log("Erro em verificar_pagamentos.php: " . $e->getMessage());
        }
    }

    if ($processedCount > 0) {
        $_SESSION['success_message'] = "Processado " . $processedCount . " arquivo(s) de log com sucesso.";
    }
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
    }

    // Redirecionar de volta para a página de créditos ou dashboard
    header('Location: ../../dashboard');
    exit();

} else {
    // Se não for POST, redirecionar
    header('Location: ../../dashboard');
    exit();
}
?>