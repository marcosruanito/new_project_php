<?php
$host = 'localhost';
$dbname = 'sistema_usuarios';
$user = 'root';
$pass = '';
$pdo = new PDO("mysql:host=localhost;port=3307;dbname=sistema_usuarios;charset=utf8", $user, $pass);
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
