<?php
require_once __DIR__ . '/../../config/database.php';

if (!function_exists('insert')) {
    function insert($table, $data) {
        try {
            return supabase_request($table, 'POST', $data);
        } catch (Exception $e) {
            error_log("Error inserting record into " . $table . ": " . $e->getMessage());
            return $e ;
        }
    }
}
?>