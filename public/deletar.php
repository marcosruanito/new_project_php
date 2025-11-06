<?php
session_start();

// Proteção básica: só usuários logados podem deletar
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// incluir arquivo de conexão (certifique-se do caminho e nome corretos)
include_once('../config/connection.php');

// Verifica método POST (mais seguro que GET)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo "Método não permitido.";
    exit;
}

// Validação CSRF (opcional, mas recomendado)
// Supondo que você gerou um token na sessão ao renderizar o formulário:
// $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    http_response_code(403);
    echo "Requisição inválida (CSRF).";
    exit;
}

// Valida o ID (garantir que é um inteiro)
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id === false || $id === null) {
    $_SESSION['flash_error'] = "ID inválido.";
    header('Location: index.php');
    exit;
}

try {
    // Opcional: checar se o usuário existe
    $check = $pdo->prepare("SELECT id FROM usuarios WHERE id = ?");
    $check->execute([$id]);
    if ($check->rowCount() === 0) {
        $_SESSION['flash_error'] = "Usuário não encontrado.";
        header('Location: index.php');
        exit;
    }

    // Deleta o usuário
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['flash_success'] = "Usuário deletado com sucesso.";
    } else {
        $_SESSION['flash_error'] = "Falha ao deletar usuário.";
    }

} catch (PDOException $e) {
    // Logar erro real em arquivo em vez de mostrar pro usuário em produção
    error_log("Erro ao deletar usuário: " . $e->getMessage());
    $_SESSION['flash_error'] = "Ocorreu um erro inesperado.";
}

header('Location: index.php');
exit;
