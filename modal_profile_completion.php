<?php
    $userModel = new User();
    $user = $userModel->find($_SESSION['user_id']); 
    // Determinar a visibilidade inicial do modal via JavaScript
    if((empty($user['name']) || empty($user['phone']))){     
        $profile = '';
    }else{
        $profile = 'hidden';
    }
    $avatarUrl = '';
    if (file_exists('uploads/avatars/' . htmlspecialchars($user['avatar_url']))) {
        $avatarUrl = $_SESSION['base_url'].'/uploads/avatars/' . htmlspecialchars($user['avatar_url']);
    }

?>

<div id="modalProfileCompletion" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex justify-center items-center <?= $profile ?> ">
    <div class="bg-white p-8 rounded-lg shadow-xl max-w-md mx-auto relative">
        <h3 class="text-2xl font-bold mb-4 text-black">Complete seu Perfil</h3>
        <p class="text-gray-700 mb-6">Por favor, preencha as informações adicionais para continuar.</p>
        <?php // print_r ($user); ?>
        <form id="profileCompletionForm" action="<?php echo $_SESSION['base_url']; ?>/api/post/update_profile.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id'] ?? '') ?>">

            <div class="mb-4 text-center">
                <label for="avatar_upload" class="cursor-pointer inline-block relative w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden border-2 border-gray-300 mx-auto">
                    <?php if (!empty($user['avatar_url'])): ?>
                        <img id="avatar_preview" src="<?= $avatarUrl ?>" alt="Avatar" class="w-full h-full object-cover">
                    <?php else: ?>
                        <i id="avatar_icon" class="fas fa-camera text-gray-500 text-3xl"></i>
                    <?php endif; ?>
                    <input type="file" id="avatar_upload" name="avatar_upload" accept="image/jpeg, image/png" class="hidden">
                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity duration-300">
                        <i class="fas fa-pencil-alt text-white text-xl"></i>
                    </div>
                </label>
            </div>

            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nome Completo:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            
            <div class="mb-6">
                <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Telefone:</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-black hover:bg-gray-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Salvar Dados
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarUpload = document.getElementById('avatar_upload');
    const avatarPreview = document.getElementById('avatar_preview');
    const avatarIcon = document.getElementById('avatar_icon');
    const modal = document.getElementById('modalProfileCompletion');

    if (avatarUpload) {
        avatarUpload.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (avatarPreview) {
                        avatarPreview.src = e.target.result;
                        avatarPreview.style.display = 'block';
                    } else {
                        const img = document.createElement('img');
                        img.id = 'avatar_preview';
                        img.src = e.target.result;
                        img.alt = 'Avatar';
                        img.className = 'w-full h-full object-cover';
                        document.querySelector('label[for="avatar_upload"]').prepend(img);
                        if (avatarIcon) avatarIcon.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Adiciona um evento para fechar o modal
    if (modal) {
        modal.addEventListener('click', function(event) {
            // Se o clique for fora do conteúdo do modal, feche-o
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    }
});
</script>