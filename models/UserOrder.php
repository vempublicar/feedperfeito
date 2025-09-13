<?php
require_once 'models/BaseModel.php';

class UserOrder extends BaseModel {
    protected $table = 'user_orders';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Get orders by user ID
    public function getOrdersByUser($userId) {
        try {
            return supabase_request($this->table . '?user_id=eq.' . $userId . '&order=created_at.desc'); // Calls the global function
        } catch (Exception $e) {
            error_log("Error getting orders by user: " . $e->getMessage());
            return [];
        }
    }
    
    // Get orders by status
    public function getOrdersByStatus($status) {
        try {
            return supabase_request($this->table . '?status=eq.' . urlencode($status) . '&order=created_at.desc'); // Calls the global function
        } catch (Exception $e) {
            error_log("Error getting orders by status: " . $e->getMessage());
            return [];
        }
    }
    
    // Get orders with user information
    public function getOrdersWithUser() {
        try {
            return supabase_request($this->table . '?select=*,profiles(name,email)&order=created_at.desc'); // Calls the global function
        } catch (Exception $e) {
            error_log("Error getting orders with user info: " . $e->getMessage());
            return [];
        }
    }
    
    // Update order status
    public function updateStatus($orderId, $status) {
        return $this->update($orderId, ['status' => $status]);
    }
}
?>