<?php
require_once 'models/BaseModel.php';

class Voucher extends BaseModel {
    protected $table = 'vouchers';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Get voucher by code
    public function getVoucherByCode($code) {
        try {
            $result = supabase_request($this->table . '?code=eq.' . urlencode($code)); // Calls the global function
            return $result ? $result[0] : null;
        } catch (Exception $e) {
            error_log("Error getting voucher by code: " . $e->getMessage());
            return null;
        }
    }
    
    // Check if voucher is valid (not used and not expired)
    public function isVoucherValid($code) {
        try {
            $now = date('Y-m-d H:i:s');
            $result = supabase_request($this->table . '?code=eq.' . urlencode($code) . '&is_used=eq.false&expires_at=gt.' . urlencode($now)); // Calls the global function
            return $result ? $result[0] : null;
        } catch (Exception $e) {
            error_log("Error checking voucher validity: " . $e->getMessage());
            return null;
        }
    }
    
    // Use voucher
    public function useVoucher($voucherId, $userId) {
        $data = [
            'is_used' => true,
            'user_id' => $userId,
            'used_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->update($voucherId, $data); // Calls the global function
    }
}
?>