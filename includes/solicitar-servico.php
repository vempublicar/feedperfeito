<?php
$servicos = [
  [
    'id' => 'SRV-NFC',
    'titulo' => 'Cartão NFC',
    'descricao' => 'Cartão físico com NFC apontando para sua página de links e contato.',
    'thumb_url' => 'https://images.unsplash.com/photo-1517649763962-0c623066013b?q=80&w=1200&auto=format&fit=crop',
    'entrega' => '3–5 dias úteis',
    'preco_tipo' => 'BRL',          // BRL | CREDITOS | SOB-CONSULTA
    'preco' => 149.90,
    'creditos' => null,
    'tag' => 'Popular'
  ],
  [
    'id' => 'SRV-LANDING',
    'titulo' => 'Landing Page',
    'descricao' => 'Página de alta conversão para capturar leads ou vendas.',
    'thumb_url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1200&auto=format&fit=crop',
    'entrega' => '7–10 dias úteis',
    'preco_tipo' => 'BRL',
    'preco' => 1190.00,
    'creditos' => null,
    'tag' => 'Mais pedido'
  ],
  [
    'id' => 'SRV-VIDEOS',
    'titulo' => 'Edição de Vídeos',
    'descricao' => 'Pacote de 4 vídeos curtos com cortes dinâmicos e legendas.',
    'thumb_url' => 'https://images.unsplash.com/photo-1518779578993-ec3579fee39f?q=80&w=1200&auto=format&fit=crop',
    'entrega' => '5–7 dias úteis',
    'preco_tipo' => 'CREDITOS',
    'preco' => null,
    'creditos' => 24,
    'tag' => null
  ],
  [
    'id' => 'SRV-LOJA',
    'titulo' => 'Criação de Loja Virtual',
    'descricao' => 'Setup completo com tema profissional e integrações essenciais.',
    'thumb_url' => 'https://images.unsplash.com/photo-1557825835-a526494be845?q=80&w=1200&auto=format&fit=crop',
    'entrega' => '10–20 dias úteis',
    'preco_tipo' => 'SOB-CONSULTA',
    'preco' => null,
    'creditos' => null,
    'tag' => 'Projeto'
  ],
];

$client_credits = $client_credits ?? 120;
?>

