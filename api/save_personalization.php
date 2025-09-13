<?php
require_once __DIR__ . '/../config/session.php';
requireUserLogin();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

// Função auxiliar para upload de arquivos
function handle_uploads($file_input_name, $destination_path, $max_files = 5) {
    $uploaded_files = [];
    if (isset($_FILES[$file_input_name]) && is_array($_FILES[$file_input_name]['name'])) {
        $count = 0;
        foreach ($_FILES[$file_input_name]['name'] as $key => $name) {
            if ($count >= $max_files) break;

            if ($_FILES[$file_input_name]['error'][$key] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES[$file_input_name]['tmp_name'][$key];
                $file_extension = pathinfo($name, PATHINFO_EXTENSION);
                $new_file_name = uniqid() . '.' . $file_extension;
                $target_file = $destination_path . '/' . $new_file_name;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $uploaded_files[] = $new_file_name; // Salva apenas o nome do arquivo para o JSON
                    $count++;
                }
            }
        }
    }
    return $uploaded_files;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid_usuario = $_SESSION['user_id'] ?? null; 

    if (!$uid_usuario) {
        $response['message'] = 'UID do usuário não encontrado.';
        echo json_encode($response);
        exit;
    }

    $doc_path = __DIR__ . '/../doc/' . $uid_usuario;

    if (!is_dir($doc_path)) {
        if (!mkdir($doc_path, 0777, true)) {
            $response['message'] = 'Falha ao criar o diretório do usuário.';
            echo json_encode($response);
            exit;
        }
    }

    $section_type = $_POST['section_type'] ?? '';

    switch ($section_type) {
        case 'redes_sociais':
            $redes_sociais_data = [
                'instagram' => $_POST['instagram'] ?? '',
                'facebook' => $_POST['facebook'] ?? '',
                'whatsapp' => $_POST['whatsapp'] ?? '',
                'site' => $_POST['site'] ?? ''
            ];
            file_put_contents($doc_path . '/redes_sociais.json', json_encode($redes_sociais_data, JSON_PRETTY_PRINT));
            $response['success'] = true;
            $response['message'] = 'Redes Sociais salvas com sucesso!';
            break;

        case 'cores':
            $cores_data = [
                'cor_principal' => $_POST['cor_principal'] ?? '',
                'cor_secundaria' => $_POST['cor_secundaria'] ?? '',
                'cor_complemento' => $_POST['cor_complemento'] ?? '',
                'cor_destaque' => $_POST['cor_destaque'] ?? ''
            ];
            file_put_contents($doc_path . '/cores.json', json_encode($cores_data, JSON_PRETTY_PRINT));
            $response['success'] = true;
            $response['message'] = 'Cores Principais salvas com sucesso!';
            break;

        case 'temas_interesse':
            $temas_data = [
                'temas' => [], // Temas principais não são mais enviados via checkbox
                'subtemas' => isset($_POST['subtemas']) ? json_decode($_POST['subtemas'], true) : []
            ];
            file_put_contents($doc_path . '/temas_interesse.json', json_encode($temas_data, JSON_PRETTY_PRINT));
            $response['success'] = true;
            $response['message'] = 'Temas de Interesse salvos com sucesso!';
            break;

        case 'segmento':
            $segmento_data = [
                'segmento' => $_POST['segmento'] ?? ''
            ];
            file_put_contents($doc_path . '/segmento.json', json_encode($segmento_data, JSON_PRETTY_PRINT));
            $response['success'] = true;
            $response['message'] = 'Segmento salvo com sucesso!';
            break;

        case 'textos_personalizados':
            $textos_data = [
                'texto_personalizado_1' => $_POST['texto_personalizado_1'] ?? '',
                'texto_personalizado_2' => $_POST['texto_personalizado_2'] ?? '',
                'texto_personalizado_3' => $_POST['texto_personalizado_3'] ?? ''
            ];
            file_put_contents($doc_path . '/textos_personalizados.json', json_encode($textos_data, JSON_PRETTY_PRINT));
            $response['success'] = true;
            $response['message'] = 'Textos Personalizados salvos com sucesso!';
            break;

        case 'logotipos':
            $logotipos_path = $doc_path . '/logotipos';
            if (!is_dir($logotipos_path)) mkdir($logotipos_path, 0777, true);
            $uploaded_logotipos = handle_uploads('logotipos', $logotipos_path, 5);
            file_put_contents($doc_path . '/logotipos.json', json_encode($uploaded_logotipos, JSON_PRETTY_PRINT));
            $response['success'] = true;
            $response['message'] = 'Logotipos salvos com sucesso!';
            break;

        case 'imagens_artes':
            $imagens_artes_path = $doc_path . '/imagens_artes';
            if (!is_dir($imagens_artes_path)) mkdir($imagens_artes_path, 0777, true);
            $uploaded_imagens_artes = handle_uploads('imagens_artes', $imagens_artes_path, 10);
            file_put_contents($doc_path . '/imagens_artes.json', json_encode($uploaded_imagens_artes, JSON_PRETTY_PRINT));
            $response['success'] = true;
            $response['message'] = 'Imagens para Artes salvas com sucesso!';
            break;

        case 'elementos_design':
            $elementos_design_path = $doc_path . '/elementos_design';
            if (!is_dir($elementos_design_path)) mkdir($elementos_design_path, 0777, true);
            $uploaded_elementos_design = handle_uploads('elementos_design', $elementos_design_path, 5);
            file_put_contents($doc_path . '/elementos_design.json', json_encode($uploaded_elementos_design, JSON_PRETTY_PRINT));
            $response['success'] = true;
            $response['message'] = 'Elementos de Design salvos com sucesso!';
            break;

        case 'imagens_empresa':
            $imagens_empresa_path = $doc_path . '/imagens_empresa';
            if (!is_dir($imagens_empresa_path)) mkdir($imagens_empresa_path, 0777, true);
            $uploaded_imagens_empresa = handle_uploads('imagens_empresa', $imagens_empresa_path, 5);
            file_put_contents($doc_path . '/imagens_empresa.json', json_encode($uploaded_imagens_empresa, JSON_PRETTY_PRINT));
            $response['success'] = true;
            $response['message'] = 'Imagens da Empresa salvas com sucesso!';
            break;

        case 'product_management':
            $product_action = $_POST['product_action'] ?? '';
            $product_id = $_POST['product_id'] ?? uniqid('prod_'); // Gerar um ID único para novos produtos
            $product_path = $doc_path . '/produtos';
            $products_list_file = $doc_path . '/produtos.json'; // Arquivo para listar IDs de produtos

            if (!is_dir($product_path)) {
                mkdir($product_path, 0777, true);
            }

            $product_file = $product_path . '/' . $product_id . '.json';

            // Carregar lista de produtos existente
            $products_list = [];
            if (file_exists($products_list_file)) {
                $products_list = json_decode(file_get_contents($products_list_file), true);
                if (!is_array($products_list)) {
                    $products_list = [];
                }
            }

            switch ($product_action) {
                case 'add':
                case 'edit':
                    $product_data = [
                        'id' => $product_id,
                        'name' => $_POST['name'] ?? '',
                        'description' => $_POST['description'] ?? '',
                        'info' => $_POST['info'] ?? '',
                        'images' => []
                    ];

                    // Carregar imagens existentes se for edição
                    if ($product_action === 'edit' && file_exists($product_file)) {
                        $existing_data = json_decode(file_get_contents($product_file), true);
                        $product_data['images'] = $existing_data['images'] ?? [];
                    }

                    // Lidar com upload de novas imagens
                    $uploaded_images = handle_uploads('images', $product_path);
                    $product_data['images'] = array_merge($product_data['images'], $uploaded_images);

                    // Salvar o arquivo JSON individual do produto
                    file_put_contents($product_file, json_encode($product_data, JSON_PRETTY_PRINT));

                    // Adicionar o ID à lista de produtos se for um novo produto
                    if ($product_action === 'add' && !in_array($product_id, $products_list)) {
                        $products_list[] = $product_id;
                        file_put_contents($products_list_file, json_encode($products_list, JSON_PRETTY_PRINT));
                    }
                    
                    $response['success'] = true;
                    $response['message'] = 'Produto salvo com sucesso!';
                    $response['product_id'] = $product_id;
                    // Retornar os nomes das imagens para o frontend atualizar
                    $response['image_names'] = $product_data['images'];
                    break;
                case 'delete':
                    if (file_exists($product_file)) {
                        unlink($product_file); // Excluir o arquivo JSON individual do produto
                        
                        // Remover o ID do produto da lista de produtos
                        $products_list = array_diff($products_list, [$product_id]);
                        file_put_contents($products_list_file, json_encode(array_values($products_list), JSON_PRETTY_PRINT)); // Reindexar o array
                        
                        // TODO: Lógica para excluir as imagens associadas ao produto
                        $response['success'] = true;
                        $response['message'] = 'Produto excluído com sucesso!';
                    } else {
                        $response['message'] = 'Produto não encontrado.';
                    }
                    break;
                default:
                    $response['message'] = 'Ação de produto inválida.';
                    break;
            }
            break;
        case 'logotipos':
        case 'imagens_artes':
        case 'elementos_design':
        case 'imagens_empresa':
        case 'produtos': // Adicionado case para 'produtos'
            $product_action = $_POST['product_action'] ?? '';
            if ($product_action === 'delete_image') {
                $file_name = $_POST['file_name'] ?? '';
                $section_name = $_POST['section_type'] ?? '';
                $product_id_for_image = $_POST['product_id'] ?? null;

                if (empty($file_name) || empty($section_name)) {
                    $response['message'] = 'Nome do arquivo ou seção não especificado.';
                    echo json_encode($response);
                    exit;
                }

                $target_dir = $doc_path . '/' . $section_name;
                $file_path_to_delete = $target_dir . '/' . $file_name;

                if (file_exists($file_path_to_delete)) {
                    if (unlink($file_path_to_delete)) {
                        // Se for uma imagem de produto, remover do JSON do produto também
                        if ($section_name === 'produtos' && $product_id_for_image) {
                            $product_data_path = $doc_path . '/produtos';
                            $product_file_to_update = $product_data_path . '/' . $product_id_for_image . '.json';
                            if (file_exists($product_file_to_update)) {
                                $product_content = json_decode(file_get_contents($product_file_to_update), true);
                                if ($product_content && isset($product_content['images'])) {
                                    $product_content['images'] = array_values(array_filter($product_content['images'], function($img) use ($file_name) {
                                        return $img !== $file_name;
                                    }));
                                    file_put_contents($product_file_to_update, json_encode($product_content, JSON_PRETTY_PRINT));
                                }
                            }
                        } else { // Remover de outros JSONs de seção
                            $json_file = $doc_path . '/' . $section_name . '.json';
                            if (file_exists($json_file)) {
                                $current_data = json_decode(file_get_contents($json_file), true);
                                if (is_array($current_data)) {
                                    $current_data = array_values(array_filter($current_data, function($img) use ($file_name) {
                                        return $img !== $file_name;
                                    }));
                                    file_put_contents($json_file, json_encode($current_data, JSON_PRETTY_PRINT));
                                }
                            }
                        }
                        $response['success'] = true;
                        $response['message'] = 'Imagem excluída com sucesso!';
                    } else {
                        $response['message'] = 'Falha ao excluir a imagem do servidor.';
                    }
                } else {
                    $response['message'] = 'Imagem não encontrada.';
                }
            } else {
                // Manter a lógica existente para outras ações (upload, etc.)
                // (Isso já está implementado fora deste switch)
                $response['message'] = 'Ação inválida para esta seção.';
            }
            break;
        default:
            $response['message'] = 'Tipo de seção inválido.';
            break;
    }

} else {
    $response['message'] = 'Método de requisição inválido.';
}

echo json_encode($response);
?>
