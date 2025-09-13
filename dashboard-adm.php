<?php
require_once 'config/session.php';
require_once 'models/UserOrder.php';
require_once 'models/User.php';
require_once 'models/Service.php';
require_once 'models/ContentTemplate.php';
require_once 'models/CreditPackage.php';

// Get current user from session
// $current_user = getCurrentAdmin();
$userModel = new User();
$user = $userModel->find($_SESSION['admin_id']);

// If user is not logged in or role is not admin, redirect
// if (!$current_user || $current_user['role'] !== 'admin') {
//     header('Location: ' . $_SESSION['base_url'] . '/login');
//     exit();
// }

// Determine a seção ativa para destacar o menu
$active_section = 'inicio'; // Default section
if (isset($_GET['section'])) {
  $active_section = $_GET['section'];
}

// Use current_user as admin
$current_admin = $user; // Keep this line as $current_user will hold the admin data
// print_r($current_admin);
// Get statistics
$avatarUrl = '';
if (file_exists('uploads/avatars/' . htmlspecialchars($current_admin['avatar_url']))) {
    $avatarUrl = $_SESSION['base_url'].'/uploads/avatars/' . htmlspecialchars($current_admin['avatar_url']);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FeedPerfeito - Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#000000',
            secondary: '#ffffff',
            background: '#f8f9fa'
          }
        }
      }
    }
  </script>
  <style>
    body {
      background-color: #f8f9fa;
    }
  </style>
