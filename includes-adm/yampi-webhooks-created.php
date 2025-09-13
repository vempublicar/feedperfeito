<?php
require_once __DIR__ . '/../api/get/get_yampi_webhooks.php';

$created_webhooks = get_yampi_webhooks('order.created');

?>

<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h3 class="text-lg font-bold text-black mb-4">Últimos Pedidos Criados (order.created)</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Yampi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número Pedido</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método Pagamento</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Evento</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($created_webhooks)): ?>
                    <?php foreach ($created_webhooks as $webhook): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black"><?php echo htmlspecialchars($webhook['yampi_order_id']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($webhook['customer_email']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($webhook['customer_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($webhook['order_number']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($webhook['total_value']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($webhook['order_status']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($webhook['payment_method']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($webhook['event_time']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">Nenhum webhook de pedido criado encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>