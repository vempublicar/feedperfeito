<?php
require_once __DIR__ . '/../models/CreditPackage.php';

// Créditos atuais (use o valor real do backend)
$client_credits = $client_credits ?? 120;

$creditPackageModel = new CreditPackage();
$credit_packs = $creditPackageModel->all();

// helper para custo por crédito (apenas para exibição)
function custoPorCredito($pack)
{
  $totalCred = $pack['credits'] + ($pack['bonus_credits'] ?? 0);
  return $totalCred > 0 ? $pack['price'] / $totalCred : 0;
}
?>

<section id="creditos" class="py-12 bg-background">
  <div class="container mx-auto px-4">
    <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end flex-wrap gap-4">
      <div>
        <h2 class="text-3xl font-bold text-black mb-2">
          Adicionar <span class="font-light">Créditos</span>
        </h2>
        <div class="w-20 h-1 bg-black mb-4"></div>
        <p class="text-gray-600">Abasteça seus créditos para aumentar sua presença digital.</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php foreach ($credit_packs as $p):
        $totalCred = $p['credits'] + ($p['bonus_credits'] ?? 0);
        $cpc = custoPorCredito($p);
        ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden pack-card relative text-center">
          <div class="p-6 flex flex-col h-full">
            <h5 class="text-xl font-bold text-black mb-1"><?= htmlspecialchars($p['title']) ?></h5>
            <div class="m-auto">
              <span class="text-2xl font-bold flex items-center">
                <i class="fas fa-coins text-yellow-500 mr-2"></i> <?= (int) $p['credits'] ?>
              </span>
            </div>
            <div class="mb-4 m-auto">
              <?php if ($p['bonus_credits'] > 0): ?>
                <span
                  class="inline-block bg-green-500 text-white text-xs px-2 py-1 rounded ">+<?= (int) $p['bonus_credits'] ?>
                  créditos de bônus</span>
              <?php endif; ?>
            </div>
            <div class="text-gray-500 mb-2">
              <p class="text-sm">Custo aprox./crédito: R$ <?= number_format($cpc, 2, ',', '.') ?></p>
            </div>

            <div class="mt-auto">
              <div class="text-2xl font-bold mb-4">R$ <?= number_format($p['price'], 2, ',', '.') ?></div>

              <!-- Botão abre modal de confirmação -->
              <button
                class="w-full bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300 btn-comprar-pack"
                data-toggle="modal" data-target="#comprarCreditosModal"
                data-pack='<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>'>
                <i class="fas fa-plus-circle mr-2"></i> Comprar Pacote
              </button>
            </div>
          </div>

          <div class="bg-gray-50 px-6 py-3 flex justify-between items-center">
            <p class="text-gray-500 text-sm">ID: <?= htmlspecialchars($p['id']) ?></p>
            <p class="text-gray-500 text-sm"><?= (int) $totalCred ?> créditos totais</p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<div class="fixed inset-0 z-50 hidden overflow-y-auto" id="comprarCreditosModal">
  <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
      <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

    <form
      class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
      <div class="bg-white">
        <div class="flex justify-between items-center p-4 border-b border-gray-200">
          <h5 class="text-lg font-bold text-black" id="comprarCreditosLabel">Confirmar Compra de Créditos</h5>
          <button type="button" class="close text-gray-500 hover:text-gray-700" data-dismiss="modal"
            aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="p-6">
          <input type="hidden" name="pack_id" id="pack_id">
          <div class="flex items-start">
            <div class="mr-4">
              <div class="rounded bg-gray-100 flex items-center justify-center" style="width:56px;height:56px;">
                <i class="fas fa-coins text-yellow-500 text-2xl"></i>
              </div>
            </div>
            <div class="flex-grow">
              <div class="flex justify-between">
                <h5 id="pack_titulo" class="text-lg font-bold text-black mb-1">—</h5>
                <span class="text-2xl font-bold" id="pack_preco">R$ —</span>
              </div>
              <div class="text-gray-600">
                <span id="pack_creditos">—</span>
                <span id="pack_bonus" class="ml-2"></span>
              </div>
              <hr class="my-4">
              <div class="text-sm">
                Saldo atual: <strong id="saldo_atual"><?= $_SESSION['user_credits'] ?></strong> créditos<br>
                Saldo após compra: <strong id="saldo_novo">—</strong> créditos
              </div>
            </div>
          </div>

          <!-- Observações/Termos -->
          <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 mt-6">
            <p class="text-gray-600 text-sm">
              <strong>O email da compra dever ser o mesmo do cadastro: [<?= $_SESSION['user_email'] ?>]</strong><br>
              Ao confirmar, você será redirecionado para o fluxo de pagamento (ou cobrança automática, se habilitada).
              Os créditos serão adicionados à sua conta assim que o pagamento for confirmado.
            </p>
          </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row justify-end gap-2">
          <button type="button"
            class="bg-white border border-gray-300 text-black py-2 px-4 rounded font-medium hover:bg-gray-100 transition duration-300"
            data-dismiss="modal">Cancelar</button>
          <button type="button" id="confirmarCompraBtn"
            class="bg-black text-white py-2 px-4 rounded font-medium hover:bg-gray-800 transition duration-300">
            <i class="fas fa-check mr-2"></i> Confirmar Compra
          </button>
          <button type="submit" id="pagamentoRealizadoBtn" form="verificarPagamentoForm"
            class="hidden bg-green-500 text-white py-2 px-4 rounded font-medium hover:bg-green-600 transition duration-300">
            <i class="fas fa-check-circle mr-2"></i> Pagamento Realizado
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<form id="verificarPagamentoForm" action="<?= $_SESSION['base_url'] ?>/api/post/verificar_pagamentos.php" method="POST" class="hidden">
    <input type="hidden" name="pack_id" id="verificar_pack_id">