</head>
<body class="bg-background">
  <?php if (isset($_SESSION['status_type']) && isset($_SESSION['status_message'])): ?>
        <div id="floatingMessage" class="fixed top-20 right-4 p-4 rounded-lg shadow-lg text-white z-50
            <?php echo $_SESSION['status_type'] === 'success' ? 'bg-green-500' : 'bg-red-500'; ?>">
            <?php echo htmlspecialchars($_SESSION['status_message']); ?>
        </div>
        <?php
        unset($_SESSION['status_type']);
        unset($_SESSION['status_message']);
        ?>
    <?php endif; ?>
  <div class="flex h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-white shadow-md border-r border-gray-200">
      <div class="p-4 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-black">
          <span class="font-bold">Feed</span><span class="font-light">Perfeito</span>
        </h1>
        <p class="text-gray-600 text-sm">Admin Dashboard - <?php echo htmlspecialchars($_SESSION['admin_email'] ?? 'N/A'); ?></p>
      </div>
      
      <nav class="mt-6">
        <a href="<?php echo $_SESSION['base_url']; ?>/admin/inicio"
          class="flex items-center px-6 py-3 <?php echo ($active_section === 'inicio') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
          <i class="fas fa-home mr-3"></i>
          <span>Dashboard</span>
        </a>
        <a href="<?php echo $_SESSION['base_url']; ?>/admin/pedidos"
          class="flex items-center px-6 py-3 <?php echo ($active_section === 'pedidos') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
          <i class="fas fa-shopping-cart mr-3"></i>
          <span>Pedidos</span>
        </a>
        <a href="<?php echo $_SESSION['base_url']; ?>/admin/clientes"
          class="flex items-center px-6 py-3 <?php echo ($active_section === 'clientes') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
          <i class="fas fa-users mr-3"></i>
          <span>Clientes</span>
        </a>
        <!-- <a href="<?php echo $_SESSION['base_url']; ?>/admin/produtos"
          class="flex items-center px-6 py-3 <?php echo ($active_section === 'produtos') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
          <i class="fas fa-box mr-3"></i>
          <span>Produtos</span>
        </a> -->
        <a href="<?php echo $_SESSION['base_url']; ?>/admin/produtos-credito"
          class="flex items-center px-6 py-3 <?php echo ($active_section === 'produtos-credito') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
          <i class="fas fa-coins mr-3"></i>
          <span>Produtos de Crédito</span>
        </a>
        <a href="<?php echo $_SESSION['base_url']; ?>/admin/produtos-carrossel"
          class="flex items-center px-6 py-3 <?php echo ($active_section === 'produtos-carrossel') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
          <i class="fas fa-images mr-3"></i>
          <span>Produtos Carrossel</span>
        </a>
        <a href="<?php echo $_SESSION['base_url']; ?>/admin/produtos-feed"
          class="flex items-center px-6 py-3 <?php echo ($active_section === 'produtos-feed') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
          <i class="fas fa-rss mr-3"></i>
          <span>Produtos Feed</span>
        </a>
        <a href="<?php echo $_SESSION['base_url']; ?>/admin/produtos-multiplo"
          class="flex items-center px-6 py-3 <?php echo ($active_section === 'produtos-multiplo') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
          <i class="fas fa-bookmark mr-3"></i>
          <span>Produtos Múltiplos</span>
        </a>
        <a href="<?php echo $_SESSION['base_url']; ?>/admin/relatorios"
          class="flex items-center px-6 py-3 <?php echo ($active_section === 'relatorios') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
          <i class="fas fa-chart-bar mr-3"></i>
          <span>Relatórios</span>
        </a>
        <a href="<?php echo $_SESSION['base_url']; ?>/admin/configuracoes"
          class="flex items-center px-6 py-3 <?php echo ($active_section === 'configuracoes') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
          <i class="fas fa-cog mr-3"></i>
          <span>Configurações</span>
        </a>
        <a href="<?php echo $_SESSION['base_url']; ?>/admin/webhooks-yampi-created"
          class="flex items-center px-6 py-3 <?php echo ($active_section === 'webhooks-yampi-created') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
          <i class="fas fa-file-invoice mr-3"></i>
          <span>Yampi Criados</span>
        </a>
        <a href="<?php echo $_SESSION['base_url']; ?>/admin/webhooks-yampi-paid"
          class="flex items-center px-6 py-3 <?php echo ($active_section === 'webhooks-yampi-paid') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
          <i class="fas fa-money-check-alt mr-3"></i>
          <span>Yampi Pagos</span>
        </a>
        <a href="<?php echo $_SESSION['base_url']; ?>/admin/adm-products-history"
          class="flex items-center px-6 py-3 <?php echo ($active_section === 'adm-products-history') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
          <i class="fas fa-history mr-3"></i>
          <span>Histórico de Produtos</span>
        </a>
      </nav>
      
      <div class="absolute bottom-0 w-64 p-4 border-t border-gray-200">
        <a href="<?php echo $_SESSION['base_url']; ?>/logout" class="flex items-center text-gray-600 hover:text-black">
          <i class="fas fa-sign-out-alt mr-3"></i>
          <span>Sair</span>
        </a>
      </div>
    </div>
    
    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Header -->
      <header class="bg-white shadow-sm border-b border-gray-200 py-4 px-6">
        <div class="flex justify-between items-center">
          <div class="flex items-center">
            <button id="sidebarToggle" class="text-gray-500 focus:outline-none mr-4">
              <i class="fas fa-bars"></i>
            </button>
            <h2 class="text-xl font-bold text-black">Admin Dashboard</h2>
          </div>
          <div class="flex items-center space-x-4">
            <div class="flex items-center bg-gray-100 px-3 py-2 rounded">
              <i class="fas fa-coins text-yellow-500 mr-2"></i>
              <span class="font-semibold text-black" id="credit-value">120</span>
            </div>
            <div class="relative">
              <button class="text-gray-500 focus:outline-none">
                <i class="fas fa-bell"></i>
              </button>
              <span class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center">3</span>
            </div>
            <div class="flex items-center">
              <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center overflow-hidden">
                <?php if (!empty($avatarUrl)): ?>
                  <img src="<?= htmlspecialchars($avatarUrl)?>" alt="Avatar" class="w-full h-full object-cover">
                <?php else: ?>
                  <i class="fas fa-user"></i>
                <?php endif; ?>
              </div>
              <span class="ml-2 text-black"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
            </div>
          </div>
        </div>
      </header>
      
      <!-- Content -->
      <main class="flex-1 overflow-y-auto p-6">
        <!-- Dashboard content (default) -->
        <!-- Dashboard content (default) -->
        <?php
        $requested_section = 'inicio'; // Default section

        if (isset($_GET['section'])) {
          $requested_section = $_GET['section'];
        }

        // Mapping of URL segments to include files
        $include_map = [
          'inicio' => 'includes-adm/resumo-adm.php',
          'pedidos' => 'includes-adm/pedidos.php',
          'clientes' => 'includes-adm/clientes.php',
          'producao-pedidos' => 'includes-adm/producao-pedidos.php',
          'produtos-credito' => 'includes-adm/credit-products.php',
          'produtos-carrossel' => 'includes-adm/carousel-products.php',
          'produtos-feed' => 'includes-adm/feed-products.php',
          'produtos-multiplo' => 'includes-adm/multiple-products.php',
          'relatorios' => 'includes-adm/relatorios.php',
          'configuracoes' => 'includes-adm/configuracoes.php',
          'webhooks-yampi-created' => 'includes-adm/yampi-webhooks-created.php',
          'webhooks-yampi-paid' => 'includes-adm/yampi-webhooks-paid.php',
          'adm-products-history' => 'includes-adm/adm-products-history.php',
        ];

        // Determine the file to include based on the requested section
        $file_to_include = $include_map[$requested_section] ?? 'includes-adm/resumo-adm.php';

        // Include the file if it exists, otherwise include the default dashboard
        if (file_exists($file_to_include)) {
          include $file_to_include;
        } else {
          // Fallback for invalid or unrecognized sections
          include 'includes-adm/resumo-adm.php';
        }
        ?>
      </main>
    </div>
  </div>
  <?php
    // Add logic to include modal_profile_completion.php if name or phone is empty for admin
    if (empty($current_admin['name']) || empty($current_admin['phone'])) {
        include 'modal_profile_completion.php';
    }
  ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
    const floatingMessage = document.getElementById('floatingMessage');
            if (floatingMessage) {
                setTimeout(() => {
                    floatingMessage.style.transition = 'opacity 1s ease-out';
                    floatingMessage.style.opacity = '0';
                    setTimeout(() => {
                        floatingMessage.remove();
                    }, 1000); // Remove o elemento após a transição
                }, 5000); // 5 segundos antes de iniciar o fade out
            }
            });
    // Sidebar toggle functionality
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      const sidebar = document.querySelector('.w-64');
      sidebar.classList.toggle('hidden');
    });
  </script>
</body>
</html>