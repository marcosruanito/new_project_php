<?php
$host = 'localhost';
$port = '3307'; // Porta alterada no XAMPP
$dbname = 'sistema_usuarios';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Conexão bem-sucedida!"; // opcional para teste
} catch (PDOException $e) {
    die("❌ Erro na conexão: " . $e->getMessage());
}
?>
