<?php
$pendentesAprovacao = [
  [
    'id' => 'CNT-2301',
    'titulo' => 'Carrossel — Setembro | Loja de Carros',
    'preview_url' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=1080&auto=format&fit=crop', // exemplo
    'thumb_url'   => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?q=80&w=480&auto=format&fit=crop',
    'tipo' => 'Imagem',
    'ultima_atualizacao' => '2025-09-07 11:10'
  ],
  [
    'id' => 'CNT-2302',
    'titulo' => 'Reels — Semana 2 | Setembro',
    'preview_url' => 'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?q=80&w=1080&auto=format&fit=crop',
    'thumb_url'   => 'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?q=80&w=480&auto=format&fit=crop',
    'tipo' => 'Vídeo',
    'ultima_atualizacao' => '2025-09-06 18:42'
  ],
];
?>

<section id="aprovacao" class="py-12 bg-white">
  <div class="container mx-auto px-4">
    <div class="mb-12">
      <h2 class="text-3xl font-bold text-black mb-2">
        Conteúdos <span class="font-light">para Aprovação</span>
      </h2>
      <div class="w-20 h-1 bg-black mb-4"></div>
      <p class="text-gray-600">Revise, envie alterações ou aprove para liberar o download final.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <?php foreach ($pendentesAprovacao as $item): ?>
        <div class="bg-background rounded-lg shadow-md overflow-hidden">
          <div class="p-6 flex flex-col h-full">
            <div class="flex items-start mb-6">
              <img src="<?= htmlspecialchars($item['thumb_url']) ?>"
                   class="rounded mr-4" alt="" style="width:72px;height:72px;object-fit:cover">
              <div>
                <h5 class="text-lg font-bold text-black mb-1"><?= htmlspecialchars($item['titulo']) ?></h5>
                <p class="text-gray-500 text-sm">
                  #<?= htmlspecialchars($item['id']) ?> • <?= htmlspecialchars($item['tipo']) ?> • Atualizado em <?= date('d/m/Y H:i', strtotime($item['ultima_atualizacao'])) ?>
                </p>
              </div>
            </div>

            <div class="mb-6">
              <button
                class="bg-white border border-gray-300 text-black py-2 px-4 rounded font-medium hover:bg-gray-100 transition duration-300"
                data-toggle="modal"
                data-target="#previewModal"
                data-id="<?= htmlspecialchars($item['id']) ?>"
                data-title="<?= htmlspecialchars($item['titulo']) ?>"
                data-src="<?= htmlspecialchars($item['preview_url']) ?>"
              >
                <i class="fas fa-eye mr-2"></i> Visualizar (com marca d’água)
              </button>
            </div>

            <form method="post" action="aprovar_ou_revisar.php" class="mt-auto">
              <input type="hidden" name="content_id" value="<?= htmlspecialchars($item['id']) ?>">
              <div class="mb-4">
                <label for="feedback_<?= $item['id'] ?>" class="block text-black font-medium mb-2">Deseja solicitar alterações?</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent" id="feedback_<?= $item['id'] ?>" name="feedback" rows="3"
                          placeholder="Descreva aqui os ajustes que deseja..."></textarea>
                <p class="text-gray-500 text-sm mt-1">Se marcar "Aprovar", o feedback será ignorado.</p>
              </div>

              <div class="flex items-center mb-4">
                <input type="checkbox" class="approve-toggle h-4 w-4 text-black rounded mr-2"
                       id="approve_<?= $item['id'] ?>" name="approve" value="1"
                       data-textarea="#feedback_<?= $item['id'] ?>">
                <label class="text-black" for="approve_<?= $item['id'] ?>">Aprovar este conteúdo e liberar download</label>
              </div>

              <div class="flex flex-wrap gap-2">
                <button type="submit" name="action" value="revisar" class="bg-white border border-gray-300 text-black py-2 px-4 rounded font-medium hover:bg-gray-100 transition duration-300">
                  <i class="fas fa-comment-dots mr-2"></i> Enviar Revisão
                </button>
                <button type="submit" name="action" value="aprovar" class="bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300">
                  <i class="fas fa-check mr-2"></i> Aprovar & Liberar Download
                </button>
              </div>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<div class="fixed inset-0 z-50 hidden overflow-y-auto" id="previewModal">
  <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
      <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
      <div class="bg-white">
        <div class="flex justify-between items-center p-4 border-b border-gray-200">
          <h5 class="text-lg font-bold text-black" id="previewModalLabel">Pré-visualização</h5>
          <button type="button" class="close text-gray-500 hover:text-gray-700" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="p-0">
          <div class="preview-safe-wrapper no-select no-context">
            <!-- área de preview com marca d’água e overlay para impedir download -->
            <div class="preview-safe-canvas" id="previewCanvas"
                 role="img" aria-label="Pré-visualização protegida"></div>
            <div class="preview-overlay"></div>
            <div class="preview-watermark">
              <span>FEEDPERFEITO — PRÉVIA</span>
              <span>FEEDPERFEITO — PRÉVIA</span>
              <span>FEEDPERFEITO — PRÉVIA</span>
              <span>FEEDPERFEITO — PRÉVIA</span>
            </div>
          </div>
        </div>

        <div class="bg-gray-50 px-4 py-3 flex flex-col sm:flex-row justify-between items-center">
          <p class="text-gray-500 text-sm mb-2 sm:mb-0">A visualização é protegida: download desabilitado até aprovação.</p>
          <button type="button" class="bg-white border border-gray-300 text-black py-2 px-4 rounded font-medium hover:bg-gray-100 transition duration-300" data-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Script para carregar o preview no modal
  document.addEventListener('DOMContentLoaded', function() {
    const previewModal = document.getElementById('previewModal');
    const previewModalLabel = document.getElementById('previewModalLabel');
    const previewCanvas = document.getElementById('previewCanvas');
    
    // Add event listeners to preview buttons
    const previewButtons = document.querySelectorAll('[data-toggle="modal"][data-target="#previewModal"]');
    previewButtons.forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        const title = this.getAttribute('data-title');
        const src = this.getAttribute('data-src');
        
        previewModalLabel.textContent = 'Pré-visualização: ' + title;
        previewCanvas.style.backgroundImage = 'url(' + src + ')';
        
        // Show modal
        previewModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
      });
    });
    
    // Close modal when close button is clicked
    const closeButtons = previewModal.querySelectorAll('[data-dismiss="modal"]');
    closeButtons.forEach(button => {
      button.addEventListener('click', function() {
        previewModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      });
    });
    
    // Close modal when clicking outside
    previewModal.addEventListener('click', function(e) {
      if (e.target === previewModal) {
        previewModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      }
    });
  });
  
  // Script para habilitar/desabilitar textarea com base no checkbox
  document.addEventListener('DOMContentLoaded', function() {
    const approveToggles = document.querySelectorAll('.approve-toggle');
    approveToggles.forEach(toggle => {
      toggle.addEventListener('change', function() {
        const textareaSelector = this.getAttribute('data-textarea');
        const textarea = document.querySelector(textareaSelector);
        if (this.checked) {
          textarea.disabled = true;
          textarea.value = '';
        } else {
          textarea.disabled = false;
        }
      });
    });
  });
</script>