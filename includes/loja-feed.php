<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/FeedProduct.php';

$feedProducts = [];
$cacheKey = 'feed_products_cache';
$cacheTimestampKey = 'feed_products_cache_timestamp';
$cacheDuration = 3600; // 1 hora em segundos

// Verifica se os dados estão em cache e se o cache ainda é válido
if (isset($_SESSION[$cacheKey]) && isset($_SESSION[$cacheTimestampKey]) && (time() - $_SESSION[$cacheTimestampKey] < $cacheDuration)) {
    $feedProducts = $_SESSION[$cacheKey];
} else {
    // Se não estiver em cache ou estiver expirado, consulta o banco de dados
    $feedProductModel = new FeedProduct();
    $feedProducts = $feedProductModel->all();
    
    // Armazena os dados e o timestamp no cache da sessão
    $_SESSION[$cacheKey] = $feedProducts;
    $_SESSION[$cacheTimestampKey] = time();
}

// Catálogo (usando feedProducts conforme solicitado)
$catalogo = $feedProducts;
$categoriasLoja = array_values(array_unique(array_map(fn($c) => $c['category'], $catalogo)));
sort($categoriasLoja);

$temasLoja = array_values(array_unique(array_map(fn($c) => $c['theme'], $catalogo)));
sort($temasLoja);

$tiposLoja = array_values(array_unique(array_map(fn($c) => $c['type'], $catalogo)));
sort($tiposLoja);

// Créditos do cliente (use o real no backend)
$client_credits = $client_credits ?? 1200;
?>

<style>
    .watermark-container {
        position: relative;
        user-select: none;
    }

    .watermark::before {
        content: 'FEEDPERFEITO.COM';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-45deg);
        font-size: 1.5rem;
        color: rgba(255, 255, 255, 0.2);
        font-weight: bold;
        z-index: 2;
        /* Z-index menor que os botões do carrossel */
        pointer-events: none;
        /* Garante que o usuário possa interagir com o que está embaixo */
    }
</style>

