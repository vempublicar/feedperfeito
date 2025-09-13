<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/CarouselProduct.php';

$carouselProducts = [];
$cacheKey = 'carousel_products_cache';
$cacheTimestampKey = 'carousel_products_cache_timestamp';
$cacheDuration = 3600; // 1 hora em segundos

// Verifica se os dados estão em cache e se o cache ainda é válido
if (isset($_SESSION[$cacheKey]) && isset($_SESSION[$cacheTimestampKey]) && (time() - $_SESSION[$cacheTimestampKey] < $cacheDuration)) {
    $carouselProducts = $_SESSION[$cacheKey];
} else {
    // Se não estiver em cache ou estiver expirado, consulta o banco de dados
    $carouselProductModel = new CarouselProduct();
    $carouselProducts = $carouselProductModel->all();
    
    // Armazena os dados e o timestamp no cache da sessão
    $_SESSION[$cacheKey] = $carouselProducts;
    $_SESSION[$cacheTimestampKey] = time();
}

// Catálogo (virá do banco futuramente)
$catalogo = $carouselProducts; // Usar os produtos do banco de dados
$categoriasLoja = array_values(array_unique(array_map(fn($c) => $c['category'], $catalogo)));
sort($categoriasLoja);

$temasLoja = array_values(array_unique(array_map(fn($c) => $c['theme'], $catalogo)));
sort($temasLoja);

$tiposLoja = array_values(array_unique(array_map(fn($c) => $c['type'], $catalogo)));
sort($tiposLoja);

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

