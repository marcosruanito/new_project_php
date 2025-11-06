<?php
include('../config/connection.php');

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

// Deleta o usuário
$sql = "DELETE FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

header('Location: index.php');
exit;
?>
