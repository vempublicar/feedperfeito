<?php
require_once __DIR__ . '/../../config/database.php';

if (!function_exists('update')) {
    function update($table, $id, $data) {
        try {
            return supabase_request($table . '?id=eq.' . $id, 'PATCH', $data);
        } catch (Exception $e) {
            error_log("Error updating record in " . $table . " with id " . $id . ": " . $e->getMessage());
            return $e;
        }
    }
}
?>