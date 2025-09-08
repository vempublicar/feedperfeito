<?php
require_once 'config/session.php';
require_once 'models/AdminUser.php';
require_once 'models/UserOrder.php';
require_once 'models/User.php';
require_once 'models/Service.php';
require_once 'models/ContentTemplate.php';
require_once 'models/CreditPackage.php';

// Require admin login
requireAdminLogin();

// Get current admin
$current_admin = getCurrentAdmin();

// Get statistics
$userModel = new User();
$users = $userModel->all();

$orderModel = new UserOrder();
$orders = $orderModel->all();

$serviceModel = new Service();
$services = $serviceModel->all();

$templateModel = new ContentTemplate();
$templates = $templateModel->all();

$packageModel = new CreditPackage();
$packages = $packageModel->all();
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
  <div class="flex h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-white shadow-md border-r border-gray-200">
      <div class="p-4 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-black">
          <span class="font-bold">Feed</span><span class="font-light">Perfeito</span>
        </h1>
        <p class="text-gray-600 text-sm">Admin Dashboard</p>
      </div>
      
      <nav class="mt-6">
        <a href="/admin" class="flex items-center px-6 py-3 text-black bg-gray-100 border-l-4 border-black">
          <i class="fas fa-home mr-3"></i>
          <span>Dashboard</span>
        </a>
        <a href="/admin/pedidos" class="flex items-center px-6 py-3 text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent">
          <i class="fas fa-shopping-cart mr-3"></i>
          <span>Pedidos</span>
        </a>
        <a href="/admin/clientes" class="flex items-center px-6 py-3 text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent">
          <i class="fas fa-users mr-3"></i>
          <span>Clientes</span>
        </a>
        <a href="/admin/produtos" class="flex items-center px-6 py-3 text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent">
          <i class="fas fa-box mr-3"></i>
          <span>Produtos</span>
        </a>
        <a href="/admin/relatorios" class="flex items-center px-6 py-3 text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent">
          <i class="fas fa-chart-bar mr-3"></i>
          <span>Relatórios</span>
        </a>
        <a href="/admin/configuracoes" class="flex items-center px-6 py-3 text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent">
          <i class="fas fa-cog mr-3"></i>
          <span>Configurações</span>
        </a>
      </nav>
      
      <div class="absolute bottom-0 w-64 p-4 border-t border-gray-200">
        <a href="/logout" class="flex items-center text-gray-600 hover:text-black">
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
              <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center">
                <i class="fas fa-user"></i>
              </div>
              <span class="ml-2 text-black"><?php echo htmlspecialchars($current_admin['name']); ?></span>
            </div>
          </div>
        </div>
      </header>
      
      <!-- Content -->
      <main class="flex-1 overflow-y-auto p-6">
        <!-- Dashboard content (default) -->
        <div id="dashboard-content">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
              <div class="flex items-center">
                <div class="rounded-full bg-blue-100 p-3 mr-4">
                  <i class="fas fa-shopping-cart text-blue-500"></i>
                </div>
                <div>
                  <p class="text-gray-500">Total Pedidos</p>
                  <p class="text-2xl font-bold text-black"><?php echo count($orders); ?></p>
                </div>
              </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
              <div class="flex items-center">
                <div class="rounded-full bg-green-100 p-3 mr-4">
                  <i class="fas fa-users text-green-500"></i>
                </div>
                <div>
                  <p class="text-gray-500">Clientes</p>
                  <p class="text-2xl font-bold text-black"><?php echo count($users); ?></p>
                </div>
              </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
              <div class="flex items-center">
                <div class="rounded-full bg-yellow-100 p-3 mr-4">
                  <i class="fas fa-coins text-yellow-500"></i>
                </div>
                <div>
                  <p class="text-gray-500">Créditos Vendidos</p>
                  <p class="text-2xl font-bold text-black">1,240</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-bold text-black mb-4">Pedidos Recentes</h3>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serviço</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <?php 
                  $displayedOrders = 0;
                  foreach ($orders as $order):
                    if ($displayedOrders >= 3) break;
                    $displayedOrders++;
                    
                    // Get user for this order
                    $orderUser = $userModel->find($order['user_id']);
                  ?>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?php echo $order['id']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black"><?php echo htmlspecialchars($orderUser['name'] ?? 'Cliente'); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($order['title']); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <?php
                      $statusClass = '';
                      $statusText = '';
                      switch ($order['status']) {
                        case 'confirmed':
                          $statusClass = 'bg-blue-100 text-blue-800';
                          $statusText = 'Confirmado';
                          break;
                        case 'in_production':
                          $statusClass = 'bg-yellow-100 text-yellow-800';
                          $statusText = 'Em Produção';
                          break;
                        case 'in_approval':
                          $statusClass = 'bg-green-100 text-green-800';
                          $statusText = 'Aprovação';
                          break;
                        case 'download_available':
                          $statusClass = 'bg-purple-100 text-purple-800';
                          $statusText = 'Download';
                          break;
                        default:
                          $statusClass = 'bg-gray-100 text-gray-800';
                          $statusText = ucfirst($order['status']);
                      }
                      ?>
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
                        <?php echo $statusText; ?>
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                  </tr>
                  <?php endforeach; ?>
                  
                  <?php if ($displayedOrders == 0): ?>
                  <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                      Nenhum pedido encontrado
                    </td>
                  </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-black mb-4">Clientes Recentes</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <?php 
              $displayedUsers = 0;
              foreach ($users as $user):
                if ($displayedUsers >= 3) break;
                $displayedUsers++;
              ?>
              <div class="border border-gray-200 rounded p-4">
                <div class="flex items-center">
                  <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                    <i class="fas fa-user text-gray-600"></i>
                  </div>
                  <div>
                    <p class="font-medium text-black"><?php echo htmlspecialchars($user['name']); ?></p>
                    <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($user['email']); ?></p>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
              
              <?php if ($displayedUsers == 0): ?>
              <div class="col-span-3 text-center text-gray-500">
                Nenhum cliente encontrado
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <!-- Content areas for other sections -->
        <div id="pedidos-content" class="hidden">
          <?php include 'includes-adm/pedidos.php'; ?>
        </div>
        
        <div id="clientes-content" class="hidden">
          <?php include 'includes-adm/clientes.php'; ?>
        </div>
        
        <div id="produtos-content" class="hidden">
          <?php include 'includes-adm/produtos.php'; ?>
        </div>
        
        <div id="relatorios-content" class="hidden">
          <?php include 'includes-adm/relatorios.php'; ?>
        </div>
        
        <div id="configuracoes-content" class="hidden">
          <?php include 'includes-adm/configuracoes.php'; ?>
        </div>
      </main>
    </div>
  </div>
  
  <script>
    // Sidebar toggle functionality
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      const sidebar = document.querySelector('.w-64');
      sidebar.classList.toggle('hidden');
    });
    
    // Menu navigation
    const menuItems = document.querySelectorAll('nav a[href^="/admin/"]');
    const contentAreas = document.querySelectorAll('[id$="-content"]');
    
    // Check if we're on a specific section
    const path = window.location.pathname;
    const sectionMatch = path.match(/\/admin\/(.+)/);
    
    if (sectionMatch) {
      const section = sectionMatch[1];
      const targetElement = document.getElementById(section + '-content');
      if (targetElement) {
        // Hide default dashboard content
        document.getElementById('dashboard-content').classList.add('hidden');
        // Show selected content
        targetElement.classList.remove('hidden');
        
        // Update header title
        const menuItem = Array.from(menuItems).find(item => item.getAttribute('href') === '/admin/' + section);
        if (menuItem) {
          const title = menuItem.querySelector('span').textContent;
          document.querySelector('header h2').textContent = title;
        }
      }
    }
    
    menuItems.forEach(item => {
      item.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Get the section from the href
        const href = this.getAttribute('href');
        const section = href.replace('/admin/', '');
        
        // Hide all content areas
        contentAreas.forEach(area => {
          area.classList.add('hidden');
        });
        
        // Show selected content area
        const targetElement = document.getElementById(section + '-content');
        if (targetElement) {
          // Hide default dashboard content
          document.getElementById('dashboard-content').classList.add('hidden');
          // Show selected content
          targetElement.classList.remove('hidden');
        }
        
        // Update header title
        const title = this.querySelector('span').textContent;
        document.querySelector('header h2').textContent = title;
        
        // Update URL without page reload
        history.pushState(null, '', href);
      });
    });
  </script>
</body>
</html>