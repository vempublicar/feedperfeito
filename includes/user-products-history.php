<?php
require_once __DIR__ . '/../api/get/get_user_products.php';

$user_products = [];

$user_products = get_user_products($_SESSION['user_email']);


?>

<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h3 class="text-lg font-bold text-black mb-4">Meus Produtos Contratados</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome Produto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ativo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entregue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Compra</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($user_products)): ?>
                    <?php foreach ($user_products as $product): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($product['sku']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($product['quantity']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars(number_format($product['price'], 2, ',', '.')); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if ($product['is_active']): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Sim</span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Não</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if ($product['is_delivered']): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Sim</span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Não</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($product['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Nenhum produto encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>