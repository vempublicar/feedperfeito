<?php
require_once 'config/session.php';
require_once 'models/User.php';
require_once 'models/UserOrder.php';
require_once 'models/ContentTemplate.php';
require_once 'models/CreditPackage.php';
require_once 'models/Promotion.php';
require_once 'models/Service.php';
require_once 'models/CreditManager.php'; // Inclui a classe CreditManager
require_once 'config/database.php'; // Inclui a configuração do banco de dados (para supabase_request)
require_once 'api/get/get_undelivered_products.php'; // Inclui a função para buscar produtos não entregues
require_once 'api/put/mark_product_as_delivered.php'; // Inclui a função para marcar produto como entregue

$userModel = new User();
$user = $userModel->find($_SESSION['user_id']);

// LÓGICA ADICIONADA: Limitar nome de usuário para exibição
$displayName = htmlspecialchars($user['name']);
if (mb_strlen($displayName) > 7) {
    $displayName = mb_substr($displayName, 0, 7) . '...';
}

// Determine a seção ativa para destacar o menu
$active_section = $_GET['section'] ?? 'inicio';

// Lógica para verificar e entregar produtos digitais não entregues
$undelivered_products = get_undelivered_products($_SESSION['user_email']); // Usa $user['email'] do banco

// Inicializa CreditManager
$creditManager = new CreditManager();
// Processa produtos não entregues
foreach ($undelivered_products as $product) {
    if ($product['is_delivered'] === null) {
        $creditsToAdd = 0;

        // Busca os pacotes de crédito para verificar o SKU
        $creditPackageModel = new CreditPackage();
        $activePackages = $creditPackageModel->getActivePackages();

        foreach ($activePackages as $package) {
            // Supondo que o 'sku' do produto venha do 'title' ou de um campo similar do CreditPackage.
            // Idealmente, haveria um campo 'sku' dedicado na tabela 'credit_packages'
            if (isset($product['sku']) && $product['sku'] == $package['tag']) {
                $creditsToAdd = $package['credits'] + $package['bonus_credits'];
                break;
            }
        }

        if ($creditsToAdd > 0) {
            // O user_id é uma string (UUID) no Supabase, então passamos como string.
            $addSuccess = $creditManager->addCredits($user['id'], $creditsToAdd, "Compra de pacote de créditos: " . $product['product_name']);

            if ($addSuccess) {
                mark_product_as_delivered($product['id']);
                // Atualiza a sessão ou recarrega o usuário para refletir os novos créditos
                $user = $userModel->find($_SESSION['user_id']);
                $_SESSION['user_credits'] = $user['credits'];
            } else {
                error_log("Falha ao adicionar créditos para o usuário " . $user['id'] . " com o produto " . $product['id']);
            }
        }
    }
}

$avatarUrl = '';
if (!empty($user['avatar_url']) && file_exists('uploads/avatars/' . htmlspecialchars($user['avatar_url']))) {
    $avatarUrl = $_SESSION['base_url'] . '/uploads/avatars/' . htmlspecialchars($user['avatar_url']);
}

