<?php
require_once __DIR__ . '/../config/session.php';
requireUserLogin();
$themesJsonPath = __DIR__ . '/../config/themes.json';
$themesData = [];
if (file_exists($themesJsonPath)) {
    $themesJson = file_get_contents($themesJsonPath);
    $themesData = json_decode($themesJson, true);
}

// Obter o UID do usuário. Assumindo que está disponível na sessão.
$uid_usuario = $_SESSION['user_id'] ?? null;
$user_doc_path = __DIR__ . '/../doc/' . $uid_usuario;

// Função auxiliar para carregar dados de JSON
function load_json_data($file_path) {
    if (file_exists($file_path)) {
        return json_decode(file_get_contents($file_path), true);
    }
    return [];
}

// Carregar dados das seções
$redes_sociais_saved = load_json_data($user_doc_path . '/redes_sociais.json');
$cores_saved = load_json_data($user_doc_path . '/cores.json');
$temas_interesse_saved = load_json_data($user_doc_path . '/temas_interesse.json');
$segmento_saved = load_json_data($user_doc_path . '/segmento.json');
$textos_personalizados_saved = load_json_data($user_doc_path . '/textos_personalizados.json');
$logotipos_saved = load_json_data($user_doc_path . '/logotipos.json');
$imagens_artes_saved = load_json_data($user_doc_path . '/imagens_artes.json');
$elementos_design_saved = load_json_data($user_doc_path . '/elementos_design.json');
$imagens_empresa_saved = load_json_data($user_doc_path . '/imagens_empresa.json');

?>

