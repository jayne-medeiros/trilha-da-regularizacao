<?php
// admin/api.php

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', 'uploads/'); 
$dataFile = 'dados.json';

$adminApiKey = getenv('ADMIN_API_KEY');
if ((!$adminApiKey || $adminApiKey === '') && file_exists(__DIR__ . '/config.php')) {
    $config = require __DIR__ . '/config.php';
    if (is_array($config) && isset($config['ADMIN_API_KEY'])) {
        $adminApiKey = $config['ADMIN_API_KEY'];
    }
}

if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode(['trilhas' => [], 'blog' => []]));
}

function handleUpload($fileInputName, $allowedTypes = []) {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) return null; 
    $file = $_FILES[$fileInputName];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!empty($allowedTypes) && !in_array($extension, $allowedTypes)) return null;
    $newFileName = uniqid() . '.' . $extension;
    if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $newFileName)) return UPLOAD_URL . $newFileName;
    return null;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    header('Content-Type: application/json');
    echo file_get_contents($dataFile);
    exit;
}

if ($method === 'POST') {
    header('Content-Type: application/json');

    if (!$adminApiKey || $adminApiKey === '') {
        echo json_encode(['error' => 'Admin API key não configurada', 'success' => false]);
        exit;
    }

    if (!isset($_POST['key']) || $_POST['key'] !== $adminApiKey) {
        echo json_encode(['error' => 'Acesso negado', 'success' => false]);
        exit;
    }

    $currentData = json_decode(file_get_contents($dataFile), true);
    $tipo = $_POST['tipo'] ?? ''; 

    // --- LÓGICA DE EXCLUSÃO (NOVO!) ---
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $indexToDelete = (int)$_POST['index'];
        
        if (isset($currentData[$tipo][$indexToDelete])) {
            // Remove do array
            array_splice($currentData[$tipo], $indexToDelete, 1);
            // Salva o arquivo atualizado
            file_put_contents($dataFile, json_encode($currentData, JSON_PRETTY_PRINT));
            echo json_encode(['success' => true, 'message' => 'Item excluído']);
        } else {
            echo json_encode(['error' => 'Item não encontrado', 'success' => false]);
        }
        exit;
    }
    // ----------------------------------

    // --- LÓGICA DE SALVAR/EDITAR (Mantida) ---
    $editIndex = (isset($_POST['editIndex']) && $_POST['editIndex'] !== '') ? (int)$_POST['editIndex'] : -1;
    
    try {
        $itemAntigo = ($editIndex >= 0 && isset($currentData[$tipo][$editIndex])) ? $currentData[$tipo][$editIndex] : [];
        $novoItem = [];

        if ($tipo === 'trilhas') {
            $imagemPath = handleUpload('trilha_imagem', ['jpg', 'jpeg', 'png', 'webp', 'svg']);
            $anexoPath = handleUpload('trilha_anexo', ['pdf', 'doc', 'docx', 'mp4', 'zip']);
            
            $novoItem = [
                'titulo' => $_POST['titulo'] ?? '',
                'desc'   => $_POST['desc'] ?? '',
                'icon'   => $_POST['icon'] ?? '',
                'link'   => $_POST['link'] ?? '',
                'imagem' => $imagemPath ? $imagemPath : ($itemAntigo['imagem'] ?? ''),
                'anexo'  => $anexoPath ? $anexoPath : ($itemAntigo['anexo'] ?? '')
            ];
        } elseif ($tipo === 'blog') {
            $imagemPath = handleUpload('blog_imagem', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            $anexoPath = handleUpload('blog_anexo', ['pdf', 'doc', 'docx', 'mp4', 'zip']);

            $novoItem = [
                'titulo' => $_POST['titulo'] ?? '',
                'resumo' => $_POST['resumo'] ?? '',
                'link_externo' => $_POST['link_externo'] ?? '',
                'imagem' => $imagemPath ? $imagemPath : ($itemAntigo['imagem'] ?? ''),
                'anexo'  => $anexoPath ? $anexoPath : ($itemAntigo['anexo'] ?? ''),
                'data'   => $itemAntigo['data'] ?? date('d/m/Y')
            ];
        }

        if ($editIndex >= 0) {
            $currentData[$tipo][$editIndex] = $novoItem;
        } else {
            array_unshift($currentData[$tipo], $novoItem);
        }

        file_put_contents($dataFile, json_encode($currentData, JSON_PRETTY_PRINT));
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage(), 'success' => false]);
    }
    exit;
}
?>