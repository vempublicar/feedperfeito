<?php
// Garante que a sessão seja iniciada para usar $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/database.php';
require_once '../../models/Purchase.php'; // Incluir o novo modelo Purchase.php
require_once '../../models/CreditManager.php'; // Incluir o CreditManager

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obter user_id da sessão
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            $_SESSION['status_type'] = 'error';
            $_SESSION['status_message'] = 'Usuário não autenticado. Por favor, faça login.';
            header('Location: ' . $_SESSION['base_url'] . '/login.php');
            exit();
        }

        // Obter dados do POST
        $productId = $_POST['productId'] ?? null;
        $productName = $_POST['productName'] ?? null;
        $uniqueCode = $_POST['uniqueCode'] ?? null;
        $creditsUsed = $_POST['credits'] ?? null;
        $observacoes = $_POST['observacoes'] ?? null;
        $customization = json_decode($_POST['customization'] ?? '{}', true);
        $productType = $_POST['productType'] ?? null;

        // Validação básica
        if (!$productId || !$productName || !$uniqueCode || !$creditsUsed) {
            $_SESSION['status_type'] = 'error';
            $_SESSION['status_message'] = 'Dados obrigatórios faltando para registrar a compra.';
            header('Location: ' . $_SESSION['base_url'] . '/dashboard/pedidos'); // Redireciona de volta para a pedidos
            exit();
        }

        // Verificar se o usuário tem créditos suficientes ANTES de registrar a compra
        $userCredits = $_SESSION['user_credits'] ?? 0;
        if ($userCredits < $creditsUsed) {
            $_SESSION['status_type'] = 'warning';
            $_SESSION['status_message'] = 'Saldo de créditos insuficiente para esta compra. Compre seus creditos e tente novamente.';
            header('Location: ' . $_SESSION['base_url'] . '/dashboard/creditos');
            exit();
        }

        $data = [
            'user_id' => $userId,
            'product_name' => $productName,
            'unique_code' => $uniqueCode,
            'credits_used' => $creditsUsed,
            'observacoes' => $observacoes,
            'customization_options' => json_encode($customization), // Supabase espera JSON string
            'status' => ($productType === 'Pronto') ? 'Disponível' : 'pending' // Definir status baseado no tipo do produto
        ];

        // Adiciona product_id se não for um UUID
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $productId)) {
            $data['product_id'] = $productId;
        }

        $purchaseModel = new Purchase(); // Usar o modelo Purchase
        $creditManager = new CreditManager(); // Instanciar CreditManager

        $resultado = $purchaseModel->create($data);

        // print_r($resultado);
        if ($resultado) {
            // Tentar remover os créditos do usuário
            $creditsRemoved = $creditManager->removeCredits($userId, $creditsUsed, 'Compra de produto: ' . $productName);
            if ($creditsRemoved !== false) { // Verifica se a remoção de créditos foi bem-sucedida (retorna o novo saldo ou false)
                $_SESSION['status_type'] = 'success';
                $_SESSION['status_message'] = 'Compra registrada e créditos debitados com sucesso! Confirme seu pedido em "Meus Pedidos"';
                $_SESSION['user_credits'] = $creditsRemoved; // Atualiza o saldo na sessão
                header('Location: ' . $_SESSION['base_url'] . '/dashboard/pedidos'); // Redireciona para a página de pedidos
                exit();
            } else {
                // Se a remoção de créditos falhar, pode ser necessário reverter a compra ou logar o erro
                // Por simplicidade, vamos apenas registrar o erro e informar ao usuário
                $_SESSION['status_type'] = 'warning'; // Alterado para warning, pois a compra foi registrada
                $_SESSION['status_message'] = 'Compra registrada, mas houve um erro ao debitar os créditos. Por favor, entre em contato com o suporte.';
                // O ideal seria adicionar um mecanismo para reverter a compra ou marcar para revisão
                header('Location: ' . $_SESSION['base_url'] . '/dashboard/pedidos');
                exit();
            }
        } else {
            $_SESSION['status_type'] = 'error';
            $_SESSION['status_message'] = 'Erro ao registrar a compra.';
            header('Location: ' . $_SESSION['base_url'] . '/dashboard/pedidos');
            exit();
        }

    } catch (Exception $e) {
        $_SESSION['status_type'] = 'error';
        $_SESSION['status_message'] = 'Erro interno do servidor: ' . $e->getMessage();
        header('Location: ' . $_SESSION['base_url'] . '/dashboard/pedidos');
        exit();
    }
} else {
    $_SESSION['status_type'] = 'error';
    $_SESSION['status_message'] = 'Método de requisição não permitido.';
    header('Location: ' . $_SESSION['base_url'] . '/dashboard/pedidos');
    exit();
}
?>