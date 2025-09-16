<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/CarouselProduct.php';
require_once __DIR__ . '/../models/FeedProduct.php';
require_once __DIR__ . '/../models/MultipleProduct.php'; // Adicionado o modelo MultipleProduct
require_once __DIR__ . '/../models/Purchase.php';
require_once __DIR__ . '/../models/AprovacaoPedido.php';

// Iniciar a sessão se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redireciona se o usuário não estiver logado
requireUserLogin();

$pedidoId = $_POST['pedido_id'] ?? null;

// Verifica se há dados de resposta na sessão
if (isset($_SESSION['form_response'])) {
    $response = $_SESSION['form_response'];
    unset($_SESSION['form_response']); // Limpa a sessão após o uso

    // Se o pedido_id veio da sessão, ele tem prioridade
    if (isset($response['pedido_id'])) {
        $pedidoId = $response['pedido_id'];
    }

}
if ($pedidoId == null) {
    $pedidoId = $_SESSION['ultimo_pedido_id'];
}else{
    $_SESSION['ultimo_pedido_id'] = $pedidoId;
}

$product = null;
$productType = null;
$purchase = null;

if ($pedidoId) {
    $purchaseModel = new Purchase();
    $purchases = $purchaseModel->where(['id' => $pedidoId]);
    if (!empty($purchases)) {
        $purchase = $purchases[0];
        $uniqueCode = $purchase['unique_code'];

        if ($uniqueCode) {
            // Tenta buscar em carousel_products
            if (strpos($uniqueCode, 'carousel_') === 0) {
                $carouselProductModel = new CarouselProduct();
                $products = $carouselProductModel->where(['unique_code' => $uniqueCode]);
                if (!empty($products)) {
                    $product = $products[0];
                    $productType = 'Carousel';
                }
            }

            // Se não encontrou, tenta buscar em feed_products
            if (!$product && strpos($uniqueCode, 'feed_') === 0) {
                $feedProductModel = new FeedProduct();
                $products = $feedProductModel->where(['unique_code' => $uniqueCode]);
                if (!empty($products)) {
                    $product = $products[0];
                    $productType = 'Feed';
                }
            }

            // Se não encontrou, tenta buscar em multiple_products
            if (!$product && strpos($uniqueCode, 'multiple_') === 0) {
                $multipleProductModel = new MultipleProduct();
                $products = $multipleProductModel->where(['unique_code' => $uniqueCode]);
                if (!empty($products)) {
                    $product = $products[0];
                    $productType = 'Multiple';
                }
            }
        }
    }
}

$aprovacaoPedido = null;
if ($pedidoId) {
    $aprovacaoPedidoModel = new AprovacaoPedido();
    $aprovacao = $aprovacaoPedidoModel->where(['pedido_id' => $pedidoId]);
    if (!empty($aprovacao)) {
        $aprovacaoPedido = $aprovacao[0];
    }
}
$status = $purchase['status'] ?? 'N/A'; // Define $status para ser usado no HTML

// Adicionando a etapa "Pendente"
$etapas = ['Pendente', 'Confirmado', 'Produção', 'Aprovação', 'Disponível'];

function etapaIndex($etapaAtual)
{
    switch ($etapaAtual) {
        case 'Pendente':
            return 0;
        case 'Confirmado':
            return 1;
        case 'Produção':
            return 2;
        case 'Aprovação':
            return 3;
        case 'Disponível':
        case 'Entregue':
            return 4;
        default:
            return 0; // Default to 'Pendente'
    }
}
print_r($aprovacao);
?>