// Get user orders
$orderModel = new UserOrder();
$orders = $orderModel->getOrdersByUser($user['id']); // Usa $user['id'] do banco

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
<html lang="pt-BR">

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

    <div class="relative min-h-screen md:flex">

        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden md:hidden"></div>

        <aside id="sidebar" class="fixed inset-y-0 left-0 bg-white shadow-md w-64 transform transition-transform duration-300 ease-in-out z-30 -translate-x-full md:translate-x-0">
            <div class="p-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-black">
                    <span class="font-bold">Feed</span><span class="font-light">Perfeito</span>
                </h1>
                <p class="text-gray-600 text-sm">Cliente Dashboard</p>
            </div>

            <nav class="mt-6 flex-1">
                <a href="<?php echo $_SESSION['base_url']; ?>/dashboard/inicio" class="flex items-center px-6 py-3 <?php echo ($active_section === 'inicio') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
                    <i class="fas fa-home mr-3 w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?php echo $_SESSION['base_url']; ?>/dashboard/pedidos" class="flex items-center px-6 py-3 <?php echo ($active_section === 'pedidos') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
                    <i class="fas fa-shopping-cart mr-3 w-5 text-center"></i>
                    <span>Meus Pedidos</span>
                </a>
                <a href="<?php echo $_SESSION['base_url']; ?>/dashboard/carrossel" class="flex items-center px-6 py-3 <?php echo ($active_section === 'loja') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
                    <i class="fas fa-clone mr-3 w-5 text-center"></i>
                    <span>Loja de Carrossel</span>
                </a>
                <a href="<?php echo $_SESSION['base_url']; ?>/dashboard/estatico" class="flex items-center px-6 py-3 <?php echo ($active_section === 'estatico') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
                    <i class="fas fa-bookmark mr-3 w-5 text-center"></i>
                    <span>Loja de Estático</span>
                </a>
                <a href="<?php echo $_SESSION['base_url']; ?>/dashboard/servicos" class="flex items-center px-6 py-3 <?php echo ($active_section === 'servicos') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
                    <i class="fas fa-tag mr-3 w-5 text-center"></i>
                    <span>Loja de Serviços</span>
                </a>
                <!-- <a href="<?php echo $_SESSION['base_url']; ?>/dashboard/promocoes" class="flex items-center px-6 py-3 <?php echo ($active_section === 'promocoes') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
                    <i class="fas fa-tag mr-3 w-5 text-center"></i>
                    <span>Promoções</span>
                </a> -->
                <a href="<?php echo $_SESSION['base_url']; ?>/dashboard/creditos" class="flex items-center px-6 py-3 <?php echo ($active_section === 'creditos') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
                    <i class="fas fa-coins mr-3 w-5 text-center"></i>
                    <span>Adicionar Créditos</span>
                </a>
                <a href="<?php echo $_SESSION['base_url']; ?>/dashboard/history" class="flex items-center px-6 py-3 <?php echo ($active_section === 'history') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
                    <i class="fas fa-check-circle mr-3 w-5 text-center"></i>
                    <span>Histórico de Pedidos</span>
                </a>
                <a href="<?php echo $_SESSION['base_url']; ?>/dashboard/products-history" class="flex items-center px-6 py-3 <?php echo ($active_section === 'products-history') ? 'bg-gray-100 border-l-4 border-black text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50 border-l-4 border-transparent'; ?>">
                    <i class="fas fa-box mr-3 w-5 text-center"></i>
                    <span>Histórico de Creditos</span>
                </a>
            </nav>

            <div class="absolute bottom-0 w-64 p-4 border-t border-gray-200">
                <a href="<?php echo $_SESSION['base_url']; ?>/logout" class="flex items-center text-gray-600 hover:text-black">
                    <i class="fas fa-sign-out-alt mr-3 w-5 text-center"></i>
                    <span>Sair</span>
                </a>
            </div>
        </aside>

        <div id="main-content" class="flex-1 flex flex-col overflow-hidden transition-all duration-300 ease-in-out md:ml-64">
            <header class="bg-white shadow-sm border-b border-gray-200 py-4 px-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <button id="mobileSidebarToggle" class="text-gray-500 focus:outline-none md:hidden">
                            <i class="fas fa-bars fa-lg"></i>
                        </button>
                        
                        <button id="desktopSidebarToggle" class="text-gray-500 focus:outline-none hidden md:block">
                            <i class="fas fa-bars fa-lg"></i>
                        </button>

                        <h2 class="text-xl font-bold text-black ml-4 hidden md:block">Dashboard</h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="<?php echo $_SESSION['base_url']; ?>/dashboard/personalize" class="text-black hover:text-gray-600 flex items-center">
                            <i class="fas fa-sliders-h mr-2"></i> Personalizar
                        </a>
                        <div class="flex items-center bg-gray-100 px-3 py-2 rounded">
                            <i class="fas fa-coins text-yellow-500 mr-2"></i>
                            <span class="font-semibold text-black" id="credit-value"><?php echo $_SESSION['user_credits']; ?></span>
                        </div>
                        <div class="flex items-center cursor-pointer" id="user-avatar-container">
                            <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center overflow-hidden">
                                <?php if ($avatarUrl) : ?>
                                    <img src="<?= $avatarUrl ?>" alt="Avatar" class="w-full h-full object-cover">
                                <?php else : ?>
                                    <i class="fas fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <span class="ml-2 text-black hidden md:block"><?php echo $displayName; ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <?php
                $requested_section = 'inicio'; // Default section
                
                // Check if PATH_INFO is available and extract the section
                if (isset($_GET['section'])) {
                    $requested_section = $_GET['section'];
                } else {
                    $requested_section = 'inicio';
                }

                // Mapping of URL segments to include files
                $include_map = [
                    'inicio' => 'includes/resumo-dashboard.php',
                    'pedidos' => 'includes/meus-pedidos.php',
                    'editar-pedidos' => 'includes/editar-pedidos.php',
                    'history' => 'includes/historico-pedidos.php',
                    'carrossel' => 'includes/loja-conteudos.php',
                    'estatico' => 'includes/loja-feed.php',
                    'promocoes' => 'includes/promocao.php',
                    'personalize' => 'includes/personalize.php',
                    'creditos' => 'includes/credito.php',
                    'servicos' => 'includes/loja-servicos.php',
                    'products-history' => 'includes/user-products-history.php',
                ];

                // Determine the file to include based on the requested section
                $file_to_include = $include_map[$requested_section] ?? 'includes/resumo-dashboard.php';

                // Include the file if it exists, otherwise include the default dashboard
                if (file_exists($file_to_include)) {
                    include $file_to_include;
                } else {
                    // Fallback for invalid or unrecognized sections
                    include 'includes/resumo-dashboard.php';
                }
                ?>
            </main>
        </div>
    </div>

    <?php include 'modal_profile_completion.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lógica da mensagem flutuante
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

            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const mobileToggle = document.getElementById('mobileSidebarToggle');
            const desktopToggle = document.getElementById('desktopSidebarToggle');
            const overlay = document.getElementById('sidebar-overlay');

            // Função para o toggle do mobile (com overlay)
            const toggleMobileSidebar = () => {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            };

            // Função para o toggle do desktop (com margem do conteúdo)
            const toggleDesktopSidebar = () => {
                sidebar.classList.toggle('-translate-x-full');
                mainContent.classList.toggle('md:ml-64');
            };

            // Listeners
            mobileToggle.addEventListener('click', toggleMobileSidebar);
            overlay.addEventListener('click', toggleMobileSidebar);
            desktopToggle.addEventListener('click', toggleDesktopSidebar);

            // Lógica do Modal
            const userAvatarContainer = document.getElementById('user-avatar-container');
            const modal = document.getElementById('modalProfileCompletion');
            if (userAvatarContainer && modal) {
                userAvatarContainer.addEventListener('click', function() {
                    modal.classList.remove('hidden');
                });
            }
        });
    </script>
</body>

</html>