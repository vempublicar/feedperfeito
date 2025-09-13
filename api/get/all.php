<?php
require_once __DIR__ . '/../../config/database.php';

if (!function_exists('get_all')) {
    function get_all($table) {
        try {
            return supabase_request($table);
        } catch (Exception $e) {
            error_log("Error getting records from " . $table . ": " . $e->getMessage());
            return [];
        }
    }
}
?>