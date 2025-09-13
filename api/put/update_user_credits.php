<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php'; // Para atualizar a sessão

if (!function_exists('update_user_credits')) {
    /**
     * Atualiza a quantidade de créditos de um usuário e a sessão.
     *
     * @param string $user_email O e-mail do usuário.
     * @param string $sku O SKU do produto que representa a quantidade de créditos.
     * @param int $quantity A quantidade de produtos comprados.
     * @return bool True se a atualização for bem-sucedida, false caso contrário.
     */
    function update_user_credits($user_email, $sku, $quantity) {
      
            // Mapeamento de SKU para créditos
            $sku_credits_map = [
                '2VTUFBAXC' => 1200, // Exemplo: SKU para 1200 créditos
                // Adicione outros SKUs e seus respectivos créditos aqui
            ];

            if (!isset($sku_credits_map[$sku])) {
                error_log("SKU desconhecido para atualização de créditos: " . $sku);
                return false;
            }

            $credits_to_add = $sku_credits_map[$sku] * $quantity;
return $credits_to_add;
            // 1. Obter o usuário pelo e-mail
    
    }
}
?>