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
    // Fetch all purchases for the current user, ordered by created_at descending
    $userPurchases = $purchaseModel->query('purchases?user_id=eq.' . $userId . '&order=created_at.desc');

    if ($userPurchases && is_array($userPurchases)) {
        foreach ($userPurchases as $purchase) {
            $pedidos[] = [
                'id' => $purchase['id'],
                'titulo' => $purchase['product_name'],
                'etapa_atual' => ucfirst($purchase['status']), // Use the status from the database
                'status' => $purchase['status'], // Adicionar o status original para estilização
                'updated_at' => $purchase['updated_at'] ?? $purchase['created_at'],
                'unique_code' => $purchase['unique_code']
            ];
        }
    }
}

// Mapeamento de status para classes CSS Tailwind
function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'confirmado':
            return 'bg-blue-100 text-blue-800';
        case 'produção':
            return 'bg-purple-100 text-purple-800';
        case 'aprovação':
            return 'bg-indigo-100 text-indigo-800';
        case 'disponível':
            return 'bg-green-100 text-green-800';
        case 'entregue':
            return 'bg-green-200 text-green-900';
        case 'canceled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}
?>

<section id="painel" class="py-12 bg-background">
  <div class="container mx-auto px-4">
    <div class="mb-12">
      <h2 class="text-3xl font-bold text-black mb-2">
        Histórico <span class="font-light">de Pedidos</span>
      </h2>
      <div class="w-20 h-1 bg-black mb-4"></div>
      <p class="text-gray-600">Visualize o histórico completo dos seus pedidos.</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pedido</th> -->
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Atualização</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código Único</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($pedidos)): ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($pedido['id']) ?></td> -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($pedido['titulo']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= getStatusClass($pedido['status']) ?>">
                                        <?= htmlspecialchars($pedido['etapa_atual']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($pedido['updated_at'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($pedido['unique_code']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Nenhum pedido encontrado no histórico.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
  </div>
</section>