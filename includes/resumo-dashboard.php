<?php
require_once __DIR__ . '/../models/Purchase.php';
require_once __DIR__ . '/../models/CarouselProduct.php';
require_once __DIR__ . '/../models/FeedProduct.php';
require_once __DIR__ . '/../models/MultipleProduct.php';

$userId = $_SESSION['user_id'] ?? null;
$pendingOrders = 0;
$confirmedOrders = 0;
$inProductionOrders = 0;
$inDownloadOrders = 0;
$inApprovalOrders = 0;
$availableOrders = 0; // Para 'Disponível' e 'Entregue'
$canceledOrders = 0;

$latestCarouselProducts = [];
$latestFeedProducts = [];
$latestMultipleProducts = [];

if ($userId) {
  $cacheDuration = 300; // 5 minutos em segundos

  // Carousel Products
  $cacheKeyCarousel = 'latest_carousel_products_cache';
  $cacheTimestampKeyCarousel = 'latest_carousel_products_cache_timestamp';
  if (isset($_SESSION[$cacheKeyCarousel]) && isset($_SESSION[$cacheTimestampKeyCarousel]) && (time() - $_SESSION[$cacheTimestampKeyCarousel] < $cacheDuration)) {
    $latestCarouselProducts = $_SESSION[$cacheKeyCarousel];
  } else {
    $carouselProductModel = new CarouselProduct();
    $latestCarouselProducts = $carouselProductModel->latest(4);
    $_SESSION[$cacheKeyCarousel] = $latestCarouselProducts;
    $_SESSION[$cacheTimestampKeyCarousel] = time();
  }

  // Feed Products
  $cacheKeyFeed = 'latest_feed_products_cache';
  $cacheTimestampKeyFeed = 'latest_feed_products_cache_timestamp';
  if (isset($_SESSION[$cacheKeyFeed]) && isset($_SESSION[$cacheTimestampKeyFeed]) && (time() - $_SESSION[$cacheTimestampKeyFeed] < $cacheDuration)) {
    $latestFeedProducts = $_SESSION[$cacheKeyFeed];
  } else {
    $feedProductModel = new FeedProduct();
    $latestFeedProducts = $feedProductModel->latest(4);
    $_SESSION[$cacheKeyFeed] = $latestFeedProducts;
    $_SESSION[$cacheTimestampKeyFeed] = time();
  }

  // Multiple Products (Serviços)
  $cacheKeyMultiple = 'latest_multiple_products_cache';
  $cacheTimestampKeyMultiple = 'latest_multiple_products_cache_timestamp';
  if (isset($_SESSION[$cacheKeyMultiple]) && isset($_SESSION[$cacheTimestampKeyMultiple]) && (time() - $_SESSION[$cacheTimestampKeyMultiple] < $cacheDuration)) {
    $latestMultipleProducts = $_SESSION[$cacheKeyMultiple];
  } else {
    $multipleProductModel = new MultipleProduct();
    $latestMultipleProducts = $multipleProductModel->latest(4);
    $_SESSION[$cacheKeyMultiple] = $latestMultipleProducts;
    $_SESSION[$cacheTimestampKeyMultiple] = time();
  }
}

