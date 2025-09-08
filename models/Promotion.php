<?php
require_once 'models/BaseModel.php';

class Promotion extends BaseModel {
    protected $table = 'promotions';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Get all active promotions
    public function getActivePromotions() {
        try {
            $now = date('Y-m-d H:i:s');
            return supabase_request($this->table . '?is_active=eq.true&expires_at=gt.' . urlencode($now) . '&order=created_at.desc');
        } catch (Exception $e) {
            error_log("Error getting active promotions: " . $e->getMessage());
            return [];
        }
    }
    
    // Get promotion by ID
    public function getPromotionById($id) {
        try {
            $result = supabase_request($this->table . '?id=eq.' . $id);
            return $result ? $result[0] : null;
        } catch (Exception $e) {
            error_log("Error getting promotion by ID: " . $e->getMessage());
            return null;
        }
    }
}
?>