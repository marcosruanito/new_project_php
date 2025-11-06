<?php include('../config/connection.php'); ?>

<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
  <p>Olá, <?= htmlspecialchars($_SESSION['usuario']['nome']) ?> | 
   <a href="logout.php">Sair</a></p>

<head>
  <meta charset="UTF-8">
  <title>Lista de Usuários</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <h1>Usuários Cadastrados</h1>
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
      <?php
      $sql = "SELECT * FROM usuarios";
      $stmt = $pdo->query($sql);
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['nome']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td>
            <a href="editar.php?id=<?= $row['id'] ?>">✏️</a>
            <a href="deletar.php?id=<?= $row['id'] ?>" onclick="return confirm('Tem certeza?')">🗑️</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>
