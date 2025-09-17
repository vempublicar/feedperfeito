<?php
require_once 'config/session.php';
$isUser = isUserLoggedIn();
$isAdmin = isAdminLoggedIn();

if (!$isUser && !$isAdmin) {
    die('Acesso negado.');
}
define('BASE_PATH', __DIR__ . '/');

function sanitizeFilePath($path) {
    $path = str_replace(['../', './'], '', $path);
    return trim($path, '/');
}

$fileParam = $_GET['file'] ?? '';

if (empty($fileParam)) {
    die('Nenhum arquivo especificado para download.');
}

// Decodifica JSON (um ou vários arquivos)
$decodedFiles = json_decode(urldecode($fileParam), true);

// Se falhar, tenta com stripslashes (para casos com \ escapados)
if (is_null($decodedFiles)) {
    $decodedFiles = json_decode(stripslashes(urldecode($fileParam)), true);
}

if (is_array($decodedFiles) && !empty($decodedFiles)) {
    // ✅ Vários arquivos
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Seus Arquivos para Download - FeedPerfeito</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <style>
            body { background-color: #f3f4f6; }
            .container { max-width: 960px; margin: 0 auto; padding: 20px; }
            .card { background-color: white; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; }
            .thank-you-message { text-align: center; margin-bottom: 2rem; }
            .image-card { border: 1px solid #e5e7eb; border-radius: 0.375rem; overflow: hidden; }
            .image-card img { width: 100%; height: 200px; object-fit: cover; }
            .download-btn { background-color: #1a202c; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; text-decoration: none; display: block; text-align: center; margin-top: 0.5rem; }
            .download-btn:hover { background-color: #2d3748; }
            .file-icon-container { height: 200px; display: flex; align-items: center; justify-content: center; background-color: #f0f0f0; }
        </style>
    </head>
    <body class="flex items-center justify-center min-h-screen">
        <div class="container">
            <div class="card text-center">
                <h1 class="text-2xl font-bold text-black m-auto">
                    <span class="font-bold" style="color: rgb(147, 51, 234);">Feed</span><span class="font-light">Perfeito</span>
                </h1>
                <hr>
                <div class="thank-you-message">
                    <h1 class="text-3xl font-bold text-gray-800 mb-1 mt-6"><b style="color: rgb(147, 51, 234);">Juntos</b>, fortalecendo sua empresa!</h1>
                    <p class="text-gray-600">Seus arquivos estão prontos para download, agradecemos a aquisição.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php foreach ($decodedFiles as $fileUrl): ?>
                        <?php
                            $fileName = basename($fileUrl);
                            $displayUrl = $_SESSION['base_url'] .$fileUrl; // URL para exibir a imagem ou o ícone
                            $downloadLink = $_SESSION['base_url'] . '/api/download.php?file=' . urlencode($fileUrl); // URL para o download real

                            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        ?>
                        <div class="image-card">
                            <?php if ($isImage): ?>
                                <img src="<?php echo htmlspecialchars($displayUrl, ENT_QUOTES); ?>" alt="<?php echo $fileName; ?>">
                            <?php else: ?>
                                <div class="file-icon-container">
                                    <i class="fas fa-file text-gray-400 text-6xl"></i>
                                </div>
                            <?php endif; ?>
                            <div class="p-2">
                                <p class="text-sm text-gray-700 truncate"><?php echo $fileName; ?></p>
                                <a href="<?php echo htmlspecialchars($downloadLink, ENT_QUOTES); ?>" 
                                class="download-btn"
                                style="background-color: rgb(147, 51, 234); hover:background-color: rgb(120, 40, 200);"
                                >
                                    <i class="fas fa-download mr-2"></i> Download
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
} else {
    // ✅ Um único arquivo
    $is_url = filter_var($fileParam, FILTER_VALIDATE_URL);

    if ($is_url) {
        $downloadLink = htmlspecialchars($fileParam, ENT_QUOTES);
        $fileName = basename(parse_url($fileParam, PHP_URL_PATH));
    } else {
        $sanitizedFile = sanitizeFilePath($fileParam);
        $filePath = BASE_PATH . $sanitizedFile;

        if (!file_exists($filePath) || !is_readable($filePath)) {
            die('Arquivo não encontrado ou sem permissão de leitura: ' . htmlspecialchars($sanitizedFile));
        }
        $downloadLink = htmlspecialchars($sanitizedFile, ENT_QUOTES);
        $fileName = basename($filePath);
    }
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Seu Arquivo para Download - FeedPerfeito</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <style>
            body { background-color: #f3f4f6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .card { background-color: white; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; text-align: center; }
            .download-button { display: inline-block; padding: 15px 30px; background-color: #1a202c; color: white; border-radius: 8px; font-size: 1.2em; transition: background-color 0.3s; margin-top: 20px; text-decoration:none; }
            .download-button:hover { background-color: #2d3748; }
        </style>
    </head>
    <body class="flex items-center justify-center min-h-screen">
        <div class="container">
            
            <div class="card">
                <h1 class="text-2xl font-bold text-black m-auto">
                    <span class="font-bold" style="color: rgb(147, 51, 234);">Feed</span><span class="font-light">Perfeito</span>
                </h1>
                <hr>
                <div class="thank-you-message">
                    <h1 class="text-3xl font-bold text-gray-800 mb-1 mt-6"><b style="color: rgb(147, 51, 234);">Juntos</b>, fortalecendo sua empresa!</h1>
                    <p class="text-gray-600">Seu arquivo está pronto para download, agradecemos a aquisição.</p>
                </div>
                <a href="<?php echo $downloadLink; ?>" download="<?php echo $fileName; ?>" class="download-button">
                    <i class="fas fa-download mr-2"></i> Baixar Arquivo
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>