<div class="container mx-auto p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Personalize sua Marca</h2>
    <div class="w-20 h-1 bg-black mb-6"></div>
    <div id="message-container" class="mb-4 p-3 rounded text-white hidden"></div>

    <form id="personalizationForm" enctype="multipart/form-data">
        <!-- Redes Sociais - Movida para o topo, largura total -->
        <div class="mb-6 border rounded-lg overflow-hidden"> <!-- Adicionado border e hidden para colapsar -->
            <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="redes-sociais-collapse">
                <div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">
                        Informe suas redes sociais
                        <?php if (!empty(array_filter($redes_sociais_saved))): ?>
                            <svg class="h-5 w-5 text-green-500 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <?php endif; ?>
                    </h3>
                    <p class="text-sm text-gray-500">Links para suas redes sociais e website.</p>
                </div>
            </div>
            <div id="redes-sociais-collapse" class="p-4 hidden">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="instagram" class="block text-sm font-medium text-gray-700">Instagram</label>
                        <input type="text" name="instagram" id="instagram"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                            placeholder="Link ou @ do Instagram" value="<?= htmlspecialchars($redes_sociais_saved['instagram'] ?? '') ?>">
                </div>
                <div>
                    <label for="facebook" class="block text-sm font-medium text-gray-700">Facebook</label>
                    <input type="text" name="facebook" id="facebook"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                        placeholder="Link do Facebook" value="<?= htmlspecialchars($redes_sociais_saved['facebook'] ?? '') ?>">
                </div>
                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700">WhatsApp</label>
                    <input type="text" name="whatsapp" id="whatsapp"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                        placeholder="Número do WhatsApp com DDD" value="<?= htmlspecialchars($redes_sociais_saved['whatsapp'] ?? '') ?>">
                </div>
                <div>
                    <label for="site" class="block text-sm font-medium text-gray-700">Website</label>
                    <input type="text" name="site" id="site"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                        placeholder="Link do seu Website" value="<?= htmlspecialchars($redes_sociais_saved['site'] ?? '') ?>">
                </div>
            </div>
            <div class="mt-4 text-right">
                <button type="button" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition save-section-btn" data-section="redes_sociais">
                    Salvar
                </button>
            </div>
        </div>
    </div>

        <div class="flex flex-wrap -mx-3">
            <!-- Coluna da Esquerda -->
            <div class="w-full lg:w-1/2 px-3">
                <!-- Cores Principais -->
                <div class="mb-6 border rounded-lg overflow-hidden">
                    <!-- Cabeçalho do Colapso -->
                    <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="cores-principais-collapse">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-700">
                                Cores Principais
                                <?php if (!empty(array_filter($cores_saved))): ?>
                                    <svg class="h-5 w-5 text-green-500 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <?php endif; ?>
                            </h3>
                            <p class="text-sm text-gray-500">Selecione as 4 cores principais da sua marca.</p>
                        </div>
                    </div>
                    <!-- Conteúdo Colapsável -->
                    <div id="cores-principais-collapse" class="p-4 hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="cor_principal" class="block text-sm font-medium text-gray-700">Cor Principal</label>
                                <input type="color" name="cor_principal" id="cor_principal" value="<?= htmlspecialchars($cores_saved['cor_principal'] ?? '#000000') ?>"
                                    class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                            </div>
                            <div>
                                <label for="cor_secundaria" class="block text-sm font-medium text-gray-700">Cor Secundária</label>
                                <input type="color" name="cor_secundaria" id="cor_secundaria" value="<?= htmlspecialchars($cores_saved['cor_secundaria'] ?? '#FFFFFF') ?>"
                                    class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                            </div>
                            <div>
                                <label for="cor_complemento" class="block text-sm font-medium text-gray-700">Cor de
                                    Complemento</label>
                                <input type="color" name="cor_complemento" id="cor_complemento" value="<?= htmlspecialchars($cores_saved['cor_complemento'] ?? '#CCCCCC') ?>"
                                    class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                            </div>
                            <div>
                                <label for="cor_destaque" class="block text-sm font-medium text-gray-700">Cor Destaque</label>
                                <input type="color" name="cor_destaque" id="cor_destaque" value="<?= htmlspecialchars($cores_saved['cor_destaque'] ?? '#FFD700') ?>"
                                    class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                            </div>
                        </div>
                        <div class="mt-4 text-right">
                            <button type="button" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition save-section-btn" data-section="cores">
                                Salvar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Temas de Interesse -->
                <div class="mb-6 border rounded-lg overflow-hidden">
                    <!-- Cabeçalho do Colapso -->
                    <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="temas-interesse-collapse">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-700">
                                Temas de Interesse
                                <?php if (!empty(array_filter($temas_interesse_saved))): ?>
                                    <svg class="h-5 w-5 text-green-500 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <?php endif; ?>
                            </h3>
                            <p class="text-sm text-gray-500">Selecione os temas e subtemas de interesse da sua marca.</p>
                        </div>
                    </div>
                    <!-- Conteúdo Colapsável -->
                    <div id="temas-interesse-collapse" class="p-4 hidden">
                        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php if (!empty($themesData['temas'])): ?>
                                <?php foreach ($themesData['temas'] as $theme): ?>
                                    <div class="mb-2">
                                        <div class="flex items-center">
                                            <span class="ml-2 text-gray-700 font-semibold"><?= htmlspecialchars($theme['nome']) ?></span>
                                        </div>
                                        <?php if (!empty($theme['subtemas'])): ?>
                                            <div class="ml-6 mt-1 subthemes-container" data-parent-theme="<?= htmlspecialchars($theme['nome']) ?>">
                                                <?php foreach ($theme['subtemas'] as $subtheme): ?>
                                                    <label class="flex items-center text-sm text-gray-600">
                                                        <input type="checkbox" name="subtemas[<?= htmlspecialchars($theme['nome']) ?>][]" value="<?= htmlspecialchars($subtheme) ?>"
                                                            class="form-checkbox h-4 w-4 text-black rounded subtheme-checkbox"
                                                            <?= in_array($subtheme, $temas_interesse_saved['subtemas'] ?? []) ? 'checked' : '' ?>>
                                                        <span class="ml-2"><?= htmlspecialchars($subtheme) ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500">Nenhum tema disponível.</p>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4 text-right">
                            <div class="mt-4 text-right">
                                <button type="button" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition save-section-btn" data-section="temas_interesse">
                                    Salvar Temas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Segmento -->
                <div class="mb-6 border rounded-lg overflow-hidden">
                    <!-- Cabeçalho do Colapso -->
                    <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="segmento-collapse">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-700">
                                Segmento
                                <?php if (!empty($segmento_saved['segmento'])): ?>
                                    <svg class="h-5 w-5 text-green-500 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <?php endif; ?>
                            </h3>
                            <p class="text-sm text-gray-500">Informe o segmento de atuação da sua marca.</p>
                        </div>
                    </div>
                    <!-- Conteúdo Colapsável -->
                    <div id="segmento-collapse" class="p-4 hidden">
                        <input type="text" name="segmento" id="segmento"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                            placeholder="Ex: Marketing Digital, Nutrição, Varejo de Roupas" value="<?= htmlspecialchars($segmento_saved['segmento'] ?? '') ?>">
                        <div class="mt-4 text-right">
                            <button type="button" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition save-section-btn" data-section="segmento">
                                Salvar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Textos Personalizados -->
                <div class="mb-6 border rounded-lg overflow-hidden">
                    <!-- Cabeçalho do Colapso -->
                    <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="textos-personalizados-collapse">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-700">
                                Textos Personalizados
                                <?php if (!empty(array_filter($textos_personalizados_saved))): ?>
                                    <svg class="h-5 w-5 text-green-500 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <?php endif; ?>
                            </h3>
                            <p class="text-sm text-gray-500">Adicione textos personalizados para suas artes.</p>
                        </div>
                    </div>
                    <!-- Conteúdo Colapsável -->
                    <div id="textos-personalizados-collapse" class="p-4 hidden">
                        <div class="space-y-4">
                            <div>
                                <label for="texto_personalizado_1" class="block text-sm font-medium text-gray-700">Texto 1</label>
                                <textarea name="texto_personalizado_1" id="texto_personalizado_1" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                    placeholder="Insira seu texto personalizado aqui..."><?= htmlspecialchars($textos_personalizados_saved['texto_personalizado_1'] ?? '') ?></textarea>
                            </div>
                            <div>
                                <label for="texto_personalizado_2" class="block text-sm font-medium text-gray-700">Texto 2</label>
                                <textarea name="texto_personalizado_2" id="texto_personalizado_2" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                    placeholder="Insira seu texto personalizado aqui..."><?= htmlspecialchars($textos_personalizados_saved['texto_personalizado_2'] ?? '') ?></textarea>
                            </div>
                            <div>
                                <label for="texto_personalizado_3" class="block text-sm font-medium text-gray-700">Texto 3</label>
                                <textarea name="texto_personalizado_3" id="texto_personalizado_3" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                                    placeholder="Insira seu texto personalizado aqui..."><?= htmlspecialchars($textos_personalizados_saved['texto_personalizado_3'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="mt-4 text-right">
                            <button type="button" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition save-section-btn" data-section="textos_personalizados">
                                Salvar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coluna da Direita -->
            <div class="w-full lg:w-1/2 px-3">
                <!-- Upload de Logotipos -->
                <div class="mb-6 border rounded-lg overflow-hidden">
                    <!-- Cabeçalho do Colapso -->
                    <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="logotipos-collapse">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-700">
                                Logotipos
                                <?php if (!empty($logotipos_saved)): ?>
                                    <svg class="h-5 w-5 text-green-500 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <?php endif; ?>
                            </h3>
                            <p class="text-sm text-gray-500">Carregue até 5 variações do seu logotipo.</p>
                        </div>
                    </div>
                    <!-- Conteúdo Colapsável -->
                    <div id="logotipos-collapse" class="p-4 hidden">
                        <input type="file" name="logotipos[]" id="logotipos" multiple accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800">
                        <p class="text-xs text-gray-500 mt-1">Máximo de 5 arquivos.</p>
                        <div id="logotipos-preview" class="flex flex-wrap gap-2 mt-2">
                            <?php if (!empty($logotipos_saved)): ?>
                                <?php foreach ($logotipos_saved as $logo): ?>
                                    <div class="relative w-24 h-24 border rounded overflow-hidden">
                                        <img src="<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario) ?>/logotipos/<?= htmlspecialchars($logo) ?>" alt="Logotipo" class="w-full h-full object-cover cursor-pointer" onclick="openImageModal(this.src, 'logotipos')">
                                        <button type="button" class="absolute top-0 right-0 bg-red-500 text-white rounded-bl-lg p-1 text-xs" onclick="removeImage(this, 'logotipos', '<?= htmlspecialchars($logo) ?>')">X</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4 text-right">
                            <button type="button" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition save-section-btn" data-section="logotipos">
                                Salvar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Upload de Imagens para Artes -->
                <div class="mb-6 border rounded-lg overflow-hidden">
                    <!-- Cabeçalho do Colapso -->
                    <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="imagens-artes-collapse">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-700">
                                Imagens para Artes
                                <?php if (!empty($imagens_artes_saved)): ?>
                                    <svg class="h-5 w-5 text-green-500 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <?php endif; ?>
                            </h3>
                            <p class="text-sm text-gray-500">Carregue até 10 imagens para aplicação nas artes.</p>
                        </div>
                    </div>
                    <!-- Conteúdo Colapsável -->
                    <div id="imagens-artes-collapse" class="p-4 hidden">
                        <input type="file" name="imagens_artes[]" id="imagens_artes" multiple accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800">
                        <p class="text-xs text-gray-500 mt-1">Máximo de 10 arquivos.</p>
                        <div id="imagens_artes-preview" class="flex flex-wrap gap-2 mt-2">
                            <?php if (!empty($imagens_artes_saved)): ?>
                                <?php foreach ($imagens_artes_saved as $img): ?>
                                    <div class="relative w-24 h-24 border rounded overflow-hidden">
                                        <img src="<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario) ?>/imagens_artes/<?= htmlspecialchars($img) ?>" alt="Imagem para Arte" class="w-full h-full object-cover cursor-pointer" onclick="openImageModal(this.src, 'imagens_artes')">
                                        <button type="button" class="absolute top-0 right-0 bg-red-500 text-white rounded-bl-lg p-1 text-xs" onclick="removeImage(this, 'imagens_artes', '<?= htmlspecialchars($img) ?>')">X</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4 text-right">
                            <button type="button" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition save-section-btn" data-section="imagens_artes">
                                Salvar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Upload de Elementos de Design -->
                <div class="mb-6 border rounded-lg overflow-hidden">
                    <!-- Cabeçalho do Colapso -->
                    <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="elementos-design-collapse">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-700">
                                Elementos de Design
                                <?php if (!empty($elementos_design_saved)): ?>
                                    <svg class="h-5 w-5 text-green-500 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <?php endif; ?>
                            </h3>
                            <p class="text-sm text-gray-500">Carregue até 5 elementos de design da sua marca.</p>
                        </div>
                    </div>
                    <!-- Conteúdo Colapsável -->
                    <div id="elementos-design-collapse" class="p-4 hidden">
                        <input type="file" name="elementos_design[]" id="elementos_design" multiple accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800">
                        <p class="text-xs text-gray-500 mt-1">Máximo de 5 arquivos.</p>
                        <div id="elementos_design-preview" class="flex flex-wrap gap-2 mt-2">
                            <?php if (!empty($elementos_design_saved)): ?>
                                <?php foreach ($elementos_design_saved as $elem): ?>
                                    <div class="relative w-24 h-24 border rounded overflow-hidden">
                                        <img src="<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario) ?>/elementos_design/<?= htmlspecialchars($elem) ?>" alt="Elemento de Design" class="w-full h-full object-cover cursor-pointer" onclick="openImageModal(this.src, 'elementos_design')">
                                        <button type="button" class="absolute top-0 right-0 bg-red-500 text-white rounded-bl-lg p-1 text-xs" onclick="removeImage(this, 'elementos_design', '<?= htmlspecialchars($elem) ?>')">X</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4 text-right">
                            <button type="button" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition save-section-btn" data-section="elementos_design">
                                Salvar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Upload de Imagens da Empresa -->
                <div class="mb-6 border rounded-lg overflow-hidden">
                    <!-- Cabeçalho do Colapso -->
                    <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="imagens-empresa-collapse">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-700">
                                Imagens da Empresa
                                <?php if (!empty($imagens_empresa_saved)): ?>
                                    <svg class="h-5 w-5 text-green-500 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <?php endif; ?>
                            </h3>
                            <p class="text-sm text-gray-500">Carregue até 5 imagens da sua empresa (interno ou externo).</p>
                        </div>
                    </div>
                    <!-- Conteúdo Colapsável -->
                    <div id="imagens-empresa-collapse" class="p-4 hidden">
                        <input type="file" name="imagens_empresa[]" id="imagens_empresa" multiple accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800">
                        <p class="text-xs text-gray-500 mt-1">Máximo de 5 arquivos.</p>
                        <div id="imagens_empresa-preview" class="flex flex-wrap gap-2 mt-2">
                            <?php if (!empty($imagens_empresa_saved)): ?>
                                <?php foreach ($imagens_empresa_saved as $img): ?>
                                    <div class="relative w-24 h-24 border rounded overflow-hidden">
                                        <img src="<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario) ?>/imagens_empresa/<?= htmlspecialchars($img) ?>" alt="Imagem da Empresa" class="w-full h-full object-cover cursor-pointer" onclick="openImageModal(this.src, 'imagens_empresa')">
                                        <button type="button" class="absolute top-0 right-0 bg-red-500 text-white rounded-bl-lg p-1 text-xs" onclick="removeImage(this, 'imagens_empresa', '<?= htmlspecialchars($img) ?>')">X</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4 text-right">
                            <button type="button" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition save-section-btn" data-section="imagens_empresa">
                                Salvar
                            </button>
                        </div>
                    </div>
                </div>

                
            </div>
        </div>
        <!-- Produtos em Tabela -->
                <div class="mb-6 border rounded-lg overflow-hidden">
                    <!-- Cabeçalho do Colapso -->
                    <div class="flex justify-between items-center bg-gray-100 p-4 cursor-pointer" data-collapse-toggle="fotos-produtos-collapse">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-700">
                                Fotos de Produtos
                            </h3>
                            <p class="text-sm text-gray-500">Gerencie as fotos dos seus produtos.</p>
                        </div>
                        <button id="openAddProductModal" type="button"
                            class="bg-black text-white text-sm px-4 py-2 rounded hover:bg-gray-800 transition">
                            + Adicionar Produto
                        </button>
                    </div>
                    <!-- Conteúdo Colapsável -->
                    <div id="fotos-produtos-collapse" class="p-4 hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600">Nome</th>
                                        <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600">Descrição</th>
                                        <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600">Informação</th>
                                        <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600">Imagens</th>
                                        <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <!-- Linhas de produtos serão adicionadas aqui via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
    </form>
</div>
<!-- Modal Adicionar Produto -->
<div id="addProductModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Adicionar Novo Produto</h3>

        <div class="mb-4">
            <label for="new_product_name" class="block text-sm font-medium text-gray-700">Nome do Produto</label>
            <input type="text" id="new_product_name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                placeholder="Ex: Post para Instagram">
        </div>

        <div class="mb-4">
            <label for="new_product_description" class="block text-sm font-medium text-gray-700">Descrição</label>
            <textarea id="new_product_description" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                placeholder="Descreva seu produto..."></textarea>
        </div>

        <div class="mb-4">
            <label for="new_product_info" class="block text-sm font-medium text-gray-700">Informação do Produto</label>
            <textarea id="new_product_info" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                placeholder="Informações adicionais..."></textarea>
        </div>

        <div class="mb-4">
            <label for="new_product_images" class="block text-sm font-medium text-gray-700">Imagens do Produto</label>
            <input type="file" id="new_product_images" multiple accept="image/*"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800">
        </div>

        <div class="flex justify-between items-center gap-2">
            <span class="text-sm text-gray-600">Custo: <span id="product_credit_cost" class="font-semibold">200 Créditos</span></span>
            <div class="flex gap-2">
                <button type="button" id="closeAddProductModal"
                    class="text-gray-700 border border-gray-300 px-4 py-2 rounded hover:bg-gray-100">Cancelar</button>
                <button type="button" id="addProductBtn" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800">Adicionar</button>
            </div>
        </div>
    </div>
</div>
<script>
    const modal = document.getElementById('addProductModal');
    const openBtn = document.getElementById('openAddProductModal');
    const closeBtn = document.getElementById('closeAddProductModal');

    openBtn?.addEventListener('click', () => {
        modal.classList.remove('hidden');
    });

    closeBtn?.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    // (Opcional) Clique fora do modal para fechar
    window.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
    let currentProducts = []; // Array para armazenar os produtos da tabela

    // Função para adicionar produto à tabela HTML e ao array currentProducts
    function addProductToTable(productId, productName, productDescription, productInfo, productImages, isSaved = false) {
        const newRow = productsTableBody.insertRow();
        newRow.className = 'border-b';
        newRow.dataset.productId = productId; // Adiciona o ID ao elemento da linha

        const nameCell = newRow.insertCell();
        nameCell.className = 'py-2 px-4';
        nameCell.textContent = productName;

        const descriptionCell = newRow.insertCell();
        descriptionCell.className = 'py-2 px-4';
        descriptionCell.textContent = productDescription;

        const infoCell = newRow.insertCell();
        infoCell.className = 'py-2 px-4';
        infoCell.textContent = productInfo;

        const imagesCell = newRow.insertCell();
        imagesCell.className = 'py-2 px-4 flex flex-wrap gap-1';
        if (Array.isArray(productImages)) {
            productImages.forEach(imgName => {
                const imgElement = document.createElement('img');
                imgElement.src = `<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario) ?>/produtos/${imgName}`;
                imgElement.className = 'w-10 h-10 object-cover rounded-sm cursor-pointer';
                imgElement.onclick = () => openImageModal(imgElement.src, 'produtos');
                imagesCell.appendChild(imgElement);
            });
        }

        const actionsCell = newRow.insertCell();
        actionsCell.className = 'py-2 px-4';
        actionsCell.innerHTML = `
            <button type="button" class="text-blue-600 hover:text-blue-800 edit-product-btn" data-product-id="${productId}">Editar</button>
            <button type="button" class="text-red-600 hover:text-red-800 delete-product-btn" data-product-id="${productId}">Excluir</button>
        `;

        // Adicionar ao array currentProducts
        if (!isSaved) { // Evita duplicar produtos já salvos ao carregar a página
            currentProducts.push({
                id: productId,
                name: productName,
                description: productDescription,
                info: productInfo,
                images: Array.from(productImages).map(file => file.name) // Salva apenas os nomes dos arquivos
            });
        }
    }

    // Event listener para o botão "Adicionar" do modal de produtos
    const addProductBtn = document.getElementById('addProductBtn');
    const productsTableBody = document.getElementById('productsTableBody');
    const newProductNameInput = document.getElementById('new_product_name');
    const newProductDescriptionInput = document.getElementById('new_product_description');
    const newProductInfoInput = document.getElementById('new_product_info');
    const newProductImagesInput = document.getElementById('new_product_images');

    addProductBtn?.addEventListener('click', async () => {
        const productName = newProductNameInput.value;
        const productDescription = newProductDescriptionInput.value;
        const productInfo = newProductInfoInput.value;
        const productImages = newProductImagesInput.files;

        if (!productName || !productDescription || !productInfo || productImages.length === 0) {
            alert('Por favor, preencha todos os campos e selecione as imagens.');
            return;
        }

        const formData = new FormData();
        formData.append('section_type', 'product_management');
        formData.append('product_action', 'add');
        formData.append('name', productName);
        formData.append('description', productDescription);
        formData.append('info', productInfo);
        for (let i = 0; i < productImages.length; i++) {
            formData.append('images[]', productImages[i]);
        }

        try {
            const response = await fetch('<?= $_SESSION['base_url'] ?>/api/save_personalization.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                showMessage('Produto adicionado com sucesso!', 'success');
                // Adicionar o produto à tabela e ao array currentProducts com o product_id retornado
                addProductToTable(result.product_id, productName, productDescription, productInfo, result.image_names, true);
                // Limpar o formulário e fechar o modal
                newProductNameInput.value = '';
                newProductDescriptionInput.value = '';
                newProductInfoInput.value = '';
                newProductImagesInput.value = ''; // Limpa o input de arquivo
                modal.classList.add('hidden');
                // Recarregar os produtos para garantir que a lista esteja atualizada
                loadProducts();
            } else {
                showMessage('Erro ao adicionar produto: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Erro ao adicionar produto:', error);
            showMessage('Ocorreu um erro ao adicionar o produto.', 'error');
        }
    });

    // Event listener para os botões de ação (editar e excluir)
    productsTableBody.addEventListener('click', async (event) => {
        const target = event.target;
        if (target.classList.contains('delete-product-btn')) {
            const productIdToDelete = target.dataset.productId;
            if (confirm('Tem certeza que deseja excluir este produto?')) {
                const formData = new FormData();
                formData.append('section_type', 'product_management');
                formData.append('product_action', 'delete');
                formData.append('product_id', productIdToDelete);

                try {
                    const response = await fetch('<?= $_SESSION['base_url'] ?>/api/save_personalization.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();

                    if (result.success) {
                        showMessage('Produto excluído com sucesso!', 'success');
                        target.closest('tr').remove(); // Remove a linha da tabela
                        currentProducts = currentProducts.filter(p => p.id !== productIdToDelete); // Remove do array JS
                    } else {
                        showMessage('Erro ao excluir produto: ' + result.message, 'error');
                    }
                } catch (error) {
                    console.error('Erro ao excluir produto:', error);
                    showMessage('Ocorreu um erro ao excluir o produto.', 'error');
                }
            }
        } else if (target.classList.contains('edit-product-btn')) {
            const productIdToEdit = target.dataset.productId;
            const productToEdit = currentProducts.find(p => p.id === productIdToEdit);
            if (productToEdit) {
                openEditProductModal(productToEdit);
            }
        }
    });

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

    // Função para exibir mensagens personalizadas
    function showMessage(message, type = 'success') {
        const messageContainer = document.getElementById('message-container');
        messageContainer.textContent = message;
        messageContainer.classList.remove('hidden', 'bg-green-500', 'bg-red-500'); // Limpa classes anteriores
        if (type === 'success') {
            messageContainer.classList.add('bg-green-500');
        } else {
            messageContainer.classList.add('bg-red-500');
        }
        messageContainer.classList.remove('hidden');

        // Esconder a mensagem após 5 segundos
        setTimeout(() => {
            messageContainer.classList.add('hidden');
        }, 5000);
    }

    // Lidar com o envio do formulário via AJAX - agora para botões individuais
    document.querySelectorAll('.save-section-btn').forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();

            const sectionType = button.getAttribute('data-section');
            const formData = new FormData();

            // Adicionar apenas os dados da seção específica ao FormData
            // Encontra o div de conteúdo colapsável associado ao botão
            const collapseHeader = button.closest('.mb-6').querySelector('[data-collapse-toggle]');
            const targetElementId = collapseHeader.getAttribute('data-collapse-toggle');
            const targetElement = document.getElementById(targetElementId);
            
            if (!targetElement) {
                showMessage('Erro: Conteúdo da seção não encontrado.', 'error');
                return;
            }
            
            // Adicionar inputs de texto, cor, number e textareas
            targetElement.querySelectorAll('input:not([type="file"]), textarea').forEach(input => {
                formData.append(input.name, input.value);
            });

            // REMOVIDO: Adicionar checkboxes (temas e subtemas)
            // REMOVIDO: targetElement.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
            // REMOVIDO:    formData.append(checkbox.name, checkbox.value);
            // REMOVIDO: });

            // Adicionar arquivos para as seções de upload
            if (sectionType === 'logotipos' || sectionType === 'imagens_artes' || 
                sectionType === 'elementos_design' || sectionType === 'imagens_empresa') {
                const fileInput = targetElement.querySelector('input[type="file"]');
                if (fileInput && fileInput.files.length > 0) {
                    for (let i = 0; i < fileInput.files.length; i++) {
                        formData.append(fileInput.name, fileInput.files[i]); // Envia os arquivos
                    }
                }
            }


            formData.append('section_type', sectionType); // Enviar o tipo de seção para o PHP

            try {
                const response = await fetch('<?= $_SESSION['base_url'] ?>/api/save_personalization.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showMessage('Dados da seção "' + sectionType + '" salvos com sucesso!', 'success');
                    // Retrair o colapso após salvar com sucesso
                    targetElement.classList.add('hidden');
                } else {
                    showMessage('Erro ao salvar seção "' + sectionType + '": ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Erro ao enviar dados da seção "' + sectionType + '":', error);
                showMessage('Ocorreu um erro ao salvar a seção "' + sectionType + '".', 'error');
            }
        });
    });


    // Ajustar a lógica de salvamento para a seção de temas de interesse
    document.querySelectorAll('.save-section-btn[data-section="temas_interesse"]').forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();

            const sectionType = button.getAttribute('data-section');
            const formData = new FormData();
            
            // O tema principal não tem mais checkbox, então não precisamos adicioná-lo
            // Apenas os subtemas serão salvos.

            const selectedSubthemes = [];
            document.querySelectorAll('.subtheme-checkbox:checked').forEach(checkbox => {
                selectedSubthemes.push(checkbox.value);
            });
            formData.append('subtemas', JSON.stringify(selectedSubthemes));

            formData.append('section_type', sectionType);

            try {
                const response = await fetch('<?= $_SESSION['base_url'] ?>/api/save_personalization.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showMessage('Dados da seção "' + sectionType + '" salvos com sucesso!', 'success');
                    // Retrair o colapso após salvar com sucesso
                    targetElement.classList.add('hidden');
                } else {
                    showMessage('Erro ao salvar seção "' + sectionType + '": ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Erro ao enviar dados da seção "' + sectionType + '":', error);
                showMessage('Ocorreu um erro ao salvar a seção "' + sectionType + '".', 'error');
            }
        });
    });

    // Função para carregar produtos
    async function loadProducts() {
        const uid_usuario = '<?= htmlspecialchars($_SESSION['user_id']) ?>'; // Passar UID do PHP para JS
        productsTableBody.innerHTML = ''; // Limpar a tabela antes de carregar novos dados
        currentProducts = []; // Limpa o array antes de carregar

        try {
            const response = await fetch(`<?= $_SESSION['base_url'] ?>/api/get/get_user_products.php`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const result = await response.json();

            if (result.success) {
                for (const productData of result.products) {
                    addProductToTable(productData.id, productData.name, productData.description, productData.info, productData.images, true);
                    currentProducts.push(productData); // Adiciona ao array global
                }
            } else {
                showMessage('Erro ao carregar produtos: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Erro ao carregar produtos:', error);
            showMessage('Erro ao carregar produtos.', 'error');
        }
    }

    // Carregar produtos salvos ao carregar a página
    window.addEventListener('load', loadProducts);
</script>
<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 hidden bg-black bg-opacity-75 flex items-center justify-center z-50">
    <div class="relative bg-white rounded-lg shadow-lg max-w-3xl w-full p-4">
        <button type="button" class="absolute top-2 right-2 text-gray-800 text-2xl font-bold" onclick="closeImageModal()">&times;</button>
        <div id="imageCarousel" class="carousel relative">
            <div class="carousel-inner relative w-full overflow-hidden">
                <!-- Imagens serão injetadas aqui -->
            </div>
            <button type="button" class="carousel-control-prev absolute top-1/2 left-0 -translate-y-1/2 bg-gray-800 bg-opacity-50 text-white p-3 rounded-full ml-2" onclick="prevImage()">&#10094;</button>
            <button type="button" class="carousel-control-next absolute top-1/2 right-0 -translate-y-1/2 bg-gray-800 bg-opacity-50 text-white p-3 rounded-full mr-2" onclick="nextImage()">&#10095;</button>
        </div>
    </div>
</div>

<script>
    let currentImageIndex = 0;
    let currentImages = [];
    let currentSection = '';

    function openImageModal(src, section) {
        currentSection = section;
        const previewContainer = document.getElementById(`${section}-preview`);
        const images = Array.from(previewContainer.querySelectorAll('img'));
        currentImages = images.map(img => img.src);
        currentImageIndex = currentImages.indexOf(src);
        
        updateCarousel();
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
    }

    function updateCarousel() {
        const carouselInner = document.querySelector('#imageCarousel .carousel-inner');
        carouselInner.innerHTML = '';
        if (currentImages.length > 0) {
            const img = document.createElement('img');
            img.src = currentImages[currentImageIndex];
            img.className = 'w-full h-auto max-h-96 object-contain'; // Adiciona classes para a imagem
            carouselInner.appendChild(img);
        }
    }

    function prevImage() {
        currentImageIndex = (currentImageIndex - 1 + currentImages.length) % currentImages.length;
        updateCarousel();
    }

    function nextImage() {
        currentImageIndex = (currentImageIndex + 1) % currentImages.length;
        updateCarousel();
    }

    // Função para exibir miniaturas de imagens recém-selecionadas
    function handleImagePreview(inputElement, previewContainerId, sectionName) {
        const previewContainer = document.getElementById(previewContainerId);
        // Limpar apenas as miniaturas de novas imagens se já houver
        // As miniaturas de imagens salvas são geradas pelo PHP
        previewContainer.querySelectorAll('.new-image-preview').forEach(el => el.remove());

        Array.from(inputElement.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const imgDiv = document.createElement('div');
                imgDiv.className = 'relative w-24 h-24 border rounded overflow-hidden new-image-preview';
                imgDiv.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover cursor-pointer" onclick="openImageModal(this.src, '${sectionName}')">
                    <button type="button" class="absolute top-0 right-0 bg-red-500 text-white rounded-bl-lg p-1 text-xs" onclick="removeNewImage(this)">X</button>
                `;
                previewContainer.appendChild(imgDiv);
            };
            reader.readAsDataURL(file);
        });
    }

    // Função para remover imagem (ainda a ser implementada a lógica de backend)
    function removeImage(buttonElement, sectionName, fileName) {
        if (confirm('Tem certeza que deseja remover esta imagem?')) {
            // Lógica para remover do servidor via AJAX
            // Exemplo:
            // fetch('/feedperfeito/api/delete_image.php', {
            //     method: 'POST',
            //     body: JSON.stringify({ section: sectionName, file: fileName })
            // }).then(response => response.json())
            // .then(data => {
            //     if (data.success) {
            //         buttonElement.closest('.relative').remove();
            //         showMessage('Imagem removida com sucesso!', 'success');
            //     } else {
            //         showMessage('Erro ao remover imagem: ' + data.message, 'error');
            //     }
            // });
            buttonElement.closest('.relative').remove(); // Remove do DOM por enquanto
            showMessage('Imagem removida (apenas visualmente por enquanto).', 'success');
        }
    }

    // Função para remover apenas a pré-visualização de uma nova imagem
    function removeNewImage(buttonElement) {
        buttonElement.closest('.new-image-preview').remove();
    }

    // Event listeners para os inputs de arquivo
    document.getElementById('logotipos').addEventListener('change', function() {
        handleImagePreview(this, 'logotipos-preview', 'logotipos');
    });
    document.getElementById('imagens_artes').addEventListener('change', function() {
        handleImagePreview(this, 'imagens_artes-preview', 'imagens_artes');
    });
    document.getElementById('elementos_design').addEventListener('change', function() {
        handleImagePreview(this, 'elementos_design-preview', 'elementos_design');
    });
    document.getElementById('imagens_empresa').addEventListener('change', function() {
        handleImagePreview(this, 'imagens_empresa-preview', 'imagens_empresa');
    });
</script>
<!-- Modal Editar Produto -->
<div id="editProductModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Editar Produto</h3>
        <input type="hidden" id="edit_product_id">

        <div class="mb-4">
            <label for="edit_product_name" class="block text-sm font-medium text-gray-700">Nome do Produto</label>
            <input type="text" id="edit_product_name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                placeholder="Ex: Post para Instagram">
        </div>

        <div class="mb-4">
            <label for="edit_product_description" class="block text-sm font-medium text-gray-700">Descrição</label>
            <textarea id="edit_product_description" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                placeholder="Descreva seu produto..."></textarea>
        </div>

        <div class="mb-4">
            <label for="edit_product_info" class="block text-sm font-medium text-gray-700">Informação do Produto</label>
            <textarea id="edit_product_info" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                placeholder="Informações adicionais..."></textarea>
        </div>

        <div class="mb-4">
            <label for="edit_product_images" class="block text-sm font-medium text-gray-700">Adicionar Novas Imagens</label>
            <input type="file" id="edit_product_images" multiple accept="image/*"
                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800">
            <div id="edit_product_images_preview" class="flex flex-wrap gap-2 mt-2">
                <!-- Miniaturas das imagens existentes serão carregadas aqui -->
            </div>
        </div>

        <div class="flex justify-between items-center gap-2">
            <span class="text-sm text-gray-600">Custo: <span class="font-semibold">200 Créditos</span></span>
            <div class="flex gap-2">
                <button type="button" id="closeEditProductModal"
                    class="text-gray-700 border border-gray-300 px-4 py-2 rounded hover:bg-gray-100">Cancelar</button>
                <button type="button" id="saveEditProductBtn" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800">Salvar Alterações</button>
            </div>
        </div>
    </div>
</div>
<script>
    const editModal = document.getElementById('editProductModal');
    const closeEditBtn = document.getElementById('closeEditProductModal');
    const saveEditProductBtn = document.getElementById('saveEditProductBtn');

    closeEditBtn?.addEventListener('click', () => {
        editModal.classList.add('hidden');
    });

    window.addEventListener('click', function (e) {
        if (e.target === editModal) {
            editModal.classList.add('hidden');
        }
    });

    function openEditProductModal(product) {
        document.getElementById('edit_product_id').value = product.id;
        document.getElementById('edit_product_name').value = product.name;
        document.getElementById('edit_product_description').value = product.description;
        document.getElementById('edit_product_info').value = product.info;

        const previewContainer = document.getElementById('edit_product_images_preview');
        previewContainer.innerHTML = ''; // Limpar pré-visualizações antigas

        // Exibir miniaturas das imagens existentes
        product.images.forEach(imgName => {
            const imgDiv = document.createElement('div');
            imgDiv.className = 'relative w-24 h-24 border rounded overflow-hidden';
            imgDiv.innerHTML = `
                <img src="<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario) ?>/produtos/${imgName}" alt="Preview" class="w-full h-full object-cover cursor-pointer" onclick="openImageModal(this.src, 'produtos')">
                <button type="button" class="absolute top-0 right-0 bg-red-500 text-white rounded-bl-lg p-1 text-xs" onclick="removeImage(this, 'produtos', '${imgName}', '${product.id}')">X</button>
            `;
            previewContainer.appendChild(imgDiv);
        });

        editModal.classList.remove('hidden');
    }

    // Event listener para o input de novas imagens no modal de edição
    document.getElementById('edit_product_images').addEventListener('change', function() {
        handleImagePreview(this, 'edit_product_images_preview', 'produtos');
    });

    // Lógica para salvar edições de produto
    saveEditProductBtn?.addEventListener('click', async () => {
        const productId = document.getElementById('edit_product_id').value;
        const productName = document.getElementById('edit_product_name').value;
        const productDescription = document.getElementById('edit_product_description').value;
        const productInfo = document.getElementById('edit_product_info').value;
        const productImagesInput = document.getElementById('edit_product_images');
        const productNewImages = productImagesInput.files;

        const formData = new FormData();
        formData.append('section_type', 'product_management');
        formData.append('product_action', 'edit');
        formData.append('product_id', productId);
        formData.append('name', productName);
        formData.append('description', productDescription);
        formData.append('info', productInfo);
        for (let i = 0; i < productNewImages.length; i++) {
            formData.append('images[]', productNewImages[i]);
        }

        // Adicionar imagens existentes que não foram removidas
        const existingImagesDivs = document.querySelectorAll('#edit_product_images_preview div:not(.new-image-preview)');
        const existingImages = [];
        existingImagesDivs.forEach(div => {
            const imgSrc = div.querySelector('img').src;
            const imgName = imgSrc.substring(imgSrc.lastIndexOf('/') + 1);
            existingImages.push(imgName);
        });
        formData.append('existing_images', JSON.stringify(existingImages));


        try {
            const response = await fetch('<?= $_SESSION['base_url'] ?>/api/save_personalization.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                showMessage('Produto atualizado com sucesso!', 'success');
                editModal.classList.add('hidden');
                // Atualizar a linha na tabela (encontrar e atualizar os dados)
                const rowToUpdate = document.querySelector(`tr[data-product-id="${productId}"]`);
                if (rowToUpdate) {
                    // Atualizar as células da tabela
                    rowToUpdate.children[0].textContent = productName; // Nome
                    rowToUpdate.children[1].textContent = productDescription; // Descrição
                    rowToUpdate.children[2].textContent = productInfo; // Informação

                    // Atualizar miniaturas de imagem
                    const updatedImages = result.image_names; // Usar as imagens retornadas pelo backend
                    const imagesCell = rowToUpdate.children[3];
                    imagesCell.innerHTML = ''; // Limpa as miniaturas antigas
                    updatedImages.forEach(imgName => {
                        const imgElement = document.createElement('img');
                        imgElement.src = `<?= $_SESSION['base_url'] ?>/doc/<?= htmlspecialchars($uid_usuario) ?>/produtos/${imgName}`;
                        imgElement.className = 'w-10 h-10 object-cover rounded-sm cursor-pointer';
                        imgElement.onclick = () => openImageModal(imgElement.src, 'produtos');
                        imagesCell.appendChild(imgElement);
                    });
                }
                // Atualizar o currentProducts array
                const productIndex = currentProducts.findIndex(p => p.id === productId);
                if (productIndex > -1) {
                    currentProducts[productIndex] = {
                        id: productId,
                        name: productName,
                        description: productDescription,
                        info: productInfo,
                        images: updatedImages
                    };
                }
                loadProducts(); // Recarregar a lista de produtos após a edição

            } else {
                showMessage('Erro ao atualizar produto: ' + result.message, 'error');
            }
        } catch (error) {
            // console.error('Erro ao atualizar produto:', error);
            // showMessage('Ocorreu um erro de comunicação ao atualizar o produto.', 'error');
        }
    });

    function removeImage(buttonElement, sectionName, fileName, productId = null) {
        if (confirm('Tem certeza que deseja remover esta imagem?')) {
            // Lógica para remover do servidor via AJAX
            const formData = new FormData();
            formData.append('section_type', sectionName);
            formData.append('product_action', 'delete_image'); // Nova ação para remover imagem
            formData.append('file_name', fileName);
            if (productId) {
                formData.append('product_id', productId);
            }

            fetch('<?= $_SESSION['base_url'] ?>/api/save_personalization.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    buttonElement.closest('.relative').remove();
                    showMessage('Imagem removida com sucesso!', 'success');
                    if (productId && sectionName === 'produtos') {
                        // Atualizar o array currentProducts após remover a imagem de um produto
                        const productIndex = currentProducts.findIndex(p => p.id === productId);
                        if (productIndex > -1) {
                            currentProducts[productIndex].images = currentProducts[productIndex].images.filter(img => img !== fileName);
                        }
                    }
                } else {
                    showMessage('Erro ao remover imagem: ' + data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erro ao remover imagem:', error);
                showMessage('Ocorreu um erro ao remover a imagem.', 'error');
            });
        }
    }
    </script>