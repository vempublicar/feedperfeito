<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/Purchase.php';

// Iniciar a sessão se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;
$pedidos = [];

if ($userId) {
    $purchaseModel = new Purchase();
    // Fetch purchases for the current user, ordered by created_at descending
    // and excluding 'canceled' or 'Entregue' statuses.
    $userPurchases = $purchaseModel->query('purchases?user_id=eq.' . $userId . '&status=neq.canceled&status=neq.Entregue&order=created_at.desc');

    if ($userPurchases && is_array($userPurchases)) {
        foreach ($userPurchases as $purchase) {
            $pedidos[] = [
                'id' => $purchase['id'],
                'titulo' => $purchase['product_name'],
                'etapa_atual' => ucfirst($purchase['status']), // Use the status from the database
                'status' => $purchase['status'], // Adicionar o status original
                'credits_used' => $purchase['credits_used'], // Adicionar os créditos usados
                'updated_at' => $purchase['updated_at'] ?? $purchase['created_at'],
                'unique_code' => $purchase['unique_code']
            ];
        }
    }
}

// Adicionando a etapa "Pendente"
$etapas = ['Pendente', 'Confirmado', 'Produção', 'Aprovação', 'Disponível'];

function etapaIndex($etapaAtual) {
    switch ($etapaAtual) {
        case 'Pendente': return 0;
        case 'Confirmado': return 1;
        case 'Produção': return 2;
        case 'Aprovação': return 3;
        case 'Disponível':
        case 'Entregue': return 4;
        default: return 0; // Default to 'Pendente'
    }
}
?>

<section id="painel" class="py-12 bg-background">
  <div class="container mx-auto px-4">
    <div class="mb-12">
      <h2 class="text-3xl font-bold text-black mb-2">
        Andamento <span class="font-light">dos Pedidos</span>
      </h2>
      <div class="w-20 h-1 bg-black mb-4"></div>
      <p class="text-gray-600">Acompanhe o status de produção dos seus conteúdos.</p>
    </div>

    <?php foreach ($pedidos as $pedido):
      $idxAtual = etapaIndex($pedido['etapa_atual']);
    ?>
      <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 flex-wrap gap-4">
            <div>
              <h5 class="text-xl font-bold text-black mb-1"><?= htmlspecialchars($pedido['titulo']) ?></h5>
              <p class="text-gray-500 text-sm">#<?= htmlspecialchars($pedido['id']) ?> • Atualizado em <?= date('d/m/Y H:i', strtotime($pedido['updated_at'])) ?></p>
            </div>
            <div class="mt-2 md:mt-0 flex justify-between relative">
               <form action="editar-pedidos" method="post" >
                   <input type="hidden" name="unique_code" value="<?= htmlspecialchars($pedido['unique_code']) ?>">
                   <input type="hidden" name="id" value="<?= htmlspecialchars($pedido['id']) ?>">
                   <button type="submit" class="bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300 inline-block">
                       Abrir Pedido
                   </button>
               </form>
               <?php if ($pedido['status'] === 'pending'): ?>
                   <form action="../api/post/cancelar_compra.php" method="post" class="inline-block ml-2">
                       <input type="hidden" name="purchaseId" value="<?= htmlspecialchars($pedido['id']) ?>">
                       <input type="hidden" name="creditsToRefund" value="<?= htmlspecialchars($pedido['credits_used']) ?>">
                       <button type="submit" class="bg-red-500 text-white py-2 px-4 rounded font-medium hover:bg-red-600 transition duration-300">
                           Estornar Pedido
                       </button>
                   </form>
               <?php endif; ?>
            </div>
          </div>

          <!-- Steps -->
          <div class="flex justify-between relative pt-8">
            <?php foreach ($etapas as $i => $etapa):
              $done  = ($i < $idxAtual);
              $active = ($i === $idxAtual);
            ?>
              <div class="flex flex-col items-center relative z-10 flex-1">
                <div class="w-8 h-8 rounded-full flex items-center justify-center mb-2
                  <?= $done ? 'bg-black text-white' : '' ?>
                  <?= $active ? 'bg-black text-white border-4 border-gray-300' : '' ?>
                  <?= (!$done && !$active) ? 'bg-gray-200 text-gray-500' : '' ?>">
                  <?php if ($done): ?>
                    <i class="fas fa-check"></i>
                  <?php else: ?>
                    <span><?= $i+1 ?></span>
                  <?php endif; ?>
                </div>
                <div class="text-center text-sm
                  <?= $done || $active ? 'text-black font-medium' : 'text-gray-500' ?>">
                  <?= $etapa ?>
                </div>
                <?php if ($i < count($etapas)-1): ?>
                  <div class="absolute top-4 left-1/2 w-full h-1 -z-10
                    <?= $done ? 'bg-black' : 'bg-gray-200' ?>"
                    style="left: 50%; width: 100%; transform: translateX(-50%);"></div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
