<?php
require_once __DIR__ . '/../models/CreditPackage.php';

$creditPackageModel = new CreditPackage();
$creditProducts = $creditPackageModel->all();
?>

<div class="bg-white rounded-lg shadow-md p-6">
  <?php if (isset($_SESSION['status_message'])): ?>
    <div class="mb-4 p-4 rounded-md <?php echo $_SESSION['status_type'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
      <?php echo htmlspecialchars($_SESSION['status_message']); ?>
    </div>
    <?php
    unset($_SESSION['status_message']);
    unset($_SESSION['status_type']);
    ?>
  <?php endif; ?>

  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-black">Produtos de Crédito</h2>
    <button id="openCreditProductModal" class="bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300">
      <i class="fas fa-plus mr-2"></i> Novo Produto
    </button>
  </div>
  
  <!-- Tabela de Produtos de Crédito -->
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créditos</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bônus</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tag</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
        </tr>
      </thead>
      <tbody id="creditProductsTableBody" class="bg-white divide-y divide-gray-200">
        <?php if (!empty($creditProducts)): ?>
          <?php foreach ($creditProducts as $product): ?>
            <tr>
              <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($product['id']); ?></td>
              <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($product['title']); ?></td>
              <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($product['credits']); ?></td>
              <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($product['bonus_credits']); ?></td>
              <td class="px-6 py-4 whitespace-nowrap">R$ <?php echo htmlspecialchars(number_format($product['price'], 2, ',', '.')); ?></td>
              <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($product['tag'] ?? '-'); ?></td>
              <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($product['link'] ?? '-'); ?></td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $product['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                  <?php echo $product['is_active'] ? 'Ativo' : 'Inativo'; ?>
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-2 edit-btn"
                   data-id="<?php echo htmlspecialchars($product['id']); ?>"
                   data-title="<?php echo htmlspecialchars($product['title']); ?>"
                   data-credits="<?php echo htmlspecialchars($product['credits']); ?>"
                   data-bonus_credits="<?php echo htmlspecialchars($product['bonus_credits']); ?>"
                   data-price="<?php echo htmlspecialchars($product['price']); ?>"
                   data-tag="<?php echo htmlspecialchars($product['tag'] ?? ''); ?>"
                   data-link="<?php echo htmlspecialchars($product['link'] ?? ''); ?>"
                   data-is_active="<?php echo htmlspecialchars($product['is_active']); ?>">Editar</a>
                <form action="<?php echo $_SESSION['base_url']; ?>/api/delete/credit_product.php" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este produto de crédito?');">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="text-red-600 hover:text-red-900 delete-btn" style="background:none; border:none; padding:0; cursor:pointer;">Excluir</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" class="px-6 py-4 text-center text-gray-500">Nenhum produto de crédito encontrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Modal para Adicionar/Editar Produto de Crédito -->
  <div id="creditProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="flex justify-between items-center pb-3">
        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">Adicionar Produto de Crédito</h3>
        <button id="closeCreditProductModal" class="text-gray-400 hover:text-gray-500">
          <span class="sr-only">Fechar</span>
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="mt-2">
        <!-- Formulário de Produto de Crédito (incorporado) -->
        <form id="creditProductForm" action="<?php echo $_SESSION['base_url']; ?>/api/post/insert_credit_product.php" method="POST">
          <input type="hidden" name="id" id="product_id">
          <input type="hidden" name="_method" id="_method" value="POST"> <!-- Para method spoofing -->
          <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
            <!-- Título -->
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
              <input type="text" name="title" id="title" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm" required>
            </div>
            
            <!-- Créditos -->
            <div>
              <label for="credits" class="block text-sm font-medium text-gray-700">Créditos Cedidos</label>
              <input type="number" name="credits" id="credits" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm" required>
            </div>
            
            <!-- Bônus de Créditos -->
            <div>
              <label for="bonus_credits" class="block text-sm font-medium text-gray-700">Bônus de Créditos</label>
              <input type="number" name="bonus_credits" id="bonus_credits" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm" value="0">
            </div>
            
            <!-- Preço -->
            <div>
              <label for="price" class="block text-sm font-medium text-gray-700">Preço</label>
              <input type="number" name="price" id="price" step="0.01" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm" required>
            </div>
            
            <!-- Tag -->
            <div>
              <label for="tag" class="block text-sm font-medium text-gray-700">Tag</label>
              <input type="text" name="tag" id="tag" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">
            </div>
            
            <!-- Link -->
            <div>
              <label for="link" class="block text-sm font-medium text-gray-700">Link de Pagamento</label>
              <input type="url" name="link" id="link" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-black focus:border-black sm:text-sm">
            </div>

            <!-- Ativo -->
            <div class="flex items-center">
              <input type="checkbox" name="is_active" id="is_active" class="h-4 w-4 text-black focus:ring-black border-gray-300 rounded">
              <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700">Ativo</label>
            </div>
          </div>
          
          <div class="mt-6">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
              Salvar Produto de Crédito
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const creditProductModal = document.getElementById('creditProductModal');
  const openCreditProductModalBtn = document.getElementById('openCreditProductModal');
  const closeCreditProductModalBtn = document.getElementById('closeCreditProductModal');
  const creditProductForm = document.getElementById('creditProductForm');
  const modalTitle = document.getElementById('modalTitle');
  const productIdField = document.getElementById('product_id');
  const methodField = document.getElementById('_method'); // Novo campo para method spoofing

  openCreditProductModalBtn.addEventListener('click', () => {
    modalTitle.textContent = 'Adicionar Produto de Crédito';
    creditProductForm.action = '<?php echo $_SESSION['base_url']; ?>/api/post/insert_credit_product.php';
    methodField.value = 'POST';
    creditProductForm.reset();
    productIdField.value = ''; // Limpa o ID para nova criação
    creditProductModal.classList.remove('hidden');
  });

  closeCreditProductModalBtn.addEventListener('click', () => {
    creditProductModal.classList.add('hidden');
    creditProductForm.reset();
  });

  window.addEventListener('click', (event) => {
    if (event.target === creditProductModal) {
      creditProductModal.classList.add('hidden');
      creditProductForm.reset();
    }
  });

  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function(event) {
      event.preventDefault();
      modalTitle.textContent = 'Editar Produto de Crédito';
      creditProductForm.action = '<?php echo $_SESSION['base_url']; ?>/api/post/update_credit_product.php'; // Aponta para o endpoint de update
      methodField.value = 'PUT'; // Define o método para PUT via spoofing

      productIdField.value = this.dataset.id;
      document.getElementById('title').value = this.dataset.title;
      document.getElementById('credits').value = this.dataset.credits;
      document.getElementById('bonus_credits').value = this.dataset.bonus_credits;
      document.getElementById('price').value = this.dataset.price;
      document.getElementById('tag').value = this.dataset.tag;
      document.getElementById('link').value = this.dataset.link;
      document.getElementById('is_active').checked = this.dataset.is_active == 1;
      creditProductModal.classList.remove('hidden');
    });
  });
</script>