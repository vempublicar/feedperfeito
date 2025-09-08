<?php
// Simulated data for clients
$clientes = [
  [
    'id' => 'CLI-1001',
    'nome' => 'André M.',
    'email' => 'andre@email.com',
    'telefone' => '(11) 99999-9999',
    'data_cadastro' => '2025-08-15',
    'ultima_compra' => '2025-09-05',
    'status' => 'Ativo',
    'creditos' => 85
  ],
  [
    'id' => 'CLI-1002',
    'nome' => 'Maria S.',
    'email' => 'maria@email.com',
    'telefone' => '(11) 88888-8888',
    'data_cadastro' => '2025-08-20',
    'ultima_compra' => '2025-09-07',
    'status' => 'Ativo',
    'creditos' => 120
  ],
  [
    'id' => 'CLI-1003',
    'nome' => 'João P.',
    'email' => 'joao@email.com',
    'telefone' => '(11) 77777-7777',
    'data_cadastro' => '2025-09-01',
    'ultima_compra' => '2025-09-08',
    'status' => 'Ativo',
    'creditos' => 45
  ],
  [
    'id' => 'CLI-1004',
    'nome' => 'Ana R.',
    'email' => 'ana@email.com',
    'telefone' => '(11) 66666-6666',
    'data_cadastro' => '2025-07-22',
    'ultima_compra' => '2025-08-30',
    'status' => 'Inativo',
    'creditos' => 0
  ],
  [
    'id' => 'CLI-1005',
    'nome' => 'Carlos M.',
    'email' => 'carlos@email.com',
    'telefone' => '(11) 55555-5555',
    'data_cadastro' => '2025-08-05',
    'ultima_compra' => '2025-09-03',
    'status' => 'Bloqueado',
    'creditos' => 20
  ]
];
?>

<div class="bg-white rounded-lg shadow-md p-6">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-black">Gerenciamento de Clientes</h2>
    <button class="bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300">
      <i class="fas fa-plus mr-2"></i> Novo Cliente
    </button>
  </div>
  
  <div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div class="flex flex-col sm:flex-row gap-2">
        <select class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
          <option>Todos os Status</option>
          <option>Ativo</option>
          <option>Inativo</option>
          <option>Bloqueado</option>
        </select>
      </div>
      <div class="relative">
        <input type="text" placeholder="Buscar clientes..." class="pl-10 pr-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent w-full">
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
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contato</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Cadastro</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Compra</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créditos</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <?php foreach ($clientes as $cliente): ?>
        <tr>
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black"><?= htmlspecialchars($cliente['id']) ?></td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-black"><?= htmlspecialchars($cliente['nome']) ?></div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm text-gray-500"><?= htmlspecialchars($cliente['email']) ?></div>
            <div class="text-sm text-gray-500"><?= htmlspecialchars($cliente['telefone']) ?></div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($cliente['data_cadastro']) ?></td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($cliente['ultima_compra']) ?></td>
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">
            <i class="fas fa-coins text-yellow-500 mr-1"></i> <?= htmlspecialchars($cliente['creditos']) ?>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <?php
            $statusClass = '';
            switch ($cliente['status']) {
              case 'Ativo':
                $statusClass = 'bg-green-100 text-green-800';
                break;
              case 'Inativo':
                $statusClass = 'bg-gray-100 text-gray-800';
                break;
              case 'Bloqueado':
                $statusClass = 'bg-red-100 text-red-800';
                break;
              default:
                $statusClass = 'bg-gray-100 text-gray-800';
            }
            ?>
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
              <?= htmlspecialchars($cliente['status']) ?>
            </span>
          </td>
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
      Mostrando <span class="font-medium">1</span> a <span class="font-medium">5</span> de <span class="font-medium">42</span> resultados
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