if ($userId) {
  $purchaseModel = new Purchase();
  $allUserPurchases = $purchaseModel->query('purchases?user_id=eq.' . $userId);

  if ($allUserPurchases && is_array($allUserPurchases)) {
    foreach ($allUserPurchases as $purchase) {
      $status = $purchase['status'];

      switch ($status) {
        case 'pending':
          $pendingOrders++;
          break;
        case 'confirmado':
          $confirmedOrders++;
          break;
        case 'Produção':
          $inProductionOrders++;
          break;
        case 'Aprovação':
          $inApprovalOrders++;
          break;
        case 'Disponível':
          $inDownloadOrders++;
          break;
        case 'Entregue': // Considerar 'Entregue' como 'Disponível' para este contexto
          $availableOrders++;
          break;
        case 'canceled':
          $canceledOrders++;
          break;
      }
    }
  }
}
?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
  <div class="md:col-span-2"> <!-- Coluna 2/3 para números contabilizados -->
    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
      <div class=" p-6">
        <div class="flex items-center">
          <div class="rounded-full" style="background-color: rgb(147, 51, 234); padding: 0.75rem; margin-right: 1rem;">
            <i class="fas fa-shopping-cart text-white"></i>
          </div>
          <div>
            <p class="text-gray-500">Pedidos Pendentes</p>
            <p class="text-2xl font-bold text-black"><?php echo $pendingOrders; ?></p>
          </div>
        </div>
      </div>

      <div class=" p-6">
        <div class="flex items-center">
          <div class="rounded-full" style="background-color: rgb(147, 51, 234); padding: 0.75rem; margin-right: 1rem;">
            <i class="fas fa-check-circle text-white"></i>
          </div>
          <div>
            <p class="text-gray-500">Pedidos Confirmados</p>
            <p class="text-2xl font-bold text-black"><?php echo $confirmedOrders; ?></p>
          </div>
        </div>
      </div>

      <div class=" p-6">
        <div class="flex items-center">
          <div class="rounded-full" style="background-color: rgb(147, 51, 234); padding: 0.75rem; margin-right: 1rem;">
            <i class="fas fa-hammer text-white"></i>
          </div>
          <div>
            <p class="text-gray-500">Em Produção</p>
            <p class="text-2xl font-bold text-black"><?php echo $inProductionOrders; ?></p>
          </div>
        </div>
      </div>
      <div class=" p-6">
        <div class="flex items-center">
          <div class="rounded-full" style="background-color: rgb(147, 51, 234); padding: 0.75rem; margin-right: 1rem;">
            <i class="fas fa-check-double text-white"></i>
          </div>
          <div>
            <p class="text-gray-500">Aprovações Pendentes</p>
            <p class="text-2xl font-bold text-black"><?php echo $inApprovalOrders; ?></p>
          </div>
        </div>
      </div>

      <div class=" p-6">
        <div class="flex items-center">
          <div class="rounded-full" style="background-color: rgb(147, 51, 234); padding: 0.75rem; margin-right: 1rem;">
            <i class="fas fa-download text-white"></i>
          </div>
          <div>
            <p class="text-gray-500">Disponível para Download</p>
            <p class="text-2xl font-bold text-black"><?php echo $inDownloadOrders; ?></p>
          </div>
        </div>
      </div>
      <div class=" p-6">
        <div class="flex items-center">
          <div class="rounded-full" style="background-color: rgb(147, 51, 234); padding: 0.75rem; margin-right: 1rem;">
            <i class="fas fa-shipping-fast text-white"></i>
          </div>
          <div>
            <p class="text-gray-500">Pedidos Entregues</p>
            <p class="text-2xl font-bold text-black"><?php echo $availableOrders; ?></p>
          </div>
        </div>
      </div>

    </div>

  </div>
  <div class="md:col-span-1 m-auto"> <!-- Coluna 1/3 para créditos -->
    <div class="flex items-center">
      <div class="mt-4 m-auto">
        <a href="<?php echo $_SESSION['base_url']; ?>/dashboard/creditos"
          class=" w-full text-black py-3 px-6 rounded-lg text-lg font-bold text-center transition duration-300"
          style="background-color: rgb(255, 185, 0); hover:background-color: rgb(255, 165, 0);">
          <i class="fas fa-coins mr-2"></i> Adicionar Créditos
        </a>
      </div>
    </div>
    <!-- Botão piscante para aprovações pendentes -->
    <div class="flex items-center p-4">
      <?php if ($inApprovalOrders > 0): ?>
        <div class="mt-4 m-auto">
          <a href="dashboard.php?tab=aprovacoes"
            class="blinking-button w-full text-white py-3 px-6 rounded-lg text-lg font-bold text-center transition duration-300"
            style="background-color: rgb(147, 51, 234); hover:background-color: rgb(120, 40, 200);">
            <i class="fas fa-exclamation-triangle mr-2"></i> Aprovações Pendentes (<?php echo $inApprovalOrders; ?>)
          </a>
        </div>
      <?php endif; ?>
    </div>
    
    
  </div>
</div>

