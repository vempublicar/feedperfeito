<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/api/put/update_user_credits.php';

echo "Iniciando processamento de pedidos pagos para produtos...\n";

try {
    // Buscar todos os eventos 'order.paid' da tabela yampi_webhooks
    $paid_orders = supabase_request('yampi_webhooks?event=eq.order.paid');

    if (!empty($paid_orders)) {
        foreach ($paid_orders as $order_webhook) {
            $raw_payload = json_decode($order_webhook['raw_payload'], true);

            // Verificar se o payload tem a estrutura esperada e contém itens
            if (isset($raw_payload['resource']['items']['data']) && is_array($raw_payload['resource']['items']['data'])) {
                $customer_id = $raw_payload['resource']['customer']['data']['id'] ?? null;
                $customer_email = $raw_payload['resource']['customer']['data']['email'] ?? null;
                $order_id = $raw_payload['resource']['id'] ?? null;

                if ($customer_id && $order_id && $customer_email) {
                    foreach ($raw_payload['resource']['items']['data'] as $item) {
                        $product_data = [
                            'customer_id' => $customer_id,
                            'customer_email' => $customer_email,
                            'order_id' => $order_id,
                            'product_id' => $item['product_id'] ?? null,
                            'product_name' => $item['sku']['data']['title'] ?? null,
                            'sku' => $item['sku']['data']['sku'] ?? null,
                            'quantity' => $item['quantity'] ?? 1,
                            'price' => $item['price'] ?? 0.00,
                            'is_active' => true,
                            'is_delivered' => false // Inicialmente como não entregue
                        ];

                        // Inserir na tabela user_products
                        try {
                            supabase_request('user_products', 'POST', $product_data);
                            echo "Produto '{$product_data['product_name']}' do pedido {$order_id} inserido para o cliente {$customer_id}.\n";

                            // Se for um produto de crédito, atualiza os créditos do usuário e marca como entregue
                            if (isset($product_data['sku']) && in_array($product_data['sku'], ['2VTUFBAXC'])) { // Adicione outros SKUs de crédito aqui
                                if (update_user_credits($customer_email, $product_data['sku'], $product_data['quantity'])) {
                                    // Marcar como entregue na tabela user_products
                                    supabase_request('user_products?order_id=eq.' . $order_id . '&product_id=eq.' . $product_data['product_id'], 'PATCH', ['is_delivered' => true]);
                                    echo "Créditos atualizados e produto entregue para {$customer_email}.\n";
                                } else {
                                    echo "Falha ao atualizar créditos para {$customer_email}.\n";
                                }
                            }
                        } catch (Exception $e) {
                            echo "Erro ao inserir produto '{$product_data['product_name']}' do pedido {$order_id}: " . $e->getMessage() . "\n";
                        }
                    }
                } else {
                    echo "customer_id, order_id ou customer_email não encontrados no payload do webhook {$order_webhook['id']}.\n";
                }
            } else {
                echo "Nenhum item encontrado no raw_payload do webhook {$order_webhook['id']}.\n";
            }
        }
    } else {
        echo "Nenhum pedido 'order.paid' encontrado para processar.\n";
    }
} catch (Exception $e) {
    echo "Erro geral ao buscar pedidos pagos: " . $e->getMessage() . "\n";
}

echo "Processamento de produtos concluído.\n";

?>