<!-- Modal de Detalhes do Produto -->
<div id="productModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 m-4 max-w-4xl w-full relative">
        <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-2xl font-bold">
            &times;
        </button>
        <div class="flex flex-col md:flex-row">
            <div class="md:w-1/2 p-4">
                <div id="modal-image-carousel" class="relative">
                    <div id="modal-images" class="flex overflow-hidden relative">
                        <!-- Imagens serão carregadas aqui pelo JS -->
                    </div>
                    <button class="absolute left-0 top-1/2 -translate-y-1/2 bg-black/50 text-white p-2 rounded-full"
                        id="modal-prev-image">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="absolute right-0 top-1/2 -translate-y-1/2 bg-black/50 text-white p-2 rounded-full"
                        id="modal-next-image">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <span id="modal-product-type"
                    class="absolute top-12 left-15 bg-yellow-400 text-gray-900 text-xs px-2 py-1 rounded font-bold z-10"><?= htmlspecialchars($p['type']) ?></span>
            </div>
            <div class="md:w-1/2 p-4 flex flex-col justify-between">
                <div>
                    <h3 id="modal-product-name" class="text-2xl font-bold mb-2">Nome do Produto</h3>
                    <p id="modal-product-description" class="text-gray-700 text-base mb-4">Descrição do produto.</p>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-gray-700 text-base mb-2">
                            Páginas: <span id="modal-product-page-count" class="font-semibold">0</span>
                        </div>
                        <div class="text-gray-700 text-base mb-2">
                            Tema: <span id="modal-product-theme" class="font-semibold"></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="text-gray-700 text-base mb-2">
                            Categoria: <span id="modal-product-category" class="font-semibold"></span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 ">
                        <button id="toggleCustomizationOptions" class="rounded border mb-4 p-1">
                            <i class="fas fa-cog mr-2"></i> Personalize
                        </button>
                    </div>
                    <div id="dynamic-customization-options" class="mb-4 hidden">
                        <span>O que deseja personalizar?</span>
                        <div id="customization-arte" class="mb-4 hidden">
                            <label for="customization-arte-select"
                                class="block text-gray-700 text-sm font-bold ">Arte:</label>
                            <i>Alteração de elementos graficos com base nos elementos que carregou na
                                personalização.</i>
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
                            <i>Alteração da imagem principal por alguma imagem que melhor se adapte nos itens
                                personalizados.</i>
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
                    <div class="grid grid-cols-2 md:grid-cols-2 gap-4 border border-gray-200 rounded-lg p-1 mb-3">
                        <div class="flex items-center m-auto ">
                            <span>Valor em Créditos:</span>
                        </div>
                        <div class="flex items-center text-lg font-semibold text-gray-900 m-auto ">
                            <i class="fas fa-coins text-yellow-500 mr-2"></i>
                            <h4 id="modal-product-credits">0</h4>
                        </div>
                    </div>
                </div>
                <button id="confirmPurchase"
                    class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-bold text-lg hover:bg-green-700 transition duration-300"
                    disabled>
                    Confirmar Compra
                </button>
                <form id="purchaseForm" action="../api/post/cadastrar_compra.php" method="POST" class="hidden">
                    <input type="hidden" name="productId" id="form-productId">
                    <input type="hidden" name="productName" id="form-productName">
                    <input type="hidden" name="uniqueCode" id="form-uniqueCode">
                    <input type="hidden" name="credits" id="form-credits">
                    <input type="hidden" name="observacoes" id="form-observacoes">
                    <input type="hidden" name="customization" id="form-customization">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const allCarouselProducts = <?= json_encode($carouselProducts); ?>;
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
                productCard.setAttribute('data-description', p.description);
                productCard.setAttribute('data-page-count', p.page_count || '');
                productCard.setAttribute('data-customization-types', JSON.stringify(customization_types));
                productCard.setAttribute('data-theme', p.theme);
                productCard.setAttribute('data-category', p.category);
                productCard.setAttribute('data-type', p.type);

                productCard.innerHTML = `
                    ${p.sold_quantity > 10 ? `
                        <span class="absolute top-2 left-2 bg-yellow-400 text-gray-900 text-xs px-2 py-1 rounded font-bold z-10">Mais vendido</span>
                    ` : `
                        <span class="absolute top-2 left-2 bg-yellow-400 text-gray-900 text-xs px-2 py-1 rounded font-bold z-10">${p.type}</span>
                    `}
                    <div class="relative w-full overflow-hidden group watermark-container" data-carousel-id="carousel-${p.id}">
                        <span class="absolute top-2 right-2 bg-gray-900 text-white text-xs font-bold px-2 py-1 rounded-full z-20 flex items-center">
                            <i class="fas fa-coins text-yellow-400 mr-1"></i> ${parseInt(p.credits)}
                        </span>
                        ${images.map((img, i) => `
                            <div class="carousel-image absolute inset-0 transition-opacity ease-in-out ${i === 0 ? 'opacity-100' : 'opacity-0'}">
                                <img src="${img}" class="w-full h-full object-cover" alt="${p.name}">
                                <div class="absolute inset-0 watermark"></div>
                            </div>
                        `).join('')}
                        ${images.length > 1 ? `
                            <button class="carousel-prev absolute left-2 top-1/2 -translate-y-1/2 bg-black/50 text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition z-30" data-carousel-target="carousel-${p.id}">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="carousel-next absolute right-2 top-1/2 -translate-y-1/2 bg-black/50 text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition z-30" data-carousel-target="carousel-${p.id}">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        ` : ''}
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
                            <button class="w-full bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300 btn-comprar-pack">
                                <i class="fas fa-plus-circle mr-2"></i> Comprar
                            </button>
                        </div>
                    </div>
                `;
                gradeLoja.appendChild(productCard);
            });
            initCarousels(); // Reinicia os carrosséis para os novos produtos
            attachBuyButtonListeners(); // Re-anexa os event listeners aos botões de compra
        }

        function filterProducts() {
            const categoryFilter = filtroCategoria.value;
            const themeFilter = filtroTema.value;
            const typeFilter = filtroTipo.value;
            const searchFilter = buscaLoja.value.toLowerCase();

            const filteredProducts = allCarouselProducts.filter(p => {
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

        // Inicializa os carrosséis
        function initCarousels() {
            const storeCarousels = document.querySelectorAll('[data-carousel-id]');
            storeCarousels.forEach(carousel => {
                const id = carousel.getAttribute("data-carousel-id");
                const slides = carousel.querySelectorAll(".carousel-image");
                const prev = carousel.querySelector(`.carousel-prev[data-carousel-target="${id}"]`);
                const next = carousel.querySelector(`.carousel-next[data-carousel-target="${id}"]`);
                let current = 0;
                function showSlide(i) {
                    slides.forEach((s, idx) => s.classList.toggle("opacity-100", idx === i));
                    slides.forEach((s, idx) => s.classList.toggle("opacity-0", idx !== i));
                }
                if (prev) {
                    prev.addEventListener("click", () => {
                        current = (current - 1 + slides.length) % slides.length;
                        showSlide(current);
                    });
                }
                if (next) {
                    next.addEventListener("click", () => {
                        current = (current + 1) % slides.length;
                        showSlide(current);
                    });
                }
                const firstImage = carousel.querySelector('img');
                if (firstImage) {
                    const setCarouselHeight = () => {
                        const imgHeight = firstImage.naturalHeight;
                        const imgWidth = firstImage.naturalWidth;
                        if (imgWidth && imgHeight) {
                            const carouselWidth = carousel.offsetWidth;
                            const calculatedHeight = (imgHeight / imgWidth) * carouselWidth;
                            carousel.style.height = `${calculatedHeight}px`;
                        }
                    };
                    firstImage.addEventListener('load', setCarouselHeight);
                    window.addEventListener('resize', setCarouselHeight);
                    if (firstImage.complete) {
                        setCarouselHeight();
                    }
                }
            });
        }

        // Lógica do Modal
        const productModal = document.getElementById('productModal');
        const closeModalBtn = document.getElementById('closeModal');
        const confirmPurchaseBtn = document.getElementById('confirmPurchase');
        const observacoesTextarea = document.getElementById('observacoes');
        const modalProductName = document.getElementById('modal-product-name');
        const modalProductDescription = document.getElementById('modal-product-description');
        const modalProductCredits = document.getElementById('modal-product-credits');
        const modalImagesContainer = document.getElementById('modal-images');
        const modalPrevImageBtn = document.getElementById('modal-prev-image');
        const modalNextImageBtn = document.getElementById('modal-next-image');
        const toggleCustomizationOptionsBtn = document.getElementById('toggleCustomizationOptions');
        const dynamicCustomizationOptionsDiv = document.getElementById('dynamic-customization-options');

        // Lógica para o botão de colapsar/expandir as opções de personalização
        toggleCustomizationOptionsBtn.addEventListener('click', () => {
            dynamicCustomizationOptionsDiv.classList.toggle('hidden');
        });

        let currentProduct = null;
        let currentModalImageIndex = 0;

        function openModal(product) {
            currentProduct = product;
            modalProductName.textContent = product.name;
            modalProductDescription.textContent = product.description;
            modalProductCredits.textContent = product.credits;
            document.getElementById('modal-product-page-count').textContent = product.page_count;
            document.getElementById('modal-product-theme').textContent = product.theme;
            document.getElementById('modal-product-category').textContent = product.category;
            document.getElementById('modal-product-type').textContent = product.type;

            // Itera sobre os tipos de customização e mostra os campos correspondentes
            const availableCustomizationTypes = ['arte', 'cores', 'imagem', 'texto'];
            availableCustomizationTypes.forEach(type => {
                const el = document.getElementById(`customization-${type}`);
                if (el) {
                    el.classList.add('hidden'); // Esconde todos os campos por padrão
                }
            });

            if (product.customization_types && product.customization_types.length > 0) {
                product.customization_types.forEach(type => {
                    const el = document.getElementById(`customization-${type}`);
                    if (el) {
                        el.classList.remove('hidden'); // Mostra apenas os campos necessários
                    }
                });
            }

            modalImagesContainer.innerHTML = ''; // Limpa imagens anteriores
            product.images.forEach((imgSrc, index) => {
                const imgElement = document.createElement('img');
                imgElement.src = imgSrc;
                imgElement.alt = product.name;
                imgElement.classList.add('w-full', 'h-auto', 'object-cover', 'flex-shrink-0');
                if (index === 0) {
                    imgElement.classList.add('block');
                } else {
                    imgElement.classList.add('hidden');
                }
                modalImagesContainer.appendChild(imgElement);
            });
            currentModalImageIndex = 0;
            updateModalImageDisplay();

            observacoesTextarea.value = '';
            confirmPurchaseBtn.disabled = true; // Desabilita inicialmente

            productModal.classList.remove('hidden');
            productModal.classList.add('flex');

            checkAllCustomizationSelects(); // Chama a função para habilitar/desabilitar o botão de compra
        }

        function closeModal() {
            productModal.classList.add('hidden');
            productModal.classList.remove('flex');
        }

        function updateModalImageDisplay() {
            const images = modalImagesContainer.querySelectorAll('img');
            images.forEach((img, index) => {
                if (index === currentModalImageIndex) {
                    img.classList.remove('hidden');
                    img.classList.add('block');
                } else {
                    img.classList.add('hidden');
                    img.classList.remove('block');
                }
            });
        }

        modalPrevImageBtn.addEventListener('click', () => {
            const images = modalImagesContainer.querySelectorAll('img');
            currentModalImageIndex = (currentModalImageIndex - 1 + images.length) % images.length;
            updateModalImageDisplay();
        });

        modalNextImageBtn.addEventListener('click', () => {
            const images = modalImagesContainer.querySelectorAll('img');
            currentModalImageIndex = (currentModalImageIndex + 1) % images.length;
            updateModalImageDisplay();
        });

        function attachBuyButtonListeners() {
            const btnComprarPacks = document.querySelectorAll('.btn-comprar-pack');
            btnComprarPacks.forEach(button => {
                button.addEventListener('click', function () {
                    const productCard = this.closest('.pack-card');
                    const productId = productCard.querySelector('[data-carousel-id]').getAttribute('data-carousel-id').replace('carousel-', '');
                    const productName = productCard.querySelector('h5').textContent;
                    const productDescription = productCard.getAttribute('data-description');
                    // Ajuste para pegar o crédito do span, que agora é o texto direto
                    const productCreditsText = productCard.querySelector('.text-white').textContent;
                    const productCredits = parseInt(productCreditsText.replace(/[^\d]/g, '')); // Remove não-dígitos e converte para int
                    const productUniqueCode = productCard.querySelector('p').textContent;

                    const productImages = [];
                    productCard.querySelectorAll('.carousel-image img').forEach(img => {
                        productImages.push(img.src);
                    });

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
                    openModal(product);
                });
            });
        }
        
        closeModalBtn.addEventListener('click', closeModal);
        productModal.addEventListener('click', (e) => {
            if (e.target === productModal) {
                closeModal();
            }
        });

        // Lógica de habilitação do botão Confirmar Compra
        function checkAllCustomizationSelects() {
            // Pega apenas os selects visíveis
            const customizationSelects = document.querySelectorAll('#dynamic-customization-options select.customization-select');
            let allSelected = true;
            let anyVisible = false;

            customizationSelects.forEach(select => {
                const parentDiv = select.closest('div[id^="customization-"]');
                if (parentDiv && !parentDiv.classList.contains('hidden')) {
                    anyVisible = true;
                    if (!select.value) { // Verifica se alguma opção foi selecionada
                        allSelected = false;
                    }
                }
            });
            confirmPurchaseBtn.disabled = !(allSelected && anyVisible);
        }

        // Adiciona event listeners aos selects de personalização (delegando para o container pai)
        document.getElementById('dynamic-customization-options').addEventListener('change', (event) => {
            if (event.target.classList.contains('customization-select')) {
                checkAllCustomizationSelects();
            }
        });

        confirmPurchaseBtn.addEventListener('click', () => {
            if (currentProduct) {
                const customizationOptions = {};
                document.querySelectorAll('.customization-select').forEach(select => {
                    const parentDiv = select.closest('div[id^="customization-"]');
                    if (parentDiv && !parentDiv.classList.contains('hidden')) {
                        const type = select.id.replace('customization-', '').replace('-select', '');
                        customizationOptions[type] = select.value;
                    }
                });

                // Preencher os campos ocultos do formulário
                document.getElementById('form-productId').value = currentProduct.id;
                document.getElementById('form-productName').value = currentProduct.name;
                document.getElementById('form-credits').value = currentProduct.credits;
                document.getElementById('form-uniqueCode').value = currentProduct.uniqueCode;
                document.getElementById('form-observacoes').value = observacoesTextarea.value;
                document.getElementById('form-customization').value = JSON.stringify(customizationOptions);

                // Enviar o formulário
                document.getElementById('purchaseForm').submit();
                closeModal();
            }
        });
        
        // Renderiza os produtos inicialmente
        renderProducts(allCarouselProducts);

        // Adiciona event listeners para os filtros
        filtroCategoria.addEventListener('change', filterProducts);
        filtroTema.addEventListener('change', filterProducts);
        filtroTipo.addEventListener('change', filterProducts);
        buscaLoja.addEventListener('input', filterProducts);
    });
</script>