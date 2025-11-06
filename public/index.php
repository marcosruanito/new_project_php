<?php
session_start();

// Protege a página (somente usuários logados)
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

include_once('../config/connection.php');

// Cria token CSRF se ainda não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Busca todos os usuários
$sql = "SELECT * FROM usuarios ORDER BY id DESC";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Captura mensagens (se houver)
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error   = $_SESSION['flash_error'] ?? null;
$flash_info    = $_SESSION['flash_info'] ?? null;

// Limpa mensagens depois de exibir
unset($_SESSION['flash_success'], $_SESSION['flash_error'], $_SESSION['flash_info']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Lista de Usuários</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; background: #f8f8f8; }
    .flash { padding: 10px; border-radius: 5px; margin-bottom: 10px; }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }
    .info { background: #d1ecf1; color: #0c5460; }
  </style>
</head>
<body>
  <p>
    Olá, <strong><?= htmlspecialchars($_SESSION['usuario']['nome']) ?></strong> |
    <a href="logout.php">Sair</a>
  </p>

  <h1>Usuários Cadastrados</h1>

  <?php if ($flash_success): ?>
    <div class="flash success"><?= htmlspecialchars($flash_success) ?></div>
  <?php elseif ($flash_error): ?>
    <div class="flash error"><?= htmlspecialchars($flash_error) ?></div>
  <?php elseif ($flash_info): ?>
    <div class="flash info"><?= htmlspecialchars($flash_info) ?></div>
  <?php endif; ?>

  <a href="cadastrar.php">➕ Novo Usuário</a>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Email</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($usuarios as $row): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['nome']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td>
            <!-- Editar -->
            <a href="editar.php?id=<?= $row['id'] ?>">✏️</a>

            <!-- Excluir via POST (mais seguro que GET) -->
            <form method="POST" action="deletar.php" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');" style="display:inline;">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
              <button type="submit" style="border:none; background:none; cursor:pointer;">🗑️</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