<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Detalhes do Pedido</h2>
        <div class="w-20 h-1 bg-black mb-6"></div>

        <?php if ($product && $purchase):
            $idxAtual = etapaIndex(ucfirst($status)); // Usa a variável $status definida acima
            $images = json_decode($product['images'], true) ?: [];
            ?>

            <!-- Grupo de imagens -->
            <?php if (!empty($images)): ?>
                <h4 class="text-lg font-semibold mb-3">Exemplo Solicitado</h4>
                <div class="bg-gray-50 rounded-lg p-3 mb-6 overflow-x-auto flex gap-2">

                    <?php foreach ($images as $img): ?>
                        <div class="flex-shrink-0 h-[300px] bg-white rounded shadow overflow-hidden">
                            <img src="<?= htmlspecialchars($img) ?>" alt="Imagem Produto" class="h-full w-auto object-contain">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <h4 class="text-lg font-semibold mb-3">Andamento do Processo</h4>

            <div class="flex justify-between relative pt-4 pb-10">
                <?php foreach ($etapas as $i => $etapa):
                    $done = ($i < $idxAtual);
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
                                <span><?= $i + 1 ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="text-center text-sm
                                    <?= $done || $active ? 'text-black font-medium' : 'text-gray-500' ?>">
                            <?= $etapa ?>
                        </div>
                        <?php if ($i < count($etapas) - 1): ?>
                            <div class="absolute top-4 left-1/2 w-full h-1 -z-10
                                        <?= $done ? 'bg-black' : 'bg-gray-200' ?>"
                                style="left: 50%; width: 100%; transform: translateX(-50%);"></div>
                        <?php endif; ?>
                        <button class="update-status-btn bg-black text-white px-4 py-2 rounded hover:bg-gray-800 mt-2"
                            data-purchase-id="<?= htmlspecialchars($purchase['id']) ?>"
                            data-new-status="<?= strtolower($etapa) ?>"
                            data-user-id="<?= htmlspecialchars($purchase['user_id']) ?>"
                            data-unique-code="<?= htmlspecialchars($purchase['unique_code']) ?>">
                            Mover para <?= htmlspecialchars($etapa) ?>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php
                        $aprovacaoImagens = [];
                        $aprovacaoObservacoes = '';
                        if ($aprovacaoPedido && isset($aprovacaoPedido['imagens'])) {
                            if (is_array($aprovacaoPedido['imagens'])) {
                                $aprovacaoImagens = $aprovacaoPedido['imagens'];
                            } elseif (is_string($aprovacaoPedido['imagens'])) {
                                $aprovacaoImagens = json_decode($aprovacaoPedido['imagens'], true) ?? [];
                            }
                        }
                        // As observações de aprovação não são armazenadas diretamente, mas fazem parte da conversa
                        // Para exibir a última observação, teríamos que parsear a conversa, o que é mais complexo.
                        // Por simplicidade, o campo de observações será sempre vazio para novas entradas.
                        ?>

                        <?php if (!empty($aprovacaoImagens)): ?>
                            <h4 class="text-md font-semibold mb-2">Modificação para Aprovação.</h4>
                            <div class="bg-gray-50 rounded-lg p-3 mb-6 overflow-x-auto flex gap-2">
                                <?php foreach ($aprovacaoImagens as $img): ?>
                                    <div class="flex-shrink-0 h-[300px] bg-white rounded shadow overflow-hidden">
                                        <img src="<?= $_SESSION['base_url'] . htmlspecialchars($img) ?>" alt="Imagem Aprovacao" class="h-full w-auto object-contain">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2 mb-2">
                <?php if ($aprovacaoPedido): ?>
                    <div class="bg-gray-50 rounded-lg p-4 md:col-span-1">
                        <h3 class="text-lg font-semibold mb-3">Chat</h3>
                        <!-- Conteúdo do chat aqui -->
                        <div id="chat-box"
                            class="flex-1 overflow-y-auto border border-gray-200 rounded-lg p-3 mb-3 bg-white max-h-full py-2">
                            <?php
                            $conversa = [];
                            if (isset($aprovacaoPedido['conversa']) && is_string($aprovacaoPedido['conversa'])) {
                                $conversa = json_decode($aprovacaoPedido['conversa'], true) ?? [];
                            } elseif (isset($aprovacaoPedido['conversa']) && is_array($aprovacaoPedido['conversa'])) {
                                $conversa = $aprovacaoPedido['conversa'];
                            }

                            if (!empty($conversa)) {
                                foreach ($conversa as $msg) {
                                    $isCurrentUser = ($msg['sender'] === ($_SESSION['user_name'] ?? 'admin')); // Assumindo 'admin' para o lado de produção
                                    $justifyClass = $isCurrentUser ? 'justify-end' : 'justify-start';
                                    $bgColorClass = $isCurrentUser ? 'bg-black text-white' : 'bg-gray-200 text-gray-800';
                                    echo '<div class="flex mb-2 ' . $justifyClass . '">';
                                    echo '<div class="max-w-[70%] p-3 rounded-lg shadow-md text-sm ' . $bgColorClass . '">';
                                    echo '<strong>' . htmlspecialchars($msg['sender']) . ':</strong> ' . htmlspecialchars($msg['message']);
                                    echo '</div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p class="text-sm text-gray-500 text-center">Nenhuma mensagem ainda.</p>';
                            }
                            ?>
                        </div>
                        <form id="chat-form" action="../api/post/insert_aprovacao_chat.php" method="POST" class="flex">
                            <input type="hidden" name="aprovacao_id"
                                value="<?= htmlspecialchars($aprovacaoPedido['id'] ?? '') ?>">
                            <input type="hidden" name="pedido_id" value="<?= htmlspecialchars($pedidoId ?? '') ?>">
                            <input type="hidden" name="unique_code" value="<?= htmlspecialchars($uniqueCode ?? '') ?>">
                            <textarea name="message" rows="1"
                                class="flex-1 border border-gray-300 rounded-l px-3 py-2 focus:ring-2 focus:ring-black resize-y"
                                placeholder="Digite sua mensagem..."></textarea>
                            <button type="submit" class="bg-black text-white px-4 rounded-r hover:bg-gray-800"><i
                                    class="fas fa-paper-plane"></i></button>
                        </form>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 md:col-span-1">
                        <h3 class="text-lg font-semibold mb-3">Upload de Arquivos para Aprovação</h3>
                        
                        <form action="../api/post/insert_aprovacao_pedido.php" method="POST"
                            enctype="multipart/form-data">
                            <input type="hidden" name="uid_usuario_pedido"
                                value="<?= htmlspecialchars($purchase['user_id']) ?>">
                            <input type="hidden" name="unique_code" value="<?= htmlspecialchars($purchase['unique_code']) ?>">
                            <input type="hidden" name="pedido_id" value="<?= htmlspecialchars($pedidoId) ?>">

                            <div class="mb-4">
                                <label for="imagens" class="block text-sm font-medium text-gray-700">Imagens
                                    (Múltiplas):</label>
                                <input type="file" name="imagens[]" id="imagens" multiple
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800"
                                    accept="image/*">
                            </div>

                            <div class="mb-4">
                                <label for="aprovacao" class="block text-sm font-medium text-gray-700">Status de
                                    Aprovação:</label>
                                <select name="aprovacao" id="aprovacao"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-black focus:border-black sm:text-sm rounded-md">
                                    <option value="Aprovação" <?= ($aprovacaoPedido && $aprovacaoPedido['aprovacao'] == 'Aprovação') ? 'selected' : '' ?>>Aprovação</option>
                                    <option value="Produção" <?= ($aprovacaoPedido && $aprovacaoPedido['aprovacao'] == 'Produção') ? 'selected' : '' ?>>Produção</option>
                                    <option value="Revisão" <?= ($aprovacaoPedido && $aprovacaoPedido['aprovacao'] == 'Revisão') ? 'selected' : '' ?>>Revisão</option>
                                    <option value="Disponível" <?= ($aprovacaoPedido && $aprovacaoPedido['aprovacao'] == 'Disponível') ? 'selected' : '' ?>>Disponível</option>
                                    <option value="Entregue" <?= ($aprovacaoPedido && $aprovacaoPedido['aprovacao'] == 'Entregue') ? 'selected' : '' ?>>Entregue</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="observacoes_aprovacao" class="block text-sm font-medium text-gray-700">Observações
                                    (para aprovação):</label>
                                <textarea name="observacoes_aprovacao" id="observacoes_aprovacao" rows="3"
                                    class="shadow-sm focus:ring-black focus:border-black mt-1 block w-full sm:text-sm border border-gray-300 rounded-md p-2"
                                    placeholder="Adicione observações sobre a aprovação..."><?= isset($aprovacaoPedido['observacoes']) ? htmlspecialchars(json_decode($aprovacaoPedido['observacoes'])) : '' ?></textarea>
                            </div>

                            <button type="submit"
                                class="bg-black text-white px-4 py-2 rounded-md font-medium hover:bg-gray-800 transition">
                                Cadastrar Aprovação
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
                <!-- Novo Formulário para Upload de ZIP -->
                <div class="bg-gray-50 rounded-lg p-4 md:col-span-1">
                    <h3 class="text-lg font-semibold mb-3">Upload de Arquivo de Entrega (ZIP)</h3>
                    <form action="../api/upload/upload_entrega_zip.php" method="POST"
                        enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($purchase['user_id']) ?>">
                        <input type="hidden" name="purchase_id" value="<?= htmlspecialchars($purchase['id']) ?>">
                        <div class="mb-4">
                            <label for="zip_file" class="block text-sm font-medium text-gray-700">Selecione o arquivo
                                ZIP:</label>
                            <input type="file" name="zip_file" id="zip_file" accept=".zip"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800">
                        </div>
                        <button type="submit"
                            class="bg-black text-white px-4 py-2 rounded-md font-medium hover:bg-gray-800 transition">
                            Fazer Upload do ZIP
                        </button>
                    </form>
                </div>
            </div>

            <!-- Conteúdo em colunas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Coluna esquerda (dados gerais) -->
                <div class="bg-gray-50 rounded-lg p-4 md:col-span-1">
                    <h3 class="text-lg font-semibold mb-3">Detalhes do Produto</h3>
                    <p class="text-sm text-gray-700 mb-2"><strong>Nome:</strong> <?= htmlspecialchars($product['name']) ?>
                    </p>
                    <p class="text-sm text-gray-700 mb-2"><strong>Tipo:</strong> <?= htmlspecialchars($productType) ?></p>
                    <p class="text-sm text-gray-700 mb-2"><strong>Código:</strong>
                        <?= htmlspecialchars($product['unique_code']) ?></p>
                    <p class="text-sm text-gray-700 mb-2"><strong>Categoria:</strong>
                        <?= htmlspecialchars($product['category']) ?></p>
                    <p class="text-sm text-gray-700 mb-2"><strong>Tema:</strong>
                        <?= htmlspecialchars($product['theme'] ?? 'N/A') ?></p>
                    <p class="text-sm text-gray-700 mb-2"><strong>Créditos:</strong> <?= (int) ($product['credits'] ?? 0) ?>
                    </p>
                    <p class="text-sm text-gray-700 mb-2"><strong>Páginas:</strong>
                        <?= htmlspecialchars($product['page_count'] ?? 'N/A') ?></p>
                    <p class="text-sm text-gray-700 mb-2"><strong>Customização:</strong>
                        <?= htmlspecialchars($product['customization_types'] ?? 'N/A') ?></p>
                    <p class="text-sm text-gray-700"><strong>Status Pedido:</strong> <span
                            id="current-status"><?= htmlspecialchars($status) ?></span></p>
                    <p class="text-sm text-gray-700 mt-4"><strong>Observações do Cliente:</strong></p>
                    <p class="text-sm text-gray-700 italic">
                        <?= htmlspecialchars($purchase['observations'] ?? 'Nenhuma observação.') ?>
                    </p>
                    <p class="text-sm text-gray-700 mt-4"><strong>Customização Solicitada:</strong></p>
                    <p class="text-sm text-gray-700 italic">
                        <?= htmlspecialchars($purchase['customization'] ?? 'Nenhuma customização específica.') ?>
                    </p>
                </div>

                <!-- Coluna direita (chat / ações por status) -->
                <div class="bg-gray-50 rounded-lg p-4 md:col-span-2 text-center">
                    <h3 class="text-lg font-semibold mb-3">Detalhes do Cliente</h3>
                    <?php
                    // Obter o UID do usuário do pedido
                    $uid_usuario_pedido = $purchase['user_id'] ?? null;
                    $user_doc_path = __DIR__ . '/../doc/' . $uid_usuario_pedido;

                    // Função auxiliar para carregar dados de JSON
                    function load_json_data_adm($file_path) {
                        if (file_exists($file_path)) {
                            return json_decode(file_get_contents($file_path), true);
                        }
                        return [];
                    }

                    // Carregar dados das seções do cliente
                    $redes_sociais_cliente = load_json_data_adm($user_doc_path . '/redes_sociais.json');
                    $cores_cliente = load_json_data_adm($user_doc_path . '/cores.json');
                    $temas_interesse_cliente = load_json_data_adm($user_doc_path . '/temas_interesse.json');
                    $segmento_cliente = load_json_data_adm($user_doc_path . '/segmento.json');
                    $textos_personalizados_cliente = load_json_data_adm($user_doc_path . '/textos_personalizados.json');
                    $logotipos_cliente = load_json_data_adm($user_doc_path . '/logotipos.json');
                    $imagens_artes_cliente = load_json_data_adm($user_doc_path . '/imagens_artes.json');
                    $elementos_design_cliente = load_json_data_adm($user_doc_path . '/elementos_design.json');
                    $imagens_empresa_cliente = load_json_data_adm($user_doc_path . '/imagens_empresa.json');
                    $produtos_cliente = load_json_data_adm($user_doc_path . '/produtos.json');
                    ?>

                    <!-- Redes Sociais -->
                    <div class="mb-4 border rounded-lg overflow-hidden">
                        <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="redes-sociais-cliente-collapse">
                            <h4 class="font-semibold text-gray-700">Redes Sociais</h4>
                            <i class="fas fa-chevron-down text-gray-500"></i>
                        </div>
                        <div id="redes-sociais-cliente-collapse" class="p-4 hidden">
                            <?php if (!empty(array_filter($redes_sociais_cliente))): ?>
                                <p class="text-sm text-gray-700 mb-1"><strong>Instagram:</strong> <?= htmlspecialchars($redes_sociais_cliente['instagram'] ?? 'N/A') ?></p>
                                <p class="text-sm text-gray-700 mb-1"><strong>Facebook:</strong> <?= htmlspecialchars($redes_sociais_cliente['facebook'] ?? 'N/A') ?></p>
                                <p class="text-sm text-gray-700 mb-1"><strong>WhatsApp:</strong> <?= htmlspecialchars($redes_sociais_cliente['whatsapp'] ?? 'N/A') ?></p>
                                <p class="text-sm text-gray-700 mb-1"><strong>Website:</strong> <?= htmlspecialchars($redes_sociais_cliente['site'] ?? 'N/A') ?></p>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">Nenhuma informação de redes sociais.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Cores Principais -->
                    <div class="mb-4 border rounded-lg overflow-hidden">
                        <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="cores-cliente-collapse">
                            <h4 class="font-semibold text-gray-700">Cores Principais</h4>
                            <i class="fas fa-chevron-down text-gray-500"></i>
                        </div>
                        <div id="cores-cliente-collapse" class="p-4 hidden">
                            <?php if (!empty(array_filter($cores_cliente))): ?>
                                <div class="grid grid-cols-2 gap-2">
                                    <p class="text-sm text-gray-700"><strong>Principal:</strong> <span class="inline-block w-4 h-4 rounded-full border" style="background-color: <?= htmlspecialchars($cores_cliente['cor_principal'] ?? '#FFFFFF') ?>;"></span> <?= htmlspecialchars($cores_cliente['cor_principal'] ?? 'N/A') ?></p>
                                    <p class="text-sm text-gray-700"><strong>Secundária:</strong> <span class="inline-block w-4 h-4 rounded-full border" style="background-color: <?= htmlspecialchars($cores_cliente['cor_secundaria'] ?? '#FFFFFF') ?>;"></span> <?= htmlspecialchars($cores_cliente['cor_secundaria'] ?? 'N/A') ?></p>
                                    <p class="text-sm text-gray-700"><strong>Complemento:</strong> <span class="inline-block w-4 h-4 rounded-full border" style="background-color: <?= htmlspecialchars($cores_cliente['cor_complemento'] ?? '#FFFFFF') ?>;"></span> <?= htmlspecialchars($cores_cliente['cor_complemento'] ?? 'N/A') ?></p>
                                    <p class="text-sm text-gray-700"><strong>Destaque:</strong> <span class="inline-block w-4 h-4 rounded-full border" style="background-color: <?= htmlspecialchars($cores_cliente['cor_destaque'] ?? '#FFFFFF') ?>;"></span> <?= htmlspecialchars($cores_cliente['cor_destaque'] ?? 'N/A') ?></p>
                                </div>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">Nenhuma cor informada.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Temas de Interesse -->
                    <div class="mb-4 border rounded-lg overflow-hidden">
                        <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="temas-interesse-cliente-collapse">
                            <h4 class="font-semibold text-gray-700">Temas de Interesse</h4>
                            <i class="fas fa-chevron-down text-gray-500"></i>
                        </div>
                        <div id="temas-interesse-cliente-collapse" class="p-4 hidden">
                            <?php if (!empty($temas_interesse_cliente['subtemas'])): ?>
                                <ul class="list-disc list-inside text-sm text-gray-700">
                                    <?php foreach ($temas_interesse_cliente['subtemas'] as $subtema): ?>
                                        <li><?= htmlspecialchars($subtema) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">Nenhum tema de interesse informado.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Segmento -->
                    <div class="mb-4 border rounded-lg overflow-hidden">
                        <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="segmento-cliente-collapse">
                            <h4 class="font-semibold text-gray-700">Segmento</h4>
                            <i class="fas fa-chevron-down text-gray-500"></i>
                        </div>
                        <div id="segmento-cliente-collapse" class="p-4 hidden">
                            <?php if (!empty($segmento_cliente['segmento'])): ?>
                                <p class="text-sm text-gray-700"><?= htmlspecialchars($segmento_cliente['segmento']) ?></p>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">Nenhum segmento informado.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Textos Personalizados -->
                    <div class="mb-4 border rounded-lg overflow-hidden">
                        <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="textos-personalizados-cliente-collapse">
                            <h4 class="font-semibold text-gray-700">Textos Personalizados</h4>
                            <i class="fas fa-chevron-down text-gray-500"></i>
                        </div>
                        <div id="textos-personalizados-cliente-collapse" class="p-4 hidden">
                            <?php if (!empty(array_filter($textos_personalizados_cliente))): ?>
                                <?php for ($i = 1; $i <= 3; $i++): ?>
                                    <?php if (!empty($textos_personalizados_cliente["texto_personalizado_$i"])): ?>
                                        <p class="text-sm text-gray-700 mb-1"><strong>Texto <?= $i ?>:</strong> <?= htmlspecialchars($textos_personalizados_cliente["texto_personalizado_$i"]) ?></p>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">Nenhum texto personalizado informado.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Logotipos -->
                    <div class="mb-4 border rounded-lg overflow-hidden">
                        <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="logotipos-cliente-collapse">
                            <h4 class="font-semibold text-gray-700">Logotipos</h4>
                            <i class="fas fa-chevron-down text-gray-500"></i>
                        </div>
                        <div id="logotipos-cliente-collapse" class="p-4 hidden">
                            <?php if (!empty($logotipos_cliente)): ?>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($logotipos_cliente as $logo): ?>
                                        <div class="relative w-24 h-24 border rounded overflow-hidden">
                                            <img src="<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario_pedido) ?>/logotipos/<?= htmlspecialchars($logo) ?>" alt="Logotipo" class="w-full h-full object-cover">
                                            <a href="<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario_pedido) ?>/logotipos/<?= htmlspecialchars($logo) ?>" download class="absolute bottom-0 right-0 bg-black bg-opacity-75 text-white p-1 text-xs rounded-tl-lg"><i class="fas fa-download"></i></a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">Nenhum logotipo enviado.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Imagens para Artes -->
                    <div class="mb-4 border rounded-lg overflow-hidden">
                        <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="imagens-artes-cliente-collapse">
                            <h4 class="font-semibold text-gray-700">Imagens para Artes</h4>
                            <i class="fas fa-chevron-down text-gray-500"></i>
                        </div>
                        <div id="imagens-artes-cliente-collapse" class="p-4 hidden">
                            <?php if (!empty($imagens_artes_cliente)): ?>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($imagens_artes_cliente as $img): ?>
                                        <div class="relative w-24 h-24 border rounded overflow-hidden">
                                            <img src="<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario_pedido) ?>/imagens_artes/<?= htmlspecialchars($img) ?>" alt="Imagem para Arte" class="w-full h-full object-cover">
                                            <a href="<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario_pedido) ?>/imagens_artes/<?= htmlspecialchars($img) ?>" download class="absolute bottom-0 right-0 bg-black bg-opacity-75 text-white p-1 text-xs rounded-tl-lg"><i class="fas fa-download"></i></a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">Nenhuma imagem para artes enviada.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Elementos de Design -->
                    <div class="mb-4 border rounded-lg overflow-hidden">
                        <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="elementos-design-cliente-collapse">
                            <h4 class="font-semibold text-gray-700">Elementos de Design</h4>
                            <i class="fas fa-chevron-down text-gray-500"></i>
                        </div>
                        <div id="elementos-design-cliente-collapse" class="p-4 hidden">
                            <?php if (!empty($elementos_design_cliente)): ?>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($elementos_design_cliente as $elem): ?>
                                        <div class="relative w-24 h-24 border rounded overflow-hidden">
                                            <img src="<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario_pedido) ?>/elementos_design/<?= htmlspecialchars($elem) ?>" alt="Elemento de Design" class="w-full h-full object-cover">
                                            <a href="<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario_pedido) ?>/elementos_design/<?= htmlspecialchars($elem) ?>" download class="absolute bottom-0 right-0 bg-black bg-opacity-75 text-white p-1 text-xs rounded-tl-lg"><i class="fas fa-download"></i></a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">Nenhum elemento de design enviado.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Imagens da Empresa -->
                    <div class="mb-4 border rounded-lg overflow-hidden">
                        <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="imagens-empresa-cliente-collapse">
                            <h4 class="font-semibold text-gray-700">Imagens da Empresa</h4>
                            <i class="fas fa-chevron-down text-gray-500"></i>
                        </div>
                        <div id="imagens-empresa-cliente-collapse" class="p-4 hidden">
                            <?php if (!empty($imagens_empresa_cliente)): ?>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($imagens_empresa_cliente as $img): ?>
                                        <div class="relative w-24 h-24 border rounded overflow-hidden">
                                            <img src="<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario_pedido) ?>/imagens_empresa/<?= htmlspecialchars($img) ?>" alt="Imagem da Empresa" class="w-full h-full object-cover">
                                            <a href="<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario_pedido) ?>/imagens_empresa/<?= htmlspecialchars($img) ?>" download class="absolute bottom-0 right-0 bg-black bg-opacity-75 text-white p-1 text-xs rounded-tl-lg"><i class="fas fa-download"></i></a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">Nenhuma imagem da empresa enviada.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Produtos do Cliente -->
                    <div class="mb-4 border rounded-lg overflow-hidden">
                        <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="produtos-cliente-collapse">
                            <h4 class="font-semibold text-gray-700">Produtos do Cliente</h4>
                            <i class="fas fa-chevron-down text-gray-500"></i>
                        </div>
                        <div id="produtos-cliente-collapse" class="p-4 hidden">
                            <?php if (!empty($produtos_cliente) && is_array($produtos_cliente)): ?>
                                <?php foreach ($produtos_cliente as $product_data): ?>
                                    <div class="mb-4 p-3 border rounded-lg bg-white">
                                        <p class="text-sm text-gray-700 mb-1"><strong>Nome:</strong> <?= htmlspecialchars($product_data['name'] ?? 'N/A') ?></p>
                                        <p class="text-sm text-gray-700 mb-1"><strong>Descrição:</strong> <?= htmlspecialchars($product_data['description'] ?? 'N/A') ?></p>
                                        <p class="text-sm text-gray-700 mb-2"><strong>Informação:</strong> <?= htmlspecialchars($product_data['info'] ?? 'N/A') ?></p>
                                        <?php if (!empty($product_data['images']) && is_array($product_data['images'])): ?>
                                            <div class="flex flex-wrap gap-2 mt-2">
                                                <?php foreach ($product_data['images'] as $img): ?>
                                                    <div class="relative w-24 h-24 border rounded overflow-hidden">
                                                        <img src="<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario_pedido) ?>/produtos/<?= htmlspecialchars($img) ?>" alt="Produto" class="w-full h-full object-cover">
                                                        <a href="<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario_pedido) ?>/produtos/<?= htmlspecialchars($img) ?>" download class="absolute bottom-0 right-0 bg-black bg-opacity-75 text-white p-1 text-xs rounded-tl-lg"><i class="fas fa-download"></i></a>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">Nenhum produto enviado.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>


        <?php else: ?>
            <p class="text-gray-700">Produto ou Pedido não encontrado ou código inválido.</p>
        <?php endif; ?>

        <div class="mt-6">
            <a href="pedidos"
                class="bg-gray-500 text-white py-2 px-4 rounded font-medium hover:bg-gray-600 transition">Voltar para
                Gerenciamento de Pedidos</a>
        </div>
    </div>
</div>

<!-- Modal para o Carrossel em Tamanho Maior -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="relative bg-white rounded-lg p-4 max-w-4xl w-full mx-4">
        <button id="closeModal"
            class="absolute top-2 right-2 text-gray-800 text-3xl leading-none hover:text-gray-600">&times;</button>
        <div class="relative w-full h-[500px] md:h-[600px] lg:h-[700px] overflow-hidden bg-gray-100 rounded"
            data-modal-carousel>
            <?php foreach ($images as $i => $img): ?>
                <div class="carousel-image absolute inset-0 transition-opacity duration-500 <?= $i === 0 ? 'opacity-100' : 'opacity-0' ?>"
                    data-index="<?= $i ?>">
                    <img src="<?= htmlspecialchars($img) ?>" class="w-full h-full object-contain mx-auto">
                    <div
                        class="absolute inset-0 flex items-center justify-center text-white text-4xl font-bold opacity-20 pointer-events-none bg-black/30">
                        FeedPerfeito
                    </div>
                </div>
            <?php endforeach; ?>
            <button
                class="carousel-prev absolute left-2 top-1/2 -translate-y-1/2 bg-black/50 text-white p-2 rounded-full"><i
                    class="fas fa-chevron-left"></i></button>
            <button
                class="carousel-next absolute right-2 top-1/2 -translate-y-1/2 bg-black/50 text-white p-2 rounded-full"><i
                    class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</div>

<script>
    const loggedInUserName = <?= json_encode($_SESSION['user_name'] ?? 'admin') ?>; // Assumindo 'admin' para o lado de produção

    function initializeCarousel(carouselElement, isModal = false) {
        let current = 0;
        const slides = carouselElement.querySelectorAll('.carousel-image');
        const prev = carouselElement.querySelector('.carousel-prev');
        const next = carouselElement.querySelector('.carousel-next');

        function showSlide(i) {
            slides.forEach((s, idx) => {
                s.classList.toggle('opacity-100', idx === i);
                s.classList.toggle('opacity-0', idx !== i);
            });
        }

        prev?.addEventListener('click', () => {
            current = (current - 1 + slides.length) % slides.length;
            showSlide(current);
        });

        next?.addEventListener('click', () => {
            current = (current + 1) % slides.length;
            showSlide(current);
        });

        // inicial
        showSlide(current);

        function showSlide(i) {
            slides.forEach((s, idx) => {
                s.classList.toggle('opacity-100', idx === i);
                s.classList.toggle('opacity-0', idx !== i);
            });
        }

        prev?.addEventListener('click', () => {
            current = (current - 1 + slides.length) % slides.length;
            showSlide(current);
        });

        next?.addEventListener('click', () => {
            current = (current + 1) % slides.length;
            showSlide(current);
        });

        // inicial
        showSlide(current);

        if (!isModal) {
            slides.forEach(slide => {
                slide.querySelector('img').addEventListener('click', () => {
                    const modal = document.getElementById('imageModal');
                    const modalCarousel = modal.querySelector('[data-modal-carousel]');
                    const modalSlides = modalCarousel.querySelectorAll('.carousel-image');
                    const clickedIndex = parseInt(slide.dataset.index);

                    // Sincroniza o slide do modal com o slide clicado
                    modalSlides.forEach((s, idx) => {
                        s.classList.toggle('opacity-100', idx === clickedIndex);
                        s.classList.toggle('opacity-0', idx !== clickedIndex);
                    });

                    modal.classList.remove('hidden');
                    // Atualiza o índice do carrossel do modal para o slide clicado
                    modalCarousel.dataset.currentIndex = clickedIndex;
                    initializeCarousel(modalCarousel, true); // Re-inicializa o carrossel do modal
                });
            });
        }
    }

    // Inicializa todos os carrosséis da página
    document.querySelectorAll('.bg-gray-50 .relative[data-carousel]').forEach(carousel => {
        initializeCarousel(carousel);
    });

    // Fechar modal
    document.getElementById('closeModal').addEventListener('click', () => {
        document.getElementById('imageModal').classList.add('hidden');
    });

    // Fechar modal clicando fora
    document.getElementById('imageModal').addEventListener('click', (e) => {
        if (e.target.id === 'imageModal') {
            document.getElementById('imageModal').classList.add('hidden');
        }
    });

    // Inicializa o carrossel do modal separadamente
    const modalCarouselElement = document.querySelector('[data-modal-carousel]');
    if (modalCarouselElement) {
        initializeCarousel(modalCarouselElement, true);
    }

    // Lógica AJAX para atualizar o status do pedido
    document.querySelectorAll('.update-status-btn').forEach(button => {
        button.addEventListener('click', async (event) => {
            const purchaseId = event.target.dataset.purchaseId;
            const newStatus = event.target.dataset.newStatus;

            try {
                const response = await fetch('../api/post/update_purchase_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        purchase_id: purchaseId,
                        new_status: newStatus,
                        uid_usuario_pedido: event.target.dataset.userId,
                        unique_code: event.target.dataset.uniqueCode
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Atualiza o status exibido na página
                    document.getElementById('current-status').textContent = result.new_status.charAt(0).toUpperCase() + result.new_status.slice(1);
                    window.location.reload();
                }
            } catch (error) {
                alert('Erro na comunicação com o servidor.');
            }
        });
    });

    // Lógica AJAX para o formulário de chat
    document.getElementById('chat-form')?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        data.sender = loggedInUserName; // Adiciona o nome do usuário
        console.log('Dados enviados:', data);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                // Adiciona a mensagem temporariamente ao chat-box
                const message = data.message;
                const sender = loggedInUserName; // Use loggedInUserName para o remetente
                const chatBox = document.getElementById('chat-box');

                if (chatBox) {
                    const isCurrentUser = (sender === loggedInUserName);
                    const justifyClass = isCurrentUser ? 'justify-end' : 'justify-start';
                    const bgColorClass = isCurrentUser ? 'bg-black text-white' : 'bg-gray-200 text-gray-800';

                    const messageContainer = document.createElement('div');
                    messageContainer.classList.add('flex', 'mb-2', justifyClass);

                    const messageCard = document.createElement('div');
                    messageCard.classList.add(
                        'max-w-[70%]',
                        'p-3',
                        'rounded-lg',
                        'shadow-md',
                        'text-sm',
                        ...bgColorClass.split(' ')
                    );

                    messageCard.innerHTML = `<strong>${sender}:</strong> ${message}`;

                    messageContainer.appendChild(messageCard);
                    chatBox.appendChild(messageContainer);
                    chatBox.scrollTop = chatBox.scrollHeight; // Scroll para o final
                }
                form.reset(); // Limpa o formulário
            } else {
                alert('Erro ao enviar mensagem: ' + result.message);
            }
        } catch (error) {
            alert('Erro na comunicação com o servidor ao enviar mensagem.');
            console.error('Erro:', error);
        }
    });

   

    // Garante que o chat esteja sempre no final
    document.addEventListener('DOMContentLoaded', () => {
        const chatBox = document.getElementById('chat-box');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        // Script para funcionalidade de colapso
        document.querySelectorAll('[data-collapse-toggle]').forEach(trigger => {
            trigger.addEventListener('click', () => {
                const targetId = trigger.getAttribute('data-collapse-toggle');
                const targetElement = document.getElementById(targetId);

                if (targetElement) {
                    targetElement.classList.toggle('hidden');
                }
            });
        });
    });
</script>