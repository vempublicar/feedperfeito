<?php
// Simulação de pedidos
$pedidos = [
  [
    'id' => 'PED-1024',
    'titulo' => 'Pacote 10 Posts — Loja de Carros',
    'etapa_atual' => 'Em Produção', // valores válidos: Confirmado, Em Produção, Em Aprovação, Download
    'updated_at' => '2025-09-05 16:22'
  ],
  [
    'id' => 'PED-1025',
    'titulo' => 'Reels — Setembro Semana 2',
    'etapa_atual' => 'Em Aprovação',
    'updated_at' => '2025-09-07 10:10'
  ],
];

$etapas = ['Pedido Confirmado','Pedido em Produção','Pedido em Aprovação','Faça o Download'];

function etapaIndex($etapaAtual) {
  switch ($etapaAtual) {
    case 'Pedido Confirmado':
    case 'Confirmado': return 0;
    case 'Pedido em Produção':
    case 'Em Produção': return 1;
    case 'Pedido em Aprovação':
    case 'Em Aprovação': return 2;
    case 'Faça o Download':
    case 'Download': return 3;
    default: return 0;
  }
}
?>

<section id="painel" class="py-12 bg-background">
  <div class="container mx-auto px-4">
    <div class="mb-12">
      <h2 class="text-3xl font-bold text-black mb-2">
        Andamento <span class="font-light">dos Pedidos</span>
      </h2>
      <div class="w-20 h-1 bg-black mb-4"></div>
      <p class="text-gray-600">Acompanhe o status de produção dos seus conteúdos.</p>
    </div>

    <?php foreach ($pedidos as $pedido): 
      $idxAtual = etapaIndex($pedido['etapa_atual']);
    ?>
      <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6">
          <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 flex-wrap gap-4">
            <div>
              <h5 class="text-xl font-bold text-black mb-1"><?= htmlspecialchars($pedido['titulo']) ?></h5>
              <p class="text-gray-500 text-sm">#<?= htmlspecialchars($pedido['id']) ?> • Atualizado em <?= date('d/m/Y H:i', strtotime($pedido['updated_at'])) ?></p>
            </div>
            <div class="mt-2 md:mt-0">
              <a href="#aprovacao" class="bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300 inline-block">
                Ir para Aprovação
              </a>
            </div>
          </div>

          <!-- Steps -->
          <div class="flex justify-between relative pt-8">
            <?php foreach ($etapas as $i => $etapa): 
              $done  = ($i < $idxAtual);
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
                    <span><?= $i+1 ?></span>
                  <?php endif; ?>
                </div>
                <div class="text-center text-sm
                  <?= $done || $active ? 'text-black font-medium' : 'text-gray-500' ?>">
                  <?= $etapa ?>
                </div>
                <?php if ($i < count($etapas)-1): ?>
                  <div class="absolute top-4 left-1/2 w-full h-1 -z-10
                    <?= $done ? 'bg-black' : 'bg-gray-200' ?>"
                    style="left: 50%; width: 100%; transform: translateX(-50%);"></div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
