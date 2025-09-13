<?php
require_once __DIR__ . '/../../config/database.php';

if (!function_exists('get_undelivered_products')) {
    function get_undelivered_products($customer_email) {
        try {
            return supabase_request('user_products?customer_email=eq.' . urlencode($customer_email) . '&is_delivered=is.null&order=created_at.asc');
        } catch (Exception $e) {
            error_log("Error getting undelivered products for " . $customer_email . ": " . $e->getMessage());
            return [];
        }
    }
}
?>