<?php
require_once __DIR__ . '/../../config/database.php';

if (!function_exists('get_by_id')) {
    function get_by_id($table, $id) {
        try {
            $result = supabase_request($table . '?id=eq.' . $id);
            return $result ? $result[0] : null;
        } catch (Exception $e) {
            error_log("Error getting record from " . $table . " with id " . $id . ": " . $e->getMessage());
            return null;
        }
    }
}
?>