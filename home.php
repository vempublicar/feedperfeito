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
            <li><a href="<?php echo $_SESSION['base_url']; ?>/" class="text-black hover:text-gray-600">Home</a></li>
            <li><a href="<?php echo $_SESSION['base_url']; ?>/#services" class="text-black hover:text-gray-600">Services</a></li>
            <li><a href="<?php echo $_SESSION['base_url']; ?>/#projects" class="text-black hover:text-gray-600">Projects</a></li>
            <li class="relative group">
              <a href="<?php echo $_SESSION['base_url']; ?>/#pages" class="text-black hover:text-gray-600">Pages</a>
              <ul class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                <li><a href="<?php echo $_SESSION['base_url']; ?>/about" class="block px-4 py-2 text-black hover:bg-gray-100">About Us</a></li>
                <li><a href="<?php echo $_SESSION['base_url']; ?>/faqs" class="block px-4 py-2 text-black hover:bg-gray-100">FAQs</a></li>
                <?php if (!isUserLoggedIn() && !isAdminLoggedIn()): ?>
                  <li><a href="<?php echo $_SESSION['base_url']; ?>/login" class="block px-4 py-2 text-black hover:bg-gray-100">Login</a></li>
                <?php else: ?>
                  <li><a href="<?php echo $_SESSION['base_url']; ?><?php echo (isAdminLoggedIn() ? '/admin' : '/dashboard'); ?>" class="block px-4 py-2 text-black hover:bg-gray-100">Dashboard</a></li>
                  <li><a href="<?php echo $_SESSION['base_url']; ?>/logout.php" class="block px-4 py-2 text-black hover:bg-gray-100">Logout</a></li>
                <?php endif; ?>
              </ul>
            </li>
            <li><a href="<?php echo $_SESSION['base_url']; ?>/#infos" class="text-black hover:text-gray-600">Infos</a></li>
            <li><a href="<?php echo $_SESSION['base_url']; ?>/#contact" class="text-black hover:text-gray-600">Contact</a></li>
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
    <h2 class="text-3xl font-bold text-gray-700">Welcome to FeedPerfeito!</h2>
  </main>

  <!-- CTA Section -->
  <section class="py-12 bg-gray-100">
    <div class="container mx-auto px-4">
      <div class="flex flex-col md:flex-row justify-between items-center">
        <div class="mb-6 md:mb-0">
          <h4 class="text-2xl font-bold text-black">
            Are You Ready To Work & Develop With Us?<br>
            <span class="font-light">Don't Hesitate & Contact Us!</span>
          </h4>
        </div>
        <div>
          <a href="/contact" class="bg-black text-white py-3 px-6 rounded font-semibold hover:bg-gray-800 transition duration-300 inline-block">
            Contact Us Now!
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-white py-8 border-t border-gray-200">
    <div class="container mx-auto px-4">
      <div class="text-center">
        <p class="text-gray-600">
          Copyright © 2036 <a href="<?php echo $_SESSION['base_url']; ?>/" class="text-black hover:text-gray-600">Tale SEO Agency</a>. All rights reserved.
          <br>
          Design: <a href="https://templatemo.com" target="_blank" class="text-black hover:text-gray-600">TemplateMo</a>
          Distribution: <a href="https://themewagon.com" class="text-black hover:text-gray-600">ThemeWagon</a>
        </p>
      </div>
    </div>
  </footer>
</body>
</html>