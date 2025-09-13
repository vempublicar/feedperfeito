<?php

require_once __DIR__ . '/config/database.php';

function hmac_signature(array $body, $webHookSecret)
{
    $payload = json_encode($body);
    return base64_encode(hash_hmac('sha256', $payload, $webHookSecret, true));
}

// Chave secreta do webhook (substitua pela sua chave real)
$webHookSecret = 'wh_ccODtKWk7E4ANDyF6N23Dn7ng8tCTv47D79xg'; // TODO: Substituir pela chave real

// Recebe o conteúdo RAW do POST
$input = file_get_contents('php://input');
$payload = json_decode($input, true);

// Verifica se o payload é válido
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    echo 'Erro ao decodificar JSON: ' . json_last_error_msg();
    exit();
}

// Obtém a assinatura do cabeçalho X-Hub-Signature
$x_hub_signature = isset($_SERVER['HTTP_X_HUB_SIGNATURE']) ? $_SERVER['HTTP_X_HUB_SIGNATURE'] : '';

// Calcula a assinatura esperada
$expected_signature = hmac_signature($payload, $webHookSecret);

// Compara as assinaturas
if ($x_hub_signature !== $expected_signature) {
    http_response_code(403); // Forbidden
    echo 'Assinatura inválida.';
    exit();
}

// Salva o payload em um arquivo de log
$logDir = __DIR__ . '/logs/'; // Alterado para a pasta 'logs'
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}

$logFileName = $logDir . 'webhook_' . date('Y-m-d_H-i-s') . '.txt';
file_put_contents($logFileName, $input);

// Extrai e salva os dados no Supabase
$event = $payload['event'] ?? null;
$time = $payload['time'] ?? null;
$resource_id = $payload['resource']['id'] ?? null;
$customer_email = $payload['resource']['customer']['data']['email'] ?? null;
$customer_name = $payload['resource']['customer']['data']['name'] ?? null;
$order_number = $payload['resource']['number'] ?? null;
$value_total = $payload['resource']['value_total'] ?? null;
require_once __DIR__ . '/api/put/update_user_credits.php';

// ... (código existente) ...

$status_alias = $payload['resource']['status']['data']['alias'] ?? null;
$gateway_transaction_id = $payload['resource']['transactions']['data'][0]['gateway_transaction_id'] ?? null;
$payment_method = $payload['resource']['payments'][0]['alias'] ?? null;
$customer_email = $payload['resource']['customer']['data']['email'] ?? null; // Adiciona o email do cliente aqui

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
    'payment_method' => $payment_method, // Adicionado o método de pagamento
    'raw_payload' => json_encode($payload) // Salva o payload completo
];

try {
    supabase_request('yampi_webhooks', 'POST', $data_to_insert);
    // Adicionar um log para indicar que a inserção no Supabase foi bem-sucedida
    file_put_contents($logFileName, "\n--- Supabase Insert Success ---\n", FILE_APPEND);

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

                    try {
                        supabase_request('user_products', 'POST', $product_data);
                        file_put_contents($logFileName, "Produto '{$product_data['product_name']}' do pedido {$resource_id} inserido.\n", FILE_APPEND);

                        // Se for um produto de crédito, atualiza os créditos do usuário e marca como entregue
                        if (isset($product_data['sku']) && in_array($product_data['sku'], ['2VTUFBAXC'])) { // Adicione outros SKUs de crédito aqui
                            if (update_user_credits($customer_email, $product_data['sku'], $product_data['quantity'])) {
                                // Marcar como entregue na tabela user_products
                                supabase_request('user_products?order_id=eq.' . $resource_id . '&product_id=eq.' . $product_data['product_id'], 'PATCH', ['is_delivered' => true]);
                                file_put_contents($logFileName, "Créditos atualizados e produto entregue para {$customer_email}.\n", FILE_APPEND);
                            } else {
                                file_put_contents($logFileName, "Falha ao atualizar créditos para {$customer_email}.\n", FILE_APPEND);
                            }
                        }
                    } catch (Exception $e) {
                        file_put_contents($logFileName, "Erro ao inserir produto '{$product_data['product_name']}' do pedido {$resource_id}: " . $e->getMessage() . "\n", FILE_APPEND);
                    }
                }
            } else {
                file_put_contents($logFileName, "Nenhum item encontrado no raw_payload do webhook {$order_webhook['id']}.\n", FILE_APPEND);
            }
        } else {
            file_put_contents($logFileName, "customer_id, customer_email ou order_id não encontrados no payload do webhook {$order_webhook['id']}.\n", FILE_APPEND);
        }
    }
} catch (Exception $e) {
    // Adicionar um log para indicar o erro na inserção do Supabase
    file_put_contents($logFileName, "\n--- Supabase Insert Error ---\n" . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500); // Internal Server Error
    echo 'Erro ao salvar dados no Supabase: ' . $e->getMessage();
    exit();
}

http_response_code(200); // OK
echo 'Webhook recebido e processado com sucesso.';

?>