<!-- Offcanvas de Detalhes do Produto -->
<div id="productOffcanvas"
    class="fixed inset-y-0 right-0 w-full md:w-1/3 lg:w-1/4 bg-white shadow-xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out">
    <div class="flex flex-col h-full">
        <div class="flex justify-between items-center p-4 border-b">
            <h2 class="text-2xl font-bold" id="offcanvas-product-name">Nome do Produto</h2>
            <button id="closeOffcanvas" class="text-gray-500 hover:text-gray-800 text-2xl font-bold">
                &times;
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-4">
            <div id="offcanvas-image-carousel" class="relative mb-4">
                <div id="offcanvas-images" class="flex overflow-x-auto whitespace-nowrap gap-4">
                    <!-- Imagens serão carregadas aqui pelo JS -->
                </div>
            </div>
            <span id="offcanvas-product-type"
                class="absolute top-12 left-15 bg-yellow-400 text-gray-900 text-xs px-2 py-1 rounded font-bold z-10"></span>

            <p id="offcanvas-product-description" class="text-gray-700 text-base mb-4">Descrição do produto.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="text-gray-700 text-base">
                    Páginas: <span id="offcanvas-product-page-count" class="font-semibold">0</span>
                </div>
                <div class="text-gray-700 text-base">
                    Tema: <span id="offcanvas-product-theme" class="font-semibold"></span>
                </div>
                <div class="text-gray-700 text-base">
                    Categoria: <span id="offcanvas-product-category" class="font-semibold"></span>
                </div>
                <div class="text-gray-700 text-base" id="offcanvas-utilization-container">
                    Utilização: <span id="offcanvas-product-utilization" class="font-semibold"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 mb-4">
                <button id="toggleCustomizationOptions" class="rounded border p-1">
                    <i class="fas fa-cog mr-2"></i> Personalize
                </button>
            </div>
            <div id="dynamic-customization-options" class="mb-4 hidden">
                <span>O que deseja personalizar?</span>
                <div id="customization-arte" class="mb-4 hidden">
                    <label for="customization-arte-select"
                        class="block text-gray-700 text-sm font-bold ">Arte:</label>
                    <i>Alteração de elementos graficos com base nos elementos que carregou na personalização.</i>
                    <select id="customization-arte-select" name="customization[arte]"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline customization-select">
                        <option value="model">Manter arte do modelo</option>
                        <option value="user">Personalizar a arte</option>
                    </select>
                </div>
                <div id="customization-cores" class="mb-4 hidden">
                    <label for="customization-cores-select"
                        class="block text-gray-700 text-sm font-bold ">Cores:</label>
                    <i>Alteração das cores do documento com base nas cores selecionadas na personalização.</i>
                    <select id="customization-cores-select" name="customization[cores]"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline customization-select">
                        <option value="model">Manter cores do modelo</option>
                        <option value="user">Personalizar as cores</option>
                    </select>
                </div>
                <div id="customization-imagem" class="mb-4 hidden">
                    <label for="customization-imagem-select"
                        class="block text-gray-700 text-sm font-bold">Imagem:</label>
                    <i>Alteração da imagem principal por alguma imagem que melhor se adapte nos itens personalizados.</i>
                    <select id="customization-imagem-select" name="customization[imagem]"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline customization-select">
                        <option value="model">Manter imagem do modelo</option>
                        <option value="user">Personalizar a imagem</option>
                    </select>
                </div>
                <div id="customization-texto" class="mb-4 hidden">
                    <label for="customization-texto-select"
                        class="block text-gray-700 text-sm font-bold ">Texto:</label>
                    <i>Alteração do texto (envie o texto nas observacoes abaixo) o texto final sempre sera
                        personalizado.</i>
                    <select id="customization-texto-select" name="customization[texto]"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline customization-select">
                        <option value="model">Manter texto do modelo</option>
                        <option value="user">Personalizar o texto</option>
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <label for="observacoes" class="block text-gray-700 text-sm font-bold mb-2">Observações
                    Complementares:</label>
                <textarea id="observacoes" rows="3"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
            </div>
        </div>
        <div class="p-4 border-t">
            <div class="flex justify-between items-center mb-3">
                <span class="text-lg font-semibold">Valor em Créditos:</span>
                <div class="flex items-center text-xl font-bold text-gray-900">
                    <i class="fas fa-coins text-yellow-500 mr-2"></i>
                    <h4 id="offcanvas-product-credits">0</h4>
                </div>
            </div>
            
            <form id="purchaseForm" action="../api/post/cadastrar_compra.php" method="POST" >
                <input type="hidden" name="productId" id="form-productId">
                <input type="hidden" name="productName" id="form-productName">
                <input type="hidden" name="uniqueCode" id="form-uniqueCode">
                <input type="hidden" name="credits" id="form-credits">
                <input type="hidden" name="observacoes" id="form-observacoes">
                <input type="hidden" name="customization" id="form-customization">
                <input type="hidden" name="productType" id="form-productType">
                <button id="confirmPurchase" type="submit"
                class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-bold text-lg hover:bg-green-700 transition duration-300"
                >
                Confirmar Compra
            </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Estilos para a barra de rolagem do offcanvas */
    #productOffcanvas .overflow-y-auto::-webkit-scrollbar {
        width: 8px; /* Largura da barra de rolagem */
    }

    #productOffcanvas .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1; /* Cor de fundo da trilha */
        border-radius: 10px;
    }

    #productOffcanvas .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #888; /* Cor do "polegar" da barra de rolagem */
        border-radius: 10px;
    }

    #productOffcanvas .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #555555ff; /* Cor do "polegar" ao passar o mouse */
    }
  @keyframes blink {
    0% {
      opacity: 1;
    }

    50% {
      opacity: 0.5;
    }

    100% {
      opacity: 1;
    }
  }

  .blinking-button {
    animation: blink 1s infinite;
  }
