<?php
require_once 'models/BaseModel.php';

class Service extends BaseModel {
    protected $table = 'services';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Get all active services
    public function getActiveServices() {
        try {
            return supabase_request($this->table . '?is_active=eq.true&order=title.asc'); // Calls the global function
        } catch (Exception $e) {
            error_log("Error getting active services: " . $e->getMessage());
            return [];
        }
    }
    
    // Get service by ID
    public function getServiceById($id) {
        try {
            $result = supabase_request($this->table . '?id=eq.' . $id); // Calls the global function
            return $result ? $result[0] : null;
        } catch (Exception $e) {
            error_log("Error getting service by ID: " . $e->getMessage());
            return null;
        }
    }
    
    // Get services by type (BRL, CREDITS, QUOTE)
    public function getServicesByType($type) {
        try {
            return supabase_request($this->table . '?price_type=eq.' . urlencode($type) . '&is_active=eq.true'); // Calls the global function
        } catch (Exception $e) {
            error_log("Error getting services by type: " . $e->getMessage());
            return [];
        }
    }
}
?>