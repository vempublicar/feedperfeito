<?php
require_once __DIR__ . '/../../config/database.php';

if (!function_exists('get_yampi_webhooks')) {
    function get_yampi_webhooks($event_type) {
        try {
            return supabase_request('yampi_webhooks?event=eq.' . $event_type . '&order=created_at.desc');
        } catch (Exception $e) {
            error_log("Error getting Yampi webhooks for event type " . $event_type . ": " . $e->getMessage());
            return [];
        }
    }
}
?>