</form>

<script>
  // Script para o modal de compra de créditos
  document.addEventListener('DOMContentLoaded', function () {
    const comprarCreditosModal = document.getElementById('comprarCreditosModal');
    const packId = document.getElementById('pack_id');
    const packTitulo = document.getElementById('pack_titulo');
    const packPreco = document.getElementById('pack_preco');
    const packCreditos = document.getElementById('pack_creditos');
    const packBonus = document.getElementById('pack_bonus');
    const saldoAtual = document.getElementById('saldo_atual');
    const saldoNovo = document.getElementById('saldo_novo');

    // Add event listeners to comprar pack buttons
    const comprarPackButtons = document.querySelectorAll('.btn-comprar-pack');
    comprarPackButtons.forEach(button => {
      button.addEventListener('click', function (e) {
        e.preventDefault();
        const packData = this.getAttribute('data-pack');
        const pack = JSON.parse(packData);

        if (pack) {
          packId.value = pack.id;
          packTitulo.textContent = pack.title;
          packPreco.textContent = 'R$ ' + parseFloat(pack.price).toFixed(2).replace('.', ',');
          packCreditos.textContent = pack.credits + ' créditos';

          const bonusText = pack.bonus_credits > 0 ? '+' + pack.bonus_credits + ' bônus' : '';
          packBonus.textContent = bonusText;

          const saldoAtualValue = parseInt(saldoAtual.textContent);
          const saldoNovoValue = saldoAtualValue + parseInt(pack.credits) + (parseInt(pack.bonus_credits) || 0);
          saldoNovo.textContent = saldoNovoValue;

          // Armazenar o link do pacote no botão de confirmação
          const confirmarCompraBtn = document.getElementById('confirmarCompraBtn');
          const packLink = pack.link;
          confirmarCompraBtn.setAttribute('data-pack-link', packLink);
        }

        // Show modal
        comprarCreditosModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
      });
    });

    // Event listener para o botão Confirmar Compra
    document.getElementById('confirmarCompraBtn').addEventListener('click', function () {
      const link = this.getAttribute('data-pack-link');
      const packIdValue = document.getElementById('pack_id').value; // Pega o pack_id do input oculto existente

      if (link) {
        window.open(link, '_blank', 'width=800,height=600,resizable=yes,scrollbars=yes');

        // Após abrir o link, ocultar o botão "Confirmar Compra" e mostrar "Pagamento Realizado"
        setTimeout(() => {
          const confirmarCompraBtn = document.getElementById('confirmarCompraBtn');
          const pagamentoRealizadoBtn = document.getElementById('pagamentoRealizadoBtn');
          
          confirmarCompraBtn.classList.add('hidden');
          pagamentoRealizadoBtn.classList.remove('hidden');

          // Preencher o formulário oculto
          const verificarPackId = document.getElementById('verificar_pack_id');
          verificarPackId.value = packIdValue; // Atribui o ID do pacote ao input oculto do novo formulário
        }, 1000); // Pequeno atraso para dar tempo de abrir a nova janela
      }
    });

    // Close modal when close button is clicked
    const closeButtons = comprarCreditosModal.querySelectorAll('[data-dismiss="modal"]');
    closeButtons.forEach(button => {
      button.addEventListener('click', function () {
        comprarCreditosModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      });
    });

    // Close modal when clicking outside
    comprarCreditosModal.addEventListener('click', function (e) {
      if (e.target === comprarCreditosModal) {
        comprarCreditosModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      }
    });
  });
</script>