<?php
require_once __DIR__ . '/../../config/database.php';

if (!function_exists('delete')) {
    function delete($table, $id) {
        try {
            return supabase_request($table . '?id=eq.' . $id, 'DELETE');
        } catch (Exception $e) {
            error_log("Error deleting record from " . $table . " with id " . $id . ": " . $e->getMessage());
            return false;
        }
    }
}
?>