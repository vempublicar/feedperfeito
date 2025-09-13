<?php
// Supabase configuration
// Função para carregar variáveis de ambiente de um arquivo .env
if (!function_exists('loadEnv')) {
    function loadEnv($path) {
        if (!file_exists($path)) {
            return false;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) {
                continue;
            }
            list($name, $value) = explode('=', $line, 2);
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
            putenv(sprintf('%s=%s', $name, $value));
        }
        return true;
    }
}

// Carrega as variáveis do .env
loadEnv(__DIR__ . '/../.env');

define('SUPABASE_URL', getenv('SUPABASE_URL'));
define('SUPABASE_KEY', getenv('SUPABASE_KEY'));
define('SUPABASE_ANON_KEY', getenv('SUPABASE_ANON_KEY'));

// Function to make API requests to Supabase
function supabase_request($endpoint, $method = 'GET', $data = null) {
    $url = SUPABASE_URL . '/rest/v1/' . $endpoint;
    
    $headers = [
        'apikey: ' . SUPABASE_KEY,
        'Authorization: Bearer ' . SUPABASE_KEY,
        'Content-Type: application/json',
        'Prefer: return=representation'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data && $method !== 'GET') { // Log data only if it exists and it's not a GET request
        $json_data = json_encode($data);
        error_log("Supabase Request - URL: " . $url . ", Method: " . $method . ", Data: " . $json_data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    } else if ($method === 'GET') {
        error_log("Supabase Request - URL: " . $url . ", Method: " . $method);
    }
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // SSL certificate options
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desabilitado para testes locais
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);     // Desabilitado para testes locais
    // curl_setopt($ch, CURLOPT_CAINFO, 'C:\xampp\apache\bin\curl-ca-bundle.crt');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($method === 'GET') { // Log response for GET requests
        error_log("Supabase Response (GET) - HTTP Code: " . $httpCode . ", Response: " . $response);
    }

    if (curl_error($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new Exception("cURL Error: " . $error);
    }
    
    curl_close($ch);
    
    if ($httpCode >= 400) {
        throw new Exception("HTTP Error: " . $httpCode . " - Response: " . $response);
    }
    
    return json_decode($response, true);
}


// Function to make API requests to Supabase Auth
function supabase_auth_request($endpoint, $method = 'POST', $data = null) {
    $url = SUPABASE_URL . '/auth/v1/' . $endpoint;
    
    $headers = [
        'Content-Type: application/json',
        'apikey: ' . SUPABASE_KEY,
        'Authorization: Bearer ' . SUPABASE_KEY, // Use SUPABASE_KEY for Authorization
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
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
    
    return $responseData;
}

?>