<section id="servicos" class="py-12 bg-white">
  <div class="container mx-auto px-4">
    <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end flex-wrap gap-4">
      <div>
        <h2 class="text-3xl font-bold text-black mb-2">
          Outros <span class="font-light">Serviços</span>
        </h2>
        <div class="w-20 h-1 bg-black mb-4"></div>
        <p class="text-gray-600">Soluções extras para impulsionar sua presença digital.</p>
      </div>
      <a href="#creditos" class="bg-white border border-gray-300 text-black py-2 px-4 rounded font-medium hover:bg-gray-100 transition duration-300 inline-flex items-center">
        <i class="fas fa-coins mr-2"></i> Ver Créditos
      </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <?php foreach ($servicos as $s): ?>
        <div class="bg-background rounded-lg shadow-md overflow-hidden srv-card relative">
          <?php if (!empty($s['tag'])): ?>
            <span class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded srv-ribbon"><?= htmlspecialchars($s['tag']) ?></span>
          <?php endif; ?>

          <div class="grid grid-cols-1 md:grid-cols-5 h-full">
            <div class="md:col-span-2">
              <img src="<?= htmlspecialchars($s['thumb_url']) ?>" class="w-full h-full object-cover" alt="">
            </div>
            <div class="md:col-span-3">
              <div class="p-6 flex flex-col h-full">
                <h5 class="text-xl font-bold text-black mb-1"><?= htmlspecialchars($s['titulo']) ?></h5>
                <p class="text-gray-600 mb-4"><?= htmlspecialchars($s['descricao']) ?></p>

                <div class="text-gray-500 mb-4 flex items-center">
                  <i class="fas fa-truck mr-2"></i> Entrega: <?= htmlspecialchars($s['entrega']) ?>
                </div>

                <div class="flex items-center mb-6">
                  <?php if ($s['preco_tipo']==='BRL'): ?>
                    <div class="text-2xl font-bold">R$ <?= number_format($s['preco'], 2, ',', '.') ?></div>
                  <?php elseif ($s['preco_tipo']==='CREDITOS'): ?>
                    <div class="text-2xl font-bold flex items-center">
                      <i class="fas fa-coins text-yellow-500 mr-2"></i> <?= (int)$s['creditos'] ?> créditos
                    </div>
                    <p class="text-gray-500 ml-2">Você: <strong><?= (int)$client_credits ?></strong></p>
                  <?php else: ?>
                    <div class="text-lg font-medium">Sob consulta</div>
                  <?php endif; ?>
                </div>

                <div class="mt-auto flex flex-wrap gap-2">
                  <button
                    class="bg-white border border-gray-300 text-black py-2 px-4 rounded font-medium hover:bg-gray-100 transition duration-300 flex items-center mr-2"
                    data-toggle="modal"
                    data-target="#briefingServicoModal"
                    data-servico='<?= json_encode($s, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'>
                    <i class="fas fa-edit mr-2"></i> Briefing / Solicitar
                  </button>

                  <?php if ($s['preco_tipo']==='CREDITOS'): ?>
                    <form method="post" action="solicitar_servico.php" class="m-0 p-0">
                      <input type="hidden" name="service_id" value="<?= htmlspecialchars($s['id']) ?>">
                      <input type="hidden" name="action" value="comprar_creditos">
                      <input type="hidden" name="creditos" value="<?= (int)$s['creditos'] ?>">
                      <?php if ($client_credits >= (int)$s['creditos']): ?>
                        <button type="submit" class="bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300 flex items-center">
                          <i class="fas fa-magic mr-2"></i> Solicitar com Créditos
                        </button>
                      <?php else: ?>
                        <a href="#creditos" class="bg-white border border-gray-300 text-black py-2 px-4 rounded font-medium hover:bg-gray-100 transition duration-300 flex items-center">
                          <i class="fas fa-plus-circle mr-2"></i> Adicionar créditos
                        </a>
                      <?php endif; ?>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 px-6 py-3 flex justify-between items-center">
            <p class="text-gray-500 text-sm">ID: <?= htmlspecialchars($s['id']) ?></p>
            <p class="text-gray-500 text-sm"><?= htmlspecialchars($s['preco_tipo']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (empty($servicos)): ?>
      <div class="bg-gray-100 border border-gray-300 rounded-lg p-6 text-center">Sem serviços adicionais no momento.</div>
    <?php endif; ?>
  </div>
</section>

<div class="fixed inset-0 z-50 hidden overflow-y-auto" id="briefingServicoModal">
  <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
      <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

    <form method="post" action="solicitar_servico.php" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
      <div class="bg-white">
        <div class="flex justify-between items-center p-4 border-b border-gray-200">
          <h5 class="text-lg font-bold text-black" id="briefingServicoLabel">Briefing do Serviço</h5>
          <button type="button" class="close text-gray-500 hover:text-gray-700" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="p-6">
          <input type="hidden" name="service_id" id="bf_service_id">
          <input type="hidden" name="action" value="briefing">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-1">
              <label class="block text-black font-medium mb-2">Serviço</label>
              <div id="bf_service_title" class="text-lg font-bold text-black">—</div>
              <p class="text-gray-600 text-sm" id="bf_service_price">—</p>
            </div>
            <div class="md:col-span-1">
              <label for="bf_urgencia" class="block text-black font-medium mb-2">Urgência</label>
              <select id="bf_urgencia" name="urgencia" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
                <option value="Normal">Normal (padrão)</option>
                <option value="Rápida">Rápida</option>
                <option value="Prioritária">Prioritária</option>
              </select>
            </div>
            <div class="md:col-span-2">
              <label for="bf_objetivo" class="block text-black font-medium mb-2">Objetivo</label>
              <input type="text" id="bf_objetivo" name="objetivo" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent" placeholder="Ex.: aumentar leads, lançar produto, etc." required>
            </div>
            <div class="md:col-span-2">
              <label for="bf_detalhes" class="block text-black font-medium mb-2">Detalhes / Referências</label>
              <textarea id="bf_detalhes" name="detalhes" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent" rows="4" placeholder="Links, referências visuais, textos, cores, CTA, regras de marca..."></textarea>
            </div>
            <div class="md:col-span-2">
              <div class="bg-gray-100 border border-gray-300 rounded-lg p-4">
                <p class="text-gray-600 text-sm">
                  Se necessário, enviaremos um formulário complementar por e-mail/WhatsApp.  
                  Após confirmação, o serviço entra no fluxo de produção e aparecerá no seu <strong>Andamento dos Pedidos</strong>.
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row justify-end gap-2">
          <button type="button" class="bg-white border border-gray-300 text-black py-2 px-4 rounded font-medium hover:bg-gray-100 transition duration-300" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300 flex items-center">
            <i class="fas fa-paper-plane mr-2"></i> Enviar Briefing
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
  // Script para o modal de briefing de serviço
  document.addEventListener('DOMContentLoaded', function() {
    const briefingServicoModal = document.getElementById('briefingServicoModal');
    const bfServiceId = document.getElementById('bf_service_id');
    const bfServiceTitle = document.getElementById('bf_service_title');
    const bfServicePrice = document.getElementById('bf_service_price');
    
    // Add event listeners to briefing buttons
    const briefingButtons = document.querySelectorAll('[data-toggle="modal"][data-target="#briefingServicoModal"]');
    briefingButtons.forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        const servicoData = this.getAttribute('data-servico');
        const servico = JSON.parse(servicoData.replace(/"/g, '"'));
        
        if (servico) {
          bfServiceId.value = servico.id;
          bfServiceTitle.textContent = servico.titulo;
          
          // Format price display
          if (servico.preco_tipo === 'BRL') {
            bfServicePrice.textContent = 'R$ ' + servico.preco.toFixed(2).replace('.', ',');
          } else if (servico.preco_tipo === 'CREDITOS') {
            bfServicePrice.textContent = servico.creditos + ' créditos';
          } else {
            bfServicePrice.textContent = 'Sob consulta';
          }
        }
        
        // Show modal
        briefingServicoModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
      });
    });
    
    // Close modal when close button is clicked
    const closeButtons = briefingServicoModal.querySelectorAll('[data-dismiss="modal"]');
    closeButtons.forEach(button => {
      button.addEventListener('click', function() {
        briefingServicoModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      });
    });
    
    // Close modal when clicking outside
    briefingServicoModal.addEventListener('click', function(e) {
      if (e.target === briefingServicoModal) {
        briefingServicoModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      }
    });
  });
</script>
