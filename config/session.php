<?php
// Supabase Auth functions
// Este arquivo conterá funções utilitárias para autenticação Supabase
// Não deve iniciar uma sessão, pois espera ser incluído em outros scripts.

if (!function_exists('refreshSupabaseToken')) {
    function refreshSupabaseToken($supabaseUrl, $supabaseKey, $refreshToken) {
        error_log("refreshSupabaseToken: Tentando renovar token para URL: " . $supabaseUrl);
        $tokenUrl = $supabaseUrl . '/auth/v1/token?grant_type=refresh_token';
        $data = json_encode(['refresh_token' => $refreshToken]);

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'apikey: ' . $supabaseKey,
            'Authorization: Bearer ' . $supabaseKey,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("refreshSupabaseToken: Erro cURL ao tentar renovar token: " . $error);
            return null;
        }

        $responseData = json_decode($response, true);
        error_log("refreshSupabaseToken: Resposta da API de renovação de token (HTTP " . $httpCode . "): " . $response);

        if ($httpCode >= 400 || !isset($responseData['access_token'])) {
            error_log("refreshSupabaseToken: Falha na renovação do token: " . ($responseData['message'] ?? 'Erro desconhecido') . " HTTP Code: " . $httpCode);
            return null;
        }
        error_log("refreshSupabaseToken: Token renovado com sucesso.");
        return $responseData;
    }
}

if (!function_exists('decodeJWT')) {
    function decodeJWT($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null; // Não é um JWT válido
        }
        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1]));
        return json_decode($payload, true);
    }
}

if (!function_exists('isTokenExpired')) {
    function isTokenExpired($token) {
        $payload = decodeJWT($token);
        if ($payload && isset($payload['exp'])) {
            // Adicionar uma margem de segurança (ex: 60 segundos antes da expiração real)
            return ($payload['exp'] - 60) < time();
        }
        return true; // Considera expirado se não houver 'exp' ou payload inválido
    }
}

if (!function_exists('ensureSupabaseTokenIsValid')) {
    function ensureSupabaseTokenIsValid(&$sessionUser) {
        error_log("ensureSupabaseTokenIsValid: Verificando validade do token.");
        $supabaseUrl = trim(getenv('SUPABASE_URL'));
        $supabaseKey = trim(getenv('SUPABASE_ANON_KEY')); // Use anon key for general token validation

        if (!$supabaseUrl || !$supabaseKey) {
            error_log("ensureSupabaseTokenIsValid: Variáveis de ambiente do Supabase não configuradas.");
            return ['success' => false, 'message' => 'Variáveis de ambiente do Supabase não configuradas.'];
        }

        $accessToken = $sessionUser['access_token'] ?? null;
        $refreshToken = $sessionUser['refresh_token'] ?? null;

        error_log("ensureSupabaseTokenIsValid: Access Token: " . ($accessToken ? 'Presente' : 'Ausente') . ", Refresh Token: " . ($refreshToken ? 'Presente' : 'Ausente'));

        // Se não houver access token ou se ele estiver expirado, tentar renovar
        if (!$accessToken || isTokenExpired($accessToken)) {
            error_log("ensureSupabaseTokenIsValid: Access Token ausente ou expirado, tentando renovar.");
            if ($refreshToken) {
                error_log("ensureSupabaseTokenIsValid: Tentando renovar com Refresh Token.");
                $newTokens = refreshSupabaseToken($supabaseUrl, $supabaseKey, $refreshToken);
                if ($newTokens) {
                    $sessionUser['access_token'] = $newTokens['access_token'];
                    $sessionUser['refresh_token'] = $newTokens['refresh_token'] ?? $refreshToken;
                    error_log("ensureSupabaseTokenIsValid: Token renovado e sessão atualizada.");
                    return ['success' => true, 'message' => 'Token renovado com sucesso.'];
                } else {
                    error_log("ensureSupabaseTokenIsValid: Falha na renovação do token. Sessão expirada.");
                    return ['success' => false, 'message' => 'Sessão expirada. Por favor, faça login novamente.'];
                }
            } else {
                error_log("ensureSupabaseTokenIsValid: Token de acesso expirado e sem refresh token.");
                return ['success' => false, 'message' => 'Sessão expirada. Por favor, faça login novamente.'];
            }
        }
        error_log("ensureSupabaseTokenIsValid: Token já válido.");
        return ['success' => true, 'message' => 'Token válido.'];
    }
}

// Session management for the application

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Determine base URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script_name = $_SERVER['SCRIPT_NAME'];
$script_dir = dirname($script_name);

