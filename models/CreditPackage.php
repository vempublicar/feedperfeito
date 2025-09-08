<?php
require_once 'models/BaseModel.php';

class CreditPackage extends BaseModel {
    protected $table = 'credit_packages';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Get all active credit packages
    public function getActivePackages() {
        try {
            return supabase_request($this->table . '?is_active=eq.true&order=credits.asc');
        } catch (Exception $e) {
            error_log("Error getting active credit packages: " . $e->getMessage());
            return [];
        }
    }
    
    // Get package by ID
    public function getPackageById($id) {
        try {
            $result = supabase_request($this->table . '?id=eq.' . $id);
            return $result ? $result[0] : null;
        } catch (Exception $e) {
            error_log("Error getting package by ID: " . $e->getMessage());
            return null;
        }
    }
}
?>