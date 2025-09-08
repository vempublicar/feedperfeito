<?php
require_once 'config/session.php';
require_once 'models/User.php';
require_once 'models/UserOrder.php';
require_once 'models/ContentTemplate.php';
require_once 'models/CreditPackage.php';
require_once 'models/Promotion.php';
require_once 'models/Service.php';

// Require user login
requireUserLogin();

// Get current user
$current_user = getCurrentUser();

// Get user data from database
$userModel = new User();
$user = $userModel->find($current_user['id']);

// Get user orders
$orderModel = new UserOrder();
$orders = $orderModel->getOrdersByUser($user['id']);

// Get content templates
$templateModel = new ContentTemplate();
$templates = $templateModel->getActiveTemplates();

// Get credit packages
$packageModel = new CreditPackage();
$packages = $packageModel->getActivePackages();

// Get promotions
$promotionModel = new Promotion();
$promotions = $promotionModel->getActivePromotions();

// Get services
$serviceModel = new Service();
$services = $serviceModel->getActiveServices();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FeedPerfeito - Dashboard</title>
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
        <p class="text-gray-600 text-sm">Cliente Dashboard</p>
      </div>
      
      <nav class="mt-6">
        <a href="/dashboard" class="flex items-center px-6 py-3 text-black bg-gray-100 border-l-4 border-black">
          <i class="fas fa-home mr-3"></i>
          <span>Dashboard</span>
        </a>
        <a href="/dashboard/pedidos" class="flex items-center px-6 py-3 text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent">
          <i class="fas fa-shopping-cart mr-3"></i>
          <span>Meus Pedidos</span>
        </a>
        <a href="/dashboard/aprovacao" class="flex items-center px-6 py-3 text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent">
          <i class="fas fa-check-circle mr-3"></i>
          <span>Aprovação</span>
        </a>
        <a href="/dashboard/loja" class="flex items-center px-6 py-3 text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent">
          <i class="fas fa-store mr-3"></i>
          <span>Loja</span>
        </a>
        <a href="/dashboard/promocoes" class="flex items-center px-6 py-3 text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent">
          <i class="fas fa-tag mr-3"></i>
          <span>Promoções</span>
        </a>
        <a href="/dashboard/creditos" class="flex items-center px-6 py-3 text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent">
          <i class="fas fa-coins mr-3"></i>
          <span>Créditos</span>
        </a>
        <a href="/dashboard/servicos" class="flex items-center px-6 py-3 text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent">
          <i class="fas fa-concierge-bell mr-3"></i>
          <span>Serviços</span>
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
            <h2 class="text-xl font-bold text-black">Dashboard</h2>
          </div>
          <div class="flex items-center space-x-4">
            <a href="/personalize" class="text-black hover:text-gray-600 flex items-center">
              <i class="fas fa-sliders-h mr-2"></i> Personalizar
            </a>
            <div class="flex items-center bg-gray-100 px-3 py-2 rounded">
              <i class="fas fa-coins text-yellow-500 mr-2"></i>
              <span class="font-semibold text-black" id="credit-value"><?php echo $user['credits']; ?></span>
            </div>
            <div class="flex items-center">
              <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center">
                <i class="fas fa-user"></i>
              </div>
              <span class="ml-2 text-black"><?php echo htmlspecialchars($user['name']); ?></span>
            </div>
          </div>
        </div>
      </header>
      
      <!-- Content -->
      <main class="flex-1 overflow-y-auto p-6">
        <!-- Dashboard content (default) -->
        <div id="dashboard-content">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
              <div class="flex items-center">
                <div class="rounded-full bg-blue-100 p-3 mr-4">
                  <i class="fas fa-shopping-cart text-blue-500"></i>
                </div>
                <div>
                  <p class="text-gray-500">Pedidos Ativos</p>
                  <p class="text-2xl font-bold text-black"><?php echo count($orders); ?></p>
                </div>
              </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
              <div class="flex items-center">
                <div class="rounded-full bg-yellow-100 p-3 mr-4">
                  <i class="fas fa-coins text-yellow-500"></i>
                </div>
                <div>
                  <p class="text-gray-500">Seus Créditos</p>
                  <p class="text-2xl font-bold text-black"><?php echo $user['credits']; ?></p>
                </div>
              </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
              <div class="flex items-center">
                <div class="rounded-full bg-green-100 p-3 mr-4">
                  <i class="fas fa-check-circle text-green-500"></i>
                </div>
                <div>
                  <p class="text-gray-500">Aprovações Pendentes</p>
                  <p class="text-2xl font-bold text-black">
                    <?php 
                    $pendingApprovals = 0;
                    foreach ($orders as $order) {
                      if ($order['status'] == 'in_approval') {
                        $pendingApprovals++;
                      }
                    }
                    echo $pendingApprovals;
                    ?>
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-bold text-black mb-4">Pedidos em Andamento</h3>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serviço</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Atualizado</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <?php 
                  $displayedOrders = 0;
                  foreach ($orders as $order):
                    if ($displayedOrders >= 3) break;
                    $displayedOrders++;
                  ?>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?php echo $order['id']; ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black"><?php echo htmlspecialchars($order['title']); ?></td>
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('d/m/Y H:i', strtotime($order['updated_at'])); ?></td>
                  </tr>
                  <?php endforeach; ?>
                  
                  <?php if ($displayedOrders == 0): ?>
                  <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                      Nenhum pedido encontrado
                    </td>
                  </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
          
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-black mb-4">Últimos Conteúdos</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <?php 
              $displayedTemplates = 0;
              foreach ($templates as $template):
                if ($displayedTemplates >= 2) break;
                $displayedTemplates++;
              ?>
              <div class="border border-gray-200 rounded p-4">
                <div class="flex items-center">
                  <div class="w-16 h-16 rounded bg-gray-200 flex items-center justify-center mr-3">
                    <i class="fas fa-image text-gray-500"></i>
                  </div>
                  <div>
                    <p class="font-medium text-black"><?php echo htmlspecialchars($template['title']); ?></p>
                    <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($template['category']); ?></p>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
              
              <?php if ($displayedTemplates == 0): ?>
              <div class="col-span-3 text-center text-gray-500">
                Nenhum conteúdo disponível
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <!-- Content areas for other sections -->
        <div id="pedidos-content" class="hidden">
          <?php include 'includes/meus-pedidos.php'; ?>
        </div>
        
        <div id="aprovacao-content" class="hidden">
          <?php include 'includes/aprovacao.php'; ?>
        </div>
        
        <div id="loja-content" class="hidden">
          <?php include 'includes/loja-conteudos.php'; ?>
        </div>
        
        <div id="promocoes-content" class="hidden">
          <?php include 'includes/promocao.php'; ?>
        </div>
        
        <div id="creditos-content" class="hidden">
          <?php include 'includes/credito.php'; ?>
        </div>
        
        <div id="servicos-content" class="hidden">
          <?php include 'includes/solicitar-servico.php'; ?>
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
    const menuItems = document.querySelectorAll('nav a[href^="/dashboard/"]');
    const contentAreas = document.querySelectorAll('[id$="-content"]');
    
    // Check if we're on a specific section
    const path = window.location.pathname;
    const sectionMatch = path.match(/\/dashboard\/(.+)/);
    
    if (sectionMatch) {
      const section = sectionMatch[1];
      const targetElement = document.getElementById(section + '-content');
      if (targetElement) {
        // Hide default dashboard content
        document.getElementById('dashboard-content').classList.add('hidden');
        // Show selected content
        targetElement.classList.remove('hidden');
        
        // Update header title
        const menuItem = Array.from(menuItems).find(item => item.getAttribute('href') === '/dashboard/' + section);
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
        const section = href.replace('/dashboard/', '');
        
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