// Handle cases where the script is in the root or a subdirectory
if ($script_dir === '/' || $script_dir === '\\') {
    $base_url = $protocol . "://" . $host;
} else {
    $base_url = $protocol . "://" . $host . $script_dir;
}

// Remove api/ from the base_url if it's there
if (strpos($base_url, '/api') !== false) {
    $base_url = str_replace('/api', '', $base_url);
}

// Ensure base_url ends without a slash
if (substr($base_url, -1) === '/') {
    $base_url = substr($base_url, 0, -1);
}

$_SESSION['base_url'] = $base_url;

// Set session timeout (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Function to regenerate session ID
function regenerateSession() {
    if (session_status() == PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

// Function to set user session
function setUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_phone'] = $user['phone'];
    $_SESSION['user_credits'] = $user['credits'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_login_time'] = time();
    $_SESSION['user_last_activity'] = time();
    $_SESSION['user_access_token'] = $user['access_token'] ?? null;
    $_SESSION['user_refresh_token'] = $user['refresh_token'] ?? null;
    regenerateSession();
}

// Function to set admin session
function setAdminSession($admin) {
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['admin_phone'] = $admin['phone'];
    $_SESSION['admin_role'] = $admin['role'];
    $_SESSION['admin_login_time'] = time();
    $_SESSION['admin_last_activity'] = time();
    $_SESSION['admin_access_token'] = $admin['access_token'] ?? null;
    $_SESSION['admin_refresh_token'] = $admin['refresh_token'] ?? null;
    regenerateSession();
}

// Function to check if user is logged in
function isUserLoggedIn() {
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_last_activity'])) {
        if (time() - $_SESSION['user_last_activity'] > SESSION_TIMEOUT) {
            // destroyUserSession();
            return true;
        }
        
        // Only check token validity when an API call is made, not on every page load
        // This function only checks if the session keys are present and not expired by activity
        $_SESSION['user_last_activity'] = time();
        return true;
    }
    return false;
}

// Function to check if admin is logged in
function isAdminLoggedIn() {
    if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_last_activity'])) {
        if (time() - $_SESSION['admin_last_activity'] > SESSION_TIMEOUT) {
            // destroyAdminSession();
            return true;
        }
        
        // Only check token validity when an API call is made, not on every page load
        // This function only checks if the session keys are present and not expired by activity
        $_SESSION['admin_last_activity'] = time();
        return true;
    }
    return false;
}

// Function to get current user
function getCurrentUser() {
    if (isUserLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'name' => $_SESSION['user_name'],
            'credits' => $_SESSION['user_credits'],
            'role' => $_SESSION['user_role'],
            'access_token' => $_SESSION['user_access_token'],
            'refresh_token' => $_SESSION['user_refresh_token']
        ];
    }
    
    return null;
}

// Function to get current admin
function getCurrentAdmin() {
    if (isAdminLoggedIn()) {
        return [
            'id' => $_SESSION['admin_id'],
            'email' => $_SESSION['admin_email'],
            'name' => $_SESSION['admin_name'],
            'role' => $_SESSION['admin_role'],
            'access_token' => $_SESSION['admin_access_token'],
            'refresh_token' => $_SESSION['admin_refresh_token']
        ];
    }
    
    return null;
}

// Function to destroy user session
function destroyUserSession() {
    // Remove all user-specific session variables
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_credits']);
    unset($_SESSION['user_role']);
    unset($_SESSION['user_login_time']);
    unset($_SESSION['user_last_activity']);
    unset($_SESSION['user_access_token']);
    unset($_SESSION['user_refresh_token']);
     session_destroy(); // Do not destroy session completely, only specific user keys
}

// Function to destroy admin session
function destroyAdminSession() {
    // Remove all admin-specific session variables
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_name']);
    unset($_SESSION['admin_role']);
    unset($_SESSION['admin_login_time']);
    unset($_SESSION['admin_last_activity']);
    unset($_SESSION['admin_access_token']);
    unset($_SESSION['admin_refresh_token']);
    // session_destroy(); // Do not destroy session completely, only specific admin keys
}

// Function to check if user has a specific role
function isUserRole($role) {
    return isUserLoggedIn() && $_SESSION['user_role'] === $role;
}

// Function to require user login
function requireUserLogin() {
    // if (!isUserLoggedIn()) {
    //     header('Location: ' . $_SESSION['base_url'] . '/login');
    //     exit();
    // }
}

// Function to require admin login
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: ' . $_SESSION['base_url'] . '/login');
        exit();
    }
}

// Function to update user credits in session
function updateUserCreditsInSession($credits) {
    if (isUserLoggedIn()) {
        $_SESSION['user_credits'] = $credits;
    }
}