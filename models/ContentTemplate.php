<?php
require_once 'models/BaseModel.php';

class ContentTemplate extends BaseModel {
    protected $table = 'content_templates';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Get all active templates
    public function getActiveTemplates() {
        try {
            return supabase_request($this->table . '?is_active=eq.true');
        } catch (Exception $e) {
            error_log("Error getting active templates: " . $e->getMessage());
            return [];
        }
    }
    
    // Get featured templates
    public function getFeaturedTemplates() {
        try {
            return supabase_request($this->table . '?is_featured=eq.true&is_active=eq.true');
        } catch (Exception $e) {
            error_log("Error getting featured templates: " . $e->getMessage());
            return [];
        }
    }
    
    // Get templates by category
    public function getTemplatesByCategory($category) {
        try {
            return supabase_request($this->table . '?category=eq.' . urlencode($category) . '&is_active=eq.true');
        } catch (Exception $e) {
            error_log("Error getting templates by category: " . $e->getMessage());
            return [];
        }
    }
}
?>