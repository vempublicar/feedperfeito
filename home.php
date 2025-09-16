<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FeedPerfeito - Home</title>
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
            <li><a href="<?php echo $_SESSION['base_url']; ?>/login" class="text-black hover:text-gray-600">Login</a></li>
          </ul>
        </nav>
        <button class="md:hidden text-black">
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>
  </header>

  <!-- Main Content - Placeholder -->
  <main class="flex-grow flex items-center justify-center">
    
    <h2 class="text-3xl font-bold text-gray-700">Bem-vindo ao FeedPerfeito!</h2>
    
  </main>
  


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