<?php
require_once __DIR__ . '/../models/CarouselProduct.php';

$carouselProductModel = new CarouselProduct();
$carouselProducts = $carouselProductModel->all();
?>

<div class="bg-white rounded-lg shadow-md p-6">
  <?php if (isset($_SESSION['status_message'])): ?>
    <div
      class="mb-4 p-4 rounded-md <?php echo $_SESSION['status_type'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
      <?php echo htmlspecialchars($_SESSION['status_message']); ?>
    </div>
    <?php
    unset($_SESSION['status_message']);
    unset($_SESSION['status_type']);
    ?>
  <?php endif; ?>

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-black">Produtos Carrossel</h2>
    <button id="openCarouselProductModal"
      class="bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300">
      <i class="fas fa-plus mr-2"></i> Novo Produto
    </button>
  </div>

  <!-- Tabela de Produtos Carrossel -->
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagem
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tema
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Créditos</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de
            Personalização
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações
          </th>
        </tr>
      </thead>
      <tbody id="carouselProductsTableBody" class="bg-white divide-y divide-gray-200">
        <?php if (!empty($carouselProducts)): ?>
          <?php foreach ($carouselProducts as $product): ?>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($product['id']); ?></td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex -space-x-2 overflow-hidden">
                  <?php
                  $images = $product['images'] ? json_decode($product['images'], true) : [];
                  if (!empty($images)) {
                    foreach ($images as $index => $image) {
                      // Limita a exibição a 3 miniaturas para evitar sobrecarga visual
                      if ($index >= 3) {
                        break;
                      }
                      echo '<img class="inline-block h-10 w-10 rounded-md ring-2 ring-white object-cover" src="' . htmlspecialchars($image) . '" alt="Imagem do Produto">';
                    }
                    if (count($images) > 3) {
                      echo '<span class="inline-block h-10 w-10 rounded-md ring-2 ring-white bg-gray-200 text-gray-700 text-xs flex items-center justify-center">+ ' . (count($images) - 3) . '</span>';
                    }
                  } else {
                    echo '-';
                  }
                  ?>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($product['name']); ?></td>
              <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($product['theme'] ?? '-'); ?></td>
              <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($product['credits']); ?></td>
              <td class="px-6 py-4 whitespace-nowrap">
                <?php
                $customization_types = $product['customization_types'] ? json_decode($product['customization_types'], true) : [];
                if (!empty($customization_types)) {
                  echo implode(', ', array_map('ucfirst', $customization_types));
                } else {
                  echo '-';
                }
                ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $product['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                  <?php echo htmlspecialchars($product['status']); ?>
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-2 edit-btn"
                  data-id="<?php echo htmlspecialchars($product['id']); ?>"
                  data-name="<?php echo htmlspecialchars($product['name']); ?>"
                  data-theme="<?php echo htmlspecialchars($product['theme'] ?? ''); ?>"
                  data-category="<?php echo htmlspecialchars($product['category'] ?? ''); ?>"
                  data-type="<?php echo htmlspecialchars($product['type'] ?? ''); ?>"
                  data-credits="<?php echo htmlspecialchars($product['credits']); ?>"
                  data-sold_quantity="<?php echo htmlspecialchars($product['sold_quantity']); ?>"
                  data-customization_types="<?php echo htmlspecialchars($product['customization_types'] ?? ''); ?>"
                  data-description="<?php echo htmlspecialchars($product['description'] ?? ''); ?>"
                  data-page_count="<?php echo htmlspecialchars($product['page_count'] ?? ''); ?>"
                  data-status="<?php echo htmlspecialchars($product['status']); ?>"
                  data-unique_code="<?php echo htmlspecialchars($product['unique_code'] ?? ''); ?>"
                  data-images="<?php echo htmlspecialchars($product['images'] ?? ''); ?>">Editar</a>
                <form action="<?php echo $_SESSION['base_url']; ?>/api/delete/carousel_product.php" method="POST"
                  style="display:inline;"
                  onsubmit="return confirm('Tem certeza que deseja excluir este produto carrossel?');">
                  <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                  <input type="hidden" name="_method" value="DELETE">
                  <button type="submit" class="text-red-600 hover:text-red-900 delete-btn"
                    style="background:none; border:none; padding:0; cursor:pointer;">Excluir</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Nenhum produto carrossel encontrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Modal para Adicionar Produto Carrossel -->
  <div id="addCarouselProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-2/3 shadow-lg rounded-md bg-white">
      <div class="flex justify-between items-center pb-3">
        <h3 class="text-lg leading-6 font-medium text-gray-900" id="addModalTitle">Adicionar Produto Carrossel</h3>
        <button id="closeAddCarouselProductModal" class="text-gray-400 hover:text-gray-500">
          <span class="sr-only">Fechar</span>
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="mt-2">
        <!-- Formulário de Adição de Produto Carrossel -->
        <form id="addCarouselProductForm" action="<?php echo $_SESSION['base_url']; ?>/api/post/insert_carousel_product.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="_method" value="POST">

          <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
            <!-- Imagens (Campo de upload) -->
            <div class="md:col-span-2">
              <label for="add_images_upload" class="block text-sm font-medium text-gray-700">Imagens (Upload)</label>
              <input type="file" name="images_upload[]" id="add_images_upload" multiple accept="image/*"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800">
              <p class="mt-1 text-sm text-gray-500">Selecione até 10 imagens.</p>
              <div id="add_image_previews" class="mt-2 flex flex-wrap gap-2">
                <!-- Miniaturas das imagens serão exibidas aqui -->
              </div>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Nome -->
            <div>
              <label for="add_name" class="block text-sm font-medium text-gray-700">Nome</label>
              <input type="text" name="name" id="add_name"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm"
                required>
            </div>
            <!-- Tema -->
            <div>
              <label for="add_theme" class="block text-sm font-medium text-gray-700">Tema</label>
              <input type="text" name="theme" id="add_theme"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">
            </div>

            <!-- Categoria -->
            <div>
              <label for="add_category" class="block text-sm font-medium text-gray-700">Categoria</label>
              <input type="text" name="category" id="add_category"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">
            </div>
            <!-- Tipo -->
            <div>
              <label for="add_type" class="block text-sm font-medium text-gray-700">Tipo</label>
              <select name="type" id="add_type"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">
                <option value="">Selecione</option>
                <option value="Destaque">Destaque</option>
                <option value="Novidade">Novidade</option>
                <option value="Promocao">Promoção</option>
                <!-- Outras opções podem ser adicionadas -->
              </select>
            </div>

            <div>
              <label for="add_credits" class="block text-sm font-medium text-gray-700">Créditos</label>
              <input type="text" name="credits" id="add_credits"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm"
                required>
            </div>

            <!-- Qtd de Páginas -->
            <div>
              <label for="add_page_count" class="block text-sm font-medium text-gray-700">Qtd de Páginas</label>
              <input type="number" name="page_count" id="add_page_count"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm"
                value="1">
            </div>

            <!-- Status -->
            <div>
              <label for="add_status" class="block text-sm font-medium text-gray-700">Status</label>
              <select name="status" id="add_status"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">
                <option value="active">Ativo</option>
                <option value="inactive">Inativo</option>
                <option value="draft">Rascunho</option>
              </select>
            </div>
            <!-- Tipo de Personalização (Multi Select) -->
            <div>
              <label for="add_customization_types" class="block text-sm font-medium text-gray-700">Tipo de
                Personalização</label>
              <div class="mt-1 space-y-2">
                <label class="inline-flex items-center">
                  <input type="checkbox" name="customization_types[]" value="arte" class="form-checkbox">
                  <span class="ml-2 text-sm text-gray-700">Arte</span>
                </label>
                <label class="inline-flex items-center">
                  <input type="checkbox" name="customization_types[]" value="cores" class="form-checkbox">
                  <span class="ml-2 text-sm text-gray-700">Cores</span>
                </label>
                <label class="inline-flex items-center">
                  <input type="checkbox" name="customization_types[]" value="imagem" class="form-checkbox">
                  <span class="ml-2 text-sm text-gray-700">Imagem</span>
                </label>
                <label class="inline-flex items-center">
                  <input type="checkbox" name="customization_types[]" value="texto" class="form-checkbox">
                  <span class="ml-2 text-sm text-gray-700">Texto</span>
                </label>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Texto / Descrição -->
            <div class="md:col-span-2">
              <label for="add_description" class="block text-sm font-medium text-gray-700">Descrição</label>
              <textarea name="description" id="add_description" rows="3"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm"></textarea>
            </div>
          </div>
          <div class="mt-6">
            <button type="submit"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
              Adicionar Produto Carrossel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal para Editar Produto Carrossel -->
  <div id="editCarouselProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-2/3 shadow-lg rounded-md bg-white">
      <div class="flex justify-between items-center pb-3">
        <h3 class="text-lg leading-6 font-medium text-gray-900" id="editModalTitle">Editar Produto Carrossel</h3>
        <button id="closeEditCarouselProductModal" class="text-gray-400 hover:text-gray-500">
          <span class="sr-only">Fechar</span>
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="mt-2">
        <!-- Formulário de Edição de Produto Carrossel -->
        <form id="editCarouselProductForm" action="<?php echo $_SESSION['base_url']; ?>/api/post/update_carousel_product.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="id" id="edit_product_id">
          <input type="hidden" name="_method" id="edit_method" value="PUT">

          <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
            <!-- Imagens (Campo de upload) -->
            <div class="md:col-span-2">
              <label for="edit_images_upload" class="block text-sm font-medium text-gray-700">Imagens (Upload)</label>
              <input type="file" name="images_upload[]" id="edit_images_upload" multiple accept="image/*"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800">
              <p class="mt-1 text-sm text-gray-500">Selecione até 10 imagens.</p>
              <div id="edit_image_previews" class="mt-2 flex flex-wrap gap-2">
                <!-- Miniaturas das imagens serão exibidas aqui -->
              </div>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Nome -->
            <div>
              <label for="edit_name" class="block text-sm font-medium text-gray-700">Nome</label>
              <input type="text" name="name" id="edit_name"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm"
                required>
            </div>
            <!-- Tema -->
            <div>
              <label for="edit_theme" class="block text-sm font-medium text-gray-700">Tema</label>
              <input type="text" name="theme" id="edit_theme"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">
            </div>

            <!-- Categoria -->
            <div>
              <label for="edit_category" class="block text-sm font-medium text-gray-700">Categoria</label>
              <input type="text" name="category" id="edit_category"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">
            </div>
            <!-- Tipo -->
            <div>
              <label for="edit_type" class="block text-sm font-medium text-gray-700">Tipo</label>
              <select name="type" id="edit_type"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">
                <option value="">Selecione</option>
                <option value="Destaque">Destaque</option>
                <option value="Novidade">Novidade</option>
                <option value="Promocao">Promoção</option>
                <!-- Outras opções podem ser adicionadas -->
              </select>
            </div>

            <div>
              <label for="edit_credits" class="block text-sm font-medium text-gray-700">Créditos</label>
              <input type="text" name="credits" id="edit_credits"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm"
                required>
            </div>

            <!-- Qtd de Páginas -->
            <div>
              <label for="edit_page_count" class="block text-sm font-medium text-gray-700">Qtd de Páginas</label>
              <input type="number" name="page_count" id="edit_page_count"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm"
                value="1">
            </div>

            <!-- Status -->
            <div>
              <label for="edit_status" class="block text-sm font-medium text-gray-700">Status</label>
              <select name="status" id="edit_status"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">
                <option value="active">Ativo</option>
                <option value="inactive">Inativo</option>
                <option value="draft">Rascunho</option>
              </select>
            </div>
            <!-- Tipo de Personalização (Multi Select) -->
            <div>
              <label for="edit_customization_types" class="block text-sm font-medium text-gray-700">Tipo de
                Personalização</label>
              <div class="mt-1 space-y-2">
                <label class="inline-flex items-center">
                  <input type="checkbox" name="customization_types[]" value="arte" class="form-checkbox">
                  <span class="ml-2 text-sm text-gray-700">Arte</span>
                </label>
                <label class="inline-flex items-center">
                  <input type="checkbox" name="customization_types[]" value="cores" class="form-checkbox">
                  <span class="ml-2 text-sm text-gray-700">Cores</span>
                </label>
                <label class="inline-flex items-center">
                  <input type="checkbox" name="customization_types[]" value="imagem" class="form-checkbox">
                  <span class="ml-2 text-sm text-gray-700">Imagem</span>
                </label>
                <label class="inline-flex items-center">
                  <input type="checkbox" name="customization_types[]" value="texto" class="form-checkbox">
                  <span class="ml-2 text-sm text-gray-700">Texto</span>
                </label>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Texto / Descrição -->
            <div class="md:col-span-2">
              <label for="edit_description" class="block text-sm font-medium text-gray-700">Descrição</label>
              <textarea name="description" id="edit_description" rows="3"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm"></textarea>
            </div>
          </div>
          <div class="mt-6">
            <button type="submit"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
              Salvar Edição
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  (function () {
    // Garante que o script só seja executado uma vez por carregamento de seção
    if (window.feedperfeito && window.feedperfeito.carouselProductScriptInitialized) {
      return;
    }
    if (!window.feedperfeito) {
      window.feedperfeito = {};
    }
    window.feedperfeito.carouselProductScriptInitialized = true;

    // Referências para o modal de Adição
    const addCarouselProductModal = document.getElementById('addCarouselProductModal');
    const openCarouselProductModalBtn = document.getElementById('openCarouselProductModal'); // Botão "Novo Produto Carrossel"
    const closeAddCarouselProductModalBtn = document.getElementById('closeAddCarouselProductModal');
    const addCarouselProductForm = document.getElementById('addCarouselProductForm');
    const addImagesUploadField = document.getElementById('add_images_upload');
    const addImagePreviewsContainer = document.getElementById('add_image_previews');
    let addUploadedImageFiles = [];

    // Referências para o modal de Edição
    const editCarouselProductModal = document.getElementById('editCarouselProductModal');
    const closeEditCarouselProductModalBtn = document.getElementById('closeEditCarouselProductModal');
    const editCarouselProductForm = document.getElementById('editCarouselProductForm');
    const editProductIdField = document.getElementById('edit_product_id');
    const editMethodField = document.getElementById('edit_method');
    const editImagesUploadField = document.getElementById('edit_images_upload');
    const editImagePreviewsContainer = document.getElementById('edit_image_previews');
    let editUploadedImageFiles = [];

    // Funções para exibir previews de imagens (adaptadas para ambos os modais)
    // Armazena as URLs das imagens existentes que devem ser mantidas
    let existingImageUrlsToKeep = [];

    // Funções para exibir previews de imagens (adaptadas para ambos os modais)
    function displayImagePreviews(imageUrls, container, isEditModal = false) {
      container.innerHTML = ''; // Limpa previews anteriores
      existingImageUrlsToKeep = []; // Limpa a lista de URLs a serem mantidas

      if (imageUrls) {
        const urls = JSON.parse(imageUrls);
        urls.forEach((url, index) => {
          const previewDiv = document.createElement('div');
          previewDiv.className = 'relative w-24 h-24';

          const img = document.createElement('img');
          img.src = url;
          img.className = 'w-full h-full object-cover rounded-md';
          previewDiv.appendChild(img);

          // Adiciona um campo oculto para cada imagem existente
          if (isEditModal) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'existing_images[]';
            hiddenInput.value = url;
            previewDiv.appendChild(hiddenInput);
            existingImageUrlsToKeep.push(url); // Adiciona a URL à lista de URLs a serem mantidas
          }

          const removeBtn = document.createElement('button');
          removeBtn.innerHTML = '&times;';
          removeBtn.className = 'absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold -mt-2 -mr-2 cursor-pointer';
          removeBtn.onclick = () => {
            previewDiv.remove();
            // Remove a URL da lista de URLs a serem mantidas
            if (isEditModal) {
              const urlIndex = existingImageUrlsToKeep.indexOf(url);
              if (urlIndex > -1) {
                existingImageUrlsToKeep.splice(urlIndex, 1);
              }
            }
          };
          previewDiv.appendChild(removeBtn);
          container.appendChild(previewDiv);
        });
      }
    }

    function handleFileSelect(event, container, isEditModal = false) {
      container.innerHTML = ''; // Limpa previews anteriores
      // A lógica para manter imagens existentes e adicionar novas foi refeita na displayImagePreviews.
      // Aqui, apenas lidamos com o upload de novas imagens.
      const currentUploadedFiles = isEditModal ? editUploadedImageFiles : addUploadedImageFiles;
      currentUploadedFiles.length = 0; // Limpa a lista de arquivos para upload

      const files = event.target.files;
      if (files.length > 0) {
        Array.from(files).forEach(file => {
          currentUploadedFiles.push(file);

          const reader = new FileReader();
          reader.onload = (e) => {
            const previewDiv = document.createElement('div');
            previewDiv.className = 'relative w-24 h-24';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-full h-full object-cover rounded-md';
            previewDiv.appendChild(img);

            const removeBtn = document.createElement('button');
            removeBtn.innerHTML = '&times;';
            removeBtn.className = 'absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold -mt-2 -mr-2 cursor-pointer';
            removeBtn.onclick = () => {
              const index = currentUploadedFiles.indexOf(file);
              if (index > -1) {
                currentUploadedFiles.splice(index, 1);
              }
              previewDiv.remove();
            };
            previewDiv.appendChild(removeBtn);
            container.appendChild(previewDiv);
          };
          reader.readAsDataURL(file);
        });
      }
    }

    // Event Listeners para o modal de Adição
    addImagesUploadField.addEventListener('change', (event) => handleFileSelect(event, addImagePreviewsContainer, false));

    openCarouselProductModalBtn.addEventListener('click', () => {
      addCarouselProductForm.reset();
      addImagePreviewsContainer.innerHTML = '';
      addUploadedImageFiles = [];
      addImagesUploadField.value = ''; // Resetar o campo de arquivo
      addCarouselProductModal.classList.remove('hidden');
    });

    closeAddCarouselProductModalBtn.addEventListener('click', () => {
      addCarouselProductModal.classList.add('hidden');
      addCarouselProductForm.reset();
      addImagePreviewsContainer.innerHTML = '';
      addUploadedImageFiles = [];
    });

    window.addEventListener('click', (event) => {
      if (event.target === addCarouselProductModal) {
        addCarouselProductModal.classList.add('hidden');
        addCarouselProductForm.reset();
        addImagePreviewsContainer.innerHTML = '';
        addUploadedImageFiles = [];
      }
    });

    // Event Listeners para o modal de Edição
    editImagesUploadField.addEventListener('change', (event) => handleFileSelect(event, editImagePreviewsContainer, true));

    document.querySelectorAll('.edit-btn').forEach(button => {
      button.addEventListener('click', function (event) {
        editCarouselProductForm.reset(); // Limpa o formulário antes de preencher
        editImagesUploadField.value = ''; // Resetar o campo de arquivo
        editCarouselProductForm.action = '<?php echo $_SESSION['base_url']; ?>/api/post/update_carousel_product.php?id=' + (this.dataset.id || '');
        editMethodField.value = 'PUT';

        editProductIdField.value = this.dataset.id || '';
        document.getElementById('edit_name').value = this.dataset.name || '';
        document.getElementById('edit_theme').value = this.dataset.theme || '';
        document.getElementById('edit_category').value = this.dataset.category || '';
        document.getElementById('edit_credits').value = this.dataset.credits || '';
        document.getElementById('edit_type').value = this.dataset.type || '';

        document.getElementById('edit_description').value = this.dataset.description || '';
        document.getElementById('edit_page_count').value = this.dataset.page_count || 1;
        document.getElementById('edit_status').value = this.dataset.status || 'active';

        let selectedCustomizationTypes = [];
        if (this.dataset.customization_types) {
          try {
            selectedCustomizationTypes = JSON.parse(this.dataset.customization_types);
          } catch (e) {
            console.warn('Erro ao parsear customization_types:', e);
            selectedCustomizationTypes = [];
          }
        }

        const customizationTypesCheckboxes = editCarouselProductForm.querySelectorAll('input[name="customization_types[]"]');
        customizationTypesCheckboxes.forEach(checkbox => {
          checkbox.checked = selectedCustomizationTypes.includes(checkbox.value);
        });

        displayImagePreviews(this.dataset.images, editImagePreviewsContainer, true);

        editCarouselProductModal.classList.remove('hidden');
      });
    });

    closeEditCarouselProductModalBtn.addEventListener('click', () => {
      editCarouselProductModal.classList.add('hidden');
      editCarouselProductForm.reset();
      editImagePreviewsContainer.innerHTML = '';
      editUploadedImageFiles = [];
    });

    window.addEventListener('click', (event) => {
      if (event.target === editCarouselProductModal) {
        editCarouselProductModal.classList.add('hidden');
        editCarouselProductForm.reset();
        editImagePreviewsContainer.innerHTML = '';
        editUploadedImageFiles = [];
      }
    });

  })();
</script>
