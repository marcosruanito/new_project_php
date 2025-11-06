<?php include('../config/conexao.php'); ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $email, $senha]);

    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastrar Usuário</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <h1>Cadastrar Usuário</h1>
  <form method="POST">
      <input type="text" name="nome" placeholder="Nome completo" required>
      <input type="email" name="email" placeholder="E-mail" required>
      <input type="password" name="senha" placeholder="Senha" required>
      <button type="submit">Cadastrar</button>
  </form>
</body>
</html>
