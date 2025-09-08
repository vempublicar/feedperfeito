<?php
// Simulated data for products
$produtos = [
  [
    'id' => 'PROD-1001',
    'nome' => 'Pacote 10 Posts',
    'categoria' => 'Posts',
    'preco' => 'R$ 499,90',
    'creditos' => 10,
    'status' => 'Ativo',
    'destaque' => true
  ],
  [
    'id' => 'PROD-1002',
    'nome' => 'Reels Setembro',
    'categoria' => 'Reels',
    'preco' => 'R$ 299,90',
    'creditos' => 8,
    'status' => 'Ativo',
    'destaque' => false
  ],
  [
    'id' => 'PROD-1003',
    'nome' => 'Cartão NFC',
    'categoria' => 'Serviços',
    'preco' => 'R$ 149,90',
    'creditos' => 0,
    'status' => 'Ativo',
    'destaque' => true
  ],
  [
    'id' => 'PROD-1004',
    'nome' => 'Landing Page',
    'categoria' => 'Serviços',
    'preco' => 'R$ 1.190,00',
    'creditos' => 0,
    'status' => 'Ativo',
    'destaque' => false
  ],
  [
    'id' => 'PROD-1005',
    'nome' => 'Edição de Vídeos',
    'categoria' => 'Vídeos',
    'preco' => 'R$ 399,90',
    'creditos' => 24,
    'status' => 'Inativo',
    'destaque' => false
  ]
];
?>

<div class="bg-white rounded-lg shadow-md p-6">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-black">Gerenciamento de Produtos</h2>
    <button class="bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300">
      <i class="fas fa-plus mr-2"></i> Novo Produto
    </button>
  </div>
  
  <div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div class="flex flex-col sm:flex-row gap-2">
        <select class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
          <option>Todas as Categorias</option>
          <option>Posts</option>
          <option>Reels</option>
          <option>Vídeos</option>
          <option>Serviços</option>
        </select>
        <select class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
          <option>Todos os Status</option>
          <option>Ativo</option>
          <option>Inativo</option>
        </select>
      </div>
      <div class="relative">
        <input type="text" placeholder="Buscar produtos..." class="pl-10 pr-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent w-full">
        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
      </div>
    </div>
  </div>
  
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($produtos as $produto): ?>
    <div class="border border-gray-200 rounded-lg overflow-hidden">
      <div class="p-5">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-lg font-bold text-black"><?= htmlspecialchars($produto['nome']) ?></h3>
            <p class="text-gray-500 text-sm"><?= htmlspecialchars($produto['categoria']) ?></p>
          </div>
          <?php if ($produto['destaque']): ?>
            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Destaque</span>
          <?php endif; ?>
        </div>
        
        <div class="mt-4">
          <p class="text-2xl font-bold text-black"><?= htmlspecialchars($produto['preco']) ?></p>
          <?php if ($produto['creditos'] > 0): ?>
            <p class="text-gray-600 mt-1">
              <i class="fas fa-coins text-yellow-500 mr-1"></i> <?= htmlspecialchars($produto['creditos']) ?> créditos
            </p>
          <?php endif; ?>
        </div>
        
        <div class="mt-4">
          <?php
          $statusClass = '';
          switch ($produto['status']) {
            case 'Ativo':
              $statusClass = 'bg-green-100 text-green-800';
              break;
            case 'Inativo':
              $statusClass = 'bg-gray-100 text-gray-800';
              break;
            default:
              $statusClass = 'bg-gray-100 text-gray-800';
          }
          ?>
          <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
            <?= htmlspecialchars($produto['status']) ?>
          </span>
        </div>
      </div>
      
      <div class="bg-gray-50 px-5 py-3 flex justify-between">
        <span class="text-gray-500 text-sm"><?= htmlspecialchars($produto['id']) ?></span>
        <div class="flex space-x-2">
          <a href="#" class="text-blue-600 hover:text-blue-900">
            <i class="fas fa-eye"></i>
          </a>
          <a href="#" class="text-green-600 hover:text-green-900">
            <i class="fas fa-edit"></i>
          </a>
          <a href="#" class="text-red-600 hover:text-red-900">
            <i class="fas fa-trash"></i>
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  
  <div class="mt-6 flex items-center justify-between">
    <div class="text-sm text-gray-500">
      Mostrando <span class="font-medium">1</span> a <span class="font-medium">5</span> de <span class="font-medium">24</span> resultados
    </div>
    <div class="flex space-x-2">
      <button class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
        Anterior
      </button>
      <button class="px-3 py-1 rounded bg-black text-white">
        1
      </button>
      <button class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
        2
      </button>
      <button class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
        3
      </button>
      <button class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
        Próximo
      </button>
    </div>
  </div>
</div>