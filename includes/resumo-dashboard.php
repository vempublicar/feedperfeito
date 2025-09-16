<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
  <div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center">
      <div class="rounded-full bg-blue-100 p-3 mr-4">
        <i class="fas fa-shopping-cart text-blue-500"></i>
      </div>
      <div>
        <p class="text-gray-500">Pedidos Ativos</p>
        <p class="text-2xl font-bold text-black">0</p>
      </div>
    </div>
  </div>
  
  <div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center">
      <div class="rounded-full bg-yellow-100 p-3 mr-4">
        <i class="fas fa-coins text-yellow-500"></i>
      </div>
      <div>
        <p class="text-gray-500">Seus Créditos</p>
        <p class="text-2xl font-bold text-black"><?php echo $_SESSION['user_credits']; ?></p>
      </div>
    </div>
  </div>
  
  <div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center">
      <div class="rounded-full bg-green-100 p-3 mr-4">
        <i class="fas fa-check-circle text-green-500"></i>
      </div>
      <div>
        <p class="text-gray-500">Aprovações Pendentes</p>
        <p class="text-2xl font-bold text-black">
          <?php
          $pendingApprovals = 0;
          // foreach ($orders as $order) {
          //   if ($order['status'] == 'in_approval') {
          //     $pendingApprovals++;
          //   }
          // }
          echo $pendingApprovals;
          ?>
        </p>
      </div>
    </div>
  </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-8">
  <h3 class="text-lg font-bold text-black mb-4">Pedidos em Andamento</h3>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead>
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serviço</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Atualizado</th>
        </tr>
      </thead>
      <!-- <tbody class="bg-white divide-y divide-gray-200">
        <?php
        $displayedOrders = 0;
        foreach ($orders as $order):
          if ($displayedOrders >= 3) break;
          $displayedOrders++;
        ?>
        <tr>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?php echo $order['id']; ?></td>
          <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black"><?php echo htmlspecialchars($order['title']); ?></td>
          <td class="px-6 py-4 whitespace-nowrap">
            <?php
            $statusClass = '';
            $statusText = '';
            switch ($order['status']) {
              case 'confirmed':
                $statusClass = 'bg-blue-100 text-blue-800';
                $statusText = 'Confirmado';
                break;
              case 'in_production':
                $statusClass = 'bg-yellow-100 text-yellow-800';
                $statusText = 'Em Produção';
                break;
              case 'in_approval':
                $statusClass = 'bg-green-100 text-green-800';
                $statusText = 'Aprovação';
                break;
              case 'download_available':
                $statusClass = 'bg-purple-100 text-purple-800';
                $statusText = 'Download';
                break;
              default:
                $statusClass = 'bg-gray-100 text-gray-800';
                $statusText = ucfirst($order['status']);
            }
            ?>
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusClass; ?>">
              <?php echo $statusText; ?>
            </span>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('d/m/Y H:i', strtotime($order['updated_at'])); ?></td>
        </tr>
        <?php endforeach; ?>
        
        <?php if ($displayedOrders == 0): ?>
        <tr>
          <td colspan="4" class="px-6 py-4 text-center text-gray-500">
            Nenhum pedido encontrado
          </td>
        </tr>
        <?php endif; ?>
      </tbody> -->
    </table>
  </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
  <h3 class="text-lg font-bold text-black mb-4">Últimos Conteúdos</h3>
  <!-- <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php
    $displayedTemplates = 0;
    foreach ($templates as $template):
      if ($displayedTemplates >= 2) break;
      $displayedTemplates++;
    ?>
    <div class="border border-gray-200 rounded p-4">
      <div class="flex items-center">
        <div class="w-16 h-16 rounded bg-gray-200 flex items-center justify-center mr-3">
          <i class="fas fa-image text-gray-500"></i>
        </div>
        <div>
          <p class="font-medium text-black"><?php echo htmlspecialchars($template['title']); ?></p>
          <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($template['category']); ?></p>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    
    <?php if ($displayedTemplates == 0): ?>
    <div class="col-span-3 text-center text-gray-500">
      Nenhum conteúdo disponível
    </div>
    <?php endif; ?>
  </div> -->
</div>