<section id="loja" class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end flex-wrap gap-4">
            <div>
                <h2 class="text-3xl font-bold text-black mb-2">
                    Loja de <span class="font-light">Conteúdos</span>
                </h2>
                <div class="w-20 h-1 bg-black mb-4"></div>
                <p class="text-gray-600">Troque seus créditos por conteúdos prontos para personalizar.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <div class="flex items-center">
                    <label class="text-gray-600 mr-2">Categoria:</label>
                    <select id="filtroCategoria"
                        class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
                        <option value="">Todas</option>
                        <?php foreach ($categoriasLoja as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-center">
                    <label class="text-gray-600 mr-2">Tema:</label>
                    <select id="filtroTema"
                        class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
                        <option value="">Todos</option>
                        <?php foreach ($temasLoja as $tema): ?>
                            <option value="<?= htmlspecialchars($tema) ?>"><?= htmlspecialchars($tema) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-center">
                    <label class="text-gray-600 mr-2">Tipo:</label>
                    <select id="filtroTipo"
                        class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
                        <option value="">Todos</option>
                        <?php foreach ($tiposLoja as $tipo): ?>
                            <option value="<?= htmlspecialchars($tipo) ?>"><?= htmlspecialchars($tipo) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-center">
                    <label class="sr-only" for="buscaLoja">Buscar</label>
                    <input id="buscaLoja" type="search"
                        class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent"
                        placeholder="Buscar conteúdo...">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="gradeLoja">
            <!-- Produtos serão carregados aqui pelo JavaScript -->
        </div>
    </div>
</section>

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

<script>
    const allFeedProducts = <?= json_encode($feedProducts); ?>;
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const gradeLoja = document.getElementById('gradeLoja');
        const filtroCategoria = document.getElementById('filtroCategoria');
        const filtroTema = document.getElementById('filtroTema');
        const filtroTipo = document.getElementById('filtroTipo');
        const buscaLoja = document.getElementById('buscaLoja');

        const badgeColors = {
            'arte': 'bg-purple-100 text-purple-800',
            'cores': 'bg-blue-100 text-blue-800',
            'imagem': 'bg-green-100 text-green-800',
            'texto': 'bg-yellow-100 text-yellow-800',
        };

        function renderProducts(productsToRender) {
            gradeLoja.innerHTML = ''; // Limpa os produtos existentes
            productsToRender.forEach(p => {
                const images = JSON.parse(p.images) || [];
                const customization_types = JSON.parse(p.customization_types) || [];

                const productCard = document.createElement('div');
                productCard.classList.add('bg-white', 'rounded-lg', 'shadow-md', 'overflow-hidden', 'pack-card', 'relative');
                productCard.setAttribute('data-product-id', p.id);
                productCard.setAttribute('data-description', p.description);
                productCard.setAttribute('data-page-count', p.page_count || '');
                productCard.setAttribute('data-customization-types', JSON.stringify(customization_types));
                productCard.setAttribute('data-theme', p.theme);
                productCard.setAttribute('data-category', p.category);
                productCard.setAttribute('data-type', p.type);

                productCard.innerHTML = `
                    ${p.utilization > 10 ? `
                        <span class="absolute top-2 left-2 bg-yellow-400 text-gray-900 text-xs px-2 py-1 rounded font-bold z-10">Mais utilizado</span>
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
                        <p class="text-sm text-gray-400 mb-1">${p.unique_code}</p>
                        <h5 class="font-bold text-black mb-1">${p.name}</h5>
                        <div class="flex flex-wrap gap-1 mb-2 m-auto">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-200 text-gray-800">
                                ${p.theme}
                            </span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-200 text-gray-800">
                                ${p.category}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-1 mb-4 m-auto">
                            ${customization_types.map(c_type => `
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full ${badgeColors[c_type] || 'bg-gray-100 text-gray-600'}">
                                    ${c_type.charAt(0).toUpperCase() + c_type.slice(1)}
                                </span>
                            `).join('')}
                        </div>
                        <div class="mt-auto">
                            <button class="w-full bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300 btn-comprar-pack"
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
                                data-product-type="${p.type}">
                                <i class="fas fa-eye mr-2"></i> Visualizar
                            </button>
                        </div>
                    </div>
                `;
                gradeLoja.appendChild(productCard);
            });
            attachBuyButtonListeners(); // Re-anexa os event listeners aos botões de compra
        }

        function filterProducts() {
            const categoryFilter = filtroCategoria.value;
            const themeFilter = filtroTema.value;
            const typeFilter = filtroTipo.value;
            const searchFilter = buscaLoja.value.toLowerCase();
            const utilizationFilter = document.getElementById('filtroUtilization').value;

            const filteredProducts = allFeedProducts.filter(p => {
                const matchesCategory = categoryFilter === '' || p.category === categoryFilter;
                const matchesTheme = themeFilter === '' || p.theme === themeFilter;
                const matchesType = typeFilter === '' || p.type === typeFilter;
                const matchesSearch = p.name.toLowerCase().includes(searchFilter) ||
                                      p.description.toLowerCase().includes(searchFilter) ||
                                      p.unique_code.toLowerCase().includes(searchFilter) ||
                                      p.theme.toLowerCase().includes(searchFilter) ||
                                      p.category.toLowerCase().includes(searchFilter) ||
                                      p.type.toLowerCase().includes(searchFilter) ||
                                      (JSON.parse(p.customization_types) || []).some(type => type.toLowerCase().includes(searchFilter));
                return matchesCategory && matchesTheme && matchesType && matchesSearch;
            });
            renderProducts(filteredProducts);
        }

        // Lógica para o botão de colapsar/expandir as opções de personalização
        // toggleCustomizationOptionsBtn.addEventListener('click', () => {
        //     dynamicCustomizationOptionsDiv.classList.toggle('hidden');
        // });

        let currentProduct = null;
        let currentOffcanvasImageIndex = 0;

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
            document.getElementById('offcanvas-product-page-count').textContent = product.page_count;
            document.getElementById('offcanvas-product-theme').textContent = product.theme;
            document.getElementById('offcanvas-product-category').textContent = product.category;
            document.getElementById('offcanvas-product-type').textContent = product.type;

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
                    // Resetar o valor do select para "model" quando o campo é ocultado
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
            }

            offcanvasImagesContainer.innerHTML = '';
            product.images.forEach((imgSrc, index) => {
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

            observacoesTextarea.value = '';

            productOffcanvas.classList.remove('translate-x-full');
            productOffcanvas.classList.add('translate-x-0');

            checkAllCustomizationSelects();
        }

        function closeOffcanvas() {
            productOffcanvas.classList.remove('translate-x-0');
            productOffcanvas.classList.add('translate-x-full');
        }

        function attachBuyButtonListeners() {
            const btnComprarPacks = document.querySelectorAll('.btn-comprar-pack');
            btnComprarPacks.forEach(button => {
                button.addEventListener('click', function () {
                    const productCard = this.closest('.pack-card');
                    const productId = button.getAttribute('data-product-id');
                    const productName = productCard.querySelector('h5').textContent;
                    const productDescription = productCard.getAttribute('data-description');
                    const productCreditsText = productCard.querySelector('.text-white').textContent;
                    const productCredits = parseInt(productCreditsText.replace(/[^\d]/g, ''));
                    const productUniqueCode = productCard.querySelector('p').textContent;

                    const productImages = JSON.parse(button.getAttribute('data-product-images'));

                    const product = {
                        id: productId,
                        name: productName,
                        description: productDescription,
                        credits: productCredits,
                        images: productImages,
                        page_count: productCard.getAttribute('data-page-count'),
                        uniqueCode: productUniqueCode,
                        customization_types: JSON.parse(productCard.getAttribute('data-customization-types')),
                        theme: productCard.getAttribute('data-theme'),
                        category: productCard.getAttribute('data-category'),
                        type: productCard.getAttribute('data-type')
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

        // Lógica de habilitação do botão Confirmar Compra
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

        // Adiciona event listeners aos selects de personalização (delegando para o container pai)
        document.getElementById('dynamic-customization-options').addEventListener('change', (event) => {
            if (event.target.classList.contains('customization-select')) {
                checkAllCustomizationSelects();
            }
        });

        // Atualiza os campos de observações e personalização quando eles mudam
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

        // confirmPurchaseBtn.addEventListener('click', () => {
        //     // O formulário já está preenchido no openOffcanvas e nos event listeners
        //     // Apenas submete o formulário
        //     purchaseForm.submit();
        //     closeOffcanvas();
        // });

        toggleCustomizationOptionsBtn.addEventListener('click', () => {
            dynamicCustomizationOptionsDiv.classList.toggle('hidden');
            checkAllCustomizationSelects(); // Re-verifica o estado do botão de compra
        });
        
        // Renderiza os produtos inicialmente
        renderProducts(allFeedProducts);

        // Adiciona event listeners para os filtros
        filtroCategoria.addEventListener('change', filterProducts);
        filtroTema.addEventListener('change', filterProducts);
        filtroTipo.addEventListener('change', filterProducts);
        buscaLoja.addEventListener('input', filterProducts);
    });
</script>
