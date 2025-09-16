<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FeedPerfeito - Login</title>
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
  // Start session if not already started (redundant due to session.php)
  if (session_status() == PHP_SESSION_NONE) {
      session_start();
  }
  $error = $_SESSION['login_error'] ?? '';
  unset($_SESSION['login_error']);
  $register_success = $_SESSION['register_success'] ?? '';
  unset($_SESSION['register_success']);
  // print_r($_SESSION);
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
            <li><a href="https://feedperfeito.com/" class="text-black hover:text-gray-600">Home</a></li>
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
             <span class="font-light">Faça o login no</span> Seu Painel
          </h1>
          <p class="text-gray-600">Tarefas profissionais, produção de conteúdo e um modelo inovador.<br> Economize tempo e dinheiro.</p>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-center gap-12">
          <!-- Login Form -->
          <div class="w-full md:w-1/2">
            <div class="bg-white rounded-lg shadow-lg p-8">
              <div class="mb-8">
                <h2 class="text-2xl font-bold text-black mb-2">
                  Acessar <span class="font-light">Painel</span>
                </h2>
                <div class="w-16 h-0.5 bg-black mb-6"></div>
                
                <?php if ($error): ?>
                  <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                  </div>
                <?php elseif ($register_success): ?>
                  <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($register_success); ?>
                  </div>
                <?php endif; ?>
              </div>
              
              <form id="login-form" action="api/login.php" method="post">
                <div class="mb-6">
                  <input 
                    type="email" 
                    class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent" 
                    name="email" 
                    id="email" 
                    placeholder="Email" 
                    required
                  >
                </div>
                
                <div class="mb-6">
                  <input 
                    type="password" 
                    class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent" 
                    name="password" 
                    id="password" 
                    placeholder="Password" 
                    required
                  >
                </div>
                
                <div class="mb-6">
                  <button 
                    type="submit" 
                    id="form-submit" 
                    class="w-full bg-black text-white py-3 px-4 rounded font-semibold hover:bg-gray-800 transition duration-300"
                  >
                    Login
                  </button>
                </div>
                
                <div class="text-center">
                  <a href="<?php echo $_SESSION['base_url']; ?>/forgot-password.php" class="text-black hover:text-gray-600 underline">Esqueci a senha</a>
                  <span class="mx-2 text-gray-400">|</span>
                  <a href="<?php echo $_SESSION['base_url']; ?>/register" class="text-black hover:text-gray-600 underline">Criar Conta</a>
                </div>
              </form>
            </div>
          </div>
          
          <!-- Image -->
          <div class="w-full md:w-1/2 hidden md:block">
            <img src="<?php echo $_SESSION['base_url']; ?>/login.svg" class="w-full h-96" alt="Login image">
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- CTA Section -->
  <!-- <section class="py-12 bg-gray-100">
    <div class="container mx-auto px-4">
      <div class="flex flex-col md:flex-row justify-between items-center">
        <div class="mb-6 md:mb-0">
          <h4 class="text-2xl font-bold text-black">
            Are You Ready To Work & Develop With Us?<br>
            <span class="font-light">Don't Hesitate & Contact Us!</span>
          </h4>
        </div>
        <div>
          <a href="<?php echo $_SESSION['base_url']; ?>/contact" class="bg-black text-white py-3 px-6 rounded font-semibold hover:bg-gray-800 transition duration-300 inline-block">
            Contact Us Now!
          </a>
        </div>
      </div>
    </div>
  </section> -->

  <!-- Footer -->
  <footer class="bg-white py-8 border-t border-gray-200">
    <div class="container mx-auto px-4">
      <div class="text-center">
        <p class="text-gray-600">
          Copyright © 2036 <a href="<?php echo $_SESSION['base_url']; ?>/" class="text-black hover:text-gray-600">FeedPerfeito</a>, desenvolvido por <a href="https://vempublicar.com" class="text-black hover:text-gray-600">VemPublicar</a>. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
</body>
</html>