<?php
// Simulated data for orders
$pedidos = [
  [
    'id' => 'PED-1024',
    'cliente' => 'André M.',
    'email' => 'andre@email.com',
    'servico' => 'Pacote 10 Posts',
    'status' => 'Em Produção',
    'data' => '2025-09-05',
    'valor' => 'R$ 499,90'
  ],
  [
    'id' => 'PED-1025',
    'cliente' => 'Maria S.',
    'email' => 'maria@email.com',
    'servico' => 'Reels Setembro',
    'status' => 'Aprovação',
    'data' => '2025-09-07',
    'valor' => 'R$ 299,90'
  ],
  [
    'id' => 'PED-1026',
    'cliente' => 'João P.',
    'email' => 'joao@email.com',
    'servico' => 'Cartão NFC',
    'status' => 'Confirmado',
    'data' => '2025-09-08',
    'valor' => 'R$ 149,90'
  ],
  [
    'id' => 'PED-1027',
    'cliente' => 'Ana R.',
    'email' => 'ana@email.com',
    'servico' => 'Landing Page',
    'status' => 'Concluído',
    'data' => '2025-09-01',
    'valor' => 'R$ 1.190,00'
  ],
  [
    'id' => 'PED-1028',
    'cliente' => 'Carlos M.',
    'email' => 'carlos@email.com',
    'servico' => 'Edição de Vídeos',
    'status' => 'Cancelado',
    'data' => '2025-09-03',
    'valor' => 'R$ 399,90'
  ]
];
?>

<div class="bg-white rounded-lg shadow-md p-6">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-black">Gerenciamento de Pedidos</h2>
    <button class="bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300">
      <i class="fas fa-plus mr-2"></i> Novo Pedido
    </button>
  </div>
  
  <div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div class="flex flex-col sm:flex-row gap-2">
        <select class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
          <option>Todos os Status</option>
          <option>Confirmado</option>
          <option>Em Produção</option>
          <option>Aprovação</option>
          <option>Concluído</option>
          <option>Cancelado</option>
        </select>
        <select class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
          <option>Todos os Serviços</option>
          <option>Pacote de Posts</option>
          <option>Reels</option>
          <option>Cartão NFC</option>
          <option>Landing Page</option>
          <option>Edição de Vídeos</option>
        </select>
      </div>
      <div class="relative">
        <input type="text" placeholder="Buscar pedidos..." class="pl-10 pr-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent w-full">
        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
      </div>
    </div>
  </div>
  
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead>
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serviço</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <?php foreach ($pedidos as $pedido): ?>
        <tr>
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black"><?= htmlspecialchars($pedido['id']) ?></td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div>
              <div class="text-sm font-medium text-black"><?= htmlspecialchars($pedido['cliente']) ?></div>
              <div class="text-sm text-gray-500"><?= htmlspecialchars($pedido['email']) ?></div>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($pedido['servico']) ?></td>
          <td class="px-6 py-4 whitespace-nowrap">
            <?php
            $statusClass = '';
            switch ($pedido['status']) {
              case 'Confirmado':
                $statusClass = 'bg-blue-100 text-blue-800';
                break;
              case 'Em Produção':
                $statusClass = 'bg-yellow-100 text-yellow-800';
                break;
              case 'Aprovação':
                $statusClass = 'bg-purple-100 text-purple-800';
                break;
              case 'Concluído':
                $statusClass = 'bg-green-100 text-green-800';
                break;
              case 'Cancelado':
                $statusClass = 'bg-red-100 text-red-800';
                break;
              default:
                $statusClass = 'bg-gray-100 text-gray-800';
            }
            ?>
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
              <?= htmlspecialchars($pedido['status']) ?>
            </span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($pedido['data']) ?></td>
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black"><?= htmlspecialchars($pedido['valor']) ?></td>
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
            <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">
              <i class="fas fa-eye"></i>
            </a>
            <a href="#" class="text-green-600 hover:text-green-900 mr-3">
              <i class="fas fa-edit"></i>
            </a>
            <a href="#" class="text-red-600 hover:text-red-900">
              <i class="fas fa-trash"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
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