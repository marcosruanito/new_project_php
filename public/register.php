<?php
session_start();

include_once('../config/connection.php');

// Gera token CSRF se ainda não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Mensagens flash
$sucesso = $_SESSION['flash_success'] ?? '';
$erro = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valida CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Requisição inválida (CSRF detectado).");
    }

    // Sanitiza dados
    $nome  = trim($_POST['nome']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $senha = trim($_POST['senha']);

    if ($nome === '' || !$email || $senha === '') {
        $_SESSION['flash_error'] = "Preencha todos os campos corretamente.";
        header('Location: register.php');
        exit;
    }

    try {
        // Verifica se o e-mail já existe
        $check = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $check->execute([$email]);
        if ($check->rowCount() > 0) {
            $_SESSION['flash_error'] = "E-mail já cadastrado.";
            header('Location: register.php');
            exit;
        }

        // Insere o novo usuário
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $email, $hash]);

        $_SESSION['flash_success'] = "Conta criada com sucesso! Faça login.";
        header('Location: login.php');
        exit;
    } catch (PDOException $e) {
        error_log("Erro ao registrar: " . $e->getMessage());
        $_SESSION['flash_error'] = "Erro interno ao criar conta.";
        header('Location: register.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Registrar</title>
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
      background: #28a745;
      color: white;
      border: none;
      cursor: pointer;
      transition: background 0.2s;
    }
    button:hover { background: #218838; }
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
    .login-btn {
      text-decoration: none;
      background: #007bff;
      color: white;
      padding: 10px 15px;
      border-radius: 5px;
      transition: background 0.2s;
    }
    .login-btn:hover { background: #0056b3; }
  </style>
</head>
<body>
  <h1>Criar nova conta</h1>

  <?php if ($sucesso): ?>
    <div class="flash-success"><?= htmlspecialchars($sucesso) ?></div>
  <?php endif; ?>

  <?php if ($erro): ?>
    <div class="flash-error"><?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>

  <form method="POST">
      <input type="text" name="nome" placeholder="Nome completo" required>
      <input type="email" name="email" placeholder="E-mail" required>
      <input type="password" name="senha" placeholder="Senha" required>
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <button type="submit">Registrar</button>
  </form>

  <!-- Link para login -->
  <a href="login.php" class="login-btn">Já tem conta? Faça login</a>
</body>
</html>
