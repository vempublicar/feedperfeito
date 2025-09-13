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
                    <!--aqui entram os dados personalizados do cliente  -->
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2">
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
                            <h4 class="text-md font-semibold mb-2">Imagens Anexadas Anteriormente:</h4>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <?php foreach ($aprovacaoImagens as $img): ?>
                                    <div class="w-24 h-24 border rounded overflow-hidden">
                                        <img src="<?= $_SESSION['base_url'] . htmlspecialchars($img) ?>" alt="Imagem Aprovacao"
                                            class="w-full h-full object-cover">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form id="upload-form" action="../api/post/insert_aprovacao_pedido.php" method="POST"
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
                                    <option value="Produção" <?= ($aprovacaoPedido && $aprovacaoPedido['aprovacao'] == 'Produção') ? 'selected' : '' ?>>Produção</option>
                                    <option value="Aprovação" <?= ($aprovacaoPedido && $aprovacaoPedido['aprovacao'] == 'Aprovação') ? 'selected' : '' ?>>Aprovação</option>
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
                    <form id="upload-zip-form" action="../api/upload/upload_entrega_zip.php" method="POST"
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

    // Lógica AJAX para o formulário de upload de imagens de aprovação
    document.getElementById('upload-form')?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData // FormData é enviado diretamente sem Content-Type para uploads
            });

            const result = await response.json();

            if (result.success) {
                alert('Upload e aprovação registrados com sucesso!');
                window.location.reload(); // Recarrega a página para atualizar tudo
            } else {
                alert('Erro ao registrar aprovação: ' + result.message);
            }
        } catch (error) {
            alert('Erro na comunicação com o servidor ao fazer upload.');
            console.error('Erro:', error);
        }
    });

    // Lógica AJAX para o formulário de upload de ZIP
    document.getElementById('upload-zip-form')?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData // FormData é enviado diretamente sem Content-Type para uploads
            });

            const result = await response.json();

            if (result.success) {
                alert('Arquivo ZIP enviado com sucesso!');
                window.location.reload(); // Recarrega a página para atualizar tudo
            } else {
                alert('Erro ao enviar arquivo ZIP: ' + result.message);
            }
        } catch (error) {
            alert('Erro na comunicação com o servidor ao fazer upload do ZIP.');
            console.error('Erro:', error);
        }
    });

    // Garante que o chat esteja sempre no final
    document.addEventListener('DOMContentLoaded', () => {
        const chatBox = document.getElementById('chat-box');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
</script>