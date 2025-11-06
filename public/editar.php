<?php
include('../config/connection.php');

// Verifica se veio o ID
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

// Busca o usuário pelo ID
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Se não encontrou o usuário
if (!$usuario) {
    die("Usuário não encontrado!");
}

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];

    // Atualiza os dados
    $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $email, $id]);

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
      <button type="submit">Salvar Alterações</button>
  </form>
</body>
</html>