</style>

<div class="md:col-span-2">
  <div class=" p-6 mb-8">
    <h3 class="text-lg font-bold text-black mb-4">Últimos Conteúdos</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="latest-carousel-products-container">
      <!-- Produtos Carousel serão carregados aqui pelo JavaScript -->
    </div>
  </div>

  <div class=" p-6 mb-8">
    <h3 class="text-lg font-bold text-black mb-4">Últimos Produtos Feed</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="latest-feed-products-container">
      <!-- Produtos Feed serão carregados aqui pelo JavaScript -->
    </div>
  </div>

  <div class=" p-6 mb-8">
    <h3 class="text-lg font-bold text-black mb-4">Últimos Serviços</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" id="latest-multiple-products-container">
      <!-- Serviços serão carregados aqui pelo JavaScript -->
    </div>
  </div>
</div>

<script>
  const latestCarouselProducts = <?= json_encode($latestCarouselProducts); ?>;
  const latestFeedProducts = <?= json_encode($latestFeedProducts); ?>;
  const latestMultipleProducts = <?= json_encode($latestMultipleProducts); ?>;

  const badgeColors = {
    'arte': 'bg-purple-100 text-purple-800',
    'cores': 'bg-blue-100 text-blue-800',
    'imagem': 'bg-green-100 text-green-800',
    'texto': 'bg-yellow-100 text-yellow-800',
  };

  function renderProductCards(containerId, productsToRender) {
    const container = document.getElementById(containerId);
    if (!container) return; // Exit if container not found

    container.innerHTML = ''; // Clear existing products

    if (productsToRender.length === 0) {
      container.innerHTML = `
                <div class="col-span-4 text-center text-gray-500">
                    Nenhum conteúdo disponível.
                </div>
            `;
      return;
    }

    productsToRender.forEach(p => {
      const images = JSON.parse(p.images) || [];
      const customization_types = JSON.parse(p.customization_types || '[]'); // Ensure it's an array

      const productCard = document.createElement('div');
      productCard.classList.add('bg-white', 'rounded-lg', 'shadow-md', 'overflow-hidden', 'pack-card', 'relative');
      productCard.setAttribute('data-product-id', p.id);
      productCard.setAttribute('data-description', p.description);
      productCard.setAttribute('data-page-count', p.page_count || '');
      productCard.setAttribute('data-customization-types', JSON.stringify(customization_types));
      productCard.setAttribute('data-theme', p.theme);
      productCard.setAttribute('data-category', p.category);
      productCard.setAttribute('data-type', p.type);
      productCard.setAttribute('data-utilization', p.utilization || '');


      productCard.innerHTML = `
                ${p.utilization && p.utilization !== '' ? `
                    <span class="absolute top-2 left-2 bg-yellow-400 text-gray-900 text-xs px-2 py-1 rounded font-bold z-10">${p.utilization}</span>
                ` : `
                    <span class="absolute top-2 left-2 bg-yellow-400 text-gray-900 text-xs px-2 py-1 rounded font-bold z-10">${p.type}</span>
                `}
                <div class="relative w-full overflow-x-auto whitespace-nowrap group watermark-container">
                    <span class="absolute top-2 right-2 bg-gray-900 text-white text-xs px-2 py-1 rounded-full z-20 flex items-center">
                        <i class="fas fa-coins text-yellow-400 mr-1"></i> ${parseInt(p.credits)}
                    </span>
                    <div class="flex gap-3">
                        ${images.map((img, i) => `
                            <div class="inline-block w-full flex-shrink-0">
                                <img src="${img}" class="w-full h-auto object-contain" alt="${p.name}">
                                <div class="absolute inset-0 watermark"></div>
                            </div>
                        `).join('')}
                    </div>
                </div>
                <div class="p-2 flex flex-col text-center">
                  <h5 class="font-bold text-black mb-2">${p.name}</h5>
                    <div class="mt-auto">
                        <button class="w-full text-white py-2 px-4 rounded font-medium transition duration-300 btn-view-product" style="background-color: rgb(147, 51, 234); hover:background-color: rgb(120, 40, 200);"
                            data-product-id="${p.id}"
                            data-product-name="${p.name}"
                            data-product-description="${p.description}"
                            data-product-credits="${p.credits}"
                            data-product-images='${JSON.stringify(images)}'
                            data-product-page-count="${p.page_count}"
                            data-product-unique-code="${p.unique_code}"
                            data-product-customization-types='${JSON.stringify(customization_types)}'
                            data-product-theme="${p.theme}"
                            data-product-category="${p.category}"
                            data-product-type="${p.type}"
                            data-product-utilization="${p.utilization}">
                            <i class="fas fa-eye mr-2"></i> Visualizar
                        </button>
                    </div>
                    <p class="text-sm text-gray-400 mb-1 mt-2">${p.unique_code}</p>
                </div>
            `;
      container.appendChild(productCard);
    });
    attachViewButtonListeners(); // Anexa os event listeners aos botões de visualização
  }

  // --- Lógica do Offcanvas ---
  let currentProduct = null;
  const productOffcanvas = document.getElementById('productOffcanvas');
  const closeOffcanvasBtn = document.getElementById('closeOffcanvas');
  const confirmPurchaseBtn = document.getElementById('confirmPurchase');
  const offcanvasProductName = document.getElementById('offcanvas-product-name');
  const offcanvasProductDescription = document.getElementById('offcanvas-product-description');
  const offcanvasProductCredits = document.getElementById('offcanvas-product-credits');
  const offcanvasImagesContainer = document.getElementById('offcanvas-images');
  const observacoesTextarea = document.getElementById('observacoes');
  const toggleCustomizationOptionsBtn = document.getElementById('toggleCustomizationOptions');
  const dynamicCustomizationOptionsDiv = document.getElementById('dynamic-customization-options');
  const purchaseForm = document.getElementById('purchaseForm');

  function openOffcanvas(product) {
    currentProduct = product;
    offcanvasProductName.textContent = product.name;
    offcanvasProductDescription.textContent = product.description;
    offcanvasProductCredits.textContent = product.credits;
    document.getElementById('offcanvas-product-page-count').textContent = product.page_count || 'N/A';
    document.getElementById('offcanvas-product-theme').textContent = product.theme || 'N/A';
    document.getElementById('offcanvas-product-category').textContent = product.category || 'N/A';
    document.getElementById('offcanvas-product-type').textContent = product.type || 'N/A';

    const utilizationContainer = document.getElementById('offcanvas-utilization-container');
    if (product.utilization) {
        document.getElementById('offcanvas-product-utilization').textContent = product.utilization;
        utilizationContainer.classList.remove('hidden');
    } else {
        utilizationContainer.classList.add('hidden');
    }

    // Preencher os campos ocultos do formulário
    document.getElementById('form-productId').value = currentProduct.id;
    document.getElementById('form-productName').value = currentProduct.name;
    document.getElementById('form-credits').value = currentProduct.credits;
    document.getElementById('form-uniqueCode').value = currentProduct.uniqueCode;
    document.getElementById('form-productType').value = currentProduct.type;

    const availableCustomizationTypes = ['arte', 'cores', 'imagem', 'texto'];
    availableCustomizationTypes.forEach(type => {
      const el = document.getElementById(`customization-${type}`);
      if (el) {
        el.classList.add('hidden');
        const select = el.querySelector('select');
        if (select) select.value = 'model';
      }
    });

    if (product.customization_types && product.customization_types.length > 0) {
      product.customization_types.forEach(type => {
        const el = document.getElementById(`customization-${type}`);
        if (el) {
          el.classList.remove('hidden');
        }
      });
    } else {
        dynamicCustomizationOptionsDiv.classList.add('hidden'); // Esconde o bloco se não houver personalização
        toggleCustomizationOptionsBtn.classList.add('hidden'); // Esconde o botão se não houver personalização
    }


    offcanvasImagesContainer.innerHTML = '';
    if (product.images && product.images.length > 0) {
        product.images.forEach((imgSrc) => {
            const imgContainer = document.createElement('div');
            imgContainer.classList.add('inline-block', 'w-full', 'flex-shrink-0', 'relative', 'watermark-container');
            const imgElement = document.createElement('img');
            imgElement.src = imgSrc;
            imgElement.alt = product.name;
            imgElement.classList.add('w-full', 'h-auto', 'object-contain');
            const watermarkDiv = document.createElement('div');
            watermarkDiv.classList.add('absolute', 'inset-0', 'watermark');
            imgContainer.appendChild(imgElement);
            imgContainer.appendChild(watermarkDiv);
            offcanvasImagesContainer.appendChild(imgContainer);
        });
    } else {
        offcanvasImagesContainer.innerHTML = '<div class="text-gray-500">Nenhuma imagem disponível.</div>';
    }


    observacoesTextarea.value = '';

    productOffcanvas.classList.remove('translate-x-full');
    productOffcanvas.classList.add('translate-x-0');

    checkAllCustomizationSelects();
  }

  function closeOffcanvas() {
    productOffcanvas.classList.remove('translate-x-0');
    productOffcanvas.classList.add('translate-x-full');
  }

  function attachViewButtonListeners() {
    const btnViewProduct = document.querySelectorAll('.btn-view-product');
    btnViewProduct.forEach(button => {
      button.addEventListener('click', function () {
        const productCard = this.closest('.pack-card');
        const product = {
            id: button.getAttribute('data-product-id'),
            name: button.getAttribute('data-product-name'),
            description: button.getAttribute('data-product-description'),
            credits: parseInt(button.getAttribute('data-product-credits')),
            images: JSON.parse(button.getAttribute('data-product-images')),
            page_count: productCard.getAttribute('data-page-count'),
            uniqueCode: productCard.querySelector('p').textContent, // Adjust if unique_code is not in <p>
            customization_types: JSON.parse(button.getAttribute('data-product-customization-types')),
            theme: productCard.getAttribute('data-theme'),
            category: productCard.getAttribute('data-category'),
            type: productCard.getAttribute('data-type'),
            utilization: productCard.getAttribute('data-utilization')
        };
        openOffcanvas(product);
      });
    });
  }

  closeOffcanvasBtn.addEventListener('click', closeOffcanvas);
  productOffcanvas.addEventListener('click', (e) => {
    if (e.target === productOffcanvas) {
      closeOffcanvas();
    }
  });

  function checkAllCustomizationSelects() {
    const customizationSelects = document.querySelectorAll('#dynamic-customization-options select.customization-select');
    let allSelectedAndVisible = true;
    let anyVisibleCustomization = false;

    customizationSelects.forEach(select => {
      const parentDiv = select.closest('div[id^="customization-"]');
      if (parentDiv && !parentDiv.classList.contains('hidden')) {
        anyVisibleCustomization = true;
        if (!select.value || select.value === 'model') {
          allSelectedAndVisible = false;
        }
      }
    });
    // confirmPurchaseBtn.disabled = !allSelectedAndVisible && anyVisibleCustomization;
    // if (!anyVisibleCustomization) {
    //     confirmPurchaseBtn.disabled = false;
    // }
  }

  document.getElementById('dynamic-customization-options').addEventListener('change', (event) => {
    if (event.target.classList.contains('customization-select')) {
      checkAllCustomizationSelects();
    }
  });

  observacoesTextarea.addEventListener('input', () => {
    document.getElementById('form-observacoes').value = observacoesTextarea.value;
  });

  document.getElementById('dynamic-customization-options').addEventListener('change', () => {
    const customizationOptions = {};
    document.querySelectorAll('.customization-select').forEach(select => {
      const parentDiv = select.closest('div[id^="customization-"]');
      if (parentDiv && !parentDiv.classList.contains('hidden')) {
        const type = select.id.replace('customization-', '').replace('-select', '');
        customizationOptions[type] = select.value;
      }
    });
    document.getElementById('form-customization').value = JSON.stringify(customizationOptions);
  });

  toggleCustomizationOptionsBtn.addEventListener('click', () => {
    dynamicCustomizationOptionsDiv.classList.toggle('hidden');
    checkAllCustomizationSelects();
  });

  document.addEventListener("DOMContentLoaded", function () {
    renderProductCards('latest-carousel-products-container', latestCarouselProducts);
    renderProductCards('latest-feed-products-container', latestFeedProducts);
    renderProductCards('latest-multiple-products-container', latestMultipleProducts);
  });
</script>