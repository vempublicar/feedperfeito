<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../config/database.php'; // Needed for supabase_auth_request
require_once __DIR__ . '/../api/get/all.php';
require_once __DIR__ . '/../api/get/by_id.php';
require_once __DIR__ . '/../api/post/insert.php';
require_once __DIR__ . '/../api/put/update.php';
require_once __DIR__ . '/../api/delete/delete.php';

class User extends BaseModel {
    protected $table = 'profiles';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Register user via Supabase Auth
    public function registerUser($email, $password) {
        try {
            $response = supabase_auth_request('signup', 'POST', [
                'email' => $email,
                'password' => $password
            ]);
            return $response;
        } catch (Exception $e) {
            error_log("Error registering user: " . $e->getMessage());
            return null;
        }
    }

    // Login user via Supabase Auth
    public function loginUser($email, $password) {
        try {
            $response = supabase_auth_request('token?grant_type=password', 'POST', [
                'email' => $email,
                'password' => $password
            ]);
            return $response;
        } catch (Exception $e) {
            error_log("Error logging in user: " . $e->getMessage());
            return null;
        }
    }
    
    // Find user by email (from public.users table)
    public function findByEmail($email) {
        try {
            $result = get_all($this->table . '?email=eq.' . urlencode($email));
            return $result ? $result[0] : null;
        } catch (Exception $e) {
            error_log("Error finding user by email in public.users: " . $e->getMessage());
            return null;
        }
    }

}
?>