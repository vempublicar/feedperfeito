<?php
require_once __DIR__ . '/../models/FeedProduct.php';

$feedProductModel = new FeedProduct();
$feedProducts = $feedProductModel->all();
?>

<div class="bg-white rounded-lg shadow-md p-6">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-black">Produtos do Feed</h2>
    <button id="openFeedProductModal"
      class="bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300">
      <i class="fas fa-plus mr-2"></i> Novo Produto
    </button>
  </div>

  <!-- Tabela -->
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Imagem</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tema</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoria</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilização</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Créditos</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Páginas</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Download</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
        </tr>
      </thead>
      <tbody id="feedProductsTableBody" class="bg-white divide-y divide-gray-200">
        <?php if (!empty($feedProducts)): ?>
          <?php foreach ($feedProducts as $product): ?>
            <tr>
              <td class="px-6 py-4"><?php echo htmlspecialchars($product['id']); ?></td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex -space-x-2 overflow-hidden">
                  <?php
                  $images = $product['images'] ? json_decode($product['images'], true) : [];
                  if (!empty($images)) {
                    foreach ($images as $index => $image) {
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
              <td class="px-6 py-4"><?php echo htmlspecialchars($product['name']); ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($product['theme'] ?? '-'); ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($product['category'] ?? '-'); ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($product['type'] ?? '-'); ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($product['utilization'] ?? '-'); ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($product['credits']); ?></td>
              <td class="px-6 py-4"><?php echo htmlspecialchars($product['page_count'] ?? '-'); ?></td>
              <td class="px-6 py-4">
                <?php if (!empty($product['download'])): ?>
                <a href="#"
                    onclick="openDownloadWindow('<?php echo $_SESSION['base_url']; ?>/download.php?file=<?php echo urlencode($product['download']); ?>'); return false;"
                    class="text-blue-600 hover:text-blue-900">
                    Download
                  </a>
                  <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <td class="px-6 py-4">
                <span
                  class="px-2 inline-flex text-xs font-semibold rounded-full
                  <?php echo $product['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                  <?php echo htmlspecialchars($product['status']); ?>
                </span>
              </td>
              <td class="px-6 py-4 text-right text-sm">
                <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-2 edit-btn"
                  data-id="<?php echo htmlspecialchars($product['id']); ?>"
                  data-name="<?php echo htmlspecialchars($product['name']); ?>"
                  data-theme="<?php echo htmlspecialchars($product['theme'] ?? ''); ?>"
                  data-category="<?php echo htmlspecialchars($product['category'] ?? ''); ?>"
                  data-credits="<?php echo htmlspecialchars($product['credits']); ?>"
                  data-description="<?php echo htmlspecialchars($product['description'] ?? ''); ?>"
                  data-page_count="<?php echo htmlspecialchars($product['page_count'] ?? ''); ?>"
                  data-status="<?php echo htmlspecialchars($product['status']); ?>"
                  data-images='<?php echo $product['images'] ? $product['images'] : "[]"; ?>'
                  data-type="<?php echo htmlspecialchars($product['type'] ?? ''); ?>"
                  data-utilization="<?php echo htmlspecialchars($product['utilization'] ?? ''); ?>"
                  data-customization_types='<?php echo $product['customization_types'] ? $product['customization_types'] : "[]"; ?>'
                  data-download="<?php echo htmlspecialchars($product['download'] ?? ''); ?>">
                   Editar</a>
                <form action="<?php echo $_SESSION['base_url']; ?>/api/delete/feed_product.php" method="POST"
                  style="display:inline;" onsubmit="return confirm('Excluir este produto?');">
                  <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                  <input type="hidden" name="_method" value="DELETE">
                  <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="11" class="px-6 py-4 text-center text-gray-500">Nenhum produto encontrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Adicionar -->
<div id="addFeedProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
  <div class="relative top-20 mx-auto p-5 border w-2/3 shadow-lg rounded-md bg-white">
    <div class="flex justify-between items-center pb-3">
      <h3 class="text-lg font-medium text-gray-900">Adicionar Produto</h3>
      <button id="closeAddFeedProductModal" class="text-gray-400 hover:text-gray-500"><i
          class="fas fa-times"></i></button>
    </div>
    <form id="addFeedProductForm" action="<?php echo $_SESSION['base_url']; ?>/api/post/insert_feed_product.php"
      method="POST" enctype="multipart/form-data">
      <input type="hidden" name="_method" value="POST">
      <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
        <div class="md:col-span-2">
          <label for="add_images_upload" class="block text-sm font-medium text-gray-700">Imagens</label>
          <input type="file" name="images_upload[]" id="add_images_upload" multiple accept="image/*"
            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800">
          <p class="mt-1 text-sm text-gray-500">Selecione até 10 imagens.</p>
          <div id="add_image_previews" class="mt-2 flex flex-wrap gap-2">
            <!-- Miniaturas das imagens serão exibidas aqui -->
          </div>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
          <label for="add_name" class="block text-sm font-medium text-gray-700">Nome</label>
          <input type="text" name="name" id="add_name" class="mt-1 block w-full border rounded p-2" required>
        </div>

        <div>
          <label for="add_theme" class="block text-sm font-medium text-gray-700">Tema</label>
          <input type="text" name="theme" id="add_theme" class="mt-1 block w-full border rounded p-2">
        </div>

        <div>
          <label for="add_category" class="block text-sm font-medium text-gray-700">Categoria</label>
          <input type="text" name="category" id="add_category" class="mt-1 block w-full border rounded p-2">
        </div>

        <div>
              <label for="add_type" class="block text-sm font-medium text-gray-700">Tipo</label>
              <select name="type" id="add_type"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">
                <option value="">Selecione</option>
                <option value="Destaque">Destaque</option>
                <option value="Novidade">Novidade</option>
                <option value="Promocao">Promoção</option>
                <option value="Pronto">Pronto</option>
                <!-- Outras opções podem ser adicionadas -->
              </select>
            </div>

            <!-- Campo de Upload de Download (inicialmente oculto) -->
            <div id="add_download_field" class="hidden">
              <label for="add_download" class="block text-sm font-medium text-gray-700">Arquivo para Download</label>
              <input type="file" name="download[]" id="add_download" multiple
                accept=".zip,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,image/*"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800">
              <p class="mt-1 text-sm text-gray-500">Selecione um ou mais arquivos (ZIP, PDF, DOC, DOCX, XLS, XLSX, PPT,
                PPTX, Imagens).</p>
              <div id="add_download_previews" class="mt-2 flex flex-wrap gap-2"></div>
            </div>

        <div>
          <label for="add_utilization" class="block text-sm font-medium text-gray-700">Utilização</label>
          <select name="utilization" id="add_utilization" class="mt-1 block w-full border rounded p-2">
            <option value="">Selecione</option>
            <option value="Feed">Feed</option>
            <option value="Stories">Stories</option>
            <option value="Capa">Capa</option>
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Tipos de Personalização</label>
          <div class="mt-1 grid grid-cols-2 sm:grid-cols-3 gap-2">
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
            <label class="inline-flex items-center">
              <input type="checkbox" name="customization_types[]" value="mockup" class="form-checkbox">
              <span class="ml-2 text-sm text-gray-700">Mockup</span>
            </label>
          </div>
        </div>

        <div>
          <label for="add_credits" class="block text-sm font-medium text-gray-700">Créditos</label>
          <input type="number" name="credits" id="add_credits" class="mt-1 block w-full border rounded p-2" required>
        </div>

        <div>
          <label for="add_page_count" class="block text-sm font-medium text-gray-700">Qtd de Páginas</label>
          <input type="number" name="page_count" id="add_page_count" class="mt-1 block w-full border rounded p-2"
            value="1">
        </div>

        <div>
          <label for="add_status" class="block text-sm font-medium text-gray-700">Status</label>
          <select name="status" id="add_status" class="mt-1 block w-full border rounded p-2">
            <option value="active">Ativo</option>
            <option value="inactive">Inativo</option>
            <option value="draft">Rascunho</option>
          </select>
        </div>

        <div class="md:col-span-2">
          <label for="add_description" class="block text-sm font-medium text-gray-700">Descrição</label>
          <textarea name="description" id="add_description" rows="3"
            class="mt-1 block w-full border rounded p-2"></textarea>
        </div>


      </div>

      <div class="mt-6">
        <button type="submit" class="bg-black text-white px-4 py-2 rounded">Adicionar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar -->
<div id="editFeedProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
  <div class="relative top-20 mx-auto p-5 border w-2/3 shadow-lg rounded-md bg-white">
    <div class="flex justify-between items-center pb-3">
      <h3 class="text-lg font-medium text-gray-900">Editar Produto</h3>
      <button id="closeEditFeedProductModal" class="text-gray-400 hover:text-gray-500"><i
          class="fas fa-times"></i></button>
    </div>
    <form id="editFeedProductForm" action="<?php echo $_SESSION['base_url']; ?>/api/post/update_feed_product.php"
      method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" id="edit_feed_id">
      <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
        <input type="hidden" name="_method" value="PUT">
        <div class="md:col-span-2">
          <label for="edit_images_upload" class="block text-sm font-medium text-gray-700">Imagens</label>
          <input type="file" name="images_upload[]" id="edit_images_upload" multiple accept="image/*"
            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800">
          <p class="mt-1 text-sm text-gray-500">Selecione até 10 imagens.</p>
        </div>
        <div id="edit_image_previews" class="mt-2 flex flex-wrap gap-2">
          <!-- Miniaturas das imagens serão exibidas aqui -->
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="edit_feed_name" class="block text-sm font-medium text-gray-700">Nome</label>
          <input type="text" name="name" id="edit_feed_name" class="mt-1 block w-full border rounded p-2" required>
        </div>

        <div>
          <label for="edit_feed_theme" class="block text-sm font-medium text-gray-700">Tema</label>
          <input type="text" name="theme" id="edit_feed_theme" class="mt-1 block w-full border rounded p-2">
        </div>

        <div>
          <label for="edit_feed_category" class="block text-sm font-medium text-gray-700">Categoria</label>
          <input type="text" name="category" id="edit_feed_category" class="mt-1 block w-full border rounded p-2">
        </div>

        <div>
          <label for="edit_feed_credits" class="block text-sm font-medium text-gray-700">Créditos</label>
          <input type="number" name="credits" id="edit_feed_credits" class="mt-1 block w-full border rounded p-2">
        </div>

        <div>
              <label for="edit_feed_type" class="block text-sm font-medium text-gray-700">Tipo</label>
              <select name="type" id="edit_feed_type"
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">
                <option value="">Selecione</option>
                <option value="Destaque">Destaque</option>
                <option value="Novidade">Novidade</option>
                <option value="Promocao">Promoção</option>
                <option value="Pronto">Pronto</option>
                <option value="Pronto">Pronto</option>
                <!-- Outras opções podem ser adicionadas -->
              </select>
            </div>

            <!-- Campo de Upload de Download (inicialmente oculto) -->
            <div id="edit_download_field" class="hidden">
              <label for="edit_download" class="block text-sm font-medium text-gray-700">Arquivo para Download</label>
              <input type="file" name="download[]" id="edit_download" multiple
                accept=".zip,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,image/*"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800">
              <p class="mt-1 text-sm text-gray-500">Selecione um ou mais arquivos (ZIP, PDF, DOC, DOCX, XLS, XLSX, PPT,
                PPTX, Imagens).</p>
              <div id="edit_download_previews" class="mt-2 flex flex-wrap gap-2"></div>
            </div>

        <div>
          <label for="edit_feed_utilization" class="block text-sm font-medium text-gray-700">Utilização</label>
          <select name="utilization" id="edit_feed_utilization" class="mt-1 block w-full border rounded p-2">
            <option value="">Selecione</option>
            <option value="Feed">Feed</option>
            <option value="Stories">Stories</option>
            <option value="Capa">Capa</option>
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Tipos de Personalização</label>
          <div id="edit_customization_types_checkboxes" class="mt-1 grid grid-cols-2 sm:grid-cols-3 gap-2">
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
            <label class="inline-flex items-center">
              <input type="checkbox" name="customization_types[]" value="mockup" class="form-checkbox">
              <span class="ml-2 text-sm text-gray-700">Mockup</span>
            </label>
          </div>
        </div>

        <div>
          <label for="edit_feed_page_count" class="block text-sm font-medium text-gray-700">Qtd de Páginas</label>
          <input type="number" name="page_count" id="edit_feed_page_count" class="mt-1 block w-full border rounded p-2">
        </div>

        <div>
          <label for="edit_feed_status" class="block text-sm font-medium text-gray-700">Status</label>
          <select name="status" id="edit_feed_status" class="mt-1 block w-full border rounded p-2">
            <option value="active">Ativo</option>
            <option value="inactive">Inativo</option>
            <option value="draft">Rascunho</option>
          </select>
        </div>

        <div class="md:col-span-2">
          <label for="edit_feed_description" class="block text-sm font-medium text-gray-700">Descrição</label>
          <textarea name="description" id="edit_feed_description" rows="3"
            class="mt-1 block w-full border rounded p-2"></textarea>
        </div>


      </div>

      <div class="mt-6">
        <button type="submit" class="bg-black text-white px-4 py-2 rounded">Salvar</button>
      </div>
    </form>
  </div>
</div>

<script>
  (function () {
    // Garante que o script só seja executado uma vez por carregamento de seção
    if (window.feedperfeito && window.feedperfeito.feedProductScriptInitialized) {
      return;
    }
    if (!window.feedperfeito) {
      window.feedperfeito = {};
    }
    window.feedperfeito.feedProductScriptInitialized = true;

    // Referências para o modal de Adição
    const addFeedProductModal = document.getElementById('addFeedProductModal');
    const openFeedProductModalBtn = document.getElementById('openFeedProductModal');
    const closeAddFeedProductModalBtn = document.getElementById('closeAddFeedProductModal');
    const addFeedProductForm = document.getElementById('addFeedProductForm');
    const addImagesUploadField = document.getElementById('add_images_upload');
    const addImagePreviewsContainer = document.getElementById('add_image_previews');
    const addTypeField = document.getElementById('add_type');
    const addDownloadField = document.getElementById('add_download_field');
    const addDownloadInput = document.getElementById('add_download');
    const addDownloadPreviewsContainer = document.getElementById('add_download_previews');
    let addUploadedDownloadFiles = []; // Adicionado para gerenciar múltiplos arquivos

    // Referências para o modal de Edição
    const editFeedProductModal = document.getElementById('editFeedProductModal');
    const closeEditFeedProductModalBtn = document.getElementById('closeEditFeedProductModal');
    const editFeedProductForm = document.getElementById('editFeedProductForm');
    const editFeedIdField = document.getElementById('edit_feed_id');
    const editImagesUploadField = document.getElementById('edit_images_upload');
    const editImagePreviewsContainer = document.getElementById('edit_image_previews');
    const editTypeField = document.getElementById('edit_feed_type');
    const editDownloadField = document.getElementById('edit_download_field');
    const editDownloadInput = document.getElementById('edit_download');
    const editDownloadPreviewsContainer = document.getElementById('edit_download_previews');
    let editUploadedImageFiles = [];
    let addUploadedImageFiles = []; // Variável para armazenar os arquivos de imagem para o modal de adição
    let editUploadedDownloadFiles = []; // Adicionado para gerenciar múltiplos arquivos

    // Funções para exibir previews de imagens (adaptadas para ambos os modais)
    // Armazena as URLs das imagens existentes que devem ser mantidas
    let existingImageUrlsToKeep = [];
    // --- Funções auxiliares ---

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
      const filesSelectedByUser = Array.from(event.target.files);
      const currentUploadedFilesArray = isEditModal ? editUploadedImageFiles : addUploadedImageFiles;

      // Limpa o array de arquivos e o container de previews
      currentUploadedFilesArray.length = 0;
      container.innerHTML = '';

      if (filesSelectedByUser.length > 0) {
        filesSelectedByUser.forEach(file => {
          currentUploadedFilesArray.push(file); // Adiciona os novos arquivos ao array
        });

        // Gera e exibe os previews para todos os arquivos no array
        currentUploadedFilesArray.forEach(file => {
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
              const index = currentUploadedFilesArray.indexOf(file);
              if (index > -1) {
                currentUploadedFilesArray.splice(index, 1);
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

    function handleDownloadFileSelect(event, container, isEditModal = false) {
      container.innerHTML = ''; // Limpa previews anteriores
      const currentUploadedFiles = isEditModal ? editUploadedDownloadFiles : addUploadedDownloadFiles;
      currentUploadedFiles.length = 0; // Limpa a lista de arquivos para upload

      const files = event.target.files;
      if (files.length > 0) {
        Array.from(files).forEach(file => {
          currentUploadedFiles.push(file);

          const previewDiv = document.createElement('div');
          previewDiv.className = 'relative w-24 h-24 flex items-center justify-center bg-gray-100 rounded-md overflow-hidden';

          let filePreview;
          if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'w-full h-full object-cover';
            filePreview = img;
          } else {
            const icon = document.createElement('i');
            icon.className = 'fas fa-file text-4xl text-gray-400';
            filePreview = icon;
          }
          previewDiv.appendChild(filePreview);

          const fileNameSpan = document.createElement('span');
          fileNameSpan.className = 'absolute bottom-0 w-full bg-black bg-opacity-50 text-white text-xs text-center truncate px-1 py-0.5';
          fileNameSpan.textContent = file.name;
          previewDiv.appendChild(fileNameSpan);

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
        });
      }
    }

    function toggleDownloadField(typeField, downloadField) {
      if (typeField.value === 'Pronto') {
        downloadField.classList.remove('hidden');
      } else {
        downloadField.classList.add('hidden');
        // Limpar campo de download se o tipo não for 'Pronto'
        const downloadInput = downloadField.querySelector('input[type="file"]');
        if (downloadInput) {
          downloadInput.value = '';
        }
        const downloadPreview = downloadField.querySelector('div');
        if (downloadPreview) {
          downloadPreview.innerHTML = '';
        }
      }
    }

    // Event Listeners para o modal de Adição
    addImagesUploadField.addEventListener('change', (event) => handleFileSelect(event, addImagePreviewsContainer, false));
    addDownloadInput.addEventListener('change', (event) => handleDownloadFileSelect(event, addDownloadPreviewsContainer, false));
    addTypeField.addEventListener('change', () => toggleDownloadField(addTypeField, addDownloadField));

    openFeedProductModalBtn.addEventListener('click', () => {
      addFeedProductForm.reset();
      addImagePreviewsContainer.innerHTML = '';
      addImagesUploadField.value = '';
      addDownloadInput.value = ''; // Resetar o campo de download
      addDownloadPreviewsContainer.innerHTML = ''; // Limpar previews do download
      addUploadedDownloadFiles = []; // Limpar lista de arquivos de download
      toggleDownloadField(addTypeField, addDownloadField); // Esconder/mostrar baseado no tipo inicial
      addFeedProductModal.classList.remove('hidden');
    });

    closeAddFeedProductModalBtn.addEventListener('click', () => {
      addFeedProductModal.classList.add('hidden');
      addFeedProductForm.reset();
      addImagePreviewsContainer.innerHTML = '';
      addImagesUploadField.value = '';
      addDownloadPreviewsContainer.innerHTML = '';
      addUploadedDownloadFiles = [];
    });

    window.addEventListener('click', (event) => {
      if (event.target === addFeedProductModal) {
        addFeedProductModal.classList.add('hidden');
        addFeedProductForm.reset();
        addImagePreviewsContainer.innerHTML = '';
        addImagesUploadField.value = '';
        addDownloadPreviewsContainer.innerHTML = '';
        addUploadedDownloadFiles = [];
      }
    });

    // --- Modal de Edição ---
    editImagesUploadField.addEventListener('change', (event) => handleFileSelect(event, editImagePreviewsContainer, true));
    editDownloadInput.addEventListener('change', (event) => handleDownloadFileSelect(event, editDownloadPreviewsContainer, true));
    editTypeField.addEventListener('change', () => toggleDownloadField(editTypeField, editDownloadField));

    document.querySelectorAll('.edit-btn').forEach(button => {
      button.addEventListener('click', function () {
        editFeedProductForm.reset();
        editImagePreviewsContainer.innerHTML = '';
        editImagesUploadField.value = ''; // Resetar o campo de arquivo
        editDownloadInput.value = ''; // Resetar o campo de download
        editDownloadPreviewsContainer.innerHTML = ''; // Limpar previews do download
        editUploadedDownloadFiles = []; // Limpar lista de arquivos de download

        editFeedIdField.value = this.dataset.id || '';
        document.getElementById('edit_feed_name').value = this.dataset.name || '';
        document.getElementById('edit_feed_theme').value = this.dataset.theme || '';
        document.getElementById('edit_feed_category').value = this.dataset.category || '';
        document.getElementById('edit_feed_credits').value = this.dataset.credits || '';
        editTypeField.value = this.dataset.type || ''; // Usar editTypeField para triggerar o evento change
        document.getElementById('edit_feed_utilization').value = this.dataset.utilization || '';
        document.getElementById('edit_feed_page_count').value = this.dataset.page_count || 1;
        document.getElementById('edit_feed_status').value = this.dataset.status || 'active';
        document.getElementById('edit_feed_description').value = this.dataset.description || '';

        // Preencher checkboxes de customization_types
        const customizationTypes = this.dataset.customization_types ? JSON.parse(this.dataset.customization_types) : [];
        document.querySelectorAll('#edit_customization_types_checkboxes input[type="checkbox"]').forEach(checkbox => {
          checkbox.checked = customizationTypes.includes(checkbox.value);
        });

        // mostra imagens já salvas
        displayImagePreviews(this.dataset.images, editImagePreviewsContainer, true);

        // Preencher e exibir os previews do download, se houver
        if (this.dataset.download) {
            try {
                const downloads = JSON.parse(this.dataset.download);
                downloads.forEach(downloadUrl => {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'relative w-24 h-24 flex items-center justify-center bg-gray-100 rounded-md overflow-hidden';

                    let filePreview;
                    const fileName = downloadUrl.split('/').pop();
                    const fileExtension = fileName.split('.').pop().toLowerCase();

                    if (['jpg', 'png', 'jpeg', 'gif'].includes(fileExtension)) {
                        const img = document.createElement('img');
                        img.src = downloadUrl;
                        img.className = 'w-full h-full object-cover';
                        filePreview = img;
                    } else {
                        const icon = document.createElement('i');
                        icon.className = 'fas fa-file text-4xl text-gray-400';
                        filePreview = icon;
                    }
                    previewDiv.appendChild(filePreview);

                    const fileNameSpan = document.createElement('span');
                    fileNameSpan.className = 'absolute bottom-0 w-full bg-black bg-opacity-50 text-white text-xs text-center truncate px-1 py-0.5';
                    fileNameSpan.textContent = fileName;
                    previewDiv.appendChild(fileNameSpan);

                    const removeBtn = document.createElement('button');
                    removeBtn.innerHTML = '&times;';
                    removeBtn.className = 'absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold -mt-2 -mr-2 cursor-pointer';
                    removeBtn.onclick = () => {
                        previewDiv.remove();
                        // Remover da lista de downloads existentes a serem mantidos
                        const index = existingDownloadUrlsToKeep.indexOf(downloadUrl);
                        if (index > -1) {
                            existingDownloadUrlsToKeep.splice(index, 1);
                        }
                    };
                    previewDiv.appendChild(removeBtn);
                    editDownloadPreviewsContainer.appendChild(previewDiv);
                    existingDownloadUrlsToKeep.push(downloadUrl);
                });
            } catch (e) {
                console.error("Erro ao parsear downloads existentes:", e);
            }
        }
        toggleDownloadField(editTypeField, editDownloadField); // Esconder/mostrar baseado no tipo do produto existente

        editFeedProductModal.classList.remove('hidden');
      });
    });

    closeEditFeedProductModalBtn.addEventListener('click', () => {
      editFeedProductModal.classList.add('hidden');
      editFeedProductForm.reset();
      editImagePreviewsContainer.innerHTML = '';
      editImagesUploadField.value = '';
      editDownloadInput.value = '';
      editDownloadPreviewsContainer.innerHTML = '';
      editUploadedDownloadFiles = [];
    });

    window.addEventListener('click', (event) => {
      if (event.target === editFeedProductModal) {
        editFeedProductModal.classList.add('hidden');
        editFeedProductForm.reset();
        editImagePreviewsContainer.innerHTML = '';
        editImagesUploadField.value = '';
      }
    });
    function openDownloadWindow(url) {
      const width = 800;
      const height = 600;
      const left = (window.screen.width / 2) - (width / 2);
      const top = (window.screen.height / 2) - (height / 2);
      window.open(url, 'DownloadWindow', `width=${width},height=${height},top=${top},left=${left},toolbar=no,menubar=no,location=no,status=no,resizable=yes,scrollbars=yes`);
    }
    window.openDownloadWindow = function (url) { // Tornar a função global
      const width = 800;
      const height = 600;
      const left = (window.screen.width / 2) - (width / 2);
      const top = (window.screen.height / 2) - (height / 2);
      window.open(url, '_blank', `width=${width},height=${height},top=${top},left=${left},toolbar=no,menubar=no,location=no,status=no,resizable=yes,scrollbars=yes`);
    }

  })();
</script>