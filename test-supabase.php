<?php
require_once __DIR__ . '/config/database.php';

// Teste simples de conexão com Supabase
$response = supabase_request('users?limit=1', 'GET');

if (is_array($response)) {
    echo "Conexão com Supabase bem-sucedida!\n";
    echo "Resposta:\n";
    print_r($response);
} else {
    echo "Falha na conexão com Supabase.\n";
    var_dump($response);
}
