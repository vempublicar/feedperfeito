

<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php'; // Adicionado para acessar funções de sessão

if (!function_exists('get_user_products')) {
    function get_user_products($customer_email) {
        try {
            // Consultar a tabela 'user_products' usando o customer_email
            return supabase_request('user_products?customer_email=eq.' . urlencode($customer_email) . '&order=created_at.desc');
        } catch (Exception $e) {
            error_log("Error getting user products for email " . $customer_email . ": " . $e->getMessage());
            return [];
        }
    }
}
?>