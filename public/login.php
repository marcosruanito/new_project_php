<?php
session_start();

$sucesso = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);

include_once('../config/connection.php');

// Gera token CSRF se ainda não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$erro = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valida CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Requisição inválida (CSRF detectado).");
    }

    // Sanitiza os campos
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $senha = trim($_POST['senha']);

    if (!$email || !$senha) {
        $_SESSION['flash_error'] = 'Preencha todos os campos.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    try {
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

            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            header('Location: index.php');
            exit;
        } else {
            $_SESSION['flash_error'] = 'E-mail ou senha incorretos.';
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    } catch (PDOException $e) {
        error_log("Erro no login: " . $e->getMessage());
        $_SESSION['flash_error'] = 'Erro interno. Tente novamente mais tarde.';
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 40px;
      background: #f8f8f8;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    h1 { margin-bottom: 20px; }
    form {
      display: flex;
      flex-direction: column;
      width: 300px;
      gap: 10px;
      margin-bottom: 15px;
    }
    input, button {
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      background: #007bff;
      color: white;
      border: none;
      cursor: pointer;
      transition: background 0.2s;
    }
    button:hover {
      background: #0056b3;
    }
    .flash-error {
      background: #f8d7da;
      color: #721c24;
      padding: 8px;
      border-radius: 5px;
      margin-bottom: 10px;
      width: 300px;
      text-align: center;
    }
    .flash-success {
      background: #d4edda;
      color: #155724;
      padding: 8px;
      border-radius: 5px;
      margin-bottom: 10px;
      width: 300px;
      text-align: center;
    }
    .register-btn {
      text-decoration: none;
      background: #28a745;
      color: white;
      padding: 10px 15px;
      border-radius: 5px;
      transition: background 0.2s;
    }
    .register-btn:hover {
      background: #218838;
    }
  </style>
</head>
<body>
  <h1>Login</h1>

  <?php if ($sucesso): ?>
    <div class="flash-success"><?= htmlspecialchars($sucesso) ?></div>
  <?php endif; ?>

  <?php if ($erro): ?>
    <div class="flash-error"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <form method="POST">
      <input type="email" name="email" placeholder="E-mail" required>
      <input type="password" name="senha" placeholder="Senha" required>
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <button type="submit">Entrar</button>
  </form>

  <!-- Botão para registro -->
  <a href="register.php" class="register-btn">Criar nova conta</a>
</body>
</html>
