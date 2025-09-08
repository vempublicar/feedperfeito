<?php
// Catálogo (virá do banco futuramente)
$catalogo = [
  [
    'id' => 'TPL-1001',
    'titulo' => 'Carrossel “Oferta da Semana”',
    'categoria' => 'Carrossel',
    'creditos' => 6,
    'preview_url' => 'https://images.unsplash.com/photo-1516245834210-c4c142787335?q=80&w=1200&auto=format&fit=crop',
    'thumb_url'   => 'https://images.unsplash.com/photo-1516245834210-c4c142787335?q=80&w=480&auto=format&fit=crop',
    'destaque' => true,
  ],
  [
    'id' => 'TPL-1002',
    'titulo' => 'Post “Antes & Depois”',
    'categoria' => 'Post',
    'creditos' => 3,
    'preview_url' => 'https://images.unsplash.com/photo-1548075413-9f0e8c2b3f4b?q=80&w=1200&auto=format&fit=crop',
    'thumb_url'   => 'https://images.unsplash.com/photo-1548075413-9f0e8c2b3f4b?q=80&w=480&auto=format&fit=crop',
    'destaque' => false,
  ],
  [
    'id' => 'TPL-1003',
    'titulo' => 'Reels “Truques em 15s”',
    'categoria' => 'Reels',
    'creditos' => 8,
    'preview_url' => 'https://images.unsplash.com/photo-1512446816042-444d641267ee?q=80&w=1200&auto=format&fit=crop',
    'thumb_url'   => 'https://images.unsplash.com/photo-1512446816042-444d641267ee?q=80&w=480&auto=format&fit=crop',
    'destaque' => true,
  ],
  [
    'id' => 'TPL-1004',
    'titulo' => 'Stories “Enquete Interativa”',
    'categoria' => 'Stories',
    'creditos' => 2,
    'preview_url' => 'https://images.unsplash.com/photo-1483478550801-ceba5fe50e8e?q=80&w=1200&auto=format&fit=crop',
    'thumb_url'   => 'https://images.unsplash.com/photo-1483478550801-ceba5fe50e8e?q=80&w=480&auto=format&fit=crop',
    'destaque' => false,
  ],
  [
    'id' => 'TPL-1005',
    'titulo' => 'Post “Depoimento Cliente”',
    'categoria' => 'Post',
    'creditos' => 4,
    'preview_url' => 'https://images.unsplash.com/photo-1507091249565-093fcca5f0c3?q=80&w=1200&auto=format&fit=crop',
    'thumb_url'   => 'https://images.unsplash.com/photo-1507091249565-093fcca5f0c3?q=80&w=480&auto=format&fit=crop',
    'destaque' => false,
  ],
];

$categoriasLoja = array_values(array_unique(array_map(fn($c)=>$c['categoria'], $catalogo)));
sort($categoriasLoja);

// Créditos do cliente (use o real no backend)
$client_credits = $client_credits ?? 120;
?>

<section id="loja" class="py-12 bg-background">
  <div class="container mx-auto px-4">
    <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end flex-wrap gap-4">
      <div>
        <h2 class="text-3xl font-bold text-black mb-2">
          Loja de <span class="font-light">Conteúdos</span>
        </h2>
        <div class="w-20 h-1 bg-black mb-4"></div>
        <p class="text-gray-600">Troque seus créditos por conteúdos prontos para personalizar.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <div class="flex items-center">
          <label class="text-gray-600 mr-2">Categoria:</label>
          <select id="filtroCategoria" class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
            <option value="">Todas</option>
            <?php foreach ($categoriasLoja as $cat): ?>
              <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="flex items-center">
          <label class="sr-only" for="buscaLoja">Buscar</label>
          <input id="buscaLoja" type="search" class="px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent" placeholder="Buscar conteúdo...">
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="gradeLoja">
      <?php foreach ($catalogo as $item): 
        $temCredito = $client_credits >= $item['creditos'];
      ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden loja-item" 
             data-categoria="<?= htmlspecialchars($item['categoria']) ?>"
             data-titulo="<?= htmlspecialchars(mb_strtolower($item['titulo'])) ?>">
          <div class="relative">
            <?php if ($item['destaque']): ?>
              <span class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded">Em alta</span>
            <?php endif; ?>
            <img src="<?= htmlspecialchars($item['thumb_url']) ?>" class="w-full h-48 object-cover" alt="">
          </div>
          <div class="p-6 flex flex-col h-full">
            <div class="mb-3">
              <span class="inline-block bg-gray-100 border border-gray-300 text-gray-700 text-xs px-2 py-1 rounded"><?= htmlspecialchars($item['categoria']) ?></span>
            </div>
            <h5 class="text-xl font-bold text-black mb-3"><?= htmlspecialchars($item['titulo']) ?></h5>
            <p class="text-gray-600 mb-6">
              Requer <strong><?= (int)$item['creditos'] ?></strong> créditos para personalização.
            </p>

            <div class="mt-auto flex justify-between">
              <!-- Visualizar -->
              <button
                class="bg-white border border-gray-300 text-black py-2 px-4 rounded font-medium hover:bg-gray-100 transition duration-300"
                data-toggle="modal"
                data-target="#previewModal"
                data-id="<?= htmlspecialchars($item['id']) ?>"
                data-title="<?= htmlspecialchars($item['titulo']) ?>"
                data-src="<?= htmlspecialchars($item['preview_url']) ?>"
              >
                <i class="fas fa-eye mr-2"></i> Visualizar
              </button>

              <!-- Personalizar -->
              <form method="post" action="solicitar_personalizacao.php" class="m-0 p-0">
                <input type="hidden" name="template_id" value="<?= htmlspecialchars($item['id']) ?>">
                <input type="hidden" name="creditos_requeridos" value="<?= (int)$item['creditos'] ?>">
                <?php if ($temCredito): ?>
                  <button type="submit" class="bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300">
                    <i class="fas fa-magic mr-2"></i> Personalizar Para Mim
                  </button>
                <?php else: ?>
                  <button type="button" class="bg-gray-200 text-gray-500 py-2 px-4 rounded font-medium cursor-not-allowed" disabled title="Créditos insuficientes">
                    <i class="fas fa-ban mr-2"></i> Créditos insuficientes
                  </button>
                <?php endif; ?>
              </form>
            </div>
          </div>
          <div class="bg-gray-50 px-6 py-3 flex justify-between items-center">
            <p class="text-gray-500 text-sm mb-0">ID: <?= htmlspecialchars($item['id']) ?></p>
            <p class="text-gray-500 text-sm">
              <i class="fas fa-coins text-yellow-500 mr-1"></i> Você: <strong><?= (int)$client_credits ?></strong>
            </p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-8">
      <a href="#creditos" class="bg-white border border-green-500 text-green-600 py-2 px-4 rounded font-medium hover:bg-green-50 transition duration-300 inline-block">
        <i class="fas fa-plus-circle mr-2"></i> Comprar Créditos
      </a>
    </div>
  </div>
</section>
