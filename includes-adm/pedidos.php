<?php
require_once __DIR__ . '/../models/Purchase.php';
require_once __DIR__ . '/../models/User.php'; // Adicionado para buscar informações do usuário

$purchaseModel = new Purchase();
$userModel = new User();

$allPurchases = $purchaseModel->query('purchases?order=created_at.desc');

$pedidos = [];
if ($allPurchases && is_array($allPurchases)) {
    foreach ($allPurchases as $purchase) {
        // Excluir pedidos com status 'canceled' ou 'Entregue'
        if ($purchase['status'] === 'canceled' || $purchase['status'] === 'Entregue') {
            continue;
        }

        $user = $userModel->find($purchase['user_id']); // Busca o usuário pelo ID
        $clienteNome = $user['name'] ?? 'Desconhecido';
        $clienteEmail = $user['email'] ?? 'Desconhecido';

        $pedidos[] = [
            'id' => $purchase['id'],
            'cliente' => $clienteNome,
            'email' => $clienteEmail,
            'servico' => $purchase['product_name'],
            'status' => ucfirst($purchase['status']), // Capitaliza a primeira letra
            'data' => date('Y-m-d', strtotime($purchase['created_at'])),
            'valor' => 'R$ ' . number_format($purchase['credits_used'], 2, ',', '.') // Formata como moeda
        ];
    }
}
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-black">Gerenciamento de Pedidos</h2>
    </div>

    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-2">
                <select id="filtroStatus"
                    class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
                    <option value="">Todos os Status</option>
                    <option value="Confirmado">Confirmado</option>
                    <option value="Em Produção">Em Produção</option>
                    <option value="Aprovação">Aprovação</option>
                    <option value="Concluído">Concluído</option>
                    <option value="pending">Pendente</option>
                </select>
                <input type="text" id="filtroServico" placeholder="Filtrar por serviço..."
                    class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
            </div>
            <div class="relative">
                <input type="text" id="buscaPedidos" placeholder="Buscar pedidos..."
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent w-full">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serviço
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="tabelaPedidosBody">
                <!-- Pedidos serão carregados aqui pelo JavaScript -->
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex items-center justify-between">
        <div class="text-sm text-gray-500">
            Mostrando <span class="font-medium" id="pedidosExibidos">0</span> de <span class="font-medium"
                id="totalPedidos">0</span> resultados
        </div>
        <div class="flex space-x-2">
            <button id="prevPage"
                class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                Anterior
            </button>
            <span id="paginationInfo" class="px-3 py-1 text-gray-700"></span>
            <button id="nextPage"
                class="px-3 py-1 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">
                Próximo
            </button>
        </div>
    </div>
</div>

<script>
    const allPedidos = <?= json_encode($pedidos); ?>;
    let currentPage = 1;
    const rowsPerPage = 5; // Número de pedidos por página

    function renderPedidos(pedidosToRender) {
        const tabelaPedidosBody = document.getElementById('tabelaPedidosBody');
        tabelaPedidosBody.innerHTML = '';

        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        const paginatedPedidos = pedidosToRender.slice(startIndex, endIndex);

        paginatedPedidos.forEach(pedido => {
            let statusClass = '';
            switch (pedido.status) {
                case 'Confirmado':
                    statusClass = 'bg-blue-100 text-blue-800';
                    break;
                case 'Em Produção':
                    statusClass = 'bg-yellow-100 text-yellow-800';
                    break;
                case 'Aprovação':
                    statusClass = 'bg-purple-100 text-purple-800';
                    break;
                case 'Concluído':
                    statusClass = 'bg-green-100 text-green-800';
                    break;
                case 'Pendente':
                    statusClass = 'bg-gray-100 text-gray-800';
                    break;
                default:
                    statusClass = 'bg-gray-100 text-gray-800';
            }

            const row = `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">${pedido.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <div class="text-sm font-medium text-black">${pedido.cliente}</div>
                            <div class="text-sm text-gray-500">${pedido.email}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${pedido.servico}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                            ${pedido.status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${pedido.data}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">${pedido.valor}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <form action="producao-pedidos" method="post" class="inline-block">
                            <input type="hidden" name="pedido_id" value="${pedido.id}">
                            <button type="submit" class="text-indigo-600 hover:text-indigo-900">
                                <i class="fas fa-eye mr-1"></i> Abrir Pedido
                            </button>
                        </form>
                    </td>
                </tr>
            `;
            tabelaPedidosBody.innerHTML += row;
        });

        document.getElementById('pedidosExibidos').textContent = paginatedPedidos.length;
        document.getElementById('totalPedidos').textContent = pedidosToRender.length;
        document.getElementById('paginationInfo').textContent = `Página ${currentPage} de ${Math.ceil(pedidosToRender.length / rowsPerPage)}`;

        document.getElementById('prevPage').disabled = currentPage === 1;
        document.getElementById('nextPage').disabled = currentPage === Math.ceil(pedidosToRender.length / rowsPerPage);
    }

    function filterAndSearchPedidos() {
        const statusFilter = document.getElementById('filtroStatus').value.toLowerCase();
        const servicoFilter = document.getElementById('filtroServico').value.toLowerCase();
        const searchInput = document.getElementById('buscaPedidos').value.toLowerCase();

        const filteredPedidos = allPedidos.filter(pedido => {
            const matchesStatus = statusFilter === '' || String(pedido.status).toLowerCase() === statusFilter;
            const matchesServico = servicoFilter === '' || String(pedido.servico).toLowerCase().includes(servicoFilter);
            const matchesSearch = String(pedido.id).toLowerCase().includes(searchInput) ||
                                  String(pedido.cliente).toLowerCase().includes(searchInput) ||
                                  String(pedido.email).toLowerCase().includes(searchInput) ||
                                  String(pedido.servico).toLowerCase().includes(searchInput) ||
                                  String(pedido.status).toLowerCase().includes(searchInput) ||
                                  String(pedido.data).toLowerCase().includes(searchInput) ||
                                  String(pedido.valor).toLowerCase().includes(searchInput);
            return matchesStatus && matchesServico && matchesSearch;
        });
        currentPage = 1; // Reseta para a primeira página após a filtragem
        renderPedidos(filteredPedidos);
    }

    document.getElementById('filtroStatus').addEventListener('change', filterAndSearchPedidos);
    document.getElementById('filtroServico').addEventListener('input', filterAndSearchPedidos);
    document.getElementById('buscaPedidos').addEventListener('input', filterAndSearchPedidos);
    document.getElementById('prevPage').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            filterAndSearchPedidos();
        }
    });
    document.getElementById('nextPage').addEventListener('click', () => {
        const totalPages = Math.ceil(allPedidos.length / rowsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            filterAndSearchPedidos();
        }
    });

    // Renderiza os pedidos inicialmente
    filterAndSearchPedidos();
</script>