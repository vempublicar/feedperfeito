<?php
require_once 'models/BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Find user by email
    public function findByEmail($email) {
        try {
            $result = supabase_request($this->table . '?email=eq.' . urlencode($email));
            return $result ? $result[0] : null;
        } catch (Exception $e) {
            error_log("Error finding user by email: " . $e->getMessage());
            return null;
        }
    }
    
    // Authenticate user
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return null;
    }
    
    // Create user with hashed password
    public function createUser($name, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'credits' => 0
        ];
        
        return $this->create($data);
    }
    
    // Update user credits
    public function updateCredits($userId, $credits) {
        return $this->update($userId, ['credits' => $credits]);
    }
    
    // Add credits to user
    public function addCredits($userId, $credits) {
        $user = $this->find($userId);
        if ($user) {
            $newCredits = $user['credits'] + $credits;
            return $this->updateCredits($userId, $newCredits);
        }
        return false;
    }
}
?>