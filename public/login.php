<?php
session_start();
include('../config/connection.php');

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'tipo' => $usuario['tipo']
        ];
        header('Location: index.php');
        exit;
    } else {
        $erro = 'E-mail ou senha incorretos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <h1>Login</h1>
  <?php if ($erro): ?>
    <p style="color:red;"><?= $erro ?></p>
  <?php endif; ?>
  <form method="POST">
      <input type="email" name="email" placeholder="E-mail" required>
      <input type="password" name="senha" placeholder="Senha" required>
      <button type="submit">Entrar</button>
  </form>
</body>
</html>
