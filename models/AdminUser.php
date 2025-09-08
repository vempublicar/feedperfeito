<?php
require_once 'models/BaseModel.php';

class AdminUser extends BaseModel {
    protected $table = 'admin_users';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Find admin user by email
    public function findByEmail($email) {
        try {
            $result = supabase_request($this->table . '?email=eq.' . urlencode($email));
            return $result ? $result[0] : null;
        } catch (Exception $e) {
            error_log("Error finding admin user by email: " . $e->getMessage());
            return null;
        }
    }
    
    // Authenticate admin user
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
            return $user;
        }
        
        return null;
    }
    
    // Create admin user with hashed password
    public function createAdminUser($name, $email, $password, $role = 'admin') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role,
            'is_active' => true
        ];
        
        return $this->create($data);
    }
}
?>