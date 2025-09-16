<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FeedPerfeito - Redefinir Senha</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#000000',
            secondary: '#ffffff',
          }
        }
      }
    }
  </script>
  <style>
    body {
      background-color: #ffffff;
    }
  </style>
</head>
<body class="bg-white h-screen flex flex-col">
  <?php
  require_once 'config/session.php';
  $error = $_SESSION['reset_error'] ?? '';
  $success = $_SESSION['reset_success'] ?? '';
  unset($_SESSION['reset_error']);
  unset($_SESSION['reset_success']);

  // PHP will not get hash parameters, so we will handle them with JavaScript
  // Initialize variables to avoid PHP notices, they will be populated by JS
  $access_token = '';
  $expires_at = '';
  ?>
  
  <!-- Header -->
  <header class="bg-white shadow-sm py-4">
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center">
        <div class="flex items-center">
          <h1 class="text-2xl font-bold text-black">
            <span class="font-bold">Feed</span><span class="font-light">Perfeito</span>
          </h1>
        </div>
        <nav class="hidden md:block">
          <ul class="flex space-x-6">
            <li><a href="<?php echo $_SESSION['base_url']; ?>/" class="text-black hover:text-gray-600">Home</a></li>
           </ul>
        </nav>
        <button class="md:hidden text-black">
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-grow flex items-center">
    <div class="container mx-auto px-4 py-8">
      <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
          <h6 class="text-lg text-black mb-2">SUA AGÊNCIA ON DEMAND</h6>
          <div class="w-24 h-0.5 bg-black mx-auto mb-4"></div>
          <h1 class="text-4xl font-bold text-black mb-4">
             <span class="font-light">Definir Nova</span> Senha
          </h1>
          <p class="text-gray-600">Digite e confirme sua nova senha.</p>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-center gap-12">
          <!-- Reset Password Form -->
          <div class="w-full md:w-1/2">
            <div class="bg-white rounded-lg shadow-lg p-8">
              <div class="mb-8">
                <h2 class="text-2xl font-bold text-black mb-2">
                  Redefinir <span class="font-light">Senha</span>
                </h2>
                <div class="w-16 h-0.5 bg-black mb-6"></div>
                
                <?php if ($error): ?>
                  <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                  </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                  <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($success); ?>
                  </div>
                <?php endif; ?>
              </div>
              
              <form id="reset-password-form" action="api/post/reset_password.php" method="post">
                <input type="hidden" name="access_token" id="access_token" value="<?php echo htmlspecialchars($access_token); ?>">
                <input type="hidden" name="expires_at" id="expires_at" value="<?php echo htmlspecialchars($expires_at); ?>">
                
                <div class="mb-6">
                  <input 
                    type="password" 
                    class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent" 
                    name="password" 
                    id="password" 
                    placeholder="Nova Senha" 
                    required
                    minlength="6"
                  >
                </div>
                
                <div class="mb-6">
                  <input 
                    type="password" 
                    class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent" 
                    name="confirm_password" 
                    id="confirm_password" 
                    placeholder="Confirmar Nova Senha" 
                    required
                  >
                </div>
                
                <div class="mb-6">
                  <button 
                    type="submit" 
                    id="form-submit" 
                    class="w-full bg-black text-white py-3 px-4 rounded font-semibold hover:bg-gray-800 transition duration-300"
                  >
                    Redefinir Senha
                  </button>
                </div>
                
                <div class="text-center">
                  <a href="<?php echo $_SESSION['base_url']; ?>/login" class="text-black hover:text-gray-600 underline">Voltar para o Login</a>
                </div>
              </form>
            </div>
          </div>
          
          <!-- Image -->
          <div class="w-full md:w-1/2 hidden md:block">
            <img src="<?php echo $_SESSION['base_url']; ?>/rev.jpg" class="w-full h-96" alt="Imagem de Redefinição de Senha">
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-white py-8 border-t border-gray-200">
    <div class="container mx-auto px-4">
      <div class="text-center">
        <p class="text-gray-600">
          Copyright © 2036 <a href="<?php echo $_SESSION['base_url']; ?>/" class="text-black hover:text-gray-600">FeedPerfeito</a>, desenvolvido por <a href="https://vempublicar.com" class="text-black hover:text-gray-600">VemPublicar</a>. Todos os direitos reservados.
        </p>
      </div>
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const hash = window.location.hash.substring(1); // Remove o '#'
      const params = new URLSearchParams(hash);

      const accessToken = params.get('access_token');
      const expiresAt = params.get('expires_at'); // Supabase já retorna expires_at

      // Remove console.log for production
      // console.log('Hash da URL:', hash);
      // console.log('Access Token (extraído):', accessToken);
      // console.log('Expires At (extraído):', expiresAt);

      const accessTokenInput = document.getElementById('access_token');
      const expiresAtInput = document.getElementById('expires_at');

      // Remove console.log for production
      // if (accessTokenInput) {
      //   console.log('Elemento access_token encontrado:', accessTokenInput);
      // } else {
      //   console.error('Elemento access_token NÃO encontrado.');
      // }

      // if (expiresAtInput) {
      //   console.log('Elemento expires_at encontrado:', expiresAtInput);
      // } else {
      //   console.error('Elemento expires_at NÃO encontrado.');
      // }

      if (accessToken && expiresAt) {
        if (accessTokenInput) accessTokenInput.value = accessToken;
        if (expiresAtInput) expiresAtInput.value = expiresAt;

        // Remove console.log for production
        // console.log('Access Token (input):', accessTokenInput ? accessTokenInput.value : 'N/A');
        // console.log('Expires At (input):', expiresAtInput ? expiresAtInput.value : 'N/A');
      } else {
        // Remove console.error for production
        // console.error('Access Token ou Expires At ausentes no hash da URL.');
        // Redirect to login if tokens are missing in the hash
        window.location.href = '<?php echo $_SESSION['base_url']; ?>/login';
      }
    });
  </script>
</body>
</html>