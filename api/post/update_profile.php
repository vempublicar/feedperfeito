<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../config/session.php'; // Para acessar $_SESSION

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $name = $_POST['name'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $avatar_url = null;

    // Handle avatar upload if present
    if (isset($_FILES['avatar_upload']) && $_FILES['avatar_upload']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['avatar_upload'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['error_message'] = 'Tipo de arquivo inválido para avatar.';
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $user_id) {
                header('Location: ../../dashboard.php?error=avatar_type');
            } elseif (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] === $user_id) {
                header('Location: ../../dashboard-adm.php?error=avatar_type');
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Tipo de arquivo inválido para avatar.']);
            }
            exit();
        }
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
            $_SESSION['error_message'] = 'Tamanho do arquivo excede o limite de 5MB.';
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $user_id) {
                header('Location: ../../dashboard.php?error=avatar_size');
            } elseif (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] === $user_id) {
                header('Location: ../../dashboard-adm.php?error=avatar_size');
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Tamanho do arquivo excede o limite de 5MB.']);
            }
            exit();
        }

        // Define the local upload directory
        $upload_dir = __DIR__ . '/../../uploads/avatars/'; 
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create directory if it doesn't exist
        }

        // Generate a unique file name
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = $user_id . '_' . uniqid() . '.' . $fileExtension;
        $target_file = $upload_dir . $fileName;

        // Move the uploaded file to the local directory
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $avatar_url = $fileName; // Salvar apenas o nome do arquivo
        } else {
            http_response_code(500);
            $_SESSION['error_message'] = 'Falha ao mover o arquivo de avatar para o diretório local.';
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $user_id) {
                header('Location: ../../dashboard.php?error=avatar_move');
            } elseif (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] === $user_id) {
                header('Location: ../../dashboard-adm.php?error=avatar_move');
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Falha ao mover o arquivo de avatar para o diretório local.']);
            }
            exit();
        }
    }

    $update_data = [];
    if ($name !== null) $update_data['name'] = $name;
    if ($phone !== null) $update_data['phone'] = $phone;
    if ($avatar_url !== null) $update_data['avatar_url'] = $avatar_url;

    if (empty($update_data)) {
        $_SESSION['error_message'] = 'Nenhum dado para atualizar.';
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $user_id) {
            header('Location: ../../dashboard.php?error=no_data');
        } elseif (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] === $user_id) {
            header('Location: ../../dashboard-adm.php?error=no_data');
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Nenhum dado para atualizar.']);
        }
        exit();
    }

    $userModel = new User();
    try {
        // Ensure user_id is valid UUID
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $user_id)) {
            http_response_code(400);
            $_SESSION['error_message'] = 'ID de usuário inválido.';
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $user_id) {
                header('Location: ../../dashboard.php?error=invalid_id');
            } elseif (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] === $user_id) {
                header('Location: ../../dashboard-adm.php?error=invalid_id');
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID de usuário inválido.']);
            }
            exit();
        }

        $result = $userModel->update($user_id, $update_data);
        if ($result) {
            // Update session data
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $user_id) {
                if ($name !== null) $_SESSION['user_name'] = $name;
                if ($phone !== null) $_SESSION['user_phone'] = $phone; // Assuming 'phone' is also stored in session
                if ($avatar_url !== null) $_SESSION['user_avatar_url'] = 'uploads/avatars/' . $avatar_url; // Salvar o caminho relativo
            } elseif (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] === $user_id) {
                if ($name !== null) $_SESSION['admin_name'] = $name;
                if ($phone !== null) $_SESSION['admin_phone'] = $phone; // Assuming 'phone' is also stored in session
                if ($avatar_url !== null) $_SESSION['admin_avatar_url'] = 'uploads/avatars/' . $avatar_url; // Salvar o caminho relativo
            }

            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $user_id) {
                header('Location: ../../dashboard.php');
            } elseif (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] === $user_id) {
                header('Location: ../../dashboard-adm.php');
            } else {
                // Fallback, if for some reason the session role is not clearly defined
                echo json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso!', 'avatar_url' => $avatar_url]);
            }
            exit();
        } else {
            $_SESSION['error_message'] = 'Falha ao atualizar perfil no banco de dados.';
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $user_id) {
                header('Location: ../../dashboard.php?error=db_update_failed');
            } elseif (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] === $user_id) {
                header('Location: ../../dashboard-adm.php?error=db_update_failed');
            } else {
                echo json_encode(['success' => false, 'message' => 'Falha ao atualizar perfil no banco de dados.']);
            }
            exit();
        }
    } catch (Exception $e) {
        error_log("Error updating profile: " . $e->getMessage());
        $_SESSION['error_message'] = 'Erro interno do servidor ao atualizar perfil.';
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $user_id) {
            header('Location: ../../dashboard.php?error=internal_error');
        } elseif (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] === $user_id) {
            header('Location: ../../dashboard-adm.php?error=internal_error');
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro interno do servidor ao atualizar perfil.']);
        }
        exit();
    }
} else {
    http_response_code(405);
    $_SESSION['error_message'] = 'Método não permitido.';
    header('Location: ' . $_SESSION['base_url'] . '/login.php?error=method_not_allowed');
    exit();
}
?>