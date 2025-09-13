<?php
header('Content-Type: application/json'); // Adiciona o cabeçalho para JSON
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/database.php';
require_once '../../models/AprovacaoPedido.php';
require_once '../../models/Purchase.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $userId = $_SESSION['admin_id'] ?? null;

        // Obter dados do POST
        $uidUsuarioPedido = $_POST['uid_usuario_pedido'] ?? null;
        $uniqueCode = $_POST['unique_code'] ?? null;
        $bonus = $_POST['bonus'] ?? 0;
        $aprovacao = $_POST['aprovacao'] ?? null;
        $observacoesAprovacao = $_POST['observacoes_aprovacao'] ?? ''; // Nova variável para observações
        $pedidoId = $_POST['pedido_id'] ?? null; // Obtém o ID do pedido

        $numRevisao = 0;
        $conversa = [];
        $imagensPaths = []; // Array para armazenar os caminhos das imagens

        // === Lógica de Upload de Imagens ===
        if (isset($_FILES['imagens']) && !empty($_FILES['imagens']['name'][0])) {
            $uploadDir = '../../uploads/aprovacao/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['imagens']['name'] as $key => $name) {
                $fileName = uniqid() . '_' . basename($name);
                $targetFilePath = $uploadDir . $fileName;
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

                // Permitir certos formatos de arquivo
                $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'pdf'); // Adicione pdf se necessário
                if (in_array($fileType, $allowTypes)) {
                    if (move_uploaded_file($_FILES['imagens']['tmp_name'][$key], $targetFilePath)) {
                        $imagensPaths[] = '/uploads/aprovacao/' . $fileName; // Caminho relativo para o banco de dados
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Erro ao fazer upload de uma das imagens.']);
                        exit();
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Formato de arquivo não permitido.']);
                    exit();
                }
            }
        }
        $imagensJson = json_encode($imagensPaths);
        // === Fim da Lógica de Upload ===

        // Validação básica
        if (!$uidUsuarioPedido || !$uniqueCode) {
            echo json_encode(['success' => false, 'message' => 'Dados obrigatórios faltando para registrar a aprovação de pedido.']);
            exit();
        }

        $aprovacaoPedidoModel = new AprovacaoPedido();
        $existingAprovacao = $aprovacaoPedidoModel->getAprovacaoByUniqueCode($uniqueCode);
        //  echo 'existingAprovacao'. $existingAprovacao;
        $dataToSave = [
            'uid_usuario_pedido' => $uidUsuarioPedido,
            'unique_code' => $uniqueCode,
            'bonus' => $bonus,
            'aprovacao' => $aprovacao,
            'status_botao_download' => 'disabled' // Valor padrão, pode ser alterado depois
        ];

        // Adicionar 'imagens' apenas se houver imagens para salvar
        if (!empty($imagensPaths)) {
            $dataToSave['imagens'] = $imagensJson;
        }

        if ($observacoesAprovacao) {
            // Decodifica antes de encodificar novamente para remover qualquer escape pré-existente
            $observacoesDecoded = json_decode($observacoesAprovacao);
            // Se a decodificação falhar ou não for uma string, usa o valor original
            if (json_last_error() !== JSON_ERROR_NONE || !is_string($observacoesDecoded)) {
                $observacoesDecoded = $observacoesAprovacao;
            }
            $dataToSave['observacoes'] = json_encode($observacoesDecoded, JSON_UNESCAPED_UNICODE);
        }

        if ($existingAprovacao) {
            // Atualizar aprovação existente
            $dataToSave['num_revisao'] = $existingAprovacao['num_revisao'] + 1;
            
            $resultado = $aprovacaoPedidoModel->updateAprovacao($existingAprovacao['id'], $dataToSave);

        } else {
            // Criar nova aprovação
            $dataToSave['num_revisao'] = 1; // Primeira revisão
            $resultado = $aprovacaoPedidoModel->createAprovacao($dataToSave);
        }
        
        if ($resultado) {
            // Atualizar o status da compra (purchase)
            if ($pedidoId && $aprovacao) {
                $purchaseModel = new Purchase();
                $purchaseUpdateResult = $purchaseModel->update($pedidoId, ['status' => $aprovacao]);
                if (!$purchaseUpdateResult) {
                    // Logar ou tratar o erro de atualização do status da compra
                    error_log("Erro ao atualizar o status da compra $pedidoId para $aprovacao.");
                }
            }
            echo json_encode(['success' => true, 'message' => 'Aprovação de pedido registrada/atualizada com sucesso!']);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao registrar/atualizar a aprovação de pedido.']);
            exit();
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro interno do servidor: ' . $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de requisição não permitido.']);
    exit();
}
?>