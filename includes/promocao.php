<?php
$promocoes = [
  [
    'id' => 'PRM-9001',
    'titulo' => 'Combo Carrossel + Reels — Lançamento',
    'descricao' => 'Pacote de 5 artes + 1 Reels otimizado para conversão.',
    'creditos_de' => 18,
    'creditos_por' => 12,
    'preview_url' => 'https://images.unsplash.com/photo-1551554781-2f27f0d57a6a?q=80&w=1200&auto=format&fit=crop',
    'thumb_url'   => 'https://images.unsplash.com/photo-1551554781-2f27f0d57a6a?q=80&w=480&auto=format&fit=crop',
    'expira_em' => '2025-09-20 23:59:59',
    'tag' => 'Lançamento'
  ],
  [
    'id' => 'PRM-9002',
    'titulo' => 'Stories Interativos (3 peças)',
    'descricao' => 'Enquetes e CTAs prontos para aumentar o engajamento.',
    'creditos_de' => 9,
    'creditos_por' => 6,
    'preview_url' => 'https://images.unsplash.com/photo-1511765224389-37f0e77cf0eb?q=80&w=1200&auto=format&fit=crop',
    'thumb_url'   => 'https://images.unsplash.com/photo-1511765224389-37f0e77cf0eb?q=80&w=480&auto=format&fit=crop',
    'expira_em' => '2025-09-14 23:59:59',
    'tag' => 'Em alta'
  ],
  [
    'id' => 'PRM-9003',
    'titulo' => 'Post Depoimento + Mockup',
    'descricao' => 'Depoimento com mockup de celular para prova social.',
    'creditos_de' => 7,
    'creditos_por' => 4,
    'preview_url' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?q=80&w=1200&auto=format&fit=crop',
    'thumb_url'   => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?q=80&w=480&auto=format&fit=crop',
    'expira_em' => '2025-09-30 23:59:59',
    'tag' => 'Oferta'
  ],
];

// créditos atuais (use valor real do backend)
$client_credits = $client_credits ?? 120;
?>

<section id="promocoes" class="py-12 bg-white">
  <div class="container mx-auto px-4">
    <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end flex-wrap gap-4">
      <div>
        <h2 class="text-3xl font-bold text-black mb-2">
          Destaques & <span class="font-light">Promoções</span>
        </h2>
        <div class="w-20 h-1 bg-black mb-4"></div>
        <p class="text-gray-600">Seleções especiais com desconto por tempo limitado.</p>
      </div>
      <a href="#loja" class="bg-white border border-gray-300 text-black py-2 px-4 rounded font-medium hover:bg-gray-100 transition duration-300 inline-block">
        Ver Loja Completa
      </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php foreach ($promocoes as $p): 
        $economia = max(0, (int)$p['creditos_de'] - (int)$p['creditos_por']);
        $temCredito = $client_credits >= (int)$p['creditos_por'];
        $cardId = 'promo_' . htmlspecialchars($p['id']);
      ?>
        <div class="bg-background rounded-lg shadow-md overflow-hidden promo-card" id="<?= $cardId ?>">
          <div class="relative">
            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">Promo</span>
            <?php if (!empty($p['tag'])): ?>
              <span class="absolute top-2 left-20 bg-green-500 text-white text-xs px-2 py-1 rounded"><?= htmlspecialchars($p['tag']) ?></span>
            <?php endif; ?>
            <img src="<?= htmlspecialchars($p['thumb_url']) ?>" class="w-full h-48 object-cover" alt="">
          </div>
          <div class="p-6 flex flex-col h-full">
            <h5 class="text-xl font-bold text-black mb-1"><?= htmlspecialchars($p['titulo']) ?></h5>
            <p class="text-gray-600 mb-4"><?= htmlspecialchars($p['descricao']) ?></p>

            <div class="flex items-center mb-4">
              <span class="text-gray-500 line-through mr-2"><?= (int)$p['creditos_de'] ?></span>
              <span class="text-xl font-bold"><i class="fas fa-coins text-yellow-500 mr-1"></i> <?= (int)$p['creditos_por'] ?></span>
              <?php if ($economia > 0): ?>
                <span class="inline-block bg-gray-100 border border-gray-300 text-gray-700 text-xs px-2 py-1 rounded ml-2">Economize <?= $economia ?></span>
              <?php endif; ?>
            </div>

            <!-- countdown -->
            <div class="text-gray-500 text-sm mb-6 flex items-center">
              <i class="fas fa-clock mr-2"></i>
              <span class="promo-countdown" data-expira="<?= htmlspecialchars($p['expira_em']) ?>">…</span>
            </div>

            <div class="mt-auto flex justify-between">
              <!-- Visualizar -->
              <button class="bg-white border border-gray-300 text-black py-2 px-4 rounded font-medium hover:bg-gray-100 transition duration-300"
                      data-toggle="modal"
                      data-target="#previewModal"
                      data-id="<?= htmlspecialchars($p['id']) ?>"
                      data-title="<?= htmlspecialchars($p['titulo']) ?>"
                      data-src="<?= htmlspecialchars($p['preview_url']) ?>">
                <i class="fas fa-eye mr-2"></i> Visualizar
              </button>

              <!-- Personalizar/Comprar -->
              <form method="post" action="solicitar_personalizacao.php" class="m-0 p-0">
                <input type="hidden" name="template_id" value="<?= htmlspecialchars($p['id']) ?>">
                <input type="hidden" name="creditos_requeridos" value="<?= (int)$p['creditos_por'] ?>">
                <?php if ($temCredito): ?>
                  <button type="submit" class="bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300">
                    <i class="fas fa-magic mr-2"></i> Personalizar Para Mim
                  </button>
                <?php else: ?>
                  <a href="#creditos" class="bg-white border border-gray-300 text-black py-2 px-4 rounded font-medium hover:bg-gray-100 transition duration-300 inline-block">
                    <i class="fas fa-plus-circle mr-2"></i> Adicionar créditos
                  </a>
                <?php endif; ?>
              </form>
            </div>
          </div>

          <div class="bg-gray-50 px-6 py-3 flex justify-between items-center">
            <p class="text-gray-500 text-sm">ID: <?= htmlspecialchars($p['id']) ?></p>
            <p class="text-gray-500 text-sm"><i class="fas fa-coins text-yellow-500 mr-1"></i> Você: <strong><?= (int)$client_credits ?></strong></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (empty($promocoes)): ?>
      <div class="bg-gray-100 border border-gray-300 rounded-lg p-6 text-center">Sem promoções no momento. Volte mais tarde ou confira a <a href="#loja" class="text-black font-medium hover:underline">Loja</a>.</div>
    <?php endif; ?>
  </div>
</section>
