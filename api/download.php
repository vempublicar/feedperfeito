<?php
require_once '../config/session.php';
$isUser = isUserLoggedIn();
$isAdmin = isAdminLoggedIn();

if (!$isUser && !$isAdmin) {
    die('Acesso negado.');
}

define('BASE_PATH', __DIR__ . '/../'); // Ajustado para ser relativo ao diretório raiz

function sanitizeFilePath($path) {
    // Remove qualquer tentativa de navegação de diretório
    $path = str_replace(['../', './'], '', $path);
    // Remove barras no início e fim
    return trim($path, '/');
}

$fileParam = $_GET['file'] ?? '';

if (empty($fileParam)) {
    die('Nenhum arquivo especificado para download.');
}

// Decodifica o caminho do arquivo
$filePath = BASE_PATH . sanitizeFilePath(urldecode($fileParam));

// Verifica se o arquivo existe e é legível
if (!file_exists($filePath) || !is_readable($filePath)) {
    die('Arquivo não encontrado ou sem permissão de leitura: ' . htmlspecialchars($fileParam));
}

// Define os cabeçalhos para forçar o download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
flush(); // Libera o output buffer
readfile($filePath); // Lê o arquivo e o envia para o browser
exit;
?>