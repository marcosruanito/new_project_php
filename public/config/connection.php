<?php
$host = 'localhost';
$port = '3307'; // porta que você usa no MySQL (confirma no XAMPP)
$dbname = 'sistema_usuarios';
$user = 'root';
$pass = '';

try {
    // Cria o objeto PDO
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pass);
    // Define o modo de erro do PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>
