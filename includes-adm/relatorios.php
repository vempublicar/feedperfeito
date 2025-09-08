<?php
// Simulated data for reports
$vendasMensais = [
  ['mes' => 'Janeiro', 'valor' => 12500, 'pedidos' => 42],
  ['mes' => 'Fevereiro', 'valor' => 18600, 'pedidos' => 58],
  ['mes' => 'Março', 'valor' => 15200, 'pedidos' => 48],
  ['mes' => 'Abril', 'valor' => 21000, 'pedidos' => 68],
  ['mes' => 'Maio', 'valor' => 19800, 'pedidos' => 62],
  ['mes' => 'Junho', 'valor' => 24500, 'pedidos' => 78],
  ['mes' => 'Julho', 'valor' => 22300, 'pedidos' => 71],
  ['mes' => 'Agosto', 'valor' => 26800, 'pedidos' => 85],
  ['mes' => 'Setembro', 'valor' => 15600, 'pedidos' => 52]
];

$topProdutos = [
  ['nome' => 'Pacote 10 Posts', 'vendas' => 124, 'receita' => 61876],
  ['nome' => 'Reels Setembro', 'vendas' => 86, 'receita' => 25714],
  ['nome' => 'Cartão NFC', 'vendas' => 68, 'receita' => 10193],
  ['nome' => 'Landing Page', 'vendas' => 32, 'receita' => 38080],
  ['nome' => 'Edição de Vídeos', 'vendas' => 28, 'receita' => 11197]
];

$creditosVendidos = [
  ['mes' => 'Janeiro', 'creditos' => 1250],
  ['mes' => 'Fevereiro', 'creditos' => 1860],
  ['mes' => 'Março', 'creditos' => 1520],
  ['mes' => 'Abril', 'creditos' => 2100],
  ['mes' => 'Maio', 'creditos' => 1980],
  ['mes' => 'Junho', 'creditos' => 2450],
  ['mes' => 'Julho', 'creditos' => 2230],
  ['mes' => 'Agosto', 'creditos' => 2680],
  ['mes' => 'Setembro', 'creditos' => 1560]
];
?>

<div class="bg-white rounded-lg shadow-md p-6 mb-8">
  <h2 class="text-2xl font-bold text-black mb-6">Relatórios</h2>
  
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="border border-gray-200 rounded-lg p-5">
      <div class="flex items-center">
        <div class="rounded-full bg-blue-100 p-3 mr-4">
          <i class="fas fa-shopping-cart text-blue-500"></i>
        </div>
        <div>
          <p class="text-gray-500">Total Pedidos (Mês)</p>
          <p class="text-2xl font-bold text-black">142</p>
        </div>
      </div>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-5">
      <div class="flex items-center">
        <div class="rounded-full bg-green-100 p-3 mr-4">
          <i class="fas fa-chart-line text-green-500"></i>
        </div>
        <div>
          <p class="text-gray-500">Receita (Mês)</p>
          <p class="text-2xl font-bold text-black">R$ 42.850</p>
        </div>
      </div>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-5">
      <div class="flex items-center">
        <div class="rounded-full bg-yellow-100 p-3 mr-4">
          <i class="fas fa-coins text-yellow-500"></i>
        </div>
        <div>
          <p class="text-gray-500">Créditos Vendidos (Mês)</p>
          <p class="text-2xl font-bold text-black">1,240</p>
        </div>
      </div>
    </div>
  </div>
  
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="border border-gray-200 rounded-lg p-5">
      <h3 class="text-lg font-bold text-black mb-4">Vendas Mensais (R$)</h3>
      <div class="h-64 flex items-end space-x-2">
        <?php foreach ($vendasMensais as $venda): ?>
          <div class="flex flex-col items-center flex-1">
            <div class="w-full bg-blue-500 rounded-t" style="height: <?= ($venda['valor'] / 30000) * 200 ?>px;"></div>
            <div class="text-xs text-gray-500 mt-2"><?= substr($venda['mes'], 0, 3) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-5">
      <h3 class="text-lg font-bold text-black mb-4">Créditos Vendidos</h3>
      <div class="h-64 flex items-end space-x-2">
        <?php foreach ($creditosVendidos as $credito): ?>
          <div class="flex flex-col items-center flex-1">
            <div class="w-full bg-yellow-500 rounded-t" style="height: <?= ($credito['creditos'] / 3000) * 200 ?>px;"></div>
            <div class="text-xs text-gray-500 mt-2"><?= substr($credito['mes'], 0, 3) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
  <div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-bold text-black mb-4">Top 5 Produtos</h3>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead>
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendas</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receita</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php foreach ($topProdutos as $produto): ?>
          <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black"><?= htmlspecialchars($produto['nome']) ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($produto['vendas']) ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">R$ <?= number_format($produto['receita'], 0, ',', '.') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  
  <div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-bold text-black mb-4">Últimos Pedidos</h3>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead>
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">#PED-1024</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">André M.</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">R$ 499,90</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">05/09/2025</td>
          </tr>
          <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">#PED-1025</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Maria S.</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">R$ 299,90</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">07/09/2025</td>
          </tr>
          <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">#PED-1026</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">João P.</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">R$ 149,90</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">08/09/2025</td>
          </tr>
          <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">#PED-1027</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Ana R.</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">R$ 1.190,00</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">01/09/2025</td>
          </tr>
          <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">#PED-1028</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Carlos M.</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">R$ 399,90</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">03/09/2025</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>