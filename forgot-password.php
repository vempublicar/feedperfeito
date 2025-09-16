<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FeedPerfeito - Esqueci a Senha</title>
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
             <span class="font-light">Redefina sua</span> Senha
          </h1>
          <p class="text-gray-600">Informe seu e-mail para receber as instruções de redefinição de senha.</p>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-center gap-12">
          <!-- Forgot Password Form -->
          <div class="w-full md:w-1/2">
            <div class="bg-white rounded-lg shadow-lg p-8">
              <div class="mb-8">
                <h2 class="text-2xl font-bold text-black mb-2">
                  Esqueci <span class="font-light">a Senha</span>
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
              
              <form id="forgot-password-form" action="api/post/request_password_reset.php" method="post">
                <div class="mb-6">
                  <input 
                    type="email" 
                    class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent" 
                    name="email" 
                    id="email" 
                    placeholder="E-mail" 
                    required
                  >
                </div>
                
                <div class="mb-6">
                  <button 
                    type="submit" 
                    id="form-submit" 
                    class="w-full bg-black text-white py-3 px-4 rounded font-semibold hover:bg-gray-800 transition duration-300"
                  >
                    Enviar Link de Redefinição
                  </button>
                </div>
                
                <div class="text-center">
                  <a href="<?php echo $_SESSION['base_url']; ?>/login" class="text-black hover:text-gray-600 underline">Lembrei a senha, fazer login</a>
                </div>
              </form>
            </div>
          </div>
          
          <!-- Image -->
          <div class="w-full md:w-1/2 hidden md:block">
            <img src="<?php echo $_SESSION['base_url']; ?>/login.svg" class="w-full h-96" alt="Imagem de Redefinição de Senha">
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
</body>
</html>