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
    public function requestPasswordReset($email) {
        try {
            $response = supabase_auth_request('recover', 'POST', [
                'email' => $email
            ]);
            // Supabase returns 200 even if email doesn't exist to prevent enumeration
            // We can check for a specific message if needed, but for now, assume success if no explicit error
            if (isset($response['msg'])) {
                return $response['msg'];
            }
            return true;
        } catch (Exception $e) {
            error_log("Error requesting password reset: " . $e->getMessage());
            return "Erro ao solicitar a redefinição de senha.";
        }
    }

    public function resetPassword($token, $newPassword) {
        // try {
            $url = SUPABASE_URL . '/auth/v1/user'; // Endpoint para atualizar o usuário
            
            $headers = [
                'Content-Type: application/json',
                'apikey: ' . SUPABASE_KEY,
                'Authorization: Bearer ' . $token, // Usar o token fornecido para autorização
            ];
            
            $data = [
                'password' => $newPassword
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desabilitado para testes locais
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);     // Desabilitado para testes locais
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_error($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new Exception("cURL Error for Auth: " . $error);
            }
            
            curl_close($ch);
            
            $responseData = json_decode($response, true);

            if ($httpCode >= 400) {
                $errorMessage = $responseData['msg'] ?? $responseData['message'] ?? 'Erro desconhecido na autenticação.';
                throw new Exception("HTTP Error for Auth: " . $httpCode . " - " . $errorMessage);
            }
            
            if (isset($responseData['user'])) {
                return true;
            } elseif (isset($responseData['msg'])) {
                return $responseData['msg'];
            }
            return $responseData;
        // } catch (Exception $e) {
        //     error_log("Error resetting password: " . $e->getMessage());
        //     return "Erro ao redefinir a senha.";
        // }
    }

}
?>
