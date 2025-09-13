<?php
require_once __DIR__ . '/../../config/database.php';

if (!function_exists('mark_product_as_delivered')) {
    /**
     * Marca um produto como entregue na tabela user_products.
     *
     * @param string $product_id O ID do produto na tabela user_products.
     * @return bool True se a atualização for bem-sucedida, false caso contrário.
     */
    function mark_product_as_delivered($product_id) {
        try {
            $update_data = ['is_delivered' => true];
            $response = supabase_request('user_products?id=eq.' . urlencode($product_id), 'PATCH', $update_data);
            
            if (!empty($response)) {
                return true;
            } else {
                error_log("Falha ao marcar produto " . $product_id . " como entregue.");
                return false;
            }
        } catch (Exception $e) {
            error_log("Erro ao marcar produto " . $product_id . " como entregue: " . $e->getMessage());
            return false;
        }
    }
}
?>