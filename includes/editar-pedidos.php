<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/CarouselProduct.php';
require_once __DIR__ . '/../models/FeedProduct.php';
require_once __DIR__ . '/../models/AprovacaoPedido.php';
require_once __DIR__ . '/../models/Purchase.php';
require_once __DIR__ . '/../models/MultipleProduct.php'; // Se houver um modelo para 'multiple_products'

// Iniciar a sessão se ainda não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redireciona se o usuário não estiver logado
requireUserLogin();

$uniqueCode = $_POST['unique_code'] ?? null;
$product = null;
$productType = null;

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

    // Se não encontrou, tenta buscar em multiple_products (descomente quando tiver o modelo)
    if (!$product && strpos($uniqueCode, 'multiple_') === 0) {
        $multipleProductModel = new MultipleProduct();
        $products = $multipleProductModel->where(['unique_code' => $uniqueCode]);
        if (!empty($products)) {
            $product = $products[0];
            $productType = 'Multiple';
        }
    }
}

$purchase = null;
$aprovacaoPedido = null; // Inicializa $aprovacaoPedido como nulo
$pedidoId = $_POST['id'] ?? null;
if ($pedidoId) {
    $purchaseModel = new Purchase();
    $purchases = $purchaseModel->where(['id' => $pedidoId]);
    if (!empty($purchases)) {
        $purchase = $purchases[0];
        $pedidoId = $purchase['id']; // Obtém o ID do pedido

        $aprovacaoPedidoModel = new AprovacaoPedido();
        $aprovacao = $aprovacaoPedidoModel->getAprovacaoByid($pedidoId);
        if (!empty($aprovacao)) {
            $aprovacaoPedido = $aprovacao;
        }
    }
}
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
            $currentStatus =  $purchase['status']; // Usa o status de aprovação se existir, caso contrário, usa o status da compra
            $idxAtual = etapaIndex(ucfirst($currentStatus));
            $images = json_decode($product['images'], true) ?: [];
            $status = ucfirst($currentStatus); // Atualiza a variável $status para refletir o status correto a ser exibido
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

            <!-- Steps -->
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
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($aprovacaoPedido): ?>
                <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-3 gap-6">

                    <div class="bg-gray-50 rounded-lg p-4 lg:col-span-1 text-center">
                        <?php
                        $aprovacaoImagens = [];
                        if ($aprovacaoPedido && isset($aprovacaoPedido['imagens'])) {
                            if (is_array($aprovacaoPedido['imagens'])) {
                                $aprovacaoImagens = $aprovacaoPedido['imagens'];
                            } elseif (is_string($aprovacaoPedido['imagens'])) {
                                $aprovacaoImagens = json_decode($aprovacaoPedido['imagens'], true) ?? [];
                            }
                        }
                        ?>
                        <?php if (!empty($aprovacaoImagens)): ?>
                            <h3 class="text-lg font-semibold mb-3">Imagens para Aprovação</h3>
                            <div class="relative bg-gray-50 rounded-lg p-3 mb-6 overflow-hidden h-64" data-carousel="aprovacao">
                                <?php foreach ($aprovacaoImagens as $i => $img): ?>
                                    <div class="carousel-image absolute inset-0 transition-opacity duration-500 <?= $i === 0 ? 'opacity-100' : 'opacity-0' ?>"
                                        data-index="<?= $i ?>">
                                        <img src="<?= $_SESSION['base_url'] . htmlspecialchars($img) ?>" alt="Imagem Aprovacao"
                                            class="w-full h-full object-contain mx-auto cursor-pointer">
                                    </div>
                                <?php endforeach; ?>
                                <button
                                    class="carousel-prev absolute left-2 top-1/2 -translate-y-1/2 bg-black/50 text-white p-2 rounded-full"><i
                                        class="fas fa-chevron-left"></i></button>
                                <button
                                    class="carousel-next absolute right-2 top-1/2 -translate-y-1/2 bg-black/50 text-white p-2 rounded-full"><i
                                        class="fas fa-chevron-right"></i></button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 lg:col-span-1 text-center">
                        <h3 class="text-lg font-semibold mb-3">Observações do Operador</h3>
                        <?php if ($aprovacaoPedido['aprovacao'] === 'Aprovação'): ?>
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <p class=" text-gray-700  mb-3">
                                    <?= isset($aprovacaoPedido['observacoes']) ? htmlspecialchars(json_decode($aprovacaoPedido['observacoes'])) : 'Nenhuma observação.' ?>
                                </p>
                                
                                <?php if ($aprovacaoPedido['aprovacao'] === 'Aprovação'): ?>
                                    <br>Aprovando agora, ganhe <i class="fas fa-coins text-yellow-500 mr-2"></i><b>10</b> créditos.
                                <?php endif; ?>
                                <div class="flex gap-3 mt-4 justify-center">
                                    <?php if ($aprovacaoPedido['aprovacao'] === 'Revisão'): ?>
                                        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 update-status-btn"
                                            data-aprovacao-id="<?= htmlspecialchars($aprovacaoPedido['id']) ?>"
                                            data-new-status="disponível">
                                            <i class="fas fa-check mr-1"></i> Aprovar
                                        </button>
                                    <?php elseif ($aprovacaoPedido['aprovacao'] === 'Aprovação'): ?>
                                        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 update-status-btn"
                                            data-aprovacao-id="<?= htmlspecialchars($aprovacaoPedido['id']) ?>"
                                            data-new-status="disponível">
                                            <i class="fas fa-check mr-1"></i> Gostei, aprovado!
                                        </button>
                                    <?php endif; ?>
                                    <button class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 update-status-btn"
                                        data-aprovacao-id="<?= htmlspecialchars($aprovacaoPedido['id']) ?>"
                                        data-new-status="revisão">
                                        <i class="fas fa-edit mr-1"></i> Solicitar Alteração
                                    </button>
                                </div>
                                <i> Aprovando, o arquivo automaticamente estará disponível para download. Se precisar de alteração, use o chat para solicitar as mudanças.</i>
                            </div>
                        <?php endif; ?>

                        <?php if ($aprovacaoPedido['aprovacao'] === 'Disponível'): ?>
                            <?php
                            $downloadPath = '';
                            if (!empty($aprovacaoPedido['arquivos'])) {
                                $arquivos = json_decode($aprovacaoPedido['arquivos'], true);
                                if (!empty($arquivos)) {
                                    $downloadPath = $_SESSION['base_url'] . $arquivos[0]; // Pega o primeiro arquivo na lista
                                }
                            }
                            ?>
                            <p class="text-gray-700 mb-4">🎉 Seu produto está pronto para download. Parabéns!</p>
                            <?php if (!empty($downloadPath)): ?>
                                <a href="<?= htmlspecialchars($downloadPath) ?>"
                                    class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 inline-block download-product-btn"
                                    data-purchase-id="<?= htmlspecialchars($purchase['id']) ?>"
                                    data-new-status="Bosta">
                                    <i class="fas fa-download mr-2"></i> Download do Produto
                                </a>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">Nenhum arquivo de entrega disponível para download.</p>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($aprovacaoPedido['aprovacao'] === 'Entregue'): ?>
                            <p class="text-gray-700 mb-4">Parabéns pela aquisição! Seu Instagram será fortalecido com este
                                conteúdo.<br>Segue também o manual para extrair o melhor engajamento.</p>
                            <div class="flex gap-3 justify-center">
                                <a href="download.php?manual=<?= urlencode($product['unique_code']) ?>"
                                    class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                                    <i class="fas fa-book mr-2"></i> Download Manual
                                </a>
                                <a href="download.php?file=<?= urlencode($product['unique_code']) ?>"
                                    class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800">
                                    <i class="fas fa-download mr-2"></i> Download Produto
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- Coluna direita (chat / ações por status) -->
                    <div class="bg-gray-50 rounded-lg p-4 lg:col-span-1 text-center">
                        <h3 class="text-lg font-semibold mb-3">Chat</h3>
                        <div class="flex flex-col h-80">
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
                                        $isCurrentUser = ($msg['sender'] === ($_SESSION['user_name'] ?? 'Usuário Desconhecido'));
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
                    </div>
                </div>
            <?php elseif (empty($aprovacaoPedido) && $currentStatus === 'Disponível'): ?>
                <div class="flex justify-center mb-6">
                    <form id="downloadForm_<?= htmlspecialchars($purchase['id']) ?>" action="../api/get/download_product.php" method="POST" style="display: none;">
                        <input type="hidden" name="file_path" value="<?= htmlspecialchars($product['download']) ?>">
                        <input type="hidden" name="purchase_id" value="<?= htmlspecialchars($purchase['id']) ?>">
                    </form>
                    <button type="button"
                        class="bg-green-500 text-white py-2 px-4 rounded font-medium hover:bg-green-600 transition download-product-btn"
                        data-purchase-id="<?= htmlspecialchars($purchase['id']) ?>"
                        data-new-status="Entregue"
                        data-form-id="downloadForm_<?= htmlspecialchars($purchase['id']) ?>"
                        data-download-path="<?= htmlspecialchars($product['download']) ?>">
                        Download do Produto
                    </button>
                </div>
            <?php else: ?>
                <p class="text-gray-700 text-center">Nenhum registro de aprovação encontrado para este pedido.</p>
            <?php endif; ?>

        <?php else: ?>
            <p class="text-gray-700">Produto ou Pedido não encontrado ou código inválido.</p>
        <?php endif; ?>

        <div class="mt-6">
            <a href="pedidos"
                class="bg-gray-500 text-white py-2 px-4 rounded font-medium hover:bg-gray-600 transition">Voltar para
                Meus Pedidos</a>
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
    const loggedInUserName = <?= json_encode($_SESSION['user_name'] ?? 'Usuário Desconhecido') ?>;

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
    document.querySelectorAll('[data-carousel]').forEach(carousel => {
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
                const sender = data.sender;
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
                        ...bgColorClass.split(' ') // Correção aqui: separa as classes por espaço
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

    // Lógica AJAX para atualizar o status da aprovação (cliente)
    document.querySelectorAll('.update-status-btn').forEach(button => {
        button.addEventListener('click', async (event) => {
            const aprovacaoId = event.target.dataset.aprovacaoId;
            const newStatus = event.target.dataset.newStatus;
 
            try {
                const response = await fetch('../api/post/update_aprovacao_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        aprovacao_id: aprovacaoId,
                        new_status: newStatus
                    })
                });
 
                const result = await response.json();
 
                if (result.success) {
                    alert('Status da aprovação atualizado com sucesso!');
                    window.location.reload();
                } else {
                    alert('Erro ao atualizar status da aprovação: ' + result.message);
                }
            } catch (error) {
                alert('Erro na comunicação com o servidor.');
                console.error('Erro:', error);
            }
        });
    });
 
    // Lógica AJAX para o botão de download do produto
    document.querySelectorAll('.download-product-btn').forEach(button => {
        button.addEventListener('click', async (event) => {
            event.preventDefault(); // Previne o comportamento padrão do link
            console.log('Botão de download clicado!');
            const purchaseId = event.target.dataset.purchaseId;
            const newStatus = event.target.dataset.newStatus;
            const downloadPath = event.target.dataset.downloadPath; // Pega o caminho do download
            const formId = event.target.dataset.formId;
            const downloadForm = document.getElementById(formId);

            try {
                const response = await fetch('../api/post/update_purchase_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        purchase_id: purchaseId,
                        new_status: newStatus,
                        download_path: downloadPath
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Se a atualização do status for bem-sucedida, submete o formulário de download
                    if (downloadForm) {
                        downloadForm.submit();
                    } else {
                        alert('Erro: Formulário de download não encontrado.');
                    }
                } else {
                    alert('Erro ao registrar download: ' + result.message);
                }
            } catch (error) {
                alert('Erro na comunicação com o servidor ao registrar download.');
                console.error('Erro:', error);
            }
        });
    });
 
    document.addEventListener('DOMContentLoaded', async () => {
        const chatBox = document.getElementById('chat-box');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
 
        // Recarrega a página completa a cada 5 minutos (300000 ms)
        setInterval(() => {
            window.location.reload();
        }, 300000);
    });
</script>