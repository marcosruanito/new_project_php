<?php
session_start();

// Protege a página (somente usuários logados podem editar)
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

include_once('../config/connection.php');

// Valida o ID vindo da URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['flash_error'] = "ID inválido.";
    header('Location: index.php');
    exit;
}

// Busca o usuário
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    $_SESSION['flash_error'] = "Usuário não encontrado.";
    header('Location: index.php');
    exit;
}

// Gera token CSRF se ainda não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valida o token CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Requisição inválida (CSRF).");
    }

    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);

    // Validações simples
    if ($nome === '' || $email === '') {
        $_SESSION['flash_error'] = "Preencha todos os campos.";
        header("Location: editar.php?id=$id");
        exit;
    }

    try {
        $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $email, $id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['flash_success'] = "Usuário atualizado com sucesso!";
        } else {
            $_SESSION['flash_info'] = "Nenhuma alteração realizada.";
        }
    } catch (PDOException $e) {
        error_log("Erro ao atualizar usuário: " . $e->getMessage());
        $_SESSION['flash_error'] = "Erro ao atualizar o usuário.";
    }

    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Usuário</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <h1>Editar Usuário</h1>
  <form method="POST">
      <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
      <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <button type="submit">Salvar Alterações</button>
  </form>
</body>
</html>
